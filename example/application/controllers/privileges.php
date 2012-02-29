<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Privileges extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->entityManager =& get_instance()->doctrine->em;
		$this->ns_manager = new \DoctrineExtensions\NestedSet\Manager(new \DoctrineExtensions\NestedSet\Config($this->entityManager, 'models\RBAC\Group'));
	}

	public function create()
	{
		if ($this->input->post()) {
			$privilege = new \models\RBAC\Privilege();
			$privilege->setName($this->input->post('name'));

			$rootNode = $this->entityManager->createQuery('SELECT p FROM models\RBAC\Privilege p WHERE p.root = 1 AND p.lft = 1')->getResult();
			$rootNode = $this->ns_manager->wrapNode($rootNode[0]);
			$rootNode->addChild($privilege);
		}
		
		redirect($_SERVER['HTTP_REFERER']);
	}


	public function delete()
	{
		if ($this->input->post()) {
			$privilege = $this->entityManager->find('models\RBAC\Privilege', $this->input->post('privilege_id'));
			$privilege = $this->ns_manager->wrapNode($privilege);
			$privilege->delete();
		}

		redirect($_SERVER['HTTP_REFERER']);
	}

	public function grant()
	{
		if ($this->input->post()) {
			$privilege = $this->entityManager->find('models\RBAC\Privilege', $this->input->post('privilege_id'));
			$action = $this->entityManager->find('models\RBAC\Action', $this->input->post('action_id'));

			if ( ! $privilege->hasAction($action) ) {
				$privilege->grant($action);
				$action->grant($privilege);

				$this->entityManager->flush();
			}
		}

		redirect($_SERVER['HTTP_REFERER']);
	}

	public function revoke()
	{
		if ($this->input->post()) {
			$privilege = $this->entityManager->find('models\RBAC\Privilege', $this->input->post('privilege_id'));
			$action = $this->entityManager->find('models\RBAC\Action', $this->input->post('action_id'));

			if ($privilege->hasAction($action)) {
				$privilege->revoke($action);
				$action->revoke($privilege);

				$this->entityManager->flush();
			}
		}

		redirect($_SERVER['HTTP_REFERER']);
	}
}