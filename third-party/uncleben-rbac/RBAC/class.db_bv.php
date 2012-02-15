<?php

// It would be great to be able to see how long a query takes to execute and send an error if it takes too long.

/**
  * Database access class with special functions to work with form INSERT, EDIT, UPDATE and DELETE.
  *
  * This class comes with a lot of error handling capabilities. If a query can not execute it will raise an error.
  * When using the 'form' function all fields are checked against a table meta data file to ensure the field is valid
  * otherwise an error is triggered.
  * It will also escape strings before inserting the data into the databse.
  *
  * Ideally this class should be used with a custom error handling class to send emails to administrator when an
  * error is triggered.
  *
  * When instantiating the class, the table name must be specified. The database table will remain associated with
  * the object until it is destroyed or no longer needed.
  *
  * This class needs the class.TableMetaData.php file to generate the database table meta files.
  *
  * All comments and suggestions most welcome.
  *
  * The standards are the same as www.lkajdsf.com
  *
  * USAGE:
  * see example.php
  *
  * - Coding standards: http://www.dagbladet.no/development/phpcodingstandard/
  *
  * @author BV <junk@vhd.com.au> 
  * @license BSD or LGPL 
  * @version 1.0 (2005_12_21) 
  **/

include_once ADODB_INC;

class db_bv{
	
	/**
	  * Database connection object (ADOdb object)
	  * @var obj
	  * @access private
	  **/
	var $mConn; // Database connection
	
	/**
	  * Database table that will be associated with the created object
	  * @var str
	  * @access private
	  **/
	var $mTable; // Database table
	
	/**
	  * Database name
	  * @var str
	  * @access private
	  **/
	var $mDb;
	
	/**
	  * Whether to use a new db connection
	  * @var str
	  * @access private
	  **/
	var $mNewConnection;
	
	/**
	  * Data container. Stores the data to be inserted into the database.
	  * @var arr
	  * @access private
	  **/
	var $mArrData;
	
	/**
	  * Keep the last auto generated ID to have been inserted in the table.
	  * @var int
	  * @access private
	  **/
	var $mLastInsertedId;
	
	/**
	  * Keeps track of the number of rows affected by an UPDATE or DELETE statement
	  * @var int
	  * @access private
	  **/
	var $mAffectedRows;
	
	/**
	  * Stores the number of rows returned by a SELECT statement
	  * @var nt
	  * @access private
	  **/
	var $mRowCount;
	
	/**
	  * Database table meta data
	  * @var arr
	  * @access private
	  **/
	var $mArrMetaData;
	
	// Stores the record set
	// Must be an array
	var $mArrRecordSet;
	
	// Keeps track of the dynamic variable objects
	var $mArrDynamicVars;
	
	/**
	  * Constructor
	  *
	  * @access public
	  * @param str $table database table to work on (optional)
	  **/
	Function Db_bv($table = '', $db = '', $newConnection = FALSE) {
		
		// Connect to the database
		$this->mNewConnection = $newConnection;
		$this->mTable = $table;
		$this->mDb = $db;
		
		$this->connect();
		
		$this->SetFetchMode();
		
		if ($table == ''){
			return $this->mConn;
		}
		
		//-----------------------------------------
		// Check that the table meta data exists otherwise create it
		$metadata_folder = DB_DEF_DIR; // This would normally be defined in a config file
		
		$file_metadata = $metadata_folder.'class.db_'.$table.'.php';
		
		if (!is_file($file_metadata)){ // File does not exist
			
			// create file
			include_once 'class.makeTableMetaData_bv.php';
			
			MakeTableMetaData_bv::Make($table, $metadata_folder, $this->mConn);
		}
		
		//-----------------------------------------
		// Retrieve information for the given table
		include_once $file_metadata;
		
		// Get the metadata for that table.
		$this->mArrMetaData = call_user_func( array( $this->mTable, 'GetMetaData' ));
	}
	
