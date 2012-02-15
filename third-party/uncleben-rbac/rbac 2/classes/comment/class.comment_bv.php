<?php

/**
  * This class is used to allow people to comment on an object
  **/
/*
CREATE TABLE comments_public (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	comments_id INT UNSIGNED NOT NULL,
	name VARCHAR(100) NOT NULL,
	email VARCHAR(200) NOT NULL
) TYPE = MyISAM COMMENT ='';
*/
  
class Comment_bv {
	
	
	var $mUserId; // Default is 0 i.e. Anonymous.
	var $mConn; // Database connection. Private
	
	var $mReferenceTable;
	var $mReferenceId;
	
	var $mData; // Container for POST or GET values
	
	// We need: user_id, reference_table, reference_id, POST or GET
	Function Comment_bv($refId, $refTable, $userId = 0, $data, $attributes = array())
	{
		// TODO: add a setting to only show comment form if user is logged in.
		
		$this->mUserId = $userId;
		
		$this->mReferenceId = (int) $refId;
		$this->mReferenceTable = $refTable;
		
		$this->mData = $data;
		
		//--------------------------
		// Establish database connection
		include_once CLASSES_DIR.'db/class.db_bv.php';
		
		$this->mConn = & new db_bv();
	}
	
	/**
	  *
	  *
	  **/
	// Retrieve the comments from the database.
	Function GetComments()
	{
		// Get the form and process a submission if any
		$frm_html = $this->GetForm();
		
		// Find out whether the reference id and reference table exist.
		/* NOT NEEDED
		if (!$this->mConn->Exists("id = $this->mReferenceId", $this->mReferenceTable)){
			return FALSE; // The reference does not exist so don't return a form
		}
		*/
		
		//--------------------------------------------
		// If we are here the object we want to comment exists
		$arr_sql = array(
			'fields'=>array(// ID always first
				't1.id',
				'users_id',
				'title',
				'comment',
				't4.username',
				'DATE_FORMAT(t1.date_created, \'%a %D %b %Y at %H:%i:%s\') as date_created',
				'CONCAT(t3.name,\' (non member)\') as public_name',
				),
			'from'=>array('comments as t1'),
			'join'=>array('
				LEFT JOIN comments_public as t3 ON t3.comments_id = t1.id
				LEFT JOIN users as t4 ON t4.id = t1.users_id
			'),
			'where'=>'ref_id = '.$this->mReferenceId.'  AND ref_table = \''.$this->mReferenceTable.'\' AND is_enabled = 1',
			'order'=>'date_created DESC',
			'group'=>'',
			'limit'=>''
		);
		
		$_tabulate = new tabulate_bv($this->mConn, $arr_sql);
		//$_tabulate->SetDebug();
		$_tabulate->SetTitle('Comments:');
		$_tabulate->SetRecordsPerPage(10);
		
		$_tabulate->SetDataTemplate('
		<div class="comment_container">
			<div class="comment_title">[#]<TITLE>[#]</div>
			<div class="comment_info">Posted by: <i>[#]<PUBLIC_NAME>[#]<USERNAME>[#]</i> on [#]<DATE_CREATED>[#]</div>
			<div class="comment_text">[#]<COMMENT>[#]</div>
		</div>
		');
		
		if (count($_tabulate->GetData()) >= 1){ // If we have some records
			$_html = $_tabulate->PrintTabulate();
		}
		else {
			$_html ='<div style="color: blue; margin-top: 10px; padding-left: 20px; padding-right: 20px;">Be the first to post a comment.</div> ';
		}
		
		return 	$_html.$frm_html;
	}
	
	
	/**
	  *
	  *
	  **/
	Function GetForm()
	{
		include_once 'frm.comment.php'; // Form defintion
		
		if ($this->mUserId === 0){ // Anonymous user
			
			$_frm->AssignInput('<NAME>', 'name', array('type'=>'text', 'class'=>'inp', 'info'=>'' ));
			$_frm->SetInputRules('name', array('table'=>'comments_public', 'required'));
			
			$_frm->AssignInput('<EMAIL>', 'email', array('type'=>'text', 'class'=>'inp', 'size'=> 30, 'info'=>'' ));
			$_frm->SetInputRules('email', array('table'=>'comments_public', 'email', 'required'));
			$_frm->Append('<EMAIL>', '<div style="font-size: 9px;">Your email address will never be given out.</div>');
		}
		
		// Process the form if it has been submitted
		if ($_frm->Validate($this->mData)){
			
			$_db = $this->mConn;
			
			$_db->SetTable('comments');
			
			$_db->StoreData($_frm->GetDbData('comments'));
			
			$_db->AddData('ref_table', $this->mReferenceTable);
			$_db->AddData('ref_id', $this->mReferenceId);
			$_db->AddData('date_created', date('Y-m-d H:i:s'));
			
			if ($this->mUserId !== 0){
				$_db->AddData('users_id', $this->mUserId);
			}
			else { // Anonymous
				$_db->AddData('users_id', 0);
			}
			
			$_db->Insert();
			
			// Insert data into comments_public if applicable
			if ($this->mUserId === 0){
				
				$_id = $_db->GetId();
				
				$_db->SetTable('comments_public');
				
				$_db->StoreData($_frm->GetDbData('comments_public'));
				
				$_db->AddData('comments_id', $_id);
				
				$_db->Insert();
			}
		}
		
		return $_frm->PrintForm();
	}
	
	/**
	  *
	  *
	  **/
	Function PrintComment()
	{
		return $this->GetComments();
	}	
}
