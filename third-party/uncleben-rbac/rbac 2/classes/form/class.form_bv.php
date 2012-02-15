<?php
/**
 * The Form class extends the table class with methods that are specific to HTML forms.
 * 
 * The Form class allows the user to set the form attributes, parse the form definition array
 * and finally return the completed form (Forms are always built inside a HTML table.)
 *
 * It will also validate the form.
 *
 * Froms will not be allowed to be submitted twice.
 *
 * SECURITY:
 * Ever time a form is generated an ID is saved in the SESSION variable. In order for the data to validate
 * the ID in the sessions have to match. The ID is also sent with the form as a hidden field.
 *
 * Functions used in this class from parent 'Table' class are:
 * - setCellContent
 * - setCellAttributes
 * - printTable
 * Furthermore the  'setTableAttributes'from the 'Table' class can also be called from the main script.
 *
 * @author Ben vautier <vhd at vhd.com.au>
 * @copyright 2005
 * @version 2.01
 * @license BSD or LGPL
 *
 * CHANGELOG:
 *	- No need to specify column and row position. It is all done automatically now.
 *  - Change the form definition to layout the form automatically.
 *  - Added 'alphanum', and 'maxlength' filters.
 *
 **/

include_once CLASSES_DIR.'table/class.table.php';

class Form_bv extends Table
{
	// Form attributes
	var $mArrFormAttr; // Form attributes
	var $mFormId; // A form identification number to stop brute force attacks
	
	var $mArrHeader; // Container for HEAD info, especially for calendar.
	
	var $mArrInputs; // Input definition container
	var $mArrRules; // Rule definition container
	
	var $mIsValidateSuccess ; // We start with no validation errors.
	var $mArrValidateErrors; // Stores the errors when validating the form
	var $mArrFormData; 	// Store relevant data submitted by form to be inserted into database $mArrFormData[table name][field name]
	var $mArrData; // This stores the data that is passed to the validate method with real names for the posted variables instead of random names.
	var $mIsScrambled;// Whether the form input names are scrambled or not.
	var $mArrFilesData; // Stores the file upload information if any. All multiple field names will be assigned to their reference name.
	
	var $mScriptNameHash;
	
	var $mFormTemplate;
	var $mTpl; // stores the template object
	var $mArrTplContainer; // Store tokens and input attributes (or content) in an associative array
	
	var $mCol;
	var $mRow;
	
	// Constructor
	Function Form_bv($arrAttr = array(), $isScrambled = TRUE){
		
		$this->mArrHeader = array(); // Initialise
		$this->mArrFormAttr = $arrAttr;
		$this->mIsValidateSuccess = TRUE; // Start with no validation errors
		$this->SetHead('<link href="'.CSS_DIR.'form.css" rel="stylesheet" type="text/css">');
		
		$this->mIsScrambled = $isScrambled;
		
		parent::Table();
		
		$this->mCol = 0;
		$this->mRow = 0;
		
		$this->mArrFilesData = array(); // Initialise in case
		
		if (!isset($this->mArrFormAttr['name']) || $this->mArrFormAttr['name'] == ''){
			trigger_error('A unique form name must be specified', E_USER_ERROR);
		}
		
		// Need to start session to store the form Id.
		//@session_start();
		
		//-----------------
		// generate a new form id if there is none
		
		$this->mScriptNameHash = md5($_SERVER['SCRIPT_NAME']);
		
		$this->SetFormId();
		
	}
	
	Function SetFormId()
	{
		if (!isset($_SESSION['form_bv'][$this->mScriptNameHash][$this->mArrFormAttr['name']]['frm_id'])){
			$_SESSION['form_bv'][$this->mScriptNameHash][$this->mArrFormAttr['name']]['frm_id'] = md5(uniqid(rand(), TRUE));
		}
		
		$this->mFormId = $_SESSION['form_bv'][$this->mScriptNameHash][$this->mArrFormAttr['name']]['frm_id'];
	}
	
	
	
	/**
	 * Form::SetFormAttribute() set's the form attributes for the form. i.e. Action, Method, Name and 
	 * Attribtues
	 *
	 * @access Public
	 * @param arr $arrAttr 
	 **/
	Function SetFormAttribute($k, $v){
		$this->mArrFormAttr[$k] = $v;
	}
	
	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	
	Function AddRow(){
		$this->mRow = parent::AddRow();
		
		// Reset column count
		$this->mCol = 0;
	}
	
	Function AddCell($content)
	{
		$this->mCol++;
		$this->SetCellContent($this->mRow, $this->mCol, $content);
	}
	
	
	Function SetCellAttributes($arr)
	{
		parent::SetCellAttributes($this->mRow, $this->mCol, $arr);
	}
	
	Function SetInputRules($inputName, $arrRules)
	{
		$this->mArrRules[$inputName] = $arrRules;
	}
	
	Function Assign($token, $content)
	{
		if (isset($this->mArrTplContainer[$token])){
			unset($this->mArrTplContainer[$token]);
		}
		
		$this->mArrTplContainer[$token][] = $content;
	}
	
	Function Append($token, $content)
	{
		$this->mArrTplContainer[$token][] = $content;
	}
	
	Function AppendAfter($token, $content, $pos){
		array_splice($this->mArrTplContainer[$token], $pos, 0, $content);
	}
	
	Function Prepend($token, $content)
	{
		array_unshift($this->mArrTplContainer[$token], $content);
	}
	
