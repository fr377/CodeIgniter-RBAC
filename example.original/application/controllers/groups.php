<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Groups extends CI_Controller {


	public function create()
	{
		if ($this->input->post()) {
			try {
				$group = new Group();
				$group->name = $this->input->post('name');
				$group->save();
			} catch (Exception $e) {
				die($e->getMessage());
			}
		}
		
		redirect();
	}


	public function change_importance()
	{
		if ($this->input->post())
			Group::find($this->input->post('id'))->update_attributes(array(
				'importance' => $this->input->post('importance')
			));
		
		redirect();
	}


	public function delete()
	{
		if ($this->input->post()) {
			$group = Group::find($this->input->post('id'));
			$group->delete();
		}

		redirect();
	}
	
	
	public function eject()
	{
		if ($this->input->post())
			User::find($this->input->post('user_id'))->leave_group(Group::find($this->input->post('group_id')));

		redirect();
	}
	

	/**
	 * Enroll a single user in a single group.
	 * 
	 * @access public
	 * @return void
	 */
	public function enroll()
	{
		if ($this->input->post())
			User::find($this->input->post('user_id'))->join_group(Group::find($this->input->post('group_id')));
		
		redirect('group/' . $this->input->post('group_id'));
	}


	/**
	 * Enroll user in groups. Requires a user_id and accepts md5(group name) and id pairings by POST.
	 * 
	 * @see Privileges->bestow() for maybe a cleaner way to avoid md5()
	 * @access public
	 * @return void
	 */
	public function enroll_bulk()
	{
		if ($this->input->post()) {
			$user = User::find($this->input->post('user_id'));
			
			// the md5() is a bit of a nasty hack to cover cases where the group name contains 'odd' characters (e.g., accents, spaces)
			foreach(Group::all() as $group) {
				if ($this->input->post(md5($group->name))) {
					if ( ! $user->in_group($group) ) {
						$user->join_group($group);
					}
				} else {
					if ($user->in_group($group))
						$user->leave_group($group);
				}
			}
		}

		redirect();
	}
}