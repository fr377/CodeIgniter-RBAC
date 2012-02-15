<?php

//--------------------------
// Include files
include_once 'config.php'; // Main configuration file for site.
include_once CLASSES_DIR.'page/class.securePage_bv.php';
include_once CLASSES_DIR.'tabulate/class.tabulate_bv.php';


class rbac_objects_view extends securePage_bv
{

	Function rbac_objects_view($arrAttr)
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
			$this->SendTo('rbac_objects_add.php');
		}
		else if ($this->Post('edit') && isset($_id)){
			$this->SendTo('rbac_objects_edit.php', array('id'=>$_id));
		}
		else if ($this->Post('delete') && isset($_id)){
			include_once CLASSES_DIR.'rbac/class.rbacAdmin_bv.php';
			
			$_sql = "SELECT name FROM rbac_objects WHERE id = $_id";
			$_row = $this->mConn->GetRow($_sql);
			
			rbacAdmin_bv::DeleteObject($_row['name'], $this->mConn);
		}
		
		$this->ProcessPage();
	}
	
	Function ProcessPage(){
		//--------------------------
		// Tabulate definition
		
		$arr_sql = array(
			'fields'=>array(// ID always first
				'id',
				'name',
				'description'
				),
			'from'=>array('rbac_objects'),
			'join'=>array(),
			'where'=>'',
			'order'=>'',
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
		$_tabulate->SetTitle('objects'.':');
		// $_tabulate->Append('TITLE', '<div>Put some text here, if needed</div>'); // This is an example
		$_tabulate->SetRecordsPerPage(20);
		
		$_tabulate->SetSelectionType('radio'); // can also be 'checkbox'
		
		$_tabulate->SetButton('add', 'Add');
		$_tabulate->SetButton('edit', 'View / Edit');
		$_tabulate->SetButton('delete', 'Delete');
		// $_tabulate->Append('BUTTON', '<div>Put some text underneath the buttons</div>'); // Example.
		
		/*
		// This is an example of post processing the data.
		$_arr = $_tabulate->GetData();
		
		$_cnt = 0;
		$arr_temp = array();
		foreach ($_arr as $_row){
			foreach ($_row as $_k=>$_v){
				
			}
			$_cnt++;
		}
		
		$_tabulate->SetData($arr_temp);
		*/
		
		
		$this->Assign('<TITLE>', 'Tabulate Example');
		$this->Assign('<MAIN>', $_tabulate->PrintTabulate());
		$this->Assign('<HEAD>', '<link href="/classes/form/css/form.css" rel="stylesheet" type="text/css">');
		$this->Append('<HEAD>', $_tabulate->GetHead());
	}

}

include_once 'controller.php';

