<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Privileges extends CI_Controller {


	public function bestow()
	{
		if ($this->input->post() && $privilege = Privilege::find($this->input->post('privilege_id'), array('include' => array('liberties')))) {

			foreach (Action::all() as $action) {
				if ($this->input->post($action->id)) {
					if ( ! $privilege->allows($action) )
						$privilege->grant($action);
				} else {
					if ($privilege->allows($action))
						$privilege->revoke($action);
				}
			}
		}

		redirect('privilege/' . $privilege->id);
	}


	public function create()
	{
		if ($this->input->post()) {
			$privilege = new Privilege();
			$privilege->name = $this->input->post('name');
			$privilege->save();
		}
		
		redirect();
	}


	public function delete()
	{
		if ($this->input->post() && $privilege = Privilege::find($this->input->post('id')))
			$privilege->delete();

		redirect();
	}


	public function revoke()
	{
		if ($this->input->post()
			&& $privilege = Privilege::find($this->input->post('privilege_id'))
			&& $action = Action::find($this->input->post('action_id')))
				$privilege->revoke($action);
		
		redirect();
	}	
}