<?php

session_start();
include_once 'config.php';

include_once CLASSES_DIR.'class.rbacAdmin_bv.php';

$_db = & new db_bv();
$rbac_admin = & new RbacAdmin_bv($_db);


$rbac_admin->SetPrivilege('all_actions', 'Administration privileges', array('view'=>'', 'add'=>'', 'edit'=>'', 'delete'=>'', 'approve'=>'', 'review'=>'', 'disable'=>''));

$rbac_admin->SetPrivilege('moderate', 'Moderation privileges', array('view'=>'', 'approve'=>'', 'review'=>'', 'disable'=>''));

$rbac_admin->SetDomain('all_objects', '', array('admin_page'=>'', 'member_page'=>'', 'public_page'=>''));

$rbac_admin->SetDomain('member_pages', '', array('member_page'=>'', 'public_page'=>''));


//--------------------
// Set up roles

// set public role
$rbac_admin->SetRole('public',  0, array( array('view', 'public_page', 1)));

// set administrator role
$rbac_admin->SetRole('administrator',  100, array( array('all_actions', 'all_objects', 1)));

// set moderator role
$rbac_admin->SetRole('moderator',  50, array( array('moderate', 'all_objects', 1)));

// set member role
$rbac_admin->SetRole('member', 1,  array( array('view', 'member_pages', 1)));


//---------------------
// Set anonymous user
$rbac_admin->SetUserRole(0, 'public');

$rbac_admin->PrintLog();

