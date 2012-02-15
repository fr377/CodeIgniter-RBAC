<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Resources extends CI_Controller {


	public function create()
	{
		if ($this->input->post()) {
			$resource = new Resource();
			$resource->name = $this->input->post('name');
			$resource->save();
		}

		redirect();
	}


	public function delete()
	{
		if ($this->input->post())
			Resource::find($this->input->post('id'))->delete();

		redirect();
	}


	public function exclude()
	{
		if ($this->input->post())
			Resource::find($this->input->post('resource_id'))->exclude(Entity::find($this->input->post('entity_id')));

		redirect('entity/' . $this->input->post('entity_id'));
	}


	public function subsume()
	{
		if ($this->input->post())
			Resource::find($this->input->post('resource_id'))->subsume(Entity::find($this->input->post('entity_id')));

		redirect('entity/' . $this->input->post('entity_id'));
	}
}