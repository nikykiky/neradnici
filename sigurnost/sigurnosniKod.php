<?php
session_start();
/*
kada je ./
dnevnik_rada me vrati na 
/dnevnik/sigurnost/login.php
umjesto na 
../sigurnost/login.php
 */
//ako nista nije upisano tj prvi put dolazim polje je prazno
if((!isset($_SESSION['user'])) || (!isset($_SESSION['user_id'])) ) {
        header("location: ../sigurnost/login.php");
		exit;
}
?>
