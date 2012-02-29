<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Rbac class.
 *
 * 'Fine-grained' role based access control library for CodeIgniter.
 *
 * @todo Delete associated singular privilege when deleting an action
 * @todo I think there's a bug in User::is_allowed() ... it doesn't account for All Groups weight.
 *
 * @see https://github.com/blt04/doctrine2-nestedset
 * @see http://www.doctrine-project.org/projects/orm/2.1/docs/en
 * @see http://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/
 */
class Rbac
{
	protected $entityManager;

	public function __construct()
	{
		$this->entityManager =& get_instance()->doctrine->em;
	}

	public function setup()
	{
		//
		//	GROUPS
		//
		$ns_manager = new \DoctrineExtensions\NestedSet\Manager(new \DoctrineExtensions\NestedSet\Config($this->entityManager, 'models\RBAC\Group'));

		$all_users = new \models\RBAC\Group();
		$all_users->setName('All Users');
		$rootNode = $ns_manager->createRoot($all_users);

		$granular_groups = new \models\RBAC\Group();
		$granular_groups->setName('Granular');
		$rootNode = $ns_manager->createRoot($granular_groups);


		// Privileges
		$ns_manager = new \DoctrineExtensions\NestedSet\Manager(new \DoctrineExtensions\NestedSet\Config($this->entityManager, 'models\RBAC\Privilege'));

		$all_privileges = new \models\RBAC\Privilege();
		$all_privileges->setName('All Privileges');
		$rootNode = $ns_manager->createRoot($all_privileges);

		$granular_privileges = new \models\RBAC\Privilege();
		$granular_privileges->setName('Granular');
		$granular_root = $ns_manager->createRoot($granular_privileges);


		// Resources
		$ns_manager = new \DoctrineExtensions\NestedSet\Manager(new \DoctrineExtensions\NestedSet\Config($this->entityManager, 'models\RBAC\Resource'));

		$all_resources = new \models\RBAC\Resource();
		$all_resources->setName('All Resources');
		$rootNode = $ns_manager->createRoot($all_resources);

		$granular_resources = new \models\RBAC\Resource();
		$granular_resources->setName('Granular');
		$granular_root = $ns_manager->createRoot($granular_resources);

		//
		//	RULES
		$rule = new \models\RBAC\Rule();
		$rule->setAllowed(TRUE);
		$rule->setGroup($all_users);
		$rule->setPrivilege($all_privileges);
		$rule->setResource($all_resources);
		$this->entityManager->persist($rule);

		$this->entityManager->flush();
	}
	
