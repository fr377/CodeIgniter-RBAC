<?php
namespace Rbac;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Group class.
 * 
 * @extends ActiveRecord
 */
class Group extends \ActiveRecord\Model
{


	/* --------------------------------------------------
	 *	ACTIVERECORD ASSOCIATIONS
	 * ----------------------------------------------- */


	static $has_many = array(
		array('rules'),
		array('memberships'),
		array('users', 'through' => 'memberships')
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
		$CI->db->query("DROP TABLE IF EXISTS `groups`");
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
			CREATE TABLE `groups` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
				`importance` int(10) unsigned NOT NULL DEFAULT '1',
				`singular` tinyint(1) unsigned NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				KEY `name` (`name`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;
		");
	}
}