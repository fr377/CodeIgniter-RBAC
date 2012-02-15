<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Actions extends CI_Controller {


	public function create()
	{
		if ($this->input->post()) {
			$action = new Action();
			$action->name = $this->input->post('name');
			$action->save();
		}
		
		redirect();
	}


	public function delete()
	{
		if ($this->input->post() && $action = Action::find($this->input->post('id')))
			$action->delete();

		redirect();
	}
}