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


	static $table_name = 'rbac_groups';

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
	public static function db_create($destroy_first = TRUE)
	{
		if ($destroy_first)
			self::db_destroy();

		return get_instance()->db->query("
			CREATE TABLE IF NOT EXISTS `".self::$table_name."` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
				`importance` int(10) unsigned NOT NULL DEFAULT '1',
				`singular` tinyint(1) unsigned NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				KEY `name` (`name`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;
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








	public function enroll(User $user)
	{
		return $user->join_group($this);
	}


	private function _query_action_on_resource(Action $action, Resource $resource)
	{
		return $query = "
			SELECT
				`allowed`,
				t2.`id` AS `privilege`,
				t2.`singular` AS `is_granular_privilege`,
				t4.`id` AS `action`,
				t5.`id` AS `resource`,
				t5.`singular` AS `is_granular_resource`,
				t7.`id` AS `entity`,
				t8.`id` AS `group`,
				t8.`importance`
			
			FROM `".Rule::$table_name."` AS t1
				
				-- Privileges Joins --
				INNER JOIN `".Privilege::$table_name."` AS t2
					ON t2.`id` = t1.`privilege_id` 
				INNER JOIN `".Liberty::$table_name."` AS t3
					ON t3.`privilege_id` = t2.`id`
				INNER JOIN `".Action::$table_name."` AS t4
					ON t4.`id` = t3.`action_id`

				-- Resources Joins --
				INNER JOIN `".Resource::$table_name."` AS t5
					ON t5.`id` = t1.`resource_id`
				INNER JOIN `".Component::$table_name."` AS t6
					ON t6.`resource_id` = t5.`id`
				INNER JOIN `".Entity::$table_name."` AS t7
					ON t7.`id` = t6.`entity_id`

				-- Groups to user Joins --
				INNER JOIN `".Group::$table_name."` AS t8
					ON t8.`id` = t1.`group_id`

			WHERE
				`group_id` = '{$this->id}'
					AND
				t4.`id` = '{$action->id}'
					AND
				`resource_id` = '{$resource->id}'

			ORDER BY
				t8.`importance` DESC,
				t8.`id`
			";
	}
	
	private function _query_action_on_entity(Action $action, Entity $entity)
	{
		return $query = "
			SELECT
				`allowed`,
				t2.`id` AS `privilege`,
				t2.`singular` AS `is_granular_privilege`,
				t4.`id` AS `action`,
				t5.`id` AS `resource`,
				t5.`singular` AS `is_granular_resource`,
				t7.`id` AS `entity`,
				t8.`id` AS `group`,
				t8.`importance`
			
			FROM `".Rule::$table_name."` AS t1
				
				-- Privileges Joins --
				INNER JOIN `".Privilege::$table_name."` AS t2
					ON t2.`id` = t1.`privilege_id` 
				INNER JOIN `".Liberty::$table_name."` AS t3
					ON t3.`privilege_id` = t2.`id`
				INNER JOIN `".Action::$table_name."` AS t4
					ON t4.`id` = t3.`action_id`

				-- Resources Joins --
				INNER JOIN `".Resource::$table_name."` AS t5
					ON t5.`id` = t1.`resource_id`
				INNER JOIN `".Component::$table_name."` AS t6
					ON t6.`resource_id` = t5.`id`
				INNER JOIN `".Entity::$table_name."` AS t7
					ON t7.`id` = t6.`entity_id`

				-- Groups to user Joins --
				INNER JOIN `".Group::$table_name."` AS t8
					ON t8.`id` = t1.`group_id`

			WHERE
				`group_id` = '{$this->id}'
					AND
				t4.`id` = '{$action->id}'
					AND
				t7.`id` = '{$entity->id}'

			ORDER BY
				t8.`importance` DESC,
				t8.`id`
			";
	}
	
	private function _query_privilege_on_resource()
	{}
	
	private function _query_privilege_on_entity()
	{}


	/**
	 * is_allowed function.
	 * 
	 * @access public
	 * @param mixed $verb Either an Action or Privilege object
	 * @param Entity $noun Either an Entity or Resource object
	 * @param boolean $force_lookup (default: FALSE)
	 * @return void
	 */
	public function is_allowed($verb, $noun, $force_lookup = FALSE)
	{
		if ( ! ($verb instanceof Action)
			&& ! ($verb instanceof Privilege))
				throw new \Exception('Verb clause must be either an Action or Privilege object.');

		if ( ! ($noun instanceof Entity)
			&& ! ($noun instanceof Resource))
				throw new \Exception('Noun clause must be either an Entity or Resource object');

		$query_method = strtolower('_query_' . get_class($verb) . '_on_' . get_class($noun));

echo '<pre>';
echo $query_method.'<br>';
print_r(self::$query_method($verb, $noun));
		$rules = array_reverse(Rule::find_by_sql(self::$query_method($verb, $noun)));

		$allowed = NULL;
		$importance_threshold = NULL;
		$weight_threshold = -1;

		do {
			$rule = array_pop($rules);

			if ($rule->importance < $importance_threshold)
				continue;

			$weight = $rule->is_granular_privilege + $rule->is_granular_resource;

			if ($weight > $weight_threshold) {
				$allowed = $rule->allowed ? TRUE : FALSE;
				$weight_threshold = $weight;

			} else if ($weight == $weight_threshold && ! $rule->allowed)
				$allowed = FALSE;

		} while ($rules);

		return $allowed;
	}


}