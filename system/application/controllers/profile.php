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
		if (empty($this->user->user_group))
		{
			Func::redirect($this->config->item('base_url'));
		}

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
				Func::redirect($this->config->item('base_url'));
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
				Func::redirect($this->config->item('base_url'));
			}
						
			$this->load->model('ManagerModel', 'Managers');
			$manager = $this->Managers->getById($user->user_id);
			
			if (empty($manager))
			{
				$this->load->model('ClientModel', 'Clients');
				$client = $this->Clients->getById($user->user_id);
			
				if (empty($client))
				{
					Func::redirect($this->config->item('base_url'));
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
			Func::redirect($this->config->item('base_url'));
		}	
	}

    public function getMoreBlogAjax($user_id,$start,$count)
    {

        $this->load->model('BlogModel', 'Blogs');
        $rows = $this->Blogs->getBlogsByUserId($user_id,$start,$count);
        echo json_encode($rows);
    }
	
	private function showDealerProfile($manager, $login)
	{
		$this->processStatistics($manager, array(), 'manager_user', $manager->manager_user, 'manager');
			
		Breadcrumb::setCrumb(array('/' . $login => $manager->statistics->fullname), 2);
		
		if(!isset($manager->order_tax) || $manager->order_tax==null)
		{
			$manager->order_tax=0;
		}
		if(!isset($manager->order_mail_forwarding_tax) || $manager->order_mail_forwarding_tax==null)
		{
			$manager->order_mail_forwarding_tax=0;
		}

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
			if(!isset($manager->order_tax) || $manager->order_tax==null)
			{
				$manager->order_tax=0;
			}
			if(!isset($manager->order_mail_forwarding_tax) || $manager->order_mail_forwarding_tax==null)
			{
				$manager->order_mail_forwarding_tax=0;
			}
			
			$this->dealerProfileGeneric($manager, $this->session->userdata('manager_login'), 'manager/pages/editProfile');
		}
		catch (Exception $e) 
		{
			//Func::redirect($this->config->item('base_url'));
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
			//Func::redirect($this->config->item('base_url'));
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
			$view['blogs']	= $this->Blogs->getBlogsByUserId($manager->manager_user,0,5);
            $view['blogs_allcount'] = $this->Blogs->getBlogsByUserIdAllCount($manager->manager_user);

			// доставка
			$view['deliveries']	= $this->Managers->getManagerDeliveries($manager->manager_user);
			
			// отзывы
			$statistics = array();
			$this->load->model('ManagerRatingsModel', 'Ratings');
			$this->load->model('RatingCommentModel', 'Comments');
			$this->load->model('ClientModel', 'Clients');
			$view['manager_ratings'] = $this->Ratings->getRatings($manager->manager_user);

			// комментарии
			if ($view['manager_ratings'])
			{
				foreach ($view['manager_ratings'] as $rating)
				{
                    $this->processStatistics($rating, $statistics, 'client_id', 0, 'client');
					$rating->comments = $this->Comments->getCommentsByRatingId($rating->rating_id);

                    $client_summary = $this->Clients->getById($rating->client_id);
                    $rating->statistics->client_name = $this->Clients->getFullName($client_summary);

					// находим данные комментатора для каждого коммента
					foreach ($rating->comments as $comment)
					{
                        if ($comment->user_id == $rating->client_id)
						{
							$this->processStatistics($comment, $statistics, 'user_id', $comment->user_id, 'client');
                            $client_summary = $this->Clients->getById($comment->user_id);
                            $comment->statistics->client_name = $this->Clients->getFullName($client_summary);
                        }
						else if ($comment->user_id == $rating->manager_id)
						{
							$this->processStatistics($comment, $statistics, 'user_id', $comment->user_id, 'manager');
                            $manager_summary = $this->Managers->getById($comment->user_id);
                            $comment->statistics->client_name = $this->Managers->getFullName($manager_summary);
                        }else{
                            $this->processStatistics($comment, $statistics, 'user_id', $comment->user_id, 'client');
                        }
					}
				}
			}

			//количество заказов в работе
			$this->load->model('OrderModel','Orders');
			$view['filter'] = $this->initFilter('Orders');
			$view['filter']->order_statuses = $this->Orders->getFilterStatuses();

			$count =0;
			// находим заказы по статусу и фильтру
			$orders = $this->Orders->getOrders(
				$view['filter'],
				'open',
				NULL,
				$manager->manager_user);
			if($orders)
				$count += count($orders);
			
			$orders = $this->Orders->getOrders(
				$view['filter'],
				'payed',
				NULL,
				$manager->manager_user);
			if($orders)
				$count += count($orders);

			$view['orders_in_work']=(int)$count;

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

            $countries = array();
            $countries_en = array();
			
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

    public function addRatingComment($id_rating)
    {

        $comment_message = Check::str('comment', 65535);

        $comment = new stdClass();
        $comment->rating_id = $id_rating;
        $comment->user_id = $this->user->user_id;
        $comment->message = $comment_message;
        $comment->status = 'active';

        $this->load->model('RatingCommentModel', 'Comments');
        $comment = $this->Comments->addComment($comment);

        $this->load->model('ClientModel', 'Clients');
        $this->load->model('ManagerModel', 'Managers');

        $statistics = array();


        if ($this->user->user_group == "client")
        {

            $client = $this->Clients->getById($this->user->user_id);
            $this->processStatistics($client, $statistics, 'client_user', $client->client_user, 'client');
            $comment->statistics->client_country = $client->client_country;
            $comment->statistics->login = $client->statistics->login;
            $comment->statistics->fullname = $client->statistics->fullname;
            $client_summary = $this->Clients->getById($comment->user_id);
            $comment->statistics->client_name = $this->Clients->getFullName($client_summary);

        }elseif($this->user->user_group == "manager"){

            $manager = $this->Managers->getById($this->user->user_id);
            $this->processStatistics($manager, $statistics, 'manager_user', $manager->manager_user, 'manager');
            $comment->statistics->manager_country = $manager->manager_country;
            $comment->statistics->login = $manager->statistics->login;
            $comment->statistics->fullname = $manager->statistics->fullname;
            $manager_summary = $this->Managers->getById($comment->user_id);
            $comment->statistics->client_name = $this->Managers->getFullName($manager_summary);
        }


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

        echo '<tr class="comment">';
            echo '<td>';
                View::show('main/elements/ratings/comment', array('comment' => $comment,
                    'countries_en' =>$countries_en));
            echo '</td>';
        echo '</tr>';

    }

    public function delRating($id_rating,$manager_id)
    {
        $this->load->model('RatingCommentModel', 'Comments');
        $this->load->model('ManagerRatingsModel', 'Ratings');

        $this->Comments->delRating($id_rating);
        $this->Ratings->updateManagerRating($manager_id);

        echo "ok";
    }

    public function delCommentRating($id_message)
    {
        $this->load->model('RatingCommentModel', 'Comments');

        $this->Comments->delCommentRating($id_message);

        echo "ok";
    }

    public function getPriceTemplateOfCountry()
    {
        $country_id = Check::int('country', 65535);
        $manager_country = Check::int('manager_country', 65535);

        if ( file_exists('system/application/views/manager/elements/templates/delivery/'.$manager_country.'/Tarrifs.php'))
        {
            $Tarrifs = $this->load->view('manager/elements/templates/delivery/'.$manager_country.'/Tarrifs.php', '', true);

            preg_match_all('/{country} = (\d+)/',$Tarrifs,$country);
            preg_match_all('/{first_kg} = ([0-9.]+)/',$Tarrifs,$first_kg);
            preg_match_all('/{second_kg} = ([0-9.]+)/',$Tarrifs,$second_kg);
            preg_match_all('/{extra_rate} = ([0-9.]+)/',$Tarrifs,$extra_rate);

            // Находим в тарифах страну направления
            $i=0;
            foreach ($country[1] as $id)
            {
                if ( $id == $country_id ) {
                    break;
                }
                $i++;
            }

            $country = $country[1][$i];
            $first_kg = $first_kg[1][$i];
            $second_kg = $second_kg[1][$i];
            $extra_rate = $extra_rate[1][$i];

            // Ищем файлы шаблонов
            $templateFiles = array();
            if ($handle = opendir('system/application/views/manager/elements/templates/delivery/'.$manager_country)) {

                while (false !== ($file = readdir($handle))) {
                    if ( preg_match('/\w+-template/',$file) ){
                        array_push($templateFiles,$file);
                    }
                }
                closedir($handle);
            }

            // Сканируем найденные файлы
            foreach($templateFiles as $files)
            {

                $template = file_get_contents('system/application/views/manager/elements/templates/delivery/'.$manager_country.'/'.$files);

                preg_match('/before_table_text = "(.+)"/',$template,$before_table_text);
                preg_match('/after_table_text = "(.+)"/',$template,$after_table_text);

                preg_match_all('/([0-9.]+) = (.+)/',$template,$templateTable);
                $associativArray = array();
                $pattern = array('/{first_kg}/','/{second_kg}/','/{extra_rate}/');
                $replace = array($first_kg,$second_kg,$extra_rate);
                for ($i=0; $i < count($templateTable[2]); $i++)
                {
                    $result = preg_replace($pattern,$replace,$templateTable[2][$i]);
                    if ( !preg_match('/(\{[0-9.]+\})/',$result,$matches) )
                    {
                        $resultEval = 0;
                        eval("\$resultEval = ".$result.";");
                        $associativArray[$templateTable[1][$i]] = $resultEval;
                    }else{
                        preg_match('/([0-9.]+)/',$matches[0],$matches2);
                        $result2 = preg_replace('/\{'.$matches2[0].'\}/',$associativArray[$matches2[0]],$templateTable[2][$i]);
                        $result3 = preg_replace($pattern,$replace,$result2);
                        $resultEval = 0;
                        eval("\$resultEval = ".$result3.";");
                        $associativArray[$templateTable[1][$i]] = $resultEval;
                    }
                }

                // строим таблицу
                $i=1;
                $data = array();
                foreach($associativArray as $k=>$v)
                {
                    //echo $k ." = ". $v ."<br />";
                    $data[$i] = array($k,$v);
                    $i++;
                }
                $this->CreateTable($data,3,$before_table_text[1],$after_table_text[1]);

            }


        }else{
            echo "";
        }

    }

    /*
     * @var data - array массив с данными
     * @var col - int кол-во столбцов
     */
    public function CreateTable($data,$col=1,$before_table_text="",$after_table_text="")
    {
        $viewdata = array("data" => $data, "cols" => $col, "before_table_text" => $before_table_text, "after_table_text" => $after_table_text);
        $this->load->view('manager/elements/templates/table_template',$viewdata);
    }

}