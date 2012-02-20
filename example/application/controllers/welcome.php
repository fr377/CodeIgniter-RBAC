<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {


	public function __construct()
	{
		parent::__construct();

		// since there is no login system build here, we'll just assume the identity of the prime user
		try {
			$this->user = Rbac\User::find(1);
		
		// if there's a problem here, just try to rebuild the database
		} catch (Exception $e) {
		}
	}


	public function index($lookup = NULL, $search = NULL)
	{
		$CI =& get_instance();
		
		$view_data = array(
			'groups'	=> Rbac\Group::all(),
			'user'		=> $this->user,
			'CI'		=> $CI,
			'display'	=> NULL
		);


		switch ($lookup) {

			case 'action':
				$view_data['display'] =  'Action';
				$view_data['search_id'] =  $search;
				break;

			case 'user':
				$view_data['display'] = 'User';
				$view_data['search_id'] = $search;
				break;

			case 'group':
				$view_data['display'] = 'Group';
				$view_data['search_id'] = $search;
				break;

			case 'resource':
				$view_data['display'] = Rbac\Resource::find($search, array('include' => array('components', 'rules')));
				break;

			case 'entity':
				$view_data['display'] = 'Entity';
				$view_data['search_id'] = $search;
				break;

			case 'privilege':
				$view_data['display'] = Rbac\Privilege::find($search, array('include' => array('liberties', 'rules')));
				break;


		}


		$this->load->view('welcome', $view_data);
	}


	public function setup()
	{
		Rbac::setup();
		redirect();
	}

	public function scenario($number)
	{
		Rbac::setup();
		$function = 'setup_scenario_' . $number;
		Rbac::$function();
		redirect();
	}

}