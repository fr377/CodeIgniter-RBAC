<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->output->enable_profiler(TRUE);
		
		// since there is no true login system here, we'll just assume the identity of the prime user
		$this->user = $this->doctrine->em->find('models\RBAC\Group', 1);
	}

	public function index($lookup = NULL, $search = NULL)
	{
		$CI =& get_instance();
		
		$view_data = array(
			'groups'	=> NULL,
			'user'		=> $this->user,
			'CI'		=> $CI,
			'display'	=> NULL,
			'EM'		=> $this->doctrine->em
		);


		switch ($lookup) {

			case 'user':
				$view_data['display'] = 'User';
				$view_data['search_id'] = $search;
				break;

			case 'group':
				$view_data['display'] = 'Group';
				$view_data['search_id'] = $search;
				break;

			case 'entity':
				$view_data['display'] = 'Entity';
				$view_data['search_id'] = $search;
				break;

			case 'resource':
				$view_data['display'] = 'Resource';
				$view_data['search_id'] = $search;
				break;

			case 'action':
				$view_data['display'] =  'Action';
				$view_data['search_id'] =  $search;
				break;

			case 'privilege':
				$view_data['display'] = 'Privilege';
				$view_data['search_id'] =  $search;
				break;

		}


		$this->load->view('welcome', $view_data);





/*
$rootNode = $this->doctrine->em->createQuery('SELECT p FROM models\RBAC\Privilege p WHERE p.root = 2 AND p.lft = 1');
$rootNode = $rootNode->getResult();
var_dump($rootNode);
*/



		// ~0.0210s
/*
		$this->benchmark->mark('Query1_start');
		$this->doctrine->em->find('models\RBAC\Group', 1);
		$this->benchmark->mark('Query1_end');
*/

		// MUCH faster! ~0.0090s
/*
		$this->benchmark->mark('Query2_start');
		$query = $this->doctrine->em->createQuery('SELECT g FROM \models\RBAC\Group g WHERE g.id = 1');
		$group = $query->getResult();
		$this->benchmark->mark('Query2_end');
*/
		
/*
		$this->benchmark->mark('Query3_start');
		$group = get_instance()->doctrine->em->getRepository('models\RBAC\Group')->findOneById(3);
		$ns_manager = new \DoctrineExtensions\NestedSet\Manager(new \DoctrineExtensions\NestedSet\Config(get_instance()->doctrine->em, 'models\RBAC\Group'));
		$group = $ns_manager->wrapNode($group);
		var_dump($group->getPath());
		$this->benchmark->mark('Query3_end');
*/
		
/* 		print_r($group); */









/*
		get_instance()->benchmark->mark('BigLoad_start');
		$result = $this->doctrine->em->find('models\RBAC\User', 1)
			->is_allowed(
				$this->doctrine->em->find('models\RBAC\Action', 1),
				$this->doctrine->em->find('models\RBAC\Resource', 1)
		);
		
		print_r($result);
		get_instance()->benchmark->mark('BigLoad_end');
*/
		
/* 		$this->load->view('welcome_message'); */
	}
	
	public function setup()
	{
		$this->rbac->setup();
		redirect();
	}
	
	public function scenario($number)
	{
		$function = 'setup_scenario_' . $number;
		$this->rbac->$function();
		redirect();
	}
}