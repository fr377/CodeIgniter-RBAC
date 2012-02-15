<?php
/**
  * Editing functionality for table users
  *
  **/


include_once 'config.php';
include_once CLASSES_DIR.'page/class.securePage_bv.php';

class users_edit extends securePage_bv
{

	Function users_edit($arrAttr)
	{
		$arrAttr['page_type'] = 'public_page';
		parent::securePage_bv($arrAttr);
		
		if (!$this->IsTarget()||  !is_numeric($this->GetTargetData('id'))){
			$this->SendBack();
		}
		
		include_once FORM_DIR.'frm.users.php';
		
		$_db = $this->mConn;
		$_db->SetTable('users');
		
		if ($_frm->Validate($this->Post())){
			
			$_db->StoreData($_frm->GetDbData('users'));
			
			
			
			$_db->Update('id', $this->GetTargetData('id'));
			
			$this->SendBack();
		}
		
		$_frm->SetInputAttribute('value', $_db->Fetch('id', $this->GetTargetData('id')) );
		
		$_frm->AssignInput('<!BUTTON!>','edit', array('value'=>'Update', 'type'=>'submit'));
		$_frm->AppendInput('<!BUTTON!>','id', array('value'=>$this->GetTargetData('id'), 'type'=>'hidden'));
		
		
		$this->Assign('<TITLE>', 'Update users');
		$this->Assign('<MAIN>', $_frm->PrintForm());
		$this->Assign('<HEAD>', '<link href="/classes/form/css/form.css" rel="stylesheet" type="text/css">');
		$this->Append('<HEAD>', $_frm->GetHead());
	}
}

include_once 'controller.php';

?>
