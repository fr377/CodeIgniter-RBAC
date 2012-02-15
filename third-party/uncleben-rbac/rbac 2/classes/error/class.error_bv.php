<?php
/**
 * error class is a simple error handler. 
 * Some of it's features are:
 * - Error logging (either to screen, to an email address or a file on the server, or all three.)
 * - Removes all duplicate errors before printing to screen
 * 
 * @author Ben Vautier <vhd@vhd.com.au>
 * @copyright 2005
 * @version 2005/09/19
 **/


class Error_bv
{
	var $mArrErrorContainer;
	
	var $mSeriousErrorsMessage; // stores the serious error message only (one message may contain several errors)
	
	/**
	  * Constructor
	  **/
	Function Error_bv(){
		
		$this->mArrErrorContainer = array('serious'=>array(), 'trivial'=>array()); // Initialise
		
		// Register our own error handling Function
		set_error_handler(array(&$this, "userErrorHandler"));
		
	}
	
	/**
	  * Error::IsError determines whether we have errors or not
	  *
	  * @access public
	  * @return bool
	  **/
	Function IsError(){
		
		$arr_err = $this->mArrErrorContainer;
		
		if ((count($arr_err['serious']) + count($arr_err['trivial'])) > 0){
			return TRUE;
		}
		else {
			return FALSE;
		}
	}
	
	/**
	  * Error::IsSeriousError determines whether we have serious errors or not (E_ERROR, E_USER_ERROR)
	  *
	  * @access public
	  * @return bool
	  **/
	Function IsSeriousError()
	{
		if (count($this->mArrErrorContainer['serious']) > 0){
			return TRUE;
		}
		else {
			return FALSE;
		}
	}
	
	/**
	  * Error::GetErrors will return ALL errors with duplicates removed.
	  *
	  * All serious errors are also stored in a container for later processing
	  *
	  * @access public
	  * @return str
	  **/
	Function GetErrors(){
		
		$_errors = str_repeat('!', 50)."\r\n"; // Initialise 
		$serious_errors = str_repeat('!', 50)."\r\n"; // Initialise
		
		$arr_err = $this->mArrErrorContainer;
		
		foreach ($arr_err as $err_type=>$arr_err_log){
			
			// Find out the number of times an error occured
			$arr_errors = array_count_values($arr_err_log);
			
			// Generate error string
			foreach ($arr_errors as $err_msg=>$_num){
				
				// if the num is > 1 then make it bold
				$_num =($_num > 1) ? "$_num": $_num;
				
				$temp_error = preg_replace('/^.*?>/', "$0 Occurrence: ".$_num.' - ', $err_msg); // Place the Occurrence in the div tag
				$_errors .=  $temp_error."\r\n";
				
				// Keep serious error messages in a seperate container
				if ($err_type === 'serious'){
					$serious_errors .=  $temp_error."\r\n";
				}
			}
		}	
		
		$this->mSeriousErrorsMessage = $serious_errors.str_repeat('!', 50)."\r\n";
		
		return $_errors.str_repeat('!', 50)."\r\n";
	}
	
	/**
	  * Error::GetSeriousErrors will return serious errors ONLY with duplicates removed.
	  *
	  * @access public
	  * @return str
	  **/
	Function GetSeriousErrors()
	{
		if (trim($this->mSeriousErrorsMessage) == ''){
			$this->GetErrors();
		}
		
		return $this->mSeriousErrorsMessage;
	}

	/**
	  * Error::logErrors will log the error to file and send an email to the administrator if set
	  *
	  * @access public
	  * @param str $extra_msg
	  **/
	Function LogErrors($extra_msg = ''){
	
		$is_serious_errors = $this->IsSeriousError();
		
		// save to the error log, and e-mail me if there is a critical user error
		if ($is_serious_errors){
			
			error_log($this->getSeriousErrors(), 3, LOG_ERROR_FILE);
			
			if (ADMIN_EMAIL != '') {
				
				$_message = $this->getErrors()."\r\n".$extra_msg; // This will return ALL errors
				
			    mail(ADMIN_EMAIL, "Critical User Error on ".DOMAIN, $_message);
		   } 
		}
	}
	
	// user defined error handling Function
	Function userErrorHandler($errno, $errmsg, $filename, $linenum, $vars)
	{
		
		// timestamp for the error entry
		$dt = date('Y-m-d H:i:s (T)');
		
		// define an assoc array of error string
		// in reality the only entries we should
		// consider are E_WARNING, E_NOTICE, E_USER_ERROR,
		// E_USER_WARNING and E_USER_NOTICE
		$errortype = array (
			E_ERROR          	=> 'Error',
			E_WARNING        	=> 'Warning',
			E_PARSE          	=> 'Parsing Error',
			E_NOTICE          	=> 'Notice',
			E_CORE_ERROR      	=> 'Core Error',
			E_CORE_WARNING    	=> 'Core Warning',
			E_COMPILE_ERROR  	=> 'Compile Error',
			E_COMPILE_WARNING 	=> 'Compile Warning',
			E_USER_ERROR      	=> 'User Error',
			E_USER_WARNING    	=> 'User Warning',
			E_USER_NOTICE    	=> 'User Notice',
			E_STRICT          	=> 'Runtime Notice'
		);
		
		// Ignore Runtime notices in PHP 5 for now.
		if (in_array($errno, array(E_STRICT))){
			return;
		}
		
		//----------------------------------------------
		// Deal with user errors slightly differently.
		// We need to change the filename and line number the original error occured on.
		$user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);
		
		if (in_array($errno, $user_errors)) {
			//$err .= "\t<vartrace>" . wddx_serialize_value($vars, "Variables") . "</vartrace>\r\r\n";
			
			$arr_debug = debug_backtrace();
			
			// Get the file and line number the error was created. It is the last entry in the $arr_debug array.
			$arr_error = array_pop($arr_debug);
			
			// reassign the $filename and $linenum variables
			$filename = $arr_error['file'];
			$linenum = $arr_error['line'];
			
			//var_dump($arr_debug);
		}
		
		//----------------------------------------------
		// General error handling is done here.
		
		if (isset($errortype[$errno])){
			$_err = "$dt  {$errortype[$errno]}: $errmsg, $filename Line: $linenum";
		}
		else{
			$_err = "$dt  No error type available $errmsg, $filename Line: $linenum";
		}
		
		// Use color code for different messages
		switch ($errno){
			case E_ERROR:
				$_err = "E> $_err";
				break;
			
			case E_WARNING:
				$_err = "E - - > $_err";
				break;
			
			case E_NOTICE:
				$_err = "E - - - - > $_err";
				break;
			
			case E_STRICT:
				$_err = "E - - - - - - > $_err";
				break;
			
			case E_USER_ERROR:
				$_err = "U> $_err";
				break;
			
			case E_USER_WARNING:
				$_err = "U - - > $_err";
				break;
			
			case E_USER_NOTICE:
				$_err = "U - - - - > $_err";
				break;
				
			default :
				$_err = "> $_err";
				break;
		}
		
		//------------------------------------------
		// Store errors in an array
		if (in_array($errno, array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR))){
			$this->mArrErrorContainer['serious'][] = $_err;
		}
		else {
			$this->mArrErrorContainer['trivial'][] = $_err;
		}
	}
}


?>