	Function AssignInput($token, $inputName, $arrInputAttr)
	{
		if (isset($this->mArrTplContainer[$token])){
			unset($this->mArrTplContainer[$token]);
		}
		
		$this->mArrTplContainer[$token][$inputName] = array();
		$this->mArrInputs[$inputName] = $arrInputAttr;
		
		if (!in_array($arrInputAttr['type'], array('submit'))){
			$lbl_token = substr($token, 0, -1). '_LBL>';
			
			if (!isset($arrInputAttr['alt'])){
				$labelTitle = ucfirst(str_replace('_', ' ', $inputName));
				$this->mArrInputs[$inputName]['alt'] = $labelTitle;
			}
			else {
				$labelTitle = $arrInputAttr['alt'];
			}
			
			$this->Assign($lbl_token, $labelTitle);
		}
	}
	
	Function AppendInput($token, $inputName, $arrInputAttr)
	{
		$this->mArrTplContainer[$token][$inputName] = array();
		$this->mArrInputs[$inputName] = $arrInputAttr;
	}
	
	Function PrependInput($token, $inputName, $arrInputAttr)
	{
		$this->mArrTplContainer[$token] = array($inputName=>array()) + $this->mArrTplContainer[$token];
		$this->mArrInputs[$inputName] = $arrInputAttr;
	}
	
	Function MergeTableForm()
	{
		$is_any_info = FALSE;
		
		// If there are random names already specified for this form unset them
		/*
		if (isset($_SESSION['form_bv'][$this->mArrFormAttr['name']]['names'])){
			//echo "Usetting names for '{$this->mArrFormAttr['name']}'";
			unset($_SESSION['form_bv'][$this->mArrFormAttr['name']]['names']);
			session_write_close();
			session_start();
		}
		*/
		
		if (isset($_SESSION['form_bv'])){
			
			unset($_SESSION['form_bv'][$this->mScriptNameHash][$this->mArrFormAttr['name']]['names']); 
			
			$arrTemp = $_SESSION['form_bv'][$this->mScriptNameHash]; // Keep the current page form info
			
			unset($_SESSION['form_bv']);
			
			$_SESSION['form_bv'][$this->mScriptNameHash] = $arrTemp;
		}
		
		while( list($_token, $arrContent) = each($this->mArrTplContainer)){
			
			foreach($arrContent as $_k=>$_v) {
				
				$is_error = FALSE;
				
				//----------------
				// Main processing
				if (is_array($_v)){ // we have an input
					$_info = ''; // Initialise
				
					if (isset($this->mArrInputs[$_k]['info'])){
						$_info = $this->mArrInputs[$_k]['info'];
						unset($this->mArrInputs[$_k]['info']); // Must unset so as not to conflict with the other attributes
					}
					
					//----------------
					// If a field is required place a star after the label
					if (isset($this->mArrRules[$_k]) && in_array('required', $this->mArrRules[$_k])){
						$lbl_token = substr($_token, 0, -1). '_LBL>';
						$this->Append($lbl_token, '<span style="color:red">*</span>');
					}
					
					//----------------
					// Check for errors
					if(isset($this->mArrValidateErrors[$_k])){
						$is_error = TRUE;
						$this->mArrInputs[$_k]['id'] = 'err'; // Set id tag
					}
					
					//--------------------------
					// Generate input code
					$this->mTpl->Append($_token, $this->GetInputHtml($_k, $this->mArrInputs[$_k]));
					
					//--------------------------------------------
					// Check whether there is some info for this input
					// Used to display a small icon to give the user directives.
					if ($_info != ''){
						$info_content = '<table border="0" cellpadding="0" cellspacing="0" style="display: inline;padding-left:5px;"><tr><td><img src="'.FORM_INFO_IMG.'" alt="Info - click me" title="'.$_info.'" onclick="javascript:alert(\''.$_info.'\');return false"></table>'; // Needed to put this inside a table to fit nicely with IE
						
						$this->mTpl->Append($_token, $info_content);
						$is_any_info = TRUE;
					}
					
					//-------------
					if($is_error){
						$error = $this->mArrValidateErrors[$_k];
						
						$error = '<img src="'.FORM_WARN_IMG.'" alt=" Error - click me." title="'.$error.'" onclick="javascript:alert(\''.$error.'\');return false">'; // Put message inside a picture.
						
						$error_content ='<table border="0" cellpadding="0" cellspacing="0" style="display:inline; padding-left:5px;"><tr><td class="err_img">'.$error.'</table>';
						
						$this->mTpl->Append($_token, $error_content);
					}
				}
				else{ // just content string
					$this->mTpl->Append($_token, $_v);
				}
			}
		}
		
		if ($is_any_info === TRUE){
			$this->mTpl->Append('<!NOTICE!>', '<div class="notice">Note: Please click on the icon(s) for more information. </div>');
		}
	}
	
	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	
	
	Function SetTemplate ($tpl){
		$this->mFormTemplate= $tpl;
	}
	
