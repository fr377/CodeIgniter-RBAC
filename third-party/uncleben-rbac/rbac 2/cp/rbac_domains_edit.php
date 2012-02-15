<?php
/**
  * Editing functionality for table rbac_domains
  *
  **/


include_once 'config.php';
include_once CLASSES_DIR.'page/class.securePage_bv.php';

class rbac_domains_edit extends securePage_bv
{

	Function rbac_domains_edit($arrAttr)
	{
		$arrAttr['page_type'] = 'public_page';
		parent::securePage_bv($arrAttr);
		
		if (!$this->IsTarget()||  !is_numeric($this->GetTargetData('id'))){
			$this->SendBack();
		}
		
		include_once FORM_DIR.'frm.rbac_domains.php';
		
		$_db = $this->mConn;
		$_db->SetTable('rbac_domains');
		
		if ($_frm->Validate($this->Post())){
			
			$_db->StoreData($_frm->GetDbData('rbac_domains'));
			
			$domain_id = $this->GetTargetData('id');
			
			if ($_db->Update('id', $domain_id)){
				
				// We now want to update the objects table
				// First we need to find the corresponding object id
				
				$_sql ="
					SELECT t1.id, t3.is_singular FROM rbac_objects as t1
					INNER JOIN rbac_domains_has_objects as t2 ON t2.objects_id = t1.id
					INNER JOIN rbac_domains as t3 ON t3.id = t2.domains_id
					WHERE  domains_id = $domain_id
				";
				
				$_row = $_db->GetRow($_sql);
				
				if ($_row['is_singular']){
					$_db->SetTable('rbac_objects');
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
		
		
		$this->Assign('<TITLE>', 'Update rbac_domains');
		$this->Assign('<MAIN>', $_frm->PrintForm());
		$this->Assign('<HEAD>', '<link href="/classes/form/css/form.css" rel="stylesheet" type="text/css">');
		$this->Append('<HEAD>', $_frm->GetHead());
	}
}

include_once 'controller.php';

?>
