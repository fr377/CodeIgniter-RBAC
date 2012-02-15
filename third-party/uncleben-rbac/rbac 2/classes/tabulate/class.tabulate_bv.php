<?php
/**
  * This class will help you display and search for data from a given database table.
  * 
  * You can optionaly specify a check box or radio button to be associated with each row, which will allow you to pass 
  * selections to another page for processing those rows. (i.e. Edit, delete, etc...)
  *
  *
  * Will only work with mysql 4.0.0 or above due to FOUND_ROWS() function
  *
  * Coding Standard used : http://www.dagbladet.no/development/phpcodingstandard/
  *
  * @author Ben Vautier <classes at vhd.com.au>
  * @version 1.0 (2006-01-03)
  * @copyright (c) 2006
  * @license LGPL or BSD
  **/

// TODO: Deal with mutiple tabulates on the same page
//include_once 'class.table.php';

class tabulate_bv
{
	
	var $mArrSql; // Store the SQL array definition
	var $mArrMap; // maps field names with labels
	
	var $mTitle; // Title for the layout table.
	var $mNoRecordMessage; // The message that is displayed if no records are found.
	
	var $mIsFirstColInputTypeEnabled; // boolean, determines whether to include checkboxes or radio buttons in the first colum
	var $mFirstColInputType; // Specify whether the input type is 'radio' or 'checkbox'
	var $mIsForm; //  used to manually return a tabulated form when mIsFirstCollInputType... is disabled
	
	var $mDebug; // If true will return SQL statement
	var $mConn; // Database connection.
	
	var $mObjPager; // holds the pager object
	var $mPagerMode; // Can either be Sliding or Jumping. See Pager.php class for more info. Default is Sliding.
	var $mRecordsPerPage; // Number of rows per page
	var $mIsPaginationEnabled;
	
	var $mSearchForm; // Stores the HTML for the search fields
	var $mIsSearchEnabled; // Specifies whether a Search has been enabled. Default FALSE. Access Private
	var $mArrSearchFields; // Store fields to be searched.
	var $mSearchFormId; // The ID tag the search form has.
	
	var $mArrHead; // stores some code to go into the HEAD of a HTML document
	
	var $mIsHeaderLinksEnabled; // Whether to enable links in the headears to reorder columns
	var $mArrTokens; // Stores token names in the order they should be displayed
	
	var $mArrTabulateContainer; // Store content for token keys
	var $mArrFormAttributes; // Store the form attributes.
	
	var $mArrData; // Stores the SQL record set.
	
	var $mDataTemplate; // Stores a template to format data with.
	
	/**
	  * CONSTRUCTOR
	  *
	  * @access public
	  * @param obj $conn database connection
	  * @param array $arrSql
	  * @param array $arrTokens
	  **/
	Function Tabulate_bv($conn, $arrSql, $pagination = TRUE, $arrTokens = array('TITLE', 'SEARCH', 'DATA', 'PAGER', 'BUTTON')){
		
		$this->mConn = $conn;
		$this->mArrSql = $arrSql;		
		$this->mArrTokens = $arrTokens;
		
		// Pager
		$this->mPagerMode = 'Sliding';
		$this->mRecordsPerPage = 15;
		$this->mIsPaginationEnabled = $pagination;
		
		// Search
		$this->mIsSearchEnabled = FALSE;
		
		// Initialise
		$this->mNoRecordMessage = '<h2 style="color:red;">No Records Found!</h2>';
		$this->mIsHeaderLinksEnabled = TRUE;
		$this->SetHead('<link href="'.CSS_DIR.'tabulate.css" rel="stylesheet" type="text/css">');
		$this->mArrFormAttributes = array('action'=>'', 'method'=>'POST', 'name'=>'frm_tabulate');
		$this->mArrMap = array();
		$this->mIsForm = FALSE;
	}
	
