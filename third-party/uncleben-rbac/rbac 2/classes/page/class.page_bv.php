<?php

/**
 * Page class enables database, error logging and ouput capabilities.
 *
 *
 * @Author Ben Vautier <vhd@vhd.com.au>
 * @Copyright 2005
 */


class Page_bv
{	
	var $mConn; // database connection object
	
	var $mError; // error handling object
	
	var $mCompressOutputBuffer; // Whether to use "compressed output buffer"
	
	var $mContainer;// Holds the outputted HTML for post processing if necessary
	
	var $mArrPost; // Stores the cleaned up _POST array (trimmed, quotes stripped)
	var $mArrGet; // Stores the cleaned up _GET array (trimmed, quotes stripped)
	var $mArrServer; // Stores the cleaned up _SERVER array (trimmed, quotes stripped)
	var $mArrFiles; // Stores the cleaned up _FILES array (trimmed, quotes stripped)
	
	var $mTpl;
	
	var $mArrAttributes;
	
	var $mReferer; // Keeps track of the referring page
	
	/**
	 * Constructor
	 *
	 * Initialise the db connection, the error handler and the user object.
	 **/
	function Page_bv($arrAttributes = array()){
		
		//ob_start();
		session_start();
		
		//-----------------
		// Instantiate Error handling
		include_once CLASSES_DIR.'error/class.error_bv.php';
		$this->mError =& new error_bv();
		
		//-------------------------
		// removes slashes, trims and stores POST variables in mArrPost, GET vars in mArrGet
		$this->CleanData(); 
		$this->GetData();
		
		//-------------------------
		// Initialise some variables
		$this->mCompressOutputBuffer = TRUE;
		
		//-----------------
		// Instantiate db connection (which can be used by all children classes.)
		include_once CLASSES_DIR.'db/class.db_bv.php';
		$this->mConn =& new db_bv();
		
		$this->SetPageAttributes($arrAttributes);
		
		//-----------------
		// Keep track of the referer
		$this->UpdateReferer();
		
		//-------------------
		// Check whether the page is a target. If not clear the redirection variables stored in session
		if(!$this->IsTarget()){
			if (isset($_SESSION['page_bv']['redirect'])){
				unset($_SESSION['page_bv']['redirect']);
			}
			
			if (isset($_SESSION['page_bv']['redirect_previous'])){
				unset($_SESSION['page_bv']['redirect_previous']);
			}
		}
		
		//print_r($_SESSION);
	}
	
	//================================================================
	// Experimental code
	
	// We are trying to add a bit more "state machine" to our class
	
	// Store some infromation in session to know where we come from
	Function SendTo($to, $arrData = array())
	{
		
		// We want to keep information from previous pages if it exists
		if (isset($_SESSION['page_bv']['redirect']['_from_'])){
			
			// The latest page will always be first.
			$_SESSION['page_bv']['redirect_previous'][] = $_SESSION['page_bv']['redirect'];
			
			unset($_SESSION['page_bv']['redirect']);
			
		}
		
		$_SESSION['page_bv']['redirect']['_to_'] = DOMAIN.$to;
		$_SESSION['page_bv']['redirect']['_from_'] = $this->GetPage('url');
		
		foreach($arrData as $_k=>$_v){
			$_SESSION['page_bv']['redirect'][$_k] = $_v;
		}
		
		//--------------------------
		// Hack to make sure all the variables in the session are properly stored
		$session_temp = $_SESSION;
		session_write_close();
		
		session_start();
		$_SESSION= $session_temp;
		//-------------------------;
		
		header('Location: '.$to); exit;
	}
	
	// Iternal function for page_bv only.
	Function SendBack()
	{
		// If we were sent to this page using the SendTo method.
		if (isset($_SESSION['page_bv']['redirect']['_from_'])){
			
			$send_back_to = $_SESSION['page_bv']['redirect']['_from_'];
			
			unset($_SESSION['page_bv']['redirect']);
			
			// Check whether there is any previous information to where we are going back to.
			if (!empty($_SESSION['page_bv']['redirect_previous'])){
				$_SESSION['page_bv']['redirect'] = array_pop($_SESSION['page_bv']['redirect_previous']);
			}
			
			session_write_close();
			
			header('Location: '.$send_back_to); exit;
		}
		else { // We were not sent here using the SendTo method. Use normal referer data.
			
			if (isset($_SESSION['page_bv']['_referer_']) && $_SESSION['page_bv']['_referer_'] != ''){
				header('Location: '.$_SESSION['page_bv']['_referer_']); exit;
			}
			else {
				return FALSE;
			}
		}
	}
	
