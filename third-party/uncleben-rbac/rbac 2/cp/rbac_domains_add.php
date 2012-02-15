<?php

/**
  * Adding functionality for table rbac_domains
  *
  **/

include_once 'config.php';
include_once CLASSES_DIR.'page/class.securePage_bv.php';

class rbac_domains_add extends securePage_bv
{

	Function rbac_domains_add($arrAttr)
	{
		$arrAttr['page_type'] = 'public_page';
		parent::securePage_bv($arrAttr);
		
		include_once FORM_DIR.'frm.rbac_domains.php';
		
		// INPUT ---------
		$_frm->AssignInput('<OBJECTS_NAME>', 'objects_name', array('type'=>'combo', 'ids'=>'', 'first'=>'- -', 'options'=>'SELECT name FROM rbac_objects',
		'class'=>'inp', 'info'=>'', 'maxlength'=>10, 'alt'=>'Object', 'multiple'=>'', 'size'=>'5', 'name'=>'objects_name[]' ));
		$_frm->SetInputRules('objects_name', array('table'=>'rbac_domains', 'required'));
		
		if ($_frm->Validate($this->Post())){
			
			$_db = $this->mConn;
			
			include_once CLASSES_DIR.'rbac/class.rbacAdmin_bv.php';
			
			// print_r($_frm->Get());
			
			$arr_actions = array();
			
			foreach($_frm->Get('objects_name') as $action){
				$arr_actions[$action] = '';
			}
			
			rbacAdmin_bv::SetDomain($_frm->Get('name'), $_frm->Get('description'), $arr_actions , $_db);
			
			$this->SendBack();
		}
		
		$this->Assign('<TITLE>', 'Add new rbac_domains');
		$this->Assign('<MAIN>', $_frm->PrintForm());
		$this->Assign('<HEAD>', '<link href="/classes/form/css/form.css" rel="stylesheet" type="text/css">');
		$this->Append('<HEAD>', $_frm->GetHead());
	}
}

include_once 'controller.php';
?>
