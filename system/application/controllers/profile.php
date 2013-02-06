<?php
require_once BASE_CONTROLLERS_PATH.'BaseController'.EXT;

class Profile extends BaseController {
	function __construct()
	{
		parent::__construct();	
		
		$this->paging_base_url = '/profile/index';	 
		View::$main_view	= '/main/index';
		Breadcrumb::setCrumb(array('/' => 'Главная'), 0);
		Breadcrumb::setCrumb(array('/dealers' => 'Посредники'), 1);
	}
	
	public function index() 
	{
		switch ($this->user->user_group)
		{
			case 'client' : 
			{
				$this->editClientProfile();
				break;
			}
			case 'manager' : 
			{
				$this->editDealerProfile();
				break;
			}
			default : 
			{
				Func::redirect(BASEURL);
			}
		}		
	}
	
	public function router($login) 
	{
		try
		{
			$this->load->model('UserModel', 'Users');
			$user = $this->Users->getUserByLogin($login);

			if (empty($user))
			{
				Func::redirect(BASEURL);
			}
						
			$this->load->model('ManagerModel', 'Managers');
			$manager = $this->Managers->getById($user->user_id);
			
			if (empty($manager))
			{
				$this->load->model('ClientModel', 'Clients');
				$client = $this->Clients->getById($user->user_id);
			
				if (empty($client))
				{
					Func::redirect(BASEURL);
				}
				
				$this->showClientProfile($client, $login);
			}
			else
			{
				$this->showDealerProfile($manager, $login);
			}
		}
		catch (Exception $e) 
		{
			Func::redirect(BASEURL);
		}	
	}
	
	private function showDealerProfile($manager, $login)
	{
		$this->processStatistics($manager, array(), 'manager_user', $manager->manager_user, 'manager');
			
		Breadcrumb::setCrumb(array('/' . $login => $manager->statistics->fullname), 2);

		$this->dealerProfileGeneric($manager, $login, 'main/pages/dealer');
	}
	
	private function showClientProfile($client, $login)
	{
		$this->processStatistics($client, array(), 'client_user', $client->client_user, 'client');
		
		Breadcrumb::setCrumb(array('/' . 'clients' => 'Клиенты'), 1);
		Breadcrumb::setCrumb(array('/' . $login => $client->statistics->fullname), 2);

		$this->clientProfileGeneric($client, $login, 'main/pages/client');
	}
	
	private function editDealerProfile()
	{
		try
		{
			// находим партнера
			$this->load->model('ManagerModel', 'Managers');
			$manager = $this->Managers->getById($this->user->user_id);
			
			$this->processStatistics($manager, array(), 'manager_user', $manager->manager_user, 'manager');
		
			Breadcrumb::setCrumb(array('/profile' => 'Мой профиль'), 1, TRUE);
			
			$this->dealerProfileGeneric($manager, $this->session->userdata('manager_login'), 'manager/pages/editProfile');
		}
		catch (Exception $e) 
		{
			//Func::redirect(BASEURL);
		}		
	}

	private function editClientProfile()
	{
		try
		{
			// находим партнера
			$this->load->model('ClientModel', 'Clients');
			$client = $this->Clients->getById($this->user->user_id);
						
			$this->processStatistics($client, array(), 'client_user', $client->client_user, 'client');
		
			Breadcrumb::setCrumb(array('/profile' => 'Мой профиль'), 1, TRUE);
			
			$this->clientProfileGeneric($client, $this->session->userdata('client_login'), 'client/pages/editProfile');
			
		}
		catch (Exception $e) 
		{
			//Func::redirect(BASEURL);
		}		
	}

	private function dealerProfileGeneric($manager, $login, $view_name)
	{
		try
		{
			// находим статусы
			$view['statuses'] = $this->Managers->getStatuses();
				
			if ( ! $view['statuses'])
			{
				throw new Exception('Статусы не найдены. Попробуйте еще раз.');
			}
			
			$this->load->model('CurrencyModel', 'Currencies');		
				
			$view['manager_user'] = $manager->manager_user;
			$view['manager'] = $manager;
			$view['manager']->currency_symbol = $this->Currencies->getCurrencyByCountry($view['manager']->manager_country)->currency_symbol;
			
			$this->load->model('CountryModel', 'Country');
			$view['Countries'] = $this->Country->getList();

			$countries = array();
			$countries_en = array();

			foreach ($view['Countries'] as $Country)
			{
				$countries[$Country->country_id] = $Country->country_name;
				$countries_en[$Country->country_id] = $Country->country_name_en;
			}

			$view['countries'] = $countries;
			$view['countries_en'] = $countries_en;

			// блог
			$this->load->model('BlogModel', 'Blogs');
			$view['blogs']	= $this->Blogs->getBlogsByUserId($manager->manager_user);

			// доставка
			$view['deliveries']	= $this->Managers->getManagerDeliveries($manager->manager_user);
			
			// отзывы
			$statistics = array();
			$this->load->model('ManagerRatingsModel', 'Ratings');
			$this->load->model('ClientModel', 'Clients');
			$view['manager_ratings'] = $this->Ratings->getRatings($manager->manager_user);

			if ($view['manager_ratings'])
			{
				foreach ($view['manager_ratings'] as $rating)
				{
					$this->processStatistics($rating, $statistics, 'client_id', 0, 'client');
				}
			}

			View::showChild($view_name, $view);
		}
		catch (Exception $e) 
		{
		}
	}

	private function clientProfileGeneric($client, $login, $view_name)
	{	
		try
		{
			// находим страны
			$this->load->model('CountryModel', 'Country');		
			
			// находим статусы
			$view['statuses'] = $this->Clients->getStatuses();
				
			if ( ! $view['statuses'])
			{
				throw new Exception('Статусы не найдены. Попробуйте еще раз.');
			}
			
			$this->load->model('CurrencyModel', 'Currencies');		
				
			$view['client_user'] = $client->client_user;
			$view['client'] = $client;
			
			if ($currency = $this->Currencies->getCurrencyByCountry($view['client']->client_country))
			{
				$view['client']->currency_symbol = $currency->currency_symbol;
			}
			else
			{
				$view['client']->currency_symbol = '';
			}
			
			$this->load->model('CountryModel', 'Country');
			$view['Countries'] = $this->Country->getList();
			
			foreach ($view['Countries'] as $Country)
			{
				$countries[$Country->country_id] = $Country->country_name;
				$countries_en[$Country->country_id] = $Country->country_name_en;
			}

            // находим адреса
            $this->load->model('AddressModel', 'Addresses');
            $addresses = $this->Addresses->getAddressesByUserId($client->statistics->client_user);

			$view['countries'] = $countries;
            $view['countries_en'] = $countries_en;
            $view['addresses'] = $addresses;
						
			View::showChild($view_name, $view);
		}
		catch (Exception $e) 
		{
		}
	}
}