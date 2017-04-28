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
function get_editable_types(){
	$custom_types = get_option(DT_CPT_OPTION, array() );

	if( !is_array($custom_types) || sizeof($custom_types) < 1)
		return false;

	$regstred_types = get_post_types(array(), 'objects');
	$regstred_types = (array)$regstred_types;
	
	$edit_types = array();
	foreach ($custom_types as $key => $value) {
		if(isset($regstred_types[ $key ]))
			$edit_types[$key] = (array)$regstred_types[ $key ]; // post, page, product ..
	}

	return $edit_types;
}

function dt_custom_post_types() {
	$custom_types = get_option(DT_CPT_OPTION, array() );
	if( !is_array($custom_types) || sizeof($custom_types) < 1)
		return false;

	$edit_types = get_editable_types();
	$register_types = array_diff_key($custom_types, $edit_types);

	/**
	 * Register Types
	 */
	apply_filters( 'dt_register_custom_post_types', $register_types );
	foreach ($register_types as $cpt => $args) {
		register_post_type( $cpt, $args );
	}

	/**
	 * Edit Registred Types
	 */
	// apply_filters( 'dt_edit_custom_post_types', $edit_types );
	// foreach ($edit_types as $key => $value) {
	// 	# code...
	// }

	$p_object = get_post_type_object( 'page' );

	if ( ! $p_object )
		return FALSE;

    // see get_post_type_labels()
	$p_object->labels->name               = 'Locations';
	$p_object->labels->singular_name      = 'Location';
	$p_object->labels->add_new            = 'Add location';
	$p_object->labels->add_new_item       = 'Add new location';
	$p_object->labels->all_items          = 'All locations';
	$p_object->labels->edit_item          = 'Edit location';
	$p_object->labels->name_admin_bar     = 'Location';
	$p_object->labels->menu_name          = 'Location';
	$p_object->labels->new_item           = 'New location';
	$p_object->labels->not_found          = 'No locations found';
	$p_object->labels->not_found_in_trash = 'No locations found in trash';
	$p_object->labels->search_items       = 'Search locations';
	$p_object->labels->view_item          = 'View location';

	return TRUE;	
	
}
add_action( 'wp_loaded', 'dt_custom_post_types', 99 );
