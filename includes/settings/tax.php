<?php

namespace CDevelopers\ProjectSettings;

$post_types = get_post_types(null, 'objects');
$labels = array();
foreach ($post_types as $post_type) {
	$labels[ $post_type->name ] = $post_type->label;
}

$tax = array(
	array(
		'id' => 'taxonomy_name',
		'type' => 'text',
		'label' => __( 'taxonomy ID', DOMAIN, 'slug' ),
		'desc' => __('The name of the taxonomy.', DOMAIN),
		'placeholder' => __( 'e.g. section', DOMAIN ),
		'required' => 'true',
		'custom_attributes' => !empty($_GET['taxonomy']) ?
			array('readonly' => 'true') : array('required' => 'true'),
		'value' => !empty($_GET['taxonomy']) ? esc_attr($_GET['taxonomy']) : false,
	),
	array(
		'id' => 'post_types][',
		'type' => 'select',
		'label' => __( 'Post type', DOMAIN, 'slug' ),
		'custom_attributes' => array(
			'multiple' => 'multiple',
			'size' => 5,
		),
		'options' => $labels,
	),
	array(
		'id' => 'labels][singular_name',
		'type' => 'text',
		'label' => __( 'Singular', DOMAIN ),
		'desc' => __( 'name for one object of this post type.', DOMAIN ),
		'placeholder' => _x( 'e.g. Section', 'singular', DOMAIN ), // 'к пр. Акция',
		),
	array(
		'id' => 'labels][name',
		'type' => 'text',
		'label' => __( 'Plural', DOMAIN ), // 'Множественное число'
		'desc' => __( 'General name for the post type, usually plural.' ),
		'placeholder' => __( 'e.g. Sections', DOMAIN ), //'к пр. Акции',
		'required' => 'true',
		),
	// array(
	// 	'id' => 'labels][name_admin_bar',
	// 	'type' => 'text',
	// 	'placeholder' => _x( 'e.g. Offer', 'accusative', DOMAIN ),
	// 	'label' => __( 'Accusative case (as singular)', DOMAIN ), //'',
	// 	'desc' => __( 'Admin bar name', DOMAIN ),
	// 	),
	array(
		'id' => 'description',
		'type' => 'textarea',
		'input_class' => 'widefat',
		'label' => __( 'Description', DOMAIN ),
		),
	);

return $tax;