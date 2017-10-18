<?php
/*
Plugin Name: Настройки проекта
Plugin URI: https://github.com/nikolays93/project-settings
Description: Скрывает нераскрытый функционал WordPress. Предоставляет возможность создавать новые типы записей и редактировать заголовки ранее зарегистрированных.
Version: 4.0
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

namespace ProjectSettings;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

// add_filter('post_type_data_render', array($this, 'read_only_inputs_filter'), 10, 2);
// add_filter('post_type_data_render', array($this, 'create_new_post_type_filter'), 10, 2);

    /**
     * Filter for new post type
     */
    // function create_new_post_type_filter($data, $type=''){
    //     if( !isset($_GET['do']) || $_GET['do'] != 'add' )
    //         return $data;

    //     foreach ($data as $i => $input) {
    //         if( $type == 'main' ){
    //             if(in_array($input['id'], array('public', 'publicly_queryable', 'show_ui', 'show_in_menu')))
    //                 $data[$i]['checked'] = 'true';
    //         }
    //         elseif( $type == 'supports'){
    //             $s = array( $type.'][title', $type.'][editor', $type.'][thumbnail', $type.'][custom-fields' );
    //             if( in_array($input['id'], $s ) ) {
    //                 $data[$i]['checked'] = 'true';
    //             }
    //         }
    //     }

    //     return $data;
    // }

    // function read_only_inputs_filter($data, $type=''){
    //     switch ($type) {
    //         case 'main':
    //         if( $this->is_builtin ){
    //             foreach ($data as $i => $input) {
    //                 $data[$i]['readonly'] = 'true';
    //             }
    //         }
    //         break;
    //         case 'page':
    //         if(!empty($_GET['post-type']))
    //             $data[0]['readonly'] = 'true';
    //         break;
    //     }

    //     return $data;
    // }

    // function get_active_cpt_or_pt(){
    //     if( empty($_GET['post-type']) )
    //         return;

    //     $post_type_array = json_decode(json_encode(get_post_type_object( $_GET['post-type'] )), true);
    //     $supports = array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks',
    //         'custom-fields', 'comments', 'revisions', 'page-attributes', 'post-formats');
    //     foreach ($supports as $support) {
    //         if( post_type_supports( $_GET['post-type'], $support ) ){
    //             $post_type_array['supports'][$support] = 'on';
    //         }
    //     }

    //     $active = WPForm::active( $post_type_array, false, true );
    //     $active['post_type_name'] = $_GET['post-type'];

    //     return $active;
    // }

// $this->is_builtin = !empty($_GET['post-type'])
// && in_array($_GET['post-type'], get_post_types( array('_builtin' => true) ) );

define('PS_DIR', rtrim( plugin_dir_path( __FILE__ ), '/') );
define('PS_URL', rtrim(plugins_url(basename(__DIR__)), '/') );
define('PS_PREFIX', 'ps_');
define('CPTYPES', 'project-types');


register_activation_hook( __FILE__, array( __NAMESPACE__ . '\ProjectSettings', 'activate' ) );
// register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\ProjectSettings', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( __NAMESPACE__ . '\ProjectSettings', 'uninstall' ) );

add_action( 'plugins_loaded', array(__NAMESPACE__ . '\ProjectSettings', 'get_instance'), 1100 );
class ProjectSettings {
    const OPTION_NAME = __CLASS__;
    const OPTION_NAME_TYPES = 'project-types';

    public static  $post_types = array();
    private static $settings = array();
    private static $_instance = null;
    private function __construct() {}
    private function __clone() {}

    public static function activate(){
        $defaults = apply_filters( 'project_settings_activate', array(
            'menu'     => 'edit-comments.php,users.php,tools.php,',
            'sub_menu' => 'index.php>index.php,index.php>update-core.php,edit.php?post_type=shop_order>edit.php?post_type=shop_order,edit.php?post_type=shop_order>edit.php?post_type=shop_coupon,edit.php?post_type=shop_order>admin.php?page=wc-reports,options-general.php>options-discussion.php,',
            ) );

        add_option( self::OPTION_NAME, $defaults );
    }
    public static function uninstall(){ delete_option(self::OPTION_NAME); }

    private static function include_required_classes()
    {
        $classes = array(
            'Example_List_Table' => 'wp-list-table.php',
            __NAMESPACE__ . '\WP_Admin_Page'      => 'wp-admin-page.php',
            __NAMESPACE__ . '\WP_Admin_Forms'     => 'wp-admin-forms.php',
            'WP_Post_Boxes'      => 'wp-post-boxes.php',
            );

        foreach ($classes as $classname => $dir) {
            if( ! class_exists($classname) ) {
                require_once PS_DIR . '/includes/classes/' . $dir;
            }
        }

    //     require_once DTS_DIR . '/inc/actions.php';
    // require_once DTS_DIR . '/inc/class-wp-admin-page-render.php';
    // require_once DTS_DIR . '/inc/class-wp-form-render.php';
    // require_once DTS_DIR . '/inc/class-post-types-list-table.php';
        // includes
        // require_once PS_DIR . '/includes/register-post_type.php';
        require_once PS_DIR . '/includes/admin-page.php';
    }

    public static function get_instance()
    {
        if( ! self::$_instance ) {
            load_plugin_textdomain( 'project-settings', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
            self::include_required_classes();
            self::$settings  = get_option( self::OPTION_NAME, array() );
            self::$post_types = get_option( self::OPTION_NAME_TYPES, array() );

            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function get( $prop_name )
    {
        return isset( self::$settings[ $prop_name ] ) ? self::$settings[ $prop_name ] : false;
    }

    public function get_type( $type_name )
    {
        return isset( self::$post_types[ $type_name ] ) ? self::$post_types[ $type_name ] : false;
    }
}
