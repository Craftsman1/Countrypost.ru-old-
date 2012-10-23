<?php
require_once BASE_CONTROLLERS_PATH.'BaseController'.EXT;

function transliterate_gost ($string,$reverse=false) {
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
		echo "<center><b>User->index</b></center>";
	}
	
	public function login ($l=null, $p=null, $redirect = true, $vk = false)
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
	
	private function loginInternal($l=null, $p=null, $redirect = true, $vk = false)
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
				
				// запоминаем в сессию статистику по платежам для админа
				if ($user->user_group == 'admin') 
				{
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
	
	public function loginManagerAjax()
	{
		$view['logged_in'] = 0;
		$view['is_manager'] = 0;
		
		if ($this->loginInternal(NULL, NULL, FALSE))
		{
			$view['logged_in'] = 1;
			$view['user'] = $this->user;

			if ($this->user->user_group == 'manager')
			{
				$view['is_manager'] = 1;
			}
		}
		
		$this->load->view("/manager/elements/div_header", $view);
	}
	
	public function logout(){
		
		$this->load->model('UserModel', 'User');

		foreach ($this->User->getPropertyList() as $prop){
			$this->session->unset_userdata(array($prop=>''));
		}
		
		header('Location: '.BASEURL);
	}

	public function facebook($code = NULL) {
		
		if(strstr($code,"code=")) {
			$code = str_replace("code=","",$code);
			$access_token = file_get_contents("https://graph.facebook.com/oauth/access_token?client_id=".FB_APP_ID."&redirect_uri=http://{$_SERVER["HTTP_HOST"]}/user/facebook&client_secret=".FB_APP_SECRET."&code={$code}");
			$access_token = @ereg_replace("[&]expires=([0-9]+)","",$access_token);
			$access_token = str_replace("access_token=","",$access_token);
			$me = json_decode(file_get_contents("https://graph.facebook.com/me?access_token={$access_token}"),true);
			$user_id = $me["id"]; 
			$info = json_decode(file_get_contents("https://api.facebook.com/method/users.getInfo?uids={$user_id}&fields=name,email,current_location&access_token={$access_token}&format=json"),true);
			$info["country"] = $info[0]['current_location']['country'];
			$info["city"] = $info[0]['current_location']['city'];
			$email = $info[0]['email'];

			$user					= new stdClass();
			$user->user_login		= "fb".$user_id;
			$tmppw = random_string();
			$user->user_password	= md5($tmppw);
			$user->repassword		= $tmppw;
			$user->user_email		= ($email!=""?"id".$user_id."@facebook.com":$email);
			$user->user_deleted		= 0; 
			$user->user_group		= 'client';
			
			$c						= new stdClass();
			$c->client_name			= transliterate_gost($me["first_name"]);
			$c->client_surname		= transliterate_gost($me["last_name"]);
			
			
			$c->client_country		= $info["country"];
			$c->client_town			= transliterate_gost($info["city"]);

			$country_rus = json_decode(iconv("KOI8-R","UTF-8",file_get_contents("http://translate.google.ru/translate_a/t?client=x&text={$info["country"]}&hl=ru&sl=en&tl=ru")),true);
			$country_rus = $country_rus["sentences"][0]["trans"];
			$this->load->model('CountryModel', 'Country');
			$countries	= $this->Country->getList();
			foreach ($countries as $country) {
				if ($country->country_name == $country_rus) {
					$c->client_country = $country->country_id;
					break;
				}
			}
		$this->load->model('UserModel', 'User');
		if ($user->user_login!="" && $this->User->select(array('user_login'=> $user->user_login, 'user_deleted'=>'0')))
		{
			$this->login($user->user_login, $user->repassword, true, true);
		} else {
			$this->db->trans_start();
			// something same to lazzy load
			$u = $this->User->addUser($user);
			
			$this->load->model('ClientModel', 'Client');
			
			if ($u && $this->Client->addClientData($u->user_id, $c))
			{
				// добавляем партнеров этому клиенту
				$this->db->trans_complete();
				
				$this->db->trans_start();
				
				$this->User->_load($u);
				
				$this->load->model('ManagerModel', 'Manager');
				$this->load->model('C2mModel', 'C2m');
		
				$managers = $this->Manager->getManagers();
	
				// добавляем связку и выставляем дату добавления клиента
				if ($managers['all']) 
				{
					foreach ($managers['all'] as $manager) 
					{
						$relation = new stdClass();
						$relation->client_id = $u->user_id;
						$relation->manager_id = $manager->manager_user;
						
						//$managers = $this->C2m->addRelation($relation);
						$this->C2m->addRelation($relation);
						
						$manager->last_client_added = date('Y-m-d H:i:s', time());
						$manager = $this->Manager->updateManager($manager); 
						
						if (!$manager)
						{
							throw new Exception('Невозможно добавить нового клиента к партнеру. Попробуйте еще раз.');
						}
					}
				}
	
				// увеличиваем счетчик клиентов у заполненных партнеров
				if (isset($managers['addons']))
				{
					foreach ($managers['addons'] as $manager) 
					{
						$manager->manager_max_clients += 1;
						$manager = $this->Manager->updateManager($manager);
						
						if (!$manager)
						{
							throw new Exception('Невозможно обновить данные по клиентам у партнера. Попробуйте еще раз.');
						}
					}
				}
	
				$this->db->trans_complete();
								
				$this->login($user->user_login,$user->repassword, true, true);
			}
			else
			{
				throw new Exception('Регистрация невозможна.',-12);
			}				
		}	
			
			
		} else { 
			echo "$code";
		
		}

		
		exit(0);
		
	}
	
	/**
	 * Авторизация вконтакте.ру
	 *
	 * @param unknown_type $type
	 */
	public function vkontakte($code = NULL)
	{
		@ob_clean();
		error_reporting(0);
		$code = str_replace("code=","",$code);
		
//		echo "Code: ".$code."--></br>";
		$response = json_decode(file_get_contents("https://oauth.vkontakte.ru/access_token?client_id=".VK_APP_ID."&client_secret=".VK_APP_SECRET."&code={$code}"),true);
		$access_token = $response["access_token"];
		$user_id = $response["user_id"];
//		echo "UserID:       ".$response["user_id"]."<br/>";
		$response = json_decode(file_get_contents("https://api.vkontakte.ru/method/getProfiles?uid={$user_id}&fields=uid,first_name,last_name,nickname,screen_name,sex,bdate,city,country,timezone,photo,photo_medium,photo_big,has_mobile,rate,contacts,education,online,counters&access_token={$access_token}"),true);
		$profile = $response["response"][0];
		$response = json_decode(file_get_contents("https://api.vkontakte.ru/method/places.getCountryById?cids={$profile["country"]}&access_token={$access_token}"),true);
		$profile["country"] = $response["response"][0]["name"];
		$response = json_decode(file_get_contents("https://api.vkontakte.ru/method/places.getCityById?cids={$profile["city"]}&access_token={$access_token}"),true);
		$profile["city"] = $response["response"][0]["name"];
		$this->load->model('CountryModel', 'Country');
		$countries	= $this->Country->getList();
		foreach ($countries as $country) {
			if ($country->country_name == $profile["country"]) {
				$profile["country_id"] = $country->country_id;
				break;
			}
		}

//		echo "<pre>";
//		print_r($profile);
		
		$user					= new stdClass();
		$user->user_login		= "vk".$user_id;
		$tmppw = random_string();
		$user->user_password	= md5($tmppw);
		$user->repassword		= $tmppw;
		$user->user_email		= "id".$user_id."@vkontakte.ru";
		$user->user_deleted		= 0; 
		$user->user_group		= 'client';
		
		$c						= new stdClass();
		$c->client_name			= transliterate_gost($profile["first_name"]);
		$c->client_surname		= transliterate_gost($profile["last_name"]);
		
		
		$c->client_country		= $profile["country_id"];
//		$c->client_index		= Check::int('index');
		$c->client_town			= transliterate_gost($profile["city"]);
//		$c->client_address		= Check::latin('address',512,1);
//		$c->client_phone_country= Check::int('phone_country',99999);
//		$c->client_phone_city	= Check::int('phone_city',99999);
//		$c->client_phone_value	= Check::int('phone_value',99999999999);
//
		$this->load->model('UserModel', 'User');
		if ($user->user_login!="" && $this->User->select(array('user_login'=> $user->user_login, 'user_deleted'=>'0')))
		{
			$this->login($user->user_login, $user->repassword, true, true);
		} else {

			$this->db->trans_start();
			// something same to lazzy load
			$u = $this->User->addUser($user);
			
			$this->load->model('ClientModel', 'Client');
			
			if ($u && $this->Client->addClientData($u->user_id, $c))
			{
				// добавляем партнеров этому клиенту
				$this->db->trans_complete();
				
				$this->db->trans_start();
				
				$this->User->_load($u);
				
				$this->load->model('ManagerModel', 'Manager');
				$this->load->model('C2mModel', 'C2m');
		
				$managers = $this->Manager->getManagers();
	
				// добавляем связку и выставляем дату добавления клиента
				if ($managers['all']) 
				{
					foreach ($managers['all'] as $manager) 
					{
						$relation = new stdClass();
						$relation->client_id = $u->user_id;
						$relation->manager_id = $manager->manager_user;
						
						//$managers = $this->C2m->addRelation($relation);
						$this->C2m->addRelation($relation);
						
						$manager->last_client_added = date('Y-m-d H:i:s', time());
						$manager = $this->Manager->updateManager($manager); 
						
						if (!$manager)
						{
							throw new Exception('Невозможно добавить нового клиента к партнеру. Попробуйте еще раз.');
						}
					}
				}
	
				// увеличиваем счетчик клиентов у заполненных партнеров
				if (isset($managers['addons']))
				{
					foreach ($managers['addons'] as $manager) 
					{
						$manager->manager_max_clients += 1;
						$manager = $this->Manager->updateManager($manager);
						
						if (!$manager)
						{
							throw new Exception('Невозможно обновить данные по клиентам у партнера. Попробуйте еще раз.');
						}
					}
				}
	
				$this->db->trans_complete();
								
				$this->login($user->user_login,$user->repassword, true, true);
			}
			else
			{
				throw new Exception('Регистрация невозможна.',-12);
			}				
		}	
		
		exit(0);
	}
	
	/**
	 * Проверка полей аякс-запросом из формы регистрации
	 *
	 * @param unknown_type $type
	 */
	public function checkRegFields()
	{
		error_reporting(0);
		Check::reset_empties();
		$user					= new stdClass();
		$user->user_login		= Check::latin('login',32,1);
		$user->user_password	= Check::latin('password',32,1);
		$user->repassword		= Check::latin('repassword',32,1);
		$user->user_email		= Check::email(Check::str('email',128,6));
		$user->user_deleted		= 2; // ожидается подтверждение регистрации
		$user->user_group		= 'client';
		$terms_accepted 		= Check::chkbox('terms_accepted');
		
		$c						= new stdClass();
		$c->client_country		= Check::int('country');
		$empties				= Check::get_empties();
		$this->load->model('UserModel', 'User');

		try
		{
			// проверка на пустоту
			if ($empties OR empty($c->client_country)) 
			{
				if (empty($user->user_login))
				{
					throw new Exception('Введите логин.', -1);
				}
				else if ($this->User->select(array('user_login'=> $user->user_login, 'user_deleted'=>'0')))
				{
					throw new Exception('Пользователь с таким логином уже существует.', -17);
				}
				else if (empty($user->user_password))
				{
					throw new Exception('Введите пароль.', -3);
				}					
				else if (empty($user->repassword))
				{
					throw new Exception('Подтвердите пароль.', -4);
				}					
				else if ($user->user_password !== $user->repassword)
				{
					throw new Exception('Пароли не совпадают.', -15);
				}
				else if (empty($user->user_email))
				{
					throw new Exception('Введите E-mail.', -2);
				}
				else if (empty($c->client_country))
				{
					throw new Exception('Выберите страну.', -25);
				}					
			}
				
			// кастомные проверки
			if ($empties && in_array('_latin',$empties))
			{
				throw new Exception('Данные должны быть введены латиницей.', -14);
			}
			
			if ($this->User->select(array('user_login'=> $user->user_login, 'user_deleted'=>'0')))
			{
				throw new Exception('Пользователь с таким логином уже существует.', -17);
			}
			
			if ($user->user_password !== $user->repassword)
			{
				throw new Exception('Пароли не совпадают.', -15);
			}
			
			if ($user->user_email!="" && $this->User->select(array('user_email'=> $user->user_email)))
			{
				throw new Exception('Пользователь с такой электронной почтой уже существует.', -16);
			}
			
			$captcha = $this->input->post('captchacode');
			if (empty($captcha))
			{
				throw new Exception('Введите текст на картинке.', -5);
			}
			
			$this->load->library('alcaptcha');
			if ($this->input->post('captchacode')!="" && !$this->alcaptcha->check($this->input->post('captchacode'))) 
			{
				throw new Exception('Текст не совпадает с картинкой.', -18);
			}
			
			if (!$terms_accepted)
			{
				throw new Exception('Вы не приняли условия предоставления услуг.', -33);
			}
		}
		catch (Exception $e)
		{			
			ob_end_clean();
			echo '{"code":'.$e->getCode().',"text":"'.$e->getMessage().'"}';
			exit(0);
		}
		ob_end_clean();
		echo '{"code":1,"text":""}';
		exit(0);
		
	}
	
	public function showRegistration()
	{	
		$this->load->model('CountryModel', 'Country');
		
		// при регистрации выводятся только те страны, в которые указана цена доставки
		$all_countries  = $this->Country->getToCountries();
		Stack::push('all_countries', $all_countries);
		
		View::showChild($this->viewpath.'pages/registration', array('Countries' => $all_countries));
	}
	
	/**
	 * Регистрация пользователя
	 *
	 * @param unknown_type $type
	 */
	public function registration()
	{
		$countries = '';
	
		if (Stack::size('all_countries')>0)
		{
			$countries	= Stack::last('all_countries');
		}
		else
		{
			$this->load->model('CountryModel', 'Country');
			$countries	= $this->Country->getList();			
		}		
		
		Check::reset_empties();
		$user					= new stdClass();
		$user->user_login		= Check::latin('login',32,1);
		$user->user_password	= Check::latin('password',32,1);
		$user->repassword		= Check::latin('repassword',32,1);
		$user->user_email		= Check::email(Check::str('email',128,6));
		$user->user_deleted		= 2; // ожидается подтверждение регистрации
		$user->user_group		= 'client';
		
		$c						= new stdClass();
		$c->client_country		= Check::int('country');
		$c->client_phone_country= "";
		$c->client_phone_city	= "";
		$c->client_phone_value	= "";
		
		$terms_accepted 		= Check::chkbox('terms_accepted');
		$empties				= Check::get_empties();
		
		/**
		 * код ошибки регистрации
		 * <0	- ошибка регистрации
		 * 0	- регистрация не происходила
		 * >0	- регистрация успешна
		*//*
		$result		= new stdClass();
		$result->e	= 0;
		$result->m	= '';	// сообщение
		$result->d	= '';	// возвращаемые данные
		*/
		
		try
		{
			// проверка на пустоту
			if ($empties OR empty($c->client_country)) 
			{
				if (empty($user->user_login))
				{
					throw new Exception('Введите логин.', -1);
				}
				else if ($this->User->select(array('user_login'=> $user->user_login, 'user_deleted'=>'0')))
				{
					throw new Exception('Пользователь с таким логином уже существует.', -17);
				}
				else if (empty($user->user_password))
				{
					throw new Exception('Введите пароль.', -3);
				}					
				else if (empty($user->repassword))
				{
					throw new Exception('Подтвердите пароль.', -4);
				}					
				else if ($user->user_password !== $user->repassword)
				{
					throw new Exception('Пароли не совпадают.', -15);
				}
				else if (empty($user->user_email))
				{
					throw new Exception('Введите E-mail.', -2);
				}
				else if (empty($c->client_country))
				{
					throw new Exception('Выберите страну.', -25);
				}					
			}
				
			// кастомные проверки
			if ($empties && in_array('_latin',$empties))
			{
				throw new Exception('Данные должны быть введены латиницей.', -14);
			}
			
			if ($this->User->select(array('user_login'=> $user->user_login, 'user_deleted'=>'0')))
			{
				throw new Exception('Пользователь с таким логином уже существует.', -17);
			}
			
			if ($user->user_password !== $user->repassword)
			{
				throw new Exception('Пароли не совпадают.', -15);
			}
			
			if ($user->user_email!="" && $this->User->select(array('user_email'=> $user->user_email)))
			{
				throw new Exception('Пользователь с такой электронной почтой уже существует.', -16);
			}
			
			$captcha = $this->input->post('captchacode');
			if (empty($captcha))
			{
				throw new Exception('Введите текст на картинке.', -5);
			}
			
			$this->load->library('alcaptcha');
			if ($this->input->post('captchacode')!="" && !$this->alcaptcha->check($this->input->post('captchacode'))) 
			{
				throw new Exception('Текст не совпадает с картинкой.', -18);
			}
			
			if (!$terms_accepted)
			{
				throw new Exception('Вы не приняли условия предоставления услуг.', -33);
			}
			
			$this->load->model('ClientModel', 'Client');
			$user->user_password = md5($user->user_password);
			
			/**
			 * transactions
			 */
			$this->db->trans_start();
			// something same to lazzy load
			$u = $this->User->addUser($user);
			
			if ($u && $this->Client->addClientData($u->user_id, $c))
			{
				Stack::push('user_confirm', $u);
				Stack::push('repassword', $user->repassword);
				
				$this->db->trans_complete();
				

				Func::redirect("http://{$_SERVER['HTTP_HOST']}/user/confirmRegistration/".md5(session_id()));
				
				return true;
			}
			else
			{
				throw new Exception('Регистрация невозможна.',-12);
			}				
		}
		catch (Exception $e)
		{
			
			$this->result->e	= $e->getCode();			
			$this->result->m	= $e->getMessage();
			
			switch ($this->result->e){
				case -1:	$user->user_login		= '';	break;	
				case -15:	
					$user->user_password			= '';
					$user->repassword				= '';
				break;
				case -2:
				case -13:	$user->user_email		= '';	break;
				case -11:
				case -12:
					 break;
			}
		
			$this->result->terms_accepted = $terms_accepted;
			$this->result->d	= $user;
		}
		
		if ($this->db->trans_status())	{
			$this->db->trans_complete();
		}
		
		if ($user->repassword){
			$user->user_password = $user->repassword;
		}
		
		$view = array(
			'client'		=> $c,
			'Countries'		=> $countries,
			'empties'		=> $empties,
		);
		
		if (isset($u) && $u)	$view['user'] = $u;
		
		View::showChild($this->viewpath.'pages/registration', $view);
	}
	
	
	/**
	 * Confirmation of registration
	 *
	 * @param string	$code
	 */
	public function confirmRegistration($code = null){
	
		/**
		 * load country list form stack, if it exists;
		 * so, we dont touch models every time
		 */
		$countries = '';
		if (Stack::size('all_countries')>0){
			$countries	= Stack::last('all_countries');
		}else{
			$this->load->model('CountryModel', 'Country');
			$countries	= $this->Country->getList();			
		}	
		
		try{
			/**
			 * open transaction
			 */
			$this->db->trans_begin();
			
			if ($code != md5(session_id()))
				throw new Exception('Не возможно произвести регистрацию. Не верный код подтверждения или прошло слишком много времени.', -2);

			$this->load->model('UserModel', 'User');
			
			$user	= Stack::last('user_confirm');
			
			if (!$user)
				throw new Exception('Операция невозможна. Необходимо пройти процедуру регистрации!', -3);
				
			$user->user_deleted	= 0;
			$this->User->_load($user);
			
			if (!$this->User->save(true))
				throw new Exception("DB_ERROR: Не возможно изменить запись!", -1);

			// добавляем партнеров этому клиенту
			$this->load->model('ManagerModel', 'Manager');
			$this->load->model('C2mModel', 'C2m');

/*			$managers = $this->Manager->getIncompleteManagers();
		
			// отсутсвующие страны партнеров
			$addon_countries = array();
			foreach ($countries as $country) {
				if (!array_key_exists($country->country_id, $managers)) {
					$addon_countries[] = $country->country_id;
				}
			}
			$addon_managers = array();
			if (count($addon_countries)) 
			{
				$addon_managers = $this->Manager->getCompleteManagers($addon_countries);
			}
			$managers = array_merge($managers, $addon_managers);
				*/

			$managers = $this->Manager->getManagers();

		// добавляем связку и выставляем дату добавления клиента
			if ($managers['all']) 
			{
				foreach ($managers['all'] as $manager) 
				{
					$relation = new stdClass();
					$relation->client_id = $user->user_id;
					$relation->manager_id = $manager->manager_user;
					
					//$managers = $this->C2m->addRelation($relation);
					$this->C2m->addRelation($relation);
					
					$manager->last_client_added = date('Y-m-d H:i:s', time());
					$manager = $this->Manager->updateManager($manager); 
					
					if (!$manager)
					{
						throw new Exception('Невозможно добавить нового клиента к партнеру. Попробуйте еще раз.');
					}
				}
			}

			// увеличиваем счетчик клиентов у заполненных партнеров
			if (isset($managers['addons']))
			{
				foreach ($managers['addons'] as $manager) 
				{
					$manager->manager_max_clients += 1;
					$manager = $this->Manager->updateManager($manager);
					
					if (!$manager)
					{
						throw new Exception('Невозможно обновить данные по клиентам у партнера. Попробуйте еще раз.');
					}
				}
			}

			$this->db->trans_complete();
			
			$this->login($user->user_login, Stack::last('repassword'));
			Stack::clear('repassword');
			Stack::clear('user_confirm');
			Stack::push('just_registered', 1);
			#Func::redirect(BASEURL.'/client/');
			return true;			
			
			// никогда так не делайте, это удобно но не правильно!!!
			#throw new Exception('Вы успешно зарегистрированы', 2);
				
		}catch (Exception $e){
			
			$this->db->trans_rollback();
			
			$this->result->e	= $e->getCode();
			$this->result->m	= $e->getMessage();
		}

		View::showChild($this->viewpath.'pages/confirmation');
	}
	
	
	public function showCaptchaImage(){
        $this->load->library('alcaptcha');
		echo $this->alcaptcha->image();
	}
	
	
	public function showPasswordRecovery()
	{
		View::showChild($this->viewpath.'pages/recovery');
	}	
	
	public function passwordRecovery()
	{
		$email		= Check::email(Check::str('email', 128,4));
		
		$result		= new stdClass();
		$result->e	= 0;
		$result->m	= '';	// сообщение
		$result->d	= '';	// возвращаемые данные		
		
		if ($email){
			$this->load->model('UserModel', 'User');
			$user = $this->User->getUserByEmail($email);
			
			if ($user){
				
				$new_passwd = Func::randStr(6,8);
				$this->User->_load($user);
				$this->User->_set('user_password', md5($new_passwd));
				
				$headers = 'From: info@countrypost.ru' . "\r\n" .
					'Reply-To: info@countrypost.ru' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();
				
				if (mail($user->user_email,"Восстановление пароля", "Ваш новый пароль: $new_passwd", $headers) && $this->User->save()){
					$result->m	=	'Новый пароль установлен и выслан на указанный Вами адрес электронной почты.';
					$result->e	=	1;
				}else {
					$result->m	=	'Не возможно восстановить пароль. Вероятно, указанный Вами почтовый ящик не существует или не работает';
					$result->e	=	-1;
				}
			}else{
				$result->e	= -2;
				$result->m	= 'Такой e-mail в системе не зарегистрирован';				
			}
		}else{
			$result->e	= -3;
			$result->m	= 'Вы ввели не правильный адрес электронной почты';
		}
		
		View::showChild($this->viewpath.'pages/recovery', array('result'=>$result));
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