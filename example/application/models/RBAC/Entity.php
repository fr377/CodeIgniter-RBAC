<?php
namespace models\RBAC;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="RBAC_Entities")
 */
class Entity
{
	//
	//	ASSOCIATIONS
	//
	/**
	 * @ManyToMany(targetEntity="Resource", inversedBy="resources")
	 * @JoinTable(name="RBAC_EntityHasResource")
	 */
	protected $resources;

	public function getResources() { return $this->resources; }

	public function join_resource(Resource $resource) { $this->resources->add($resource); }
	public function leave_resource(Resource $resource) { $this->resources->removeElement($resource); }

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
	 * @Column(type="string", nullable=true)
	 */
	protected $description;
	
	public function getDescription() { return $this->description; }
	public function setDescription($description) { $this->description = $description; }

	//
	//	EVENTS
	//
	/**
	 * @PostPersist
	 */
	public function create_granular_resource()
	{
		$entityManager =& get_instance()->doctrine->em;

		$resource = new Resource();
		$resource->setName($this->name);
		$resource->setGranular(TRUE);

		$this->join_resource($resource);
		$resource->includes($this);

		$ns_manager = new \DoctrineExtensions\NestedSet\Manager(new \DoctrineExtensions\NestedSet\Config($entityManager, 'models\RBAC\Resource'));
		$rootNode = $ns_manager->fetchTree(2);
		$rootNode->addChild($resource);
	}

	/**
	 * @PostPersist
	 */
	public function join_global_resource()
	{
		$resource = get_instance()->doctrine->em->getReference('\models\RBAC\Resource', 1);
		
		$this->join_resource($resource);
		$resource->includes($this);
get_instance()->doctrine->em->flush();	// again, don't think I should have to do this .. see User (no prob) and Action (same prob) ... wtf???
	}


	//
	//	ACCESSIBLE METHODS
	//
	public function __construct()
	{
		$this->resources = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function is_allowed()
	{
		return TRUE;
	}
}