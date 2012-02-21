<?php
namespace Rbac;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Actions are described with verbs. An action is something that can be done to (or with) a resource.
 * For example, your system might benefit from actions such as 'create', 'retrieve', 'update', and 'delete'.
 * But consider another system with actions like 'chew', 'cook', 'dance', or 'telepathy'.
 *
 * Because of the way associated objects are automatically created/deleted, the following is true:
 *	- $this->liberties[0] is always the global privilege ('all privileges')
 *	- $this->liberties[1] is always the singular privilege (for granular control)
 * 
 * @extends ActiveRecord
 */
class Action extends \ActiveRecord\Model
{


	/* --------------------------------------------------
	 *	ACTIVERECORD ASSOCIATIONS
	 * ----------------------------------------------- */


	static $table_name = 'rbac_actions';
	
	static $after_save = array('create_singular_privilege');

	static $before_destroy = array('destroy_singular_privilege');

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
	public static function db_create($destroy_first = TRUE)
	{
		if ($destroy_first)
			self::db_destroy();

		return get_instance()->db->query("
			CREATE TABLE IF NOT EXISTS `".self::$table_name."` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
				`description` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `name` (`name`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		");
	}


	/**
	 * Installation helper method.
	 * 
	 * @access private
	 * @static
	 * @return void
	 */
	protected static function db_destroy()
	{
		return get_instance()->db->query("DROP TABLE IF EXISTS `".self::$table_name."`");
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
	public function create_singular_privilege()
	{
		// every entity belongs to the special global (i.e., 'all') resource
		Privilege::find(1)->grant($this);
		
		// every entity needs a singular resource for granular control
		$privilege = new Privilege();
		$privilege->name = $this->name;
		$privilege->singular = TRUE;
		$privilege->save();
		
		$privilege->grant($this);
	}


	public function destroy_singular_privilege()
	{
		// need to delete the singular privilege
		return Privilege::find($this->liberties[1]->privilege_id)->delete();
	}
}