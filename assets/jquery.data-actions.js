/*!
 * Script name: data-actions
 * Script URI: https://github.com/nikolays93/jquery.data-actions/
 * Author: NikolayS93
 * Author URI: //vk.com/nikolays_93
 * Description: Common jQuery actions.
 * Version: 1.1b
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

jQuery(function($){
  function replaceTextOnly($obj, from, to){
    var node = $obj.get(0);
    var childs = node.childNodes;
    for(var inc = 0; inc < childs.length; inc++) {
      if(childs[inc].nodeType == 3){ 
        if(childs[inc].textContent) {
          childs[inc].textContent = childs[inc].textContent.replace(from, to);
        } 
        else {
         childs[inc].nodeValue = childs[inc].nodeValue.replace(from, to);
        }
      }
    }
  }

  function doLoadAction($obj, target, action){
    var evalTarget = ( target !== 'this' ) ? "'"+target+"'" : 'this';
    var props = $obj.data('props');
    eval( '$( ' + evalTarget + ' ).' + action + '(' + props + ');' );
  }

  function doAction($obj, target, trigger, action = false){
    var evalTarget = ( target !== 'this' ) ? "'"+target+"'" : 'this';
    var loadAction = (trigger == 'load') ? action : $obj.data('load');
    if( loadAction )
      doLoadAction($obj, target, loadAction);

    $obj.on(trigger, function(event) {
      var toggleClass = $(this).data('toggle-class');
      if( toggleClass )
        $(target).toggleClass(toggleClass);
      
      var wrap = $(this).data('wrapper');
      if( wrap && event.target !== this )
        return;

      var allowClick  = $(this).data('allow-click');
      if( ! allowClick && trigger == 'click' )
        event.preventDefault();

      if(!action)
        action = $obj.data('action');

      var props = $obj.data('props');
      if( action )
        eval( '$( ' + evalTarget + ' ).' + action + '(' + props + ');' );
    });
  }

  $('[data-target]').each(function(index, el) {
    $this = $(el);
    var target  = $this.data('target');
    var trigger = $this.data('trigger');
    if( ! trigger ) trigger = 'click';
    
    doAction( $this, target, trigger );
    $(this).children('[data-action]').each(function(){
      doAction( $(this), target, trigger );
    });
  });

  var easyActions = ['hide', 'show', 'fade-Out', 'fade-In', 'slide-Up', 'slide-Down'];
  easyActions.forEach(function(item, i, arr) {
    $('[data-' + item + ']').each(function(index, el) {
      var action = item.split('-');
      if(action[0] == 'hide' || action[0] == 'show')
        action = 'toggle';
      else
        action = action[0] + 'Toggle';

      doAction( $(this), $(this).data(item), 'change', action );

      action = item.replace('-', '');

      if( $(this).attr('type') == 'checkbox' && !$(this).is(':checked')){
        if( ['show', 'fadeIn', 'slideDown'].includes(action) ){
          action = action.replace('show', 'hide').replace('In', 'Out').replace('Down', 'Up');
        }
        else{
          action = action.replace('hide', 'show').replace('Out', 'In').replace('Up', 'Down');
        }
      }
      
      doLoadAction( $(this), $(this).data(item), action );
    });
  });

  function textRepalce( $obj ){
    $wasObj = $obj;
    var textReplace = $obj.attr('data-text-replace');
    var textReplaceTo = $obj.attr('data-text-replace-to');
    var target  = $obj.data('target');

    if( target )
      $obj = $( target )

    if( textReplace && textReplaceTo ){
      if( ! $obj.attr('data-text-replaced') ){
        replaceTextOnly($obj, textReplace, textReplaceTo);
        $obj.attr('data-text-replaced', 'true');
      }
      else {
        replaceTextOnly($obj, textReplaceTo, textReplace);
        $obj.removeAttr('data-text-replaced');
      }
    }
    else {
      var text = $obj.text();
      $wasObj.attr('data-text-replace', text);
      $obj.text( textReplace );
    }
  }
  
  $('[data-text-replace]').each(function(index, el) {
    var trigger = $(this).data('trigger');
    if( ! trigger ) trigger = 'click';
    
    if( trigger == 'load' ){
      textRepalce( $(this) );
    }
    else {
      $(this).on(trigger, function(){
        textRepalce( $(this) );
      });
    }
  });
  
});