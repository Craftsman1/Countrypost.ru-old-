<?php
require_once BASE_CONTROLLERS_PATH.'BaseController'.EXT;

function transliterate_gost ($string, $reverse=false) {
	static $ru = array(
		'А', 'а', 'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д', 'Е', 'е', 'Ё', 'ё', 'Ж', 'ж', 'З', 'з',
		'И', 'и', 'Й', 'й', 'К', 'к', 'Л', 'л', 'М', 'м', 'Н', 'н', 'О', 'о', 'П', 'п', 'Р', 'р',
		'С', 'с', 'Т', 'т', 'У', 'у', 'Ф', 'ф', 'Х', 'х', 'Ц', 'ц', 'Ч', 'ч', 'Ш', 'ш', 'Щ', 'щ',
		'Ъ', 'ъ', 'Ы', 'ы', 'Ь', 'ь', 'Э', 'э', 'Ю', 'ю', 'Я', 'я'
	);

	static $en = array(
		'A', 'a', 'B', 'b', 'V', 'v', 'G', 'g', 'D', 'd', 'E', 'e', 'E', 'e', 'Zh', 'zh', 'Z', 'z', 
		'I', 'i', 'J', 'j', 'K', 'k', 'L', 'l', 'M', 'm', 'N', 'n', 'O', 'o', 'P', 'p', 'R', 'r',
		'S', 's', 'T', 't', 'U', 'u', 'F', 'f', 'H', 'h', 'C', 'c', 'Ch', 'ch', 'Sh', 'sh', 'Sch', 'sch',
		'', '', 'Y', 'y',  '', '', 'E', 'e', 'Ju', 'ju', 'Ja', 'ja'
	);
	
	if(!$reverse) $string = str_replace($ru, $en, $string);
	else $string = str_replace($en, $ru, $string);
	return $string;
}

class User extends BaseController {

	function User()
	{
		parent::__construct();	
	}
	
	function index()
	{
		Func::redirect(BASEURL);
	}
	
	public function login($l = null, $p = null, $redirect = true, $vk = false)
	{
		// Дорефакторить этот огрызок
		if ($this->loginInternal())
		{
			return TRUE;
		}
		else
		{
			echo 'Wrong password or login';
			die();		
			
			return false;
		}
	}
	
