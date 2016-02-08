
<nav>
<a href="./"><div class="logo">wordo</div></a>
<?php
    $is_user_logged_in = $_SESSION['Id'];
	if ($is_user_logged_in) {
	    $profile_image = $_SESSION['profile_image'];
?> 

<a href="<?php echo './'.$_SESSION['screen_name']; ?>"><div class="profile-icon" style="background: url(<?php echo $profile_image ?>) no-repeat center;"></div></a>
<a id="logoutbutton" href="./notifications.php"><div class="notifications-nav">Notifications</div></a>
            <?php } else { ?>
<a href="./login.php"><div class="login-with-twitter">Login with Twitter</div></a>
            <?php } ?>
</nav>
