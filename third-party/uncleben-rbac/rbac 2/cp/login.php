<?php

include_once 'config.php';
include_once CLASSES_DIR.'page/class.securePage_bv.php';
include_once CLASSES_DIR.'tabulate/class.tabulate_bv.php';

class login extends securePage_bv
{

	Function login($arrAttr)
	{
		$arrAttr['page_type'] = 'public_page';
		parent::securePage_bv($arrAttr);
		
		// If the user has successfully logged in send back to where they came from.
		if ( $this->IsUserAuthenticated()){
			if (!$this->SendBack()){ // If we can't send back because this is the first page they landed on. Send to home page
				$this->SendTo(DOMAIN);
			}
		}
		
		if ($this->GetTargetData('status') === 'login_failure'){
			$_msg = '<div id="login_failure">Wrong username or password.</div>';
		}
		else if ($this->GetTargetData('status') === 'refuse_must_login_first_to_view_page'){
			$_msg = '<div id="login_message">You must first login in order to view this page.</div>';
		}
		else if ($this->GetTargetData('status') === 'timed_out'){
			$_msg = '<div id="login_message">Your session has timed out. Please login again.</div>';
		}
		else {
			$_msg = 'Please provide your login details.';
		}
		
		$this->Assign('<TITLE>', 'Login page');
		
		$this->Assign('<MAIN>', $this->GetLoginFormHtml($_msg));
	}
}

include_once 'controller.php';
?>
