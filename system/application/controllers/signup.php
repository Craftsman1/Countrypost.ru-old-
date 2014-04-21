<?
require_once CONTROLERS_PATH.'user'.EXT;

class Signup extends User {
	const FATAL_ERROR = -12;
	const LOGIN_ERROR = -1;
	const PASSWORD_ERROR = -3;
	const EMAIL_ERROR = -2;
	const COUNTRY_ERROR = -25;
	const CAPTCHA_ERROR = -5;
	const LATIN_ERROR = -14;
	const TERMS_ERROR = -33;
	const FIO_ERROR = -100;
	const CITY_ERROR = -300;

	const SHORT_PASSWORD_ERROR = -200;
	const DUPLICATE_LOGIN_ERROR = -17;
	const DUPLICATE_EMAIL_ERROR = -16;
	const CAPTCHA_MISMATCH_ERROR = -18;

	function Signup()
	{
		parent::__construct();

		$this->paging_base_url = '/clients/index';
		View::$main_view	= '/main/index';

		Breadcrumb::setCrumb(array($this->config->item('base_url') => 'Главная'), 0);
		Breadcrumb::setCrumb(array($this->config->item('base_url') . "signup" => 'Регистрация'), 1, TRUE);
	}

	public function index()
	{
		try
		{
			// регистрируем только если незалогинен
			if (isset($this->user->user_id) AND
				$this->user->user_id)
			{
				Func::redirect($this->config->item('base_url'));
			}

			// находим куда редиректить после логина
			$this->prepareRedirect();

			View::showChild('/main/pages/signup');
		}
		catch (Exception $e)
		{
			Func::redirect($this->config->item('base_url'));
		}
	}

	private function processValidateClient($user, $client)
	{
		$this->load->model('UserModel', 'User');
		$this->load->model('ClientModel', 'Clients');
		//$this->load->library('alcaptcha');

		Check::reset_empties();

		$user->user_login		= Check::login('login', 32, 1);
		$user->user_password	= Check::password('password', 32, 1);
		$user->user_email		= Check::email(Check::str('email', 128, 6));

		$terms_accepted 		= Check::chkbox('terms_accepted');
		//$captcha = $this->input->post('captchacode');

		$client->client_country	= Check::int('country');
		$client->client_name	= Check::str('fio', 255, 1, '');
		$user->user_group		= 'client';

		// проверка на пустоту
		if (empty($user->user_login))
		{
			throw new Exception('Для поля "Логин" допускается ввод чисел от 0 до 10, а также букв латинского алфавита и символа "_" .', Signup::LOGIN_ERROR);
		}
		else if ($this->User->select(array(
			'user_login'=> $user->user_login)))
		{
			throw new Exception('Пользователь с таким логином уже существует.', Signup::DUPLICATE_LOGIN_ERROR);
		}
		else if (empty($user->user_password))
		{
			throw new Exception('Для поля "Пароль" допускается ввод чисел от 0 до 10, а также букв латинского алфавита и символов "-!./\$,?:&*;@%()+=№#_[].', Signup::PASSWORD_ERROR);
		}
		else if (strlen($user->user_password) < 6)
		{
			throw new Exception('Пароль слишком короткий (от 6 символов).', Signup::SHORT_PASSWORD_ERROR);
		}
		else if (empty($user->user_email))
		{
			throw new Exception('Введите E-mail.', Signup::EMAIL_ERROR);
		}
		else if (empty($client->client_name))
		{
			throw new Exception('Введите ваше имя.', Signup::FIO_ERROR);
		}
		else if (empty($client->client_country))
		{
			throw new Exception('Выберите страну.', Signup::COUNTRY_ERROR);
		}
		else if ( ! empty($user->user_email) AND
			$this->User->select(array(
				'user_email' => $user->user_email,
				'user_deleted' => 0
			)))
		{
			throw new Exception('Пользователь с такой электронной почтой уже существует.', Signup::DUPLICATE_EMAIL_ERROR);
		}
		/*else if (empty($captcha))
		{
			throw new Exception('Введите текст на картинке.', Signup::CAPTCHA_ERROR);
		}
		else if ($this->input->post('captchacode') != "" AND
			! $this->alcaptcha->check($this->input->post('captchacode')))
		{
			throw new Exception('Текст не совпадает с картинкой.', Signup::CAPTCHA_MISMATCH_ERROR);
		}*/
		else if ( ! $terms_accepted)
		{
			throw new Exception('Вы не приняли условия предоставления услуг.', Signup::TERMS_ERROR);
		}
	}

