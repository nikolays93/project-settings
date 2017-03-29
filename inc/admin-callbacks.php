<?php
namespace DTSettings;

class dtAdminPage
{
	protected $page = '';
	protected $args = array(
		'parent' => 'options-general.php',
		'title' => '',
		'menu' => 'New page',
		'permissions' => 'manage_options'
		);
	protected $page_content_cb = '';

	function __construct( $page_slug, $args, $page_content_cb )
	{
		// slug required
		if( !$page_slug )
			wp_die( 'You have false slug in admin page class', 'Slug is false or empty' );

		$this->page = $page_slug;
		$this->args = array_merge( $this->args, $args );
		$this->page_content_cb = $page_content_cb;

		add_action('admin_menu', array($this,'add_page'));
		// add_action('admin_enqueue_scripts', array($this, 'load_scripts'));
	}

	function add_page(){
		$this->page = add_submenu_page(
			$this->args['parent'],
			$this->args['title'],
			$this->args['menu'],
			$this->args['permissions'],
			$this->page,
			array($this,'render_page'), 10);

		add_action('load-'.$this->page, array($this,'page_actions'),9);
	}

	function page_actions(){
		do_action('add_meta_boxes_'.$this->page, null);
		do_action('add_meta_boxes', $this->page, null);

		// User can choose between 1 or 2 columns (default 2)
		add_screen_option('layout_columns', array('max' => 2, 'default' => 2) );

		// Enqueue WordPress' script for handling the metaboxes
		wp_enqueue_script('postbox');
	}
	function load_scripts( $screen ){
		if($screen !== $this->page)
			return false;

		wp_enqueue_script( 'devtools_admin_page', DT_PS_DIR_PATH . '/assets/project-settings.js', array(), '1.0', true );
	}

	function render_page(){
				?>

		<div class="wrap">

			<?php screen_icon(); ?>
			<h2> <?php echo esc_html($this->args['title']);?> </h2>

			<form id="ccpt" enctype="multipart/form-data" action="options.php" method="post">  
				<input type="hidden" name="action" value="some-action">
				<?php wp_nonce_field( 'some-action-nonce' );

				/* Used to save closed metaboxes and their order */
				wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
				wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>

				<div id="poststuff">

					<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>"> 

						<div id="post-body-content">
							<?php do_settings_sections($this->page); ?>
							<?php call_user_func($this->page_content_cb); ?>
						</div>    

						<div id="postbox-container-1" class="postbox-container">
							<?php
								do_meta_boxes('','side',null); 
								submit_button();
							?>
						</div>    

						<div id="postbox-container-2" class="postbox-container">
							<?php do_meta_boxes('','normal',null);  ?>
							<?php do_meta_boxes('','advanced',null); ?>
						</div>	     					

					</div> <!-- #post-body -->

				</div> <!-- #poststuff -->
				<?php
					// add hidden settings
					settings_fields( DT_GLOBAL_PAGESLUG );
				?>
			</form>			

		</div><!-- .wrap -->

		<script>
			jQuery(document).ready(function($){
				postboxes.add_postbox_toggles(pagenow);
				
				$('#ccpt input#name').on('keyup', function(){
					$('#ccpt input#menu_name').attr('placeholder', $('#ccpt input#name').val() );
				});
				
			});
		</script>
		<?php
	}
}


new dtAdminPage( DT_GLOBAL_PAGESLUG,
	array(
		'parent' => 'options-general.php',
		'title' => __('Project settings','domain'),
		'menu' => __('Project settings','domain'),
		),
	'page_settings_body' );

new dtAdminPage( DT_CCPT_PAGESLUG,
	array(
		'parent' => 'options-general.php',
		'title' => __('Create custom post type','domain'),
		'menu' => __('Add post type','domain'),
		),
	'DTSettings\page_cpt_body' );

new dtAdminPage( DT_ECPT_PAGESLUG,
	array(
		'parent' => 'options-general.php',
		'title' => __('Edit post type','domain'),
		'menu' => __('Edit post type','domain'),
		),
	'DTSettings\page_cpt_body' );

function page_settings_body(){
	echo "Some test";
}

// Define the body content for the pag
function page_cpt_body(){
	echo "Use http://wp-default.lc/wp-admin/options-general.php?page=edit_cpt&post_type=post for load \$active"; 
	\DTForm::render(
		array(
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
			),
		array(),
		true
		);
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
		add_meta_box('type_settings','Settings','DTSettings\dt_example_metabox','settings_page_'.$value,'side','high');
		add_meta_box('supports','Supports','DTSettings\dt_supports','settings_page_'.$value,'side','high');
	}
}

function dt_labels(){
	$plural = $single = '';
	\DTForm::render( array(
		array('id' => 'add_new',
			'type' => 'text',
			'placeholder' => 'Добавить ' . $single,
			'label' => 'Add new',
			'desc' => 'The add new text. The default is "Add New" for both hierarchical and non-hierarchical post types. When internationalizing this string, please use a gettext context matching your post type.'),
		
		array('id' => 'add_new_item',
			'type' => 'text',
			'placeholder' => 'Добавить ' . $single,
			'label' => 'Add new item',
			'desc' => 'Default is Add New Post/Add New Page'),
		
		array('id' => 'new_item',
			'type' => 'text',
			'placeholder' => 'Новая ' . $single,
			'label' => 'New item',
			'desc' => 'Default is New Post/New Page.'),
		
		array('id' => 'edit_item',
			'type' => 'text',
			'placeholder' => 'Изменить ' . $single,
			'label' => 'Edit item',
			'desc' => 'Default is Edit Post/Edit Page'),

		array('id' => 'view_item',
			'type' => 'text',
			'placeholder' => 'Показать ' . $single,
			'label' => 'View item',
			'desc' => 'Default is View Post/View Page.'),

		array('id' => 'all_items',
			'type' => 'text',
			'placeholder' => 'Все ' . $plural,
			'label' => 'All items',
			'desc' => 'String for the submenu. Default is All Posts/All Pages.'),

		array('id' => 'search_items',
			'type' => 'text',
			'placeholder' => 'Найти ' . $single,
			'label' => 'Search items',
			'desc' => 'Default is Search Posts/Search Pages.'),

		array('id' => 'not_found',
			'type' => 'text',
			'placeholder' => $plural . ' не найдены',
			'label' => 'Not found',
			'desc' => 'Default is No posts found/No pages found.'),

		array('id' => 'not_found_in_trash',
			'type' => 'text',
			'placeholder' => $plural . ' в корзине не найдены',
			'label' => 'Not found in Trash',
			'desc' => 'Default is No posts found in Trash/No pages found in Trash.'),
		), array(), true );
}

function dt_example_metabox(){
	\DTForm::render( array(
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
		), array(), false, array(
			'hide_desc' => true
		) );

	\DTForm::render( array(
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
		), array(), true, array(
			'form_wrap' => array('<table class="table"><tbody>', '</tbody></table>'),
			'label_tag' => 'td',
			'hide_desc' => true
		) );
}

function dt_supports(){
	echo "see more about add_post_type_support()";

	\DTForm::render( array(
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

		) );
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