	// Determines whether a page is a meant to be hit or targeted
	Function IsTarget()
	{
		
		$_regex = '/'.basename($this->GetPage('script_name')).'/';
		
		if (isset($_SESSION['page_bv']['redirect']['_to_']) && preg_match ($_regex, $_SESSION['page_bv']['redirect']['_to_'])){
			return TRUE;
		}
		else{
			//echo "FALSE $_regex --- {$_SESSION['page_bv']['redirect']['_to_']}, From:{$_SESSION['page_bv']['redirect']['_from_']}";
			return FALSE;
		}
	}
	
	Function GetTargetData($key)
	{
		if (isset($_SESSION['page_bv']['redirect'][$key])){
			return $_SESSION['page_bv']['redirect'][$key];
		}
		else {
			return FALSE;
		}
	}
	
	//================================================================
	
	/**
	  *
	  *
	  **/
	Function CleanData()
	{
		
		include_once CLASSES_DIR.'filter/class.filter_bv.php';
		
		$_filter = &new filter_bv();
		
		$is_magic_quotes_on = FALSE;
		
		if (get_magic_quotes_gpc()){
			$is_magic_quotes_on = TRUE;
			define('MAGIC_QUOTES_OFF', TRUE);
		}
		
		$_input = array( &$_GET,  &$_POST,  &$_COOKIE,  &$_ENV,  &$_SERVER, &$_FILES);
		
		while (list($_k, $_v) = each($_input)){
			foreach($_v as $_key => $_val){
				if (!is_array($_val)){
					
					if ($is_magic_quotes_on){
						$_val = stripslashes($_val);
					}
					
					$_input[$_k][$_key] = $_filter->CleanUp($_val);
					
					continue;
				}
				
				$_input[] = & $_input[$_k][$_key];

			}
		}
		unset($_input);
	}
	
	Function GetData()
	{
		if (count($_POST) > 0){
			$this->mArrPost = $_POST;
			//unset($_POST);
		}
		
		if (count($_GET) > 0){
			$this->mArrGet = $_GET;
			//unset($_GET);
		}
		
		if (count($_FILES) > 0){
			$this->mArrFiles = $_FILES;
			//unset($_FILES;
		}
		
		
		$this->mArrServer = $_SERVER;
		//unset($_SERVER);
		
		//unset($_REQUEST);
	}
	
	Function Post($key = '')
	{
		if ($key == ''){
			return $this->mArrPost;
		}
		else {
			return (isset($this->mArrPost[$key])) ? $this->mArrPost[$key] : FALSE;
		}
	}
	
	Function Get($key = '')
	{
		if ($key == ''){
			return $this->mArrGet;
		}
		else {
			return (isset($this->mArrGet[$key])) ? $this->mArrGet[$key] : FALSE;
		}
	}
	
	Function Files($key = '')
	{
		if ($key == ''){
			return $this->mArrFiles;
		}
		else {
			return (isset($this->mArrFiles[$key])) ? $this->mArrFiles[$key] : FALSE;
		}
	}
	
	Function Server($key = '')
	{
		if ($key == ''){
			return $this->mArrServer;
		}
		else {
			return (isset($this->mArrServer[$key])) ? $this->mArrServer[$key] : FALSE;
		}
	}
	
	Function GetPage($key)
	{
		if (isset($this->mArrAttributes[$key])){
			return $this->mArrAttributes[$key];
		}
		
		switch ($key){
			
			case 'url':
					$query_string = ($this->Server('QUERY_STRING') == '') ? '' : '?'.$this->Server('QUERY_STRING');
					$_url = $this->Server('SCRIPT_NAME').$query_string;
					
					$this->mArrAttributes['url'] = $_url; // Set for next time. Won't have to query.
					return $_url;
				break;
			
			case 'referer':
					return $this->Server('HTTP_REFERER');
				break;
			
			case 'script_name':
					return $this->Server('SCRIPT_NAME');
				break;
				
			case 'folder':
				$_folder = preg_replace('#/[^/]*$#', '/', $this->Server('SCRIPT_NAME'));
				return $_folder;
				break;
				
			default :
				return FALSE;
		}
	}
	
