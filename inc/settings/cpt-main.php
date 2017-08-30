<?php
$cpt = array(
	array(
		'id' => 'public', // false
		'name' => 'post_type][public',
		'type' => 'checkbox',
		'label' => 'Public',
		'desc' => 'Публичный или используется только технически',
		// 'data-fade-Out' => 'tr#publicly_queryable > td, tr#publicly_queryable > th, tr#show_ui > td, tr#show_ui > th',
		),
	array(
		'id' => 'publicly_queryable',
		'name' => 'post_type][publicly_queryable', // deph: public
		'type' => 'checkbox',
		'label' => 'Publicly queryable',
		'desc' => 'Показывать во front\'е',
		),
	array(
		'id' => 'show_ui',
		'name' => 'post_type][show_ui', // deph : public
		'type' => 'checkbox',
		'label' => 'Show UI',
		'desc' => 'Показывать управление типом записи',
		),
	array(
		'id' => 'show_in_menu',
		'name' => 'post_type][show_in_menu', // show_in_admin_bar, show_in_nav_menus
		'type' => 'checkbox',
		'label' => 'Show in Menu',
		'desc' => 'Показывать ли в админ-меню',
		),
	array(
		'id' => 'rewrite',
		'name' => 'post_type][rewrite', // true
		'type' => 'checkbox',
		'label' => 'ReWrite',
		'desc' => 'ЧПУ',
		'default' => 'on'
		),
	array(
		'id' => 'has_archive',
		'name' => 'post_type][has_archive', // false
		'type' => 'checkbox',
		'label' => 'Has archive',
		'desc' => 'Поддержка архивной страницы',
		),
	array(
		'id' => 'hierarchical',
		'name' => 'post_type][hierarchical', // false
		'type' => 'checkbox',
		'label' => 'Hierarchical',
		'desc' => 'Родители / тексономии',
		),
	array(
		'id' => 'query_var',
		'name' => 'post_type][query_var', // deph : post_type_name
		'type' => 'text',
		'label' => 'Query var',
		'desc' => '$post_type в query_var',
		'data-pattern' => '[id]',
		),
	array(
		'id' => 'capability_type',
		'name' => 'post_type][capability_type', // capabilities
		'type' => 'text',
		'label' => 'Capability as',
		'desc' => 'Права такие же как..',
		'default' => 'post'
		),
	array(
		'id' => 'menu_position',
		'name' => 'post_type][menu_position',
		'type' => 'number',
		'label' => 'Menu position',
		'desc' => '',
		),
	// array(
	// 	'id' => 'menu_icon',
	// 	'name' => 'post_type][menu_icon',
	// 	'type' => 'text',
	// 	'label' => 'Menu icon',
	// 	'default' => 'dashicons-admin-post',
	// 	)
		// map_meta_cap
		// taxonomies
		// show_in_rest
		// rest_base
	);

return $cpt;