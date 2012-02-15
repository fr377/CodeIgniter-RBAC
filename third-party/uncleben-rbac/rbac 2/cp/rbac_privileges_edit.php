<?php
/**
  * Editing functionality for table rbac_privileges
  *
  **/


include_once 'config.php';
include_once CLASSES_DIR.'page/class.securePage_bv.php';

class rbac_privileges_edit extends securePage_bv
{

	Function rbac_privileges_edit($arrAttr)
	{
		$arrAttr['page_type'] = 'public_page';
		parent::securePage_bv($arrAttr);
		
		if (!$this->IsTarget()||  !is_numeric($this->GetTargetData('id'))){
			$this->SendBack();
		}
		
		include_once FORM_DIR.'frm.rbac_privileges.php';
		
		$_db = $this->mConn;
		$_db->SetTable('rbac_privileges');
		
		if ($_frm->Validate($this->Post())){
			
			$_db->StoreData($_frm->GetDbData('rbac_privileges'));
			
			$privilege_id = $this->GetTargetData('id');
			
			if ($_db->Update('id', $privilege_id)){
				
				// We now want to update the actions table
				// First we need to find the corresponding action id
				
				$_sql ="
					SELECT t1.id, t3.is_singular FROM rbac_actions as t1
					INNER JOIN rbac_privileges_has_actions as t2 ON t2.actions_id = t1.id
					INNER JOIN rbac_privileges as t3 ON t3.id = t2.privileges_id
					WHERE  privileges_id = $privilege_id
				";
				
				$_row = $_db->GetRow($_sql);
				
				if ($_row['is_singular']){
					$_db->SetTable('rbac_actions');
					$_db->AddData('name', $_frm->get('name'));
					$_db->AddData('description', $_frm->get('description'));
					$_db->Update('id', $_row['id']);
				}
				
			}
			
			$this->SendBack();
		}
		
		$_frm->SetInputAttribute('value', $_db->Fetch('id', $this->GetTargetData('id')) );
		
		$_frm->AssignInput('<!BUTTON!>','edit', array('value'=>'Update', 'type'=>'submit'));
		$_frm->AppendInput('<!BUTTON!>','id', array('value'=>$this->GetTargetData('id'), 'type'=>'hidden'));
		
		
		$this->Assign('<TITLE>', 'Update rbac_privileges');
		$this->Assign('<MAIN>', $_frm->PrintForm());
		$this->Assign('<HEAD>', '<link href="/classes/form/css/form.css" rel="stylesheet" type="text/css">');
		$this->Append('<HEAD>', $_frm->GetHead());
	}
}

include_once 'controller.php';

?>
