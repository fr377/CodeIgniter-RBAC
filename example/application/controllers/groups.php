<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Groups extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->entityManager =& get_instance()->doctrine->em;
		$this->ns_manager = new \DoctrineExtensions\NestedSet\Manager(new \DoctrineExtensions\NestedSet\Config($this->entityManager, 'models\RBAC\Group'));
	}

	public function change_importance()
	{
		if ($this->input->post()) {
			$group = $this->entityManager->find('models\RBAC\Group', $this->input->post('group_id'));
			$group->setImportance($this->input->post('importance'));
			$this->entityManager->flush();
		}

		redirect($_SERVER['HTTP_REFERER']);
	}

	public function create()
	{
		if ($this->input->post()) {
			$group = new \models\RBAC\Group();
			$group->setName($this->input->post('name'));

			$rootNode = $this->entityManager->createQuery('SELECT g FROM models\RBAC\Group g WHERE g.root = 1 AND g.lft = 1')->getResult();
			$rootNode = $this->ns_manager->wrapNode($rootNode[0]);
			$rootNode->addChild($group);
		}

		redirect($_SERVER['HTTP_REFERER']);
	}

	public function delete()
	{
		if ($this->input->post()) {
			$group = $this->entityManager->find('models\RBAC\Group', $this->input->post('group_id'));
			$group = $this->ns_manager->wrapNode($group);
			$group->delete();
		}

		redirect($_SERVER['HTTP_REFERER']);
	}
	
	
	public function discharge()
	{
		if ($this->input->post()) {
			$group = $this->entityManager->find('models\RBAC\Group', $this->input->post('group_id'));
			$user = $this->entityManager->find('models\RBAC\User', $this->input->post('user_id'));

			$group->discharge($user);
			$user->leaveGroup($group);

			$this->entityManager->flush();
		}

		redirect($_SERVER['HTTP_REFERER']);
	}
	

	/**
	 * Enroll a single user in a single group.
	 * 
	 * @access public
	 * @return void
	 */
	public function enlist()
	{
		if ($this->input->post()) {
			$group = $this->entityManager->find('models\RBAC\Group', $this->input->post('group_id'));
			$user = $this->entityManager->find('models\RBAC\User', $this->input->post('user_id'));

			$group->joinUser($user);
			$user->joinGroup($group);
			
			$this->entityManager->flush();
		}

		redirect($_SERVER['HTTP_REFERER']);
	}
}