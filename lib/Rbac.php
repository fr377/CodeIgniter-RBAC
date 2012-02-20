<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Rbac class.
 *
 * 'Fine-grained' role based access control library for CodeIgniter.
 *
 */
class Rbac
{

	public function __construct()
	{
		require_once(__CLASS__ . '/Action.php');
		require_once(__CLASS__ . '/Privilege.php');
		require_once(__CLASS__ . '/Liberty.php');
		
		require_once(__CLASS__ . '/User.php');
		require_once(__CLASS__ . '/Group.php');
		require_once(__CLASS__ . '/Membership.php');
		
		require_once(__CLASS__ . '/Entity.php');
		require_once(__CLASS__ . '/Resource.php');
		require_once(__CLASS__ . '/Component.php');
		
		require_once(__CLASS__ . '/Rule.php');
	}


	public static function setup_scenario_1()
	{
		$entity1 = new Rbac\Entity();
		$entity1->name = 'thing one';
		$entity1->save();
		
		$entity2 = new Rbac\Entity();
		$entity2->name = 'thing two';
		$entity2->save();
		
		$entity3 = new Rbac\Entity();
		$entity3->name = 'thing three';
		$entity3->save();
		
		$entity4 = new Rbac\Entity();
		$entity4->name = 'thing four';
		$entity4->save();

		$entity5 = new Rbac\Entity();
		$entity5->name = 'thing five';
		$entity5->save();

		$entity6 = new Rbac\Entity();
		$entity6->name = 'thing six';
		$entity6->save();

		$entity7 = new Rbac\Entity();
		$entity7->name = 'thing seven';
		$entity7->save();
		
		$entity8 = new Rbac\Entity();
		$entity8->name = 'thing eight';
		$entity8->save();
		
		$entity9 = new Rbac\Entity();
		$entity9->name = 'thing nine';
		$entity9->save();
		
		$entity10 = new Rbac\Entity();
		$entity10->name = 'thing ten';
		$entity10->save();
		
		$resource = new Rbac\Resource();
		$resource->name = 'Biological things';
		$resource->save();
		
		$resource->subsume($entity1);
		$resource->subsume($entity3);
		$resource->subsume($entity5);
		$resource->subsume($entity7);
		$resource->subsume($entity9);
		
		$resource = new Rbac\Resource();
		$resource->name = 'In the vacuum of space';
		$resource->save();

		$resource = new Rbac\Resource();
		$resource->name = 'On earth';
		$resource->save();

		$resource = new Rbac\Resource();
		$resource->name = 'Psionic things';
		$resource->save();

		$resource = new Rbac\Resource();
		$resource->name = 'Extra-terrestrial things';
		$resource->save();

		$resource = new Rbac\Resource();
		$resource->name = 'Terrestrial things';
		$resource->save();

		$resource = new Rbac\Resource();
		$resource->name = 'Mechanical things';
		$resource->save();

		$resource->subsume($entity2);
		$resource->subsume($entity4);
		$resource->subsume($entity6);
		$resource->subsume($entity8);
		$resource->subsume($entity10);
		
		$action1 = new Rbac\Action();
		$action1->name = 'see';
		$action1->save();
		
		$action2 = new Rbac\Action();
		$action2->name = 'taste';
		$action2->save();
		
		$action3 = new Rbac\Action();
		$action3->name = 'touch';
		$action3->save();
		
		$action4 = new Rbac\Action();
		$action4->name = 'hear';
		$action4->save();
		
		$action5 = new Rbac\Action();
		$action5->name = 'smell';
		$action5->save();
		
		$action6 = new Rbac\Action();
		$action6->name = 'fly';
		$action6->save();
		
		$action7 = new Rbac\Action();
		$action7->name = 'read minds';
		$action7->save();

		$action8 = new Rbac\Action();
		$action8->name = 'irritate';
		$action8->save();

		$action9 = new Rbac\Action();
		$action9->name = 'invisibilify';
		$action9->save();

		$action10 = new Rbac\Action();
		$action10->name = 'levitate';
		$action10->save();
		
		$privilege = new Rbac\Privilege();
		$privilege->name = 'good guy moral code';
		$privilege->save();
		$privilege->grant($action6);
		$privilege->grant($action7);
		$privilege->grant($action8);
		$privilege->grant($action9);
		$privilege->grant($action10);
		
		$privilege = new Rbac\Privilege();
		$privilege->name = 'bad guy moral code';
		$privilege->save();
		$privilege->grant($action1);
		$privilege->grant($action2);
		$privilege->grant($action3);
		$privilege->grant($action4);
		$privilege->grant($action5);
		
		$group1 = new Rbac\Group();
		$group1->name = 'Northern Freedom Alliance';
		$group1->save();
		
		$group2 = new Rbac\Group();
		$group2->name = 'The Bloody Mummers';
		$group2->save();
		
		$user = new Rbac\User();
		$user->email = 'l337@hotmai1.com';
		$user->save();
		$user->join_group($group1);
		
		$user = new Rbac\User();
		$user->email = 'hot2trot627@hotmai1.com';
		$user->save();
		$user->join_group($group2);
	}


	public static function setup()
	{
		self::_reset_schema();
		self::_reset_records();
/* 		self::_reset_relations(); */
	}
	

	private static function _reset_records()
	{
		$privilege = new Rbac\Privilege();
		$privilege->name = 'All actions';
		$privilege->save();
		
		// global users group has id 1
		$group = new Rbac\Group();
		$group->name = 'All users';
		$group->save();
		
		// likewise, the administrator naturally is id 1
		$user = new Rbac\User();
		$user->email = 'admin@example.com';
		$user->first_name = 'Administrator';
		$user->save();

		// now we need some objects:
		// an object container
		$resource = new Rbac\Resource();
		$resource->name = 'All resources';
		$resource->save();

		// someone said 'admin', right?
		$rule = new Rbac\Rule();
		$rule->group_id = 1;
		$rule->privilege_id = 1;
		$rule->resource_id = 1;
		$rule->allowed = TRUE;
		$rule->save();
	}
	

	private static function _reset_relations()
	{
		Rbac\Component::db_relations();
		Rbac\Liberty::db_relations();
		Rbac\Membership::db_relations();
		Rbac\Rule::db_relations();
	}
	
	
	private static function _reset_schema()
	{
		Rbac\Component::db_create();
		Rbac\Liberty::db_create();
		Rbac\Membership::db_create();
		Rbac\Rule::db_create();
		
		Rbac\Action::db_create();
		Rbac\Entity::db_create();
		Rbac\Group::db_create();
		Rbac\Privilege::db_create();
		Rbac\Resource::db_create();
		Rbac\User::db_create();
	}
}