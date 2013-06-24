<?php
require_once BASE_CONTROLLERS_PATH.'BaseController'.EXT;

class Clients extends BaseController {
	function __construct()
	{
		parent::__construct();	
		
		$this->paging_base_url = '/clients/index';	 
		View::$main_view	= '/main/index';
		Breadcrumb::setCrumb(array('/' => 'Главная'), 0);
		Breadcrumb::setCrumb(array('/clients' => 'Клиенты'), 1, TRUE);
	}
	
	function index() 
	{
		try
		{
			$this->load->model('ClientModel', 'Clients');
			
			// обработка фильтра
			$view['filter'] = $this->initFilter('Clients');
			
			$clients = $this->Clients->getClientsData($view['filter']);
			
			// пейджинг
			$per_page = isset($this->session->userdata['clients_per_page']) ? $this->session->userdata['clients_per_page'] : NULL;
			$per_page = isset($per_page) ? $per_page : $this->per_page;
			$this->per_page = $per_page;
						
			$this->init_paging();		
			$this->paging_count = count($clients);
			
			if ($clients)
			{
				$clients = array_slice($clients, $this->paging_offset, $this->per_page);
			}
			
			$statistics = array();
			foreach ($clients as $client)
			{
				$this->processStatistics($client, $statistics, 'client_user', 0, 'client');
			}
			
			$this->load->model('CountryModel', 'Country');
            $Countries  = parent::Country_Order_Prio();
			$countries = array();
			$countries_en = array();
			
			foreach ($Countries as $Country)
			{
				$countries[$Country->country_id] = $Country->country_name;
				$countries_en[$Country->country_id] = $Country->country_name_en;
			}
			
			$view = array(
				'clients' 	     => $clients,
				'countries'	     => $countries,
				'countries_en'	 => $countries_en,
				'statuses'	     => $this->Clients->getStatuses(),
				'per_page'	     => $per_page,
				'pager'		     => $this->get_paging(),
				'clients_filter' => $view['filter']
			);
			
			
			
			// парсим шаблон
			if ($this->uri->segment(4) == 'ajax')
			{
				$view['selfurl'] = $this->config->item('base_url').$this->cname.'/';
				$view['viewpath'] = $this->viewpath;
				$this->load->view("main/ajax/showClients", $view);
			}
			else
			{
				View::showChild("main/pages/showClients", $view);
			}
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
			Func::redirect($this->config->item('base_url').$this->cname);
		}	
	}
	
	public function updatePerPage($per_page)
	{
		if ( ! is_numeric($per_page))
		{
			throw new Exception('Доступ запрещен.');
		}
	
		$this->session->set_userdata(array('clients_per_page' => $per_page));
		Func::redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function filterClients()
	{
		$this->filter('Clients', 'showClients');
	}
	
	public function processClientsFilter(&$filter)
	{
		$filter->country_from = '';
		$filter->client_id = 0;
		$filter->login = 0;
		
		// сброс фильтра
		if (isset($_POST['resetFilter']) && $_POST['resetFilter'] == '1')
		{
			return $filter;
		}
		
		$filter->country_from = Check::int('country_from');
		$filter->client_id = Check::int('client_id');
		$filter->login = Check::str('login', 255, 0);
		
		return $filter;
	}
	
	public function showClients()
	{
		try
		{
			$this->load->model('ClientModel', 'Clients');
			
			// обработка фильтра
			$view['filter'] = $this->initFilter('Clients');
			
			$clients = $this->Clients->getClientsData($view['filter']);
			
			// пейджинг
			$per_page = isset($this->session->userdata['clients_per_page']) ? $this->session->userdata['clients_per_page'] : NULL;
			$per_page = isset($per_page) ? $per_page : $this->per_page;
			$this->per_page = $per_page;
			
			$this->init_paging();		
			$this->paging_count = count($clients);
		
			if ($clients)
			{
				$clients = array_slice($clients, $this->paging_offset, $this->per_page);
			}
			
			$statistics = array();
			foreach ($clients as $client)
			{
				$this->processStatistics($client, $statistics, 'client_user', 0, 'client');
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
				'clients' 	     => $clients,
				'countries'	     => $countries,
				'countries_en'	 => $countries_en,
				'statuses'	     => $this->Clients->getStatuses(),
				'per_page'	     => $per_page,
				'pager'		     => $this->get_paging(),
				'clients_filter' => $view['filter']
			);
		
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
			Func::redirect($this->config->item('base_url').$this->cname);
		}	
		
		$view['selfurl'] = $this->config->item('base_url').$this->cname.'/';
		$view['viewpath'] = $this->viewpath;
		$this->load->view("main/ajax/showClients", $view);
	}
}