	/**
	  * Tabulate::SetTitle set's the title for the layout table
	  *
	  * @access Public
	  * @param str $str String for the table header
	  **/
	Function SetTitle($str)
	{
		$this->Assign('TITLE' , '<div id="title">'. $str .'</div>');
	}
	
	Function SetFormAttributes($arr)
	{
		$this->mArrFormAttributes = $arr;
	}
	
	Function Prepend($token, $content)
	{
		array_unshift($this->mArrTabulateContainer[$token], $content);
	}
	
	Function Assign($token, $content)
	{
		$this->mArrTabulateContainer[$token] = array();
		
		$this->mArrTabulateContainer[$token][] = $content;
	}
	
	Function Append($token, $content)
	{
		$this->mArrTabulateContainer[$token][] = $content;
	}
	
	/**
	  * Tabulate::PrintTabulate Returns the complete HTML table.
	  *
	  * @access private
	  * @return HTML
	  **/
	Function PrintTabulate()
	{
		$_layout = '';
		
		$this->BuildFormattedData();
		
		foreach ($this->mArrTokens as $_token){
			
			if (!isset($this->mArrTabulateContainer[$_token])){
				continue;
			}
			
			if ($_token === 'BUTTON' && $this->mFirstColInputType === 'checkbox'){
				$this->Prepend('BUTTON', '<a onClick="checkAll(1,\''.$this->mArrFormAttributes['name'].'\')">select all</a> | <a onClick="checkAll(0,\''.$this->mArrFormAttributes['name'].'\')">unselect all</a><br>');
			}
			
			$_layout .= '<div class="'.strtolower($_token).'">'.implode('', $this->mArrTabulateContainer[$_token])."\n</div>\n";
		}
		
		$_html = "\n<div id=\"tabulate\">\n".$_layout."</div>\n";
		
		// Wrap table around a form if applicable (i.e. there is something to select)
		if ($this->mIsFirstColInputTypeEnabled || $this->mIsForm){
			$_html = "\n<form ".$this->GetAttributeString($this->mArrFormAttributes).">\n $_html</form>\n";
		}
		
		return $_html;
	}
	
