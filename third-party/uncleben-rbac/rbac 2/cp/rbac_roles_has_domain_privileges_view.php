<?php

//--------------------------
// Include files
include_once 'config.php'; // Main configuration file for site.
include_once CLASSES_DIR.'page/class.securePage_bv.php';
include_once CLASSES_DIR.'tabulate/class.tabulate_bv.php';


class rbac_roles_has_domain_privileges_view extends securePage_bv
{

	Function rbac_roles_has_domain_privileges_view($arrAttr)
	{
		$arrAttr['page_type'] = 'public_page';
		
		parent::securePage_bv($arrAttr);
		
		$this->PageLogic();
	}
	
	Function PageLogic()
	{
		
		//--------------------------
		// Logic
		
		if (preg_match('/\d+/', $this->Post('id'))){
			$_id = $this->Post('id');
		}
		
		if ($this->Post('add')){
			$this->SendTo('rbac_roles_has_domain_privileges_add.php');
		}
		else if ($this->Post('edit')){
			$this->SendTo('rbac_roles_has_domain_privileges_edit.php', array('id'=>$_id));
		}
		
		$this->ProcessPage();
	}
	
	Function ProcessPage(){
		//--------------------------
		// Tabulate definition
		
		$arr_sql = array(
			'fields'=>array(// ID always first
				't1.id',
				't2.name as role',
				't3.name as privilege',
				't4.name as domain',
				'IF(is_allowed = 1, \'YES\', \'NO\') as is_allowed',
				'importance'
				),
			'from'=>array('rbac_roles_has_domain_privileges as t1'),
			'join'=>array(
				'INNER JOIN rbac_roles as t2 ON t2.id = t1.roles_id ',
				'INNER JOIN rbac_privileges as t3 ON t3.id = t1.privileges_id',
				'INNER JOIN rbac_domains as t4 ON t4.id = t1.domains_id'
				),
			'where'=>'',
			'order'=>'importance DESC, t3.is_singular ASC, t4.is_singular ASC, t1.is_allowed DESC',
			'group'=>'',
			'limit'=>''
		);
		
		$_db = $this->mConn;
		$_tabulate = new tabulate_bv($_db, $arr_sql);
		//$_tabulate->SetDebug();
		$_tabulate->SetMap( array(
				'id'=>NULL
				)
			);
		$_tabulate->SetTitle('Role Permissions:');
		// $_tabulate->Append('TITLE', '<div>Put some text here, if needed</div>'); // This is an example
		$_tabulate->SetRecordsPerPage(20);
		
		$_tabulate->SetSelectionType('radio'); // can also be 'checkbox'
		
		$_tabulate->SetButton('add', 'Add');
		$_tabulate->SetButton('edit', 'View / Edit');
		// $_tabulate->Append('BUTTON', '<div>Put some text underneath the buttons</div>'); // Example.
		
		
		// This is an example of post processing the data.
		$_arr = $_tabulate->GetData();
		
		$arr_temp = array();
		$old_role_name = '';
		$is_new_role_name = TRUE;
		
		foreach ($_arr as $row_id=>$_row){
			foreach ($_row as $_k=>$_v){
				if ($_k === 'role'){
					
					if ($old_role_name === $_v){
						$_v = '';
						$is_new_role_name = FALSE;
					} else {
						$old_role_name = $_v;
						$is_new_role_name = TRUE;
					}
				}
				
				if ($_k === 'importance' && !$is_new_role_name){
					$_v = '';
				}
				
				$arr_temp[$row_id][$_k] = $_v;
			}
		}
		
		$_tabulate->SetData($arr_temp);
		
		
		
		$this->Assign('<TITLE>', 'Tabulate Example');
		$this->Assign('<MAIN>', $_tabulate->PrintTabulate());
		$this->Assign('<HEAD>', '<link href="/classes/form/css/form.css" rel="stylesheet" type="text/css">');
		$this->Append('<HEAD>', $_tabulate->GetHead());
	}

}

include_once 'controller.php';

