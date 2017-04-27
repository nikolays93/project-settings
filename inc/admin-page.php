<?php
namespace DTSettings;

/**
 * Actions And Filters
 */
add_filter( DT_GLOBAL_PAGESLUG . '_columns', function(){return 2;} );
add_action( DT_GLOBAL_PAGESLUG . '_inside_side_container', 'submit_button', 20 );

if( ! _isset_false($_GET['post-type'])){
	add_filter( 'DTSettings\dt_admin_options', function($inputs, $option){
		$defaults = array(
			'public' => 'on',
			'publicly_queryable' => 'on',
			'show_ui' => 'on',
			'show_in_menu' => 'on',
			'has_archive' => 'on',
			'hierarchical' => 'on',
			'supports][title' => 'on',
			'supports][editor' => 'on',
			'supports][thumbnail' => 'on',
			'supports][excerpt' => 'on',
			'supports][custom-fields' => 'on',
			'supports][page-attributes' => 'on',
			);

		foreach ($inputs as &$input) {
			if( array_key_exists($input['id'], $defaults) )
				$input['value'] = $defaults[ $input['id'] ];
		}

		return $inputs;
	}, 12, 2 );


	add_action(DT_GLOBAL_PAGESLUG . '_inside_page_content', function(){
		echo "Заполните данные ниже чтобы создать новый тип записи или нажмите на 'шестеренку настроек' типа записи в меню слева.";
	}, 5);

	// add_action(DT_GLOBAL_PAGESLUG . '_inside_page_content', function(){
		
	// }, 15);
}

function get_cpt_or_pt(){
	$pt = _isset_false($_GET['post-type']);
	//_debug(WPForm::active( DT_CPT_OPTION, $pt, true ));
	$active = WPForm::active( DT_CPT_OPTION, $pt );
	if( ! $active && $pt ){
		$active = get_post_type_object( $pt );
	}
	$act = (array)$active;
	$act['post_type_name'] = $pt;
	return $act;
}

/**
 * Page Renders
 */
$page = new WPAdminPageRender( DT_GLOBAL_PAGESLUG,
	array(
		'parent' => 'options-general.php',
		'title' => __('Настройки проекта'),
		'menu' => __('Настройки проекта'),
		),
	'DTSettings\settings_page', DT_CPT_OPTION, 'DTSettings\valid' );

function settings_page(){
	WPForm::render(
		apply_filters( 'DTSettings\dt_admin_options', include('settings/cpt.php'), DT_CPT_OPTION ),
		get_cpt_or_pt(),
		true
		);
}

$page->add_metabox( 'project-types-main', 'Настройки', function(){
	WPForm::render(
    	apply_filters( 'DTSettings\dt_admin_options', include('settings/cpt-main.php'), DT_CPT_OPTION ),
    	get_cpt_or_pt(),
    	true,
    	array('item_wrap' => false)
    	);
});

$page->add_metabox( 'project-types-supports', 'Возможности типа записи', function(){
	WPForm::render(
    	apply_filters( 'DTSettings\dt_admin_options', include('settings/cpt-supports.php'), DT_CPT_OPTION ),
    	get_cpt_or_pt(),
    	true,
    	array('clear_value' => false)
    	);
});

$page->add_metabox( 'project-types-labels', 'Надписи', function(){
	WPForm::render(
    	apply_filters( 'DTSettings\dt_admin_options', include('settings/cpt-labels.php'), DT_CPT_OPTION ),
    	get_cpt_or_pt(),
    	true
    	);
});

/**
 * General Project Settings
 */
$page->add_metabox( 'project-settings', 'Настройки', function(){
	WPForm::render(
    	apply_filters( 'DTSettings\dt_admin_options', include('settings/global.php'), DT_CPT_OPTION ),
    	WPForm::active( DT_GLOBAL_PAGESLUG, false, true ),
    	true,
    	array('hide_desc' => true)
    	);
}, 'side');

add_action( DT_GLOBAL_PAGESLUG . '_inside_side_container', function(){
	$add_class = (!empty($_COOKIE['developer'])) ? ' button-primary': '';

	echo '<p><input type="button" id="setNotHide" class="button'.$add_class.'" value="Показать скрытые меню (для браузера)"></p>';
}, 5);

$page->set_metaboxes();

/**
 * Validate Input's Values
 */
function valid( $values ){
	// # Globals
	$globals = array();
	$gls = array('check_updates', 'clear_dash', 'clear_toolbar', 'pre_menu', 'pre_sub_menu', 'menu', 'sub_menu');
	foreach ($values as $key => $value) {
		if(in_array($key, $gls)){
			if($value != 'false') // str
				$globals[$key] = $value;

			unset($values[$key]);
		}
	}
	update_option( DT_GLOBAL_PAGESLUG, $globals );
	
	// # Post Type
	$all_cpts = get_option(DT_CPT_OPTION, array() );
	if( ! $values['post_type_name'] )
		return $all_cpts;

	if( !is_array($all_cpts) )
		return false;
	
	foreach ($values as $key => $value) {
		if( ! $value )
			unset($values[$key]);
		elseif( $value == 'on' )
			$values[$key] = true;

		if( $key == 'labels' && is_array($value) ) {
			foreach ($value as $lkey => $lvalue) {
				if( $lvalue == '' )
					unset($values[$key][$lkey]);
			}
			if(sizeof($value) < 1)
				unset($values[$key]);
		}

		if($key == 'supports' && is_array($value) )
			$values[$key] = array_keys( $value );
	}

	$name = $values['post_type_name'];
	unset($values['post_type_name']);
	$all_cpts[ $name ] = $values;

	file_put_contents( DTS_DIR . '/debug.log', array(print_r($globals, 1), print_r($all_cpts, 1)) );
	return $all_cpts;
}