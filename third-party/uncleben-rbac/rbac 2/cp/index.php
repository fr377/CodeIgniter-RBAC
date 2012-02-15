<?php
include_once 'config.php';
include_once CLASSES_DIR.'page/class.page_bv.php';


$_page = & new page_bv(array('page_template'=>'templates/main.tpl'));

$_page->mTpl->Assign('<TITLE>', 'Make files');
$_page->mTpl->Assign('<HEADER>', 'This will be a header');

$arr_lines = file('links.php');

$_page->Assign('<LEFT>', '<h3>Tables:</h3>' . implode('', $arr_lines));

$_page->PrintPage();

?>
