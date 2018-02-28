<?php

namespace CDevelopers\ProjectSettings;

if ( ! defined( 'ABSPATH' ) )
    exit; // disable direct access

class Admin_Page
{
    static $context = false;
    static $type = 'type';
    static $form_args = array('sub_name' => 'post_type');

    /** For Admin Sanitize post data */
    static function sanitize_assoc_array($values)
    {
        if( ! is_array($values) ) return $values;
        foreach ($values as $k => $v) {
            if( is_array($v) ) {
                foreach ($v as $key => $value) {
                    $values[$k . '_' . $key] = $value;
                }
            }
        }

        return $values;
    }

    public static function get_type( $type_name )
    {
        $types = get_post_types(array('name' => $type_name), 'objetcs');

        $post_type = isset($types[ $type_name ]) ? (array) $types[ $type_name ] : false;

        if( $post_type ) {
            $post_type['labels'] = (array)$post_type['labels'];
            $post_type['supports'] = get_all_post_type_supports( $post_type['name'] );
            return $post_type;
        }

        return false;
    }

    private static function set_posttype_data( &$form )
    {
        if( !empty($_GET['value']) ) {
            if( $values = self::get_type( $_GET['value'] ) ) {
                $form->set_active( self::sanitize_assoc_array($values) );
            }
        }
    }
    /** /Sanitize admin data */

    public static function set_main_metabox( &$page )  {

        $page->add_metabox( 'main', __('Main', DOMAIN), array(__CLASS__, 'metabox_main'), 'side' );
    }

    public static function set_supports_metabox( &$page ) {

        $page->add_metabox( 'supports', __('Supports', DOMAIN), array(__CLASS__, 'metabox_supports'), 'side' );
    }

    public static function set_labels_metabox( &$page ) {

        $page->add_metabox( 'labels', __('Labels', DOMAIN), array(__CLASS__, 'metabox_labels'), 'normal' );
    }

    static function _assets()
    {
        $assets = Utils::get_plugin_url('assets');
        wp_enqueue_style( 'project-settings-style', $assets . '/admin.css', array(), '1.0' );
        wp_enqueue_script('project-settings-script', $assets . '/admin.js',  array('jquery'), '1.0', true );

        wp_localize_script( 'project-settings-script', 'menu_disabled', array(
            'menu' => Utils::get( 'menu' ),
            'sub_menu' => Utils::get( 'sub_menu' ),
            'edit_cpt_page' => Utils::OPTION,
            '_Request' => $_REQUEST,
            ) );

        wp_localize_script( 'project-settings-script', 'post_types', array_values( get_post_types() ) );
    }

    static function added_referer()
    {
        printf('<input type="hidden" name="_wp_http_referer" value="%s">',
            esc_url( admin_url( "options-general.php?page=" . Utils::OPTION ) )
        );
    }

    function __construct()
    {
        $page = new WP_Admin_Page( Utils::OPTION );
        $page->set_assets( array(__CLASS__, '_assets') );

        $args = wp_parse_args( $_GET,
            array_fill_keys(array('do', 'context', 'value'), '') );

        $page->add_metabox( 'globals', __('Global settings', DOMAIN),
            array(__CLASS__, 'metabox_globals'), 'side' );

        if( 'remove' === $args['do'] ) {
            if( $args['value'] && wp_verify_nonce( $_REQUEST['_wpnonce'], 'trash-'.$args['value'] ) ) {
                if( 'types' == $args['context'] )
                    Registration::del_custom_post_types( $args['value'] );
                if( 'taxes' == $args['context'] )
                    Registration::del_custom_taxanomies( $args['value'] );
            }

            wp_redirect( get_admin_url() . 'options-general.php?page=' . Utils::OPTION );
            exit;
        }

        if( 'add' === $args['do'] || 'edit' === $args['do'] ) {
            self::set_main_metabox($page);

            if( 'types' == $args['context'] ) {
                if( 'add' === $args['do'] || ! Registration::is_built_in( $args['value'], 'types' ) )
                    self::set_supports_metabox($page);
            }

            self::set_labels_metabox($page);

            if( 'add' === $args['do'] )
                add_action( "{$page->page}_after_form_inputs", array(__CLASS__, 'added_referer'), 10 );
        }

        $page->set_args( array(
            'parent' => 'options-general.php',
            'title' => __('Project Settings', DOMAIN),
            'menu' => __('Project Settings', DOMAIN),
            'callback'    => ( in_array($args['do'], ['add', 'edit']) ) ?
                array(__CLASS__, 'edit_page_settings') :
                array(__CLASS__, 'start_page'),
            'validate'    => array(__CLASS__, 'validate'),
            'permissions' => 'manage_options',
            'tab_sections'=> null,
            'columns'     => 2,
        ) );

        $page->set_metaboxes();
    }

    static function get_filter_field( $id )
    {
        $options = apply_filters( 'project-settings-filter-options', array(
            '' => 'Все',
            '_builtin' => 'Штатные',
            'registred' => 'Зарегистрированные',
            'project-settings' => 'Созданные',
        ), $id );

        $default = apply_filters('project-settings-filter-default', 'project-settings');

        $field = array(
            'id' => $id . '_table__filter',
            'type' => 'select',
            'label' => 'Показывать',
            'options' =>  $options,
            'input_class' => 'button',
            'label_class' => 'alignright',
        );

        return WP_Admin_Forms::render_input( $field, $default );
    }

