<?php
/**
  * Editing functionality for table rbac_objects
  *
  **/


include_once 'config.php';
include_once CLASSES_DIR.'page/class.securePage_bv.php';

class rbac_objects_edit extends securePage_bv
{

	Function rbac_objects_edit($arrAttr)
	{
		$arrAttr['page_type'] = 'public_page';
		parent::securePage_bv($arrAttr);
		
		if (!$this->IsTarget()||  !is_numeric($this->GetTargetData('id'))){
			$this->SendBack();
		}
		
		include_once FORM_DIR.'frm.rbac_objects.php';
		
		$_db = $this->mConn;
		$_db->SetTable('rbac_objects');
		
		if ($_frm->Validate($this->Post())){
			
			$_db->StoreData($_frm->GetDbData('rbac_objects'));
			$object_id = $this->GetTargetData('id');
			
			if ($_db->Update('id', $object_id)){
				
				// We now want to update the domains table
				// First we need to find the corresponding domain id
				
				$_sql ="
					SELECT t1.id FROM rbac_domains as t1
					INNER JOIN rbac_domains_has_objects as t2 ON t2.domains_id = t1.id
					WHERE is_singular = 1 AND objects_id = $object_id
				";
				
				$_row = $_db->GetRow($_sql);
				
				$_db->SetTable('rbac_domains');
				$_db->AddData('name', $_frm->get('name'));
				$_db->AddData('description', $_frm->get('description'));
				$_db->Update('id', $_row['id']);
				
			}
			
			$this->SendBack();
		}
		
		$_frm->SetInputAttribute('value', $_db->Fetch('id', $this->GetTargetData('id')) );
		
		$_frm->AssignInput('<!BUTTON!>','edit', array('value'=>'Update', 'type'=>'submit'));
		$_frm->AppendInput('<!BUTTON!>','id', array('value'=>$this->GetTargetData('id'), 'type'=>'hidden'));
		
		
		$this->Assign('<TITLE>', 'Update rbac_objects');
		$this->Assign('<MAIN>', $_frm->PrintForm());
		$this->Assign('<HEAD>', '<link href="/classes/form/css/form.css" rel="stylesheet" type="text/css">');
		$this->Append('<HEAD>', $_frm->GetHead());
	}
}

include_once 'controller.php';

?>
