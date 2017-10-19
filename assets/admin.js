jQuery(document).ready(function($) {
    console.log('project-settings script loaded');

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
    $('#adminmenu > li > a').each(function() {
        var elem = $(this).attr('href').split('?');
        if(elem[0] == 'edit.php'){
            elem[1] = elem[1] ? elem[1].replace('_', '-') : 'post-type=post';

            link = '/wp-admin/options-general.php?page=' + menu_disabled.edit_cpt_page + '&' + elem[1];
            $(this).parent('li').append(
                $("<a></a>").attr('href', link).attr('class', 'after dashicons dashicons-admin-generic') );
        }
    });

    // Добавим всем глазки
    $('#adminmenu li').each(function() {
        if(
            'collapse-menu' !== $(this).attr('id') &&
            ! $(this).hasClass( 'wp-menu-separator' ) &&
            ! $(this).hasClass( 'hide-if-no-customize' )
        ){
            $(this).append( '<span class="after dashicons dashicons-hidden"></span>' );
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

    // Toogle Cookie
    $('#setNotHide').on('click', function() {
        $(this).toggleClass('button-primary');

        if($(this).hasClass('button-primary')) {
            var date = new Date(new Date().getTime() + 3600 * 24 * 7 * 1000);
            document.cookie = "developer=true; path=/; expires=" + date.toUTCString();
        }
        else {
            document.cookie = "developer=; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
        }
    });

    // Placeholder to value
    $('input[type=\'text\'], input[type=\'number\'], textarea').on('focus', function() {
        var ph = $(this).attr('placeholder');
        if( ! $(this).val() && ! $(this).attr('readonly') && typeof(ph) == 'string' ) {
            $(this).val( ph.replace('e.g. ', '').replace('к пр. ', '') );
            $(this).select();
        }
    });

    // change referer
    $('form#options').on('submit', function(e) {
        if( ! $('input#post_type_name').val() ) {
            $('input#post_type_name').focus();
            return false;
        }

        var referer = $('[name="_wp_http_referer"]').val();
        var type = '&post-type=' + $('input#post_type_name').val();
        $('[name="_wp_http_referer"]').val( referer.replace('do=add', '') + type );
    });

    var patterns = [
        { selector : 'input#post_type_name', pattern : '[id]' },
        { selector : 'input#labels_singular_name', pattern : '[singular]' },
        { selector : 'input#labels_name', pattern : '[plural]' },
        { selector : 'input#labels_name_admin_bar', pattern : '[accusative]' }
    ];

    patterns.forEach(function(item, i, arr) {
        $(item.selector).on('keyup change focus', function(event) {
            var self = $(this);

            $("input[data-pattern]").each(function(index, el) {
                if( ! $(this).val() ) {
                    $(this).attr('data-fill-pattern', 1);
                }

                if( $(this).attr('data-fill-pattern') ) {
                    var pat = $(this).attr('data-pattern');

                    if( pat.indexOf(item.pattern) == 0 ) {
                        $(this).val(pat.replace(item.pattern, self.val()) );
                    }

                    else if( pat.indexOf(" " + item.pattern) >= 0 ) {
                        $(this).val( pat.replace(" " + item.pattern, " " + self.val().toLowerCase() ) );
                    }
                }
            });
        });
    });
});
