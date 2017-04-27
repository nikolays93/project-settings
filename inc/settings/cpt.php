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
		'id' => 'labels][singular_name',
		'type' => 'text',
		'label' => 'Единственное число',
		'desc' => 'name for one object of this post type.',
		'placeholder' => 'к пр. Новость'
		),
	array(
		'id' => 'label',
		'type' => 'text',
		'label' => 'Множественное число',
		'desc' => '',
		'placeholder' => 'к пр. Новости'
		),
	array(
		'id' => 'html_1',
		'type' => 'html',
		'value' => "Следующие настройки заполнять не обязательно:"
		// todo: random string
		// "Если вы не знаете как использовать следующие настройки, рекомендую оставить по умолчанию:"
		),
	array(
		'id' => 'description',
		'type' => 'textarea',
		'label' => 'Описание',
		),
	);

return $cpt;