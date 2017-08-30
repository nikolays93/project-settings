<?php

namespace PSettings;

if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly

$page = isset($_GET['page']) ? $_GET['page'] : '';
if( empty($_COOKIE['developer']) && $page != DTSettings::SETTINGS )
  add_action( 'admin_menu', 'PSettings\hide_menus_init', 9999 );

if(! isset(DTSettings::$settings['globals']['check_updates']) )
  disable_updater();

if(! isset(DTSettings::$settings['globals']['clear_dash']) )
  add_action('wp_dashboard_setup', 'PSettings\clear_dash' );

if(! isset(DTSettings::$settings['globals']['clear_toolbar']) ){
  add_action('admin_bar_menu', 'PSettings\clear_toolbar', 666);
  add_action('admin_head', 'PSettings\clear_yoast_from_toolbar');
}

add_action( 'wp_loaded', 'PSettings\custom_post_types', 99 );

function disable_updater(){
    add_action( 'init', create_function( '$a', "remove_action( 'init', 'wp_version_check' );" ), 2 );
    add_filter( 'pre_option_update_core', create_function( '$a', "return null;" ) );
    remove_action( 'wp_version_check', 'wp_version_check' );
    remove_action( 'admin_init', '_maybe_update_core' );
    add_filter( 'pre_transient_update_core', create_function( '$a', "return null;"));
    add_filter( 'pre_site_transient_update_core', create_function( '$a', "return null;"));
    wp_clear_scheduled_hook( 'wp_version_check' );

    remove_action( 'load-plugins.php', 'wp_update_plugins' );
    remove_action( 'load-update.php', 'wp_update_plugins' );
    remove_action( 'load-update-core.php', 'wp_update_plugins' );
    remove_action( 'admin_init', '_maybe_update_plugins' );
    remove_action( 'wp_update_plugins', 'wp_update_plugins' );
    add_filter( 'pre_transient_update_plugins', create_function( '$a', "return null;" ) );
    add_filter( 'pre_site_transient_update_plugins', create_function( '$a', "return null;" ) );
    wp_clear_scheduled_hook( 'wp_update_plugins' );

    remove_action( 'load-themes.php', 'wp_update_themes' );
    remove_action( 'load-update.php', 'wp_update_themes' );
    remove_action( 'load-update-core.php', 'wp_update_themes' );
    remove_action( 'admin_init', '_maybe_update_themes' );
    remove_action( 'wp_update_themes', 'wp_update_themes' );
    add_filter( 'pre_transient_update_themes', create_function( '$a', "return null;" ) );
    add_filter( 'pre_site_transient_update_themes', create_function( '$a', "return null;" ) );
    wp_clear_scheduled_hook( 'wp_update_themes' );
}
function clear_dash(){
    $side = &$GLOBALS['wp_meta_boxes']['dashboard']['side']['core'];
    $normal = &$GLOBALS['wp_meta_boxes']['dashboard']['normal']['core'];

    unset($side['dashboard_quick_press']); //Быстрая публикация
    unset($side['dashboard_recent_drafts']); // Последние черновики
    unset($side['dashboard_primary']); //Блог WordPress
    unset($side['dashboard_secondary']); //Другие Новости WordPress

    unset($normal['dashboard_incoming_links']); //Входящие ссылки
    unset($normal['dashboard_recent_comments']); //Последние комментарии
    unset($normal['dashboard_plugins']); //Последние Плагины
}
function clear_toolbar($wp_admin_bar){
    $wp_admin_bar->remove_node( 'appearance' );
    $wp_admin_bar->remove_node( 'comments' );
    $wp_admin_bar->remove_node( 'updates' );
    $wp_admin_bar->remove_node( 'wpseo-menu' ); // hide yost seo
}
function clear_yoast_from_toolbar(){

    echo '<style rel="stylesheet" type="text/css" media="all">.yoast-seo-score.content-score,.yoast-seo-score.keyword-score,#wpseo-filter{display:none;}</style>';
}
function hide_menus_init(){
    $values = get_option( DTSettings::SETTINGS, false );

    if(!isset($values['globals']['pre_menu'])){
        if(isset($values['globals']['menu'])){
            $menus = explode(',', $values['globals']['menu']);

            foreach ($menus as $menu){
                if(!empty($menu)){
                    $menu = str_replace("admin.php?page=", "", $menu);
                    switch ($menu) {
                        case 'edit.php?post_type=shop_order': $menu = 'woocommerce';break;
                    }
                    remove_menu_page($menu);
                }
            }
        }
    }

    if(!isset($values['globals']['pre_sub_menu'])){
        if(isset($values['globals']['sub_menu'])){
            $sub_menus = explode(',', $values['globals']['sub_menu']);
            foreach ($sub_menus as $sub_menu) {
                $sub_menu = str_replace("admin.php?page=", "", $sub_menu);
                if(!empty($sub_menu)){
                    $parent_children = explode('>', $sub_menu);
                    if(!empty($parent_children[1])){ // на случай ошибки
                        switch ($parent_children[0]) {
                            case 'edit.php?post_type=shop_order': $parent_children[0] = 'woocommerce';break;
                        }
                        remove_submenu_page($parent_children[0], $parent_children[1]);
                    }

                }
            }
        }
    }
}

function get_editable_types(){
    $regstred_types = get_post_types(array(), 'objects');
    $regstred_types = (array)$regstred_types;

    $edit_types = array();
    foreach (DTSettings::$post_types as $key => $value) {
        if(isset($regstred_types[ $key ]))
            $edit_types[$key] = (array)$regstred_types[ $key ]; // post, page, product ..
    }

    return $edit_types;
}
function get_formatted_post_types(){
    if( sizeof(DTSettings::$post_types) < 1)
        return false;

    foreach ( DTSettings::$post_types as &$post_type ):
        foreach ($post_type as $arg => &$value) {
            if( in_array($value, array('1', 'on', 'true')) )
                $value = true;

            if( $value == "" ){
                switch ($arg) {
                    case 'menu_position':
                        $value = 30;
                        break;

                    default:
                        unset($post_type[$arg]);
                        break;
                }
            }
        }
    endforeach;

    return DTSettings::$post_types;
}
function custom_post_types() {
    if($post_types = get_formatted_post_types()){
        $register_types = array_diff_key($post_types, get_editable_types());
        $change_types   = array_diff_key($post_types, $register_types);

        /**
         * Register Types
         */
        foreach (apply_filters( 'dt_register_custom_post_types', $register_types ) as $cpt => $args) {
            register_post_type( $cpt, $args );
        }

        /**
         * Edit Registred Types
         */
        foreach (apply_filters( 'dt_edit_custom_post_types', $change_types ) as $key => $value) {
            $p_object = get_post_type_object( $key );
            if ( ! $p_object )
                break;

            $p_object->labels = (object) array_merge((array) $p_object->labels, (array) $value['labels']);
        }
    }
}

/**
 * Отчистить мета теги
 */
add_action( 'init', __NAMESPACE__ . 'template_head_cleanup' );
function template_head_cleanup() {
  remove_action( 'wp_head', 'feed_links_extra', 3 );                    // Category Feeds
  remove_action( 'wp_head', 'feed_links', 2 );                          // Post and Comment Feeds
  remove_action( 'wp_head', 'rsd_link' );                               // EditURI link
  remove_action( 'wp_head', 'wlwmanifest_link' );                       // Windows Live Writer
  remove_action( 'wp_head', 'index_rel_link' );                         // index link
  remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );            // previous link
  remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );             // start link
  remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 ); // Links for Adjacent Posts
  remove_action( 'wp_head', 'wp_generator' );                           // WP version
}