	/**
	 * Form::PrintForm Construct a form based on the form definition array
	 *
	 * @access Public
	 * @return str the form in a HTML table
	 **/
	Function PrintForm(){
		// ---------------------------------
		// Build Template
		include_once CLASSES_DIR.'template/class.template_bv.php';
		
		if ($this->mFormTemplate == ''){
			$this->mTpl = & new Template_bv($this->printTable(), TRUE);
			
			//echo $this->printTable();
		}
		else {
			$this->mTpl = & new Template_bv($this->mFormTemplate, TRUE);
		}
		
		
		// check if validation was a succes. By default this is TRUE and will only be FALSE if a user submitted a form that did not validate. We want to re-inject the data they typed in so they don't have to do it again (except for passwords).
		if (!$this->mIsValidateSuccess){
			
			$this->SetInputAttribute('value', $this->Get());
			
			$error_msg = '';
			foreach ($this->mArrValidateErrors as $_k=>$_msg){
				$error_msg .= $this->mArrInputs[$_k]['alt'] . ': ' . $_msg . '<br/>';
			}
			
			$this->Assign('<!NOTICE!>', '<div class="notice_error">The following field(s) have an error: <br/><br/>'.$error_msg.'</div>');
		}
		
		$this->AppendInput('<!BUTTON!>','frm_id',array('type'=>'hidden', 'value'=>$this->mFormId));
		
		$this->MergeTableForm();
		
		// Put the form together
		$form_html = '<FORM '.$this->CollapseArray($this->mArrFormAttr).">\n";
		$form_html .= $this->mTpl->PrintTemplate();
		$form_html .='</FORM>';
		
		return $form_html;
	}
	
	
	/**
	 * Form::GetInputHtml will return the HTML for a given input specification
	 *
	 * @access Private
	 *
	 * @param str $name Name of the input field
	 * @param arr $arr Contains the attributes of the field.
	 * @return formatted HTML for the given input type.
	 **/
	Function GetInputHtml($name, $arr){
		
		// Look to see if a name key is defined in $arr. If it is not then use $name as provided.
		if (isset($arr['name'])){
			$name = $arr['name'];
			unset($arr['name']);
		}
		
		if ($this->mIsScrambled){
			// Create a random name and store it in session
			$fld_name = md5(uniqid(rand(), true));
			
			if (preg_match('/\[\]$/', $name)){ // check whether we have a multiple field name finishing with []
				
				$fld_name  .= '[]';
				
				// If the name was already scrambled reuse the scramble
				if (in_array($name, $_SESSION['form_bv'][$this->mScriptNameHash][$this->mArrFormAttr['name']]['names'])){
					$fld_name = array_search($name, $_SESSION['form_bv'][$this->mScriptNameHash][$this->mArrFormAttr['name']]['names']);
				}
			}
			
			$_SESSION['form_bv'][$this->mScriptNameHash][$this->mArrFormAttr['name']]['names'][$fld_name] = $name;
		}
		else{
			$fld_name = $name;
		}
		
		$html = ''; // keep HTML
		
		switch (TRUE){
			
			case in_array($arr['type'], array('text','submit','checkbox','password', 'radio', 'hidden')):
				$html = '<INPUT name="'.$fld_name.'" '.$this->collapseArray($arr).'>';
				
				break;
			
			case 'file' === $arr['type']:
				$html = '<INPUT name="'.$fld_name.'" '.$this->collapseArray($arr).'>';
				
				$this->SetFormAttribute('enctype', 'multipart/form-data');
				break;
			
			case 'calendar' === $arr['type']:
			
				require_once JSCALENDAR_DIR.'class.calendar.php';
				
				$calendar = new DHTML_Calendar(JSCALENDAR_URL, 'en', 'skins/aqua/theme', TRUE);
				
				if (!isset($arr['value'])){
					$value = date('Y-m-d H:i:s');
				}
				else{
					$value = $arr['value'];
				}
				
				$html = $calendar->make_input_field(
					// calendar options go here; see the documentation and/or calendar-setup.js
					array('firstDay'       => 1, // show Monday first
						 'showsTime'      => true,
						 'showOthers'     => true,
						 'ifFormat'       => '%Y-%m-%d %H:%M:%S',
						 'timeFormat'     => '24'),
					// field attributes go here
					array('style'       => 'width: 13em; border: 1px solid #000; text-align: center; background-color: #F4F4F0;',
						 'name'        => $fld_name,
						 'value'       => $value,
						 'readonly'    => ''
						 )
					);
					
				$this->SetHead($calendar->get_head_files(FALSE));
				
				break;
				
			case 'textarea' === $arr['type'] :
			
				// Get the content for the text area.
				(isset($arr['value'])) ? $_content = $arr['value'] : $_content = '';
				unset($arr['value']); // Unset the value tag, since it is no longer needed
				
				$html = '<TEXTAREA name="'.$fld_name.'" '.$this->collapseArray($arr).'>'.$_content.'</TEXTAREA>';
				break;
				
			case 'combo' === $arr['type'] :
				
				$arr_select_value = array(); // Initialise
				
				// check to see if a value has been selected
				if (isset($arr['value'])){
					if (is_array($arr['value'])){
						$arr_select_value = $arr['value'];
					}
					else {
						($arr['value'] != '') ? $arr_select_value[] = $arr['value'] : '';
					}
				}
				
				if (!is_array($arr['options'])){ // We have an SQL statement
					
					include_once CLASSES_DIR.'db/class.db_bv.php';
					
					$_db = & new db_bv();
					
					$_sql = $arr['options'];
					
					$arr['options'] = $this->buildComboArray($_db->GetAll($_sql));
					
				}
				
				// Generate the options html code
				$options_html = '';
				
				// If the 'first' key is set, include the corresponding value as the first entry.
				// The passed value will be blank
				isset($arr['first']) ? $options_html .= '<OPTION value="">'.$arr['first'].'</OPTION>' : '';
				
				foreach ($arr['options'] as $key=>$value)
				{
					// if the ids key is set we want to retrun the options with values
					if (isset($arr['ids']))
					{
						// If selected key is the same as the key being processed
						(in_array($key, $arr_select_value)) ? $select_html = ' SELECTED': $select_html = '';
						
						$options_html .= '<OPTION value="'.$key.'"'.$select_html.'>'.$value.'</OPTION>';
					}
					else
					{
						// If selected value is the same as the value being processed
						(in_array($value, $arr_select_value)) ? $select_html = ' SELECTED': $select_html = '';
						
						$options_html .= '<OPTION'.$select_html.'>'.$value.'</OPTION>';
					}
				}
				
				// Unset entries in the array that do not have a 0ne-to-0ne mapping in the HTML domain.
				unset($arr['first']);
				unset($arr['ids']);
				unset($arr['value']);
				unset($arr['options']);
				
				$html = '<SELECT name="'.$fld_name.'" '.$this->collapseArray($arr).'/>';
				$html .= $options_html;
				$html .= '</SELECT>';
				break;
				
			default:
				echo "You must specify an input type for $name in the array definition, sublevel 'input'";
				break;
		}
		
		return $html;
	}
	
	
	/**
	 * Form::collapseArray will collapse the key value pairs into a comma seperated string of key = value
	 *
	 * @access Private
	 * @param arr $arr
	 * @return str
	 **/
	Function collapseArray($arr){
		
		$str = '';
		
		foreach ($arr as $k=>$v){
			
			if (trim($v) != ''){
				$str .= "$k=\"$v\" ";
			}
			else{
				$str .= "$k ";
			}
		}
		
		return $str;
	}
	
	
	/**
	 * Form::buildComboArray Manipulates a Record Set array (associative array) obtained from an SQL query and turns 
	 * it into a one dimensional array ready to be used as the options of a combo box.
	 *
	 * @access private
	 * @param arr $arr Record set array
	 * @param str $vName field name for the value
	 * @param str $kName field name for the key (optional)
	 * @return a one dimensional array with key=>value pairs
	 **/
	Function buildComboArray($arr, $vName = '', $kName = ''){
		
		$arr_store = array(); // initialise array
		
		if ($vName == '' && $kName == ''){
			
			foreach ($arr as $arr_row){
				
				$cnt = 1;
				
				foreach($arr_row as $_k=>$_v){
					if (is_int($_k)){continue;}
					
					if ($cnt === 1){
						$v_value = $_v;
					}
					else{
						$k_value = $_v;
					}
					
					$cnt++;
				}
				
				if ($cnt === 2){
					$k_value = $v_value;
				}
				
				$arr_store[$k_value] = $v_value;
			}
		}
		else {
			// if kName is not set use vName instead
			(trim($kName) == '') ? $kName = $vName : '';
			
			foreach ($arr as $arr_row){
				$v_value = $arr_row[$vName];
				$k_value = $arr_row[$kName];
				
				$arr_store[$k_value] = $v_value;
			}
		}
		return $arr_store;
	}
	
	
	/**
	 * Form::SetInputAttribute modifies the 'input' array of the form array definition. 
	 *
	 * This can be used to add, or replace values of any attributes.
	 *
	 * @access public
	 * @param str $attr attribute (e.g. value, maxlength, size)
	 * @param arr $arr array with key value pairs, 'field name'=>'value'
	 * @return void
	 *
	 **/
	Function SetInputAttribute($attr, $arr)
	{
		foreach ($arr as $_fld=>$_v){
			
			// If $_v is empty - skip
			if ('' == trim($_v) || '' == trim($_fld)){continue;}
			
			// Ensure that the field is in the input definition otherwise skip.
			if (!in_array($_fld, array_keys($this->mArrInputs))){
				continue;
			}
			
			// If we only want to update the values.
			if ($attr === 'value'){
				
				// If we have a password field we will skip it
				if ('password' === $this->mArrInputs[$_fld]['type']){continue;}
				
				if ('checkbox' === $this->mArrInputs[$_fld]['type']){ // If we have a check box that is enabled. Check boxes need to be dealt with differently
					
					if ((int) $_v === 1 || strtolower($_v) === 'on'){ // if checked  (1 would come from database, and 'on from form submission.)
							$this->mArrInputs[$_fld]['checked'] = '';
					} else { //it is 0 or not set, so unset.
						unset($this->mArrInputs[$_fld]['checked']);
					}
				}
				else if ('radio' !== $this->mArrInputs[$_fld]['type']){ // we have and INPUT type widget
					$this->mArrInputs[$_fld][$attr] = $_v;
				}
				else{
					$this->mArrInputs[$_fld]['value'] = $_v;
				}
			}
			else { // for other attributes 
				$this->mArrInputs[$_fld][$attr] = $_v;
			}
		
			// We now need to check wether a combo box which is by default selected was unselected, 
			// so that we leave it unselected.
			// We also need to check radio buttons
			
			$arr_attr = $this->mArrInputs[$_fld];
			
			if ($attr == 'value' && ('checkbox' == $arr_attr['type'] || 'radio' == $arr_attr['type']) ){
				foreach ($this->mArrInputs as $_fld=>$arr_attr){
					
					// If $arr_attr has a 'name' tag use that for the $_fld
					if (isset($arr_attr['name'])){
						$fld_original = $_fld; // We need to keep a temp value for the radio button processing
						$_fld = $arr_attr['name'];
					}
					
					//-----------------------------
					// CHECKBOX
					if ('checkbox' == $arr_attr['type'] && isset($arr_attr['checked']) && !in_array($_fld, array_keys($arr))){
						unset($this->mArrInputs[$_fld]['checked']);
					}
					//-----------------------------
					// RADIO BUTTONS
					// If we have a radio button and a corresponding value
					else if ('radio' == $arr_attr['type'] && isset($arr[$_fld])){
						// if there is a value then place a checked
						if ($arr_attr['value'] == $arr[$_fld]){
							$this->mArrInputs[$fld_original]['checked'] = '';
						}
						else {
							//if it is checked by default remove
							if (isset($arr_attr['checked'])){
								unset($this->mArrInputs[$fld_original]['checked']);
							}
						}
					}
				}
			}
		}
	}
	
