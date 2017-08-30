<?php
/*
Plugin Name: Настройки проекта
Plugin URI: https://github.com/nikolays93/project-settings
Description: Скрывает нераскрытый функционал WordPress. Предоставляет возможность создавать новые типы записей и редактировать заголовки ранее зарегистрированных.
Version: 3.1b
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

namespace PSettings;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

// if( ! isset($_GET['page']) || $_GET['page'] !== DT_GLOBAL_PAGESLUG )
//   return;

function get_active_cpt_or_pt(){
  if( empty($_GET['post-type']) )
    return;

  $post_type_array = json_decode(json_encode(get_post_type_object( $_GET['post-type'] )), true);
  $supports = array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks',
    'custom-fields', 'comments', 'revisions', 'page-attributes', 'post-formats');
  foreach ($supports as $support) {
    if( post_type_supports( $_GET['post-type'], $support ) ){
      $post_type_array['supports'][$support] = 'on';
    }
  }

  $active = WPForm::active( $post_type_array, false, true );
  $active['post_type_name'] = $_GET['post-type'];

  return $active;
}

class DTSettings {
  const SETTINGS = 'project-settings';
  const PREFIX = 'dt_';

  static public $settings = array();
  static public $post_types = array();

  public $is_builtin = false;

  private function __clone() {}
  private function __wakeup() {}

  private static $instance = null;
  public static function get_instance() {
    if ( ! isset( self::$instance ) )
      self::$instance = new self;

    return self::$instance;
  }

  public static function activate(){
    $defaults = array(
      'menu'     => 'edit-comments.php,users.php,tools.php,',
      'sub_menu' => 'index.php>index.php,index.php>update-core.php,edit.php?post_type=shop_order>edit.php?post_type=shop_order,edit.php?post_type=shop_order>edit.php?post_type=shop_coupon,edit.php?post_type=shop_order>admin.php?page=wc-reports,options-general.php>options-discussion.php,',
      );

    add_option( self::SETTINGS, $defaults );
  }
  public static function uninstall(){ delete_option(self::SETTINGS); }

  /* Singleton Class */
  private function __construct() {

    self::define_constants();
    /** Set Options */
    self::$settings = get_option( self::SETTINGS, array() );
    self::$post_types = get_option( CPTYPES, array() );

    self::load_classes();

    // echo "<pre style='margin-left: 100px;'>";
    // var_dump( self::$settings );
    // echo "</pre>";

    $this->is_builtin = !empty($_GET['post-type'])
      && in_array($_GET['post-type'], get_post_types( array('_builtin' => true) ) );

    add_action( 'admin_enqueue_scripts', array($this, 'load_assets') );

    add_filter( self::SETTINGS . '_columns', function(){return 2;} );
    add_action( self::SETTINGS . '_inside_side_container', 'submit_button', 20 );
    add_filter('post_type_data_render', array($this, 'read_only_inputs_filter'), 10, 2);
    add_filter('post_type_data_render', array($this, 'create_new_post_type_filter'), 10, 2);

    $this->init_admin_page();
  }

  private static function define_constants(){
    define('DTS_DIR', rtrim(plugin_dir_path( __FILE__ ), '/') );
    define('CPTYPES', 'project-types');
  }

  private static function load_classes(){
    require_once DTS_DIR . '/inc/actions.php';
    require_once DTS_DIR . '/inc/class-wp-admin-page-render.php';
    require_once DTS_DIR . '/inc/class-wp-form-render.php';
    require_once DTS_DIR . '/inc/class-post-types-list-table.php';
  }

  /**
   * Filter for new post type
   */
  function create_new_post_type_filter($data, $type=''){
    if( !isset($_GET['do']) || $_GET['do'] != 'add' )
      return $data;

    foreach ($data as $i => $input) {
      if( $type == 'main' ){
        if(in_array($input['id'], array('public', 'publicly_queryable', 'show_ui', 'show_in_menu')))
          $data[$i]['checked'] = 'true';
      }
      elseif( $type == 'supports'){
        if(in_array($input['id'], array(
          $type.'][title',
          $type.'][editor',
          $type.'][thumbnail',
          $type.'][custom-fields')
          )){
          $data[$i]['checked'] = 'true';
        }
      }
    }

    return $data;
  }

  function read_only_inputs_filter($data, $type=''){
    switch ($type) {
      case 'main':
        if( $this->is_builtin ){
          foreach ($data as $i => $input) {
            $data[$i]['readonly'] = 'true';
          }
        }
        break;
      case 'page':
        if(!empty($_GET['post-type']))
          $data[0]['readonly'] = 'true';
        break;
    }

    return $data;
  }

  /************************************* Admin Page *************************************/
  function load_assets(){
    $current = get_current_screen();
    if($current->id != 'settings_page_project-settings')
      return;

    wp_enqueue_style( 'project-settings', plugins_url(basename(__DIR__) . '/assets/p-settings.css'), array(), '1.0' );
    wp_enqueue_script(  'project-settings', plugins_url(basename(__DIR__) . '/assets/project-settings.js'), array('jquery'), '1.0', true );

    wp_localize_script( 'project-settings', 'menu_disabled', array(
      'menu' => isset(self::$settings['globals']['menu']) ? self::$settings['globals']['menu'] : '',
      'sub_menu' => isset(self::$settings['globals']['sub_menu']) ? self::$settings['globals']['sub_menu'] : '',
      'edit_cpt_page' => self::SETTINGS
      ) );

    wp_localize_script( 'project-settings', 'post_types', array_values( get_post_types() ) );
  }

  function init_admin_page(){
    $post_type = !empty($_GET['post-type']) ? $_GET['post-type'] : '';
    $action = !empty($_GET['do']) ? $_GET['do'] : '';

    $metaboxes_active = array(
      'globals'  => array( 'globals', __('Globals'), array($this, 'metabox_globals'), 'side' ),
      );
    $metaboxes = array(
      'labels'   => array( 'labels', __('Labels'), array($this, 'metabox_labels'), 'normal' ),
      'main'     => array( 'main', __('Settings'), array($this, 'metabox_main'), 'normal' ),
      'supports' => array( 'supports', __('Supports'), array($this, 'metabox_supports'), 'normal' ),
      );

    if( $action == 'remove' ){
      if( wp_verify_nonce( $_REQUEST['_wpnonce'], 'trash-type-'.$_GET['cpt'] ) ){
        unset(self::$post_types[$_GET['cpt']]);
        update_option( CPTYPES, self::$post_types );
      }
      wp_redirect( get_admin_url() . 'options-general.php?page=' . self::SETTINGS );
      exit;
    }
    elseif( $action == 'add' ){
      $page_callback = array($this, 'custom_type_page_settings');

      $metaboxes_active[] = $metaboxes['labels'];
      $metaboxes_active[] = $metaboxes['main'];
      $metaboxes_active[] = $metaboxes['supports'];
    }
    elseif( $post_type ){
      // edit exists
      $page_callback = array($this, 'custom_type_page_settings');

      $metaboxes_active[] = $metaboxes['labels'];
      $metaboxes_active[] = $metaboxes['main'];

      if( !$this->is_builtin )
        $metaboxes_active[] = $metaboxes['supports'];
    }
    else {
      $page_callback = array($this, 'welcome_page');
    }

    $page = new WPAdminPageRender(self::SETTINGS, array(
      'parent' => 'options-general.php',
      'title' => __('Настройки проекта'),
      'menu' => __('Настройки проекта'),
      ),
    $page_callback,
    self::SETTINGS,
    array($this, 'validate')
    );

    foreach ($metaboxes_active as $mb) {
      $page->add_metabox($mb[0], $mb[1], $mb[2], $mb[3]);
    }

    $page->set_metaboxes();
  }

  function welcome_page(){
    include DTS_DIR . '/templates/welcome_page.php';
  }

  function custom_type_page_settings(){
    // var_dump(self::$settings);
    WPForm::render(
      apply_filters('post_type_data_render', include(DTS_DIR . '/inc/settings/cpt.php'), 'page'),
      get_active_cpt_or_pt(),
      true,
      array('admin_page' => self::SETTINGS)
      );
  }

  /************************************* Meta Boxes *************************************/
  function metabox_main(){
    WPForm::render(
      apply_filters('post_type_data_render', include(DTS_DIR . '/inc/settings/cpt-main.php'), 'main'),
      get_active_cpt_or_pt(),
      true,
      array('item_wrap' => false, 'admin_page' => self::SETTINGS)
      );
  }

  function metabox_supports(){
    WPForm::render(
      apply_filters('post_type_data_render', include(DTS_DIR . '/inc/settings/cpt-supports.php'), 'supports'),
      get_active_cpt_or_pt(),
      true,
      array('admin_page' => self::SETTINGS)
      );
  }

  function metabox_labels(){
    WPForm::render(
      include(DTS_DIR . '/inc/settings/cpt-labels.php'),
      get_active_cpt_or_pt(),
      true,
      array('admin_page' => self::SETTINGS)
      );
  }

  /**
   * Side Meta Box
   */
  function metabox_globals(){
    $add_class = (!empty($_COOKIE['developer'])) ? ' button-primary': '';

    echo '<p><input type="button" id="setNotHide" class="button'.$add_class.'" value="Показывать мне скрытые меню"></p>';

    WPForm::render(
      include(DTS_DIR . '/inc/settings/global.php'),
      WPForm::active( self::SETTINGS, false, true ),
      true,
      array('clear_value' => false, 'hide_desc' => true, 'admin_page' => self::SETTINGS)
      );
  }

  /**
   * Validate Input's Values
   */
  function validate( $values ){
    // Update Post Types
    if( !empty($values['post_type_name']) ){
      if(!empty($values['labels']))
        $values['post_type']['labels'] = $values['labels'];

      unset($values['labels']);

      if(!empty($values['supports']))
        $values['post_type']['supports'] = $values['supports'];

      unset($values['supports']);

      self::$post_types[ $values['post_type_name'] ] = $values['post_type'];
      update_option(CPTYPES, self::$post_types);

      unset($values['post_type']);
      unset($values['post_type_name']);

      // Update fields
      $fields = array();
      if( isset($values['fields']) && is_array($values['fields']) ){
        for ($i=0; $i < sizeof($values['fields']['type']) ; $i++) {
          if( empty($values['fields']['id'][$i]) || empty($values['fields']['label'][$i]) )
            continue;

          $fields[$i] = array(
            'id'    => $values['fields']['id'][$i],
            'label' => $values['fields']['label'][$i],
            'type'  => $values['fields']['type'][$i],
            );
        }
      }
      if(sizeof($fields) >= 1 )
        $values['fields'] = $fields;
    }

    // file_put_contents( DTS_DIR . '/debug.log', print_r(array($values,$_POST, $values['supports']), 1 ));
    return $values;
  }
}

add_action( 'plugins_loaded', function(){ $p = DTSettings::get_instance(); }, 1100 );
register_activation_hook( __FILE__, array( __NAMESPACE__ . '\DTSettings', 'activate' ) );
// register_deactivation_hook( __FILE__, array( 'DTSettings', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( __NAMESPACE__ . '\DTSettings', 'uninstall' ) );