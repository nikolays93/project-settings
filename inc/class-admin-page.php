<?php

namespace DTSettings;

class dtAdminPage
{
	public $page = '';
	public $screen = '';

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
		$this->screen = add_submenu_page(
			$this->args['parent'],
			$this->args['title'],
			$this->args['menu'],
			$this->args['permissions'],
			$this->page,
			array($this,'render_page'), 10);

		add_action('load-'.$this->screen, array($this,'page_actions'),9);
		add_action('admin_footer-'.$this->screen, array($this,'footer_scripts'));
	}

	function page_actions(){
		do_action('add_meta_boxes_'.$this->screen, null);
		do_action('add_meta_boxes', $this->screen, null);

		// User can choose between 1 or 2 columns (default 2)
		add_screen_option('layout_columns', array('max' => 2, 'default' => 2) );

		// Enqueue WordPress' script for handling the metaboxes
		wp_enqueue_script('postbox');
	}
	function load_scripts( $screen ){
		if($screen !== $this->screen)
			return false;

		wp_enqueue_script( 'devtools_admin_page', DT_PS_DIR_PATH . '/assets/project-settings.js', array(), '1.0', true );
	}

	function footer_scripts(){
		
		echo "<script> jQuery(document).ready(function($){ postboxes.add_postbox_toggles(pagenow); });</script>";
	}

	function render_page(){
		?>

		<div class="wrap">

			<?php screen_icon(); ?>
			<h2> <?php echo esc_html($this->args['title']);?> </h2>

			<form id="ccpt" enctype="multipart/form-data" action="options.php" method="post">  
				<?php
				register_setting( $this->page, $this->page, array($this, 'validate_options') );

				/* Used to save closed metaboxes and their order */
				wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
				wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>

				<div id="poststuff">

					<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>"> 

						<div id="post-body-content">
							<?php // do_settings_sections($this->page); ?>
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
					settings_fields( $this->page );
				?>
			</form>			

		</div><!-- .wrap -->
		<?php
	}

	function validate_options($input){
		// file_put_contents( plugin_dir_path( __FILE__ ) .'/debug.log', print_r($input, 1) );
		$valid_input = array();

		if(sizeof($input) > 0){
			foreach ($input as $k => $v) {
				$valid_input[$k] = $v;
			}
		}

		return $valid_input;
	}
}