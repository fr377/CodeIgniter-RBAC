<?php

//--------------------------
// Include files
include_once 'config.php'; // Main configuration file for site.
include_once CLASSES_DIR.'page/class.securePage_bv.php';
include_once CLASSES_DIR.'tabulate/class.tabulate_bv.php';


class rbac_privileges_has_actions_view extends securePage_bv
{

	Function rbac_privileges_has_actions_view($arrAttr)
	{
		$arrAttr['page_type'] = 'public_page';
		
		parent::securePage_bv($arrAttr);
		
		$this->PageLogic();
	}
	
	Function PageLogic()
	{
		
		//--------------------------
		// Logic
		
		if ($this->Post('delete')){
			if (is_array($this->Post('id')) && count($this->Post('id')) > 0){ // Make sure we have an array that is not empty
				$_ids = preg_replace('/[^0-9,]/', '', implode(',', $this->Post('id')));
				
				$_sql = "DELETE FROM rbac_privileges_has_actions WHERE id IN ($_ids)";
				
				$this->mConn->Execute($_sql);
			}
		}
		
		$this->ProcessPage();
	}
	
	Function ProcessPage(){
		//--------------------------
		// Tabulate definition
		
		$arr_sql = array(
			'fields'=>array(// ID always first
				't1.id',
				't2.name as privilege',
				't3.name as action',
				'IF(t2.is_singular = 1, \'YES\', \'NO\') as is_singular'
				),
			'from'=>array('rbac_privileges_has_actions as t1'),
			'join'=>array(
				'INNER JOIN rbac_privileges as t2 ON t2.id = t1.privileges_id',
				'INNER JOIN rbac_actions as t3 ON t3.id = t1.actions_id'
				),
			'where'=>'',
			'order'=>' is_singular ASC, t2.name, t3.id',
			'group'=>'',
			'limit'=>''
		);
		
		$_db = $this->mConn;
		$_tabulate = new tabulate_bv($_db, $arr_sql);
		//$_tabulate->SetDebug();
		$_tabulate->SetMap( array(
				'id'=>NULL,
				'checkbox'=>''
				)
			);
		$_tabulate->SetTitle('privileges has actions'.':');
		// $_tabulate->Append('TITLE', '<div>Put some text here, if needed</div>'); // This is an example
		$_tabulate->SetRecordsPerPage(20);
		
		// $_tabulate->SetSelectionType('checkbox'); // can also be 'checkbox'
		
		// $_tabulate->SetButton('add', 'Add');
		$_tabulate->SetButton('delete', 'Delete');
		// $_tabulate->Append('BUTTON', '<div>Put some text underneath the buttons</div>'); // Example.
		
		
		// This is an example of post processing the data.
		$_arr = $_tabulate->GetData();
		
		$arr_temp = array();
		$old_privilege_name = '';
		$is_new_privilege_name = TRUE;
		
		foreach ($_arr as $row_id=>$_row){
			
			// add a new column
			if ($_row['is_singular'] === 'NO'){ // checkboxes only for non singular definitions
				$arr_temp[$row_id]['checkbox'] = '<INPUT type="checkbox" name="id[]" value="'.$_row['id'].'">';
			}
			else {
				$arr_temp[$row_id]['checkbox'] = '&nbsp;';
			}
			
			foreach ($_row as $_k=>$_v){
				
				if ($_k === 'privilege'){
					
					if ($old_privilege_name === $_v){
						$_v = '';
						$is_new_privilege_name = FALSE;
					} else {
						$old_privilege_name = $_v;
						$is_new_privilege_name = TRUE;
					}
				}
				
				if ($_k === 'is_singular' && !$is_new_privilege_name){
					$_v = '';
				}
				
				$arr_temp[$row_id][$_k] = $_v;
			}
		}
		
		$_tabulate->SetData($arr_temp);
		$_tabulate->mIsForm = TRUE;
		
		
		
		$this->Assign('<TITLE>', 'Tabulate Example');
		$this->Assign('<MAIN>', $_tabulate->PrintTabulate());
		$this->Assign('<HEAD>', '<link href="/classes/form/css/form.css" rel="stylesheet" type="text/css">');
		$this->Append('<HEAD>', $_tabulate->GetHead());
	}

}

include_once 'controller.php';

