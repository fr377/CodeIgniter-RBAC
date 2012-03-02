<?php
namespace models\RBAC;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="RBAC_Users")
 */
class User
{
	//
	//	ASSOCIATIONS
	//
	/**
	 * @ManyToMany(targetEntity="Group", inversedBy="groups")
	 * @JoinTable(name="RBAC_UserHasGroup")
	 */
	protected $groups;

	public function getGroups() { return $this->groups; }
	
	public function joinGroup(Group $group) { $this->groups->add($group); }
	public function leaveGroup(Group $group) { $this->groups->removeElement($group); }
	public function inGroup(Group $group) {	return $this->groups->contains($group); }

	//
	//	PROPERTIES
	//
	/**
	 * @Id
	 * @Column(type="integer", nullable=false)
	 * @GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	public function getId() { return $this->id; }

	/**
	 * @Column(type="string", nullable=true)
	 */
	protected $first_name;
	
	public function getFirstName() { return $this->first_name; }
	public function setFirstName($first_name) { $this->first_name = $first_name; }

	/**
	 * @Column(type="string", nullable=true)
	 */
	protected $last_name;
	
	public function getLastName() { return $this->last_name; }
	public function setLastName($last_name) { $this->last_name = $last_name; }

	/**
	 * @Column(type="string")
	 */
	protected $email;
	
	public function getEmail() { return $this->email; }
	public function setEmail($email) { $this->email = $email; }

	//
	//	EVENTS
	//
	/**
	 * @PostPersist
	 */
	public function create_granular_group()
	{
		$entityManager =& get_instance()->doctrine->em;
		
		$group = new Group();
		$group->setName($this->email);
		$group->setGranular(TRUE);

		$this->joinGroup($group);
		$group->joinUser($this);
		
		$ns_manager = new \DoctrineExtensions\NestedSet\Manager(new \DoctrineExtensions\NestedSet\Config($entityManager, 'models\RBAC\Group'));
		$rootNode = $ns_manager->fetchTree(2);
		$rootNode->addChild($group);
	}

	/**
	 * @PostPersist
	 */
	public function join_global_group()
	{
		$group = get_instance()->doctrine->em->getReference('\models\RBAC\Group', 1);

		$this->joinGroup($group);
		$group->joinUser($this);
	}


	//
	//	ACCESSIBLE METHODS
	//
	public function __construct()
	{
		$this->groups = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function is_allowed($verb, $noun, $force_lookup = TRUE)
	{
		if ( ! ($verb instanceof Action)
			&& ! ($verb instanceof Privilege))
				throw new \Exception('Verb clause must be either an Action or Privilege object.');

		if ( ! ($noun instanceof Entity)
			&& ! ($noun instanceof Resource))
				throw new \Exception('Noun clause must be either an Entity or Resource object');

		$query_method = strtolower('_query_' . end(explode('\\', get_class($verb))) . '_on_' . end(explode('\\', get_class($noun))));
//return self::$query_method($verb, $noun)->getSql();

		$rules = self::$query_method($verb, $noun)->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

		// if no rules apply, run away! 
		if ( ! $rules ) return NULL;
return TRUE;
		$allowed = NULL;
		$importance_threshold = NULL;
		$weight_threshold = -1;

		do {
			$rule = array_pop($rules);
		
			if ($rule->importance < $importance_threshold)
				continue;

			$importance_threshold = $rule->importance;
			$weight = $rule->is_granular_privilege + $rule->is_granular_resource;

			if ($weight > $weight_threshold) {
				$allowed = $rule->allowed ? TRUE : FALSE;
				$weight_threshold = $weight;

			} else if ($weight == $weight_threshold && ! $rule->allowed)
				$allowed = FALSE;

		} while ($rules);

		return $allowed;
	}
	
	private function _query_action_on_entity(Action $action, Entity $entity)
	{
		$queryBuilder =& get_instance()->doctrine->em->createQueryBuilder();
		
		$queryBuilder
			->select(
				't1.allowed',
				't2.id',
				't2.granular',
				't4.id',
				't5.id',
				't5.granular',
				't7.id',
				't8.importance'
			)
			->from('models\RBAC\Rule', 't1')

			->innerJoin('t1.privilege', 't2')
			
			->innerJoin('t2.actions', 't4')
			->innerJoin('t1.resource', 't5')
			->innerJoin('t5.entities', 't7')
			->innerJoin('t1.group', 't8')
			->innerJoin('t8.users', 't9')

			->where('t9.id = ' . $this->getId())
			->andWhere('t4.id = ' . $action->getId())
			->andWhere('t7.id = ' . $entity->getId())

			->orderBy('t8.importance', 'DESC')
			->addOrderBy('t8.id');

		return $queryBuilder->getQuery();
	}

	private function _query_privilege_on_resource(Privilege $privilege, Resource $resource)
	{
//		$queryBuilder =& get_instance()->doctrine->em->createQueryBuilder();
//		
//		$queryBuilder
//			->select(
//				'rule',
//				'privilege'
//			)
//			->from('models\RBAC\Rule', 'rule')
//
//			->innerJoin('rule.group', 'group')
//				->innerJoin('group.users', 'user')
//
//			->innerJoin('rule.privilege', 'privilege')
//			->innerJoin('rule.resource', 'resource')
//
//			->where("user.id = {$this->getId()}")
//			->andWhere('privilege.id = 2')
//			->andWhere("resource.id = 1")
///* 			->andWhere("privilege.id = {$privilege->getId()}") */
///* 			->andWhere("resource.id = {$resource->getId()}") */
//
//			->orderBy('group.importance', 'DESC')
//			->addOrderBy('group.id');
//        \Doctrine\Common\Util\Debug::dump($resource->getRootValue(),3);exit;
                $entity_manager = get_instance()->doctrine->em;
                $dql   = "select ru from models\RBAC\Rule ru
                  inner join ru.group g
                  inner join g.users u
                            with u.id = :user_id
                  inner join ru.resource re
                           with re.id between :resource_lft and :resource_rgt and re.root = :route_id
                  inner join ru.privilege p
                  where 
                           p.lft between  
                           ( select min( p1.lft) from models\RBAC\Privilege p1 inner join p1.actions a1 with a1.name = :action_name ) 
                           and
                           ( select max( p2.rgt) from models\RBAC\Privilege p2 inner join p2.actions a2 with a2.name = :action_name ) 
                  ";
                $query = $entity_manager->createQuery($dql);
                $query->setParameters(
                    array(
                        "user_id"      => $this->id,
                        "resource_lft" => $resource->getLeftValue(),
                        "resource_rgt" => $resource->getRightValue(),
                        "route_id"     => $resource->getRootValue(),
                        "action_name"  => $privilege->getName()
                    )
                );
		return $query;
	}
}