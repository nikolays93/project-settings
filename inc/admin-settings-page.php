<?php

namespace DTSettings;

function get_not_hide_button(){ //has html
	$add_class = (!empty($_COOKIE['developer'])) ? ' button-primary': '';

	echo '<input type="button" id="setNotHide" class="button'.$add_class.'" value="Показать скрытые меню (для браузера)">';
}

new dtAdminPage( DT_GLOBAL_PAGESLUG,
	array(
		'parent' => 'options-general.php',
		'title' => __('Project settings','domain'),
		'menu' => __('Project settings','domain'),
		),
	'page_settings_body' );
function page_settings_body(){
	echo "Some test";
}

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


function get_admin_assets(){
	$opts = get_option( DT_GLOBAL_PAGESLUG, false );

	wp_enqueue_script(  'project-settings', plugins_url( basename(__DIR__) . '/assets/project-settings.js' ), array('jquery') );
	wp_localize_script( 'project-settings', 'menu_disabled', array(
		'menu' => _isset_empty($opts['menu']),
		'sub_menu' => _isset_empty($opts['sub_menu']),
		) );
}

if(isset($_GET['page']) && $_GET['page'] == DT_GLOBAL_PAGESLUG )
	add_action( 'admin_enqueue_scripts', array($this, 'get_admin_assets') );