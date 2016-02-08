<?php 
	require_once('../includes/session.php');
	require_once('../includes/db.php');
	
	require_once('../includes/TwitterOAuth/Common/Curl.php');
	require_once('../includes/TwitterOAuth/Auth/AuthAbstract.php');
	require_once('../includes/TwitterOAuth/Auth/SingleUserAuth.php');
	require_once('../includes/TwitterOAuth/Serializer/SerializerInterface.php');
	require_once('../includes/TwitterOAuth/Serializer/ArraySerializer.php');
	require_once('../includes/TwitterOAuth/Exception/TwitterException.php');
	
	use TwitterOAuth\Auth\SingleUserAuth;
	use TwitterOAuth\Serializer\ArraySerializer;

	$credentials = array(
    	'consumer_key' => 'fxy5RXmMPpvMyMlYWVMP0kqx3',
    	'consumer_secret' => 'LT29Fbr5OuVPs40OsyiOpFy7mpdqVUNo0zvEuFC59nk0VXRzJu',
    	'oauth_token' => $_SESSION['oauth_token'],
    	'oauth_token_secret' => $_SESSION['oauth_token_secret']
	);
	
	$serializer = new ArraySerializer();
	$auth = new SingleUserAuth($credentials, $serializer);
	
	
    if ($_SESSION['oauth_token'] == $_GET['oauth_token'])
    {
        $params = array(
    	'oauth_verifier' => $_GET['oauth_verifier']
	    );
        
        $response = $auth->post('oauth/access_token', $params);

//         setcookie("oauth_token", $response['oauth_token'],0,'/');
// 		setcookie("oauth_token_secret", $response['oauth_token_secret'],0,$path='/');
		
		$_SESSION['oauth_token'] = $response['oauth_token'];
		$_SESSION['oauth_token_secret'] = $response['oauth_token_secret'];
        
        $credentials = array(
        	'consumer_key' => 'fxy5RXmMPpvMyMlYWVMP0kqx3',
        	'consumer_secret' => 'LT29Fbr5OuVPs40OsyiOpFy7mpdqVUNo0zvEuFC59nk0VXRzJu',
        	'oauth_token' => $response['oauth_token'],
        	'oauth_token_secret' => $response['oauth_token_secret']
	    );
        
        $auth = new SingleUserAuth($credentials, $serializer);
        
        $params = array(
    	'include_entities' => 'false',
    	'skip_status' => 'true',
    	'include_email' => 'true'
	    );
        
        $response = $auth->get('account/verify_credentials', $params);
        
        $twitter_user_id = $response['id'];
        $email = $response['email'];
        $screen_name = $response['screen_name'];
        $profile_image_url_https = $response['profile_image_url_https'];
        
        $sql = "select count(Id) AS alreadyRegisteredUser from users where twitter_user_id = :twitter_user_id";
        $query = $db->prepare( $sql );
        $query->execute( array( ':twitter_user_id'=>$twitter_user_id));
        $row = $query->fetch();
        
        $newInserted = false;
        
        if ($row['alreadyRegisteredUser'] == 0) {
            $sql = "INSERT INTO users ( twitter_user_id, screen_name, email, profile_image ) VALUES ( :twitter_user_id, :screen_name, :email, :profile_image )";
            $query = $db->prepare( $sql );
            $query->execute( array( ':twitter_user_id'=>$twitter_user_id, ':screen_name'=>$screen_name,
                ':email' => $email, ':profile_image' => $profile_image_url_https) );
            
            $newInserted = true;
            
        } else {
            $sql = "UPDATE users SET screen_name = :screen_name, email = :email, profile_image = :profile_image where twitter_user_id = :twitter_user_id";
            $query = $db->prepare( $sql );
            $query->execute( array( ':screen_name'=>$screen_name,
                ':email' => $email, ':profile_image' => $profile_image_url_https, ':twitter_user_id'=>$twitter_user_id) );
        }
        

        $sql = "select Id, screen_name, profile_image from users where twitter_user_id = :twitter_user_id";
        $query = $db->prepare( $sql );
        $query->execute( array( ':twitter_user_id'=>$twitter_user_id));
        $row = $query->fetch();
        
        $_SESSION['Id'] = $row['Id'];
        $_SESSION['screen_name'] = '@'.$row['screen_name'];
        $_SESSION['profile_image'] = $row['profile_image'];
        
        if (true){
            $cursor = -1;
            while($cursor != 0) {
                $params = array(
            	    'user_id' => $twitter_user_id,
            	    'stringify_ids' => 'true',
            	    'cursor' => $cursor
        	    );
                
                $response = $auth->get('friends/ids', $params);
                
                $cursor = $response['next_cursor'];
                
                $sql = "select Id,twitter_user_id from users where Id not in (select followed_user_Id from users_followed where user_Id = :user_Id)";
                $query = $db->prepare( $sql );
                $query->execute( array( ':user_Id' => $_SESSION['Id']));
                $response2 = $query->fetchAll(PDO::FETCH_KEY_PAIR);
                
                $correlation = array_intersect($response2,$response['ids']);
    
                foreach ($correlation as $key => $value){
                    $sql = "INSERT INTO users_followed ( user_Id, followed_user_id ) VALUES ( :user_Id, :followed_user_id )";
                    $query = $db->prepare( $sql );
                    $query->execute( array( ':user_Id'=> $_SESSION['Id'], ':followed_user_id'=>$key ) );
                }
            }
            
            $cursor = -1;
            while($cursor != 0) {
                $params = array(
            	    'user_id' => $twitter_user_id,
            	    'stringify_ids' => 'true',
            	    'cursor' => $cursor
        	    );
                
                $response = $auth->get('followers/ids', $params);
                
                $cursor = $response['next_cursor'];
                
                $sql = "select Id,twitter_user_id from users where Id not in (select user_Id from users_followed where followed_user_Id = :user_Id)";
                $query = $db->prepare( $sql );
                $query->execute( array( ':user_Id' => $_SESSION['Id']));
                $response2 = $query->fetchAll(PDO::FETCH_KEY_PAIR);
                
                $correlation = array_intersect($response2,$response['ids']);
    
                foreach ($correlation as $key => $value){
                    $sql = "INSERT INTO users_followed ( user_Id, followed_user_id ) VALUES ( :user_Id, :followed_user_id )";
                    $query = $db->prepare( $sql );
                    $query->execute( array( ':user_Id'=> $key, ':followed_user_id'=> $_SESSION['Id']) );
                }
            }
        }
        
        header('Location: ../');
        
    } else {
        header('Location: ../login.php');
    }
?>