<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller {


	public function create()
	{
		if ($this->input->post()) {
			$user = new User();
			$user->email = $this->input->post('email');
			$user->save();
		}
		
		redirect();
	}


	public function delete()
	{
		if ($this->input->post() && $user = User::find($this->input->post('id')))
			$user->delete();

		redirect();
	}


	public function join_group()
	{
		if ($this->input->post()) {
			$user = User::find($this->input->post('user_id'));
			$group = Group::find($this->input->post('group_id'));
			
			if ( ! $user->in_group($group) )
				$user->join_group($group);
		}

		redirect();
	}


	public function leave_group()
	{
		if ($this->input->post()) {
			$user = User::find($this->input->post('user_id'));
			$group = Group::find($this->input->post('group_id'));
			
			if ( $user->in_group($group) )
				$user->leave_group($group);
		}
		
		redirect();
	}
}