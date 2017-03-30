<?php

namespace DTSettings;

function page_description(){
	echo apply_filters( 'the_content', 'На этой странице вы непременно смогёте' );
}
add_action( DT_GLOBAL_PAGESLUG . '_after_title', 'DTSettings\page_description', 8, 1 );

function get_not_hide_button(){ //has html
	$add_class = (!empty($_COOKIE['developer'])) ? ' button-primary': '';

	echo '<input type="button" id="setNotHide" class="button'.$add_class.'" value="Показать скрытые меню (для браузера)">';
}
add_action( DT_GLOBAL_PAGESLUG . '_after_title', 'DTSettings\get_not_hide_button', 10, 1 );

new dtAdminPage( DT_GLOBAL_PAGESLUG,
	array(
		'parent' => 'options-general.php',
		'title' => __('Project settings','domain'),
		'menu' => __('Project settings','domain'),
		),
	'DTSettings\page_settings_body' );

function page_settings_body(){
	$form = array(
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
			),
		array(
			'type'      => 'checkbox',
			'id'        => 'pre_menu',
			'label'		=> 'Не скрывать меню',
			'desc'      => '',
			),
		array(
			'type'      => 'checkbox',
			'id'        => 'pre_sub_menu',
			'label'		=> 'Не скрывать под меню',
			'desc'      => '',
			),
		array(
			'type'      => 'hidden',
			'id'        => 'menu',
			),
		array(
			'type'      => 'hidden',
			'id'        => 'sub_menu',
			)
		);

	$result = array();
	foreach ($form as $filter) {
		$result[] = apply_filters( 'dt_admin_options_page_render', $filter, DT_GLOBAL_PAGESLUG );
	}
	DTForm::render( $result, get_option(DT_GLOBAL_PAGESLUG), true );
}