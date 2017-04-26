<?php
$plural = $single = '';
$cpt = array(
	array(
		'id' => 'labels][add_new',
		'type' => 'text',
		'placeholder' => 'Добавить ' . $single,
		'data-pattern' => 'Добавить [single]',
		'label' => 'Add new',
		'desc' => 'The add new text. The default is "Add New" for both hierarchical and non-hierarchical post types. When internationalizing this string, please use a gettext context matching your post type.'),

	array(
		'id' => 'labels][add_new_item',
		'type' => 'text',
		'placeholder' => 'Добавить ' . $single,
		'data-pattern' => 'Добавить [single]',
		'label' => 'Add new item',
		'desc' => 'Default is Add New Post/Add New Page'),

	array(
		'id' => 'labels][new_item',
		'type' => 'text',
		'placeholder' => 'Новая ' . $single,
		'data-pattern' => 'Новая [single]',
		'label' => 'New item',
		'desc' => 'Default is New Post/New Page.'),

	array(
		'id' => 'labels][edit_item',
		'type' => 'text',
		'placeholder' => 'Изменить ' . $single,
		'data-pattern' => 'Изменить [single]',
		'label' => 'Edit item',
		'desc' => 'Default is Edit Post/Edit Page'),

	array(
		'id' => 'labels][view_item',
		'type' => 'text',
		'placeholder' => 'Показать ' . $single,
		'data-pattern' => 'Показать [single]',
		'label' => 'View item',
		'desc' => 'Default is View Post/View Page.'),

	array(
		'id' => 'labels][all_items',
		'type' => 'text',
		'placeholder' => 'Все ' . $plural,
		'data-pattern' => 'Все [plural]',
		'label' => 'All items',
		'desc' => 'String for the submenu. Default is All Posts/All Pages.'),

	array(
		'id' => 'labels][search_items',
		'type' => 'text',
		'placeholder' => 'Найти ' . $single,
		'data-pattern' => 'Найти [single]',
		'label' => 'Search items',
		'desc' => 'Default is Search Posts/Search Pages.'),

	array(
		'id' => 'labels][not_found',
		'type' => 'text',
		'placeholder' => $plural . ' не найдены',
		'data-pattern' => '[plural] не найдены',
		'label' => 'Not found',
		'desc' => 'Default is No posts found/No pages found.'),

	array(
		'id' => 'labels][not_found_in_trash',
		'type' => 'text',
		'placeholder' => $plural . ' в корзине не найдены',
		'data-pattern' => '[plural] в корзине не найдены',
		'label' => 'Not found in Trash',
		'desc' => 'Default is No posts found in Trash/No pages found in Trash.'),
	);

return $cpt;