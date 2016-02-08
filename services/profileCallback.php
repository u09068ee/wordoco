<?php 
    require_once('../includes/session.php');

    if (!($_SERVER['REQUEST_METHOD'] === 'POST')) {
        header('Location: ./');
    } elseif (!$_SESSION['Id']) {
        ;
    } else {
    
        require_once('../includes/db.php');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['Id'];
            
            $action = $_POST['action'];
            
            if ($action === 'follow-user') {
                $args = $_POST['args'];
                $follow_user_id = $args[0];
                $screen_name = $args[1];
                    
                if(!($user_id == $follow_user_id)) {
                    $sql = "select 'User Valid' from users where Id = :follow_user_id and screen_name = :screen_name";
                    $query = $db->prepare( $sql );
                    $query->execute( array(':follow_user_id'=>$follow_user_id, ':screen_name'=>$screen_name));
                    
                    if ($query->rowCount()){
                        $sql = "select 'Unfollow', Id as deleteRowId from users_followed where followed_user_Id = :follow_user_id and user_Id = :user_id";
                        $query = $db->prepare( $sql );
                        $query->execute( array(':follow_user_id'=>$follow_user_id, ':user_id'=>$user_id));
                        if (!$query->rowCount()){    
                            $sql = "insert into users_followed values(null,:user_Id,:follow_user_Id)";
                            $query = $db->prepare( $sql );
                            $query->execute( array( ':user_Id'=>$user_id, ':follow_user_Id'=>$follow_user_id,));
                            echo 'followed';
                        } else {
                            $deleteRow = $query->fetch();
                            $deleteRowId = $deleteRow['deleteRowId'];
                            $sql = "delete from users_followed where Id = :Id";
                            $query = $db->prepare( $sql );
                            $query->execute( array( ':Id'=>$deleteRowId ));
                            echo 'unfollowed';
                        }
                    } else {
                        echo 'User not exists.';
                    }
                } else {
                    echo 'Can\'t follow yourself.';
                }
                
            }
            elseif ($action === 'is-followed?') {
                $args = $_POST['args'];
                $follow_user_id = $args[0];
                $screen_name = $args[1];
                    
                if(!($user_id == $follow_user_id)) {
                    $sql = "select 'User Valid' from users where Id = :follow_user_id and screen_name = :screen_name";
                    $query = $db->prepare( $sql );
                    $query->execute( array(':follow_user_id'=>$follow_user_id, ':screen_name'=>$screen_name));
                    
                    if ($query->rowCount()){
                        $sql = "select 'Followed', Id as deleteRowId from users_followed where followed_user_Id = :follow_user_id and user_Id = :user_id";
                        $query = $db->prepare( $sql );
                        $query->execute( array(':follow_user_id'=>$follow_user_id, ':user_id'=>$user_id));
                        if ($query->rowCount()){    
                            echo 'followed';
                        } else {
                            echo 'unfollowed';
                        }
                    } else {
                        echo 'unfollowed.User not exists.';
                    }
                } else {
                    echo 'unfollowed.Can\'t follow yourself.';
                }
                
            }
            elseif ($action === 'newNotificationCount') {
                $sql = "select count(*) as newNotificationCount from user_words uw 
                    inner join users_followed uf on uw.user_Id = uf.followed_user_Id 
            		inner join users u on uf.user_Id = u.Id and uw.row_created_date >= u.last_seen_date
            		where u.Id = :user_Id";
                $query = $db->prepare( $sql );
                $query->execute( array( ':user_Id'=>$user_id));
                	
            	$result = $query->fetch();
            	echo $result['newNotificationCount'];
            }
            elseif ($action == 'updateLastSeenDate') {
                $sql = "update users set last_seen_date = now() where Id = :user_Id";
                $query = $db->prepare( $sql );
                $query->execute( array( ':user_Id'=>$user_id));
                	
            	echo $query->rowCount();
            }
            
    //         elseif ($action === 'add') {
                
    //             $sql = "delete from user_words where user_id = :user_id and user_word = :user_word";
    //             $query = $db->prepare( $sql );
    //             $query->execute( array( ':user_id'=>$user_id, ':user_word'=>$user_word));
    //             $deleted_rows = $query->rowCount();
    //             if ($deleted_rows == 0) {
    //                 $sql = "insert into user_words values (null, :user_id, :user_word)";
    //                 $query = $db->prepare( $sql );
    //                 $query->execute( array( ':user_id'=>$user_id, ':user_word'=>$user_word));
    //                 echo 'word_added';
    //             } else {
    //                 echo 'word_removed';    
    // 			}
    //         } elseif ($action === 'whoElseLiked') {
                
    //             $sql = "select u.screen_name as screen_name from user_words uw inner join users u on uw.user_id = u.Id where uw.user_word = :user_word";
    //             $query = $db->prepare( $sql );
    //             $query->execute( array( ':user_word'=>$user_word));
    //             $whoElseLiked_rows = $query;
    //             if ($whoElseLiked_rows->rowCount()) {
    //                 foreach ($whoElseLiked_rows as $row){
    // 					echo "<a href='./@" . $row['screen_name'] . "'>" . $row['screen_name'] . "</a> liked this word<br/>";
    // 				}
    //             } else {
    // 				echo 'none'; 
    //             }
    //         }
        }
    }
?>