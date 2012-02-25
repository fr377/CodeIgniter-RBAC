<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Entities extends CI_Controller {


	public function create()
	{
		if ($this->input->post()) {
			$entity = new Entity();
			$entity->name = $this->input->post('name');
			$entity->save();
		}
		
		redirect();
	}


	public function delete()
	{
		if ($this->input->post() && $entity = Entity::find($this->input->post('id')))
			$entity->delete();

		redirect();
	}
}