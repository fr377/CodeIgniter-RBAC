<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rules extends CI_Controller {


	public function create()
	{
		if ($this->input->post()) {
			$rule = new Rule();
			$rule->privilege_id = $this->input->post('privilege_id');
			$rule->group_id = $this->input->post('group_id');
			$rule->resource_id = $this->input->post('resource_id');
			$rule->allowed = $this->input->post('allowed');
			$rule->save();
		}
		
		redirect();
	}


	public function delete()
	{
		if ($this->input->post())
			Rule::find($this->input->post('id'))->delete();

		redirect();
	}
}