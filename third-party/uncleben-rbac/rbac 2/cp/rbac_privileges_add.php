<?php

/**
  * Adding functionality for table rbac_privileges
  *
  **/

include_once 'config.php';
include_once CLASSES_DIR.'page/class.securePage_bv.php';

class rbac_privileges_add extends securePage_bv
{

	Function rbac_privileges_add($arrAttr)
	{
		$arrAttr['page_type'] = 'public_page';
		parent::securePage_bv($arrAttr);
		
		include_once FORM_DIR.'frm.rbac_privileges.php';
		
		// INPUT ---------
		$_frm->AssignInput('<ACTIONS_NAME>', 'actions_name', array('type'=>'combo', 'ids'=>'', 'first'=>'- -', 'options'=>'SELECT name FROM rbac_actions',
		'class'=>'inp', 'info'=>'', 'maxlength'=>10, 'alt'=>'Action', 'multiple'=>'', 'size'=>'5', 'name'=>'actions_name[]' ));
		$_frm->SetInputRules('actions_name', array('table'=>'rbac_privileges', 'required'));
		
		if ($_frm->Validate($this->Post())){
			
			$_db = $this->mConn;
			
			include_once CLASSES_DIR.'rbac/class.rbacAdmin_bv.php';
			
			// print_r($_frm->Get());
			
			$arr_actions = array();
			
			foreach($_frm->Get('actions_name') as $action){
				$arr_actions[$action] = '';
			}
			
			rbacAdmin_bv::SetPrivilege($_frm->Get('name'), $_frm->Get('description'), $arr_actions , $_db);
			
			$this->SendBack();
		}
		
		$this->Assign('<TITLE>', 'Add new rbac_privileges');
		$this->Assign('<MAIN>', $_frm->PrintForm());
		$this->Assign('<HEAD>', '<link href="/classes/form/css/form.css" rel="stylesheet" type="text/css">');
		$this->Append('<HEAD>', $_frm->GetHead());
	}
}

include_once 'controller.php';
?>