	/**
	  * Db::Connect will attempt a connection to the database.
	  *
	  * All the database connection parameters are defined in the dbConnection.php file
	  * @access private
	  **/
	function Connect() { 
		
		include DB_CON_FILE;
		
		$this->mConn = &ADONewConnection('mysql');
		
		if ($this->mDb != ''){ // Allow us to overwrite the database connection
			$db_name = $this->mDb;
		}
		
		if ($this->mNewConnection){
			$this->mConn->NConnect($_host, $_user, $_pswd, $db_name) or trigger_error('(db:'.$this->mConn->errorNo().') <i>'.$this->mConn->errorMsg().'</i> on host'.$_host.' with databse'.$db_name.' and user '.$_user, E_USER_ERROR);
		}
		else{
			$this->mConn->Connect($_host, $_user, $_pswd, $db_name) or trigger_error('(db:'.$this->mConn->errorNo().') <i>'.$this->mConn->errorMsg().'</i> on host'.$_host.' with databse'.$db_name.' and user '.$_user, E_USER_ERROR);
		}
	}
	
	/**
	  * Db::SetTable will set the given table to the current database connection
	  *
	  * @access private
	  * @param str $table 
	  **/
	Function SetTable($table)
	{
		$this->SetFetchMode();
		$this->ClearData();
		if (isset($this->mArrMetaData)){ unset($this->mArrMetaData); }
		
		$this->mTable = $table;
		
		//-----------------------------------------
		// Check that the table meta data exists otherwise create it
		$metadata_folder = DB_DEF_DIR; // This would normally be defined in a config file
		
		$file_metadata = $metadata_folder.'class.db_'.$table.'.php';
		
		if (!is_file($file_metadata)){ // File does not exist
			
			// create file
			include_once 'class.makeTableMetaData_bv.php';
			
			MakeTableMetaData_bv::Make($table, $metadata_folder, $this->mConn);
		}
		
		//-----------------------------------------
		// Retrieve information for the given table
		include_once $metadata_folder."class.db_$this->mTable.php";
		
		// Get the metadata for that table.
		$this->mArrMetaData = call_user_func( array( $table, 'GetMetaData' ));
	}
	
	//=========================================================================================
	// Base functions.
	// These fuctions are just wrappers around ADOdb functions with extra error handling capabilities.
	
	/**
	  * Db::Execute Execute an SQl statement
	  *
	  * @access public
	  * @param str $sql SQL query
	  **/
	function Execute($sql){
		
		$_rs = $this->mConn->execute($sql) or trigger_error('(db:'.$this->mConn->ErrorNo().') <i>'.$this->mConn->ErrorMsg().'</i>, <tt><b> '.$sql.' </b></tt>', E_USER_WARNING);
		
		return $_rs;
	}
	
	/**
	  * Db:: GetRow retrieve the first row from a SELECT statement
	  *
	  * @access public
	  * @param str $sql SQL query
	  **/
	Function GetRow($sql){
		
		$_rs = $this->mConn->getRow($sql);
		
		if ($_rs === FALSE){
			trigger_error('(db:'.$this->mConn->ErrorNo().') <i>'.$this->mConn->ErrorMsg().'</i>, <tt><b> '.$sql.' </b></tt>', E_USER_WARNING);
		}
		
		return $_rs;
	}
	
	/**
	  * Db::GetAll retrieve record set as an array from a SELECT statement
	  *
	  * @access public
	  * @param str $sql SQL query
	  **/
	Function GetAll($sql){
		
		$_rs = $this->mConn->getAll($sql);
		
		if ($_rs === FALSE){
			trigger_error('(db:'.$this->mConn->ErrorNo().') <i>'.$this->mConn->ErrorMsg().'</i>, <tt><b> '.$sql.' </b></tt>', E_USER_WARNING);
		}
		else{
			$this->mArrRecordSet = $_rs;	
		}
		
		//$this->mRowCount = $this->mConn->RowCount();
		
		return $_rs;
	}
	
	/**
	  * Db::MetaPrimaryKeys returns an array of primary key fields belonging to the table
	  *
	  * @access public
	  **/
	Function MetaPrimaryKeys(){
		
		$_rs = $this->mConn->MetaPrimaryKeys($this->mTable);
		
		return $_rs;
	}
	
	/**
	  * Db::Debug
	  *
	  * This function will use the ADOdb debugging feature to print all the SQL statements that are being executed.
	  *
	  * @access public
	  **/
	Function Debug($bool = TRUE){
		$this->mConn->debug = $bool;
	}
	
