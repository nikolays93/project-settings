jQuery(document).ready(function($) {
    console.log('project-settings script loaded');

    // Считаем нажатые глазки
    function compileResult() {
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
    $('#adminmenu > li a').each(function() {
        var elem = $(this).attr('href').split('?');
        if('edit.php' == elem[0]) {
            var value  = 'do=edit&context=types&';
                value += (! elem[1]) ? 'value=post' : elem[1].replace('post_type', 'value');

            link = '/wp-admin/options-general.php?page=' + menu_disabled.edit_cpt_page + '&' + value;
            $(this).parent('li').append(
                $("<a></a>").attr('href', link).attr('class', 'after dashicons dashicons-admin-generic') );
        }

        if('edit-tags.php' == elem[0]) {
            var value  = 'do=edit&context=taxes&';
                value += elem[1].replace('taxonomy', 'value');
            link = '/wp-admin/options-general.php?page=' + menu_disabled.edit_cpt_page + '&' + value;
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

    // AutoChange labels
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

    $.each($('.wp-list-table'), function(index, el) {
        var $table = $(el);
        var id = $table.attr('class').replace('wp-list-table widefat fixed striped ', '');
        var $filter = $('#' + id + '__filter');
        var $tbody = $table.find('tbody');
        var columns = $table.find('thead tr th').length + 1;

        $filter.on('change', function(event) {
            var val = $filter.val();
            $tbody.find('tr').each(function(index, el) {
                $(this).hide().removeClass('showed');

                if( '' == val || val == $(this).attr('class') )
                    $(this).show().addClass('showed');
            });

            $tbody.find('.empty').remove();
            if( ! $tbody.find('tr.showed').length ) {
                $tbody.append('<tr class="empty"><td colspan="'+ columns +'">Данных не найдено</td></tr>')
            }
        }).trigger('change');
    });

    $('#globals.postbox').removeClass('closed');
    if( 'do' in menu_disabled._Request ) {
        switch (menu_disabled._Request.do) {
            case 'add':
            case 'edit':
            case 'remove':
                $('#globals.postbox').addClass('closed');
                break;
        }
    }
});
