<?php

session_start();
include_once 'config.php';

include_once CLASSES_DIR.'rbac/class.rbacAdmin_bv.php';

$_db = & new db_bv();

//-------------------
// Setup privileges
RbacAdmin_bv::SetPrivilege('all_actions', 'Administration privileges', array('view'=>'', 'add'=>'', 'edit'=>'', 'delete'=>'', 'approve'=>'', 'review'=>'', 'disable'=>''), $_db);

RbacAdmin_bv::SetPrivilege('moderate', 'Moderation privileges', array('view'=>'', 'approve'=>'', 'review'=>'', 'disable'=>''), $_db);


//-------------------
// Setup Domains
RbacAdmin_bv::SetDomain('all_objects', '', array('admin_page'=>'', 'member_page'=>'', 'public_page'=>''), $_db);

RbacAdmin_bv::SetDomain('member_pages', '', array('member_page'=>'', 'public_page'=>''), $_db);


//--------------------
// Set up roles

// set public role
RbacAdmin_bv::SetRole('public',  0, array( array('view', 'public_page', 1)), $_db);

// set administrator role
RbacAdmin_bv::SetRole('administrator',  100, array( array('all_actions', 'all_objects', 1)), $_db);

// set moderator role
RbacAdmin_bv::SetRole('moderator',  50, array( array('moderate', 'all_objects', 1)), $_db);

// set member role
RbacAdmin_bv::SetRole('member',  1, array( array('view', 'member_pages', 1)), $_db);


//---------------------
// Set anonymous user
RbacAdmin_bv::SetUserRole(0, 'public', $_db);

//---------------------
// insert some test users in the users database.

$_db->SetTable('users');
$_db->AddData('username', 'admin');
$_db->AddData('pswd', md5('admin'));
$_db->Insert();

RbacAdmin_bv::SetUserRole($_db->GetId(), 'administrator', $_db);

$_db->SetTable('users');
$_db->AddData('username', 'user');
$_db->AddData('pswd', md5('password'));
$_db->Insert();

RbacAdmin_bv::SetUserRole($_db->GetId(), 'member', $_db);

$_db->SetTable('users');
$_db->AddData('username', 'super_user');
$_db->AddData('pswd', md5('password'));
$_db->Insert();

RbacAdmin_bv::SetUserRole($_db->GetId(), 'moderator', $_db);
