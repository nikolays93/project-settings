<?php

$form = array(
	array(
		'type'      => 'checkbox',
		'id'        => 'check_updates',
		'label'		=> 'Не скрывать обновления',
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

return $form;