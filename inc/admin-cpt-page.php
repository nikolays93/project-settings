<?php
namespace DTSettings;

add_filter( DT_CCPT_PAGESLUG . '_columns', function(){return 2;} );
add_filter( DT_ECPT_PAGESLUG . '_columns', function(){return 2;} );

new dtAdminPage( DT_CCPT_PAGESLUG,
	array(
		'parent' => 'options-general.php',
		'title' => __('Create custom post type','domain'),
		'menu' => __('Add post type','domain'),
		),
	'DTSettings\page_cpt_body', DT_CPT_OPTION, 'DTSettings\cpt_validate' );

new dtAdminPage( DT_ECPT_PAGESLUG,
	array(
		'parent' => 'options-general.php',
		'title' => __('Edit post type','domain'),
		'menu' => __('Edit post type','domain'),
		),
	'DTSettings\page_cpt_body', DT_CPT_OPTION, 'DTSettings\cpt_validate' );


function array_filter_recursive($input){ 
	foreach ($input as &$value){ 
		if (is_array($value)){ 
			$value = array_filter_recursive($value); 
		} 
	}

	return array_filter($input); 
} 

function cpt_validate( $input ){
	// file_put_contents( plugin_dir_path( __FILE__ ) .'/debug.log', print_r($input, 1) );
	$post_types = get_option( DT_CPT_OPTION );

	$slug = _isset_false( $input['post_type_name'], 1 );
	
	if( $slug )
		$post_types[$slug] = array_filter_recursive( $input );
	
	return $post_types;
}

// Define the body content for the pag
function page_cpt_body(){
	//delete_option( DT_CPT_OPTION );
	
	global $cpt_settings;

	echo "Use http://wp-default.lc/wp-admin/options-general.php?page=edit_cpt&post_type=post for load \$active";	

	DTForm::render(
		apply_filters( 'dt_admin_options', $cpt_settings['cpt_global']),
		get_option(DT_CPT_OPTION),
		true );
}

/**
 * MetaBoxes
 *
 * Define the insides of the metabox
 */

add_action('add_meta_boxes', 'DTSettings\admin_page_boxes');
function admin_page_boxes(){
	// menu id + _page_ + pageslug
	foreach (array(DT_CCPT_PAGESLUG, DT_ECPT_PAGESLUG) as $value) {
		add_meta_box('labels','Labels (not required)','DTSettings\dt_labels','settings_page_'.$value,'normal','high');
		add_meta_box('type_settings','Settings','DTSettings\dt_main_settings','settings_page_'.$value,'side','high');
		add_meta_box('supports','Supports','DTSettings\dt_supports','settings_page_'.$value,'side','high');
	}
}

function dt_labels(){
	global $cpt_settings;

	DTForm::render(
		apply_filters( 'dt_admin_options', $cpt_settings['labels']),
		get_option(DT_CPT_OPTION),
		true );
}

function dt_main_settings(){
	global $cpt_settings;

	DTForm::render(
		apply_filters( 'dt_admin_options', $cpt_settings['cpt_main']),
		get_option(DT_CPT_OPTION),
		false,
		array( 'hide_desc' => true )
		);

	DTForm::render( 
		apply_filters( 'dt_admin_options', $cpt_settings['cpt_main_textfields']),
		get_option(DT_CPT_OPTION),
		true,
		array(
			'form_wrap' => array('<table class="table"><tbody>', '</tbody></table>'),
			'label_tag' => 'td',
			'hide_desc' => true
		) );
}

function dt_supports(){
	global $cpt_settings;

	echo "see more about add_post_type_support()";

	DTForm::render(
		apply_filters( 'dt_admin_options', $cpt_settings['supports']),
		get_option(DT_CPT_OPTION) );
}

  //      if(isset($args['public']) && $args['public'] == false){
  //       if( is_singular($type) || is_post_type_archive($type) ){
  //         global $wp_query;
  //         $wp_query->set_404();
  //         status_header(404);
  //         // u can hide this pages:
  //         // wp_die('Это техническая страница - к сожалению она не доступна');
  //       }
  //     }
  //   }
  // }
  //   add_action( 'wp', array($this, 'deny_access_private_type'), 1 );
  //   
  //   