	//$bool whether to return error or not.
	Function NextRow($bool = TRUE){
		
		if (!isset($this->mArrRecordSet) || empty($this->mArrRecordSet)){
			
			if ($bool){
				trigger_error('No record set was found. Must first execute a SELECT statement using the GetAll method. Check that a result set exits.', E_USER_NOTICE);
			}
			
			return FALSE;
		}
		
		list($_num, $arr_row) = each($this->mArrRecordSet);
		
		
		if (is_array($arr_row)){
			
			//If we are at the first row, clear data
			if ((int) $_num === 0){
				$this->ClearData();
			}
			
			$this->array2object($arr_row, FALSE);
			
			return TRUE;
		}
		else {
			// Put the pointer back to the beginning
			reset($this->mArrRecordSet);
			
			// Unset the dynamic variables so they won't conflict with other things.
			$this->ClearData();
			
			return FALSE;
		}
	}
	
	// If $check = TRUE then we make sure the table field names do not collide with a predefined class variable.
	// In this case we can't just use type casting because we want to check for conflicts.
	Function array2object($arr, $check = TRUE)
	{
		foreach($arr as $_k=>$_v){
				
			if ($check && isset($this->$_k)){
				trigger_error('The variable *'.$_k.'* is already set. It should not be. We may be trying to set a predefined class variable.', E_USER_WARNING);
			}
			
			$this->mArrDynamicVars[] = $_k;
			
			$this->$_k = $_v;
		}
	}
	
	// Check whether a row already exists
	Function Exists($str_where, $table = ''){
		// TODO: set default table if $table is not set.
		$_sql = "SELECT count(*) as cnt FROM $table WHERE $str_where";
		
		$_row = $this->GetRow($_sql);
		
		if ((int) $_row['cnt'] >= 1 ){
			return TRUE;
		}
		else {
			return FALSE;
		}
	}
	
	//=========================================================================================
	// General Functions
	
	/**
	  * Db::StoreData will store an array of data in 'field name' => 'value' pairs in a  container.
	  *
	  * This function is mainly used with form submissions.
	  *
	  * Example: ->StoreData($POST); // Obviously the $POST array will have to be cleaned up first.
	  *
	  * @access public
	  * @param arr $arrData
	  **/
	Function StoreData($arrData){
		$this->mArrData = $arrData; // Form data
	}
	
	/**
	  * Db::RetrieveDataFrom will retrieve the data from a given array, where the keys correspond to table fields
	  *
	  * A table has to be set when instantiating the class.
	  * Example: ->RetrieveData($POST); // Obviously the $POST array will have to be cleaned up first.
	  *
	  * @access public
	  * @param arr $arrData
	  * @param bool $unset will remove the key from $arrData if set to TRUE
	  * @return FALSE if error or array = key value pairs with matches
	  **/
	Function RetrieveDataFrom($arrData){
		
		if ($this->mTable == ''){
			trigger_error('A table must be specified when instantiating the class.', E_USER_ERROR);
			return FALSE;
		}
		
		$arr_found = array();
		
		// Loop through the available table fields and store the data in the container if appropriate.
		foreach($this->mArrMetaData as $_field=>$arr_info){
			if (in_array($_field, array_keys($arrData))){
				$this->mArrData[$_field] = $arrData[$_field];
				
				$arr_found[$_field] = $arrData[$_field];
			}
		}
		
		return $arr_found;
	}
	
	/**
	  * Db::AddData will add some data to the mArrData container on key-value pair at a time.
	  *
	  * @access public
	  * @param str $fieldName
	  * @param str,int $value
	  **/
	Function AddData($fieldName, $value){
		$this->mArrData[$fieldName] = $value;
	}
	
	/**
	  * Db::ClearData will empty the data container
	  *
	  * @access public
	  **/
	Function ClearData(){
		
		$this->mArrData = array();
		
		if (!empty($this->mArrDynamicVars)){
			foreach($this->mArrDynamicVars as $field_name){
				if (isset($this->$field_name)){
					unset($this->$field_name);
				}
			}
			
			$this->mArrDynamicVars = array();
		}
	}
	
	/**
	  * Db::GetAffectedRows Returns the number of rows affected by an UPDATE or DELETE statement.
	  *
	  * @access public
	  **/
	Function GetAffectedRows()
	{
		return $this->mAffectedRows;
	}
	
