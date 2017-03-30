<?php
/*
Plugin Name: Настройки проекта
Plugin URI:
Description: Скрывает не раскрытый функионал WordPress.
Version: 2.0a
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
*/
/*  Copyright 2017  NikolayS93  (email: NikolayS93@ya.ru)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

namespace DTSettings;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('DT_PS_DIR_PATH', __DIR__);
define('DT_GLOBAL_PAGESLUG', 'project-settings');
define('DT_CPT_OPTION', 'project-types');
define('DT_CCPT_PAGESLUG', 'create_cpt');
define('DT_ECPT_PAGESLUG', 'edit_cpt');

if(!function_exists('is_wp_debug')){
  function is_wp_debug(){
    if( WP_DEBUG ){
      if( defined(WP_DEBUG_DISPLAY) && ! WP_DEBUG_DISPLAY){
        return false;
      }
      return true;
    }
    return false;
  }
}

// require_once(__DIR__ . '/inc/cpt-actions.php');
require_once(__DIR__ . '/inc/settings-actions.php');

if( is_admin() ){
	require_once(__DIR__ . '/inc/class-admin-page.php');
	require_once(__DIR__ . '/inc/class-form-render.php');

	require_once(__DIR__ . '/inc/admin-cpt-page.php');
	require_once(__DIR__ . '/inc/admin-settings-page.php');
}

register_activation_hook( __FILE__, 'DTSettings\project_settings_activation' );
function project_settings_activation(){
	$opt = get_option( DT_GLOBAL_PAGESLUG );
	if( $opt === false || sizeof($opt) < 1)
		DTSettings\set_defaults();

	add_option( DT_CPT_OPTION, array() );
}

function check_do_actions( $opts = false ){
	$page = isset($_GET['page']) ? $_GET['page'] : '';
	if( empty($_COOKIE['developer']) && !in_array($page, array(DT_GLOBAL_PAGESLUG, DT_CCPT_PAGESLUG, DT_ECPT_PAGESLUG)) )
		add_action( 'admin_menu', 'dt_hide_menus_init', 9999 );

	if( !$opts )
		return false;

	if(! isset($opts['check_updates']) )
		dt_disable_updater();

	if(! isset($opts['clear_dash']) )
		add_action('wp_dashboard_setup', 'dt_clear_dash' );

	if(! isset($opts['clear_toolbar']) ){
		add_action('admin_bar_menu', 'dt_clear_toolbar', 666);
		add_action('admin_head', 'dt_clear_yoast_from_toolbar');
	}

		// add_action( 'admin_init', array($this, 'options_settings') );
		// add_action( 'admin_menu', array($this, 'options') );
}

function set_defaults(){
	$defaults = array(
		'menu'     => 'edit-comments.php,users.php,tools.php,',
		'sub_menu' => 'index.php>index.php,index.php>update-core.php,edit.php?post_type=shop_order>edit.php?post_type=shop_order,edit.php?post_type=shop_order>edit.php?post_type=shop_coupon,edit.php?post_type=shop_order>admin.php?page=wc-reports,options-general.php>options-discussion.php,',
		);

	update_option( DT_GLOBAL_PAGESLUG, $defaults );
}

check_do_actions( get_option( DT_GLOBAL_PAGESLUG ) );

function get_admin_assets(){
	$opts = get_option( DT_GLOBAL_PAGESLUG, false );

	wp_enqueue_style( 'project-settings', plugins_url(basename(__DIR__) . '/assets/p-settings.css'), array(), '1.0' );
	wp_enqueue_script(  'project-settings', plugins_url(basename(__DIR__) . '/assets/project-settings.js'), array('jquery') );
	wp_localize_script( 'project-settings', 'menu_disabled', array(
		'menu' => _isset_empty($opts['menu']),
		'sub_menu' => _isset_empty($opts['sub_menu']),
		) );
}

if( isset($_GET['page']) ){
	if(in_array( $_GET['page'], array(DT_GLOBAL_PAGESLUG, DT_CCPT_PAGESLUG, DT_ECPT_PAGESLUG) ))
		add_action( 'admin_enqueue_scripts', 'DTSettings\get_admin_assets' );
}