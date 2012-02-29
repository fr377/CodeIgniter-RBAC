<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inquiry extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->entityManager =& get_instance()->doctrine->em;
		$this->ns_manager = new \DoctrineExtensions\NestedSet\Manager(new \DoctrineExtensions\NestedSet\Config($this->entityManager, 'models\RBAC\Group'));
	}

	public function index()
	{
		if ($this->input->post()) {
			// the who
			if ($this->input->post('user_id'))
				$who = $this->entityManager->find('models\RBAC\User', $this->input->post('user_id'));

			if ($this->input->post('group_id'))
				$who = $this->entityManager->find('models\RBAC\Group', $this->input->post('group_id'));

			// the action-verb
			if ($this->input->post('privilege_id'))
				$verb = $this->entityManager->find('models\RBAC\Privilege', $this->input->post('privilege_id'));

			// the object-noun
			if ($this->input->post('resource_id'))
				$noun = $this->entityManager->find('models\RBAC\Resource', $this->input->post('resource_id'));

			// the punctuation?
			return var_dump($who->is_allowed($verb, $noun));
		}

		redirect();
	}
}