<?php
/*
Plugin Name: Настройки проекта
Plugin URI:
Description: Скрывает не раскрытый функионал WordPress.
Version: 1.0
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
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
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

register_activation_hook( __FILE__, 'project_settings_activation' );
function project_settings_activation(){
	$tdps = new dt_projectSettings();
	if($tdps->is_empty_settings())
		$tdps->set_defaults();
}

class dt_projectSettings //extends AnotherClass
{
	public $option_name = 'project-settings';
	protected $option_values = false;
	protected $page = '.php'; // $option_name . $page

	function __construct(){
		$this->page = $this->option_name . $this->page;
		$this->option_values = get_option( $this->option_name, false );
	}

	public function set_actions(){
		extract($this->option_values);

		if(! isset($check_updates) )
			$this->disable_updater();

		if(! isset($clear_dash) )
			add_action('wp_dashboard_setup', array($this, 'clear_dash') );

		if(! isset($clear_toolbar) ){
			add_action('admin_bar_menu', array($this, 'clear_toolbar'), 666);
			add_action('admin_head', array($this, 'clear_yoast_from_toolbar'));
		}
			
		if ( (isset($_GET['page']) && $_GET['page'] == 'project-settings.php') || isset($_COOKIE['developer']) ){}
		else { add_action('admin_menu', array($this, 'hide_menus_init'), 9999 ); }

		add_action( 'admin_menu', array($this, 'options') );
		add_action( 'admin_init', array($this, 'options_settings') );
		add_action( 'admin_enqueue_scripts', array($this, 'get_assets') );
	}
	public function is_empty_settings(){
		if(sizeof($this->option_values) >= 1)
			return false;
		else
			return true;
	}

	/**
	 * CallBacks
	 */
	function get_assets(){
		wp_enqueue_script(  'project-settings', plugins_url( basename(__DIR__) . '/assets/project-settings.js' ), array('jquery') );
		wp_localize_script( 'project-settings', 'menu_disabled', array(
			'menu' => isset($this->option_values['menu']) ? $this->option_values['menu'] : '',
			'sub_menu' => isset($this->option_values['sub_menu']) ? $this->option_values['sub_menu'] : '',
			) );
	}
	function inputs_template($args){
		extract( $args );

		$option = $this->option_name;
		$vals = $this->option_values;

		$value = (isset($vals[$id])) ? $vals[$id] : false; 

		echo "<label>";
		switch ( $type ) {  
			case 'checkbox':
				$checked = $value ? " checked='checked'" : '';
				echo "<input value='1' type='checkbox' id='$id' name='{$option}[$id]'$checked />";  
				break;
			case 'hidden_textarea':
				$hidden = !is_wp_debug() ? ' style="display: none;"' : '';
				$checked = (isset($vals['pre_'.$id])) ? " checked='checked'" : "";
				echo "<input type='checkbox' name='{$option}[pre_$id]'{$checked}>
				<textarea id='$id' name='{$option}[$id]' cols='60' rows='6'{$hidden}>{$value}</textarea>";  
				break;
		}
		echo ( isset($desc) ) ? "<div class='description'>".apply_filters( 'the_content', $desc)."</div>" : "";
		echo "</label>";  
	}
	function add_section($name='string', $arr_args=array(), $section_slug='string', $desc_callback=''){
		if($name === 'string' || sizeof($arr_args) == 0 || $section_slug === 'string')
			return;

		if($desc_callback != '')
			$desc_callback = array($this, $desc_callback);

		add_settings_section( $this->option_name.'_'.$section_slug, $name, $desc_callback, $this->page );
		foreach ($arr_args as $args ) {
			if($args == 'hidden_textarea')
				add_settings_field( 'pre_'.$arg['id'], '', create_function( '$a', "return null;" ),
					$this->page, $this->option_name.'_'.$section_slug );

			add_settings_field( $args['id'], $args['label'], array($this, 'inputs_template'),
				$this->page, $this->option_name.'_'.$section_slug, $args );
		}
	}
	function get_not_hide_button(){ //has html
		$add_class = (!empty($_COOKIE['developer'])) ? ' button-primary': '';
		?>
		<input type="button" id="setNotHide" class="button<?=$add_class;?>" value="Показать скрытые меню (для браузера)">
	<?php }
	function options_settings() {
		register_setting( $this->option_name, $this->option_name, array($this, 'validate_settings') );

		$arr_args = array(
			array(
				'type'      => 'checkbox',
				'id'        => 'check_updates',
				'label'		=> 'Проверять обновления',
				'desc'      => 'Разрешить WordPress проверять обновления и указывать на их наличие. (Может показывать ошибки на страницах обновления)',
				),
			array(
				'type'      => 'checkbox',
				'id'        => 'clear_dash',
				'label'		=> 'Не очищать консоль',
				'desc'      => 'Показывать все стандартные окна консоли.',
				),
			array(
				'type'      => 'checkbox',
				'id'        => 'clear_toolbar',
				'label'		=> 'Не очищать верхнее меню',
				'desc'      => 'Показывать все стандартные ссылки верхнего админ. меню (тулбара).',
				)
			);
		$this->add_section('Основные настройки', $arr_args, 'global');
		
		$arr_args = array(
			array(
				'type'      => 'hidden_textarea',
				'id'        => 'menu',
				'label'		=> 'Не скрывать меню',
				'desc'      => '',
				),
			array(
				'type'      => 'hidden_textarea',
				'id'        => 'sub_menu',
				'label'		=> 'Не скрывать под меню',
				'desc'      => '',
				)
			);
		$this->add_section('Настройки меню', $arr_args, 'menu', 'get_not_hide_button');
	}
	function options(){
		add_options_page( 'Настройки проекта', 'Настройки проекта', 'manage_options',
			$this->page, array($this, 'options_preview') );
	}
	function options_preview(){ // has html	?>
		<style>
			#adminmenu li {
				position: relative;
			}
			#adminmenu li .after {
				position: absolute;
				z-index: 100;
				top: 0;
				right: 0;
				display: block;
				text-align: center;
				width: 34px;
				height: 34px;
				color: #444;
				opacity: 1;
			}
			#adminmenu li .after.hide {
				color: #fff;
			}
			#adminmenu>li>.after {
				line-height: 34px;
			}
		</style>
		<div class="wrap">
			<h2>Настройки проекта</h2>
			<form method="post" enctype="multipart/form-data" action="options.php">
				<?php 
				settings_fields($this->option_name);
				do_settings_sections($this->page);
				?>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
			</form>
			<?php /* debug ?>
			<pre style="width: 100%; overflow: auto; height:300px;"><?php //print_r( $GLOBALS[ 'submenu' ]);?></pre>
			<?php */ ?>
		</div>
	<?php }
	function validate_settings($input){
		// file_put_contents( plugin_dir_path( __FILE__ ) .'/debug.log', print_r($input, 1) );
		$valid_input = array();

		if(sizeof($input) > 0){
			foreach ($input as $k => $v) {
				$valid_input[$k] = $v;
			}
		}

		return $valid_input;
	}

	/**
	 * Actions
	 */
	public function set_defaults(){
		$defaults = array(
			'menu'     => 'edit-comments.php,users.php,tools.php,',
			'sub_menu' => 'index.php>index.php,index.php>update-core.php,edit.php?post_type=shop_order>edit.php?post_type=shop_order,edit.php?post_type=shop_order>edit.php?post_type=shop_coupon,edit.php?post_type=shop_order>admin.php?page=wc-reports,options-general.php>options-discussion.php,',
			);

		update_option( $this->option_name, $defaults );
	}
	private function disable_updater(){
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
		$values = $this->option_values;

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
}
$dtps = new dt_projectSettings();
$dtps->set_actions();