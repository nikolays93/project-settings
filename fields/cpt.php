<?php

$cpt = array(
	array(
		'id' => 'post_type_name',
		'type' => 'text',
		'label' => __( 'Post type ID', 'project-settings', 'slug' ),
		'desc' => __( 'The handle (slug) name of the post type.', 'project-settings' ),
		'placeholder' => __( 'e.g. offer', 'project-settings' ),
		'required' => 'true',
		'custom_attributes' => array(
			'required' => 'true',
			'readonly' => !empty($_GET['post-type']) ? 'true' : false,
		),
		'value' => !empty($_GET['post-type']) ? esc_attr($_GET['post-type']) : false,
	),
	array(
		'id' => 'labels][singular_name',
		'type' => 'text',
		'label' => __( 'Singular', 'project-settings' ),
		'desc' => __( 'name for one object of this post type.', 'project-settings' ),
		'placeholder' => _x( 'e.g. Offer', 'singular', 'project-settings' ), // 'к пр. Акция',
		),
	array(
		'id' => 'labels][name',
		'type' => 'text',
		'label' => __( 'Plural', 'project-settings' ), // 'Множественное число'
		'desc' => __( 'General name for the post type, usually plural.' ),
		'placeholder' => __( 'e.g. Offers', 'project-settings' ), //'к пр. Акции',
		'required' => 'true',
		),
	array(
		'id' => 'labels][name_admin_bar',
		'type' => 'text',
		'placeholder' => _x( 'e.g. Offer', 'accusative', 'project-settings' ),
		'label' => __( 'Accusative case (as singular)', 'project-settings' ), //'',
		'desc' => __( 'Admin bar name', 'project-settings' ),
		),
	array(
		'id' => 'html_1',
		'type' => 'html',
		'value' => __( 'The following settings are optional:', 'project-settings' )
		),
	array(
		'id' => 'description',
		'type' => 'textarea',
		'label' => __( 'Description', 'project-settings' ),
		),
	);

return $cpt;