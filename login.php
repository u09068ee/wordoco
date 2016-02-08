<?php 
	require_once('includes/header.php');
	require_once('includes/TwitterOAuth/Common/Curl.php');
	require_once('includes/TwitterOAuth/Auth/AuthAbstract.php');
	require_once('includes/TwitterOAuth/Auth/SingleUserAuth.php');
	require_once('includes/TwitterOAuth/Serializer/SerializerInterface.php');
	require_once('includes/TwitterOAuth/Serializer/ArraySerializer.php');
	require_once('includes/TwitterOAuth/Exception/TwitterException.php');
	
	use TwitterOAuth\Auth\SingleUserAuth;
	use TwitterOAuth\Serializer\ArraySerializer;

	$credentials = array(
    	'consumer_key' => 'fxy5RXmMPpvMyMlYWVMP0kqx3',
    	'consumer_secret' => 'LT29Fbr5OuVPs40OsyiOpFy7mpdqVUNo0zvEuFC59nk0VXRzJu',
	);
	
	$serializer = new ArraySerializer();
	$auth = new SingleUserAuth($credentials, $serializer);
	
	$params = array(
    	'oauth_callback' => rawurldecode('https://upwork-projects-u09068ee.c9users.io/services/loginCallback.php')
    	// 'oauth_callback' => rawurldecode('http://162.243.146.56/services/loginCallback.php')
	);

?>
<?php
	$loginTwitter = 1;
	if($_GET['loginTwitter'] || $loginTwitter) {
		// $loginTwitter = $_GET['loginTwitter'];
		if($loginTwitter == 1) {
			
			$response = $auth->post('oauth/request_token', $params);
			
			// echo '<pre>'; print_r($auth->getHeaders()); echo '</pre>';
			// echo '<pre>'; print_r($response['oauth_token']); echo '</pre><hr />';
			
			// $_COOKIE['oauth_token'] = $response['oauth_token'];
			// $_COOKIE['oauth_token_secret'] = $response['oauth_token_secret'];
			
			// setcookie("oauth_token", $response['oauth_token'],time()+60*5);
			// setcookie("oauth_token_secret", $response['oauth_token_secret'],time()+60*5);
			$_SESSION['oauth_token'] = $response['oauth_token'];
			$_SESSION['oauth_token_secret'] = $response['oauth_token_secret'];
			
			// $response = $auth->get('auth/authenticate?oauth_token='.$response['oauth_token']);
			
			header('Location: https://api.twitter.com/oauth/authenticate?oauth_token='.$response['oauth_token']);
		}
	}
?>
<!--<div class="container">-->
<!--	<div id="loginpage">-->
		
<!--		<p>Welcome to the login page.</p>-->

		<!-- IMAGE BUTTON -->
		<!--<a href="http://wordo.co/wp-login.php?loginFacebook=1&redirect=http://wordo.co" onclick="window.location = 		        'http://wordo.co/wp-login.php?loginFacebook=1&redirect=http://wordo.co'; return false;">-->
			<!-- FB LOGIN BUTTON -->
		<!--	<div class="new-fb-btn new-fb-1 new-fb-default-anim">-->
		<!--		<div class="new-fb-1-1">-->
		<!--			<div class="new-fb-1-1-1">-->
		<!--				CONNECT WITH-->
		<!--			</div>-->
		<!--		</div>-->
		<!--	</div>-->
		<!--</a>-->
<!--		<a href="./login.php?loginTwitter=1" target="_self">-->
<!--			<img src="./assets/images/sign-in-with-twitter-gray.png"/>-->
<!--		</a>-->
		
<!--	</div>-->
<!--</div>-->

<?php 
require_once('includes/footer.php');
?>