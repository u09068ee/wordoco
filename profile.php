<?php require_once('./includes/header.php'); ?>
<?php
    $screen_name = $_GET['screen_name'];
    $sql = "select Id, screen_name, email, profile_image from users where screen_name = substr(:screen_name,2)";
    $query = $db->prepare( $sql );
    $query->execute( array( ':screen_name'=>$screen_name));
	
	if(!$query->rowCount()) {
		header('Location: /');
	}
	
	$user_object = $query->fetch();
?>

<div class="container">
    <div class="profileimg" style="<?php echo "background: url(".str_replace('_normal','',$user_object['profile_image']).") no-repeat center; background-size: contain;"; ?>">
    
    </div>
    <h3><?php echo '@'.$user_object['screen_name']; ?></h3> 
    <?php if (!$_SESSION['Id']) { ?>
            <a href='./login.php'><button id="login-to-follow" class="button-green">Follow</button></a>
    <?php } elseif ($user_object['Id'] != $_SESSION['Id']) { ?>
			<button id="<?php echo $user_object['Id'].'@'.$user_object['screen_name']; ?>" class="button-green follow-user-button">Follow</button>
	<?php } ?>
</div>

<div class="notes-profile">
    <center style="height: 25px;">
        <?php
            $sql = "select user_word from user_words where user_Id = :user_Id";
            $likes_query = $db->prepare( $sql );
            $likes_query->execute( array( ':user_Id'=>$user_object['Id']));
            
            $sql = "select u.screen_name from users_followed uf inner join users u on u.Id = uf.followed_user_Id where uf.user_Id = :user_Id";
		    $followings_query = $db->prepare( $sql );
	        $followings_query->execute( array( ':user_Id'=>$user_object['Id']));
	        
	        $sql = "select u.screen_name from users_followed uf inner join users u on u.Id = uf.user_Id where uf.followed_user_Id = :user_Id";
		    $followers_query = $db->prepare( $sql );
		    $followers_query->execute( array( ':user_Id'=>$user_object['Id']));
		?>
        <div id="user-likes-tab-header" class="profiletabs" onclick="(function(){$('.content-tab').hide();$('#user-likes-tab').show();})()"><?php echo $likes_query->rowCount(); ?> Likes</div>
        <div id="user-followings-tab-header" class="profiletabs" onclick="(function(){$('.content-tab').hide();$('#user-followings-tab').show();})()"><?php echo $followings_query->rowCount(); ?> Following</div>
        <div id="user-followers-tab-header" class="profiletabs" onclick="(function(){$('.content-tab').hide();$('#user-followers-tab').show();})()" style="border-right:0;"><?php echo $followers_query->rowCount(); ?> Followers</div>
    </center>

    <ul id="user-likes-tab" class="content-tab">
        <?php if ($likes_query->rowCount()){ ?>
                    <b><?php echo $likes_query->rowCount() ?> likes</b>
        <?php   foreach ($likes_query as $user_likes){ ?>
                    <li> <a href="./<?php echo $user_likes['user_word']; ?>"><?php echo $user_likes['user_word']; ?></a></li>
        <?php   } 
              } else { ?>
                    <?php if ($user_object['Id'] == $_SESSION['Id']) { ?>
                        <li>Like some words, and check back here in a minute <img src="http://emojipedia-us.s3.amazonaws.com/cache/e2/fc/e2fc91084bd4870dd2dc8947adc1f363.png" width="25" style="vertical-align:bottom;"></li>
                    <?php } else { ?>
                        <li><?php echo '@'.$user_object['screen_name']; ?> hasn't liked any words yet...</li>
                    <?php } ?>
        <?php } ?>
    </ul>
    
    <ul id="user-followings-tab" class="content-tab" style="display:none;">
        <?php if ($followings_query->rowCount()){ ?>
                    <b><?php echo $followings_query->rowCount() ?> followings</b>
        <?php   foreach ($followings_query as $user_followings){ ?>
                    <li> <a href="./<?php echo '@'.$user_followings['screen_name']; ?>"><?php echo $user_followings['screen_name']; ?></a></li>
        <?php   } 
              } else { ?>
                    <?php if ($user_object['Id'] == $_SESSION['Id']) { ?>
                        <li>Start following others, its more fun with friends around...</li>
                    <?php } else { ?>
                        <li><?php echo '@'.$user_object['screen_name']; ?> is enjoying solitude...</li>
                    <?php } ?>
        <?php } ?>
    </ul>
    
    <ul id="user-followers-tab" class="content-tab" style="display:none;">
        <?php if ($followers_query->rowCount()){ ?>
                    <b><?php echo $followers_query->rowCount() ?> followers</b>
        <?php   foreach ($followers_query as $user_followers){ ?>
                    <li> <a href="./<?php echo '@'.$user_followers['screen_name']; ?>"><?php echo $user_followers['screen_name']; ?></a></li>
        <?php   } 
              } else { ?>
                    <?php if ($user_object['Id'] == $_SESSION['Id']) { ?>
                        <li>You have no followers. Time to invite some friends...</li>
                    <?php } else { ?>
                        <li>You can be <?php echo '@'.$user_object['screen_name']; ?>'s first follower...</li>
                    <?php } ?>
                    
        <?php } ?>
    </ul>

<?php 
    if ($user_object['Id'] == $_SESSION['Id']) { ?>
    <a href="./logout.php"><button class="button-red logout-button">Logout</button></a>
<?php } ?>
</div>

<?php require_once('includes/footer.php'); ?>