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


	static $table_name = 'rbac_rules';
	
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
	public static function db_create($destroy_first = TRUE)
	{
		if ($destroy_first)
			self::db_destroy();

		return get_instance()->db->query("
			CREATE TABLE IF NOT EXISTS `".self::$table_name."` (
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
	 * @access private
	 * @static
	 * @return void
	 */
	protected static function db_destroy()
	{
		return get_instance()->db->query("DROP TABLE IF EXISTS `".self::$table_name."`");
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
			ALTER TABLE `".self::$table_name."`
				ADD CONSTRAINT `rules_ibfk_6` FOREIGN KEY (`resource_id`) REFERENCES `".Resource::$table_name."` (`id`) ON DELETE CASCADE,
				ADD CONSTRAINT `rules_ibfk_4` FOREIGN KEY (`group_id`) REFERENCES `".Group::$table_name."` (`id`) ON DELETE CASCADE,
				ADD CONSTRAINT `rules_ibfk_5` FOREIGN KEY (`privilege_id`) REFERENCES `".Privilege::$table_name."` (`id`) ON DELETE CASCADE;
  		");
	}
}