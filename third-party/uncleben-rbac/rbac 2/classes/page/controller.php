<?php

$arr_attr = array('page_type'=>'public_page', 'page_template'=>'../template/main.tpl');
$_page = & new ExamplePage($arr_attr);
$_page->PrintPage();
