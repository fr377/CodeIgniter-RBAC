<?php

/**
  * Adding functionality for table rbac_domains_has_objects
  *
  **/

include_once 'config.php';
include_once CLASSES_DIR.'page/class.securePage_bv.php';

class rbac_domains_has_objects_add extends securePage_bv
{

	Function rbac_domains_has_objects_add($arrAttr)
	{
		$arrAttr['page_type'] = 'public_page';
		parent::securePage_bv($arrAttr);
		
		include_once FORM_DIR.'frm.rbac_domains_has_objects.php';
		
		if ($_frm->Validate($this->Post())){
			
			$_db = $this->mConn;
			$_db->SetTable('rbac_domains_has_objects');
			
			$_db->StoreData($_frm->GetDbData('rbac_domains_has_objects'));
			
			
			
			$_db->Insert();
			
			//$_id = $_db->GetId();
			
			$this->SendBack();
		}
		
		$this->Assign('<TITLE>', 'Add new rbac_domains_has_objects');
		$this->Assign('<MAIN>', $_frm->PrintForm());
		$this->Assign('<HEAD>', '<link href="/classes/form/css/form.css" rel="stylesheet" type="text/css">');
		$this->Append('<HEAD>', $_frm->GetHead());
	}
}

include_once 'controller.php';
?>
