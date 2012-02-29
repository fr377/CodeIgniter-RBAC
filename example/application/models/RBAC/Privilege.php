<?php
namespace models\RBAC;
use \DoctrineExtensions\NestedSet\MultipleRootNode;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @Entity
 * @Table(name="RBAC_ActionPrivileges")
 */
class Privilege implements MultipleRootNode
{
	//
	//	ASSOCIATIONS
	//
	/**
	 * @ManyToMany(targetEntity="Action", mappedBy="privileges")
	 */
	protected $actions;

	public function getActions() { return $this->actions; }

	public function grant(Action $action) { $this->actions->add($action); }
	public function revoke(Action $action) { $this->actions->removeElement($action); }
	public function countActions() { return $this->actions->count(); }
	public function hasAction(Action $action) { return $this->actions->contains($action); }
	public function hasActions() { return $this->countActions() > 0 ? TRUE : FALSE; }

	/**
	 * @OneToMany(targetEntity="Rule", mappedBy="privilege")
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
		$this->actions = new \Doctrine\Common\Collections\ArrayCollection();
		$this->rules = new \Doctrine\Common\Collections\ArrayCollection();
	}
}