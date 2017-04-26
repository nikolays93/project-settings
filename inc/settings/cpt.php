<?php
$cpt = array(
	array(
		'id' => 'post_type_name',
		'type' => 'text',
		'label' => 'ID типа записи',
		'desc' => 'The handle (slug) name of the post type, usually plural.',
		'placeholder' => 'к пр. news',
		'required' => 'true'
		),
	array(
		'id' => 'singular_name',
		'type' => 'text',
		'label' => 'Единственное число',
		'desc' => 'name for one object of this post type.',
		'placeholder' => 'к пр. Новость'
		),
	array(
		'id' => 'menu_name',
		'type' => 'text',
		'label' => 'Множественное число',
		'desc' => 'display left menu label. same as name (if empty)',
		'placeholder' => 'к пр. Новости'
		),
	);

return $cpt;