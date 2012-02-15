<?php
namespace Rbac;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Actions are described with verbs. An action is something that can be done to (or with) a resource.
 * For example, your system might benefit from actions such as 'create', 'retrieve', 'update', and 'delete'.
 * But consider another system with actions like 'chew', 'cook', 'dance', or 'telepathy'.
 * 
 * @extends ActiveRecord
 */
class Action extends \ActiveRecord\Model
{


	/* --------------------------------------------------
	 *	ACTIVERECORD ASSOCIATIONS
	 * ----------------------------------------------- */


	static $has_many = array(
		array('liberties'),
		array('privileges', 'through' => 'liberties')
	);


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
		$CI->db->query("DROP TABLE IF EXISTS `actions`");
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
			CREATE TABLE `actions` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
				`description` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `name` (`name`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		");
	}


	/* --------------------------------------------------
	 *	ACTIVERECORD CALLBACKS
	 * ----------------------------------------------- */


	/**
	 * Grants the global privilege a liberty to this action.
	 * 
	 * @access public
	 * @return void
	 */
	public function after_save()
	{
		// every entity belongs to the special global (i.e., 'all') resource
		Privilege::find(1)->grant($this);
		
		// every entity needs a singular resource for granular control
		$privilege = new Privilege();
		$privilege->name = $this->name;
		$privilege->singular = TRUE;
		$privilege->save();
		
		$privilege->grant($this);
/*
		// every action has a singular privilege for ganular rules
		if ( ! Liberty::find_by_action_id($this->id) ) {
			$privilege = new Privilege();
			$privilege->name = $this->name;
			$privilege->save();
			
			if ( ! $privilege->allows($this) )
				$privilege->grant($this);

			// every action is a part of the global privilege
			Privilege::find(1)->grant($this);
		}
*/
	}
}