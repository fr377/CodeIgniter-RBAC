<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inquiry extends CI_Controller
{

	public function index()
	{
		if ($this->input->post()) {
			// the who
			if ($this->input->post('user_id'))
				$who = User::find($this->input->post('user_id'));
			
			if ($this->input->post('group_id'))
				$who = Group::find($this->input->post('group_id'));

			// the action-verb
			if ($this->input->post('action_id'))
				$verb = Action::find($this->input->post('action_id'));
			
			if ($this->input->post('privilege_id'))
				$verb = Privilege::find($this->input->post('privilege_id'));
			
			// the object-noun
			if ($this->input->post('entity_id'))
				$noun = Entity::find($this->input->post('entity_id'));
			
			if ($this->input->post('resource_id'))
				$noun = Resource::find($this->input->post('resource_id'));
				
			// the punctuation?
			return var_dump($who->is_allowed($verb, $noun));
		}

		redirect();
	}
}