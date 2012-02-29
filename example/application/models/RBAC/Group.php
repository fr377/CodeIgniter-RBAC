<?php
namespace models\RBAC;
use \DoctrineExtensions\NestedSet\MultipleRootNode;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @Entity
 * @Table(name="RBAC_UserGroups")
 */
class Group implements MultipleRootNode
{
	//
	//	ASSOCIATIONS
	//
	/**
	 * @ManyToMany(targetEntity="User", mappedBy="groups")
	 */
	protected $users;

	public function getUsers() { return $this->users; }

	public function joinUser(User $user) { $this->users->add($user); }
	public function discharge(User $user) { $this->users->removeElement($user); }
	public function countMembers() { return $this->users->count(); }
	public function hasMembers() { return $this->countMembers() > 0 ? TRUE : FALSE;}

	/**
	 *	Bidirectional MANY-TO-ONE (Inverse side)
	 *
	 * @OneToMany(targetEntity="Rule", mappedBy="group")
	 */
	protected $rules;

	public function getRules() { return $this->rules; }
	public function countRules() { return $this->rules->count(); }
	public function hasRules() { return $this->countRules() > 0 ? TRUE : FALSE; }


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
	 * @Column(type="string")
	 */
	protected $name;

	public function getName() { return $this->name; }
	public function setName($name) { $this->name = $name; }

	/**
	 * @Column(type="boolean")
	 */
	protected $granular = FALSE;

	public function getGranular() { return $this->granular; }
	public function isGranular() { return $this->granular; }
	public function setGranular($boolean) { $this->granular = $boolean; }

	/**
	 * @Column(type="integer")
	 */
	protected $importance = 1;

	public function getImportance() { return $this->importance; }
	public function setImportance($importance) { $this->importance = $importance; }

	//
	//	NESTED SET (MULTIPLE ROOT)
	//
	/**
	 * @Column(type="integer")
	 */
	protected $lft;
	
	public function getLeftValue() { return $this->lft; }
	public function setLeftValue($left_key) { $this->lft = $left_key; }

	/**
	 * @Column(type="integer")
	 */
	protected $rgt;
	
	public function getRightValue() { return $this->rgt; }
	public function setRightValue($right_key) { $this->rgt = $right_key; }
	
	/**
	 * @Column(type="integer")
	 */
	protected $root;
	
	public function setRootValue($root) { $this->root = $root; }
	public function getRootValue() { return $this->root; }

	public function __toString() { return $this->name; }


	//
	//	PUBLIC METHODS
	//
	public function __construct()
	{
		$this->users = new \Doctrine\Common\Collections\ArrayCollection();
		$this->rules = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function is_allowed($verb, $noun, $force_lookup = TRUE)
	{
		return TRUE;
		
		if ( ! ($verb instanceof Action)
			&& ! ($verb instanceof Privilege))
				throw new \Exception('Verb clause must be either an Action or Privilege object.');

		if ( ! ($noun instanceof Entity)
			&& ! ($noun instanceof Resource))
				throw new \Exception('Noun clause must be either an Entity or Resource object');

		return self::_query_action_on_resource($verb, $noun);
/* 		return self::_query_action_on_entity($verb, $noun); */
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

		return $queryBuilder->getQuery()->getSQL();
	}

	private function _query_action_on_ns_resource(Action $action, Resource $resource)
	{}

	private function _query_action_on_resource(Action $action, Resource $resource)
	{
		$queryBuilder = get_instance()->doctrine->em->createQueryBuilder();
		$queryBuilder ->select(
				't1.allowed',
				't2.id',
				't2.granular',
				't4.id',
				't5.id',
				't5.granular',
				't8.importance'
			)
			->from('models\RBAC\Rule', 't1')

			->innerJoin('t1.privilege', 't2')
			->innerJoin('t2.actions', 't4')
			->innerJoin('t1.resource', 't5')
			->innerJoin('t1.group', 't8')
			->innerJoin('t8.users', 't9')

			->where('t9.id = ' . $this->getId())
			->andWhere('t4.id = ' . $action->getId())
			->andWhere('t1.resource = ' . $resource->getId())

			->orderBy('t8.importance', 'DESC')
			->addOrderBy('t8.id');

		return $queryBuilder->getQuery()->getSQL();
	}

	private function _query_privilege_on_entity(Privilege $privilege, Entity $entity)
	{}

	private function _query_privilege_on_resource(Privilege $privilege, Resource $resource)
	{}
}