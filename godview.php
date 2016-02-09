<?php 
  require_once('./includes/db.php');
  require_once('./includes/session.php');
  
?>
<html>
	<head>
		<title>Wordo God View</title>
		<style>
			body {
				font-family: 'Roboto', Helvetica;
				margin: 100 auto;
				width: 100%;
			}
			
			table {
				border-collapse: collapse;
				width: 200px;
			}
			
			 td {
				border: 1px dashed gray;
				padding: 25px;
				text-align: center;
			}
		</style>
	</head>
	<body>
		
		<center>
		
		<h2>Total Statistics</h2><br><br>
<table>
    <tr>
        <td colspan="2">total statistics</td>
    </tr>
    <?php 
      $sql = "select count(*) as users_count from users;";
      $query = $db->prepare( $sql );
      $query->execute();
      $result = $query->fetch();
    ?>
    
    <tr>
        <td>users</td>
        <td><?php echo $result['users_count']; ?></td>
    </tr>
    <?php 
      $sql = "select count(*) as likes_count from user_words;";
      $query = $db->prepare( $sql );
      $query->execute();
      $result = $query->fetch();
    ?>
    <tr>
        <td>likes</td>
        <td><?php echo $result['likes_count']; ?></td>
    </tr>
</table>


<h2>All users:</h2><br><br><br>

<table>

  <tr>
    <th>#</th>
    <th>Username</th>
    <th>Followers</th>
    <th>Email</th>
  </tr>
  <?php 
      $sql = "select u.Id, u.screen_name, (select count(*) from users_followed uf where uf.followed_user_id = u.Id) as followers, u.email from users u order by u.Id;";
      $query = $db->prepare( $sql );
      $query->execute();
      $result = $query->fetchAll();
      
      foreach($result as $row) {
  ?>
  <tr>
    <td><?php echo $row['Id']; ?></td>
    <td><?php echo $row['screen_name']; ?></td>
    <td><?php echo $row['followers']; ?></td>
    <td><?php echo $row['email']; ?></td>
  </tr>
  <?php } ?>
</table>
		
		
		</center>
	</body>
</html>