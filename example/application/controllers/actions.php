<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Actions extends CI_Controller {


	public function create()
	{
		if ($this->input->post()) {
			$action = new Action();
			$action->name = $this->input->post('name');
			$action->save();
		}
		
		redirect('action/' . $action->id);
	}


	public function delete()
	{
		if ($this->input->post() && $action = Action::find($this->input->post('id')))
			$action->delete();

		redirect();
	}


	public function grant()
	{
		if ($this->input->post()) {
			$privilege = Privilege::find($this->input->post('privilege_id'));
			$action = Action::find($this->input->post('action_id'));

			if ($privilege->allows($action))
				$privilege->grant($action);
		}

		redirect('action/' . $action->id);
	}


	public function revoke()
	{
		if ($this->input->post()) {
			$privilege = Privilege::find($this->input->post('privilege_id'));
			$action = Action::find($this->input->post('action_id'));

			if ($privilege->allows($action))
				$privilege->revoke($action);
		}

		redirect('action/' . $action->id);
	}
}