<?php

namespace CDevelopers\ProjectSettings;

$checked = (isset($_GET['do']) && $_GET['do'] === 'add_taxonomy')
	? array('checked' => 'true') : array();

$cpt = array(
	array(
		'id' => 'public', // false
		'name' => 'public',
		'type' => 'checkbox',
		'label' => __( 'Public', DOMAIN ),
		'desc' => __( 'Controls how the type is visible to authors (show_in_nav_menus, show_ui) and readers (exclude_from_search, publicly_queryable)', DOMAIN ),
		// 'data-fade-Out' => 'tr#publicly_queryable > td, tr#publicly_queryable > th, tr#show_ui > td, tr#show_ui > th',
		'custom_attributes' => $checked,
		),
	array(
		'id' => 'publicly_queryable',
		'name' => 'publicly_queryable', // deph: public
		'type' => 'checkbox',
		'label' => __( 'Publicly queryable', DOMAIN ),
		'desc' => __( 'Show in front query', DOMAIN ),
		'custom_attributes' => $checked,
		),
	array(
		'id' => 'show_ui',
		'name' => 'show_ui', // deph : public
		'type' => 'checkbox',
		'label' => __( 'Show UI', DOMAIN ),
		'desc' => __( 'Show User Interface for edit post type', DOMAIN ),
		'custom_attributes' => $checked,
		),
	array(
		'id' => 'show_in_nav_menus',
		'name' => 'show_in_nav_menus',
		'type' => 'checkbox',
		'label' => __( 'Show in Menu', DOMAIN ),
		'desc' => __( 'Show in WordPress menu', DOMAIN ),
		'custom_attributes' => $checked,
		),
	array(
		'id' => 'rewrite',
		'name' => 'rewrite', // true
		'type' => 'checkbox',
		'label' => __( 'Rewrite', DOMAIN ),
		'desc' => __( 'Friendly URL', DOMAIN ),
		'custom_attributes' => $checked,
		),
	array(
		'id' => 'hierarchical',
		'name' => 'hierarchical', // false
		'type' => 'checkbox',
		'label' => __( 'Hierarchical', DOMAIN ),
		'desc' => __( 'Parents \ children', DOMAIN ),
		),
	// array(
	// 	'id' => 'query_var',
	// 	'name' => 'query_var', // deph : post_type_name
	// 	'type' => 'text',
	// 	'label' => __( 'Query var', DOMAIN ),
	// 	'desc' => __( '$post_type в query_var', DOMAIN ),
	// 	'data-pattern' => '[id]',
	// 	),
	array(
		'id' => 'capabilities',
		'name' => 'capabilities',
		'type' => 'text',
		'label' => __( 'Capability', DOMAIN ),
		'desc' => __( 'The permissions are the same as..', DOMAIN ),
		'default' => 'post'
		),
	array(
		'id' => 'show_tagcloud',
		'name' => 'show_tagcloud',
		'type' => 'checkbox',
		'label' => __( 'Show TagCloud', DOMAIN ),
		'desc' => '',
		'custom_attributes' => $checked,
		),
	// array(
	// 	'id' => 'menu_icon',
	// 	'name' => 'menu_icon',
	// 	'type' => 'text',
	// 	'label' => 'Menu icon',
	// 	'default' => 'dashicons-admin-post',
	// 	)
		// show_in_rest
		// rest_base
		// update_count_callback
	);

return $cpt;