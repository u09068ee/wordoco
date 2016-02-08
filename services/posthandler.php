<?php 
    require_once('../includes/session.php');
    
    if (!($_SERVER['REQUEST_METHOD'] === 'POST')) {
        header('Location: ./');
    } elseif (!$_SESSION['Id']) {
        require_once('../includes/db.php');
        
        $action = $_POST['mywlaction'];
        
        if ($action === 'whoElseLiked') {
            $user_word = $_POST['word'];    
            $sql = "select u.screen_name as screen_name,DATE_FORMAT(uw.row_created_date,'%Y-%m-%dT%TZ') as word_liked_time_ISO8601
		            ,DATE_FORMAT(uw.row_created_date,'%M %d, %Y') as word_liked_time from user_words uw inner join users u on uw.user_id = u.Id where uw.user_word = :user_word
		            order by uw.row_created_date desc";
            $query = $db->prepare( $sql );
            $query->execute( array( ':user_word'=>$user_word));
            $whoElseLiked_rows = $query;
            if ($whoElseLiked_rows->rowCount()) {
                echo '<b>'.$whoElseLiked_rows->rowCount().' Likes</b>';
                foreach ($whoElseLiked_rows as $row) {
					echo "\r\n<li><b><a href='./@" . $row['screen_name'] . "'>@" . $row['screen_name'] . "</a></b> liked this word, 
					<time class='timeago' datetime='".$row['word_liked_time_ISO8601']."'>".$row['word_liked_time']."</time></li>";
				}
            } else {
				echo 'none'; 
            }
        }
    } else {
    
        require_once('../includes/db.php');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['Id'];
            
            $action = $_POST['mywlaction'];
            $user_word = $_POST['word'];
            
            if ($action === 'check') {
                
                $sql = "select 'favorite' as favorite from user_words where user_id = :user_id and user_word = :user_word";
                $query = $db->prepare( $sql );
                $query->execute( array( ':user_id'=>$user_id, ':user_word'=>$user_word));
                $row = $query->fetch();
                
                echo $row['favorite'];
                
            } elseif ($action === 'add') {
                
                $sql = "delete from user_words where user_id = :user_id and user_word = :user_word";
                $query = $db->prepare( $sql );
                $query->execute( array( ':user_id'=>$user_id, ':user_word'=>$user_word));
                $deleted_rows = $query->rowCount();
                if ($deleted_rows == 0) {
                    $sql = "insert into user_words values (null, :user_id, :user_word, null)";
                    $query = $db->prepare( $sql );
                    $query->execute( array( ':user_id'=>$user_id, ':user_word'=>$user_word));
                    echo 'word_added';
                } else {
                    echo 'word_removed';    
    			}
            } elseif ($action === 'whoElseLiked') {
                $user_word = $_POST['word'];    
                $sql = "select u.screen_name as screen_name,DATE_FORMAT(uw.row_created_date,'%Y-%m-%dT%TZ') as word_liked_time_ISO8601
    		            ,DATE_FORMAT(uw.row_created_date,'%M %d, %Y') as word_liked_time from user_words uw inner join users u on uw.user_id = u.Id where uw.user_word = :user_word
    		            order by uw.row_created_date desc";
                $query = $db->prepare( $sql );
                $query->execute( array( ':user_word'=>$user_word));
                $whoElseLiked_rows = $query;
                if ($whoElseLiked_rows->rowCount()) {
                    echo '<b>'.$whoElseLiked_rows->rowCount().' Likes</b>';
                    foreach ($whoElseLiked_rows as $row) {
    					echo "\r\n<li><b><a href='./@" . $row['screen_name'] . "'>@" . $row['screen_name'] . "</a></b> liked this word, 
    					<time class='timeago' datetime='".$row['word_liked_time_ISO8601']."'>".$row['word_liked_time']."</time></li>";
    				}
                } else {
    				echo 'none'; 
                }
            }
        }
    }
?>