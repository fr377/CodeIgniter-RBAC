<?php

/**
  * Adding functionality for table rbac_objects
  *
  **/

include_once 'config.php';
include_once CLASSES_DIR.'page/class.securePage_bv.php';

class rbac_objects_add extends securePage_bv
{

	Function rbac_objects_add($arrAttr)
	{
		$arrAttr['page_type'] = 'public_page';
		parent::securePage_bv($arrAttr);
		
		include_once FORM_DIR.'frm.rbac_objects.php';
		
		if ($_frm->Validate($this->Post())){
			
			$_db = $this->mConn;
			
			include_once CLASSES_DIR.'rbac/class.rbacAdmin_bv.php';
			
			rbacAdmin_bv::SetDomain($_frm->Get('name'), $_frm->Get('description'), '' , $_db);
			
			$this->SendBack();
		}
		
		$this->Assign('<TITLE>', 'Add new rbac_objects');
		$this->Assign('<MAIN>', $_frm->PrintForm());
		$this->Assign('<HEAD>', '<link href="/classes/form/css/form.css" rel="stylesheet" type="text/css">');
		$this->Append('<HEAD>', $_frm->GetHead());
	}
}

include_once 'controller.php';
?>
