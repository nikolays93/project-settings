<?php

namespace ProjectSettings;

if ( ! defined( 'ABSPATH' ) ) 
    exit; // Exit if accessed directly

if( empty($_COOKIE['developer']) && isset($_GET['page']) && $_GET['page'] != ProjectSettings::OPTION_NAME ){
    add_action( 'admin_menu', __NAMESPACE__ . '\hide_menus_init', 9999 );

    function hide_menus_init(){
        /**
         * Hide menu
         */
        if( ! ProjectSettings::get('pre_menu') && ($menu_str = ProjectSettings::get('menu')) ){
            foreach (explode(',', $menu_str) as $menu) {
                if( ! empty( $menu ) ){
                    $menu = str_replace("admin.php?page=", "", $menu);

                    switch ($menu) {
                        case 'edit.php?post_type=shop_order': $menu = 'woocommerce';break;
                    }

                    remove_menu_page($menu);
                }
            }
        }

        /**
         * Hide submenu
         */
        if( ! ProjectSettings::get('pre_sub_menu') && ($sub_menu_str = ProjectSettings::get('sub_menu')) ){
            foreach (explode(',', $sub_menu_str) as $sub_menu) {
                if( ! empty( $sub_menu ) ){
                    $sub_menu = str_replace("admin.php?page=", "", $sub_menu);
                    $group = explode('>', $sub_menu);

                    if( ! empty( $group[1] ) ) { // на случай ошибки
                        switch ($group[0]) {
                            case 'edit.php?post_type=shop_order': $group[0] = 'woocommerce';break;
                        }

                        remove_submenu_page($group[0], $group[1]);
                    }
                }
            }
        }
    }
}

if( ! ProjectSettings::get('check_updates') ) {
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

if( ! ProjectSettings::get('clear_dash') ) {
    add_action('wp_dashboard_setup', __NAMESPACE__ . '\clear_dash' );
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
        if( ! current_user_can( 'edit_pages' ) ) {
            unset($normal['dashboard_right_now']);
        }
    }
}

if( ! ProjectSettings::get('clear_toolbar') ) {
    add_action('admin_bar_menu', __NAMESPACE__ . '\clear_toolbar', 666);
    add_action('admin_head', __NAMESPACE__ . '\clear_yoast_from_toolbar');

    function clear_toolbar($wp_admin_bar){
        $wp_admin_bar->remove_node( 'appearance' );
        $wp_admin_bar->remove_node( 'comments' );
        $wp_admin_bar->remove_node( 'updates' );
        $wp_admin_bar->remove_node( 'wpseo-menu' ); // hide yost seo
    }

    function clear_yoast_from_toolbar(){

        echo '<style rel="stylesheet" type="text/css" media="all">.yoast-seo-score.content-score,.yoast-seo-score.keyword-score,#wpseo-filter{display:none;}</style>';
    }
}

add_action( 'wp_loaded', __NAMESPACE__ . '\custom_post_types', 99 );
function custom_post_types() {
    if( $post_types = ProjectSettings::get_post_types() ){
        foreach ($post_types as $cpt => $args) {
            if( ! ProjectSettings::is_built_in( $cpt ) ) {
                register_post_type( $cpt, $args );
            }
            else {
                /**
                 * Edit Registred Types
                 */
                $p_object = get_post_type_object( $cpt );
                if ( ! $p_object ) continue;

                $p_object->labels = (object) array_merge((array) $p_object->labels, (array) $args['labels']);
            }
        }
    }
}
