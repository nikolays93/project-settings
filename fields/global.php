<?php

$form = array(
	array(
		'type'      => 'checkbox',
		'id'        => 'check_updates',
		'label'		=> __('Allow updates', 'project-settings'),
		'desc'      => 'Разрешить WordPress проверять обновления и указывать на их наличие. (Может показывать ошибки на страницах обновления)',
		),
	array(
		'type'      => 'checkbox',
		'id'        => 'clear_dash',
		'label'		=> __('Show all dashboard elements', 'project-settings'),
		'desc'      => 'Показывать все стандартные окна консоли.',
		),
	array(
		'type'      => 'checkbox',
		'id'        => 'clear_toolbar',
		'label'		=> __('Show all toolbar elements', 'project-settings'),
		'desc'      => 'Показывать все стандартные ссылки верхнего админ. меню (тулбара).',
		),
	array(
		'type'      => 'checkbox',
		'id'        => 'pre_menu',
		'label'		=> __('Show hidden menu items', 'project-settings'),
		'desc'      => '',
		),
	array(
		'type'      => 'checkbox',
		'id'        => 'pre_sub_menu',
		'label'		=> __('Show hidden sub menu items', 'project-settings'),
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