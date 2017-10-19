<?php

namespace ProjectSettings;

class ProjectSettings_Page
{
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
            $page_callback = array($this, 'welcome_page');
        }

        $page = new WP_Admin_Page( ProjectSettings::OPTION_NAME );
        $page->set_args( array(
            'parent' => 'options-general.php',
            'title' => __('Project Settings', 'project-settings'),
            'menu' => __('Project Settings', 'project-settings'),
            'callback'    => $page_callback,
            'validate'    => array($this, 'validate'),
            'permissions' => 'manage_options',
            'tab_sections'=> null,
            'columns'     => 2,
            ) );

        $page->set_assets( array($this, '_assets') );

        if( 'remove' === $args['do'] ) {
            if( wp_verify_nonce( $_REQUEST['_wpnonce'], 'trash-type-'.$args['cpt'] ) ){
                unset( ProjectSettings::$post_types[ $args['cpt'] ] );

                update_option( ProjectSettings::OPTION_NAME_TYPES, ProjectSettings::$post_types );
            }

            wp_redirect( get_admin_url() . 'options-general.php?page=' . ProjectSettings::OPTION_NAME );
            exit;
        }

        $page->add_metabox( 'globals', __('Globals', 'project-settings'), array($this, 'metabox_globals'), 'side' );

        if( 'add' === $args['do'] || $args['post-type'] ) {
            if( ! ProjectSettings::is_built_in( $args['post-type'] ) ) {
                $page->add_metabox( 'main', __('Main', 'project-settings'), array($this, 'metabox_main'), 'normal' );
                $page->add_metabox( 'supports', __('Supports', 'project-settings'), array($this, 'metabox_supports'), 'normal' );
            }

            $page->add_metabox( 'labels', __('Labels', 'project-settings'), array($this, 'metabox_labels'), 'normal' );
        }

        $page->set_metaboxes();
    }

    /**
     * Добавить js/css файлы (@hook admin_enqueue_scripts)
     *
     * @access
     *     must be public for the WordPress
     */
    function _assets()
    {
        wp_enqueue_style( 'project-settings-style', PS_URL . '/assets/admin.css', array(), '1.0' );
        wp_enqueue_script('project-settings-script', PS_URL . '/assets/admin.js',  array('jquery'), '1.0', true );

        wp_localize_script( 'project-settings-script', 'menu_disabled', array(
            'menu' => ProjectSettings::get( 'menu' ),
            'sub_menu' => ProjectSettings::get( 'sub_menu' ),
            'edit_cpt_page' => ProjectSettings::OPTION_NAME
            ) );

        wp_localize_script( 'project-settings-script', 'post_types', array_values( get_post_types() ) );
    }

    /**
     * Основное содержимое страницы
     *
     * @access
     *     must be public for the WordPress
     */
    function welcome_page() {
        echo sprintf('<a href="?page=%s&do=add" class="button button-primary alignright">%s</a>',
            esc_attr( $_REQUEST['page'] ),
            __( 'Create new post type', 'project-settings' )
        );

        _e( 'You may create new post type or edit him and hide no used wordpress functions from menu', 'project-settings' );

        $types = ProjectSettings::$post_types;

        if( sizeof( $types ) < 1 ) {
            return;
        }

        $table = new Post_Types_List_Table();
        foreach ($types as $id => $type) {
            $table->set_type( $id, $type['labels']['singular_name'], $type['labels']['name'] );
        }

        $table->prepare_items();
        $table->display();

        // $table = new Example_List_Table();
        // $table->set_fields( array('post_type' => ProjectSettings::OPTION_NAME) );
        // $table->prepare_items();
        ?>

        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <?php // $table->display() ?>
        <?php
    }

    function custom_type_page_settings(){
        $data = include(PS_DIR . '/fields/cpt.php');
        $form = new WP_Admin_Forms( $data, $is_table = true, $args = array(
            'sub_name' => 'post_type',
        ) );

        if( !empty($_GET['post-type']) ) {
            if( $values = ProjectSettings::get_type($_GET['post-type']) ) {
                $form->set_active( self::sanitize_assoc_array($values) );
            }
        }

        echo $form->render();
    }


    function metabox2_callback() {
        // array(
        //     // id or name - required
        //     array(
        //         'id'    => 'example_0',
        //         'type'  => 'text',
        //         'label' => 'TextField',
        //         'desc'  => 'This is example text field',
        //         ),
        //      array(
        //         'id'    => 'example_1',
        //         'type'  => 'select',
        //         'label' => 'Select',
        //         'options' => array(
        //             // simples first (not else)
        //             'key_option5' => 'option5',
        //             'option1' => array(
        //                 'key_option2' => 'option2',
        //                 'key_option3' => 'option3',
        //                 'key_option4' => 'option4'),
        //             ),
        //         ),
        //     array(
        //         'id'    => 'example_2',
        //         'type'  => 'checkbox',
        //         'label' => 'Checkbox',
        //         ),
        //     );
    }

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

    function metabox_main(){
        $data = include(PS_DIR . '/fields/cpt-main.php');
        $form = new WP_Admin_Forms( $data, $is_table = true, $args = array(
            'sub_name'    => 'post_type',
        ) );

        if( !empty($_GET['post-type']) ) {
            if( $values = ProjectSettings::get_type($_GET['post-type']) ) {
                $form->set_active( self::sanitize_assoc_array($values) );
            }
        }

        echo $form->render();
    }

    function metabox_supports(){
        $data = include(PS_DIR . '/fields/cpt-supports.php');
        $form = new WP_Admin_Forms( $data, $is_table = true, $args = array(
            'sub_name'    => 'post_type',
        ) );

        if( !empty($_GET['post-type']) ) {
            if( $values = ProjectSettings::get_type($_GET['post-type']) ) {
                $form->set_active( self::sanitize_assoc_array($values) );
            }
        }

        echo $form->render();
    }

    function metabox_labels(){
        $data = include(PS_DIR . '/fields/cpt-labels.php');
        $form = new WP_Admin_Forms( $data, $is_table = true, $args = array(
            'sub_name'    => 'post_type',
        ) );

        if( !empty($_GET['post-type']) ) {
            if( $values = ProjectSettings::get_type($_GET['post-type']) ) {
                $form->set_active( self::sanitize_assoc_array($values) );
            }
        }

        echo $form->render();
    }

    function metabox_globals()
    {
        $add_class = (!empty($_COOKIE['developer'])) ? 'button button-primary': 'button';

        echo sprintf('<p><input type="button" id="setNotHide" class="%s" value="%s"></p>',
            esc_attr( $add_class ),
            __( 'Show me hidden menus', 'project-settings' )
        );

        $data = include(PS_DIR . '/fields/global.php');
        $form = new WP_Admin_Forms( $data, $is_table = true, $args = array(
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
    // return false;
    // Update Post Types
    if( ! empty( $values['post_type']['post_type_name'] ) ) {
        if( isset($values['post_type']['labels']) && is_array($values['post_type']['labels']) ) {
            $values['post_type']['labels'] = array_filter($values['post_type']['labels']);
        }

        ProjectSettings::$post_types[ strtolower($values['post_type']['post_type_name']) ] = $values['post_type'];
        update_option(ProjectSettings::OPTION_NAME_TYPES, ProjectSettings::$post_types);

        unset($values['post_type']);
    }

    return $values;
}
}
new ProjectSettings_Page();