	// Only update the _referer_ if the current page is different from the referer page
	Function UpdateReferer()
	{
		if ( strpos( trim($this->GetPage('referer')), trim($this->GetPage('url'))) === FALSE){
			$_SESSION['page_bv']['_referer_'] = $this->GetPage('referer');
		}
	}
	
	Function SetPageAttributes($arrAttr)
	{
		$this->mArrAttributes = $arrAttr;
		
		if (isset($arrAttr['page_template'])){
			 $this->SetTemplate($arrAttr['page_template']);
		}
	}
	
	Function SetTemplate($templateFile)
	{
		include_once CLASSES_DIR.'template/class.template_bv.php';
		
		$this->mTpl = & new template_bv( $templateFile);
	}
	
	/**
	 * Page::PrintPage() sends the page output to screen.
	 *
	 * This function allows us to process the HTML before it is printed to screen, depending on whether we
	 * are in DEBUG mode or not, or whether any errors were encountered during the script execution.
	 *
	 * @access public
	 **/
	Function PrintPage($html=''){
		
		if (isset($this->mTpl)){
			$html = $html.$this->mTpl->PrintTemplate();
		}
		
		if (DEBUG){
		
			// This is not very pretty but it works. Only used in debugging mode.
			if ($this->mError->IsError()){
				
				$error_msgs = $this->mError->GetErrors();
				
				if ($this->mError->IsSeriousError()){
					$this->mError->LogErrors($html);
				}
				
				$error_msgs = nl2br($error_msgs);
				$html = $error_msgs.$html;
				
				$this->PrintToScreen( $html );
			}
			else{
				$this->PrintToScreen( $html );
			}
		}
		else{ // No debugging. Production Ready
			
			// If there are some errors do not show content to screen.
			// Rather redirect user to error page and let them know we will fix the problem. (Perhaps they can try again in a few minutes.)
			if ($this->mError->IsSeriousError()){
			
				// Log error to the file defined in the config.php file as well as an email to the webmaster, if set.
				$this->mError->LogErrors($html);
				
				// send user to 404 page.
				// or some error message
				echo "The page has was unable to be processed without errors. This error has been logged and will be
				sent to the administrator.";
			}
			else { // No serious errors
				$this->PrintToScreen( $html );
			}
		}
		
	}
	
	/**
	 * Page::PrintToScreen() will simply print the HTML to screen.
	 *
	 * Compressed output buffering can also be specified.
	 *
	 * @access public
	 **/
	Function PrintToScreen($html){
		
		$this->mContainer = $html;
		
		if ($this->mCompressOutputBuffer){
			ob_start("ob_gzhandler");
			
			echo $html;
		}
		else
		{
			echo $html;
		}
	}
	
	//==========================
	// Template functions
	
	Function Assign($token, $content)
	{
		if (!isset($this->mTpl)){
			trigger_error('No template is set. Please use the SetTemplate method first.', E_USER_ERROR);
		}
		else {
			$this->mTpl->Assign($token, $content);
		}
	}
	
	Function Append($token, $content)
	{
		if (!isset($this->mTpl)){
			trigger_error('No template is set. Please use the SetTemplate method first.', E_USER_ERROR);
		}
		else {
			$this->mTpl->Append($token, $content);
		}
	}
	
	Function Prepend($token, $content)
	{
		if (!isset($this->mTpl)){
			trigger_error('No template is set. Please use the SetTemplate method first.', E_USER_ERROR);
		}
		else {
			$this->mTpl->Prepend($token, $content);
		}
	}
	
	// Retrieve all the valid token names from the template
	Function GetAllTokens()
	{
		preg_match_all('/\[#\](<[A-Z0-9_]*>)\[#\]/', $this->mTpl->mTemplateContent, $_matches);
		
		return array_unique($_matches[1]);
	}
}

?>