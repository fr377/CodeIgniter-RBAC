<?php
namespace Rbac;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * User class.
 * 
 * @extends ActiveRecord
 */
class User extends \ActiveRecord\Model
{


	/* --------------------------------------------------
	 *	ACTIVERECORD ASSOCIATIONS
	 * ----------------------------------------------- */


	static $has_many = array(
		array('memberships'),
		array('groups', 'through' => 'memberships')
	);
	


	/* --------------------------------------------------
	 *	MODEL BEHAVIOURS (PUBLIC METHODS)
	 * ----------------------------------------------- */


	/**
	 * Determine whether the user is a member of a group.
	 * 
	 * @access public
	 * @param Group $group
	 * @return void
	 */
	public function in_group(Group $group)
	{
		if (Membership::find_by_user_id_and_group_id($this->id, $group->id))
			return TRUE;
		
		return FALSE;
	}
	

	/**
	 * Determine whether a user is allowed to (action) with (entity)
	 * 
	 * @access public
	 * @param Entity $entity
	 * @param Action $action
	 * @return void
	 */
	public function is_allowed(Entity $entity, Action $action)
	{
		$query = "
			SELECT
				allowed,
				t2.name AS `privilege`,
				t2.singular AS `is_privilege_singular`,
				t4.name AS `action`,
				t5.name AS `resource`,
				t5.singular AS `is_resource_singular`,
				t7.name AS `entity`,
				t8.name AS `group`,
				t8.importance
			
			FROM rules AS t1
				
				-- Privileges Joins --
				INNER JOIN privileges AS t2 ON t2.id = t1.privilege_id 
				INNER JOIN liberties AS t3 ON t3.privilege_id = t2.id
				INNER JOIN actions AS t4 ON t4.id = t3.action_id

				-- Resources Joins --
				INNER JOIN resources AS t5 ON t5.id = t1.resource_id
				INNER JOIN components AS t6 ON t6.resource_id = t5.id
				INNER JOIN entities AS t7 ON t7.id = t6.entity_id

				-- Groups to user Joins --
				INNER JOIN groups AS t8 ON t8.id = t1.group_id
				INNER JOIN memberships AS t9 ON t9.group_id = t8.id

			WHERE
				user_id = {$this->id}
					AND
				t4.name = '{$action->name}'
					AND
				t7.name = '{$entity->name}'

			ORDER BY
				t8.importance DESC,
				t8.name
			";

		// adjusted to search by action_id and entity_id, instead of names (which may not be unique)
		$query = "
			SELECT
				allowed,
				t2.name AS `privilege`,
				t2.singular AS `is_privilege_singular`,
				t4.name AS `action`,
				t5.name AS `resource`,
				t5.singular AS `is_resource_singular`,
				t7.name AS `entity`,
				t8.name AS `group`,
				t8.importance
			
			FROM rules AS t1
				
				-- Privileges Joins --
				INNER JOIN privileges AS t2 ON t2.id = t1.privilege_id 
				INNER JOIN liberties AS t3 ON t3.privilege_id = t2.id
				INNER JOIN actions AS t4 ON t4.id = t3.action_id

				-- Resources Joins --
				INNER JOIN resources AS t5 ON t5.id = t1.resource_id
				INNER JOIN components AS t6 ON t6.resource_id = t5.id
				INNER JOIN entities AS t7 ON t7.id = t6.entity_id

				-- Groups to user Joins --
				INNER JOIN groups AS t8 ON t8.id = t1.group_id
				INNER JOIN memberships AS t9 ON t9.group_id = t8.id

			WHERE
				user_id = {$this->id}
					AND
				t4.id = '{$action->id}'
					AND
				t7.id = '{$entity->id}'

			ORDER BY
				t8.importance DESC,
				t8.name
			";
echo '<pre>';
print_r($query);
		$allowed = NULL;
		$importance_threshold = NULL;
		$weight_threshold = -1;

		$rules = array_reverse(Rule::find_by_sql($query));

		do {
			$rule = array_pop($rules);
			
			if ($rule->importance < $importance_threshold) {
				continue;
			}

			$weight = $rule->is_privilege_singular + $rule->is_resource_singular;

			if ($weight > $weight_threshold) {
				$allowed = $rule->allowed ? TRUE : FALSE;
				$weight_threshold = $weight;

			} else if ($weight == $weight_threshold && ! $rule->allowed)
				$allowed = FALSE;

		} while ($rules);

		return $allowed;
	}
	

	/**
	 * Join a group.
	 * 
	 * @access public
	 * @param Group $group
	 * @return void
	 */
	public function join_group(Group $group)
	{
		$membership = new Membership();
		$membership->user_id = $this->id;
		$membership->group_id = $group->id;
		$membership->save();
	}
	

	/**
	 * Leave a group.
	 * 
	 * @access public
	 * @param Group $group
	 * @return void
	 */
	public function leave_group(Group $group)
	{
		return Membership::find_by_user_id_and_group_id($this->id, $group->id)->delete();
	}


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
			CREATE TABLE `users` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`email` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
				`first_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `email` (`email`)
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
		$CI->db->query("DROP TABLE IF EXISTS `users`");
	}


	/* --------------------------------------------------
	 *	ACTIVERECORD CALLBACKS
	 * ----------------------------------------------- */


	/**
	 * Create a singular group for granular rules.
	 * 
	 * @access public
	 * @return void
	 */
	public function after_save()
	{
		// create and join a singular (i.e., granular) group
		$group = new Group();
		$group->name = $this->email;
		$group->singular = TRUE;
		$group->importance = 101;
		$group->save();
		
		$this->join_group($group);
		
		// join the global group
		$this->join_group(Group::find(1));
	}
}