    private static function view_table( $context = 'types', Array $values, $custom_values, $columns ) // || tax
    {
        // $id = ( 'types' === $context ) ? 'post-types' : 'taxonomies';
        // $single = ( 'types' === $context ) ? 'post-type' : 'taxonomy';
        $table = new Registrations_Table( $context, array( 'plural' => $context . '_table' ) );
        $filter = '';

        $table->set_columns( $columns );
        foreach ($values as $value) {
            $classrow = $value->_builtin ? '_builtin' : 'registred';
            if( isset($custom_values[ $value->name ]) ) {
                $classrow = 'project-settings';
            }

            $arrValue = array(
                'title'    => $value->name,
                'label'    => $value->label,
                'singular' => $value->labels->singular_name,
                'classrow' => apply_filters( 'project-settings-table-type-classrow', $classrow, $context ),
                );

            if( 'tax' === $context ) {
                $objects = array();
                foreach ($value->object_type as $type) {
                    if(!isset($values[ $type ])) continue;

                    $objects[] = $values[ $type ]->label;
                }

                $arrValue['objects'] = implode(', ', $objects);
            }

            $table->set_value( $arrValue );
        }

        $table->prepare_items();
        $table->display();
    }

    public static function view_post_types_table($values, $custom_values, $columns )
    {
        echo '<div class="pre-table-wrapper">';
        printf('<h3 class="alignleft">%s</h3>', __('Post types:', DOMAIN));
        printf('<a href="?page=%s&do=add&context=types" class="alignright button button-primary">%s</a>',
            esc_attr( Utils::OPTION ),
            __( 'Create new post type', DOMAIN )
        );
        echo self::get_filter_field( 'types' );
        echo '</div><!-- .pre-table-wrapper -->';

        self::view_table('types', $values, $custom_values, $columns);
    }

    public static function view_taxonomies_table($values, $custom_values, $columns )
    {
        echo '<div class="pre-table-wrapper">';
        printf('<h3 class="alignleft">%s</h3>', __('Taxonomies:', DOMAIN));
        printf('<a href="?page=%s&do=add&context=taxes" class="alignright button button-primary">%s</a>',
            esc_attr( Utils::OPTION ),
             __( 'Create new taxonomy', DOMAIN )
        );
        echo self::get_filter_field( 'taxes' );
        echo '</div><!-- .pre-table-wrapper -->';

        self::view_table('taxes', $values, $custom_values, $columns);
    }

    static function start_page()
    {
        // echo '<!-- <form id="" method="get"> -->';
        _e( 'You may create new post type or edit him and hide no used wordpress functions from menu', DOMAIN );

        self::view_post_types_table(get_post_types(null, 'objects'), Registration::get_custom_post_types(), array(
            'label'    => __('Label'),
            'singular' => __('Singular'),
            ) );

        self::view_taxonomies_table(get_taxonomies(null, 'objects'), Registration::get_custom_taxonomies(), array(
            'label'    => __('Label'),
            'singular' => __('Singular'),
            'objects'  => __('Objects'),
            ) );

        // echo "<!-- </form> -->";

        echo '<input type="hidden" name="page[]" value="project-settings" />';
        echo '<input type="hidden" name="page[]" value="project-settings-start-page" />';
    }

    /**
     * Global metabox for start page
     */
    static function metabox_globals()
    {
        $add_class = (!empty($_COOKIE['developer'])) ? 'button button-primary': 'button';

        echo sprintf('<p><input type="button" id="setNotHide" class="%s" value="%s"></p>',
            esc_attr( $add_class ),
            __( 'Show me hidden menus', DOMAIN )
        );

        $form = new WP_Admin_Forms(
            Utils::get_settings('global.php'), $is_table = true, $args = array(
                'hide_desc'   => true,
            ) );

        echo $form->render();

        submit_button( 'Сохранить', 'primary right', 'save_changes' );
        echo '<div class="clear"></div>';
    }

    static function edit_page_settings()
    {
        $form = new WP_Admin_Forms(
            Utils::get_settings( self::$type . '.php' ), true, self::$form_args );
        self::set_posttype_data( $form );

        echo $form->render();
        submit_button( 'Применить изменения', 'primary left', 'save_changes' );

        echo '<input type="hidden" name="page[]" value="project-settings" />';
        echo '<input type="hidden" name="page[]" value="project-settings-edit-page" />';
    }

    static function metabox_main()
    {
        $args = wp_parse_args( array(
            'item_wrap' => array('<p><strong>', '</strong></p>')), self::$form_args );
        $form = new WP_Admin_Forms(
            Utils::get_settings( self::$type . '-main.php' ), false, $args );
        self::set_posttype_data( $form );

        echo $form->render();
    }

    static function metabox_supports()
    {
        $form = new WP_Admin_Forms(
            Utils::get_settings( self::$type . '-supports.php' ), true, self::$form_args );
        self::set_posttype_data( $form );

        echo $form->render();
    }

    static function metabox_labels()
    {
        $form = new WP_Admin_Forms(
            Utils::get_settings( self::$type . '-labels.php' ), true, self::$form_args );
        self::set_posttype_data( $form );

        echo $form->render();
    }

    /**
     * Validate Input's Values
     */
    static function validate( $values ) {

        // Update Customs
        Registration::set_custom_post_types( $values );
        Registration::set_custom_taxonomies( $values );
        Registration::register_customs();
        flush_rewrite_rules();

        // Update Global Settings
        unset($values['post_type']);
        unset($values['taxonomy']);

        return $values;
    }
}
new Admin_Page();
