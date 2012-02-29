<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Resources extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->entityManager =& get_instance()->doctrine->em;
		$this->ns_manager = new \DoctrineExtensions\NestedSet\Manager(new \DoctrineExtensions\NestedSet\Config($this->entityManager, 'models\RBAC\Group'));
	}

	public function create()
	{
		if ($this->input->post()) {
			$resource = new \models\RBAC\Resource();
			$resource->setName($this->input->post('name'));

			$rootNode = $this->entityManager->createQuery('SELECT r FROM models\RBAC\Resource r WHERE r.root = 1 AND r.lft = 1')->getResult();
			$rootNode = $this->ns_manager->wrapNode($rootNode[0]);
			$rootNode->addChild($resource);
		}

		redirect($_SERVER['HTTP_REFERER']);
	}


	public function delete()
	{
		if ($this->input->post()) {
			$resource = $this->entityManager->find('models\RBAC\Resource', $this->input->post('resource_id'));
			$resource = $this->ns_manager->wrapNode($resource);
			$resource->delete();
		}

		redirect($_SERVER['HTTP_REFERER']);
	}

	public function excludes()
	{
		if ($this->input->post()) {
			$resource = $this->entityManager->find('models\RBAC\Resource', $this->input->post('resource_id'));
			$entity = $this->entityManager->find('models\RBAC\Entity', $this->input->post('entity_id'));

			$resource->excludes($entity);
			$entity->leave_resource($resource);

			$this->entityManager->flush();
		}

		redirect($_SERVER['HTTP_REFERER']);
	}

	public function includes()
	{
		if ($this->input->post()) {
			$resource = $this->entityManager->find('models\RBAC\Resource', $this->input->post('resource_id'));
			$entity = $this->entityManager->find('models\RBAC\Entity', $this->input->post('entity_id'));

			$resource->includes($entity);
			$entity->join_resource($resource);
			
			$this->entityManager->flush();
		}

		redirect($_SERVER['HTTP_REFERER']);
	}
}