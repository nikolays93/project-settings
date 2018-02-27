<?php

namespace CDevelopers\ProjectSettings;

$checked = (isset($_GET['do']) && $_GET['do'] === 'add')
	? array('checked' => 'true') : array();

$cpt = array(
	array(
		'id'    => 'supports][title',
		'type'  => 'hidden',
		'label' => __( 'Post Title', DOMAIN ),
		'value' => 'title',
		),
	array(
		'id'    => 'supports][editor',
		'type'  => 'hidden',
		'label' => __( 'Content Editor', DOMAIN ),
		'value' => 'editor',
		),
	array(
		'id'    => 'supports][thumbnail',
		'type'  => 'checkbox',
		'label' => __( 'Post Thumbnail', DOMAIN ),
		'value' => 'thumbnail',
		'custom_attributes' => $checked,
		),
	array(
		'id'    => 'supports][excerpt',
		'type'  => 'checkbox',
		'label' => __( 'Post Excerpt', DOMAIN ),
		'value' => 'excerpt',
		),
	array(
		'id'    => 'supports][custom-fields',
		'type'  => 'checkbox',
		'label' => __( 'Custom Fields', DOMAIN ),
		'value' => 'custom-fields',
		),
	array(
		'id'    => 'supports][page-attributes',
		'type'  => 'checkbox',
		'label' => __( 'Page Attributes', DOMAIN ),
		'value' => 'page-attributes',
		),
	);

return $cpt;