	private function processValidateDealer($user, $manager)
	{
		$this->load->model('UserModel', 'User');
		$this->load->model('ManagerModel', 'Managers');
		//$this->load->library('alcaptcha');

		Check::reset_empties();

		$user->user_login		= Check::login('login', 32, 1);
		$user->user_password	= Check::password('password', 32, 1);
		$user->user_email		= Check::email(Check::str('email', 128, 6));

		$terms_accepted 		= Check::chkbox('terms_accepted');
		//$captcha = $this->input->post('captchacode');

		$manager->manager_country	= Check::int('country');
		$manager->manager_name	= Check::str('fio', 255, 1, '');
		$manager->city = Check::str('city', 255, 1, '');
		$manager->manager_status = 1;
		$manager->manager_address_name = $manager->manager_name;
		$user->user_group		= 'manager';

		// проверка на пустоту
		if (empty($user->user_login))
		{
			throw new Exception('Для поля "Логин" допускается ввод чисел от 0 до 10, а также букв латинского алфавита и символа "_".', Signup::LOGIN_ERROR);
		}
		else if ($this->User->select(array(
			'user_login'=> $user->user_login)))
		{
			throw new Exception('Пользователь с таким логином уже существует.', Signup::DUPLICATE_LOGIN_ERROR);
		}
		else if (empty($user->user_password))
		{
			throw new Exception('Для поля "Пароль" допускается ввод чисел от 0 до 10, а также букв латинского алфавита и символов "-!./\$,?:&*;@%()+=№#_[].', Signup::PASSWORD_ERROR);
		}
		else if (strlen($user->user_password) < 6)
		{
			throw new Exception('Пароль слишком короткий (от 6 символов).', Signup::SHORT_PASSWORD_ERROR);
		}
		else if (empty($user->user_email))
		{
			throw new Exception('Введите E-mail.', Signup::EMAIL_ERROR);
		}
		else if (empty($manager->manager_name))
		{
			throw new Exception('Введите ваше имя.', Signup::FIO_ERROR);
		}
		else if (empty($manager->manager_country))
		{
			throw new Exception('Выберите страну.', Signup::COUNTRY_ERROR);
		}
		else if (empty($manager->city))
		{
			throw new Exception('Добавьте город.', Signup::CITY_ERROR);
		}
		else if ( ! empty($user->user_email) AND
			$this->User->select(array(
				'user_email' => $user->user_email,
				'user_deleted' => 0
			)))
		{
			throw new Exception('Пользователь с такой электронной почтой уже существует.', Signup::DUPLICATE_EMAIL_ERROR);
		}
		/*else if (empty($captcha))
		{
			throw new Exception('Введите текст на картинке.', Signup::CAPTCHA_ERROR);
		}
		else if ($this->input->post('captchacode') != "" AND
			! $this->alcaptcha->check($this->input->post('captchacode')))
		{
			throw new Exception('Текст не совпадает с картинкой.', Signup::CAPTCHA_MISMATCH_ERROR);
		}*/
		else if ( ! $terms_accepted)
		{
			throw new Exception('Вы не приняли условия предоставления услуг.', Signup::TERMS_ERROR);
		}
	}

	public function validateClientAjax()
	{
		$this->validateAjax('client');
	}

	public function validateDealerAjax()
	{
		$this->validateAjax('manager');
	}

	private function validateAjax($user_group)
	{
		try
		{
			$user = new stdClass();

			if ($user_group == 'client')
			{
				$client	= new stdClass();
				$this->processValidateClient($user, $client);
			}
			else if ($user_group == 'manager')
			{
				$manager = new stdClass();
				$this->processValidateDealer($user, $manager);
			}
		}
		catch (Exception $e)
		{			
			echo '{"code":' . $e->getCode().',"text":"' . addslashes($e->getMessage()) . '"}';
			exit(0);
		}

		echo '{"code":1,"text":""}';
		exit(0);
	}
	
	public function client()
	{
		try
		{
			$this->load->model('CountryModel', 'Country');
			$view['Countries'] = $this->Country->getList();

			Breadcrumb::setCrumb(array($this->config->item('base_url') . "signup/client" => 'Клиент'), 2, TRUE);
			View::showChild('/client/pages/signup', $view);
		}
		catch (Exception $e)
		{
			$this->result->e = $e->getCode();
			$this->result->m = $e->getMessage();

			$this->result->d = FALSE;
			Func::redirect($this->config->item('base_url'));
		}
	}
	
