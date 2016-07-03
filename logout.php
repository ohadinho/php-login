<?php
   session_start();
   session_destroy();
   
   // Clear rememberme cookie if exists
   if (isset($_COOKIE['rememberme'])) {	   
		unset($_COOKIE['rememberme']);
		setcookie("rememberme", "", time()-3600);
   }
   
   header("Location: home.php");
   die();
?>