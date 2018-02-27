<?php

/*
Plugin Name: Настройки проекта
Plugin URI: https://github.com/nikolays93/project-settings
Description: Скрывает нераскрытый функционал WordPress. Предоставляет возможность создавать новые типы записей и редактировать заголовки ранее зарегистрированных.
Version: 4.1.1
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

namespace CDevelopers\ProjectSettings;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

const DOMAIN = 'project-settings';

class Utils
{
    const OPTION = 'project-settings';
    const REGISTER = 'project-settings-register';

    private static $initialized;
    private static $settings;
    private function __construct() {}
    private function __clone() {}

    static function activate()
    {

        $defaults = apply_filters( 'project_settings_activate', array(
            'menu'     => 'edit-comments.php,users.php,tools.php,',
            'sub_menu' => 'index.php>index.php,index.php>update-core.php,edit.php?post_type=shop_order>edit.php?post_type=shop_order,edit.php?post_type=shop_order>edit.php?post_type=shop_coupon,edit.php?post_type=shop_order>admin.php?page=wc-reports,options-general.php>options-discussion.php,',
            ) );

        add_option( self::OPTION, $defaults );
    }

    static function uninstall() {

        delete_option(self::OPTION);
    }

    private static function include_required_classes()
    {
        $dir_include = self::get_plugin_dir('includes');
        $dir_class = self::get_plugin_dir('classes');

        $classes = array(
            __NAMESPACE__ . '\Registrations_List_Table' => $dir_include . '/registrations-list-table.php',
            __NAMESPACE__ . '\Registration'             => $dir_include . '/registration.php',
            __NAMESPACE__ . '\WP_Admin_Page'      => $dir_class . '/wp-admin-page.php',
            __NAMESPACE__ . '\WP_Admin_Forms'     => $dir_class . '/wp-admin-forms.php',
        );

        foreach ($classes as $classname => $dir) {
            if( ! class_exists($classname) ) {
                self::load_file_if_exists( $dir );
            }
        }

        // includes
        if( is_admin() ) {
            self::load_file_if_exists( $dir_include . '/admin-page.php' );

            self::load_file_if_exists( $dir_include . '/hooks/hide-menus.php' );
            self::load_file_if_exists( $dir_include . '/hooks/clear-dash.php' );
        }

        self::load_file_if_exists( $dir_include . '/hooks/disable-check-updates.php' );
    }

    public static function initialize()
    {
        if( self::$initialized ) {
            return false;
        }

        load_plugin_textdomain( DOMAIN, false, DOMAIN . '/languages/' );
        self::include_required_classes();
        Registration::register_customs();

        self::$initialized = true;
    }

    /**
     * Записываем ошибку
     */
    public static function write_debug( $msg, $dir )
    {
        if( ! defined('WP_DEBUG_LOG') || ! WP_DEBUG_LOG )
            return;

        $dir = str_replace(__DIR__, '', $dir);
        $msg = str_replace(__DIR__, '', $msg);

        $date = new \DateTime();
        $date_str = $date->format(\DateTime::W3C);

        if( $handle = @fopen(__DIR__ . "/debug.log", "a+") ) {
            fwrite($handle, "[{$date_str}] {$msg} ({$dir})\r\n");
            fclose($handle);
        }
        elseif (defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY) {
            echo sprintf( __('Can not have access the file %s (%s)', DOMAIN),
                __DIR__ . "/debug.log",
                $dir );
        }
    }

    /**
     * Загружаем файл если существует
     */
    public static function load_file_if_exists( $file_array, $args = array(), $once = false, $reqire = false )
    {
        $cant_be_loaded = __('The file %s can not be included', DOMAIN);
        if( is_array( $file_array ) ) {
            $result = array();
            foreach ( $file_array as $id => $path ) {
                if ( ! is_readable( $path ) ) {
                    self::write_debug(sprintf($cant_be_loaded, $path), __FILE__);
                    continue;
                }

                if( $reqire )
                    $result[] = ( $once ) ? require_once( $path ) : require( $path );
                else
                    $result[] = ( $once ) ? include_once( $path ) : include( $path );
            }
        }
        else {
            if ( ! is_readable( $file_array ) ) {
                self::write_debug(sprintf($cant_be_loaded, $file_array), __FILE__);
                return false;
            }

            if( $reqire )
                $result = ( $once ) ? require_once( $file_array ) : require( $file_array );
            else
                $result = ( $once ) ? include_once( $file_array ) : include( $file_array );
        }

        return $result;
    }

    public static function get_plugin_dir( $path = false )
    {
        $result = __DIR__;

        switch ( $path ) {
            case 'classes': $result .= '/includes/classes'; break;
            case 'settings': $result .= '/includes/settings'; break;
            default: $result .= '/' . $path;
        }

        return $result;
    }

    public static function get_plugin_url( $path = false )
    {
        $result = plugins_url(basename(__DIR__) );

        switch ( $path ) {
            default: $result .= '/' . $path;
        }

        return $result;
    }

    /**
     * Получает настройку из self::$settings или из кэша или из базы данных
     */
    public static function get( $prop_name, $default = false )
    {
        if( ! self::$settings )
            self::$settings = get_option( self::OPTION, array() );

        if( 'all' === $prop_name ) {
            if( is_array(self::$settings) && count(self::$settings) )
                return self::$settings;

            return $default;
        }

        return isset( self::$settings[ $prop_name ] )
            ? self::$settings[ $prop_name ] : $default;
    }

    public static function get_settings( $filename, $args = array() )
    {
        return self::load_file_if_exists(
            self::get_plugin_dir('settings') . '/' . $filename, $args );
    }
}

register_activation_hook( __FILE__, array( __NAMESPACE__ . '\Utils', 'activate' ) );
register_uninstall_hook( __FILE__, array( __NAMESPACE__ . '\Utils', 'uninstall' ) );
// register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\Utils', 'deactivate' ) );

add_action( 'plugins_loaded', array( __NAMESPACE__ . '\Utils', 'initialize' ), 10 );
