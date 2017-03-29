<?php
// ver 2.0

namespace DTSettings;

function _isset_default(&$var, $default, $unset = false){
  $result = $var = isset($var) ? $var : $default;
  if($unset)
    $var = FALSE;
  return $result;
}

function _isset_false(&$var, $unset = false){
  return _isset_default( $var, false, $unset );
}

function _isset_empty(&$var, $unset = false){
  return _isset_default( $var, '', $unset );
}



if(! has_filter( 'dt_admin_options_page_render' ) ){
  function options_page_render($field, $option_name){
    $field['name'] = $option_name . "[" . $field['id'] . "]";

    // if( isset($field['desc']) ){
    //   $field['label'] = $field['desc'];
    //   unset( $field['desc'] );
    // } else {
    //   unset( $field['label'] );
    // }

    // if( isset($active[$field['id']]) ){
    //   if($field['type'] == 'checkbox')
    //     $field['checked'] = 'checked';
    //   $field['value'] = $active[$field['id']];
    // }

    return $field;
  }
  add_filter( 'dt_admin_options_page_render', 'DTSettings\options_page_render', 10, 3 );
}
class DTForm
{
  private static function is_checked( $name, $value, $active, $default ){
    if( $active || $default ){
      if( $value ){ // str or bool
        if( is_array($active) ){
          if( in_array($value, $active) )
            return true;
        }
        else {
          if( $value == $active )
            return true;
        }
      }
      else {
        if( ($default || $active != '') && $active != 'false' ) // str or bool
          return true;
      }
    }
    return false;
  }

  public static function render(
    $render_data = false,
    $active = array(),
    $is_table = false,
    $args = array(),
    $is_not_echo = false){

    $html = $hidden = array();

    if( empty($render_data) ){
      if( function_exists('is_wp_debug') && is_wp_debug() )
        echo '<pre> Файл настроек не найден </pre>';
      return false;
    }
    if( isset($render_data['type']) )
        $render_data = array($render_data);


    $default_args = array(
      'item_wrap' => array('<p>', '</p>'),
      'form_wrap' => array('<table class="table form-table"><tbody>', '</tbody></table>'),
      'label_tag' => 'th',
      'hide_desc' => false
      );
    $args = array_merge($default_args, $args);

    if( $args['item_wrap'] === false )
      $args['item_wrap'] = array('', '');

    if($args['form_wrap'] === false)
      $args['form_wrap'] = array('', '');

    if( $args['label_tag'] == 'th' && $is_table == false ){
      $args['label_tag'] = 'label';
    }
    /**
     * Template start
     */
    if($is_table)
        $html[] = $args['form_wrap'][0];

    foreach ( $render_data as $input ) {
      $label   = _isset_false($input['label'], 1);
      $before  = _isset_empty($input['before'], 1);
      $after   = _isset_empty($input['after'], 1);
      $default = _isset_false($input['default'], 1);
      $value   = _isset_false($input['value']);

      if( isset($input['desc']) ){
        $desc = $input['desc'];
        $input['desc'] = false;
      }
      elseif( isset( $input['description'] ) ) {
        $desc = $input['description'];
        $input['description'] = false;
      }
      else {
        $desc = false;
      }  

      if( !isset($input['name']) )
          $input['name'] = _isset_empty($input['id']); //isset($input['id']) ? $input['id'] : '';
      
      /**
       * get values
       */
      $name = ( has_filter( 'dt_admin_options_page_render' ) ) ? $input['id'] : str_replace('[]', '', $input['name']);
      $entry = '';
      if($input['type'] == 'checkbox' || $input['type'] == 'radio'){
        $entry = self::is_checked( $name, $value, _isset_false($active[$name]), $default );
      }
      elseif( $input['type'] == 'select'){
        $entry = _isset_default($active[$name], $default);
      }
      else {
        // if text, textarea, number, email..
        if( isset( $active[$name] ) ){
          $entry = $active[$name];
        }
        elseif( $default ){
          if( !isset($input['placeholder']) ){
            $input['placeholder'] = $default;
          }
          else {
            $entry = $default;
          }
        }

        if( $entry )
          $input['value'] = $entry;
      }

      $func = 'render_' . $input['type'];
      $input_html = self::$func($input, $entry, $is_table, $label);

      if( $desc ){
        if( isset($args['hide_desc']) && $args['hide_desc'] === true )
          $desc_html = "<div class='description' style='display: none;'>{$desc}</div>";
        else
          $desc_html = "<div class='description'>{$desc}</div>";
      } else {
        $desc_html = '';
      }
      
      if(!$is_table){
        $html[] = $before . $args['item_wrap'][0] . $input_html . $args['item_wrap'][1] . $after . $desc_html;
      }
      elseif( $input['type'] == 'hidden' ){
        $hidden[] = $before . $input_html . $after;
      }
      else {
        $item = $before . $args['item_wrap'][0]. $input_html .$args['item_wrap'][1] . $after;

        $html[] = "<tr id='{$input['id']}'>";
        $html[] = "  <{$args['label_tag']} class='label'>{$label}</{$args['label_tag']}>";
        $html[] = "  <td>";
        $html[] = "    " .$item;
        $html[] = $desc_html;
        $html[] = "  </td>";
        $html[] = "</tr>";
      }
    } // endforeach
    if($is_table)
      $html[] = $args['form_wrap'][1];

    $result = implode("\n", $html) . "\n" . implode("\n", $hidden);
    if( $is_not_echo )
      return $result;
    else
      echo $result;
  }
  