	//========================================================
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
	//================================================================================================
	// FORM DATABASE preperation methods
	
	/**
	  * Form::GetDbData will return an array of data ready to be inserted into the database.
	  *
	  * The data has already been process and if mIsValidateSuccess is TRUE then the data should be valid.
	  *
	  * @access Public
	  * @param str $table database table we want the data for
	  * @return array
	  **/
	Function GetDbData($table)
	{
		// Make sure that the given table exists in the definition.
		if (isset($this->mArrFormData[$table])){
			
			return $this->mArrFormData[$table];
		}
		else{
			trigger_error("No database table defined by the name: $table", E_USER_ERROR);
			
			return FALSE;
		}
		
	}
	
	//================================================================================================
	// FORM VALIDATION RULES - This could be in another class of it's own but left it here because it 
	// is still nice to have everything related to forms in one place.
	
	Function IsPosted($arrFormData)
	{
		if ($this->mIsScrambled){ // form names are scrambled
			
			if (!isset($_SESSION['form_bv'][$this->mScriptNameHash][$this->mArrFormAttr['name']]['names']) || !is_array($_SESSION['form_bv'][$this->mScriptNameHash][$this->mArrFormAttr['name']]['names'])){
				return FALSE;
			}
			
			$frm_id_name = array_search('frm_id', $_SESSION['form_bv'][$this->mScriptNameHash][$this->mArrFormAttr['name']]['names']);
			
			if ($frm_id_name === FALSE){
				return FALSE;
			}
		}
		else {
			$frm_id_name = 'frm_id';
		}
		
		
		if (isset($_SESSION['form_bv'][$this->mScriptNameHash][$this->mArrFormAttr['name']]['frm_id']) && isset($arrFormData[$frm_id_name]) && $arrFormData[$frm_id_name] === $_SESSION['form_bv'][$this->mScriptNameHash][$this->mArrFormAttr['name']]['frm_id']){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/**
	 * Form::validate will take and array of data and validate it agains the form validation definition, and store relevant
	 * data in an array ready to be inserted into a database.
	 *
	 * It will also extract the relevant information and store it in $mArrFormData. Some of the submitted form data needs
	 * to be processed first. (i.e. combo boxes that don't have a selection need to be pruned, check boxes that are checked
	 * will have a value of 1.)
	 *
	 * @access Public
	 * @param  arr $arrFormData (data from POST or GET)
	 * @return bool TRUE if no errors were found, FALSE otherwise. It also updates the class variable $mArrValidateErrors
	 **/
	Function validate($arrFormData, $arrFilesData = array()){
		
		// Get the fields that were used in the form definition
		$arr_inputs = $this->mArrInputs;
		
		if (!$this->IsPosted($arrFormData)){ // Don't validate if not posted
			$this->mIsValidateSuccess = TRUE;
			
			return FALSE;
		}
		
		//--------------------------------
		if ($this->mIsScrambled){ // Convert the random names back to their original names
			
			foreach($_SESSION['form_bv'][$this->mScriptNameHash][$this->mArrFormAttr['name']]['names'] as $random_name=> $name){
				
				
				// Check whether we have a multiple field name finishing with []
				if (preg_match('/\[\]$/', $name)){
					$random_name = rtrim($random_name, '[]'); // remove trailing []
					$name = rtrim($name, '[]');
				}
				
				if (isset($arrFormData[$random_name])){ // Check boxes if not selected will not be set.
					$arrFormData[$name] = $arrFormData[$random_name];
					unset($arrFormData[$random_name]);
				}
				else if (isset($arrFilesData[$random_name])){ // process files
					$arrFilesData[$name] = $arrFilesData[$random_name];
					unset($arrFilesData[$random_name]);
				}
			}
			
			// Remove the random name mapping from session.
			unset ($_SESSION['form_bv'][$this->mScriptNameHash][$this->mArrFormAttr['name']]['names']);
		}
		
		//-----------------------------
		$this->mArrData = $arrFormData;
		
		// First check that the form id is in the SESSION variable and the it corresponds to the POST value
		if (!isset($_SESSION['form_bv'][$this->mScriptNameHash][$this->mArrFormAttr['name']]['frm_id']) || (isset($arrFormData['frm_id']) && $arrFormData['frm_id'] !== $_SESSION['form_bv'][$this->mScriptNameHash][$this->mArrFormAttr['name']]['frm_id'])){
			
			$this->mIsValidateSuccess = FALSE;
			
			if (DEBUG){
				if (!isset($_SESSION['form_bv'][$this->mScriptNameHash][$this->mArrFormAttr['name']]['frm_id'])){
					trigger_error('The session variable "frm_id" is not set', E_USER_NOTICE);
				}
				else{
					trigger_error('The form id posted does not match the session form id.', E_USER_NOTICE);
				}
			}
			
			// Unset the frm_id
			unset($_SESSION['form_bv'][$this->mScriptNameHash][$this->mArrFormAttr['name']]['frm_id']);
			
			return FALSE;
		}
		
		$arr_field_names_processed = array(); // Keep track of all the field name that have been processed.
		$arr_count = array(); // Count the number of multiple fileds that share the same field name if any.
		
		// Loop through the inputs
		foreach($arr_inputs as $fld_name_ref=>$arr_void){
			
			// TODO: If fld_name is an array we need to do more processing.
			
			// If $arr_void has a 'name' tag use that for the $fld_name_form
			// In most cases the field name reference in our form definition is the field name that will be in the form.
			// This will not be the case when we deal with inputs that will have the same field name finishing with [].
			// Unless the 'name' attribute is used
			// Errors must be logged agains the fld_name_ref.
			// The data from the submitted form will be saved against the fld_name_form
			if (isset($arr_void['name'])){
				$fld_name_form = rtrim($arr_void['name'], '[]');
			}
			else {
				$fld_name_form = $fld_name_ref;
			}
			
			// Get validation rules for the field from the array form definition.
			if (isset($this->mArrRules[$fld_name_ref])){
				$arr_rules = $this->mArrRules[$fld_name_ref];
			}
			else {
				$arr_rules ='' ;
			}
			
			// Process the input array. If there are any combo boxes with a blank value, unset them since the user has not selected anything. If another value than blank needs to be used change it in form::GetInputHtml
			foreach($arrFormData as $fld_frm_name=>$fld_frm_value){
				
				if (!isset($this->mArrInputs[$fld_frm_name])){continue;}
				
				if ('combo' === $this->mArrInputs[$fld_frm_name]['type'] && '' == $fld_frm_value){
					unset($arrFormData[$fld_frm_name]);
				}
			}
			
			// Get the data corresponding to a field name
			if (isset($arrFormData[$fld_name_form])){
				if (is_array($arrFormData[$fld_name_form])){
					$data = $arrFormData[$fld_name_form]; // we will have an array.
					// TODO: Process each item in the array.
					// for now we will just continue;
					continue;
				}
				else{
					$data = (trim($arrFormData[$fld_name_form]) !='') ? trim($arrFormData[$fld_name_form]) : '';
				}
				
				// TODO: this will be used when dealing with multiple file submissions
				// $arr_field_names_processed[] = $fld_name_form;
			}
			else if (isset($arrFilesData[$fld_name_form])){ // We have a file
				
				// This is to keep track of whether we have multiples or not
				if (in_array($fld_name_form, $arr_field_names_processed)){
					$arr_count[$fld_name_form]++;
				}
				else {
					$arr_count[$fld_name_form] = 0;
				}
				
				if ($arr_rules != ''){ // We have some rules
					$this->ValidateFileUpload($arr_rules, $arrFilesData[$fld_name_form], $fld_name_ref, $arr_count[$fld_name_form]);
				}
				
				$arr_field_names_processed[] = $fld_name_form;
				
				continue; // Since files are dealt with a bit differently
			}
			
			//--------------------------------
			// Look at whether we have a checkbox or not. If we do set it's value to 1.
			if ('checkbox' === $this->mArrInputs[$fld_name_ref]['type']){
				('on' === $data ) ? $data = 1 : $data = 0;
			}
			
			//--------------------------------
			// PASSWORD Look at whether we have a password or not. If we do MD5 it.
			if ('password' === $this->mArrInputs[$fld_name_ref]['type']){
				$regx_pswd = '/\W+/';
				if (preg_match($regx_pswd, $data) || strlen($data) <= 3){
					$this->mArrValidateErrors[$fld_name_ref] = 'Passwords must be alpha numeric and at least 4 characters long.'; 
				}
				else{ // The password is valid
					$data = md5($data);
				}
			}
			
			//--------------------------------
			// Store data in $mArrFormData
			if (trim($data) != '' && isset($arr_rules['table'])){
				$this->mArrFormData[$arr_rules['table']][$fld_name_ref] = $data; 
			}
			
			//----------------------------------------------
			// Actual validation starts here. First check that there are some validation rules to check, otherwise skip
			if(!is_array($arr_rules)){continue;}
			
			// NOTE: check for SAME_AS and REGEX first and then unset them so that it does not create a conflict
			// with the in_array() commnad. This may happen due to the fact that UNIQUE and REGEX use a key=>value pair
			// whith the kewords REGEX as the keys. Their corresponding values may conflict with the values
			// already in the array, therefore unset them
			
			// SAME_AS -----------------------------------------------
			// Check that the value of this field is the same as another field
			if (isset($arr_rules['same_as']) && $data != ''){
				
				$other_fld_name = $arr_rules['same_as'];
				
				if ('password' == $this->mArrInputs[$fld_name_ref]['type']){
					if ($data !== md5($arrFormData[$other_fld_name])){
						$this->mArrValidateErrors[$fld_name_ref] = 'Does not match';
					}
				} else {
					if ($data !== $arrFormData[$other_fld_name]){
						$this->mArrValidateErrors[$fld_name_ref] = 'Does not match';
					}
				}
				
				//Unset entry from rules array to avoid conflict.
				unset($arr_rules['same_as']);
			}
			
			// MAXLENGTH --------------------------------------------
			// Check the maximum length of the field
			if (isset($arr_rules['maxlength']) && $data != ''){
				
				if (strlen($data) > $arr_rules['maxlength']){
					$this->mArrValidateErrors[$fld_name_ref] = "Only allowed {$arr_rules['maxlength']} characters";
				}
				
				//Unset entry from rules array to avoid conflict.
				unset($arr_rules['maxlength']);
			}
			
			// REGEX ------------------------------------------------
			// check user defined regular expression
			if (isset($arr_rules['regex'])){
				
				$_regex = $arr_rules['regex'][0];
				$error_msg = $arr_rules['regex'][1];
				
				if (!preg_match($_regex, $data)){
					$this->mArrValidateErrors[$fld_name_ref] = $error_msg;
				}
				
				//Unset entry from rules array to avoid conflict.
				unset($arr_rules['regex']);
			}
			
			// UNIQUE ------------------------------------------------
			// check to see if an entry is unique
			if (in_array('unique', $arr_rules) && $data != ''){
				
				$db_table = $arr_rules['table']; // get the table name
				$conn = & new db_bv($db_table);
				
				//---------------------
				// If we are updating a record we must make sure that we do not check the current record
				// Get PK from form table.
				$arr_pk_fld = $conn->MetaPrimaryKeys();
				
				$sql_exclude_pk_fld = '';
				
				foreach ($arr_pk_fld as $pk_fld){
					
					// If the pk_fld is in the POST array we are updating a record
					if (isset($arrFormData[$pk_fld]) && $arrFormData[$pk_fld] !== '' ){
						$sql_exclude_pk_fld .= " AND $pk_fld <> $arrFormData[$pk_fld]";
					}
				}
				
				//---------------------
				
				$sql = "SELECT count(*) as cnt FROM $db_table WHERE $fld_name_form = '$data' $sql_exclude_pk_fld";
				$rs = $conn->getRow($sql);
				
				// See if there was a match. If there was a match return an error
				if ($rs['cnt'] > 0){
					$this->mArrValidateErrors[$fld_name_ref] = 'Value already taken'; 
				}
				
			}
			
			// REQUIRED ------------------------------------------------
			// The field is required but there is no data
			if (in_array('required',$arr_rules) && $data == '')
			{
				$this->mArrValidateErrors[$fld_name_ref] = 'Required field, do not leave blank'; 
			}
			
			// EMAIL ------------------------------------------------
			// Check to see if we have a valid email address
			$regx_email = '/^[a-zA-Z][\w\.-]*[a-zA-Z0-9]@[a-zA-Z0-9][\w\.-]*[a-zA-Z0-9]\.[a-zA-Z][a-zA-Z\.]*[a-zA-Z]$/';
			if (in_array('email',$arr_rules)){
				
				if (!preg_match($regx_email, $data)){ // Check format
					$this->mArrValidateErrors[$fld_name_ref] = 'Wrong email format'; 
				}
				/*
				else { // The email address has the right format. so check SMTP
					
					$HTTP_HOST = $_SERVER['HTTP_HOST'];
					
					// code taken from http://www.zend.com/zend/spotlight/ev12apr.php?print=1
					list ( $Username, $Domain ) = split ("@",$data);
					
					if (getmxrr($Domain, $MXHost))  {
						$ConnectAddress = $MXHost[0];
					} 
					else {
						$ConnectAddress = $Domain;
					}
					
					$Connect = fsockopen( $ConnectAddress, 25, $errno, $errstr, 5); // Timeout after 5 seconds
					
					if ($Connect) {
						
						if (ereg("^220", $Out = fgets($Connect, 1024))) {
							
							fputs ($Connect, "HELO $HTTP_HOST\r\n");
							$Out = fgets ( $Connect, 1024 );
							fputs ($Connect, "MAIL FROM: <{$data}>\r\n");
							$From = fgets ( $Connect, 1024 );
							fputs ($Connect, "RCPT TO: <{$data}>\r\n");
							$To = fgets ($Connect, 1024);
							fputs ($Connect, "QUIT\r\n");
							fclose($Connect);
							
							//echo "From message was: $From\n";
							//echo "To message was: $To\n";
							
							// From the little experimenting I have done it is better to only check the $To string
							// Out of Yahoo, Hotmail and Gmail, it looks like Yahoo will accept any type of address
							// as long as it finished with yahoo.com
							if (substr(trim($To), 0, 3) != '250') {
							   $this->mArrValidateErrors[$fld_name_ref] = 'Server rejected address';
							}
						} 
						else {
							$this->mArrValidateErrors[$fld_name_ref] = 'No response from server';
						}
					}  
					else {
						$this->mArrValidateErrors[$fld_name_ref] = 'Cannot connect to E-Mail server, Try another email address';
					} 
				}
				*/
			}
			
			// IP ------------------------------------------------
			// check to see if we have a valid IP address
			$regx_ip = '/\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b/';
			if (in_array('ip',$arr_rules) && !preg_match($regx_ip, $data)){
				$this->mArrValidateErrors[$fld_name_ref] = 'Invalid IP address'; 
			}
			
			// INT ------------------------------------------------
			// check to see if we have an integer
			$regx_int = '/^\d*$/';
			if (in_array('int',$arr_rules) && !preg_match($regx_int, $data)){
				$this->mArrValidateErrors[$fld_name_ref] = 'This field only accepts integer values'; 
			}
			
			
			// ALPHANUM -------------------------------------------
			// Make sure we only have alphabetic and numeric characters including _ and \s'
			$regx_alphanum = '/^[\w\s]*$/';
			if (in_array('alphanum',$arr_rules) && !preg_match($regx_alphanum, $data)){
				$this->mArrValidateErrors[$fld_name_ref] = 'Only alpha numeric characters are allowed'; 
			}
		}
		
		// See if any errors were found.
		if (count($this->mArrValidateErrors) > 0){
			
			$this->mIsValidateSuccess = FALSE;
			
			return FALSE;
		}
		else{ //No errors
			
			// Unset the frm_id
			// This will  stop a form from beeing submitted twice.
			unset($_SESSION['form_bv'][$this->mScriptNameHash][$this->mArrFormAttr['name']]['frm_id']);
			
			if ($this->mIsScrambled){
				unset($_SESSION['form_bv'][$this->mScriptNameHash][$this->mArrFormAttr['name']]['names']);
			}
			
			// Generate a new form ID
			$this->SetFormId();
			
			return TRUE;
		}
	}
	
	// This function is only called if  uploaded files need to be checked
	// idx would be used for multiple file uploads that use the same file name.
	Function ValidateFileUpload($arrRules, $arrData, $fieldNameRef, $idx=0)
	{
		// First find out if $arrData is a two dimensional array. If it is extract the proper index and store it as a one dimensioal array.
		
		$arr_data = array(); // data container
		
		foreach($arrData as $_k=>$_v){
			
			if (is_array($_v)) {
					$arr_data[$_k] = $_v[$idx];
			}
			else {
				$arr_data[$_k] = $_v;
			}
		}
		
		// Loop through the rules
		foreach($arrRules as $_k=>$_v){
			
			//-------------------
			// Check whether we have an array or not. Since some rules use a key=>array_value pair
			if (!is_array($_v)){
				
				//-----------------------------
				// REQUIRED
				if (strtolower($_v) === 'required'){
					if ( (int) $arr_data['size'] === 0){
						$this->mArrValidateErrors[$fieldNameRef]= 'This field is required. Please select a file.';
					}
				}
				
				//-----------------------------
				// FILE_SIZE
				if (strtolower($_k) === 'size'){
					if ( (int) $arr_data['size'] >  $_v){
						$this->mArrValidateErrors[$fieldNameRef]= "The file you uploaded is too large ({$arr_data['size']}). Make sure it is less than $_v bytes.";
					}
				}
			}
			else { // we have an array.
				
				//-----------------------------
				// EXTENSION
				if (strtolower($_k) === 'ext'){
					
					$_ext = preg_replace('/.*[.]/', '', $arr_data['name']); // remove everything up to (and including) the last dot.
					
					if ( !in_array($_ext, $_v)){
						$this->mArrValidateErrors[$fieldNameRef]= "Wrong file format. Only '".implode(', ', $_v)."' are allowed. You uploaded a '$_ext' file.";
					}
				}
				
			}
		}
		
		$this->mArrFilesData[$fieldNameRef] = $arr_data;
	}
	
	Function ProcessFile($nameRef, $destinationFolder, $newNameBody = '', $arrSize = array('x'=>'', 'y'=>''))
	{
		// check that the file $nameref exists
		if (!isset($this->mArrFilesData[$nameRef])){
			trigger_error('The file: '.$nameRef.' was not uploaded. Could not find it.', E_USER_NOTICE);
			return FALSE;
		}
		else {
			$arr_file_data = $this->mArrFilesData[$nameRef];
		}
		
		if ($newNameBody == ''){
			$newNameBody = preg_replace('/\.[^.]+$/', '', $arr_file_data['name']); // This will still have the extension.
		}
		
		// check that the destination path is writeable
		if (is_writeable($destinationFolder)) {
			
			include_once CLASSES_DIR.'form/class.upload.php';
			
			$_file = new upload($arr_file_data);
			
			if ($_file->uploaded) { // Does some checking
				
				$_file->file_new_name_body   = $newNameBody;
				$_file->jpeg_quality = 85;
				
				if ($arrSize['x'] != '' || $arrSize['y'] !=''){ // If we have some data, means we want to resize the image
					$_file->image_resize = TRUE;
					
					// X dimension
					if ($arrSize['x'] === 'auto'){
						$_file->image_ratio_x = TRUE;
					}
					else {
						$_file->image_x = (int) $arrSize['x'];
					}
					
					// Y dimension
					if ($arrSize['y'] === 'auto'){
						$_file->image_ratio_y = TRUE;
					}
					else {
						$_file->image_y = (int) $arrSize['y'];
					}
				}
				
				// Process the whole file
				$_file->process($destinationFolder);
			} 
			else {
				trigger_error('There seems to be a problem with the upload of the file '.$nameRef, E_USER_WARNING);
			}
			
			return $_file;
		}
		else{
			trigger_error('The destination folder: '.$destinationFolder.' is not writeable.', E_USER_WARNING);
		}
	}
	
	
	Function Get($fld_name ='')
	{
		
		if ($fld_name == ''){
			return $this->mArrData;
		}
		
		if (isset($this->mArrData[$fld_name])){
			return $this->mArrData[$fld_name];
		}
		else{
			return FALSE;
		}
	}
	
	Function SetError($fldName, $errMessage)
	{
		$this->mArrValidateErrors[$fldName] = $errMessage;
	}
}
?>
