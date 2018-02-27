<?php

namespace CDevelopers\ProjectSettings;

if ( ! defined( 'ABSPATH' ) )
    exit; // disable direct access

class Admin_Page
{
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
        if( !empty($_GET['post-type']) ) {
            if( $values = self::get_type( $_GET['post-type'] ) ) {
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
            'edit_cpt_page' => Utils::OPTION
            ) );

        wp_localize_script( 'project-settings-script', 'post_types', array_values( get_post_types() ) );
    }

    function __construct()
    {
        $page = new WP_Admin_Page( Utils::OPTION );
        $page->set_assets( array(__CLASS__, '_assets') );

        $args = wp_parse_args( $_GET,
            array_fill_keys(array('post-type', 'taxonomy', 'do', 'cpt', 'tax'), '') );

        if( 'remove' === $args['do'] ) {
            if( $args['cpt'] && wp_verify_nonce( $_REQUEST['_wpnonce'], 'trash-type-'.$args['cpt'] ) )
                Registration::del_custom_post_types( $args['cpt'] );

            if( $args['tax'] && wp_verify_nonce( $_REQUEST['_wpnonce'], 'trash-type-'.$args['tax'] ) )
                Registration::del_custom_taxanomies( $args['tax'] );

            wp_redirect( get_admin_url() . 'options-general.php?page=' . Utils::OPTION );
            exit;
        }

        $columns = 2;
        if( 'add' === $args['do'] || $args['post-type'] ) {
            self::$type = 'type';
            self::$form_args = array('sub_name' => 'post_type');
            $page_callback = array(__CLASS__, 'edit_page_settings');

            if( !$args['post-type'] ||
                ($args['post-type'] && ! Registration::is_built_in( $args['post-type'], 'types' )) ) {
                self::set_main_metabox($page);
                self::set_supports_metabox($page);
            }

            self::set_labels_metabox( $page );
        }
        elseif( 'add_taxonomy' === $args['do'] || $args['taxonomy'] ) {
            self::$type = 'tax';
            self::$form_args = array('sub_name' => 'taxonomy');
            $page_callback = array(__CLASS__, 'edit_page_settings');

            if( !$args['taxonomy'] ||
                ($args['taxonomy'] && ! Registration::is_built_in( $args['taxonomy'], 'taxes' )) ) {
                self::set_main_metabox($page);
            }
            else {
                $columns = 1;
            }

            // self::set_labels_metabox($page);
        }
        else {
            $page_callback = array(__CLASS__, 'start_page');
            $page->add_metabox( 'globals', __('Globals', DOMAIN), array(__CLASS__, 'metabox_globals'), 'side' );
        }

        $page->set_args( array(
            'parent' => 'options-general.php',
            'title' => __('Project Settings', DOMAIN),
            'menu' => __('Project Settings', DOMAIN),
            'callback'    => $page_callback,
            'validate'    => array(__CLASS__, 'validate'),
            'permissions' => 'manage_options',
            'tab_sections'=> null,
            'columns'     => $columns,
        ) );

        $page->set_metaboxes();
    }

    private static function view_table( $context = 'type', $values, $custom_values, $columns ) // || tax
    {
        $id = ( 'type' === $context ) ? 'post-types' : 'taxonomies';
        // $single = ( 'type' === $context ) ? 'post-type' : 'taxonomy';
        $table = new Registrations_Table( $context, array( 'plural' => $id . '_table' ) );
        $filter = '';

        if( $values ) {
            echo '<br class="clear">';
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

            $field = array(
                'id' => $id . '_table__filter',
                'type' => 'select',
                'label' => 'Показывать',
                'options' => array(
                    '' => 'Все',
                    '_builtin' => 'Штатные',
                    'registred' => 'Зарегистрированные',
                    'project-settings' => 'Созданные',
                    ),
                'input_class' => 'button',
                'label_class' => 'alignright'
                );
            $filter = WP_Admin_Forms::render_input( $field, $custom_values ?  'project-settings' : 'registred' );
            ?>
            <div class="pre-table-wrapper">
                <h3 class="alignleft"><?php echo ( 'type' === $context ) ?
                    __('Post types:', DOMAIN) : __('Taxonomies:', DOMAIN); ?></h3>
                <?php
                    printf('<a href="?page=%s&do=%s" class="alignright button button-primary">%s</a> %s',
                        esc_attr( $_REQUEST['page'] ),
                        ( 'type' === $context ) ? 'add' : 'add_taxonomy',
                        ( 'type' === $context ) ? __( 'Create new post type', DOMAIN ) : __( 'Create new taxonomy', DOMAIN ),
                        $filter
                    );
                ?>
            </div>
            <?php
            if( $values ) {
                $table->prepare_items();
                $table->display();
            }
        }
    }

    public static function view_post_types_table($values, $custom_values, $columns ) {

        self::view_table('type', $values, $custom_values, $columns);
    }

    public static function view_taxonomies_table($values, $custom_values, $columns ) {

        self::view_table('tax', $values, $custom_values, $columns);
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
        /**
         * Проверять существование ID
         */
        $pagename = 'project-settings-start-page';
        // Update Post Types
        if( is_array($_REQUEST['page']) ?
            in_array( $pagename, $_REQUEST['page']) : $pagename == $_REQUEST['page'] ) {
            Registration::set_custom_post_types( $values );
            Registration::set_custom_taxonomies( $values );
            Registration::register_customs();
            flush_rewrite_rules();

            return Utils::get( 'all', array() );
        }

        // $pagename = 'project-settings-start-page';
        // @else: Update Global Options
        // if( is_array($_REQUEST['page']) ?
        //     in_array( $pagename, $_REQUEST['page']) : $pagename == $_REQUEST['page'] ) {

            unset($values['post_type']);
            unset($values['taxonomy']);
        // }

        return $values;
    }
}
new Admin_Page();
