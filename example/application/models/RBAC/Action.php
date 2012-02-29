<?php
namespace models\RBAC;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="RBAC_Actions")
 */
class Action
{
	//
	//	ASSOCIATIONS
	//
	/**
	 * @ManyToMany(targetEntity="Privilege", inversedBy="actions")
	 * @JoinTable(name="RBAC_ActionHasPrivilege")
	 */
	protected $privileges;

	public function getPrivileges() { return $this->privileges; }

	public function grant(Privilege $privilege) { $this->privileges->add($privilege); }
	public function revoke(Privilege $privilege) { $this->privileges->removeElement($privilege); }
	public function inPrivilege(Privilege $privilege) { return $this->privileges->contains($privilege); }

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

	//
	//	EVENTS
	//
	/**
	 * @PostPersist
	 */
	public function create_granular_privilege()
	{
		$entityManager =& get_instance()->doctrine->em;

		$privilege = new Privilege();
		$privilege->setName($this->name);
		$privilege->setGranular(TRUE);

		$this->grant($privilege);
		$privilege->grant($this);

		$ns_manager = new \DoctrineExtensions\NestedSet\Manager(new \DoctrineExtensions\NestedSet\Config($entityManager, 'models\RBAC\Privilege'));
		$rootNode = $ns_manager->fetchTree(2);
		$rootNode->addChild($privilege);
	}

	/**
	 * @PostPersist
	 */
	public function join_global_privilege()
	{
		$privilege = get_instance()->doctrine->em->getReference('\models\RBAC\Privilege', 1);

		$this->grant($privilege);
		$privilege->grant($this);
// shouldn't have to flush here, but it doesn't seem to work otherwise ... wtf???
get_instance()->doctrine->em->flush();
	}

	//
	//	ACCESSIBLE METHODS
	//
	public function __construct()
	{
		$this->privileges = new \Doctrine\Common\Collections\ArrayCollection();
	}
}