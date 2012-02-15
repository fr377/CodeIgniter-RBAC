<?php

$script_name = $_SERVER['SCRIPT_NAME'];

$script_name_base = basename($script_name);
$class_name = preg_replace('/\.php/', '', $script_name_base);

if (file_exists($script_name_base)){
	
	$arr_attr = array('page_template'=>'templates/main.tpl');
	
	$_page = & new $class_name($arr_attr);
	$_page->Assign('<TITLEBAR>', ' - Currently viewing '.$script_name_base);
	
	$arr_lines = file('links.php');
	$_page->Assign('<LEFT>', '<h3>Tables:</h3>' . implode('', $arr_lines));
	
	$_page->PrintPage();
}
