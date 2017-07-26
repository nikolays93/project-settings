<?php

$form = array(
	array(
		'type'      => 'checkbox',
		'id'        => 'check_updates',
		'label'		=> 'Разрешить обновления',
		'desc'      => 'Разрешить WordPress проверять обновления и указывать на их наличие. (Может показывать ошибки на страницах обновления)',
		),
	array(
		'type'      => 'checkbox',
		'id'        => 'clear_dash',
		'label'		=> 'Показывать все элементы консоли',
		'desc'      => 'Показывать все стандартные окна консоли.',
		),
	array(
		'type'      => 'checkbox',
		'id'        => 'clear_toolbar',
		'label'		=> 'Показывать все элементы тулбара',
		'desc'      => 'Показывать все стандартные ссылки верхнего админ. меню (тулбара).',
		),
	array(
		'type'      => 'checkbox',
		'id'        => 'pre_menu',
		'label'		=> 'Показывать скрытые пункты меню',
		'desc'      => '',
		),
	array(
		'type'      => 'checkbox',
		'id'        => 'pre_sub_menu',
		'label'		=> 'Показывать скрытые пункты подменю',
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

return $form;