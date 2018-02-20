<?php

namespace CDevelopers\ProjectSettings;

if ( ! defined( 'ABSPATH' ) )
    exit; // disable direct access

class Admin_Page
{
    static $form_args = array('sub_name' => 'post_type');

    function __construct()
    {
        $args = wp_parse_args( $_GET, array(
            'post-type' => '',
            'do'        => '',
            'cpt'       => '',
            ) );

        if( 'add' === $args['do'] || $args['post-type'] ) {
            $page_callback = array($this, 'custom_type_page_settings');
        }
        else {
            $page_callback = array(__CLASS__, 'welcome_page');
        }

        $page = new WP_Admin_Page( Utils::OPTION );
        $page->set_args( array(
            'parent' => 'options-general.php',
            'title' => __('Project Settings', DOMAIN),
            'menu' => __('Project Settings', DOMAIN),
            'callback'    => $page_callback,
            'validate'    => array($this, 'validate'),
            'permissions' => 'manage_options',
            'tab_sections'=> null,
            'columns'     => 2,
            ) );

        $page->set_assets( array(__CLASS__, '_assets') );

        if( 'remove' === $args['do'] ) {
            if( wp_verify_nonce( $_REQUEST['_wpnonce'], 'trash-type-'.$args['cpt'] ) ){
                unset( Utils::$post_types[ $args['cpt'] ] );

                update_option( Utils::OPTION_NAME_TYPES, Utils::$post_types );
            }

            // flush_rewrite_rules();
            wp_redirect( get_admin_url() . 'options-general.php?page=' . Utils::OPTION );
            exit;
        }

        $page->add_metabox( 'globals', __('Globals', DOMAIN), array($this, 'metabox_globals'), 'side' );

        if( 'add' === $args['do'] || $args['post-type'] ) {
            if( ! Utils::is_built_in( $args['post-type'] ) ) {
                $page->add_metabox( 'main', __('Main', DOMAIN), array($this, 'metabox_main'), 'normal' );
                $page->add_metabox( 'supports', __('Supports', DOMAIN), array($this, 'metabox_supports'), 'normal' );
            }

            $page->add_metabox( 'labels', __('Labels', DOMAIN), array($this, 'metabox_labels'), 'normal' );
        }

        $page->set_metaboxes();
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

    static function welcome_page()
    {
        echo sprintf('<a href="?page=%s&do=add" class="button button-primary alignright">%s</a>',
            esc_attr( $_REQUEST['page'] ),
            __( 'Create new post type', DOMAIN )
            );

        _e( 'You may create new post type or edit him and hide no used wordpress functions from menu', DOMAIN );

        if( $types = Utils::get_post_types() ) {
            echo '<!-- <form id="" method="get"> -->';
            $table = new Post_Types_List_Table();
            foreach ($types as $id => $type) {
                $table->set_type( $id, $type['labels']['singular_name'], $type['labels']['name'] );
            }

            $table->prepare_items();
            $table->display();
            echo "<!-- </form> -->";
        }

        printf('<input type="hidden" name="page" value="" />',
            esc_attr($_REQUEST['page']));
    }

    static function set_posttype_data( &$form )
    {
        if( !empty($_GET['post-type']) ) {
            if( $values = Utils::get_type( $_GET['post-type'] ) ) {
                $form->set_active( Utils::sanitize_assoc_array($values) );
            }
        }
    }

    static function custom_type_page_settings()
    {
        $form = new WP_Admin_Forms(
            Utils::get_settings('cpt.php'), true, self::$form_args );
        self::set_posttype_data( $form );

        echo $form->render();
    }

    static function metabox_main()
    {
        $form = new WP_Admin_Forms(
            Utils::get_settings('cpt-main.php'), true, self::$form_args );
        self::set_posttype_data( $form );

        echo $form->render();
    }

    static function metabox_supports()
    {
        $form = new WP_Admin_Forms(
            Utils::get_settings('cpt-supports.php'), true, self::$form_args );
        self::set_posttype_data( $form );

        echo $form->render();
    }

    static function metabox_labels()
    {
        $form = new WP_Admin_Forms(
            Utils::get_settings('cpt-labels.php'), true, self::$form_args );
        self::set_posttype_data( $form );

        echo $form->render();
    }

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

    /**
     * Validate Input's Values
     */
    function validate( $values ) {
        // Update Post Types
        if( ! empty( $values['post_type']['post_type_name'] ) ) {
            if( isset($values['post_type']['labels']) && is_array($values['post_type']['labels']) ) {
                $values['post_type']['labels'] = array_filter($values['post_type']['labels']);
            }

            $post_types = Utils::get_post_types();
            $post_types[ strtolower($values['post_type']['post_type_name']) ] = $values['post_type'];
            update_option(Utils::OPTION_NAME_TYPES, $post_types);

            unset($values['post_type']);
        }

        custom_post_types();
        flush_rewrite_rules();

        return $values;
    }
}
new Admin_Page();
