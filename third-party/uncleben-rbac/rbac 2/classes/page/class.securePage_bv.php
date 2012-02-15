<?php

include_once 'class.page_bv.php';


class SecurePage_bv extends Page_bv
{
	
	var $mAction;
	var $mTimeOut;
	var $mUser;
	
	var $mScriptNameHash;
	
	/**
	 * constructor
	 **/
	Function SecurePage_bv($arrAttributes = array()){
		
		$this->mTimeOut = 30; // 30 mins
		
		$this->mScriptNameHash = md5($_SERVER['SCRIPT_NAME']);
		
		//------------
		// Call parent constructor
		parent::Page_bv($arrAttributes);
		
		//-------------------------
		// Authenticate user
		
		if ($this->Post('action', 'login') === 'login'){ // User has just logged in
			
			// TODO: Make sure username and password are clean
			if (!$this->AuthenticateUser($this->Post('username', 'login'), $this->Post('password', 'login'))){ // User was not authenticated
				
				// TODO: Do some logging to make sure there is not abuse.
				
				$this->SendTo(DOMAIN.'login.php', array('status'=>'login_failure'));
			}
			else {
				$this->mAction ='allow_just_logged_in';
			}
		}
		
		if ($this->Post('action', 'logout') === 'logout'){
			$this->Logout();
		}
		
		if (!$this->IsUserAuthenticated()){ // We have an anonymous user
			$_SESSION['user_bv']['id'] = 0;
			$_SESSION['user_bv']['username'] = 'Anon';
			$_SESSION['user_bv']['is_authenticated'] = 'no';
			$_SESSION['user_bv']['last_time'] = time();
		}
		else if ($this->HasUserTimedOut()){
			$this->Logout();
			$this->SendTo(DOMAIN.'login.php', array('status'=>'timed_out'));
			
		}
		else {
			$_SESSION['user_bv']['last_time'] = time(); // Update the last visit time.
			
			$this->mAction  = 'allow';
		}
		
		//--------------
		// Initialise the Authorization class
		include_once CLASSES_DIR.'rbac/class.rbac_bv.php';
		
		$this->mAuthorization = & new rbac_bv($this->mConn);
		
		if (!$this->IsAllowedToViewPage()){
			
			// if the user logged out from an authorized page, send to home page
			if ($this->Post('action', 'logout') === 'logout'){
				$this->SendTo(DOMAIN);
			}
			
			if ($this->IsUserAuthenticated()){ 
				echo "Unauthorized.";
			} else { // user it not logged in
				$this->SendTo(DOMAIN.'login.php', array('status'=>'refuse_must_login_first_to_view_page'));
			}
			
			exit; // Stop everything here.
		}
		
	}
	
	// Override parent Post() method
	// We need to deal with the case where form names may be scrambled. Otherwise just use the parent method
	Function Post($key = '', $formName = '')
	{
		// If there is a simple match return it.
		if ($key != '' && ($value = parent::Post($key))){
			return $value;
		}
		
		if ($formName != ''){
			// All the form random names are stored in session
			if (!isset($_SESSION['form_bv'][$this->mScriptNameHash][$formName]['names']) || !is_array($_SESSION['form_bv'][$this->mScriptNameHash][$formName]['names'])){
				return FALSE;
			}
			
			// Find the corresponding random name to the input name $key
			$frm_rand_key = array_search($key, $_SESSION['form_bv'][$this->mScriptNameHash][$formName]['names']);
			
			if ($frm_rand_key === FALSE){
				return FALSE;
			}
			else{
				return $this->Post($frm_rand_key);
			}
		}
		else {
			return parent::Post($key);
		}
	}
	
	Function Files($key = '', $formName = '')
	{
		// If there is a simple match return it.
		if ($key != '' && ($value = parent::Files($key))){
			return $value;
		}
		
		if ($formName != ''){
			// All the form random names are stored in session
			if (!isset($_SESSION['form_bv'][$this->mScriptNameHash][$formName]['names']) || !is_array($_SESSION['form_bv'][$this->mScriptNameHash][$formName]['names'])){
				return FALSE;
			}
			
			// Find the corresponding random name to the input name $key
			$frm_rand_key = array_search($key, $_SESSION['form_bv'][$this->mScriptNameHash][$formName]['names']);
			
			if ($frm_rand_key === FALSE){
				return FALSE;
			}
			else{
				return $this->Files($frm_rand_key);
			}
		}
		else {
			return parent::Files($key);
		}
	}
	
	Function IsAllowedTo($action, $object)
	{
		return $this->mAuthorization->IsAllowedTo($this->GetUser('id'), $action, $object);
	}
	
	Function IsAllowedToViewPage()
	{
		// Anonymous user is always allowed to view public page
		if ((int) $this->GetUser('id') === 0 && $this->GetPage('page_type') === 'public_page'){
			return TRUE;
		}
		else {
			return $this->mAuthorization->IsAllowedTo($this->GetUser('id'), 'view', $this->GetPage('page_type'));
		}
	}
	
	
	Function IsUserAuthenticated()
	{
		if (isset($_SESSION['user_bv']['is_authenticated']) && $_SESSION['user_bv']['is_authenticated'] === 'yes'){
			
			// Change the session id name
			if (md5($_SERVER['HTTP_USER_AGENT']) === $_SESSION['user_bv']['hash']){ 
				session_regenerate_id();
			}
			else { // Session has been highjecked
				return FALSE;
			}
			
			return TRUE;
		}
		else {
			return FALSE;
		}
	}
	
