<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Rbac class.
 *
 * 'Fine-grained' role based access control library for CodeIgniter.
 *
 * @todo Delete associated singular privilege when deleting an action
 * @todo I think there's a bug in User::is_allowed() ... it doesn't account for group weight.
 */
class Rbac2
{

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->EM =& $this->CI->doctrine->em;

		require_once(__CLASS__ . '/Action.php');
		require_once(__CLASS__ . '/Privilege.php');
		require_once(__CLASS__ . '/PrivilegeHasAction.php');
		
		require_once(__CLASS__ . '/User.php');
		require_once(__CLASS__ . '/Group.php');
		require_once(__CLASS__ . '/GroupHasUser.php');
		
		require_once(__CLASS__ . '/Entity.php');
		require_once(__CLASS__ . '/Resource.php');
		require_once(__CLASS__ . '/ResourceHasEntity.php');
		
		require_once(__CLASS__ . '/Rule.php');
	}
	
	public function install()
	{}
}