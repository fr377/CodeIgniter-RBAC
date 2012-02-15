<?php

include_once 'class.page_bv.php';


class TemplatePage_bv extends page_bv
{
	
	var $mTpl;
	
	Function TemplatePage_bv($templateFile)
	{
		parent::Page_bv();
		
		include_once CLASSES_DIR.'template/class.template_bv.php';
		
		$this->mTpl = & new template_bv( $templateFile);
	}
	
	function PrintPage()
	{
		parent::PrintPage($this->mTpl->PrintTemplate());
	}
}
?>
