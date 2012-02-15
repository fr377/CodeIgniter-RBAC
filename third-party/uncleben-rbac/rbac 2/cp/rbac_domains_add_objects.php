<?php

/**
  * Adding functionality for table rbac_domains
  *
  **/

include_once 'config.php';
include_once CLASSES_DIR.'page/class.securePage_bv.php';

class rbac_domains_add_objects extends securePage_bv
{

	Function rbac_domains_add_objects($arrAttr)
	{
		$arrAttr['page_type'] = 'public_page';
		parent::securePage_bv($arrAttr);
		
		if (!$this->IsTarget()||  !is_numeric($this->GetTargetData('id'))){
			if (!$this->SendBack()){
				$this->SendTo('index.php');
			}
		}
		
		$domain_id = $this->GetTargetData('id');
		
		include_once FORM_DIR.'frm.rbac_domains.php';
		
		$_frm->Assign('<!TITLE!>', '<h1>Add objects to existing domain</h1>');
		$_frm->Assign('<DESCRIPTION>', '');
		$_frm->Assign('<DESCRIPTION_LBL>', '');
		
		// Get the domain name
		
		$_sql = "SELECT name FROM rbac_domains WHERE id = $domain_id ";
		$_row = $this->mConn->GetRow($_sql);
		$domain_name = $_row['name'];
		
		// Reset the NAME field
		$_frm->AssignInput('<NAME>', 'name', array('type'=>'text',  'size'=>'30', 'class'=>'inp', 'info'=>'', 'maxlength'=>50, 'readonly'=>'', 'value'=>$domain_name ));
		$_frm->SetInputRules('name', array('table'=>'rbac_domains', 'alphanum', 'maxlength'=>50));
		
		// INPUT ---------
		$_frm->AssignInput('<OBJECTS_NAME>', 'objects_name', array('type'=>'combo', 'ids'=>'', 'first'=>'- -', 
			'options'=>'SELECT name FROM rbac_objects WHERE id NOT IN (SELECT objects_id FROM rbac_domains_has_objects WHERE domains_id = '.$domain_id.')',
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