	/**
	  * Db::GetAffectedRows Returns the number of rows returned by a SELECT statement.
	  *
	  * @access public
	  **/
	Function GetRowCount()
	{
		return $this->mRowCount;
	}
	
	/**
	  * Db::GetId will return the last inserted id 
	  *
	  * @access public
	  **/
	Function GetId(){
		// Return a value only if there is one.
		if (trim($this->mLastInsertedId) !=''){
			return $this->mLastInsertedId;
		}
		else{
			
			$last_id = $this->mConn->Insert_Id();
			
			if ($last_id > 0){
				return $last_id;
			}
			else{
				return FALSE;
			}
		}
	}
	
	/**
	  * Db::LockTable Lock specified table
	  *
	  * @access public
	  **/
	Function LockTable(){
		$this->Execute("LOCK TABLES $this->mTable WRITE"); 
	}
	
	/**
	  * Db::UnlockTable unlocks the table
	  *
	  * @access public
	  **/
	Function UnlockTable(){
		$this->Execute("UNLOCK TABLES");
	}
	
	
	//================================================================
	// Insert, Update, Delete, Edit functions.
	// These function are to be used with a form class. If you need more flexibility you
	// can write your own SQL statements and directly use the execute() method.
	// If you would like to get a preview of the form class send me an email at junk@vhd.com.au
	
	/**
	  * Db::Insert Inserts the data stored in the mArrData array into the database.
	  *
	  * $arrData is an array $fldName =>$value pair
	  *
	  * @access Public
	  * @return FALSE if failure (Insert's data into a database table.)
	  **/
	Function Insert($echo = FALSE) {
		
		$arr_data = $this->mArrData;
		
		$_table = $this->mTable;
		
		$arr_fld_names = array_keys($arr_data); // get the field names from the container data
		$arr_fld_values = $this->ArrayEscapeString(array_values($arr_data)); // get the corresponding values.
		
		//---------------------------
		// Check that the field names actually belong to this table
		foreach ($arr_data as $field_name=>$_data){
			$this->CheckFieldName($field_name, E_USER_ERROR);
		}
		
		//--------------------------
		// construct SQL statement
		
		$_sql = 'INSERT IGNORE INTO '.$_table.' (';
		$_sql .= implode(', ',$arr_fld_names);
		$_sql .= ") VALUES ('";
		$_sql .= implode("', '",$arr_fld_values);
		$_sql .= "')";
		
		if ($echo){
			echo $_sql;
		}
		
		$_return = $this->Execute($_sql);
		
		$this->mLastInsertedId = $this->mConn->Insert_ID(); // Keep track of the last inserted ID.
		
		//------------------
		// clear the data in the container
		$this->ClearData();
		
		return $_return; // FALSE if failure or an object if success
	}
	
	/**
	  * Db::Update Update the information 
	  *
	  * @access public
	  * @param str $fieldName
	  * @param str $value
	  * @return FALSE if the query did not succeed.
	  **/
	Function Update($fieldName, $fieldValue, $echo = FALSE) {
		
		$arr_data = $this->mArrData;
		$_table = $this->mTable;
		
		//----------------------
		// Make sure the $fieldValue does not have a single quote
		$fieldValue = addslashes(stripslashes($fieldValue));
		
		//----------------------
		// check that the field names actually belong to this table
		foreach ($arr_data as $field_name=>$_data){
			$this->CheckFieldName($field_name, E_USER_ERROR);
		}
		
		//---------------
		// Make sure that $fieldName is unique.
		if ($this->mArrMetaData[$fieldName]['key'] !== 'UNI' && $this->mArrMetaData[$fieldName]['key'] !== 'PRI'){
			trigger_error('The field *'.$fieldName.'* is not unique nor a primary key', E_USER_WARNING);
			exit;
		}
		
		//---------------
		// Clean the data
		$arr_fld_names = array_keys($arr_data); // get the field names
		$arr_fld_values = $this->ArrayEscapeString(array_values($arr_data)); # get the corresponding values.
		
		//--------------------------
		// Rebuild the arr_data array
		$_cnt = count($arr_fld_names);
		
		for ($i=0; $i<$_cnt; $i++ ){
			$arr_clean_data[$arr_fld_names[$i]] = $arr_fld_values[$i];
		}
		
		//------------------------
		// Build the sql statement
		$_sql = 'UPDATE '.$_table;
		$_sql .= ' SET ';
		
		foreach($arr_clean_data as $fld_name=>$_v){
			$_sql .= $fld_name.'=\''.$_v.'\', ';
		}
		$_sql = rtrim($_sql, ', ');
		
		if (!is_numeric($fieldValue)){ // Add single quotes.
			$fieldValue = "'$fieldValue'";
		}
		
		$_sql .= ' WHERE '.$fieldName.' = '.$fieldValue;
		
		if ($echo){
			echo $_sql;
		}
		
		//------------------------------
		$_return = $this->Execute($_sql);
		
		$this->mAffectedRows = $this->mConn->Affected_Rows();
		
		//------------------
		// clear the data in the container
		$this->ClearData();
		
		return $_return;
	}
	
