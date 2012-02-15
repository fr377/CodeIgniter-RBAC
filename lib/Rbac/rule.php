<?php
namespace Rbac;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Rules are the key to it all! A rule brings together a group (i.e., one or more users) with a resource (i.e., one
 * or more entities) and a privilege (i.e., one or more actions).
 * 
 * @extends ActiveRecord
 */
class Rule extends \ActiveRecord\Model
{


	/* --------------------------------------------------
	 *	ACTIVERECORD ASSOCIATIONS
	 * ----------------------------------------------- */


	static $belongs_to = array(
		array('privilege'),
		array('group'),
		array('resource')
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
	public static function db_create()
	{
		$CI =& get_instance();
		$CI->db->query("
			CREATE TABLE `rules` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`group_id` int(10) unsigned DEFAULT NULL,
				`privilege_id` int(10) unsigned DEFAULT NULL,
				`resource_id` int(10) unsigned DEFAULT NULL,
				`allowed` tinyint(1) unsigned DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `group_id` (`group_id`),
				KEY `privilege_id` (`privilege_id`),
				KEY `resource_id` (`resource_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		");
	}

	
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
		$CI->db->query("DROP TABLE IF EXISTS `rules`");
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
			ALTER TABLE `rules`
				ADD CONSTRAINT `rules_ibfk_6` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE CASCADE,
				ADD CONSTRAINT `rules_ibfk_4` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
				ADD CONSTRAINT `rules_ibfk_5` FOREIGN KEY (`privilege_id`) REFERENCES `privileges` (`id`) ON DELETE CASCADE;
  		");
	}
}