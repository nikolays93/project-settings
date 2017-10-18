<?php

$checked = (isset($_GET['do']) && $_GET['do'] === 'add') ? array('checked' => 'true') : array();

$cpt = array(
	array(
		'id'    => 'supports][title',
		'type'  => 'hidden',
		'label' => __( 'Post Title', 'project-settings' ),
		'value' => 'title',
		),
	array(
		'id'    => 'supports][editor',
		'type'  => 'hidden',
		'label' => __( 'Content Editor', 'project-settings' ),
		'value' => 'editor',
		),
	array(
		'id'    => 'supports][thumbnail',
		'type'  => 'checkbox',
		'label' => __( 'Post Thumbnail', 'project-settings' ),
		'value' => 'thumbnail',
		'custom_attributes' => $checked,
		),
	array(
		'id'    => 'supports][excerpt',
		'type'  => 'checkbox',
		'label' => __( 'Post Excerpt', 'project-settings' ),
		'value' => 'excerpt',
		),
	array(
		'id'    => 'supports][custom-fields',
		'type'  => 'checkbox',
		'label' => __( 'Custom Fields', 'project-settings' ),
		'value' => 'custom-fields',
		),
	array(
		'id'    => 'supports][page-attributes',
		'type'  => 'checkbox',
		'label' => __( 'Page Attributes', 'project-settings' ),
		'value' => 'page-attributes',
		),
	);

return $cpt;