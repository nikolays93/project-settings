<?php

namespace CDevelopers\ProjectSettings;

$cpt = array(
	array(
		'id' => 'post_type_name',
		'type' => 'text',
		'label' => __( 'Post type ID', DOMAIN, 'slug' ),
		'desc' => __( 'The handle (slug) name of the post type.', DOMAIN ),
		'placeholder' => __( 'e.g. offer', DOMAIN ),
		'required' => 'true',
		'custom_attributes' => !empty($_GET['post-type']) ?
			array('readonly' => 'true') : array('required' => 'true'),
		'value' => !empty($_GET['post-type']) ? esc_attr($_GET['post-type']) : false,
	),
	array(
		'id' => 'labels][singular_name',
		'type' => 'text',
		'label' => __( 'Singular', DOMAIN ),
		'desc' => __( 'name for one object of this post type.', DOMAIN ),
		'placeholder' => _x( 'e.g. Offer', 'singular', DOMAIN ), // 'к пр. Акция',
		),
	array(
		'id' => 'labels][name',
		'type' => 'text',
		'label' => __( 'Plural', DOMAIN ), // 'Множественное число'
		'desc' => __( 'General name for the post type, usually plural.' ),
		'placeholder' => __( 'e.g. Offers', DOMAIN ), //'к пр. Акции',
		'required' => 'true',
		),
	array(
		'id' => 'labels][name_admin_bar',
		'type' => 'text',
		'placeholder' => _x( 'e.g. Offer', 'accusative', DOMAIN ),
		'label' => __( 'Accusative case (as singular)', DOMAIN ), //'',
		'desc' => __( 'Admin bar name', DOMAIN ),
		),
	array(
		'id' => 'description',
		'type' => 'textarea',
		'input_class' => 'widefat',
		'label' => __( 'Description', DOMAIN ),
		),
	);

return $cpt;