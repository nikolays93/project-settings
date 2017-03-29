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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('DT_PS_DIR_PATH', __DIR__);
define('DT_GLOBAL_PAGESLUG', 'project-settings');
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

register_activation_hook( __FILE__, 'project_settings_activation' );
function project_settings_activation(){
	$opt = get_option( DT_GLOBAL_PAGESLUG );
	if( $opt === false || sizeof($opt) < 1)
		dt_projectSettings::set_defaults();
}

class dt_projectSettings //extends AnotherClass
{
	public $page = DT_GLOBAL_PAGESLUG;

	function __construct(){}

	public function check_do_actions(){
		$opts = get_option( DT_GLOBAL_PAGESLUG );

		if(! $opts )
			return false;

		if(! isset($opts['check_updates']) )
			dt_disable_updater();

		if(! isset($opts['clear_dash']) )
			add_action('wp_dashboard_setup', 'dt_clear_dash' );

		if(! isset($opts['clear_toolbar']) ){
			add_action('admin_bar_menu', 'dt_clear_toolbar', 666);
			add_action('admin_head', 'dt_clear_yoast_from_toolbar');
		}
			
		if(isset($_GET['page']) && $_GET['page'] == DT_GLOBAL_PAGESLUG )
			add_action( 'admin_enqueue_scripts', array($this, 'get_assets') );
		elseif(!isset($_COOKIE['developer']))
			add_action( 'admin_menu', 'dt_hide_menus_init', 9999 );

		add_action( 'admin_init', array($this, 'options_settings') );
		add_action( 'admin_menu', array($this, 'options') );
	}

	/**
	 * CallBacks
	 */
	function get_assets(){
		$opts = get_option( DT_GLOBAL_PAGESLUG, false );

		wp_enqueue_script(  'project-settings', plugins_url( basename(__DIR__) . '/assets/project-settings.js' ), array('jquery') );
		wp_localize_script( 'project-settings', 'menu_disabled', array(
			'menu' => _isset_empty($opts['menu']),
			'sub_menu' => _isset_empty($opts['sub_menu']),
			) );
	}
	function inputs_template($args){
		extract( $args );

		$option = DT_GLOBAL_PAGESLUG;
		$vals = get_option( $option, false );

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

		add_settings_section( DT_GLOBAL_PAGESLUG.'_'.$section_slug, $name, $desc_callback, $this->page );
		foreach ($arr_args as $args ) {
			if($args == 'hidden_textarea')
				add_settings_field( 'pre_'.$arg['id'], '', create_function( '$a', "return null;" ),
					$this->page, DT_GLOBAL_PAGESLUG.'_'.$section_slug );

			add_settings_field( $args['id'], $args['label'], array($this, 'inputs_template'),
				$this->page, DT_GLOBAL_PAGESLUG.'_'.$section_slug, $args );
		}
	}

	function get_not_hide_button(){ //has html
		$add_class = (!empty($_COOKIE['developer'])) ? ' button-primary': '';
		?>
		<input type="button" id="setNotHide" class="button<?=$add_class;?>" value="Показать скрытые меню (для браузера)">
	<?php }
	function options_settings() {
		register_setting( DT_GLOBAL_PAGESLUG, DT_GLOBAL_PAGESLUG, array($this, 'validate_settings') );

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
		<div class="wrap">
			<h2>Настройки проекта</h2>
			<form method="post" enctype="multipart/form-data" action="options.php">
				<?php 
				settings_fields(DT_GLOBAL_PAGESLUG);
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
	public static function set_defaults(){
		$defaults = array(
			'menu'     => 'edit-comments.php,users.php,tools.php,',
			'sub_menu' => 'index.php>index.php,index.php>update-core.php,edit.php?post_type=shop_order>edit.php?post_type=shop_order,edit.php?post_type=shop_order>edit.php?post_type=shop_coupon,edit.php?post_type=shop_order>admin.php?page=wc-reports,options-general.php>options-discussion.php,',
			);

		update_option( DT_GLOBAL_PAGESLUG, $defaults );
	}
}

require_once(__DIR__ . '/inc/actions.php');
require_once(__DIR__ . '/inc/admin-callbacks.php');
// require_once(__DIR__ . '/inc/advanced-post-types.php');

if( is_admin() )
	require_once(__DIR__ . '/inc/dt-form-render.php');



$dtps = new dt_projectSettings();
$dtps->check_do_actions();



/*
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
 */