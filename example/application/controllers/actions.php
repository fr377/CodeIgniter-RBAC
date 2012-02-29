<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Actions extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->entityManager =& get_instance()->doctrine->em;
		$this->ns_manager = new \DoctrineExtensions\NestedSet\Manager(new \DoctrineExtensions\NestedSet\Config($this->entityManager, 'models\RBAC\Group'));
	}

	public function create()
	{
		if ($this->input->post()) {
			$action = new \models\RBAC\Action();
			$action->setName($this->input->post('name'));

			$this->entityManager->persist($action);
			$this->entityManager->flush();
		}

		redirect($_SERVER['HTTP_REFERER']);
	}

	public function delete()
	{
		if ($this->input->post()) {
			$this->entityManager->remove($this->entityManager->find('models\RBAC\Action', $this->input->post('action_id')));
			$this->entityManager->flush();
		}

		redirect($_SERVER['HTTP_REFERER']);
	}
}