	/**
	  * dbForm::Fetch will retrieve the record from the database given field name and its value to be edited
	  *
	  * @access public
	  * @param str $fieldName
	  * @param str $value
	  * @return FALSE if the query did not succeed
	  **/
	Function Fetch($fieldName, $fieldValue)
	{
		$this->CheckFieldName($fieldName, E_USER_ERROR); // Will return an error if the field does not belong to the correct table
		
		//-----------------------
		// Make sure the $fieldValue does not have a single quote
		$fieldValue = addslashes(stripslashes($fieldValue));
		
		$_sql = "SELECT * FROM $this->mTable WHERE  $fieldName = '$fieldValue' LIMIT 1";
		
		$this->mConn->SetFetchMode(ADODB_FETCH_ASSOC); 
		
		$_return = $this->GetRow($_sql);
		
		return $_return;
	}
	
	/**
	  * Db::Delete will delete a record
	  *
	  * Specify te field name and value to be deleted.
	  *
	  * @param str $fieldName
	  * @param str $value
	  * @return FALSE if the query did not succeed
	  **/
	Function Delete($fieldName, $fieldValue){
		
		$this->CheckFieldName($fieldName, E_USER_ERROR);
		
		//----------------------
		// Make sure the $fieldValue does not have a single quote
		$fieldValue = addslashes(stripslashes($fieldValue));
		
		$sql = "DELETE FROM $this->mTable WHERE $fieldName = '$fieldValue'";
		$_return = $this->Execute($sql);
		
		$this->mAffectedRows = $this->mConn->Affected_Rows();
		
		return $_return;
	}
	
	/**
	  * Db::SetFetchMode will set the fetch mode to either numeric or associative
	  *
	  * Is a wrapper for the ADOdb SetFetchMode method.
	  *
	  * @access public
	  * @param mode $mode
	  **/
	Function SetFetchMode($mode = ADODB_FETCH_ASSOC)
	{
		$this->mConn->SetFetchMode($mode); 
	}
	
	//================================================================
	// PRIVATE FUNCTIONS 
	
	/**
	  * Db::ArrayEscapeString Add slashes to values if magic quotes are turned off, recursively to all members of an array
	  *
	  * @access private
	  * @param arr $arrData
	  * @return array
	  **/
	Function ArrayEscapeString($arrData){
		
		reset($arrData);
		$magic_on = FALSE; // Initialise
		
		if (get_magic_quotes_gpc() && !defined('MAGIC_QUOTES_OFF')){
			$magic_on = TRUE ;
		}
		
		while (list($k, $v) = each($arrData)){
			
			if ($magic_on){
				$v = stripslashes($v);
			}
			
			$arrData[$k] = (is_numeric($v)) ? $v :  mysql_real_escape_string($v); // This will work with Mysql as numbers can be quoted, but will not work for postgres.
		}
		
		return $arrData;
	}
	
	
	/**
	  * Db::CheckFieldName Ensures the given field name actually exists for the specified table, otherwise an error is returned.
	  *
	  * @access private
	  * @param str $fieldName
	  * @return int $errorType
	  **/
	Function CheckFieldName($fieldName, $errorType = E_USER_WARNING)
	{
		if (!in_array($fieldName, array_keys($this->mArrMetaData))){
			trigger_error('The field *'.$fieldName.'* does not exist in the table '.$this->mTable.'.', $errorType);
		}
	}
}

?>
