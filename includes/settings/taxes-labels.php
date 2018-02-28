<?php

namespace CDevelopers\ProjectSettings;

		// // 'name'              => 'Genres',
		// // 'singular_name'     => 'Genre',
		// 'search_items'      => 'Search Genres',
		// 'all_items'         => 'All Genres',
		// 'view_item '        => 'View Genre',
		// 'parent_item'       => 'Parent Genre',
		// 'parent_item_colon' => 'Parent Genre:',
		// 'edit_item'         => 'Edit Genre',
		// 'update_item'       => 'Update Genre',
		// 'add_new_item'      => 'Add New Genre',
		// 'new_item_name'     => 'New Genre Name',
		// 'menu_name'         => 'Genre',

$o = array(
	// (global)'name'
	// (global)'singular_name'
	'new_item' => _x('New [singular]', 'new_item', DOMAIN),
	'menu_name' => _x('[plural]', 'menu_name', DOMAIN), // Название меню. По умолчанию равен name.
	'all_items' => _x( 'All [plural]', 'all_items', DOMAIN ), // равен menu_name
	'archives' => _x( 'All [plural]', 'archives', DOMAIN ), // равен all_items
	'view_items' => _x( 'Show [plural]', 'view_items', DOMAIN ),
	// (global)'name_admin_bar'
	'add_new' => _x( 'Add [accusative]', 'add_new', DOMAIN ), // title
	'add_new_item' => _x( 'Add new [accusative]', 'add_new_item', DOMAIN ),
	'edit_item' => _x( 'Edit [accusative]', 'edit_item', DOMAIN ),
	'view_item' => _x( 'View [accusative]', 'view_item', DOMAIN ),
	'search_items' => _x( 'Search [accusative]', 'search_items', DOMAIN ),

	'not_found' => _x( 'No posts found', 'not_found', DOMAIN ),
	'not_found_in_trash' => _x( 'No posts found in Trash', 'not_found_in_trash', DOMAIN ),

	'featured_image' => _x( 'Thumbnail', 'featured_image', DOMAIN ),
	'set_featured_image' => _x( 'Set thumbnail', 'set_featured_image', DOMAIN ),
	'remove_featured_image' => _x( 'Remove thumbnail', 'remove_featured_image', DOMAIN ),
	'use_featured_image' => _x( 'Use as thumbnail', 'use_featured_image', DOMAIN ),

	'filter_items_list' => _x( 'filter list', 'filter_items_list', DOMAIN ),
	'items_list_navigation' => _x( 'Navigation', 'items_list_navigation', DOMAIN ),
	'items_list' => _x( 'Items list', 'items_list', DOMAIN ),

	'attributes' => _x( 'Attributes', 'attributes', DOMAIN ),
);

