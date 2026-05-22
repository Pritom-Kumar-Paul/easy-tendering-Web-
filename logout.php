<?php

	session_start();
	session_destroy();
	if(isset($_COOKIE['email'])) {
    setcookie("email", "", time() - 3600);
}
	
	header("location: login.php");

?>