<?php
namespace Rbac;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * A liberty represents the privilege to perform an action. If a user has a certain privilege,
 * then (barring other rules) they are 'at liberty' to perform certain actions.
 *
 * A liberty is to a privilege and an action as a membership is to a group and a user.
 * 
 * @extends ActiveRecord
 */
class Liberty extends \ActiveRecord\Model
{


	/* --------------------------------------------------
	 *	ACTIVERECORD ASSOCIATIONS
	 * ----------------------------------------------- */


	static $has_one = array(
		array('action'),
		array('privilege')
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
		$CI->db->query("DROP TABLE IF EXISTS `liberties`");
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
			CREATE TABLE `liberties` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`privilege_id` int(10) unsigned NOT NULL,
				`action_id` int(10) unsigned NOT NULL,
				PRIMARY KEY (`id`),
				KEY `privilege_id` (`privilege_id`),
				KEY `action_id` (`action_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;
		");
	}


	/**
	 * Installation helper method.
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function db_relations()
	{
		$CI =& get_instance();
		$CI->db->query("
			ALTER TABLE `liberties`
				ADD CONSTRAINT `liberties_ibfk_4` FOREIGN KEY (`action_id`) REFERENCES `actions` (`id`) ON DELETE CASCADE,
				ADD CONSTRAINT `liberties_ibfk_3` FOREIGN KEY (`privilege_id`) REFERENCES `privileges` (`id`) ON DELETE CASCADE;
  		");
	}
}