$plural = $single = '';
$cpt = array(
	array(
		'id' => 'labels][new_item',
		'type' => 'text',
		'placeholder' => $o['new_item'],
		'custom_attributes' => array('data-pattern' => $o['new_item']),
		'label' => __( 'New item', DOMAIN ),
		'desc' => __( 'Default is New Post/New Page.', DOMAIN )
		),
	array(
		'id' => 'labels][menu_name',
		'type' => 'text',
		'label' => __( 'Menu Name', DOMAIN ),
		'desc' => __( 'display left menu label. same as name (if empty)', DOMAIN ),
		'placeholder' => $o['menu_name'],
		'custom_attributes' => array('data-pattern' => $o['menu_name']),
		),
	array(
		'id' => 'labels][all_items',
		'type' => 'text',
		'placeholder' => $o['all_items'],
		'custom_attributes' => array('data-pattern' => $o['all_items']),
		'label' => __( 'All items', DOMAIN ),
		'desc' => __( 'String for the submenu. Default is All Posts/All Pages.', DOMAIN )
		),
	array(
		'id' => 'labels][archives',
		'type' => 'text',
		'placeholder' => $o['archives'],
		'custom_attributes' => array('data-pattern' => $o['archives']),
		'label' => __( 'Archives', DOMAIN ),
		'desc' => __( 'Default is All Posts/All Pages.', DOMAIN )
		),
	array(
		'id' => 'labels][view_items',
		'type' => 'text',
		'placeholder' => $o['view_items'],
		'custom_attributes' => array('data-pattern' => $o['view_items']),
		'label' => __( 'View items', DOMAIN ),
		'desc' => __( 'Default is Show Posts/Show Pages.', DOMAIN )
		),

	array(
		'id' => 'labels][add_new',
		'type' => 'text',
		'placeholder' => $o['add_new'],
		'custom_attributes' => array('data-pattern' => $o['add_new']),
		'label' => __( 'Add new', DOMAIN ),
		'desc' => __( 'The add new text. The default is "Add New" for both hierarchical and non-hierarchical post types. When internationalizing this string, please use a gettext context matching your post type.', DOMAIN )
		),
	array(
		'id' => 'labels][add_new_item',
		'type' => 'text',
		'placeholder' => $o['add_new_item'],
		'custom_attributes' => array('data-pattern' => $o['add_new_item']),
		'label' => __( 'Add new item', DOMAIN ),
		'desc' => __( 'Default is Add New Post/Add New Page', DOMAIN )
		),
	array(
		'id' => 'labels][edit_item',
		'type' => 'text',
		'placeholder' => $o['edit_item'],
		'custom_attributes' => array('data-pattern' => $o['edit_item']),
		'label' => __( 'Edit item', DOMAIN ),
		'desc' => __( 'Default is Edit Post/Edit Page', DOMAIN )
		),
	array(
		'id' => 'labels][view_item',
		'type' => 'text',
		'placeholder' => $o['view_item'],
		'custom_attributes' => array('data-pattern' => $o['view_item']),
		'label' => __( 'View item', DOMAIN ),
		'desc' => __( 'Default is View Post/View Page.', DOMAIN )
		),
	array(
		'id' => 'labels][search_items',
		'type' => 'text',
		'placeholder' => $o['search_items'],
		'custom_attributes' => array('data-pattern' => $o['search_items']),
		'label' => __( 'Search items', DOMAIN ),
		'desc' => __( 'Default is Search Posts/Search Pages.', DOMAIN )
		),

	array(
		'id' => 'labels][not_found',
		'type' => 'text',
		'placeholder' => $o['not_found'],
		'custom_attributes' => array('data-pattern' => $o['not_found']),
		'label' => __( 'Not found', DOMAIN ),
		'desc' => __( 'Default is No posts found/No pages found.', DOMAIN )
		),
	array(
		'id' => 'labels][not_found_in_trash',
		'type' => 'text',
		'placeholder' => $o['not_found_in_trash'],
		'custom_attributes' => array('data-pattern' => $o['not_found_in_trash']),
		'label' => __( 'Not found in Trash', DOMAIN ),
		'desc' => __( 'Default is No posts found in Trash/No pages found in Trash.', DOMAIN )
		),
	// array(
	// 	'id' => 'label',
	// 	'name' => 'post_type][label',
	// 	'type' => 'text',
	// 	'label' => __( 'Label', DOMAIN ),
	// 	'desc' => __( 'Used for translate post type', DOMAIN ),
	// 	'custom_attributes' => array('data-pattern' => '[id]'),
	// 	),
	array(
		'id' => 'labels][featured_image',
		'type' => 'text',
		'placeholder' => $o['featured_image'],
		'custom_attributes' => array('data-pattern' => $o['featured_image']),
		'label' => __( 'Featured image', DOMAIN ),
		),
	array(
		'id' => 'labels][set_featured_image',
		'type' => 'text',
		'placeholder' => $o['set_featured_image'],
		'custom_attributes' => array('data-pattern' => $o['set_featured_image']),
		'label' => __( 'Set Featured Image', DOMAIN ),
		),
	array(
		'id' => 'labels][remove_featured_image',
		'type' => 'text',
		'placeholder' => $o['remove_featured_image'],
		'custom_attributes' => array('data-pattern' => $o['remove_featured_image']),
		'label' => __( 'Remove Featured Image', DOMAIN ),
		),
	array(
		'id' => 'labels][use_featured_image',
		'type' => 'text',
		'placeholder' => $o['use_featured_image'],
		'custom_attributes' => array('data-pattern' => $o['use_featured_image']),
		'label' => __( 'Use Featured Image', DOMAIN ),
		),

	array(
		'id' => 'labels][filter_items_list',
		'type' => 'text',
		'placeholder' => $o['filter_items_list'],
		'custom_attributes' => array('data-pattern' => $o['filter_items_list']),
		'label' => __( 'Filter Items List', DOMAIN ),
		),
	array(
		'id' => 'labels][items_list_navigation',
		'type' => 'text',
		'placeholder' => $o['items_list_navigation'],
		'custom_attributes' => array('data-pattern' => $o['items_list_navigation']),
		'label' => __( 'Items List Navigation', DOMAIN ),
		),
	array(
		'id' => 'labels][items_list',
		'type' => 'text',
		'placeholder' => $o['items_list'],
		'custom_attributes' => array('data-pattern' => $o['items_list']),
		'label' => __( 'Items List', DOMAIN ),
		),
		array(
		'id' => 'labels][attributes',
		'type' => 'text',
		'placeholder' => $o['attributes'],
		'custom_attributes' => array('data-pattern' => $o['attributes']),
		'label' => __( 'Attributes', DOMAIN ),
		),
	);

return $cpt;