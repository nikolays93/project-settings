pcjq = jQuery.noConflict();
pcjq(function( $ ) {
	// Считаем нажатые глазки
	function compileResult(){
		var result = '';
		$('#adminmenu>li>span.after').each(function(){
			if($(this).hasClass('hide')){
				result += $(this).parent().children('a').attr('href') + ',';
			}
		});
		$('textarea#menu').val(result);

		var result = '';
		$('#adminmenu>li>ul span.after').each(function(){

			if($(this).hasClass('hide')){
				var parent = $(this).parent().parent().parent().children('a').attr('href');
				var obj = $(this).parent().children('a').attr('href');
				result += parent + '>' + obj +',';
			}
		});
		$('textarea#sub_menu').val(result);
	}

	// onload
	$(function(){
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

		// Добавим всем глазки
		$('#adminmenu li').each(function(){
			if(!$(this).hasClass('wp-menu-separator') && $(this).attr('id') != 'collapse-menu' && !$(this).hasClass('hide-if-no-customize')){
				$(this).append('<span class="after dashicons dashicons-hidden"></span>');
			}
		});

		$('#adminmenu > li > a').each(function(){
			var elem = $(this).attr('href').split('?');
			if(elem[0] == 'edit.php'){
				if( ! elem[1] )
					elem[1] = 'post_type=post'

				link = '/wp-admin/options-general.php?page=' + menu_disabled.edit_cpt_page + '&edit_' + elem[1];

				$(this).parent('li').append( 
					$("<a></a>").attr('href', link).attr('class', 'after dashicons dashicons-admin-generic') );

				
			}
		});

		var mainMenus = menu_disabled.menu.split(',');
		var subMenus = menu_disabled.sub_menu.split(',');

		mainMenus.forEach(function(item, i) {
			// console.log( i + ": " + item );
			$('a[href="'+item+'"]:first').parent().children('.after').addClass('hide');
		});
		subMenus.forEach(function(item, i) {
			splitItem = item.split('>');
			$('a[href="'+splitItem[1]+'"]:last').parent().children('.after').addClass('hide');

			// $('a[href="'+item+'"]').parent().children('.after').addClass('hide');
		});
		// </>
		$('#adminmenu span.after').on('click', function(){
			$(this).toggleClass('hide');

			compileResult();
			return false;
		});
	});
});