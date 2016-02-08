jQuery(document).ready( function($) {
    function notificationCount(){
        var url = document.location.origin+'/services/profileCallback.php';

		var payload = { action: 'newNotificationCount', ajax: 1 };
		$.post(url, payload, function(data){
				if(data != '0'){
					// button.css('background-color','blue').css('color','white');
					notificationsTab.text('Notifications ('+data+')');
				} else {
					// button.css('background-color','').css('color','black');
					notificationsTab.text('Notifications');
				}
		});
    }
    
    function updateLastSeenDate(){
        var url = document.location.origin+'/services/profileCallback.php';

		var payload = { action: 'updateLastSeenDate', ajax: 1 };
		$.post(url, payload, function(data){
				if(data != '0'){
					// button.css('background-color','blue').css('color','white');
					notificationsTab.text('Notifications');
				}
		});
    }
        
    var notificationsTab = $('.notifications-nav');
    
    jQuery("time.timeago").timeago();
    
    (function notificationPolling(){
        // do some stuff
        notificationCount();
        setTimeout(notificationPolling, 60000);
    })();
    // notificationsTab.on("click", updateLastSeenDate);
});