	public function dealer()
	{
		try
		{
			$this->load->model('CountryModel', 'Country');
			$view['Countries'] = $this->Country->getList();

			Breadcrumb::setCrumb(array($this->config->item('base_url') . "signup/dealer" => 'Посредник'), 2, TRUE);
			View::showChild('/manager/pages/signup', $view);
		}
		catch (Exception $e)
		{
			$this->result->e = $e->getCode();
			$this->result->m = $e->getMessage();

			$this->result->d = FALSE;
			Func::redirect($this->config->item('base_url'));
		}
	}

	public function signupClient()
	{
		try
		{
			$user = new stdClass();
			$client	= new stdClass();

			$this->processValidateClient($user, $client);

			// Хешируем пароль (ДОБАВИТЬ СОЛЬ!!!)
			$user->user_password = md5($user->user_password);

			// добавляем клиента и пользователя
			$u = $this->User->addUser($user);

			if ($u AND $this->Clients->addClientData($u->user_id, $client))
			{
				if ($this->loginInternal())
				{
					Stack::push('just_registered', 1);
					$this->processRedirect();
				}
				else
				{
					throw new Exception('Регистрация невозможна.', Signup::FATAL_ERROR);
				}
			}
			else
			{
				throw new Exception('Регистрация невозможна.', Signup::FATAL_ERROR);
			}
		}
		catch (Exception $e)
		{
			$this->result->e = $e->getCode();
			$this->result->m = $e->getMessage();


			$this->result->d = $user;
		}

		$this->load->model('CountryModel', 'Country');

		$view = array(
			'client' => $client,
			'Countries' => $this->Country->getList()
		);

		if (isset($u) AND $u)
		{
			$view['user'] = $u;
		}

		$this->result->terms_accepted = (self::TERMS_ERROR != $this->result->e);
		Breadcrumb::setCrumb(array($this->config->item('base_url') . "signup/client" => 'Клиент'), 2, TRUE);
		View::showChild('/client/pages/signup', $view);
	}

	public function signupDealer()
	{
		try
		{
			$user = new stdClass();
			$manager = new stdClass();

			$this->processValidateDealer($user, $manager);

			// Хешируем пароль (ДОБАВИТЬ СОЛЬ!!!)
			$user->user_password = md5($user->user_password);

			// добавляем клиента и пользователя
			$u = $this->User->addUser($user);

			if ($u AND $this->Managers->addManagerData($u->user_id, $manager))
			{
				if ($this->loginInternal())
				{
					Stack::push('just_registered', 1);
					$this->processRedirect();
				}
				else
				{
					throw new Exception('Регистрация невозможна.', Signup::FATAL_ERROR);
				}
			}
			else
			{
				throw new Exception('Регистрация невозможна.', Signup::FATAL_ERROR);
			}
		}
		catch (Exception $e)
		{
			$this->result->e = $e->getCode();
			$this->result->m = $e->getMessage();

			$this->result->d = $user;
		}

		$this->load->model('CountryModel', 'Country');

		$view = array(
			'manager' => $manager,
			'Countries' => $this->Country->getList()
		);

		if (isset($u) AND $u)
		{
			$view['user'] = $u;
		}

		$this->result->terms_accepted = (self::TERMS_ERROR != $this->result->e);
		Breadcrumb::setCrumb(array($this->config->item('base_url') . "signup/dealer" => 'Посредник'), 2, TRUE);
		View::showChild('/manager/pages/signup', $view);
	}

	public function showCaptchaImage()
	{
        $this->load->library('alcaptcha');
		echo $this->alcaptcha->image();
	}

	private function prepareRedirect()
	{
		// любая страница Countrypost.ru кроме регистрации
		$referer = isset($_SERVER['HTTP_REFERER']) ?
			$_SERVER['HTTP_REFERER'] :
			'';

		if (stripos($referer, $this->config->item('base_url')) !== FALSE AND
			stripos($referer, 'signup') === FALSE)
		{
			$_SESSION['signup_redirect'] = $referer;
		}
		else
		{
			unset($_SESSION['signup_redirect']);
		}
	}

	private function processRedirect()
	{
		if ($_SESSION['signup_redirect'] AND
			isset($this->user->user_group) AND
			$this->user->user_group == 'client')
		{
			$redirect = $_SESSION['signup_redirect'];
		}
		else
		{
			$redirect = $this->config->item('base_url') . 'profile';
		}

		unset($_SESSION['signup_redirect']);
		Func::redirect($redirect);
	}
}