<?php

$cpt = array(
	array(
		'id' => 'post_type_name',
		'type' => 'text',
		'label' => 'ID типа записи',
		'desc' => 'The handle (slug) name of the post type, usually plural.',
		'placeholder' => 'к пр. offer',
		'required' => 'true',
		),
	array(
		'id' => 'labels][singular_name',
		'type' => 'text',
		'label' => 'Единственное число',
		'desc' => 'name for one object of this post type.',
		'placeholder' => 'к пр. Акция',
		),
	array(
		'id' => 'labels][name',
		'type' => 'text',
		'label' => 'Множественное число',
		'desc' => '',
		'placeholder' => 'к пр. Акции',
		'required' => 'true',
		),
	array(
		'id' => 'labels][name_admin_bar',
		'type' => 'text',
		'placeholder' => 'к пр. Акцию',
		'label' => 'Винительный падеж',
		//'label' => 'Accusative case',
		'desc' => 'Admin Bar Name',
		),
	array(
		'id' => 'html_1',
		'type' => 'html',
		'value' => "Следующие настройки заполнять не обязательно:"
		),
	array(
		'id' => 'post_type][description',
		'type' => 'textarea',
		'label' => 'Описание',
		),
	);

return $cpt;