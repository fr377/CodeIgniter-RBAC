<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Privileges extends CI_Controller {


	public function grant()
	{
		if ($this->input->post()) {
			$privilege = Privilege::find($this->input->post('privilege_id'));
			$action = Action::find($this->input->post('action_id'));
			
			if ( ! $privilege->allows($action) )
				$privilege->grant($action);
		}

		redirect('privilege/' . $privilege->id);
	}
	
	
	public function revoke()
	{
		if ($this->input->post()) {
			$privilege = Privilege::find($this->input->post('privilege_id'));
			$action = Action::find($this->input->post('action_id'));

			if ($privilege->allows($action))
				$privilege->revoke($action);
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
		
		redirect('privilege/' . $privilege->id);
	}


	public function delete()
	{
		if ($this->input->post())
			Privilege::find($this->input->post('id'))->delete();

		redirect();
	}
}