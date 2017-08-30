<?php

$o = array(
	// (global)'name' => '[plural]', // основное название для типа записи, обычно во множественном числе.
	// (global)'singular_name' => '[singular]', // название для одной записи этого типа.
	'new_item' => 'Новая [singular]',
	'menu_name' => '[plural]', // Название меню. По умолчанию равен name.
	'all_items' => 'Все [plural]', // равен menu_name
	'archives' => 'Все [plural]', // равен all_items
	'view_items' => 'Показать [plural]',
	// (global)'name_admin_bar' => '[accusative]', // равен singular_name.
	'add_new' => 'Добавить [accusative]', // title
	'add_new_item' => 'Добавить [accusative]',
	'edit_item' => 'Редактировать [accusative]',
	'view_item' => 'Посмотреть [accusative]',
	'search_items' => 'Найти [accusative]',

	'not_found' => 'По вашему запросу ничего не найдено',
	'not_found_in_trash' => 'В корзине, по вашему запросу ничего не найдено',

	'featured_image' => 'Миниатюра',
	'set_featured_image' => 'Установить миниатюру',
	'remove_featured_image' => 'Удалить миниатюру',
	'use_featured_image' => 'Использовать как миниатюру',

	'filter_items_list' => 'Фильтровать список',
	'items_list_navigation' => 'Навигация',
	'items_list' => 'Список',

	'attributes' => 'Свойства',
);

$plural = $single = '';
$cpt = array(
	array(
		'id' => 'label',
		'name' => 'post_type][label',
		'type' => 'text',
		'label' => 'Label',
		'desc' => 'Used for translate post type',
		'data-pattern' => '[id]',
		),
	array(
		'id' => 'labels][new_item',
		'type' => 'text',
		'placeholder' => 'Новая ' . $single,
		'data-pattern' => $o['new_item'],
		'label' => 'New item',
		'desc' => 'Default is New Post/New Page.'
		),
	array(
		'id' => 'labels][menu_name',
		'type' => 'text',
		'label' => 'Menu Name',
		'desc' => 'display left menu label. same as name (if empty)',
		'placeholder' => 'к пр. Новости',
		'data-pattern' => $o['menu_name'],
		),
	array(
		'id' => 'labels][all_items',
		'type' => 'text',
		'placeholder' => 'Все ' . $plural,
		'data-pattern' => $o['all_items'],
		'label' => 'All items',
		'desc' => 'String for the submenu. Default is All Posts/All Pages.'
		),
	array(
		'id' => 'labels][archives',
		'type' => 'text',
		'placeholder' => 'Все ' . $plural,
		'data-pattern' => $o['archives'],
		'label' => 'Archives',
		'desc' => 'Default is All Posts/All Pages.'
		),
	array(
		'id' => 'labels][view_items',
		'type' => 'text',
		'placeholder' => 'Показать ' . $plural,
		'data-pattern' => $o['view_items'],
		'label' => 'View items',
		'desc' => 'Default is Show Posts/Show Pages.'
		),

	array(
		'id' => 'labels][add_new',
		'type' => 'text',
		'placeholder' => 'Добавить ' . $single,
		'data-pattern' => $o['add_new'],
		'label' => 'Add new',
		'desc' => 'The add new text. The default is "Add New" for both hierarchical and non-hierarchical post types. When internationalizing this string, please use a gettext context matching your post type.'
		),
	array(
		'id' => 'labels][add_new_item',
		'type' => 'text',
		'placeholder' => 'Добавить ' . $single,
		'data-pattern' => $o['add_new_item'],
		'label' => 'Add new item',
		'desc' => 'Default is Add New Post/Add New Page'
		),
	array(
		'id' => 'labels][edit_item',
		'type' => 'text',
		'placeholder' => 'Изменить ' . $single,
		'data-pattern' => $o['edit_item'],
		'label' => 'Edit item',
		'desc' => 'Default is Edit Post/Edit Page'
		),
	array(
		'id' => 'labels][view_item',
		'type' => 'text',
		'placeholder' => 'Показать ' . $single,
		'data-pattern' => $o['view_item'],
		'label' => 'View item',
		'desc' => 'Default is View Post/View Page.'
		),
	array(
		'id' => 'labels][search_items',
		'type' => 'text',
		'placeholder' => 'Найти ' . $single,
		'data-pattern' => $o['search_items'],
		'label' => 'Search items',
		'desc' => 'Default is Search Posts/Search Pages.'
		),

	array(
		'id' => 'labels][not_found',
		'type' => 'text',
		'placeholder' => $o['not_found'],
		'data-pattern' => $o['not_found'],
		'label' => 'Not found',
		'desc' => 'Default is No posts found/No pages found.'
		),
	array(
		'id' => 'labels][not_found_in_trash',
		'type' => 'text',
		'placeholder' => $o['not_found_in_trash'],
		'data-pattern' => $o['not_found_in_trash'],
		'label' => 'Not found in Trash',
		'desc' => 'Default is No posts found in Trash/No pages found in Trash.'
		),

	array(
		'id' => 'labels][featured_image',
		'type' => 'text',
		'placeholder' => $o['featured_image'],
		'data-pattern' => $o['featured_image'],
		'label' => 'Featured image',
		),
	array(
		'id' => 'labels][set_featured_image',
		'type' => 'text',
		'placeholder' => $o['set_featured_image'],
		'data-pattern' => $o['set_featured_image'],
		'label' => 'Set Featured Image',
		),
	array(
		'id' => 'labels][remove_featured_image',
		'type' => 'text',
		'placeholder' => $o['remove_featured_image'],
		'data-pattern' => $o['remove_featured_image'],
		'label' => 'Remove Featured Image',
		),
	array(
		'id' => 'labels][use_featured_image',
		'type' => 'text',
		'placeholder' => $o['use_featured_image'],
		'data-pattern' => $o['use_featured_image'],
		'label' => 'Use Featured Image',
		),

	array(
		'id' => 'labels][filter_items_list',
		'type' => 'text',
		'placeholder' => $o['filter_items_list'],
		'data-pattern' => $o['filter_items_list'],
		'label' => 'Filter Items List',
		),
	array(
		'id' => 'labels][items_list_navigation',
		'type' => 'text',
		'placeholder' => $o['items_list_navigation'],
		'data-pattern' => $o['items_list_navigation'],
		'label' => 'Items List Navigation',
		),
	array(
		'id' => 'labels][items_list',
		'type' => 'text',
		'placeholder' => $o['items_list'],
		'data-pattern' => $o['items_list'],
		'label' => 'Items List',
		),
		array(
		'id' => 'labels][attributes',
		'type' => 'text',
		'placeholder' => $o['attributes'],
		'data-pattern' => $o['attributes'],
		'label' => 'Attributes',
		),
	);

return $cpt;