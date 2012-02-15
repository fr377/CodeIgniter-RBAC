<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inquiry extends CI_Controller {


	public function index()
	{
		if ($this->input->post()) {
			$user = User::find($this->input->post('user_id'));
			$entity = Entity::find($this->input->post('entity_id'));
			$action = Action::find($this->input->post('action_id'));
			
			die(var_dump($user->is_allowed($entity, $action)));
		}

		redirect();
	}
}