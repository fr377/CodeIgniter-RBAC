<?php

/**
  * Adding functionality for table users
  *
  **/

include_once 'config.php';
include_once CLASSES_DIR.'page/class.securePage_bv.php';

class users_add extends securePage_bv
{

	Function users_add($arrAttr)
	{
		$arrAttr['page_type'] = 'public_page';
		parent::securePage_bv($arrAttr);
		
		include_once FORM_DIR.'frm.users.php';
		
		if ($_frm->Validate($this->Post())){
			
			$_db = $this->mConn;
			$_db->SetTable('users');
			
			$_db->StoreData($_frm->GetDbData('users'));
			
			
			
			$_db->Insert();
			
			//$_id = $_db->GetId();
			
			$this->SendBack();
		}
		
		$this->Assign('<TITLE>', 'Add new users');
		$this->Assign('<MAIN>', $_frm->PrintForm());
		$this->Assign('<HEAD>', '<link href="/classes/form/css/form.css" rel="stylesheet" type="text/css">');
		$this->Append('<HEAD>', $_frm->GetHead());
	}
}

include_once 'controller.php';
?>