  public static function render_checkbox( $input, $checked, $is_table, $label = '' ){
    $result = '';

    if( !isset($input['value']) || $input['value'] === false )
      $input['value'] = 'on';

    if( $checked )
      $input['checked'] = 'true';

    if( apply_filters( 'clear_checkbox_render', false ) )
      $result .= "<input name='{$input['name']}' type='hidden' value=''>\n";

    $result .= "<input";
    foreach ($input as $attr => $val) {
      if($val){
        $attr = esc_attr($attr);
        $val  = esc_attr($val);
        $result .= " {$attr}='{$val}'";
      }
    }
    $result .= ">";

    if(!$is_table && $label)
      $result .= "<label for='{$input['id']}'> {$label} </label>";

    return $result;
  }

  public static function render_select( $input, $active_id, $is_table, $label = '' ){
    $result = '';
    $options = _isset_false($input['options'], 1);
    if(! $options )
      return false;

    if(!$is_table && $label)
      $result .= "<label for='{$input['id']}'> {$label} </label>";

    $result .= "<select";
    foreach ($input as $attr => $val) {
      if( $val ){
        $attr = esc_attr($attr);
        $val  = esc_attr($val);
        $result .= " {$attr}='{$val}'";
      }
    }
    $result .= ">";
    foreach ($options as $value => $option) {
      $active_str = ($active_id == $value) ? " selected": "";
      $result .= "<option value='{$value}'{$active_str}>{$option}</option>";
    }
    $result .= "</select>";

    return $result;
  }

  public static function render_textarea( $input, $entry, $is_table, $label = '' ){
    $result = '';
    // set defaults
    _isset_default($input['rows'], 5);
    _isset_default($input['cols'], 40);

    if(!$is_table && $label)
      $result .= "<label for='{$input['id']}'> {$label} </label>";

    $result .= "<textarea";
    foreach ($input as $attr => $val) {
      if($val){
        $attr = esc_attr($attr);
        $val  = esc_attr($val);
        $result .= " {$attr}='{$val}'";
      }
    }
    $result .= ">{$entry}</textarea>";

    return $result;
  }

  public static function render_text( $input, $entry, $is_table, $label = '' ){
    $result = '';

    if(!$is_table && $label)
      $result .= "<label for='{$input['id']}'> {$label} </label>";
    if( $entry )
      $input['value'] = $entry;

    $result .= "<input";
    foreach ($input as $attr => $val) {
      if( $val ){
        $attr = esc_attr($attr);
        $val  = esc_attr($val);
        $result .= " {$attr}='{$val}'";
      }
    }
    $result .= ">";

    return $result;
  }

  public static function render_hidden( $input, $entry, $is_table, $label = '' ){

     return self::render_text($input, $entry, $is_table, $label);
  }

  public static function render_number($input, $entry, $is_table, $label = ''){
    
    return self::render_text($input, $entry, $is_table, $label);
  }
  
  public static function render_email($input, $entry, $is_table, $label = ''){

    return self::render_text($input, $entry, $is_table, $label);
  }
}