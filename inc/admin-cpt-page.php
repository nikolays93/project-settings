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

	$slug = _isset_false( $input['type_slug'], 1 );
	
	if( $slug )
		$post_types[$slug] = array_filter_recursive( $input );
	
	return $post_types;
}
add_filter( 'cpt_page_defaults', 'DTSettings\defaults', 10, 1 );
function defaults( $form ){
	$defaults = array(
		's_title' => true,
		's_editor' => true,
		);

	$new_form = array();
	foreach ( $form as $input ) {
		if( isset($defaults[ $input['id'] ]) ){
			if ( $input['type'] == 'checkbox' || $input['type'] == 'radio' ){
				if( $defaults[ $input['id'] ] )
					$input['checked'] = 'checked';
			}
			else {
				if( $defaults[ $input['id'] ] )
					$input['value'] = $defaults[ $input['id'] ];
			}
		}
		$new_form[] = $input;
	}

	return $new_form;
}


// Define the body content for the pag
function page_cpt_body(){
	// delete_option( DT_CPT_OPTION );
	
	echo "Use http://wp-default.lc/wp-admin/options-general.php?page=edit_cpt&post_type=post for load \$active"; 
	
	$form = array(
		array( 'id' => 'type_slug',
			'type' => 'text',
			'label' => 'Post type general name',
			'desc' => 'The handle (slug) name of the post type, usually plural.',
			'placeholder' => 'e.g. news'
			),
		array( 'id' => 'singular_name',
			'type' => 'text',
			'label' => 'Singular name',
			'desc' => 'name for one object of this post type.',
			'placeholder' => 'e.g. article'
			),
		array( 'id' => 'menu_name',
			'type' => 'text',
			'label' => 'Menu name',
			'desc' => 'display left menu label. same as name (if empty)'
			),
		);
	
	if( $_GET['page'] == DT_CCPT_PAGESLUG )
		$form = apply_filters( 'cpt_page_defaults', $form );

	$result = array();
	foreach ($form as $filter) {
		$result[] = apply_filters( 'dt_admin_options_page_render', $filter, DT_CPT_OPTION );
	}
	DTForm::render( $result, get_option(DT_CPT_OPTION), true );
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
	$plural = $single = '';
	$form = array(
		array('id' => DT_CPT_OPTION."[labels][add_new]",
			'type' => 'text',
			'placeholder' => 'Добавить ' . $single,
			'label' => 'Add new',
			'desc' => 'The add new text. The default is "Add New" for both hierarchical and non-hierarchical post types. When internationalizing this string, please use a gettext context matching your post type.'),
		
		array('id' => DT_CPT_OPTION."[labels][add_new_item]",
			'type' => 'text',
			'placeholder' => 'Добавить ' . $single,
			'label' => 'Add new item',
			'desc' => 'Default is Add New Post/Add New Page'),
		
		array('id' => DT_CPT_OPTION."[labels][new_item]",
			'type' => 'text',
			'placeholder' => 'Новая ' . $single,
			'label' => 'New item',
			'desc' => 'Default is New Post/New Page.'),
		
		array('id' => DT_CPT_OPTION."[labels][edit_item]",
			'type' => 'text',
			'placeholder' => 'Изменить ' . $single,
			'label' => 'Edit item',
			'desc' => 'Default is Edit Post/Edit Page'),

		array('id' => DT_CPT_OPTION."[labels][view_item]",
			'type' => 'text',
			'placeholder' => 'Показать ' . $single,
			'label' => 'View item',
			'desc' => 'Default is View Post/View Page.'),

		array('id' => DT_CPT_OPTION."[labels][all_items]",
			'type' => 'text',
			'placeholder' => 'Все ' . $plural,
			'label' => 'All items',
			'desc' => 'String for the submenu. Default is All Posts/All Pages.'),

		array('id' => DT_CPT_OPTION."[labels][search_items]",
			'type' => 'text',
			'placeholder' => 'Найти ' . $single,
			'label' => 'Search items',
			'desc' => 'Default is Search Posts/Search Pages.'),

		array('id' => DT_CPT_OPTION."[labels][not_found]",
			'type' => 'text',
			'placeholder' => $plural . ' не найдены',
			'label' => 'Not found',
			'desc' => 'Default is No posts found/No pages found.'),

		array('id' => DT_CPT_OPTION."[labels][not_found_in_trash]",
			'type' => 'text',
			'placeholder' => $plural . ' в корзине не найдены',
			'label' => 'Not found in Trash',
			'desc' => 'Default is No posts found in Trash/No pages found in Trash.'),
		);
	
	// if( $_GET['page'] == DT_CCPT_PAGESLUG )
	// 	$form = apply_filters( 'cpt_page_defaults', $form );

	// $result = array();
	// foreach ($form as $filter) {
	// 	$result[] = apply_filters( 'dt_admin_options_page_render', $filter,  );
	// }
	DTForm::render( $form, get_option(DT_CPT_OPTION), true );
}

