<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rules extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->entityManager =& get_instance()->doctrine->em;
		$this->ns_manager = new \DoctrineExtensions\NestedSet\Manager(new \DoctrineExtensions\NestedSet\Config($this->entityManager, 'models\RBAC\Group'));
	}

	public function create()
	{
		if ($this->input->post()) {
			$rule = new \models\RBAC\Rule();
			$rule->setAllowed($this->input->post('allowed'));
			$rule->setGroup($this->entityManager->getReference('\models\RBAC\Group', $this->input->post('group_id')));
			$rule->setPrivilege($this->entityManager->getReference('\models\RBAC\Privilege', $this->input->post('privilege_id')));
			$rule->setResource($this->entityManager->getReference('\models\RBAC\Resource', $this->input->post('resource_id')));

			$this->entityManager->persist($rule);
			$this->entityManager->flush();
		}

		redirect($_SERVER['HTTP_REFERER']);
	}

	public function delete()
	{
		if ($this->input->post()) {
			$this->entityManager->remove($this->entityManager->find('models\RBAC\Rule', $this->input->post('rule_id')));
			$this->entityManager->flush();
		}

		redirect($_SERVER['HTTP_REFERER']);
	}
}