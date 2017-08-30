<?php

$form = array(
	array(
		'type'      => 'checkbox',
		'id'        => 'globals][check_updates',
		'label'		=> 'Разрешить обновления',
		'desc'      => 'Разрешить WordPress проверять обновления и указывать на их наличие. (Может показывать ошибки на страницах обновления)',
		),
	array(
		'type'      => 'checkbox',
		'id'        => 'globals][clear_dash',
		'label'		=> 'Показывать все элементы консоли',
		'desc'      => 'Показывать все стандартные окна консоли.',
		),
	array(
		'type'      => 'checkbox',
		'id'        => 'globals][clear_toolbar',
		'label'		=> 'Показывать все элементы тулбара',
		'desc'      => 'Показывать все стандартные ссылки верхнего админ. меню (тулбара).',
		),
	array(
		'type'      => 'checkbox',
		'id'        => 'globals][pre_menu',
		'label'		=> 'Показывать скрытые пункты меню',
		'desc'      => '',
		),
	array(
		'type'      => 'checkbox',
		'id'        => 'globals][pre_sub_menu',
		'label'		=> 'Показывать скрытые пункты подменю',
		'desc'      => '',
		),
	array(
		'type'      => 'hidden',
		'id'        => 'globals][menu',
		),
	array(
		'type'      => 'hidden',
		'id'        => 'globals][sub_menu',
		)
	);

return $form;