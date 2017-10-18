<?php

$checked = (isset($_GET['do']) && $_GET['do'] === 'add') ? array('checked' => 'true') : array();

$cpt = array(
	array(
		'id' => 'public', // false
		'name' => 'public',
		'type' => 'checkbox',
		'label' => __( 'Public', 'project-settings' ),
		'desc' => __( 'Controls how the type is visible to authors (show_in_nav_menus, show_ui) and readers (exclude_from_search, publicly_queryable)', 'project-settings', 'project-settings' ),
		// 'data-fade-Out' => 'tr#publicly_queryable > td, tr#publicly_queryable > th, tr#show_ui > td, tr#show_ui > th',
		'custom_attributes' => $checked,
		'value' => 'on',
		),
	array(
		'id' => 'publicly_queryable',
		'name' => 'publicly_queryable', // deph: public
		'type' => 'checkbox',
		'label' => __( 'Publicly queryable', 'project-settings' ),
		'desc' => __( 'Show in front query', 'project-settings' ),
		'custom_attributes' => $checked,
		),
	array(
		'id' => 'show_ui',
		'name' => 'show_ui', // deph : public
		'type' => 'checkbox',
		'label' => __( 'Show UI', 'project-settings' ),
		'desc' => __( 'Show User Interface for edit post type', 'project-settings' ),
		'custom_attributes' => $checked,
		),
	array(
		'id' => 'show_in_menu',
		'name' => 'show_in_menu', // show_in_admin_bar, show_in_nav_menus
		'type' => 'checkbox',
		'label' => __( 'Show in Menu', 'project-settings' ),
		'desc' => __( 'Show in WordPress menu', 'project-settings' ),
		'custom_attributes' => $checked,
		),
	array(
		'id' => 'rewrite',
		'name' => 'rewrite', // true
		'type' => 'checkbox',
		'label' => __( 'Rewrite', 'project-settings' ),
		'desc' => __( 'Friendly URL', 'project-settings' ),
		'default' => 'on'
		),
	array(
		'id' => 'has_archive',
		'name' => 'has_archive', // false
		'type' => 'checkbox',
		'label' => __( 'Has archive', 'project-settings' ),
		'desc' => __( 'Archive page support', 'project-settings' ),
		),
	array(
		'id' => 'hierarchical',
		'name' => 'hierarchical', // false
		'type' => 'checkbox',
		'label' => __( 'Hierarchical', 'project-settings' ),
		'desc' => __( 'Parents \ children', 'project-settings' ),
		),
	// array(
	// 	'id' => 'query_var',
	// 	'name' => 'query_var', // deph : post_type_name
	// 	'type' => 'text',
	// 	'label' => __( 'Query var', 'project-settings' ),
	// 	'desc' => __( '$post_type в query_var', 'project-settings' ),
	// 	'data-pattern' => '[id]',
	// 	),
	array(
		'id' => 'capability_type',
		'name' => 'capability_type', // capabilities
		'type' => 'text',
		'label' => __( 'Capability', 'project-settings' ),
		'desc' => __( 'The permissions are the same as..', 'project-settings' ),
		'default' => 'post'
		),
	array(
		'id' => 'menu_position',
		'name' => 'menu_position',
		'type' => 'number',
		'label' => __( 'Menu position', 'project-settings' ),
		'desc' => '',
		),
	// array(
	// 	'id' => 'menu_icon',
	// 	'name' => 'menu_icon',
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