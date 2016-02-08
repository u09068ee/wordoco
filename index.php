<?php 
	require_once('includes/header.php');
	require_once('includes/functions.php');
?>

<div class="container">
<form id="foo" action="./">
	<br>
	<input class="search" name="search" type="text" placeholder="Search for definitions" autofocus autocomplete="off" autocapitalize="off" value="" />
	<?php 
		$word = substr($_SERVER['REQUEST_URI'],1);
		if ($is_user_logged_in) {
	?>
		<span class="mywl-link" style="display: block; top:-70px; left:-15px;">
		<img src="../assets/images/loading.gif" alt="Loading" title="Loading" class="mywl-hide mywl-img">
		&#9829;</span>
	<?php 
	} else { ?>
		<a href='./login.php'>
		<span class="mywl-link-login" style="display: block; top:-70px; left:-15px;">
		<img src="../assets/images/loading.gif" alt="Loading" title="Loading" class="mywl-hide mywl-img">
		&#9829;</span>
		</a>
	<?php
	}
	?>
	<br/>
</form>
<div id="detail">
	<div id="result">
	<?php echo wordo_get_result();?>
	</div>
	<?php
		if ($is_user_logged_in) {
	?>
	<div id="notes"></div>
	<?php 
		}
	?>
</div>
<div id="log"></div>
</div>

<div class="notes" style="display: none;">
	
    <center style="height: 25px;">
        <div id="word-likes-tab-header" class="profiletabs" style="border-right:0;"></div>
    </center>
    <ul id="word-likes-tab" class="content-tab">
    </ul>
    
</div>

	
<?php require_once('includes/footer.php'); ?>