<?php
    try {
        $db = new PDO('mysql:host=localhost;dbname=wordo;charset=utf8', 'root', 'gIZCtmp4F0', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    } catch (Exception $e) {
        echo 'Can\'t Connect to Database at this time: ',  $e->getMessage(), "\n";
        header('Location: ../');
    }
?>