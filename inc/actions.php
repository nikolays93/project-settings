<?php
function dt_disable_updater(){
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
function dt_clear_dash(){
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
function dt_clear_toolbar($wp_admin_bar){
	$wp_admin_bar->remove_node( 'appearance' );
	$wp_admin_bar->remove_node( 'comments' );
	$wp_admin_bar->remove_node( 'updates' );
    $wp_admin_bar->remove_node( 'wpseo-menu' ); // hide yost seo
}
function dt_clear_yoast_from_toolbar(){

	echo '<style rel="stylesheet" type="text/css" media="all">.yoast-seo-score.content-score,.yoast-seo-score.keyword-score,#wpseo-filter{display:none;}</style>';
}
function dt_hide_menus_init(){
	$values = get_option( DT_GLOBAL_PAGESLUG, false );

	if(!isset($values['pre_menu'])){
		if(isset($values['menu'])){
			$menus = explode(',', $values['menu']);
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
	
	if(!isset($values['pre_sub_menu'])){
		if(isset($values['sub_menu'])){
			$sub_menus = explode(',', $values['sub_menu']);
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