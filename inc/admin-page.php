<?php
namespace DTSettings;

if ( ! defined( 'ABSPATH' ) )
  exit; // Exit if accessed directly
/**
 * Actions And Filters
 */
add_filter( DT_GLOBAL_PAGESLUG . '_columns', function(){return 2;} );
add_action( DT_GLOBAL_PAGESLUG . '_inside_side_container', 'submit_button', 20 );


$page_callback = 'settings_page';
if( !empty($_GET['do']) ){
  switch ($_GET['do']) {
    case 'remove':
      add_action('plugins_loaded', function(){
        if( wp_verify_nonce( $_REQUEST['_wpnonce'], 'trash-type-'.$_GET['cpt'] ) ){
          $cpts = get_option( DT_CPT_OPTION );
          unset($cpts[$_GET['cpt']]);
          update_option( DT_CPT_OPTION, $cpts );

          wp_redirect( get_admin_url() . 'options-general.php?page=' . DT_GLOBAL_PAGESLUG );
          exit;
          
        }
      });
      break;
    case 'create_type':
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
      break;
  }
}
else {
  if(!isset($_GET['post-type']))
    $page_callback = 'first_page';
}

function get_cpt_or_pt(){
  $pt = _isset_false($_GET['post-type']);
  $active = WPForm::active( DT_CPT_OPTION, $pt, true ); 

  if( ! $active && $pt ){
    $active = get_post_type_object( $pt );
    $active = (array)$active;

    if(isset($active['labels']) || isset($active->labels)){
      foreach ((array)$active['labels'] as $key => $value) {
        $active['labels_'.$key] = $value;
      }
      unset($active['labels']);     
    }
  } else {
    foreach ($active as $key => $value) {
      $support = explode('_', $key);
      if($support[0] == 'supports'){
        $active['supports_'.$value] = true;
        unset( $active[$key] );
      }
    }
  }
  
  $active['post_type_name'] = $pt;
  return $active;
}

$page = new WPAdminPageRender( DT_GLOBAL_PAGESLUG,
  array(
    'parent' => 'options-general.php',
    'title' => __('Настройки проекта'),
    'menu' => __('Настройки проекта'),
    ),
  'DTSettings\\'.$page_callback, DT_CPT_OPTION, 'DTSettings\valid' );

function first_page(){
  echo sprintf('<a href="?page=%s&do=%s" class="button button-primary alignright">Создать новый тип записей</a>',
    $_REQUEST['page'],
    'create_type');

  $cpts = get_option( DT_CPT_OPTION );
  if(sizeof($cpts) >= 1){
    $table = new \Post_Types_List_Table();
    foreach ($cpts as $id => $type) {
      $table->set_type( $id, $type['labels']['singular_name'], $type['label'] );
    }
    
    $table->prepare_items();
    $table->display();
  }
  else {
    echo "<p>Здесь вы можете создать новый тип записи или изменить вид уже зарегистрированного типа <br> и\или скрыть не реализованный функционал CMS WordPress из меню.</p>";
  }
}
// Main Page Render
function settings_page(){
  /**
   * Redirect If Type Not Exists 
   */
  $pt = _isset_false($_GET['post-type']);
  $active = WPForm::active( DT_CPT_OPTION, $pt, true );
  if( $pt && (!is_array($active) || ! in_array($pt, $active)) && ! get_post_type_object( $pt ) )
    wp_redirect( '/wp-admin/options-general.php?page=project-settings' );

  /**
   * Show Settings else
   */
  WPForm::render(
    apply_filters( 'DTSettings\dt_admin_options', include('settings/cpt.php'), DT_CPT_OPTION ),
    get_cpt_or_pt(),
    true
    );
}

if( ! isset($_GET['page']) || $_GET['page'] !== DT_GLOBAL_PAGESLUG )
  return;

/**
 * Edit this only for custom post types
 */
if(!empty($_GET['do']) && $_GET['do'] == 'create_type' || !empty($_GET['post-type']) ){
  if( empty($_GET['post-type']) || !array_key_exists($_GET['post-type'], get_editable_types()) ){
    $page->add_metabox( 'project-types-main', 'Настройки', 'DTSettings\project_types_main');
    $page->add_metabox( 'project-types-supports', 'Возможности типа записи', 'DTSettings\project_types_supports');
  }

  $page->add_metabox( 'project-types-labels', 'Надписи', 'DTSettings\project_types_labels');
}

$page->add_metabox( 'project-settings', 'Настройки', 'DTSettings\dt_project_settings', 'side');

function project_types_main(){
  WPForm::render(
    apply_filters( 'DTSettings\dt_admin_options', include('settings/cpt-main.php'), DT_CPT_OPTION ),
    get_cpt_or_pt(),
    true,
    array('item_wrap' => false)
    );
}
function project_types_supports(){
  WPForm::render(
    apply_filters( 'DTSettings\dt_admin_options', include('settings/cpt-supports.php'), DT_CPT_OPTION ),
    get_cpt_or_pt(),
    true,
    array('clear_value' => false)
    );
}
function project_types_labels(){
  WPForm::render(
    apply_filters( 'DTSettings\dt_admin_options', include('settings/cpt-labels.php'), DT_CPT_OPTION ),
    get_cpt_or_pt(),
    true
    );
}

/**
 * General Project Settings
 */
function dt_project_settings(){
  $add_class = (!empty($_COOKIE['developer'])) ? ' button-primary': '';

  echo '<p><input type="button" id="setNotHide" class="button'.$add_class.'" value="Показывать мне скрытые меню"></p>';

  WPForm::render(
      apply_filters( 'DTSettings\dt_admin_options', include('settings/global.php'), DT_CPT_OPTION ),
      WPForm::active( DT_GLOBAL_PAGESLUG, false, true ),
      true,
      array('hide_desc' => true)
      );
}

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
  if( !isset($values['post_type_name']) || ! $values['post_type_name'] )
    return $all_cpts;

  if( !is_array($all_cpts) )
    return false;
  
  foreach ($values as $key => $value) {
    if( ! $value )
      unset($values[$key]);
    elseif( $value == 'on' )
      $values[$key] = true;
    elseif( $value == 'false' )
      $values[$key] = false;

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

  // file_put_contents( DTS_DIR . '/debug.log', array(print_r($globals, 1), print_r($all_cpts, 1)) );
  return $all_cpts;
}