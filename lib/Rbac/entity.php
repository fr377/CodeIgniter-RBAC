<?php
namespace Rbac;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Entities are described by nouns.  An entity is a discrete thing to which a user
 * can be allowed or denied based on their privileges.
 *
 * Because of the way associated objects are automatically created/deleted, the following is true:
 *	- $this->components[0] is always the global resource
 *	- $this->components[1] is always the singular resource (for granular control)
 * 
 * @extends ActiveRecord
 */
class Entity extends \ActiveRecord\Model
{

	/**
	 * @var mixed
	 * @access private
	 */
	private $_singular_resource;
	

	/* --------------------------------------------------
	 *	ACTIVERECORD ASSOCIATIONS
	 * ----------------------------------------------- */


	static $has_many = array(
		array('components'),
		array('resources', 'through' => 'components')
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
			CREATE TABLE `entities` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
				`description` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `name` (`name`)
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
		$CI->db->query("DROP TABLE IF EXISTS `entities`");
	}
	

	/* --------------------------------------------------
	 *	ACTIVERECORD CALLBACKS
	 * ----------------------------------------------- */


	/**
	 * Creates a ganular (i.e., singular) resource and includes the new entity in the global resource.
	 * 
	 * @access public
	 * @return void
	 */
	public function after_save()
	{
		// every entity belongs to the special global (i.e., 'all') resource
		Resource::find(1)->subsume($this);
		
		// every entity needs a singular resource for granular control
		$resource = new Resource();
		$resource->name = $this->name;
		$resource->description = $this->description;
		$resource->singular = TRUE;
		$resource->save();
		
		$resource->subsume($this);
	}
	

	/**
	 * Destroy the granular (i.e., singular) resource.
	 * 
	 * @access public
	 * @return void
	 */
	public function before_destroy()
	{
		Resource::find($this->components[1]->resource_id)->delete();
	}
}