	/**
	  * Tabulate::SetSelectionType will set the $mIsFirstColCheckBox to TRUE
	  *
	  * This specifies whether the first column should have radio buttons or not.
	  *
	  * @access public
	  * @param str $type, can either be 'radio' or 'checkbox'.
	  **/
	Function SetSelectionType($type = 'radio')
	{
		$this->mIsFirstColInputTypeEnabled = TRUE;
		$this->mFirstColInputType = $type;
		
		
		if ($type === 'checkbox'){
			$this->SetHead('<script language = "Javascript">
			<!-- 
			function checkAll(val, form) {
				dml =document.forms[form];
				
				len = dml.elements.length;
				
				for( i=0 ; i<len ; i++) {
					if (dml.elements[i].type == "checkbox") {
						dml.elements[i].checked=val;
					}
				}
			}
			-->
			</script>');

		}
	}
	
	/**
	  * Allows the user to specify a message that will be shown in the center of the table if no 
	  * records were found.
	  *
	  * @access public
	  * @param str $msg
	  **/
	Function SetNoRecordMessage($msg)
	{
		$this->mNoRecordMessage = $msg;
	}
	
	Function SetButton($fieldName, $fieldValue, $arrAttr = array()){
		$arrAttr['type'] = 'submit';
		$arrAttr['value'] = $fieldValue;
		$this->Append('BUTTON', $this->GetInputHtml($fieldName, $arrAttr));
	}
	
	Function GetInputHtml($name, $arrAttr) {
		return '<INPUT name="'.$name.'"'.$this->GetAttributeString($arrAttr).' >';
	}
	
	Function GetData()
	{
		//---------------------
		// Connect to the database
		$_conn = $this->mConn;
		$_conn->SetFetchMode(ADODB_FETCH_ASSOC);
		
		if (!$this->mIsPaginationEnabled){ // Pagination is disbaled
			
			$_sql = $this->GetSql();
			$arr_rs = $_conn->getAll($_sql);
			
			$this->SetData($arr_rs);
			
			return $arr_rs;
		}
		
		//-------------
		// Initialise Pager
		
		// Find out the total number of records if not already known
		if (!isset($_GET['totalItems'])){ // First time the page has been accessed.
			
			// We can do everything in one pass. We can find the total number of records and retrieve the
			// first mReocrdsPerPage rows at the same time
			$this->mArrSql['limit'] = "$this->mRecordsPerPage OFFSET 0";
			
			$_sql = $this->GetSql();
			
			$_sql = preg_replace('/^SELECT/', 'SELECT SQL_CALC_FOUND_ROWS', $_sql); // Only change the FIRST select (there may be subqueries).
			
			$arr_rs = $_conn->getAll($_sql);
			$arr_num_rows = $_conn->getRow('SELECT FOUND_ROWS() as num');
			
			$total_items = $arr_num_rows['num'];
			
			$this->initPager($total_items); // Initialise Pager
		}
		else { // user is paginating result set.
			$total_items = (int) $_GET['totalItems'];
			
			$this->initPager($total_items); // Initialise Pager
			
			// Set the limit for the query
			$this->mArrSql['limit'] = $this->getPagerLimit();
			
			//-----------------
			// Get rows
			$_sql = $this->GetSql();
			$arr_rs = $_conn->getAll($_sql);
		}
		
		$this->SetData($arr_rs);
		
		return $arr_rs;
	}
	
	/**
	  * Tabulate::SetData() Allow user to set the data to be displayed in the table.
	  *
	  * This is normally used in conjunction with GetData. The user can get the data and process it and
	  * send it back to the tabulate object using this method.
	  *
	  * @access public
	  * @param array $arrData (2 dimensional)
	  **/
	Function SetData($arrData)
	{
		$this->mArrData = $arrData;
	}
	
	/**
	  * Tabulate:: SetDebug will put the object in debug mode and return the SQL statements
	  *
	  * @access public
	  **/
	Function SetDebug()
	{
		$this->mDebug = TRUE;
	}
	
	
	/**
	  * Tabulate::DisableHeaderLinks allows us to define whether the header can be clicked to reorder thier column 
	  * data in ascending or descending order.
	  *
	  * @access public
	  * @param bool $bool
	  **/
	Function DisableHeaderLinks()
	{
		$this->mIsHeaderLinksEnabled = FALSE;
	}
	
	Function SetDataTemplate($tpl){
		$this->mDataTemplate = $tpl;
	}
	
	//========================================================================
	// Functions to SET and GET information for HTML HEAD area
	/**
	  * Form::SetHead will place some information in the mArrHeader container
	  *
	  * This information is meant to be place in the HEAD section of a HTML document.
	  * It is generally needed for calendar javascript functions.
	  *
	  * @access public
	  * @param str $str
	  **/
	Function SetHead($str)
	{
		$this->mArrHeader[] = $str;
	}
	
	/**
	  * Form::GetHead will return the information in the mArrHeader container that needs
	  * to be included in the HEAD section of a HTML document.
	  * It is generally needed for calendar functions.
	  *
	  * @access public
	  * @return str
	  **/
	Function GetHead()
	{
		//print_r($this->mArrHeader);
		//exit;
		$arr = array_unique($this->mArrHeader);
		$str = implode("\r\n", $arr);
		
		return $str;
	}
	
	//========================================================================
	// PAGER METHODS
	
	/**
	  * Tabulate::InitPager will initialise the pager class
	  *
	  * This function uses the PEAR Pager.php class. See it for more info.
	  *
	  * @access private
	  * @param int $totalItems it the total number of items available
	  * @param str $mode can either be 'Sliding' or 'Jumping'.
	  **/
	Function InitPager($totalItems)
	{
		require_once 'pager/Pager.php';
		
		$_params = array(
			'totalItems'=> $totalItems,
			'firstPageText'=>'FIRST',
			//'firstPagePre'=>'',
			//'firstPagePost'=>'',
			'lastPageText'=>'LAST',
			//'lastPagePre'=>'',
			//'lastPagePost'=>'',
			'extraVars'=>array('setPerPage'=> $this->mRecordsPerPage, 'totalItems'=> $totalItems),
			'prevImg'=>'< Back',
			'nextImg'=>'Next >',
			'spacesBeforeSeparator'=> 1,
			'spacesAfterSeparator'=> 1,
			'perPage' => $this->mRecordsPerPage,
			'delta' => 3,             // for 'Jumping'-style a lower number is better
			'append' => true,
			'separator' => '',
			'clearIfVoid' => false,
			'urlVar' => 'entrant',
			'clearIfVoid'=> true,
			'useSessions' => false,
			'closeSession' => false
		);
		
		// Whether 'Folding' or 'Sliding'
		$_params['mode'] = $this->mPagerMode; 
		
		// Instantiate object
		$this->mObjPager = & Pager::factory($_params);
		
		$this->Assign('PAGER', $this->getPagerHtml());
	}
	
	/**
	  * Tabulate::getPagerHtml will return the HTML for the pagnination links.
	  *
	  * @access private
	  * @return HTML
	  **/
	Function GetPagerHtml()
	{
		
		// get the links array from the pager object
		$arr_links = $this->mObjPager->getLinks();
		
		// If sliding
		if ('sliding' == strtolower($this->mPagerMode)){
			$str_links = $arr_links['all'];
		}
		else {// Jumping
			$str_links = $arr_links['first'].' '.$arr_links['all'].' '.$arr_links['last'];
		}
		
		// Build HTML
		$links_html = '
				<span id="pager_links">'.$str_links.'</span>
				<span id="pager_info"> Total number of rows: '.$this->mObjPager->numItems().'</span>
		';
		
		return $links_html;
	}
	
	/**
	  * Tabulate::GetPagerLimit will return a string for the LIMIT clause to be used with MySQL
	  *
	  * @access private
	  * @return str
	  **/
	Function GetPagerLimit()
	{
		// get the links array from the pager object
		$arr_offset = $this->mObjPager->getOffsetByPageId();
		
		$_start = $arr_offset[0] - 1; // NOTE: the minus one is to fix mysql
		$_end = $arr_offset[1] - 1;
		
		// if there is no limit required the $_end will be 0 therefore return empty string
		if (0 == $_end){
			return '';
		}
		else {
			
			$_delta = ($_end - $_start) + 1;
			
			return "$_delta OFFSET $_start";
		}
	}
	
	/**
	  * Tabulate::SetRecordsPerPage allows user to change how many rows are to be shown per page.
	  *
	  * @access public
	  * @param int $num
	  **/
	Function SetRecordsPerPage($num)
	{
		$this->mRecordsPerPage = $num;
		
		// Clear value from session if isset
		if(isset($_SESSION['setPerPage'])){
			unset($_SESSION['setPerPage']);
		}
		
	}
	
	//========================================================================
	// SEARCH METHODS
	
	/**
	  * Tabulate::SetSearchFields is used to specify the name of the HTML input tags.
	  * These names must also correspond to the database table field names
	  *
	  * @access public
	  * @param arr $arrFields array of field names
	  **/
	Function SetSearchFields($arrFields)
	{
		$this->mArrSearchFields = $arrFields;
	}
	
	/**
	  * Tabulate::SetSearchFormId set the HTML ID tag for the form. The default is 'search_form'
	  *
	  * @access public
	  * @param str $name name of the ID tag
	  **/
	Function SetSearchFormId($name = 'search_form')
	{
		$this->mSearchFormId = $name;
	}
	
	/**
	  * Tabulate::GetSearchForm will return the HTML for the search form
	  *
	  * @access private
	  **/
	Function GetSearchForm()
	{
		return $this->mSearchForm;
	}
	
	/**
	  * Tabulate::SetSearchForm set the HTML search form to be used in the data table
	  *
	  * @access public
	  * @param str $html the HTML form for the search fields
	  **/
	Function SetSearchForm($html)
	{
		if (!isset($this->mSearchFormId)){
			$this->SetSearchFormId();
		}
		
		$this->mIsSearchEnabled = TRUE;
		$this->Assign('SEARCH', $html);
		
		$_js ='
			<style type="text/css">
				#'.$this->mSearchFormId.' {display: none;}
			</style>
			
			<script type="text/javascript">
			<!--
				var status = "none";
				
				function toggleSearchForm(){
					
					if (status == "none"){
						document.getElementById("'.$this->mSearchFormId.'").style.display = "block";
						status = "block";
					}
					else{
						document.getElementById("'.$this->mSearchFormId.'").style.display = "none";
						status = "none";
					}
				}
			// -->
			</script>
		';
		
		$this->SetHead($_js);
	}
	
	/**
	  * Tabulate::ProcessSearchFields will process the search fields if a search form was set
	  *
	  * @access public
	  * @param str $sqlWhere The WHERE clause is needed if already contains information
	  **/
	// sqlWhere is the existing where clause.
	Function ProcessSearchFields($sqlWhere)
	{
		$sql_where = $sqlWhere;
		
		// If no search field available
		if (count($this->mArrSearchFields) === 0){
			return ;
		}
		
		foreach ($this->mArrSearchFields as $field_key=>$field_names){
			
			// If field_names was an array then we have a date to process.
			if (!is_array($field_names)){
				
				if (!isset($_REQUEST[$field_names])){continue;}
				
				$_value = htmlentities(trim($_REQUEST[$field_names]));
				
				if ($_value == ''){continue;}
				
				// Escape characters
				$_v = $this->EscapeString($_value);
				
				// Find out if a field_key is used, use that as the field_name
				if ($field_key !== (int) $field_key){ // We have a user defined field name
					$_field = $field_key;
				}
				else {
					$_field = $field_names;
				}
				
				// Process wildcard searches
				if (preg_match('/(^\*|\*$)/',$_v)){ // If start or end with a star
					$_v = preg_replace('/(^\*|\*$)/', '%', $_v);
					
					$sql_where .= " AND $_field LIKE '$_v'";
				}
				else if (preg_match('/^regex:/i', $_v)){ // Regular expression
					
					$_v = preg_replace('/^regex:\s*/i', '', $_v);
					
					$sql_where .= " AND $_field REGEXP '$_v'";
				}
				else {
					$sql_where .= " AND $_field = '$_v'";
				}
				
				// We have to insert some values in the GET container to work with the pager class.
				$_GET[$field_names] = $_value;
				
			}
			else {// We have a date to process.
				if (count($field_names) !== 2){
					trigger_error('The date field must have exactly two fields. One for the start and the other for the end.', E_USER_ERROR);
				}
				
				if (!isset($_REQUEST[$field_names[0]]) || !isset($_REQUEST[$field_names[1]])){continue;}
				
				if ($_REQUEST[$field_names[0]] !== $_REQUEST[$field_names[1]]){
					
					$date_1 = htmlentities($_REQUEST[$field_names[0]]);
					$date_2 = htmlentities($_REQUEST[$field_names[1]]);
					
					if (trim($_REQUEST[$field_names[0]]) != ''){
						$sql_where .= " AND $field_key >= '$date_1'";
					}
					
					if (trim($_REQUEST[$field_names[1]]) != ''){
						$sql_where .= " AND $field_key <= '$date_2'";
					}
				}
				
				// We have to insert some values in the GET container to work with the pager class.
				$_GET[$field_names[0]] = htmlentities($date_1);
				$_GET[$field_names[1]] = htmlentities($date_2);
			}
			
		}
		
		//----------------
		$sql_where = preg_replace ('/^\s*AND/', '', $sql_where); // remove any leading 'AND' keyword
		
		return $sql_where;
	}
	
	/**
	  * Tabulate_bv::BuildCombo will construct an HTML combo box
	  *
	  * @access private
	  * @param str $inputName the value of the HTML name tag
	  * @param str $sql sql query
	  **/
	Function BuildCombo($inputName, $sql){
		
		$this->mConn->SetFetchMode('NUM');
		$arr_rs = $this->mConn->GetAll($sql);
		
		
		$_options = '<option value="">- -</option>';
		foreach ($arr_rs as $arr_row){
			$_options .= "<option value=\"{$arr_row[0]}\">{$arr_row[0]}</option>";
		}
		
		return "<SELECT name=\"$inputName\">$_options</SELECT>";
	}
	
	
	// Map field names with actual labels to appear in the header
	Function SetMap($arr)
	{
		$this->mArrMap = $arr;
	}
	
	
	//======================================================================
	// MISC PRIVATE FUNCTIONS
	
	/**
	  * Tabulate::GetSql generate the sql statement from the SQL definition
	  *
	  * @access private
	  * @return SQL query
	  **/
	Function GetSql()
	{
		$arr_sql_fields = (isset($this->mArrSql['fields'])) ? $this->mArrSql['fields'] : array();
		$arr_sql_from = (isset($this->mArrSql['from'])) ? $this->mArrSql['from'] : array();
		$arr_sql_join = (isset($this->mArrSql['join'])) ? $this->mArrSql['join'] : array();
		$sql_where = (isset($this->mArrSql['where'])) ? $this->mArrSql['where'] : '';
		$sql_order = (isset($this->mArrSql['order'])) ? $this->mArrSql['order'] : '';
		$sql_group = (isset($this->mArrSql['group'])) ? $this->mArrSql['group'] : '';
		$sql_having = (isset($this->mArrSql['having'])) ? $this->mArrSql['having'] : '';
		$sql_limit = (isset($this->mArrSql['limit'])) ? $this->mArrSql['limit'] : '';
		
		// Construct SQL statement
		$_sql = 'SELECT ';
		$_sql .= implode(', ', $arr_sql_fields);
		$_sql .= ' FROM '.implode(', ', $arr_sql_from);
		$_sql .= ' '.implode(' ', $arr_sql_join);
		
		// Look at the query string to superceed any default values. This is essentially used to reorder columns
		if (isset($_GET['order_by']) && trim($_GET['order_by']) != ''){
			$sql_order = trim($_GET['order_by']);
		}
		
		//----------------------------------------------
		// If search is enabled process the search terms
		if ($this->mIsSearchEnabled){
			
			if (empty($this->mArrSearchFields)){
				trigger_error('Please define the fields to be searched using the SetSearchFields method.', E_USER_ERROR);
			}
			
			$sql_where = $this->ProcessSearchFields($sql_where);
		}
		
		//------------------------
		// Add where, order by and 
		(trim($sql_where) != '') ? $_sql .= ' WHERE '.$sql_where : '';
		(trim($sql_group) != '') ? $_sql .= ' GROUP BY '.$sql_group : '';
		(trim($sql_having) != '') ? $_sql .= ' HAVING '.$sql_having : '';
		(trim($sql_order) != '') ? $_sql .= ' ORDER BY '.$sql_order : '';
		(trim($sql_limit) != '') ? $_sql .= ' LIMIT '.$sql_limit : '';
		
		if ($this->mDebug){
			echo $_sql;
		}
		
		return $_sql;
	}
	
	
	Function BuildFormattedData(){
		
		if (!isset($this->mArrData)){
			$arr_rs = $this->GetData();
		}
		else {
			$arr_rs = $this->mArrData;
		}
		
		//----------------------------
		if ($this->mDataTemplate == ''){
			$this->BuildDataTable($arr_rs);
		}
		else{
			include_once CLASSES_DIR.'/template/class.template_bv.php';
			
			$_tpl = & new template_bv( $this->mDataTemplate, TRUE);
			
			foreach($arr_rs as $arr_row){
				foreach ($arr_row as $_fld=>$_value){
					$_tpl->Assign('<'.strtoupper($_fld).'>', $_value);
				}
				
				$this->Append('DATA', $_tpl->PrintTemplate());
				$_tpl->ClearContainer();
			}
		}
	}
	
	/**
	  * Tabulate::BuildDataTable Builds the data array. All information is stored in the parent table class
	  *
	  * @access private
	  * @return FALSE on failure
	  **/
	Function BuildDataTable($arrRs)
	{
		
		$_table = & new table();
		$_table->SetTableAttributes(array('border'=>'0', 'width'=>'100%', 'cellpadding'=>'2', 'cellspacing'=>'2', 'class'=>'tbl_data'));
		
		//-------------------------------
		// If there is no record set, return FALSE
		if (!$arrRs){ // Since $arrRs = FALSE
			$_row = $_table->AddRow();
			
			$_table->SetCellContent($_row, 1, $this->mNoRecordMessage);
			$_table->SetCellAttributes($_row, 1, array('align'=>'center'));
			
			$this->Assign('DATA', $_table->PrintTable());
			
			return FALSE;
		}
		
		//-----------------
		// Add headers to the first row.
		$this->SetDataTableHeaders($_table, $arrRs[0]);
		
		//-----------------
		// Loop through rows
		foreach ($arrRs as $arr_row){
			
			$_col = 1;
			$_row = $_table->AddRow();
			
			// Loop through columns
			foreach ($arr_row as $fld_name=>$fld_v){
				
				//$fld_v = htmlentities($fld_v);
				
				// Put a check box or radio button in column 1 if requested
				if (1 === $_col && $this->mIsFirstColInputTypeEnabled){
					('checkbox' === $this->mFirstColInputType) ? $fld_name_frm = $fld_name.'[]': $fld_name_frm = $fld_name;
					
					$_table->SetCellContent($_row, $_col, '<INPUT type="'.$this->mFirstColInputType.'" name="'.$fld_name_frm.'" value="'.$fld_v.'">');
					
					$_col++;
				}
				
				// If the map name is set but intentionally set to NULL then skip that column 
				// and do not show it in the table
				if (array_key_exists($fld_name, $this->mArrMap) && (NULL === $this->mArrMap[$fld_name])){
					continue;
				}
				
				$_table->SetCellContent($_row, $_col, $fld_v);
				
				// Set different colors for even / odd rows
				if (( $_row % 2 ) !== 0) {
					$_table->SetFancyRowStyle($_row, array('class'=>'even_row'));
				}
				else {
					$_table->SetFancyRowStyle($_row, array('class'=>'odd_row'));
				}
				
				$_col++;
			}
		}
		
		$this->Assign('DATA', $_table->PrintTable());
	}
	
	/**
	  * Tabulate::SetDataTableHeaders will set the first row of the data table with headers (i.e. column names)
	  *
	  * @access private
	  * @param object $objTable by reference
	  * @param array $arrFirstRow
	  **/
	Function SetDataTableHeaders(&$objTable, $arrFirstRow)
	{
		
		$_col = 1;
		$_row = $objTable->AddRow();
		
		foreach ($arrFirstRow as $fld_name=>$fld_v){ // Just grab the first record
			
			if (1 === $_col && $this->mIsFirstColInputTypeEnabled){
				// If it the first column and IsFirstColInputBox is enabled then don't put any headers
				// in the first column.
				// Start headers on the second column 
				// Increment the column count
				$objTable->SetCellContent($_row, $_col, '');
				$_col++;
			}
			
			// If the map name is set to NULL then skip that column 
			// and do not show it in the table
			if (array_key_exists($fld_name, $this->mArrMap) && ($this->mArrMap[$fld_name]) === NULL){
				continue;
			}
			
			// look to see if there is an associated label in the mArrMap array otherwise use label name
			// If we have a dash (-) as the name, use the field name for the header.
			$_lbl =(isset($this->mArrMap[$fld_name])) ? $this->mArrMap[$fld_name] : ucfirst(str_replace('_', ' ', $fld_name));
			
			// Set headers in anchors or leave as is
			if ($this->mIsHeaderLinksEnabled){
				
				//-----------------------
				// URL of the current page.
				$_url = $this->GetThisUrl();
				
				parse_str($_SERVER['QUERY_STRING'], $arr_query_str);
				
				$order_by = (isset($arr_query_str['order_by'])) ? $arr_query_str['order_by'] : '';
				
				// if there was an order_by variable remove it from the URL
				$_url = preg_replace('/(\&|\&amp;)order_by.*?(\&|$)/','&', $_url);
				$_url = rtrim($_url, '& ='); // Remove any trailing &, \s, and =
				
				// We will allow people to click on a column to order by that column.
				// If the fld_name corresponds to an already sorted column switch the order direction
				if ($order_by != '' && preg_match("/^$fld_name.*?(DESC|ASC)/", $order_by, $_match)){
					if (strtoupper($_match[1]) === 'DESC'){
						$asc_desc = '+ASC';
					}
					else{
						$asc_desc = '+DESC';
					}
				}
				else { // Check the sql container
					// If a column has already been ordered we want to link the opposite
					$sql_order = (isset($this->mArrSql['order'])) ? $this->mArrSql['order'] : '';
					
					$_regex = "/$fld_name.*?(ASC|DESC)/";
					
					if ($sql_order != '' && preg_match($_regex, $sql_order, $_match)){
						
						if (strtoupper($_match[1]) === 'ASC'){
							$asc_desc = '+DESC';
						}
						else {
							$asc_desc = '+ASC';
						}
					}
					else {
						$asc_desc = '+DESC'; // Default
					}
				}
				
				$cell_col = '<b><a href="'.$_url.'&order_by='.$fld_name.$asc_desc.'">'.$_lbl.'</a></b>';
			}
			else {
				$cell_col = '<b>'.$_lbl.'</b>';
			}
			
			$objTable->SetCellContent($_row, $_col, $cell_col);
			$objTable->SetCellAttributes($_row, $_col, array('align'=>'left'));
			
			$_col++;
		}
	}
	
	
	/**
	  * Tabulate::GetThisUrl Returns the current script name with query string parsed for htmlentities.
	  *
	  * @access private
	  * @return str
	  **/	
	Function GetThisUrl()
	{
		$_url = basename($_SERVER['SCRIPT_NAME']).'?'.htmlentities($_SERVER['QUERY_STRING']); // htmlentities to avoid XSS
		
		return $_url;
	}
	
	/**
	  * Tabulate::EscapeString is used internally to escape strings when building a SQL statement.
	  *
	  * @access private
	  * @param str $data
	  * @return str
	  **/
	Function EscapeString($data){
		
		if (get_magic_quotes_gpc()){
			$data = stripslashes($data);
		}
		
		$data = (is_numeric($data)) ? $data :  mysql_real_escape_string($data); // This will work with Mysql as numbers can be quoted, but will not work for postgres.
		
		return $data;
	}

	/**
	  * Tabulate::GetAttributeString returns a string of key values to use as HTML attributes
	  *
	  * @access private
	  * @param array $arrAttr
	  * @return str
	  **/
	Function GetAttributeString($arrAttr)
	{
		$_attr = '';
		
		foreach($arrAttr as $_k=>$_v){
			$_attr .= " $_k=\"$_v\"";
		}
		
		return $_attr;
	}
}
?>
