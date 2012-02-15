<?php
namespace Rbac;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * A component describes the relationship between a resource and an entity. The term 'component' was chosen
 * to support the conceptualization of entities being component parts of resources.
 * 
 * @extends ActiveRecord
 */
class Component extends \ActiveRecord\Model
{


	/* --------------------------------------------------
	 *	ACTIVERECORD ASSOCIATIONS
	 * ----------------------------------------------- */


	static $belongs_to = array(
		array('entity'),
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
			CREATE TABLE `components` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`resource_id` int(10) unsigned DEFAULT NULL,
				`entity_id` int(10) unsigned DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `resource_id` (`resource_id`),
				KEY `entity_id` (`entity_id`)
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
	public static function db_destroy()
	{
		$CI =& get_instance();
		$CI->db->query("DROP TABLE IF EXISTS `components`");
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
			ALTER TABLE `components`
				ADD CONSTRAINT `components_ibfk_4` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE,
				ADD CONSTRAINT `components_ibfk_3` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE CASCADE;
  		");
	}
}