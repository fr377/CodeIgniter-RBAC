<?php
$_host = 'localhost';
$_user = 'root';
$_pswd = '';

if (isset($_SESSION['db_name'])){ // from RWAD
	$db_name = $_SESSION['db_name'];
}
else{
	$db_name = 'spellbee';
}

?>
