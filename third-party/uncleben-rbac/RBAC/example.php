<?php
/*
* This file is simple example on how to use the RBAC system
* You must first run the setup.php file before running this file.
*/

session_start();
include_once 'config.php';

include_once CLASSES_DIR.'class.rbacAdmin_bv.php';
include_once CLASSES_DIR.'class.rbac_bv.php';

$_db = & new db_bv();
$rbac_admin = & new RbacAdmin_bv($_db);
$_rbac = & new Rbac_bv($_db);

//----------------------
// Create a user
$_db->SetTable('users');
$_db->AddData('username', 'test1');
$_db->Insert();

$user_id = $_db->GetId();


// Assign a role to the new user
$rbac_admin->SetUserRole($user_id, 'member'); // The user is just a member

$rbac_admin->PrintLog();

// Check wether the user is allowed to view admin pages
if ($_rbac->IsAllowedTo($user_id, 'view', 'admin_page')){
	echo "User, $user_id is ALLOWED to view admin_page<br>";
}
else {
	echo "User, $user_id is NOT ALLOWED to view admin_page<br>";
}

//--------------------------------
// The user is not allowed to view the admin page so we can create a new role that will allow people to view the admin page only.
$rbac_admin->SetRole('view_admin_page',  2, array( array('view', 'admin_page', 1)));

$rbac_admin->SetUserRole($user_id, 'view_admin_page');
$rbac_admin->PrintLog();

// Now check wether the user test1 can view admin pages
if ($_rbac->IsAllowedTo($user_id, 'view', 'admin_page')){
	echo "User, $user_id is ALLOWED to view admin_page<br>";
}
else {
	echo "User, $user_id is NOT ALLOWED to view admin_page<br>";
}

// Check wether the user test1 can edit admin pages
if ($_rbac->IsAllowedTo($user_id, 'edit', 'admin_page')){
	echo "User, $user_id is ALLOWED to edit admin_page<br>";
}
else {
	echo "User, $user_id is NOT ALLOWED to edit admin_page<br>";
}

// Make the user an administrator
$rbac_admin->SetUserRole($user_id, 'administrator');
$rbac_admin->PrintLog();

// Now check wether the user test1 can edit admin pages
if ($_rbac->IsAllowedTo($user_id, 'edit', 'admin_page')){
	echo "User, $user_id is ALLOWED to edit admin_page<br>";
}
else {
	echo "User, $user_id is NOT ALLOWED to edit admin_page<br>";
}

// Create a role that denies the privilege to add an admin_page
$rbac_admin->SetRole('add_admin_page_no',  100, array( array('add', 'admin_page', 0))); // importance of 100. Same importance as administrator role
$rbac_admin->SetUserRole($user_id, 'add_admin_page_no');
$rbac_admin->PrintLog();

// Example of loading all privileges for a user at once.
$_rbac->LoadAllUserPrivileges($user_id);

// You can see all the data in the session
print_r($_SESSION['rbac_bv']);

// Delete the user 
$_db->SetTable('users');
$_db->Delete('username', 'test1');

// IMPORTANT: Should also delete the corresponding entries in the users_has_roles table


