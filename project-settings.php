<?php
/*
Plugin Name: Настройки проекта
Plugin URI: https://github.com/nikolays93/project-settings
Description: Скрывает нераскрытый функционал WordPress. Предоставляет возможность создавать новые типы записей и редактировать заголовки ранее зарегистрированных.
Version: 4.0.1
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

namespace ProjectSettings;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

define('PS_DIR', rtrim( plugin_dir_path( __FILE__ ), '/') );
define('PS_URL', rtrim(plugins_url(basename(__DIR__)), '/') );
define('PS_PREFIX', 'ps_');

register_activation_hook( __FILE__, array( __NAMESPACE__ . '\ProjectSettings', 'activate' ) );
// register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\ProjectSettings', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( __NAMESPACE__ . '\ProjectSettings', 'uninstall' ) );

add_action( 'plugins_loaded', array(__NAMESPACE__ . '\ProjectSettings', 'get_instance'), 1100 );
class ProjectSettings {
    const OPTION_NAME = 'ProjectSettings';
    const OPTION_NAME_TYPES = 'project-types';

    public static  $post_types = array();
    private static $settings = array();
    private static $_instance = null;
    private static $builtins;
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
            __NAMESPACE__ . '\WP_Admin_Page'      => 'wp-admin-page.php',
            __NAMESPACE__ . '\WP_Admin_Forms'     => 'wp-admin-forms.php',
            );

        foreach ($classes as $classname => $dir) {
            if( ! class_exists($classname) ) {
                require_once PS_DIR . '/includes/classes/' . $dir;
            }
        }

        // includes
        if( is_admin() ) {
            require_once PS_DIR . '/includes/post-types-list-table.php';
            require_once PS_DIR . '/includes/admin-page.php';
        }

        require_once PS_DIR . '/includes/actions.php';
    }

    public static function get_instance()
    {
        if( ! self::$_instance ) {
            load_plugin_textdomain( 'project-settings', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
            self::$settings  = get_option( self::OPTION_NAME, array() );
            self::$post_types = get_option( self::OPTION_NAME_TYPES, array() );
            self::is_built_in();
            self::include_required_classes();

            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public static function get( $prop_name = false )
    {
        if( $prop_name ) {
            return isset( self::$settings[ $prop_name ] ) ? self::$settings[ $prop_name ] : false;
        }

        return self::$settings;
    }

    public static function get_type( $type_name )
    {
        if( isset( self::$post_types[ $type_name ] ) ) {
            return self::$post_types[ $type_name ];
        }

        $types = get_post_types(array('name' => $type_name), 'objetcs');

        $post_type = isset($types[ $type_name ]) ? (array) $types[ $type_name ] : false;
        if( $post_type ) {
            $post_type['labels'] = (array)$post_type['labels'];
            return $post_type;
        }

        return false;
    }

    public static function is_built_in( $type_name = false ) {
        if( ! self::$builtins ) {
            self::$builtins = get_post_types( array('_builtin' => true), 'names' );
        }

        return $type_name !== false ? in_array( $type_name, self::$builtins ) : self::$builtins;
    }

    public static function get_post_types()
    {
        $post_types = self::$post_types;
        if( sizeof($post_types) < 1) {
            return false;
        }

        foreach ( $post_types as &$post_type ):
            foreach ($post_type as $arg => &$value) {
                if( in_array($value, array('1', 'on', 'true')) ) {
                    $value = true;
                }
                elseif( $value == "" ){
                    switch ( $arg ) {
                        case 'menu_position': $value = 30; break;

                        default: unset($post_type[$arg]); break;
                    }
                }
            }
        endforeach;

        return $post_types;
    }
}

