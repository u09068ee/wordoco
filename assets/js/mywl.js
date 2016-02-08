jQuery(document).ready( function($) {
	linkVisible();
    $('.mywl-link').on('click', function() {
        dhis = $(this);
        wpfp_do_js( dhis, 1 );
        // for favorite post listing page
        if (dhis.hasClass('remove-parent')) {
            dhis.parent("li").fadeOut();
        }
        return false;
    });
	
	function wpfp_do_js( dhis, doAjax ) {
		loadingImg = $('.mywl-img');
		loadingImg.show();
		url = document.location.origin+'/services/posthandler.php';
		//params = dhis.attr('href').replace('?', '') + '&ajax=1';
		/*
		if ( doAjax ) {
			jQuery.get(url, params, function(data) {
					dhis.parent().html(data);
					if(typeof wpfp_after_ajax == 'function') {
						wpfp_after_ajax( dhis ); // use this like a wp action.
					}
					loadingImg.hide();
				}
			);
		}*/
		
		if ( doAjax ){
			var word;
			if($('#foo .search').val()){
				word = $('#foo .search').val();
				// word = window.location.pathname.substr(1);
				// word = word.replace(/_/g," ");
			}
			if(word!=null){
				var mydata = { word : word, mywlaction: 'add', ajax:1 };
				$.post(url,mydata, function(data){
						if(data == 'word_removed'){
							$('.mywl-link').removeClass('favorited');
							loadingImg.hide();
						} else {
							$('.mywl-link').addClass('favorited');
							loadingImg.hide();
						}
				});
			} else {
				loadingImg.hide();
			}
		}
		
	}
	$(window).bind('popstate', function(event) {
		linkVisible();
	});
	function linkVisible(){
// 		if(window.location.pathname.length > 1){
        if($('#foo .search').val()){
			$('.mywl-link').show();
		} else {
			$('.mywl-link').hide();
		}
	}
});