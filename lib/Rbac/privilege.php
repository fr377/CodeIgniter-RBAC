<?php
namespace Rbac;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Privileges grant you liberties to take action(s).  If you have a privilege to an action then you are
 * 'at liberty' to take that action.
 * 
 * @extends ActiveRecord
 */
class Privilege extends \ActiveRecord\Model
{


	/* --------------------------------------------------
	 *	ACTIVERECORD ASSOCIATIONS
	 * ----------------------------------------------- */


	static $has_many = array(
		array('rules'),
		array('liberties'),
		array('actions', 'through' => 'liberties')
	);
	
	

	/* --------------------------------------------------
	 *	MODEL BEHAVIOURS (PUBLIC METHODS)
	 * ----------------------------------------------- */


	/**
	 * Determine whether this privilege grants a liberty to do an action.
	 * 
	 * @access public
	 * @param Action $action
	 * @return boolean
	 */
	public function allows(Action $action)
	{
		if (Liberty::find_by_privilege_id_and_action_id($this->id, $action->id))
			return TRUE;
		
		return FALSE;
	}


	/**
	 * Grant liberty to do an action.
	 * 
	 * @access public
	 * @param Action $action
	 * @return void
	 */
	public function grant(Action $action)
	{
		// does this privilege already grant this liberty? if not, continue
		if ( ! Liberty::find_by_privilege_id_and_action_id($this->id, $action->id)) {

			// grant liberty to this action
			$liberty = new Liberty();
			$liberty->privilege_id = $this->id;
			$liberty->action_id = $action->id;
			$liberty->save();
		}
	}
	
	
	/**
	 * Revoke the liberty to do an action.
	 * 
	 * @access public
	 * @param Action $action
	 * @return void
	 */
	public function revoke(Action $action)
	{
		Liberty::find_by_privilege_id_and_action_id($this->id, $action->id)->delete();
	}
	
	
	/**
	 * Revoke all liberties granted .
	 * 
	 * @access public
	 * @return void
	 */
	public function revoke_all()
	{
		Liberty::table()->delete(array('privilege_id' => $this->id));
	}


	/* --------------------------------------------------
	 *	MODEL BEHAVIOURS (PUBLIC STATIC METHODS)
	 * ----------------------------------------------- */


	/**
	 * Installation helper method.
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function db_destroy()
	{
		$CI =& get_instance();
		$CI->db->query("DROP TABLE IF EXISTS `privileges`");
	}
	

	/**
	 * Installation helper method.
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function db_create()
	{
		$CI =& get_instance();
		$CI->db->query("
			CREATE TABLE `privileges` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
				`singular` tinyint(1) unsigned NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				KEY `name` (`name`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;
		");
	}
}