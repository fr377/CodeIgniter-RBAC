<?php

include_once 'config.php';
include_once 'class.securePage_bv.php';

class ExamplePage extends SecurePage_bv
{
	
	// Constructor
	Function ExamplePage($arrAttr = array())
	{
		parent::SecurePage_bv($arrAttr);
		
		$this->ProcessPage();
	}
	
	Function ProcessPage()
	{
		$this->Assign('<RIGHT>', $this->GetLoginFormHtml());
		
		$this->Assign('<TITLE>', 'Example');
		
		$this->Assign('<FOOTER>', 'Footer');
		$this->Assign('<LEFT>', 'Left');
		$this->Append('<CENTER>', 'Center');
		//print_r($_SESSION);
	}
}

/*
$arr_attr = array('page_type'=>'public_page', 'page_template'=>'../template/main.tpl');
$_page = & new ExamplePage($arr_attr);
$_page->PrintPage();
*/
include_once 'controller.php';
?>
