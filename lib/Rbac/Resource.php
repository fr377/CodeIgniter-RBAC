<?php
namespace Rbac;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * A resource is a collection of one or more entities.
 * 
 * @extends ActiveRecord
 */
class Resource extends \ActiveRecord\Model
{


	/* --------------------------------------------------
	 *	ACTIVERECORD ASSOCIATIONS
	 * ----------------------------------------------- */


	static $table_name = 'rbac_resources';


	static $has_many = array(
		array('rules'),
		array('components'),
		array('entities', 'through' => 'components')
	);
	

	/* --------------------------------------------------
	 *	MODEL BEHAVIOURS
	 * ----------------------------------------------- */


	/**
	 * Remove an entity from a resource.
	 * 
	 * @access public
	 * @param Entity $entity
	 * @return void
	 */
	public function exclude(Entity $entity)
	{
		// is the entity part of this resource? if so, proceed
		if ($this->includes($entity)) {
			Component::find_by_resource_id_and_entity_id($this->id, $entity->id)->delete();
		}
	}
	
	
	/**
	 * Determine whether a resource includes an entity.
	 * 
	 * @access public
	 * @param Entity $entity
	 * @return boolean
	 */
	public function includes(Entity $entity)
	{
		return Component::find_by_resource_id_and_entity_id($this->id, $entity->id) ? TRUE : FALSE;
	}


	/**
	 * Add an entity to a resource.
	 * 
	 * @access public
	 * @param Entity $entity
	 * @return void
	 */
	public function subsume(Entity $entity)
	{
		// does this resource already subsume this entity? if not, continue
		if ( ! $this->includes($entity) ) {
			$component = new Component();
			$component->resource_id = $this->id;
			$component->entity_id = $entity->id;
			$component->save();
		}
	}
	
	
	/* --------------------------------------------------
	 *	MODEL BEHAVIOURS :: STATIC
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
			CREATE  TABLE IF NOT EXISTS `".self::$table_name."` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
				`created_at` DATETIME NULL ,
				`updated_at` DATETIME NULL ,
 
				`name` VARCHAR(128) NULL DEFAULT NULL ,
				`description` VARCHAR(128) DEFAULT NULL,
				`singular` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 ,
 
				PRIMARY KEY (`id`)
 
			) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;
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
}