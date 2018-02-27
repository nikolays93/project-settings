<?php

namespace CDevelopers\ProjectSettings;

if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly

$page = isset($_GET['page']) ? $_GET['page'] : false;
if( empty($_COOKIE['developer']) && $page !== Utils::OPTION ){
    add_action( 'admin_menu', __NAMESPACE__ . '\hide_menus_init', 9999 );

    function hide_menus_init()
    {
        /**
         * Hide menu
         */
        if( ! Utils::get('pre_menu') && ($menu_str = Utils::get('menu')) ){
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
        if( ! Utils::get('pre_sub_menu') && ($sub_menu_str = Utils::get('sub_menu')) ){
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