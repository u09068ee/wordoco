<?php 
	require_once('includes/header.php');
	require_once('includes/db.php'); 
    
?>
<div class="container">

<?php 
	$user_Id = $_SESSION['Id'];
	
	if(!$user_Id) {
		header('Location: ./');
	}
?>
	<center style="height: 25px;">
        <div id="user-followers-tab-header" class="notificationtabs"  style="border-right:0; margin-top: 45px;">Notifications</div>
    </center>
<?php
	
	$sql = "select u.screen_name screen_name, uw.user_word user_word
		,DATE_FORMAT(uw.row_created_date,'%Y-%m-%dT%TZ') as word_liked_time_ISO8601
		,DATE_FORMAT(uw.row_created_date,'%M %d, %Y') as word_liked_time
		,uw.row_created_date-(select last_seen_date from users where Id = :user_Id) newNotifications
		from user_words 
		uw inner join users_followed uf on uw.user_Id = uf.followed_user_Id 
		inner join users u on uf.followed_user_Id = u.Id
		where uf.user_Id = :user_Id order by uw.row_created_date desc";
    $query = $db->prepare( $sql );
    $query->execute( array( ':user_Id'=>$user_Id));
    
    if ($query->rowCount()){
    	echo '<div class="notification"><ul>';
   		foreach ($query as $row){
        	echo "<li class='".(($row['newNotifications']>=0)?'new-notifications':'')."'><div style='font-size:1rem;'>
        			<b><a href='./@" . $row['screen_name'] . "'>@".$row['screen_name']."</a></b>
        			liked the word <b><a href='./" . $row['user_word'] . "'>".$row['user_word']."</a></b>, 
        			<time class='timeago' datetime='".$row['word_liked_time_ISO8601']."'>".$row['word_liked_time']."</time>".
        		 "</div></li>";
   		}
   		echo '</ul></div>';
	} else {
	    echo "You have no new notification...";
	}
	
	$sql = "update users set last_seen_date = now() where Id = :user_Id";
    $query = $db->prepare( $sql );
    $query->execute( array( ':user_Id'=>$user_Id));
	    
?>
</div>
<?php require_once('includes/footer.php'); ?>