	Function AuthenticateUser($username, $password)
	{
		if (!$_row = $this->mConn->GetRow("SELECT id, pswd FROM users WHERE username = '$username'")){
			return FALSE;
		}
		
		if ($_row['pswd'] === md5($password)){
			
			$_SESSION['user_bv']['id'] = $_row['id'];
			$_SESSION['user_bv']['username'] = $username;
			$_SESSION['user_bv']['is_authenticated'] = 'yes';
			$_SESSION['user_bv']['last_time'] = time();
			$_SESSION['user_bv']['hash'] = md5($_SERVER['HTTP_USER_AGENT']);
			
			// TODO: Set a cookie on the users machine. Store hash.
			
			// Commit changes to the session.
			//session_commit();
			//session_start();
			
			return TRUE;
		}
		else {
			return FALSE;
		}
	}
	
	/**
	 * Page::GetLoginFormHtml returns the Login HTML form
	 *
	 * @access public
	 * @return HTML login form
	 **/
	function GetLoginFormHtml( $msg = '')
	{
		include_once CLASSES_DIR.'form/class.form_bv.php';
		
		if ($this->IsUserAuthenticated()){ // User is logged in
			return 
			'<div style="margin-right:10px;" class="login">
				<table id="headerForm"><tr><td>
				<div style="float: left; margin-right: 10px;">Logged in as:</div>
				<div style="float: left; margin-right: 10px;"><b>'.$this->GetUser('username').'<b></div>
				<div>
					<form action="'.$this->GetPage('url').'" method="POST">
						<input type="submit" name="logout" value="Logout">
						<input type="hidden" name="action" value="logout">
					</form>
				</div>
				</table>
			<div>';
		}
		
		$_frm = &new Form_bv(array('method'=>'POST', 'action'=>'', 'name'=>'login'));
		
		// ROW ---------
		$_frm->AssignInput('<USERNAME>', 'username', array('type'=>'text', 'class'=>'inp', 'info'=>'', 'alt'=>'Username:', 'size'=>10 ));
		$_frm->SetInputRules('username', array('table'=>'users'));
		
		// ROW ---------
		$_frm->AssignInput('<PASSWORD>', 'password', array('type'=>'password', 'class'=>'inp', 'info'=>'', 'alt'=>'Password:', 'size'=>10 ));
		$_frm->SetInputRules('password', array('table'=>'users'));
		
		// ROW ---------
		$_frm->SetCellAttributes(array('colspan'=>'2', 'align'=>'center', 'id'=>'button'));
		
		$_frm->AssignInput('<!BUTTON!>', 'login', array('type'=>'submit', 'value'=>'Login'));
		
		$_frm->AppendInput('<!BUTTON!>','action',array('type'=>'hidden', 'value'=>'login'));
		
		if ($msg != ''){ // IF there is a message we will generally put the login form in the MAIN tag of the page
			
			$_frm->Append('<!NOTICE!>', $msg);
			
			$_frm->SetInputAttribute('size', array('username'=>'20', 'password'=>'20'));
			
			$_frm->setTemplate( '
				<table  id="loginForm" align="center">
					<tr>
						<td colspan="2">[#]<!NOTICE!>[#]</td>
					</tr>
					<tr>
						<td>[#]<USERNAME_LBL>[#]</td>
						<td>[#]<USERNAME>[#]</td>
					</tr>
					<tr>
						<td>[#]<PASSWORD_LBL>[#]</td>
						<td>[#]<PASSWORD>[#]</td>
					</tr>
					<tr>
						<td><a href="register.php">Register</a></td>
						<td align="center">[#]<!BUTTON!>[#]</td>
					</tr>
						<td colspan="2"><a href="forgot_password.php">Forgot your password?</a></td>
					<tr>
					</tr>
				</table>');
			
			$_html = $_frm->PrintForm();
		}
		else {
			
		$_frm->setTemplate('
			<td>[#]<!NOTICE!>[#]</td>
			<td>[#]<USERNAME_LBL>[#] [#]<USERNAME>[#]</td>
			<td>[#]<PASSWORD_LBL>[#] [#]<PASSWORD>[#]</td>

			<td>[#]<!BUTTON!>[#]</td>
			<td><a href="register.php">Signup</a></td>
			');
		
		$_html = '<table id="headerForm"><tr>'.$_frm->PrintForm().'</tr></table>';
		}
		
		return $_html;
	}
	
	Function HasUserTimedOut()
	{
		$_epoc = time();
		
		if (($_epoc - $_SESSION['user_bv']['last_time']) > $this->mTimeOut*60 && $this->mTimeOut != '-1'){ // -1 means never time out.
			
			$this->Logout(); // unset all session variables
			
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	Function Logout()
	{
		// Unset all of the session variables.
		//session_unset();
		// Finally, destroy the session.
		//session_destroy();
		unset($_SESSION['user_bv']);
		unset($_SESSION['page_bv']);
		session_commit();
	}
	
	//=================
	//User inforamtion
	
	/**
	  *
	  **/
	Function GetUser($info)
	{
		if (isset($_SESSION['user_bv'][$info])){
			return $_SESSION['user_bv'][$info];
		}
		else {
			
			if ($info === 'ip'){
				
				if (getenv('HTTP_X_FORWARDED_FOR')) {
						$_ip = getenv('HTTP_X_FORWARDED_FOR'); 
				} else { 
						$_ip = getenv('REMOTE_ADDR');
				}	
				
				// $_SESSION['user_bv']['ip'] = $_ip; // BETTER NOT TO STORE IT IN SESSION FOR SECURITY REASONS.
				return $_ip;
			}
			
		}
	}
	
	
}


?>
