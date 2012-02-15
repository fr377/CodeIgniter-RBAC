<?php

/**
  * Adding functionality for table rbac_privileges
  *
  **/

include_once 'config.php';
include_once CLASSES_DIR.'page/class.securePage_bv.php';

class rbac_privileges_add_actions extends securePage_bv
{

	Function rbac_privileges_add_actions($arrAttr)
	{
		$arrAttr['page_type'] = 'public_page';
		parent::securePage_bv($arrAttr);
		
		if (!$this->IsTarget() ||  !is_numeric($this->GetTargetData('id'))){
			if (!$this->SendBack()){
				$this->SendTo('index.php');
			}
		}
		
		$privilege_id = $this->GetTargetData('id');
		
		include_once FORM_DIR.'frm.rbac_privileges.php';
		
		$_frm->Assign('<!TITLE!>', '<h1>Add actions to existing privilege</h1>');
		$_frm->Assign('<DESCRIPTION>', '');
		$_frm->Assign('<DESCRIPTION_LBL>', '');
		
		// Get the privilege name
		
		$_sql = "SELECT name FROM rbac_privileges WHERE id = $privilege_id ";
		$_row = $this->mConn->GetRow($_sql);
		$privilege_name = $_row['name'];
		
		// Reset the NAME field
		$_frm->AssignInput('<NAME>', 'name', array('type'=>'text',  'size'=>'30', 'class'=>'inp', 'info'=>'', 'maxlength'=>50, 'readonly'=>'', 'value'=>$privilege_name ));
		$_frm->SetInputRules('name', array('table'=>'rbac_privileges', 'alphanum', 'maxlength'=>50));
		
		// INPUT ---------
		$_frm->AssignInput('<ACTIONS_NAME>', 'actions_name', array('type'=>'combo', 'ids'=>'', 'first'=>'- -', 
			'options'=>'SELECT name FROM rbac_actions WHERE id NOT IN (SELECT actions_id FROM rbac_privileges_has_actions WHERE privileges_id = '.$privilege_id.')',
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