	public function setup_scenario_1()
	{
		self::setup();

		//
		//	GROUPS
		//
		$ns_manager = new \DoctrineExtensions\NestedSet\Manager(new \DoctrineExtensions\NestedSet\Config($this->entityManager, 'models\RBAC\Group'));

		$tier_2_1 = new \models\RBAC\Group();
		$tier_2_1->setName('Group (1)');

		$tier_2_2 = new \models\RBAC\Group();
		$tier_2_2->setName('Group (2)');

		$tier_2_3 = new \models\RBAC\Group();
		$tier_2_3->setName('Group (3)');

		$rootNode = $this->entityManager->createQuery('SELECT g FROM models\RBAC\Group g WHERE g.root = 1 AND g.lft = 1')->getResult();
		$rootNode = $ns_manager->wrapNode($rootNode[0]);
		$rootNode->addChild($tier_2_1);
		$rootNode->addChild($tier_2_2);
		$rootNode->addChild($tier_2_3);


		//
		//	USERS
		//
		$user = new \models\RBAC\User();
		$user->setEmail('peter@example.com');
		$user->setFirstName('Peter');
		$user->setLastName('Castell');
		$this->entityManager->persist($user);

		$user = new \models\RBAC\User();
		$user->setEmail('karen@example.com');
		$user->setFirstName('Karen');
		$user->setLastName('Campbell');
		$this->entityManager->persist($user);

		$random_users = array();

		for ($i = 1; $i <= 5; $i++) {
			$random_users[$i] = new \models\RBAC\User();
			$random_users[$i]->setEmail("dummy{$i}@example.com");
			$random_users[$i]->setFirstName('Firstname');
			$random_users[$i]->setLastName('Lastname');
			$this->entityManager->persist($random_users[$i]);
		}
		
		$random_users[1]->joinGroup($tier_2_1);
		$random_users[2]->joinGroup($tier_2_1);
		
		$random_users[1]->joinGroup($tier_2_2);
		$random_users[2]->joinGroup($tier_2_2);
		$random_users[3]->joinGroup($tier_2_2);

		$random_users[4]->joinGroup($tier_2_3);
		$random_users[5]->joinGroup($tier_2_3);
		

		//
		//	PRIVILEGES
		//
		$ns_manager = new \DoctrineExtensions\NestedSet\Manager(new \DoctrineExtensions\NestedSet\Config($this->entityManager, 'models\RBAC\Privilege'));

		$tier_2_1 = new \models\RBAC\Privilege();
		$tier_2_1->setName('Second tier (1)');

		$tier_2_2 = new \models\RBAC\Privilege();
		$tier_2_2->setName('Second tier (2)');

		$rootNode = $this->entityManager->createQuery('SELECT p FROM models\RBAC\Privilege p WHERE p.root = 1 AND p.lft = 1')->getResult();
		$rootNode = $ns_manager->wrapNode($rootNode[0]);
		$rootNode->addChild($tier_2_1);
		$rootNode->addChild($tier_2_2);

		$tier_3_1 = new \models\RBAC\Privilege();
		$tier_3_1->setName('Third tier (1)');

		$rootNode = $ns_manager->wrapNode($tier_2_2);
		$rootNode->addChild($tier_3_1);

		//
		//	ACTIONS
		//
		$action_1 = new \models\RBAC\Action();
		$action_1->setName('Action 1');
		$this->entityManager->persist($action_1);

		$action_2 = new \models\RBAC\Action();
		$action_2->setName('Action 2');
		$this->entityManager->persist($action_2);

		$action_3 = new \models\RBAC\Action();
		$action_3->setName('Action 3');
		$this->entityManager->persist($action_3);

		$action_1->grant($tier_2_1);
		$action_2->grant($tier_2_1);
		$action_3->grant($tier_2_1);

		$action_1->grant($tier_2_2);
		$action_2->grant($tier_2_2);

		$action_3->grant($tier_3_1);


		//
		//	RESOURCES
		//
		$ns_manager = new \DoctrineExtensions\NestedSet\Manager(new \DoctrineExtensions\NestedSet\Config($this->entityManager, 'models\RBAC\Resource'));

		$tier_2_1 = new \models\RBAC\Resource();
		$tier_2_1->setName('Second tier (1)');

		$tier_2_2 = new \models\RBAC\Resource();
		$tier_2_2->setName('Second tier (2)');

		$rootNode = $this->entityManager->createQuery('SELECT r FROM models\RBAC\Resource r WHERE r.root = 1 AND r.lft = 1')->getResult();
		$rootNode = $ns_manager->wrapNode($rootNode[0]);
		$rootNode->addChild($tier_2_1);
		$rootNode->addChild($tier_2_2);

		$tier_3_1 = new \models\RBAC\Resource();
		$tier_3_1->setName('Third tier (1)');

		$rootNode = $ns_manager->wrapNode($tier_2_1);
		$rootNode->addChild($tier_3_1);

		$tier_3_2 = new \models\RBAC\Resource();
		$tier_3_2->setName('Third tier (2)');

		$rootNode = $ns_manager->wrapNode($tier_2_2);
		$rootNode->addChild($tier_3_2);


		//
		//	ENTITIES
		//
		$entities = array();
		for ($i = 1; $i <= 5; $i++) {
			$entities[$i] = new \models\RBAC\Entity();
			$entities[$i]->setName('Entity ' . $i);
			$this->entityManager->persist($entities[$i]);
		}

		$entities[1]->join_resource($tier_2_1);
		$entities[2]->join_resource($tier_2_1);
		$entities[3]->join_resource($tier_2_1);

		$entities[4]->join_resource($tier_2_2);
		$entities[5]->join_resource($tier_2_2);

		$entities[1]->join_resource($tier_3_1);
		$entities[2]->join_resource($tier_3_1);
		$entities[3]->join_resource($tier_3_1);
		$entities[4]->join_resource($tier_3_1);
		$entities[5]->join_resource($tier_3_1);

		$entities[1]->join_resource($tier_3_2);
		$entities[2]->join_resource($tier_3_2);
		$entities[5]->join_resource($tier_3_2);


		$this->entityManager->flush();
	}
}