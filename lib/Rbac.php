<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Rbac class.
 *
 * 'Fine-grained' role based access control library for CodeIgniter
 *
 */
class Rbac
{

	public function __construct()
	{
		require_once(__CLASS__ . '/action.php');
		require_once(__CLASS__ . '/privilege.php');
		require_once(__CLASS__ . '/liberty.php');
		
		require_once(__CLASS__ . '/user.php');
		require_once(__CLASS__ . '/group.php');
		require_once(__CLASS__ . '/membership.php');
		
		require_once(__CLASS__ . '/entity.php');
		require_once(__CLASS__ . '/resource.php');
		require_once(__CLASS__ . '/component.php');
		
		require_once(__CLASS__ . '/rule.php');
	}


	public static function setup_scenario_1()
	{
		Group::create(array('name' => 'Unactivated accounts', 'importance' => 40));
		Group::create(array('name' => 'Client accounts', 'importance' => 60));
		Group::create(array('name' => 'Proofreaders', 'importance' => 70));
		Group::create(array('name' => 'Editors', 'importance' => 80));
		Group::create(array('name' => 'Manager', 'importance' => 90));
		
		Resource::create(array('name' => 'Public pages'));
		Resource::create(array('name' => 'Signed-in pages'));
		Resource::create(array('name' => 'Backend pages'));
		
		Action::create(array('name' => 'View'));
		Action::create(array('name' => 'Update'));
		Action::create(array('name' => 'Delete'));
	}
	

	public static function setup_scenario_2()
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
		$resource->name = 'big things';
		$resource->save();
		
		$resource->subsume($entity1);
		$resource->subsume($entity3);
		$resource->subsume($entity5);
		$resource->subsume($entity7);
		$resource->subsume($entity9);
		
		$resource = new Rbac\Resource();
		$resource->name = 'little things';
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
		$action6->name = 'domesticate';
		$action6->save();
		
		$action7 = new Rbac\Action();
		$action7->name = 'play';
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
		$privilege->name = 'trained professional';
		$privilege->save();
		$privilege->grant($action6);
		$privilege->grant($action7);
		$privilege->grant($action8);
		$privilege->grant($action9);
		$privilege->grant($action10);
		
		$privilege = new Rbac\Privilege();
		$privilege->name = 'reckless amateur';
		$privilege->save();
		$privilege->grant($action1);
		$privilege->grant($action2);
		$privilege->grant($action3);
		$privilege->grant($action4);
		$privilege->grant($action5);
		
		$group1 = new Rbac\Group();
		$group1->name = 'recruited from Walmart parking lot';
		$group1->save();
		
		$group2 = new Rbac\Group();
		$group2->name = 'recruited from circus loading dock';
		$group2->save();
		
		$user = new Rbac\User();
		$user->email = 'l337@diy.net';
		$user->save();
		$user->join_group($group1);
		
		$user = new Rbac\User();
		$user->email = 'dr.doolittle@cannus.edu';
		$user->save();
		$user->join_group($group2);
	}


	public static function setup()
	{
/* 		self::_drop_db(); */
/* 		self::_create_db(); */
		self::_reset_schema();
		self::_reset_records();
		self::_reset_relations();
	}
	

	private static function _create_db()
	{
		$CI =& get_instance();
		$CI->db->query("CREATE DATABASE `acl` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;");
		$CI->db->query("USE `p7_rbac`");
	}


	private static function _drop_db()
	{
		$CI =& get_instance();
		$CI->db->query("DROP DATABASE `p7_rbac");
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
		Rbac\Component::db_destroy();
		Rbac\Liberty::db_destroy();
		Rbac\Membership::db_destroy();
		Rbac\Rule::db_destroy();

		Rbac\Entity::db_destroy();
		Rbac\Action::db_destroy();
		Rbac\Group::db_destroy();
		Rbac\Privilege::db_destroy();
		Rbac\Resource::db_destroy();
		Rbac\User::db_destroy();

		Rbac\Action::db_create();
		Rbac\Component::db_create();
		Rbac\Entity::db_create();
		Rbac\Group::db_create();
		Rbac\Liberty::db_create();
		Rbac\Membership::db_create();
		Rbac\Privilege::db_create();
		Rbac\Resource::db_create();
		Rbac\Rule::db_create();
		Rbac\User::db_create();
	}
}