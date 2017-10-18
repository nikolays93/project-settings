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

        if( $args['post-type'] || $args['post-type'] ) {
            $page->add_metabox( 'labels', __('Labels', 'project-settings'), array($this, 'metabox_labels'), 'normal' );
            if( ! $this->is_builtin ) {
                $page->add_metabox( 'main', __('Settings', 'project-settings'), array($this, 'metabox_main'), 'normal' );
                $page->add_metabox( 'supports', __('Supports', 'project-settings'), array($this, 'metabox_supports'), 'normal' );
            }
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
        wp_enqueue_style( 'project-settings', PS_URL . '/assets/admin.css', array(), '1.0' );
        wp_enqueue_script('project-settings', PS_URL . '/assets/admin.js',  array('jquery'), '1.0', true );

        wp_localize_script( 'project-settings', 'menu_disabled', array(
            'menu' => isset(self::$settings['globals']['menu']) ? self::$settings['globals']['menu'] : '',
            'sub_menu' => isset(self::$settings['globals']['sub_menu']) ? self::$settings['globals']['sub_menu'] : '',
            'edit_cpt_page' => self::SETTINGS
            ) );

        wp_localize_script( 'project-settings', 'post_types', array_values( get_post_types() ) );
    }

    /**
     * Основное содержимое страницы
     *
     * @access
     *     must be public for the WordPress
     */
    function welcome_page() {
        ?>
        <p>
            <a href="?page=<?php echo $_REQUEST['page']; ?>&do=add" class="button button-primary alignright">Создать новый тип записей</a>

            Здесь вы можете создать новый тип записи или изменить вид уже зарегистрированного типа <br> и\или скрыть не реализованный функционал CMS WordPress из меню.
        </p>
        <?php
        $types = ProjectSettings::$post_types;

        if( sizeof( $types ) < 1 ) {
            return;
        }

        // $table = new PSettings\Post_Types_List_Table();
        // foreach ($types as $id => $type) {
        //     $table->set_type( $id, $type['labels']['singular_name'], $type['labels']['name'] );
        // }

        // $table->prepare_items();
        // $table->display();

        $table = new Example_List_Table();
        $table->set_fields( array('post_type' => ProjectSettings::OPTION_NAME) );
        $table->prepare_items();
        ?>

        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <?php $table->display() ?>
        <?php
    }

    function custom_type_page_settings(){
        // WPForm::render(
        //   apply_filters('post_type_data_render', include(PS_DIR . '/inc/settings/cpt.php'), 'page'),
        //   get_active_cpt_or_pt(),
        //   true,
        //   array('admin_page' => self::SETTINGS)
        //   );
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

    function metabox_main(){
        $data = include(PS_DIR . '/fields/cpt-main.php');
        $form = new WP_Admin_Forms( $data, $is_table = true, $args = array(
            // Defaults:
            // 'admin_page'  => true,
            // 'item_wrap'   => array('<p>', '</p>'),
            // 'form_wrap'   => array('', ''),
            // 'label_tag'   => 'th',
            // 'hide_desc'   => false,
            ) );
        echo $form->render();
    }

    function metabox_supports(){
        // WPForm::render(
        //     apply_filters('post_type_data_render', include(PS_DIR . '/inc/settings/cpt-supports.php'), 'supports'),
        //     get_active_cpt_or_pt(),
        //     true,
        //     array('admin_page' => self::SETTINGS)
        //     );
    }

    function metabox_labels(){
        // WPForm::render(
        //     include(PS_DIR . '/inc/settings/cpt-labels.php'),
        //     get_active_cpt_or_pt(),
        //     true,
        //     array('admin_page' => self::SETTINGS)
        //     );
    }

    function metabox_globals(){
        $add_class = (!empty($_COOKIE['developer'])) ? ' button-primary': '';

        echo '<p><input type="button" id="setNotHide" class="button'.$add_class.'" value="Показывать мне скрытые меню"></p>';

        $data = include(PS_DIR . '/fields/global.php');
        $form = new WP_Admin_Forms( $data, $is_table = true, $args = array(
            // Defaults:
            // 'admin_page'  => true,
            // 'item_wrap'   => array('<p>', '</p>'),
            // 'form_wrap'   => array('', ''),
            // 'label_tag'   => 'th',
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
    if( ! empty( $values['post_type_name'] ) ){
        if( ! empty($values['labels']) ) {
            $values['post_type']['labels'] = array_filter($values['labels']);
        }

        unset($values['labels']);

        if(!empty($values['supports']))
            $values['post_type']['supports'] = $values['supports'];

        unset($values['supports']);

        self::$post_types[ strtolower($values['post_type_name']) ] = $values['post_type'];
        update_option(CPTYPES, self::$post_types);

        unset($values['post_type']);
        unset($values['post_type_name']);

        // Update fields
        $fields = array();
        if( isset($values['fields']) && is_array($values['fields']) ){
            for ($i=0; $i < sizeof($values['fields']['type']) ; $i++) {
                if( empty($values['fields']['id'][$i]) || empty($values['fields']['label'][$i]) )
                    continue;

                $fields[$i] = array(
                    'id'    => $values['fields']['id'][$i],
                    'label' => $values['fields']['label'][$i],
                    'type'  => $values['fields']['type'][$i],
                    );
            }
        }
        if(sizeof($fields) >= 1 )
            $values['fields'] = $fields;
    }

    return $values;
}
}
new ProjectSettings_Page();