function dt_main_settings(){
	$form = array(
		array('id' => 'public',
			'type' => 'checkbox',
			'label' => 'Public',
			'desc' => 'Публичный или используется только технически',
			),
		array('id' => 'publicly_queryable',
			'type' => 'checkbox',
			'label' => 'Publicly queryable',
			'desc' => 'Показывать во front\'е',
			),
		array('id' => 'show_ui',
			'type' => 'checkbox',
			'label' => 'Show UI',
			'desc' => 'Показывать управление типом записи',
			),
		array('id' => 'show_in_menu',
			'type' => 'checkbox',
			'label' => 'Show in Menu',
			'desc' => 'Показывать ли в админ-меню',
			),
		array('id' => 'rewrite',
			'type' => 'checkbox',
			'label' => 'ReWrite',
			'desc' => 'ЧПУ',
			),
		array('id' => 'has_archive',
			'type' => 'checkbox',
			'label' => 'Has archive',
			'desc' => 'Поддержка архивной страницы',
			),
		array('id' => 'hierarchical',
			'type' => 'checkbox',
			'label' => 'Hierarchical',
			'desc' => 'Родители / тексономии',
			),
		array('id' => 'menu_position',
			'type' => 'checkbox',
			'label' => 'Menu position',
			'desc' => '',
			),
		);
	
	if( $_GET['page'] == DT_CCPT_PAGESLUG )
		$form = apply_filters( 'cpt_page_defaults', $form );

	$result = array();
	foreach ($form as $filter) {
		$result[] = apply_filters( 'dt_admin_options_page_render', $filter, DT_CPT_OPTION );
	}
	DTForm::render( $result, get_option(DT_CPT_OPTION), false, array(
			'hide_desc' => true
		) );

	$form = array(
		array('id' => 'query_var',
			'type' => 'text',
			'label' => 'Query var',
			'desc' => '$post_type в query_var',
			),
		array('id' => 'capability_type',
			'type' => 'text',
			'label' => 'Capability type',
			'desc' => 'Права такие же как..',
			),
		array('id' => 'menu_icon',
			'type' => 'text',
			'label' => 'Menu icon',
			'placeholder' => 'dashicons-admin-post',
			)
		);
	
	if( $_GET['page'] == DT_CCPT_PAGESLUG )
		$form = apply_filters( 'cpt_page_defaults', $form );

	$result = array();
	foreach ($form as $filter) {
		$result[] = apply_filters( 'dt_admin_options_page_render', $filter, DT_CPT_OPTION );
	}
	DTForm::render( $result, get_option(DT_CPT_OPTION), true, array(
			'form_wrap' => array('<table class="table"><tbody>', '</tbody></table>'),
			'label_tag' => 'td',
			'hide_desc' => true
		) );
}

function dt_supports(){
	echo "see more about add_post_type_support()";

	$form = array(
		array("id" => 's_title',
			'type' => 'checkbox',
			'label' => 'Post Title',
			'desc' => ''
			),
		array("id" => 's_editor',
			'type' => 'checkbox',
			'label' => 'Content Editor',
			'desc' => ''
			),
		array("id" => 's_thumbnail',
			'type' => 'checkbox',
			'label' => 'Post Thumbnail',
			'desc' => ''
			),
		array("id" => 's_excerpt',
			'type' => 'checkbox',
			'label' => 'Post Excerpt',
			'desc' => ''
			),
		array("id" => 's_custom-fields',
			'type' => 'checkbox',
			'label' => 'Custom Fields',
			'desc' => ''
			),
		array("id" => 's_page-attributes',
			'type' => 'checkbox',
			'label' => 'Page Attributes',
			'desc' => ''
			),
		);
	
	if( $_GET['page'] == DT_CCPT_PAGESLUG )
		$form = apply_filters( 'cpt_page_defaults', $form );

	$result = array();
	foreach ($form as $filter) {
		$result[] = apply_filters( 'dt_admin_options_page_render', $filter, DT_CPT_OPTION );
	}
	DTForm::render( $result, get_option(DT_CPT_OPTION) );
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