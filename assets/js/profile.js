jQuery(document).ready( function($) {
	function follow(){
		var button = $(this);
		var args = button.attr('id').split("@");
		
		var url = document.location.origin+'/services/profileCallback.php';
		
		var payload = { args: args, action: 'follow-user', ajax: 1 };
		$.post(url, payload, function(data){
				if(data == 'followed'){
					button.text('Following');
				} else {
					button.text('Follow');
				}
		});
	}
	
	function init() {
		var button = $('.follow-user-button');
		if(button[0]) {
			var args = button.attr('id').split("@");
			
			var url = document.location.origin+'/services/profileCallback.php';
			
			var payload = { args: args, action: 'is-followed?', ajax: 1 };
			$.post(url, payload, function(data){
					if(data == 'followed'){
						button.text('Following');
					} else {
						button.text('Follow');
					}
			});
		}
	}
	
	init();
	
	var followUserButton = $(".follow-user-button");
	followUserButton.on("click", follow);
	
});