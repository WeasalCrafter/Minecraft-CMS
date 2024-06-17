<?php

session_start();


//if(isset($_SESSION['loggedin']){
//	unset($_SESSION['loggedin'];
//}

$_SESSION = array();



session_destroy();

header("location: index.php");
exit;

?>

