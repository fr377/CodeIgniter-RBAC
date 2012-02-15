<?php

session_start();

unset($_SESSION);
include_once 'config.php';

include_once CLASSES_DIR.'rbac/class.rbac_bv.php';
include_once CLASSES_DIR.'rbac/class.rbacAdmin_bv.php';

$_db = & new db_bv();

$user_id = 1; // admin
$action = 'view';
$object = 'admin_page';

if (rbac_bv::IsAllowedTo($user_id, $action, $object, $_db)){
	echo "User id $user_id is allowed to <b>$action</b> the object <b>$object</b><br>";
}
else {
	echo "User id $user_id is <u>NOT</u> allowed to <b>$action</b> the object <b>$object</b><br>";
}

//----------------------
// Another test
$user_id = 2; // user
$action = 'view';
$object = 'admin_page';

if (rbac_bv::IsAllowedTo($user_id, $action, $object, $_db)){
	echo "User id $user_id is allowed to <b>$action</b> the object <b>$object</b><br>";
}
else {
	echo "User id $user_id is <u>NOT</u> allowed to <b>$action</b> the object <b>$object</b><br>";
}

