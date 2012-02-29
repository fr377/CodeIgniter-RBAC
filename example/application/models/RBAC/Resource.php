<?php
namespace models\RBAC;
use \DoctrineExtensions\NestedSet\MultipleRootNode;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @Entity
 * @Table(name="RBAC_EntityResources")
 */
class Resource implements MultipleRootNode
{
	//
	//	ASSOCIATIONS
	//
	/**
	 * @ManyToMany(targetEntity="Entity", mappedBy="resources")
	 */
	protected $entities;

	public function includes(Entity $entity) { $this->entities->add($entity); }
	public function excludes(Entity $entity) { $this->entities->removeElement($entity); }
	public function countEntities() { return $this->entities->count(); }
	public function hasEntities() { return $this->countEntities() > 0 ? TRUE : FALSE; }
	public function getEntities() { return $this->entities; }
	public function hasEntity(Entity $entity) { return $this->entities->contains($entity); }

	/**
	 * @OneToMany(targetEntity="Rule", mappedBy="resource")
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
	 * @Column(type="string", nullable=true)
	 */
	protected $description;
	
	public function getDescription() { return $this->description; }
	public function setDescription($description) { $this->description = $description; }

	/**
	 * @Column(type="boolean")
	 */
	protected $granular = FALSE;
	
	public function getGranular() { return $this->granular; }
	public function setGranular($boolean) { $this->granular = $boolean; }
	public function isGranular() { return $this->granular; }


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
		$this->entities = new \Doctrine\Common\Collections\ArrayCollection();
		$this->rules = new \Doctrine\Common\Collections\ArrayCollection();
	}
}