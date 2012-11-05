<?php
require_once BASE_CONTROLLERS_PATH.'BaseController'.EXT;

class Dealers extends BaseController {
	function __construct()
	{
		parent::__construct();	
		
		$this->paging_base_url = '/dealers/index';	 
		View::$main_view	= '/main/index';
		Breadcrumb::setCrumb(array('/' => 'Главная'), 0);
		Breadcrumb::setCrumb(array('/dealers' => 'Посредники'), 1, TRUE);
	}
	
	function index() 
	{
		try
		{
			$this->load->model('ManagerModel', 'Managers');
			$managers = $this->Managers->getManagersData();
			
			// пейджинг
			$per_page = isset($this->session->userdata['dealers_per_page']) ? $this->session->userdata['dealers_per_page'] : NULL;
			$per_page = isset($per_page) ? $per_page : $this->per_page;
			$this->per_page = $per_page;
			
			$this->init_paging();		
			$this->paging_count = count($managers);
		
			if ($managers)
			{
				$managers = array_slice($managers, $this->paging_offset, $this->per_page);
			}
			
			$statistics = array();
			foreach ($managers as $manager)
			{
				$this->processStatistics($manager, $statistics, 'manager_user', 0, 'manager');
			}
			
			$this->load->model('CountryModel', 'Country');
			$Countries	= $this->Country->getList();
			$countries = array();
			$countries_en = array();
			
			foreach ($Countries as $Country)
			{
				$countries[$Country->country_id] = $Country->country_name;
				$countries_en[$Country->country_id] = $Country->country_name_en;
			}
			
			$view = array(
				'managers' 	=> $managers,
				'countries'	=> $countries,
				'countries_en'	=> $countries_en,
				'statuses'	=> $this->Managers->getStatuses(),
				'per_page'	=> $per_page,
				'pager'		=> $this->get_paging()
			);
		
			// парсим шаблон
			if ($this->uri->segment(4) == 'ajax')
			{
				$view['selfurl'] = BASEURL.$this->cname.'/';
				$view['viewpath'] = $this->viewpath;
				$this->load->view("main/ajax/showDealers", $view);
			}
			else
			{
				View::showChild("main/pages/showDealers", $view);
			}
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
			Func::redirect(BASEURL.$this->cname);
		}	
	}
	
	public function updatePerPage($per_page)
	{
		if ( ! is_numeric($per_page))
		{
			throw new Exception('Доступ запрещен.');
		}
	
		$this->session->set_userdata(array('dealers_per_page' => $per_page));
		Func::redirect($_SERVER['HTTP_REFERER']);
	}
}