pcjq = jQuery.noConflict();
pcjq(function( $ ) {
  // onload
  $(function(){

    // Считаем нажатые глазки
    function compileResult(){
      var result = '';
      $('#adminmenu>li>span.after').each(function(){
        if($(this).hasClass('hide')){
          result += $(this).parent().children('a').attr('href') + ',';
        }
      });
      $('input#menu').val(result);

      var result = '';
      $('#adminmenu>li>ul span.after').each(function(){

        if($(this).hasClass('hide')){
          var parent = $(this).parent().parent().parent().children('a').attr('href');
          var obj = $(this).parent().children('a').attr('href');
          result += parent + '>' + obj +',';
        }
      });
      $('input#sub_menu').val(result);
    }

    // Добавить шестерни
    $('#adminmenu > li > a').each(function(){
      var elem = $(this).attr('href').split('?');
      if(elem[0] == 'edit.php'){
        elem[1] = elem[1] ? elem[1].replace('_', '-') : 'post-type=post';

        link = '/wp-admin/options-general.php?page=' + menu_disabled.edit_cpt_page + '&' + elem[1];
        $(this).parent('li').append( 
          $("<a></a>").attr('href', link).attr('class', 'after dashicons dashicons-admin-generic') );
      }
    });

    // Добавим всем глазки
    $('#adminmenu li').each(function(){
      if(!$(this).hasClass('wp-menu-separator') && $(this).attr('id') != 'collapse-menu' && !$(this).hasClass('hide-if-no-customize')){
        $(this).append('<span class="after dashicons dashicons-hidden"></span>');
      }
    });      

    var mainMenus = menu_disabled.menu.split(',');
    var subMenus = menu_disabled.sub_menu.split(',');

    mainMenus.forEach(function(item, i) {
      $('a[href="'+item+'"]:first').parent().children('.after').addClass('hide');
    });
    subMenus.forEach(function(item, i) {
      splitItem = item.split('>');
      $('a[href="'+splitItem[1]+'"]:last').parent().children('.after').addClass('hide');
    });
    $('#adminmenu span.after').on('click', function(){
      $(this).toggleClass('hide');

      compileResult();
      return false;
    });

    // Добавить куку
    $('#setNotHide').on('click', function(){
      $(this).toggleClass('button-primary');

      if($(this).hasClass('button-primary')){
        var date = new Date(new Date().getTime() + 3600 * 24 * 7 * 1000);
        document.cookie = "developer=true; path=/; expires=" + date.toUTCString();
      }
      else {
        document.cookie = "developer=; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
      }
    });
  
    // Placeholder to value  
    $('input[type=\'text\'], input[type=\'number\'], textarea').on('focus', function(){
      var ph = $(this).attr('placeholder');
      if($(this).val() == '' && typeof(ph) == 'string' ){
        $(this).val( ph.replace('e.g. ', '').replace('к пр. ', '') );
        $(this).select();
      }
    });

    // change referer
    $('form#options').on('submit', function(e){
      var val = $('[name="_wp_http_referer"]').val() + '&post-type=';
      $('[name="_wp_http_referer"]').val( val + $('input#post_type_name').val() );
    });

    var singleSelector = 'input#labels_singular_name';
    var pluralSelector = 'input#label';
    $( singleSelector + ', ' + pluralSelector).on('keyup change focus', function(event) {
      var single = $(singleSelector).val();
      var plural = $(pluralSelector).val();

      $("input[data-pattern]").each(function(index, el) {
        if( ! $(this).val() )
          $(this).attr('data-fill-pattern', 1);

        if($(this).attr('data-fill-pattern'))
          $(this).val( $(this).attr('data-pattern').replace("[single]", single).replace("[plural]", plural) );
      });
    });

  });
});