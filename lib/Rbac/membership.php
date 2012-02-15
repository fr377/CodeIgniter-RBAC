<?php
namespace Rbac;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Memberships represent the associations between users and groups.
 *
 * A membership is to a group and a user as a liberty is to a privilege and an action.
 * 
 * @extends ActiveRecord
 */
class Membership extends \ActiveRecord\Model
{


	/* --------------------------------------------------
	 *	ACTIVERECORD ASSOCIATIONS
	 * ----------------------------------------------- */


	static $belongs_to = array(
		array('user'),
		array('group')
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
		$CI->db->query("DROP TABLE IF EXISTS `memberships`");
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
			CREATE TABLE `memberships` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`user_id` int(10) unsigned DEFAULT NULL,
				`group_id` int(10) unsigned DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `user_id` (`user_id`),
				KEY `group_id` (`group_id`)
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
			ALTER TABLE `memberships`
				ADD CONSTRAINT `memberships_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
				ADD CONSTRAINT `memberships_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE;
  		");
	}
}