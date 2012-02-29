<?php
namespace models\RBAC;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @Entity
 * @Table(name="RBAC_Rules")
 */
class Rule
{
	//
	//	ASSOCIATIONS
	//
	/**
	 * @ManyToOne(targetEntity="Privilege", inversedBy="rules")
	 */
	protected $privilege;

	public function getPrivilege() { return $this->privilege; }
	public function setPrivilege(Privilege $privilege) { $this->privilege = $privilege; }

	/**
	 *	Bidirectional MANY-TO-ONE (Owning side)
	 *
	 * @ManyToOne(targetEntity="Group", inversedBy="rules", cascade={"delete"})
	 */
	protected $group;
	
	public function getGroup() { return $this->group; }
	public function setGroup(Group $group) { $this->group = $group; }

	/**
	 * @ManyToOne(targetEntity="Resource", inversedBy="rules")
	 */
	protected $resource;

	public function getResource() { return $this->resource; }
	public function setResource(Resource $resource) { $this->resource = $resource; }


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
	 * @Column(type="boolean")
	 */
	protected $allowed;
	
	public function getAllowed() { return $this->allowed; }
	public function setAllowed($allowed) { $this->allowed = $allowed; }
	public function isAllowed() { return $this->allowed; }
    
	//
	//	ACCESSIBLE METHODS
	//
	public function __construct()
	{}
}