	protected function loginInternal($l=null, $p=null, $redirect = true, $vk = false)
	{
		$login		= $l ? Check::var_str($l,32,1) : Check::str('login',	32,1);
		$password	= $p ? Check::var_str($p,32,1) : Check::str('password',	32,1);
		$password	= md5($password);
		$this->load->model('UserModel', 'User');
		
		if ($login && $password)
		{
			$user = $this->User->getUserForLogin($login, $password, $vk);

			if ($user)
			{
				$this->session->set_userdata((array) $user);
				$this->user = Check::user();
				$this->load->model('CountryModel', 'Countries');


				// запоминаем в сессию статистику по платежам для админа
				if ($user->user_group == 'admin') 
				{
					$this->session->set_userdata('user_name', 'Администрация Countrypost.ru');
					$this->session->set_userdata('user_country_name_en', 'Russia');

					$this->load->model('PaymentModel', 'Payment');
					$stat = $this->Payment->getSummaryStat();
					Stack::push('admin_summary_stat', $stat);
				}
				else if ($user->user_group == 'manager') 
				{
					$this->load->model('ManagerModel', 'Managers');
					$manager_summary = $this->Managers->getById($user->user_id);
					$user_summary = $this->User->getById($user->user_id);
					
					// находим местную валюту
					$this->load->model('CurrencyModel', 'Currency');
					$currency = $this->Currency->getCurrencyByCountry($manager_summary->manager_country);

					$country = $this->Countries->getById($manager_summary->manager_country);

					$this->session->set_userdata('user_country_name_en', $country->country_name_en);
					$this->session->set_userdata('user_name', $this->Managers->getFullName($manager_summary, $user));

					$this->session->set_userdata('manager_country', $manager_summary->manager_country);
					$this->session->set_userdata('country_name_en', $manager_summary->manager_country);
					$this->session->set_userdata('manager_credit', $manager_summary->manager_credit);
					$this->session->set_userdata('manager_credit_date', $manager_summary->manager_credit_date);
					$this->session->set_userdata('manager_credit_local', $manager_summary->manager_credit_local);
					$this->session->set_userdata('manager_credit_date_local', $manager_summary->manager_credit_date_local);
					$this->session->set_userdata('manager_balance_local', $manager_summary->manager_balance_local);
					$this->session->set_userdata('manager_currency', $currency->currency_symbol);
					$this->session->set_userdata('manager_website', $manager_summary->website);
					$this->session->set_userdata('manager_rating', $manager_summary->rating);
					$this->session->set_userdata('manager_status', $manager_summary->website);
					$this->session->set_userdata('manager_name', $this->Managers->getFullName($manager_summary, $user));
					$this->session->set_userdata('manager_login', $login);
				}
				else if ($user->user_group == 'client')
				{
					$this->load->model('ClientModel', 'Clients');
					$client_summary = $this->Clients->getById($user->user_id);
					$country = $this->Countries->getById($client_summary->client_country);

					$this->session->set_userdata('user_country_name_en', $country->country_name_en);
					$this->session->set_userdata('user_name', $this->Clients->getFullName($client_summary));
				}

				if ($redirect)
				{
					header('Location: '.BASEURL.$user->user_group);
				}
					
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	public function loginAjax($handler = NULL, $id = NULL)
	{
		$view['is_manager'] = 0;
		$view['is_client'] = 0;
		$view['allowed_segments'] = array();
		$view['segment'] = Check::str('segment', 32, 1);
		
		if ( ! $this->loginInternal(NULL, NULL, FALSE))
		{
			return;
		}

		if ($this->user->user_group == 'manager')
		{
			$view['is_manager'] = 1;
			$view['allowed_segments'][] = 'order';

		}
		elseif ($this->user->user_group == 'client')
		{
			$view['is_client'] = 1;
            $view['allowed_segments'][] = 'createorder';
            $view['allowed_segments'][] = 'online';
            $view['allowed_segments'][] = 'offline';
            $view['allowed_segments'][] = 'service';
            $view['allowed_segments'][] = 'delivery';
            $view['allowed_segments'][] = 'mailforwarding';
		}

		if (isset($handler) AND isset($id))
		{
			$this->getLoginData($handler, $id, $view);
		}

		if (isset($handler))
		{
			$this->load->view("/{$this->user->user_group}/elements/div_header", $view);
		}
		else if (isset($is_main_page))
		{
			$this->load->view("/main/elements/auth/success", $view);
		}
		else
		{
			$this->load->view("/{$this->user->user_group}/elements/auth/success", $view);
		}
	}

	public function loginAjaxMain()
	{
		$view['is_manager'] = 0;
		$view['is_client'] = 0;
		$view['allowed_segments'] = array();
		$view['segment'] = Check::str('segment', 32, 1);

		if ( ! $this->loginInternal(NULL, NULL, FALSE))
		{
			return;
		}

		if ($this->user->user_group == 'manager')
		{
			$view['is_manager'] = 1;
			$view['allowed_segments'][] = 'order';

		}
		elseif ($this->user->user_group == 'client')
		{
			$view['is_client'] = 1;
            $view['allowed_segments'][] = 'createorder';
            $view['allowed_segments'][] = 'online';
            $view['allowed_segments'][] = 'offline';
            $view['allowed_segments'][] = 'service';
            $view['allowed_segments'][] = 'delivery';
            $view['allowed_segments'][] = 'mailforwarding';
		}

		$this->load->view("/main/elements/auth/success", $view);
	}

	private function getLoginData($handler, $id, &$view)
	{
		if ($this->user->user_group == 'manager')
		{
			return $this->getManagerLoginData($handler, $id, $view);
		}
	}

	private function getManagerLoginData($handler, $id, &$view)
	{
		if ($handler == 'newBid' AND is_numeric($id))
		{
			$this->load->model('OrderModel', 'Orders');
			$order = $this->getPublicOrder($id, FALSE);

			if ($order)
			{
				$this->load->model('BidModel', 'Bids');

				$view['extra_view'] = 'newBid';

				if ($this->Bids->isBidAllowed($order, $this->user->user_id))
				{
					$this->Orders->prepareNewBidView($order, $this->user->user_id, TRUE);

					$view['extra_data']['order'] = $order;
					$view['extra_data']['bid'] = $this->generateNewBid($order);
				}
			}
		}
	}
	
	public function logout()
	{
		$this->load->model('UserModel', 'User');

		foreach ($this->User->getPropertyList() as $prop)
		{
			$this->session->unset_userdata(array($prop => ''));
		}
		
		header('Location: ' . BASEURL);
	}

	public function showPasswordRecovery()
	{
		Func::redirect(BASEURL . 'user/remindpassword');
	}	

	public function remindpassword()
	{
		Breadcrumb::setCrumb(array(BASEURL => 'Главная'), 0);
		Breadcrumb::setCrumb(array(BASEURL . 'user/remindpassword' => 'Восстановление пароля'), 1, TRUE);

		View::showChild($this->viewpath.'pages/recovery');
	}

	public function passwordRecovery()
	{
		$result = new stdClass();
		$result->e = 0;
		$result->m = '';	// сообщение
		$result->d = '';	// возвращаемые данные

		try
		{
			$email = Check::email(Check::str('email', 128, 4));

			if ($email)
			{
				$this->load->model('UserModel', 'User');
				$user = $this->User->getUserByEmail($email);

				if ($user)
				{
					$new_passwd = Func::randStr(6, 8);
					$this->User->_load($user);
					$this->User->_set('user_password', md5($new_passwd));

					$headers = 'From: info@countrypost.ru' . "\r\n" .
						'Reply-To: info@countrypost.ru' . "\r\n" .
						'X-Mailer: PHP/' . phpversion();

					if (mail($user->user_email,"Восстановление пароля", "Ваш новый пароль: $new_passwd", $headers) AND
						$this->User->save())
					{
						$result->m = 'Новый пароль установлен и выслан на указанный Вами адрес электронной почты.';
						$result->e = 1;
					}
					else
					{
						$result->m = 'Невозможно восстановить пароль.<br>
								Вероятно, указанный Вами почтовый ящик не существует<br>
								или не работает.';
						$result->e = -1;
					}
				}
				else
				{
					$result->e = -2;
					$result->m = 'Такой e-mail в системе не зарегистрирован.';
				}
			}
			else
			{
				$result->e = -3;
				$result->m = 'Вы ввели неправильный адрес электронной почты.';
			}
		}
		catch (Exception $ex)
		{
			$result->e	= -5;
			$result->m	= 'Попытка восстановления пароля не удалась.<br>
				Обновите страницу и попробуйте еще раз.';
		}

		View::showChild($this->viewpath.'pages/recovery', array('result' => $result));
	}
	
	public function showProfile(){ 
		/**
		 * load country list form stack, if it exists;
		 * so, we dont touch models every time
		 */
		$countries = '';
		if (Stack::size('all_countries')>0){
			$countries	= Stack::last('all_countries');
		}else{
			$this->load->model('CountryModel', 'Country');
			//$countries	= $this->Country->getList();
			$countries	= $this->Country->getToCountries();
		}
		
		if (!$this->user){
			$this->showRegistration();
			return false;
		}
		
		if (!Check::str('action',6,1)){
			$this->load->model('ClientModel', 'Client');
		
			View::showChild($this->viewpath. 'pages/profile' , array(
					'client'			=> $this->Client->getById($this->user->user_id),
					'Countries'		=> $countries,
			));
			return;
		}
		
		Check::reset_empties();
		$this->user->user_login			= Check::latin('login',32,1);
		$this->user->user_email			= Check::email(Check::str('email',128,6));
		
		$c											= new stdClass();
		$c->client_user						= $this->user->user_id;
		$c->client_name						= Check::latin('name',128,1);
		$c->client_otc						= Check::latin('otc',128,1);
		$c->client_surname					= Check::latin('surname',128,1);
		$c->client_country					= Check::int('country');
		$c->client_index					= Check::int('index');
		$c->client_town						= Check::latin('town',64,1);
		$c->client_address					= Check::latin('address',512,1);
		$c->client_phone_country			= Check::int('phone_country',99999);
		$c->client_phone_city				= Check::int('phone_city',99999);
		$c->client_phone_value				= Check::int('phone_value',99999999999);
		$c->notifications_on				= Check::chkbox('notifications_on');
		$empties							= Check::get_empties();
		
		$this->user->user_password	= Check::latin('password',32,1);
		$this->user->repassword		= Check::latin('repassword',32,1);
		
		try{

			if ($empties && in_array('_latin',$empties)){
				throw new Exception('Данные должны быть введены латиницей!', -14);
			}
			
			if ($this->user->user_password !== $this->user->repassword){
				throw new Exception('Пароли не совпадают.', -15);
			}			
			
			if ($empties){
				throw new Exception('Одно или несколько полей не заполнено!', -11);
			}

			if (!$this->user->user_email){
				throw new Exception('Не верный E-mail.', -13);
			}	

			$this->load->model('UserModel', 'User');
			
			$nu	= $this->User->getUserByLogin( $this->user->user_login);
			if ($nu && $nu->user_id != $this->user->user_id){
				throw new Exception('Пользователь с таким логином уже существует!', -17);
			}
			
			$ne	= $this->User->getUserByEmail( $this->user->user_email);
			if ($ne && $ne->user_id != $this->user->user_id){
				throw new Exception('Пользователь с такой электронной почтой уже существует!', -16);
			}
			
			if ($this->user->user_password)
				$this->user->user_password = md5($this->user->user_password);
			
			/**
			 * transactions
			 */
			$this->db->trans_start();
			// something same to lazzy load
			$this->load->model('ClientModel', 'Client');
			$c->client_phone = '+'.$c->client_phone_country.'('.$c->client_phone_city.')'.$c->client_phone_value;
			
			$this->User->_load($this->user);
			$this->Client->_load($c);
			
			if ($this->User->save() && $this->Client->save())
			{
				// меняем данные о пользователе в сесии
				$this->session->set_userdata((array) $this->user);
				
				$this->db->trans_complete();
				
				$this->result->e	= 1;
				$this->result->m	= 'Данные сохранены.';
				
			}
			else
			{
				throw new Exception('Регистрация невозможна.',-12);
			}
				
		}
		catch (Exception $e)
		{
			
			$this->result->e		= $e->getCode();			
			$this->result->m	= $e->getMessage();
			
			switch ($this->result->e){
				case -1:	$this->user->user_login		= '';	break;	
				case -15:	
					$this->user->user_password			= '';
					$this->user->repassword				= '';
				break;
				case -2:
				case -13:	$this->user->user_email		= '';	break;
				case -11:
				case -12:
					 break;
			}
		}
		
		View::showChild($this->viewpath.'pages/profile', array(
																'client'		=> $c,
																'Countries'		=> $countries,
																'empties'		=> $empties,
		));
	}
}