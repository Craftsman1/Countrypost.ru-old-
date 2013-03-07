<?
require_once CONTROLERS_PATH.'User'.EXT;

class Signup extends User {

	function Signup()
	{
		parent::__construct();

		$this->paging_base_url = '/clients/index';
		View::$main_view	= '/main/index';

		Breadcrumb::setCrumb(array(BASEURL => 'Главная'), 0);
		Breadcrumb::setCrumb(array("{BASEURL}signup" => 'Регистрация'), 1, TRUE);
	}
	
	public function index()
	{
		try
		{
			if (isset($this->user->user_id) AND
				$this->user->user_id)
			{
				Func::redirect(BASEURL);
			}

			View::showChild('/main/pages/signup');
		}
		catch (Exception $e)
		{
			Func::redirect(BASEURL);
		}
	}
	
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
	
	/**
	 * Регистрация пользователя
	 */
	public function client()
	{
		try
		{
			$countries = '';
	
			if (Stack::size('all_countries') > 0)
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
			$user->user_email		= Check::email(Check::str('email',128,6));
			$user->user_deleted		= 0; // ожидается подтверждение регистрации
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
			if ($empties && in_array('_latin', $empties))
			{
				throw new Exception('Данные должны быть введены латиницей.', -14);
			}
			
			if ($this->User->select(array('user_login'=> $user->user_login, 'user_deleted'=>'0')))
			{
				throw new Exception('Пользователь с таким логином уже существует.', -17);
			}
			
			if ($user->user_email != "" && $this->User->select(array('user_email'=> $user->user_email)))
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
			
			// something same to lazzy load
			$u = $this->User->addUser($user);
			
			if ($u && $this->Client->addClientData($u->user_id, $c))
			{
				Stack::push('user_confirm', $u);

				$this->loginInternal();
				Func::redirect(BASEURL . '/profile');
			}
			else
			{
				throw new Exception('Регистрация невозможна.', -12);
			}				
		}
		catch (Exception $e)
		{
			
			$this->result->e	= $e->getCode();			
			$this->result->m	= $e->getMessage();
			
			switch ($this->result->e){
				case -1:	$user->user_login		= '';	break;	
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
		
		$view = array(
			'client'		=> $c,
			'Countries'		=> $countries,
			'empties'		=> $empties,
		);
		
		if (isset($u) && $u)	$view['user'] = $u;
		
		View::showChild('/client/pages/signup', $view);
	}
	
	public function showCaptchaImage()
	{
        $this->load->library('alcaptcha');
		echo $this->alcaptcha->image();
	}
}