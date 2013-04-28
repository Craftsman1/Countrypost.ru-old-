<? require_once BASE_CONTROLLERS_PATH . 'BaseController' . EXT;

class Client extends BaseController {
	protected $__partners;
	protected $__client;

	function Client()
	{
		parent::__construct();

		$user = Check::user();

		if (empty($user) OR $user->user_group !== 'client')
		{
			Func::redirect(BASEURL);
		}

		Breadcrumb::setCrumb(array('/' => 'Главная'), 0);
		Breadcrumb::setCrumb(array('/client/orders' => 'Мои заказы'), 1, TRUE);
	}
	
	function index()
	{
		Func::redirect(BASEURL.$this->cname.'/orders');

		$this->load->model('NewsModel', 'News');

		$news = $this->News->getInfo(null,null,array(
				'news_addtime'	=> 'desc'
			),3);
			
		if ( ! empty($news) && ! is_array($news))
		{
			$news = array(0 => $news);
		}
			
		$openp = $this->Packages->getPackages(null, 'open', $this->user->user_id, null);
		$sentp = $this->Packages->getPackages(null, 'sent', $this->user->user_id, null);
		$payedp = $this->Packages->getPackages(null, 'payed', $this->user->user_id, null);
		
		// находим комиссии
		$this->load->model('ConfigModel', 'Config');
		$config = $this->Config->getConfig();
		
		if (empty($config))
		{
			throw new Exception('Комиссии доп. услуг не найдены. Попробуйте еще раз.');
		}
		
		// курс юаня
		$this->load->model('CurrencyModel', 'Currencies');
		$cny_rate = $this->Currencies->getById('CNY');
			
		if (empty($cny_rate))
		{
			throw new Exception('Курсы валют не найдены. Попробуйте еще раз.');
		}
		
		View::showChild($this->viewpath.'/pages/main', array(
			'news'					=> $news,
			'just_registered'		=> Stack::shift('just_registered', true),
			'package_open'			=> ($openp ? count($openp) : 0),
			'package_payed'			=> ($payedp ? count($payedp) : 0),
			'package_sent'			=> ($sentp ? count($sentp) : 0),
			'taobao_register_tax'	=> $config['taobao_register_tax']->config_value,
			'alipay_refill_tax'		=> $config['alipay_refill_tax']->config_value,
			'taobao_payment_tax'	=> $config['taobao_payment_tax']->config_value,
			'cny_rate'				=> $cny_rate->cbr_cross_rate,
		));
	}
	
	public function showShop()
	{	
		foreach ($_POST as $key => $val)
		{
			$$key = $val;
		}
		
		$error		= new stdClass();
		$error->m	= '';
		$shop = array();
		
		try
		{
			if (empty($sname) OR empty($surl))
			{
				throw new Exception('Пожалуйста, введите название магазина!');
			}

			$shop['name'] = $sname;
				
			if ( ! Check::url($surl))
			{
				throw new Exception('Пожалуйста, введите адрес магазина!');
			}
			
			$shop['url'] = substr($surl, 7);
		}
		catch (Exception $e)
		{
			$error->m	= $e->getMessage();				
		}
		
		$view = array(
			'error'		=> $error,
			'shop'		=> $shop			
		);
		
		$this->load->model('OdetailModel', 'OdetailModel');
		$Odetails = $this->OdetailModel->getFilteredDetails(array('odetail_client' => $this->user->user_id, 'odetail_order' => 0));
		
		if (count($Odetails))
		{
			$view['country'] = false;
		}
		else
		{
			$view['country'] = true;
			$this->load->model('CountryModel', 'CountryModel');
			$view['countries'] = $this->CountryModel->getClientAvailableCountries($this->user->user_id);
		}		
		
		View::showChild($this->viewpath.'/pages/show_shop', $view);
	}
	
	public function addProductToPrivilegedOrder($order_id)
	{
		try
		{
			// валидация
			if (empty($order_id) OR
				! is_numeric($order_id))
			{
				throw new Exception('Доступ запрещен.');
			}

			$order = $this->getPrivilegedOrder($order_id, 'Заказ не найден.');

			$this->load->model('OrderModel', 'Orders');
			$this->load->model('OdetailModel', 'OdetailModel');

			// необязательные поля
			Check::reset_empties();
			$detail = new OdetailModel();

			$detail->odetail_shop				    = Check::str('oshop', 255, 0);
			$detail->odetail_volume				    = Check::float('ovolume', 0);
			$detail->odetail_tnved				    = Check::str('otnved', 255, 1);
			$detail->odetail_insurance				= Check::chkbox('insurance');
			$detail->odetail_comment                = Check::str('ocomment', 255, 0);
			$detail->odetail_tracking               = Check::str('otracking', 80, 0);
			$detail->odetail_status                 = 'processing';
			$detail->odetail_img					= Check::str('userfileimg', 500, 1);
			$detail->odetail_product_amount			= Check::int('oamount');
			$detail->odetail_product_color			= Check::str('ocolor', 32, 0);
			$detail->odetail_product_size			= Check::str('osize', 32, 0);
			$detail->odetail_client					= $this->user->user_id;
			$detail->odetail_manager				= $order->order_manager;
			$detail->odetail_country				= Check::str('ocountry', 255, 1);
			$detail->odetail_foto_requested			= Check::chkbox('foto_requested');
			$detail->odetail_search_requested		= Check::chkbox('search_requested');
			$userfile								= (isset($_FILES['userfile']) AND ! $_FILES['userfile']['error']);

			// обязательные поля
			$detail->odetail_order 					= $order_id;
			$detail->odetail_link					= Check::str('olink', 500, 1);
			$detail->odetail_product_name			= Check::str('oname', 255, 1);
			$detail->odetail_price					= Check::float('oprice');
			$detail->odetail_pricedelivery			= Check::float('odeliveryprice');
			$detail->odetail_weight					= Check::float('oweight');

			switch ($order->order_type)
			{
				case 'online' :
					$this->onlineProductCheck($detail);
					break;
				case 'offline' :
					$this->offlineProductCheck($detail);
					break;
				case 'service' :
					$this->serviceProductCheck($detail);
					break;
				case 'delivery' :
					$this->deliveryProductCheck($detail);
					break;
				case 'mail_forwarding' :
					$this->mailforwardProductCheck($detail);
					break;
			}

			// погнали
			$detail = $this->OdetailModel->addOdetail($detail);

			// загружаем файл
			if (isset($userfile) AND $userfile)
			{
				$this->uploadOrderScreenshot($detail, $this->user->user_id);
			}

			// пересчитываем заказ
			if ( ! $this->Orders->recalculate($order))
			{
				throw new Exception('Невожможно пересчитать стоимость заказа. Попоробуйте еще раз.');
			}

			$this->Orders->saveOrder($order);

			// возвращаем json с инфой по заказу и товару
			$result = new stdClass();
			$result->order_id = $order->order_id;
			$result->odetail_id = $detail->odetail_id;
			$result->odetail_img = $detail->odetail_img;

			echo json_encode($result);
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

	// BOF: костыль
	// TODO: этот код дублирует методы из main. поскольку страница временная, оставляем как есть ради экономии времени
	protected function onlineProductCheck ($detail)
	{
		if (empty($detail->odetail_link))
		{
			throw new Exception('Добавьте ссылку на товар.');
		}

		if (empty($detail->odetail_product_name))
		{
			throw new Exception('Добавьте наименование товара.');
		}

		if (empty($detail->odetail_price))
		{
			throw new Exception('Добавьте цену товара.');
		}

		if (empty($detail->odetail_country))
		{
			throw new Exception('Выберите страну.');
		}

		if ( ! $detail->odetail_product_amount)
		{
			$detail->odetail_product_amount = 1;
		}
	}

	protected function offlineProductCheck ($detail)
	{
		if (empty($detail->odetail_product_name))
		{
			throw new Exception('Добавьте наименование товара.');
		}

		if (empty($detail->odetail_price))
		{
			throw new Exception('Добавьте цену товара.');
		}

		if (empty($detail->odetail_country))
		{
			throw new Exception('Выберите страну.');
		}

		if ( ! $detail->odetail_product_amount)
		{
			$detail->odetail_product_amount = 1;
		}
	}

	protected function deliveryProductCheck ($detail)
	{
		if (empty($detail->odetail_product_name))
		{
			throw new Exception('Добавьте наименование товара.');
		}

		if (empty($detail->odetail_weight))
		{
			throw new Exception('Добавьте примерный вес товара.');
		}

		if ( ! $detail->odetail_product_amount)
		{
			$detail->odetail_product_amount = 1;
		}
	}

	protected function serviceProductCheck ($detail)
	{
		if (empty($detail->odetail_comment))
		{
			throw new Exception('Добавьте описание услуги.');
		}
	}

	protected function mailforwardProductCheck ($detail)
	{
		if (empty($detail->odetail_product_name))
		{
			throw new Exception('Добавьте наименование товара.');
		}

		if (empty($detail->odetail_tracking))
		{
			throw new Exception('Добавьте Tracking номер.');
		}
	}
	// EOF: костыль

	public function addproduct($order_id)
	{
		try
		{
			if (empty($order_id) OR
				! is_numeric($order_id))
			{
				throw new Exception('Доступ запрещен.');
			}

			$order = $this->getPrivilegedOrder($order_id, 'Заказ не найден.');

			// крошки
			$this->showOrderBreadcrumb($order);
			Breadcrumb::setCrumb(array("/client/addproduct/$order_id" => 'Добавление товара'), 3, TRUE);

			View::showChild($this->viewpath.'/pages/showAddProduct', array(
				'order' => $order,
				'order_currency' => $this->Orders->getOrderCurrency($order->order_id)
			));
		}
		catch (Exception $ex)
		{
			print_r($ex);
		}
	}

	public function deleteDetail($oid) {
		$this->load->model('OdetailModel', 'OdetailModel');
		
		$_o = $this->OdetailModel->getById((int) $oid);
		if ($_o && 
			$_o->odetail_order == 0 && 
			$_o->odetail_client == $this->user->user_id)
		{
			try 
			{
				// удаляем товар
				if (!$this->OdetailModel->delete((int) $oid)) 
				{
					throw new Exception('Невозможно удалить товар.');
				}
				
				$Odetails = $this->OdetailModel->getFilteredDetails(array(
					'odetail_client' => $this->user->user_id,
					'odetail_order' => 0
				));

				if ($Odetails)
				{
					$this->result->m = 'Товар успешно удален.';
				}
				else
				{
					$this->result->m = 'В корзине больше нет товаров.';
				}
			} 
			catch (Exception $e)
			{
				$this->db->trans_rollback();
				$this->result->m = $e->getMessage();
			}
		}
		else
		{
			$this->result->m = 'Невозможно удалить товар.';
		}
		
		Stack::push('result', $this->result);

		if ($Odetails)
		{
			Func::redirect(BASEURL.$this->cname.'/showBasket');
		}
		else
		{
			Func::redirect(BASEURL.$this->cname.'/showOpenOrders');
		}
	}
	
	public function showScreen($oid=null)
	{
        header('Content-type: image/jpg');
        $this->load->model('OdetailModel', 'OdetailModel');
        if ($Detail = $this->OdetailModel->getInfo(array('odetail_client' => $this->user->user_id, 'odetail_id' => intval($oid))))
		{
            readfile("{$_SERVER['DOCUMENT_ROOT']}/upload/orders/{$this->user->user_id}/$oid.jpg");
        }
        die();

        /*if (!empty($this->user))
        {
            $client_id = $this->user->user_id;
        }
        else
        {
            $client_id = UserModel::getTemporaryKey();
        }
        // TODO : а если картинка не JPG!?
		header('Content-type: image/jpg');
		$this->load->model('OdetailModel', 'OdetailModel');
		if ($Detail = $this->OdetailModel->getInfo(array('odetail_client' => $client_id, 'odetail_id' => intval($oid)))) {
			readfile("{$_SERVER['DOCUMENT_ROOT']}/upload/orders/{$Detail->client_id}/$oid.jpg");
		}
		die();*/
	}
	
	public function showPaymentFoto($oid, $filename)
	{
		$this->load->model('Order2InModel', 'Order2in');

		if ($o2i = $this->Order2in->getInfo(array(
			'order2in_user' => $this->user->user_id,
			'order2in_id' => intval($oid))))
		{
			header('Content-type: image/jpg');
			readfile($_SERVER['DOCUMENT_ROOT'] . "/upload/orders2in/$oid/$filename");
		}

		die();
	}
	
	public function showNewsList($limit = 0, $offset = 0, $news_id = null)
	{
		$this->load->model('NewsModel', 'News');
		
		$news = $this->News->getInfo(
			$news_id,
			null,
			array('news_addtime' => 'desc'),
			(int) $limit, 
			(int) $offset,
			false);
		
		View::showChild($this->viewpath.'/pages/news', 
			array('news' => $news, 
			'pagination' => ''));		
	}
	
	public function addOrder2In($order_id)
	{
		try
		{
			// валидация
			if (empty($order_id) OR
				! is_numeric($order_id))
			{
				throw new Exception('Доступ запрещен.');
			}

			// заказ
			$order = $this->getPrivilegedOrder(
				$order_id,
				'Заказ недоступен.');

			// погнали
			$order2in = new stdClass();
			$order2in->order_id = $order_id;
			$order2in->is_countrypost = 1;
			$order2in->order2in_to = $order->order_manager;
			$order2in->order2in_createtime = date('Y-m-d H:i:s');
			$order2in->order2in_amount = Check::float('total_local');
			$order2in->order2in_amount_local = Check::float('total_usd');
			$order2in->order2in_payment_service = Check::txt('payment_service', 3, 2);
			$order2in->order2in_details = Check::txt('account', 20, 1);
			$order2in->excess_amount = $this->Orders->processExcessAmountTransfer($order);

			// input validation
			if (isset($order2in->order2in_payment_service))
			{
				$service = $order2in->order2in_payment_service;

				$this->load->model('CurrencyModel', 'Currencies');

				switch ($service)
				{
					case 'bm' :
					case 'sv' :
						$order2in->order2in_amount = Check::int('total_usd');
						$order2in->order2in_amount_local = Check::float('total_ru');
						$order2in->order2in_currency = 'RUB';
						break;
					case 'qw' :
					case 'alf' :
					case 'wur' :
					case 'con' :
					case 'unr' :
					case 'gcr' :
					case 'anr' :
					case 'vm' :
						$order2in->order2in_amount = Check::int('total_local');
						$order2in->order2in_amount_local = Check::float('total_ru');
						$order2in->order2in_currency = 'RUB';
						break;
					case 'ald' :
						$order2in->order2in_amount = Check::int('total_local');
						$order2in->order2in_amount_local = Check::float('total_usd');
						$order2in->order2in_payment_service = 'alf';
						$order2in->order2in_currency = 'USD';
						break;
					case 'wud' :
						$order2in->order2in_amount = Check::int('total_local');
						$order2in->order2in_amount_local = Check::float('total_usd');
						$order2in->order2in_payment_service = 'wur';
						$order2in->order2in_currency = 'USD';
						break;
					case 'cod' :
						$order2in->order2in_amount = Check::int('total_local');
						$order2in->order2in_amount_local = Check::float('total_usd');
						$order2in->order2in_payment_service = 'con';
						$order2in->order2in_currency = 'USD';
						break;
					case 'und' :
						$order2in->order2in_amount = Check::int('total_local');
						$order2in->order2in_amount_local = Check::float('total_usd');
						$order2in->order2in_payment_service = 'unr';
						$order2in->order2in_currency = 'USD';
						break;
					case 'gcd' :
						$order2in->order2in_amount = Check::int('total_local');
						$order2in->order2in_amount_local = Check::float('total_usd');
						$order2in->order2in_payment_service = 'gcr';
						$order2in->order2in_currency = 'USD';
						break;
					case 'and' :
						$order2in->order2in_amount = Check::int('total_local');
						$order2in->order2in_amount_local = Check::float('total_usd');
						$order2in->order2in_payment_service = 'anr';
						$order2in->order2in_currency = 'USD';
						break;
					case 'cus' :
						$order2in->order2in_amount = Check::int('total_local');
						$order2in->order2in_amount_local = Check::float('total_usd');
						$order2in->order2in_currency = 'USD';
						break;
					case 'cuu' :
						$order2in->order2in_amount = Check::int('total_local');
						$order2in->order2in_amount_local = Check::float('total_uah');
						$order2in->order2in_currency = 'UAH';
						break;
				}
			}

			if ($order2in->order2in_amount <= 0 OR
				$order2in->order2in_amount_local <= 0)
			{
				throw new Exception('Введите сумму платежа.');
			}
			
			$order2in->order2in_user = $this->user->user_id;
			$order2in->order2in_status = 'processing';
			//$order2in->order2in_tax = $order2in->order2in_amount * constant(strtoupper($service).'_IN_TAX') * 0.01;
			$order2in->order2in_tax = 0;

			$this->load->model('Order2InModel', 'Order2in');
			$order2in = $this->Order2in->addOrder($order2in);

			if ( ! $order2in) 
			{
				throw new Exception('Ошибка создания заявки.');
			}
			
			$this->result->m = 'Заявка успешно добавлена.';
			
			// грузим скриншот
			if ($service == 'bm' OR
				$service == 'bta' OR
				$service == 'ccr' OR
				$service == 'kkb' OR
				$service == 'nb' OR
				$service == 'tb' OR
				$service == 'atf' OR
				$service == 'ab' OR
				$service == 'pb' OR
				$service == 'sv' OR
				$service == 'vtb' OR
				$service == 'alf' OR
				$service == 'ald' OR
				$service == 'wur' OR
				$service == 'wud' OR
				$service == 'con' OR
				$service == 'cod' OR
				$service == 'unr' OR
				$service == 'und' OR
				$service == 'gcr' OR
				$service == 'gcd' OR
				$service == 'anr' OR
				$service == 'and' OR
				$service == 'vm' OR
				$service == 'cus')
			{
				$userfile	= isset($_FILES['userfile']) && ! $_FILES['userfile']['error'];
				$o2i_id		= $order2in->order2in_id;

				if ($userfile)
				{
					// загружаем файл
					$old = umask(0);

					if ( ! is_dir($_SERVER['DOCUMENT_ROOT']."/upload/orders2in/$o2i_id"))
					{
						mkdir($_SERVER['DOCUMENT_ROOT']."/upload/orders2in/$o2i_id", 0777);
					}

					$config['upload_path']			= $_SERVER['DOCUMENT_ROOT'].'/upload/orders2in/'.$o2i_id;
					$config['allowed_types']		= 'bmp|gif|jpg|jpeg|png|GIF|JPG|PNG|JPEG|BMP';
					$config['max_size']				= '4096';
					$config['encrypt_name'] 		= false;
					$this->load->library('upload', $config);

					if ( ! $this->upload->do_upload()) 
					{
						throw new Exception(strip_tags(trim($this->upload->display_errors())));
					}
				}
			}
			
			// уведомления
			$this->load->model('UserModel', 'Users');
			
			Mailer::sendAdminNotification(
				Mailer::SUBJECT_NEW_ORDER2IN, 
				Mailer::NEW_ORDER2IN_CLIENT_NOTIFICATION, 
				0,
				$order2in->order2in_id, 
				$order2in->order2in_user,
				"http://countrypost.ru/syspay/showOpenOrders2In",
				null,
				$this->Users);
		}
		catch (Exception $e)
		{

		}
		
		Stack::push('result', $this->result);
		Func::redirect(BASEURL . "client/order/$order_id");
	}
	
	public function getScreenshot($fname)
	{
		header('Content-Type: image/jpeg');
		echo file_get_contents($_SERVER['DOCUMENT_ROOT']."/upload/orders/{$this->user->user_id}/tmp/$fname.jpg");
	}
	
	public function getScreenshotHtml($fname)
	{
		echo "<img src='/client/getScreenshot/$fname' />";
	}
	
	public function orders()
	{
		$this->showOpenOrders();
	}

	public function showOpenOrders()
	{
		$this->showClientOrders('open');
	}

	public function showSentOrders()
	{
		$this->showClientOrders('sent');
	}

	public function showPayedOrders()
	{
		$this->showClientOrders('payed');
	}

	private function showClientOrders($status)
	{
		$this->load->model('OdetailModel', 'OdetailModel');
		$basket = $this->OdetailModel->getNewDetails($this->user->user_id);

		if ( ! empty($basket))
		{
			Func::redirect(BASEURL.$this->cname.'/showBasket');
			return;
		}

		$this->showOrders($status);
	}

	function payOrder($order_id)
	{
		if (empty($order_id) OR
			! is_numeric($order_id))
		{
			throw new Exception('Доступ запрещен.');
		}

		// крошки
		Breadcrumb::setCrumb(array('/client/orders' => 'Мои заказы'), 1, TRUE);
		Breadcrumb::setCrumb(array("/client/order/$order_id" => "Заказ №$order_id"	), 2, TRUE);
		Breadcrumb::setCrumb(array("/client/payOrder/$order_id" => "Оплата"), 3, TRUE);

		// заказ
		$order = $this->getPrivilegedOrder(
			$order_id,
			'Заказ недоступен.');

		$order->excess_amount = $this->Orders->getExcessOrdersAmount($order->order_client, $order->order_manager);

		// заявки на пополнение
		$this->load->model('CurrencyModel', 'Currencies');
		$this->load->model('PaymentServiceModel', 'Services');

		$view['order'] = $order;
		$this->Orders->prepareOrderView($view);

		$view += array (
			'services'	=> $this->Services->getInServices(),
			'rate_usd' => $this->Currencies->getExchangeRate($order->order_currency, 'USD', 'client'),
			'rate_kzt' => $this->Currencies->getExchangeRate($order->order_currency, 'KZT', 'client'),
			'rate_uah' => $this->Currencies->getExchangeRate($order->order_currency, 'UAH', 'client'),
			'rate_rur' => $this->Currencies->getExchangeRate($order->order_currency, 'RUB', 'client')
		);

		$this->load->model('ManagerModel', 'Managers');

		if ( ! ($manager = $this->Managers->getById($order->order_manager)))
		{
			throw new Exception('Посредник не найден.');
		}
		else
		{
			$view['is_countrypost_payments_allowed'] = $manager->is_internal_payments;
		}

		// парсим шаблон
		if ($this->uri->segment(4) == 'ajax')
		{
			$view['selfurl'] = BASEURL.$this->cname.'/';
			$view['viewpath'] = $this->viewpath;
			$this->load->view('/client/ajax/showOpenOrders2In', $view);
		}
		else
		{
			$view += array (
				'rate_usd_rur' => $this->Currencies->getExchangeRate('USD', 'RUB', 'client'),
				'rate_eur_rur' => $this->Currencies->getExchangeRate('EUR', 'RUB', 'client'),
				'rate_usd_kzt' => $this->Currencies->getExchangeRate('USD', 'KZT', 'client'),
				'rate_usd_uah' => $this->Currencies->getExchangeRate('USD', 'UAH', 'client'),
			);

			View::showChild('/client/pages/payOrder', $view);
		}
	}

	public function payOrderDirect($order_id)
	{
		try
		{
			// валидация
			if (empty($order_id) OR
				! is_numeric($order_id))
			{
				throw new Exception('Доступ запрешен.');
			}

			// заказ
			$order = $this->getPrivilegedOrder(
				$order_id,
				'Заказ недоступен.');

			// погнали
			$order2in = new stdClass();
			$order2in->order_id = $order_id;
			$order2in->is_countrypost = 0;
			$order2in->order2in_to = $order->order_manager;
			$order2in->order2in_createtime = date('Y-m-d H:i:s');
			$order2in->order2in_amount = Check::float('amount');
			$order2in->payment_service_name = Check::txt('service', 255, 1);
			$order2in->order2in_details = Check::txt('comment', 20, 1);
			$order2in->order2in_user = $this->user->user_id;
			$order2in->order2in_status = 'processing';
			$order2in->usd_amount = 'processing';
			$order2in->excess_amount = $this->Orders->processExcessAmountTransfer($order);

			// валюта
			$order2in->order2in_currency = $this->Orders->getOrderCurrency($order->order_id);

			$this->load->model('Order2InModel', 'Order2in');
			$order2in = $this->Order2in->addOrder($order2in);

			if ( ! $order2in)
			{
				throw new Exception('Ошибка создания заявки.');
			}

			$this->result->m = 'Заявка успешно добавлена.';

			// уведомления
			/*
			$this->load->model('UserModel', 'Users');

			Mailer::sendAdminNotification(
				Mailer::SUBJECT_NEW_ORDER2IN,
				Mailer::NEW_ORDER2IN_CLIENT_NOTIFICATION,
				0,
				$order2in->order2in_id,
				$order2in->order2in_user,
				"http://countrypost.ru/syspay/showOpenOrders2In",
				null,
				$this->Users);*/
		}
		catch (Exception $e)
		{

		}

		Func::redirect(BASEURL . "client/order/$order_id");
	}
	
	public function showAddresses($partner_id = null)
	{
		$view	= array(
						'client'	=> $this->__client,
						'partners'	=> $this->__partners,
		);

		if (isset($this->__partners[$partner_id])){

			$view['partner_id']	= $partner_id;
		}
		
		View::showChild($this->viewpath.'/pages/showAddresses', $view);
	}
	
	public function showAddImage()
	{
		View::showChild($this->viewpath.'/pages/showAddImage');
	}
	
	public function taobaoRegister()
	{
		try 
		{
			// находим клиента
			$this->load->model('UserModel', 'Users');
			$user = $this->Users->getById($this->user->user_id);
			
			if (empty($user))
			{
				throw new Exception('Доступ запрещен.');
			}
			
			// находим комиссии
			$this->load->model('ConfigModel', 'Config');
			$config = $this->Config->getConfig();
			
			if (empty($config) OR
				empty($config['taobao_register_tax']))
			{
				throw new Exception('Невозможно добавить запрос на регистрацию. Попробуйте еще раз.');
			}
			
			// валидация
			Check::reset_empties();
			
			$taobao_login = Check::str('taobao_login', 20, 1);
			$taobao_password = Check::str('taobao_password', 20, 1);

			$empties = Check::get_empties();
			
			if (empty($taobao_login))
			{
				throw new Exception('Введите логин для регистрации аккаунта.');
			}

			if (empty($taobao_password))
			{
				throw new Exception('Введите пароль для регистрации аккаунта.');
			}
			
			if ($user->user_coints < $config['taobao_register_tax']->config_value)
			{
				throw new Exception("Недостаточно денег на счету. <a target='_blank' href='/client/showAddBalance'>Пополнить.</a>");
			}

			// шлем мыло
			Mailer::sendTaobaoRegisterNotification(
				Mailer::SUBJECT_TAOBAO_REGISTRATION,
				Mailer::TAOBAO_REGISTRATION_NOTIFICATION, 
				$user->user_id,
				$user->user_email, 
				$taobao_login,
				$taobao_password);
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}
	
	public function alipayRefill()
	{
		try 
		{
			// находим клиента
			$this->load->model('UserModel', 'Users');
			$user = $this->Users->getById($this->user->user_id);
			
			if (empty($user))
			{
				throw new Exception('Доступ запрещен.');
			}
			
			// находим комиссии
			$this->load->model('ConfigModel', 'Config');
			$config = $this->Config->getConfig();
			
			if (empty($config) OR
				empty($config['alipay_refill_tax']))
			{
				throw new Exception('Невозможно добавить запрос на пополнение счета Alipay. Попробуйте еще раз.');
			}
			
			$this->load->model('CurrencyModel', 'Currencies');
			$currency = $this->Currencies->getById('CNY');
				
			if ( ! $currency)
			{
				throw new Exception('Невозможно конвертировать сумму в доллары. Попробуйте еще раз.');
			}
			
			// валидация
			Check::reset_empties();
			
			$alipay_login = Check::str('alipay_login', 20, 1);
			$alipay_password = Check::str('alipay_password', 20, 1);
			$alipay_amount = Check::int('alipay_amount');

			$empties = Check::get_empties();
			
			if (empty($alipay_login))
			{
				throw new Exception('Введите логин от Alipay.');
			}

			if (empty($alipay_password))
			{
				throw new Exception('Введите пароль от Alipay.');
			}
			
			if (empty($alipay_amount))
			{
				throw new Exception('Введите сумму пополнения.');
			}
			
			$payment_amount = ceil(parent::convert($currency, (float)$alipay_amount));
			
			if ($user->user_coints < $payment_amount)
			{
				throw new Exception("Недостаточно денег на счету. <a target='_blank' href='/client/showAddBalance'>Пополнить.</a>");
			}
			
			$config_tax = $config['alipay_refill_tax']->config_value;
			$alipay_tax = ceil($alipay_amount * $config_tax * 0.01);
			$alipay_total = $alipay_amount - $alipay_tax;
			
			// шлем мыло
			Mailer::sendAlipayRefillNotification(
				Mailer::SUBJECT_ALIPAY_REFILL,
				Mailer::ALIPAY_REFILL_NOTIFICATION, 
				$user->user_id,
				$user->user_email, 
				$alipay_login,
				$alipay_password,
				$alipay_amount,
				$alipay_total,
				$payment_amount);
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

	public function taobaoPayment()
	{
		try 
		{
			// находим клиента
			$this->load->model('UserModel', 'Users');
			$user = $this->Users->getById($this->user->user_id);
			
			if (empty($user))
			{
				throw new Exception('Доступ запрещен.');
			}
			
			// находим комиссии
			$this->load->model('ConfigModel', 'Config');
			$config = $this->Config->getConfig();
			
			if (empty($config) OR
				empty($config['taobao_payment_tax']))
			{
				throw new Exception('Невозможно добавить заявку на оплату заказа Taobao.com. Попробуйте еще раз.');
			}
			
			$this->load->model('CurrencyModel', 'Currencies');
			$currency = $this->Currencies->getById('CNY');
				
			if ( ! $currency)
			{
				throw new Exception('Невозможно конвертировать сумму в доллары. Попробуйте еще раз.');
			}
			
			// валидация
			Check::reset_empties();
			
			$taobao_payment_link1 = Check::str('taobao_payment_link1', 4096, 1);
			$taobao_payment_amount1 = Check::int('taobao_payment_amount1');

			$empties = Check::get_empties();
			
			if (empty($taobao_payment_link1))
			{
				throw new Exception('Введите ссылку на заказ Taobao.com.');
			}

			if (empty($taobao_payment_amount1))
			{
				throw new Exception('Введите сумму для оплаты заказа.');
			}
			
			// собираем данные
			$taobao_payment_count = Check::int('taobao_payment_count');
			$taobao_payment_count = ($taobao_payment_count AND $taobao_payment_count <= 5) ?
				$taobao_payment_count :
				1;
				
			$payments_total = $taobao_payment_amount1;
			$payments = array(
				0 => array(
					$taobao_payment_link1,
					$taobao_payment_amount1
			));
			
			for ($i = 2; $i <= $taobao_payment_count; $i++)
			{
				$amount = Check::int('taobao_payment_amount' . $i);				
				
				if (empty($amount))
				{
					throw new Exception('Введите сумму для оплаты заказа.');
				}
			
				$payments_total += $amount;
				
				$payments[] = array(
					Check::str('taobao_payment_link' . $i, 4096, 1),
					$amount
				);
			}
			
			// вычисляем суммы платежа
			$payments_total_usd = ceil(parent::convert($currency, (float)$payments_total));
			$payments_tax = ceil(
				$config['taobao_payment_tax']->config_value *
				$payments_total_usd * 
				0.01
				);
			
			if ($user->user_coints < ($payments_total_usd + $payments_tax))
			{
				throw new Exception("Недостаточно денег на счету. <a target='_blank' href='/client/showAddBalance'>Пополнить.</a>");
			}
			
			// шлем мыло
			Mailer::sendTaobaoPaymentNotification(
				Mailer::SUBJECT_TAOBAO_PAYMENT,
				Mailer::TAOBAO_PAYMENT_NOTIFICATION, 
				$user->user_id,
				$user->user_email, 
				$payments,
				$payments_total,
				$payments_total_usd,
				$payments_tax);
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}
	
	public function importOrder()
	{
		try
		{
			Check::reset_empties();
			$country_id	= Check::str('importcountry', 255, 1);
			$userfile = isset($_FILES['importfile']) && ! $_FILES['importfile']['error'];
			$empties = Check::get_empties();	
		
			if (empty($country_id))
			{
				throw new Exception('Выберите страну.');
			}
				
			if ( ! $userfile)
			{
				throw new Exception('Выберите файл.');
			}

			Excel::ImportClientOrder($_FILES['importfile'], $this->user->user_id, $country_id);
		}
		catch (Exception $ex)
		{
			echo $ex->getMessage();
		}
	}

	public function addBidComment($bid_id, $comment_id = NULL)
	{
		parent::addBidComment($bid_id, $comment_id);
	}
	
	public function addPaymentComment($payment_id, $comment_id = NULL)
	{
		parent::addPaymentComment($payment_id, $comment_id);
	}

	public function chooseBid($bid_id)
	{
		try 
		{
			// предложение
			if ( ! is_numeric($bid_id))
			{				
				throw new Exception('Доступ запрещен.');
			}
						
			$this->load->model('BidModel', 'Bids');
			$bid = $this->Bids->getById($bid_id);

			if (empty($bid))
			{				
				throw new Exception('Предложение не найдено.');
			}

			$order_id = $bid->order_id;

			// заказ
			$order = $this->getPrivilegedOrder(
				$order_id,
				'Заказ недоступен.');

			//
			if ( ! empty($order->order_manager))
			{
				throw new Exception('Извините, уже выбран другой исполнитель.');
			}

			// позволяет ли текущий статус редактирование
			$view['editable_statuses'] = $this->Orders->getEditableStatuses($this->user->user_group);

			if ( ! in_array($order->order_status, $view['editable_statuses']))
			{
				throw new Exception('Доступ запрещен.');
			}

			// пересчитываем заказ
			$order->order_manager = $bid->manager_id;
			$order->order_status = 'processing';

			if ( ! $this->Orders->recalculate($order))
			{
				throw new Exception('Предложение не выбрано. Ошибка расчета стоимости заказа.');
			}

			// сохраняем заказ
			if ( ! $this->Orders->saveOrder($order))
			{
				throw new Exception('Предложение не выбрано. Обновите страницу и попробуйте еще раз.');
			}

			// показываем новую форму заказа
			$this->load->model('ClientModel', 'Clients');
			$this->load->model('AddressModel', 'Addresses');
			$this->load->model('ManagerModel', 'Managers');

			$view['addresses'] = $this->Addresses->getAddressesByUserId($this->user->user_id);
			$view['statuses'] = $this->Orders->getAllStatuses();
			$view['editable_statuses'] = $this->Orders->getEditableStatuses($this->user->user_group);
			$view['payable_statuses'] = $this->Orders->getPayableStatuses($this->user->user_group);

			$view['order'] = $order;
			$this->Orders->prepareOrderView($view);

			$view['selfurl'] = BASEURL.$this->cname.'/';
			$view['viewpath'] = $this->viewpath;
			$this->load->view("/client/ajax/showOrderInfoAjax", $view);
		}
		catch (Exception $e)
		{
			//echo $e->getMessage();
		}
	}

	public function unchooseBid($order_id)
	{
		try 
		{
			// предложение
			if ( ! is_numeric($order_id))
			{				
				throw new Exception('Доступ запрещен.');
			}
						
			// заказ
			$this->load->model('OrderModel', 'Orders');
			$order = $this->Orders->getById($order_id);

			if (empty($order))
			{				
				throw new Exception('Заказ не найден.');
			}

			if (empty($order->order_manager))
			{				
				return;
			}
			
			$order->order_manager = 0;
			$order->order_status = 'pending';

			// пересчитываем заказ
			if ( ! $this->Orders->recalculate($order))
			{
				throw new Exception('Невожможно пересчитать стоимость заказа. Попоробуйте еще раз.');
			}

			if ( ! $this->Orders->saveOrder($order))
			{
				throw new Exception('Заказ не сохранен. Обновите страницу и попробуйте еще раз.');
			}

			// показываем новую форму заказа
			//$this->load->model('ClientModel', 'Clients');
			$this->load->model('AddressModel', 'Addresses');
			//$this->load->model('ManagerModel', 'Managers');

			$view['addresses'] = $this->Addresses->getAddressesByUserId($this->user->user_id);
			$view['statuses'] = $this->Orders->getAllStatuses();
			$view['editable_statuses'] = $this->Orders->getEditableStatuses($this->user->user_group);
			$view['payable_statuses'] = $this->Orders->getPayableStatuses($this->user->user_group);

			$view['order'] = $order;

			$this->Orders->prepareOrderView($view);

			$view['selfurl'] = BASEURL.$this->cname.'/';
			$view['viewpath'] = $this->viewpath;
			$this->load->view("/client/ajax/showOrderInfoAjax", $view);
		}
		catch (Exception $e)
		{
			//echo $e->getMessage();
		}
	}

	public function saveProfilePhoto()
	{
		try
		{
			// находим пользователя
			$this->load->model('UserModel', 'User');
			$user = $this->User->getById($this->user->user_id);

			// находим партнера
			$this->load->model('ClientModel', 'Client');
			$client = $this->Client->getById($this->user->user_id);
			$userfile						= (isset($_FILES['userfile']) AND ! $_FILES['userfile']['error']);
			
			
			$this->db->trans_begin();
			
			if ($userfile)
			{ 
				// загрузка файла

				
				$config['upload_path']			= $_SERVER['DOCUMENT_ROOT']."/upload/avatars";
				$config['allowed_types']		= 'gif|jpeg|jpg|png|GIF|JPEG|JPG|PNG';
				$config['max_size']				= '3072';
				$config['encrypt_name'] 		= TRUE;
				$max_width						= 1024;
				$max_height						= 768;
				$this->load->library('upload', $config);

				if ( ! $this->upload->do_upload())
				{
					throw new Exception(strip_tags(trim($this->upload->display_errors())));
				}
				
				$uploadedImg = $this->upload->data();
				if (!rename($uploadedImg['full_path'],$_SERVER['DOCUMENT_ROOT']."/upload/avatars/".$this->user->user_id.".jpg"))
				{
					throw new Exception("Bad file name!");
				}
				
				$uploadedImg	= $_SERVER['DOCUMENT_ROOT']."/upload/avatars/".$this->user->user_id.".jpg";
				$imageInfo		= getimagesize($uploadedImg);

				if ($imageInfo[0]>$max_width OR $imageInfo[1]>$max_height)
				{
					$config['image_library']	= 'gd2';
					$config['source_image']		= $uploadedImg;
					$config['maintain_ratio']	= TRUE;
					$config['width']			= $max_width;
					$config['height']			= $max_height;
					
					$this->load->library('image_lib', $config); // загружаем библиотеку
					$this->image_lib->resize(); // и вызываем функцию
				}

                $client->avatar = "/upload/avatars/".$this->user->user_id.".jpg";
			}
			// наконец, все сохраняем
			$client = $this->Client->updateClient($client);

            if (!$client)
			{ 
				throw new Exception('Клиент не сохранен. Попробуйте еще раз.');
			}
			
			// коммитим транзакцию
			if ($this->db->trans_status() === FALSE) 
			{ 
				throw new Exception('Невозможно сохранить данные партнера. Попробуйте еще раз.');
			} 
			echo $client->avatar.'?r='.rand(0,99999);		
			$this->db->trans_commit();
		}
		catch (Exception $e) 
		{
			$this->db->trans_rollback();
		}
	}
		
	public function saveProfile()
	{
		try
		{
			// находим пользователя
			$this->load->model('UserModel', 'User');
			$user = $this->User->getById($this->user->user_id);

			// находим партнера
			$this->load->model('ClientModel', 'Client');
			$client = $this->Client->getById($this->user->user_id);

            // получаем необязательные поля
            $client->skype					= Check::str('skype', 255, 0);
            $client->about_me				= Check::str('about_me', 65535, 0);
		 
			// валидация пользовательского ввода
			Check::reset_empties();
            $user->user_email = Check::email(Check::str('email', 128, 4));

			if (isset($_POST['password']) &&
				$_POST['password'])
			{
				$user->user_password = Check::str('password', 32, 1);

				if (isset($user->user_password))
				{
					$user->user_password = md5($user->user_password);
				}
			}


			$client->client_country		    = Check::int('client_country');
            $client->notifications_on       = Check::chkbox('notifications_on');

			$empties = Check::get_empties();

			if ($empties)
			{
				throw new Exception('Одно или несколько полей не заполнено. Попробуйте еще раз.');
			}

			$this->db->trans_begin();
			
			 
			// наконец, все сохраняем
			$user = $this->User->updateUser($user);
			$client = $this->Client->updateClient($client);

            if ( ! $user || ! $client)
			{
				throw new Exception('Клиент не сохранен. Попробуйте еще раз.');
			}
			
			// коммитим транзакцию
			if ($this->db->trans_status() === FALSE) 
			{
				throw new Exception('Невозможно сохранить данные партнера. Попробуйте еще раз.');
			}
		 
			$this->db->trans_commit();
		}
		catch (Exception $e) 
		{
			$this->db->trans_rollback();
		}
	}

    public function saveAddress()
    {
        try
        {
            // находим пользователя
            $this->load->model('UserModel', 'User');
            $user = $this->User->getById($this->user->user_id);

            // находим адрес
            $this->load->model('AddressModel', 'Addresses');
            $address = new stdClass();

            $address->address_user = $this->user->user_id;
            $address->address_recipient = Check::str('recipient', 255, 1);
            $address->address_country = Check::int('country');
            $address->address_town = Check::str('city', 255, 1);
            $address->address_zip = Check::str('index', 255, 1);
            $address->address_address = Check::str('address', 4096, 1);
            $address->address_phone = Check::str('phone', 255, 1);
            $address->address_is_default = false;


            // валидация пользовательского ввода
            $empties = Check::get_empties();
            if ($empties)
            {
                throw new Exception('Одно или несколько полей не заполнено. Попробуйте еще раз.');
            }

            $this->db->trans_begin();

            // наконец, все сохраняем
            $address = $this->Addresses->updateAddress($address);

            if ( ! $address)
            {
                throw new Exception('Адрес не сохранен. Попробуйте еще раз.');
            }

            // коммитим транзакцию
            if ($this->db->trans_status() === FALSE)
            {
                throw new Exception('Невозможно сохранить данные партнера. Попробуйте еще раз.');
            }

            $this->db->trans_commit();

            echo json_encode($this->Addresses->getAddressById($address->address_id));
        }
        catch (Exception $e)
        {
            $this->db->trans_rollback();
        }
    }

    public function removeAddress()
    {
        $address_id = Check::int('address_id');
        if ($address_id != 0)
        {
            $this->load->model('AddressModel', 'Addresses');
            echo (int)$this->Addresses->deleteAddress($address_id); die();
        }
        echo 0; die();
    }

	public function saveRating()
	{
		try
		{
			$rating = new stdClass();

			$rating->client_id = $this->user->user_id;
			$rating->communication_rating = Check::float('communication_rating', -1);
			$rating->buy_rating = Check::float('buy_rating', -1);
			$rating->consolidation_rating = Check::float('consolidation_rating', -1);
			$rating->pack_rating = Check::float('pack_rating', -1);

			// валидация пользовательского ввода
			Check::reset_empties();
			$rating->manager_id = Check::int('manager_id');
			$rating_type = Check::str('rating_type', 8, 7);

			$empties = Check::get_empties();

			if ($empties)
			{
				throw new Exception('Одно или несколько полей не заполнено. Попробуйте еще раз.');
			}

			// подготовка данных к сохранению
			switch ($rating_type)
			{
				case "positive" :
					$rating->rating_type = '1';
					break;
				case "negative" :
					$rating->rating_type = '-1';
					break;
				case "neutral" :
					$rating->rating_type = '0';
					break;
			}

			$rating->communication_rating = (isset($rating->communication_rating) AND
					$rating->communication_rating >= 0 AND
					$rating->communication_rating < 5) ?
				strval($rating->communication_rating / 4)
				: NULL;
			$rating->buy_rating = (isset($rating->buy_rating) AND
					$rating->buy_rating >= 0 AND
					$rating->buy_rating < 5) ?
				strval($rating->buy_rating / 4)
				: NULL;
			$rating->consolidation_rating = (isset($rating->consolidation_rating) AND
					$rating->consolidation_rating >= 0 AND
					$rating->consolidation_rating < 5) ?
				strval($rating->consolidation_rating / 4)
				: NULL;
			$rating->pack_rating = (isset($rating->pack_rating) AND
					$rating->pack_rating >= 0 AND
					$rating->pack_rating < 5) ?
				strval($rating->pack_rating / 4)
				: NULL;

			// сохраняем
			$this->load->model('ManagerRatingsModel', 'Ratings');

			$rating = $this->Ratings->addRating($rating);

			if (empty($rating))
			{
				throw new Exception('Отзыв не сохранен. Попробуйте еще раз.');
			}
		}
		catch (Exception $e)
		{

		}
	}

	protected function showOrderBreadcrumb($order)
	{
		$index = 1;

		if ($order->order_client == $this->user->user_id)
		{
			$index = 2;
		}

		Breadcrumb::setCrumb(array(
			"/client/order/{$order->order_id}" => "Заказ №{$order->order_id}"
		), $index, TRUE);
	}

	public function updateOrder($order_id)
	{
		try
		{
			if ( ! is_numeric($order_id))
			{
				throw new Exception('Доступ запрещен.');
			}

			// роли и разграничение доступа
			$order = $this->getPrivilegedOrder(
				$order_id,
				'Невозможно сохранить детали заказа. Заказ недоступен.');

			$this->load->model('OrderModel', 'Orders');
			$this->load->model('ClientModel', 'Clients');
			$this->load->model('AddressModel', 'Addresses');

			// позволяет ли текущий статус редактирование
			$editable_statuses = $this->Orders->getEditableStatuses($this->user->user_group);

			if ( ! in_array($order->order_status, $editable_statuses))
			{
				throw new Exception('Доступ запрещен.');
			}

			$addresses = $this->Addresses->getAddressesByUserId($this->user->user_id);

			// валидация пользовательского ввода
			$order->preferred_delivery	= Check::str('delivery', 255, 1, '');
			$order->address_id = Check::int('address');

			// если в профиле нет адресов, добавляем новый
			if (empty($addresses))
			{
				$order->order_address = Check::str('address_text', 255, 1, '');

				if ($order->order_address)
				{
					$client = $this->Clients->getStatistics($this->user->user_id);
					$address = new stdClass();

					$address->address_user = $this->user->user_id;
					$address->address_recipient = $client->fullname;
					$address->address_country = $order->order_country_to;
					$address->address_address = $order->order_address;
					$address->is_generated = TRUE;

					$address = $this->Addresses->addAddress($address);
					$order->address_id = $address->address_id;
				}
			}
			// если такой адрес есть, ставим его в заказ
			else
			{
				$address_id = Check::int('address');

				if ($address_id)
				{
					foreach ($addresses as $address)
					{
						if ($address->address_id == $address_id)
						{
							$order->address_id = $address_id;
							$order->order_address = implode(', ', array(
								$address->address_zip,
								$address->country_name,
								$address->address_address,
								$address->address_town,
								$address->address_recipient,
								'тел.' . $address->address_phone
							));
							break;
						}
					}
				}
			}

			// сохранение результатов
			$this->Orders->saveOrder($order);
		}
		catch (Exception $e)
		{

		}
	}

	protected function init_paging()
	{
		$this->load->helper('url');
		$this->load->library('pagination');

		$handler = $this->uri->segment(2);

		if ($handler == 'order' OR
			$handler == 'showOpenPayments' OR
			$handler == 'showPayedPayments')
		{
			if ($handler == 'order')
			{
				$this->paging_base_url =
					'/client/showOpenPayments' .
						'/' .
						($this->uri->segment(3) ? $this->uri->segment(3) : 0);
			}
			else
			{
				$this->paging_base_url =
					'/client/' .
						($this->uri->segment(2) ? $this->uri->segment(2) : 0) .
						'/' .
						($this->uri->segment(3) ? $this->uri->segment(3) : 0);
			}

			$this->paging_uri_segment = 4;
			$this->paging_offset = $this->uri->segment(4);
		}
		else
		{
			parent::init_paging();
		}
	}

	protected function processBalanceFilter()
	{
		$filter = $this->initBalanceFilter();

		// сброс фильтра
		if (isset($_POST['resetFilter']) AND $_POST['resetFilter'] == '1')
		{
			return $filter;
		}

		$filter->svalue		= Check::str('svalue', 255, 1, '');

		if (isset($filter->sfield) AND
			$filter->sfield)
		{
			$filter->condition['like'] = array(
				'manager_id' => $filter->svalue,
				'manager_login' => $filter->svalue
			);
		}

		return $filter;
	}

	protected function initBalanceFilter()
	{
		$filter = new stdClass();
		$filter->svalue		= '';

		return $filter;
	}

	public function balance()
	{
		try
		{
			$filter = $this->initFilter('Balance');
			$this->load->model('OrderModel', 'Orders');

			$view['balance'] = $this->Orders->getFilteredBalance($this->user->user_id, $filter->svalue);

			// парсим шаблон
			$view['selfurl'] = BASEURL.$this->cname.'/';
			$view['viewpath'] = $this->viewpath;

			$this->load->view($this->viewpath."ajax/showBalance", $view);
		}
		catch (Exception $e)
		{
			//$error->m	= $e->getMessage();
		}
	}

	// BOF: перенаправление обработчиков в базовый контроллер
	public function deletePayment($oid)
	{
		parent::deletePayment($oid);
	}

	public function addProductManualAjax()
	{
		parent::addProductManualAjax();
	}

	public function addO2iComment()
	{
		parent::addO2iComment();
	}

	public function deleteOrder()
	{
		parent::deleteOrder();
	}

	public function deleteProduct($odid)
	{
		parent::deleteProduct($odid);
	}

	public function payment()
	{
		parent::showO2iComments();
	}

	public function addOrderComment($order_id, $comment_id = NULL)
	{
		parent::addOrderComment($order_id, $comment_id);
	}

	public function order()
	{
		parent::showOrderDetails();
	}

	public function update_odetail_weight($order_id, $odetail_id, $weight)
	{
		parent::update_odetail_weight($order_id, $odetail_id, $weight);
	}

	public function update_odetail_price($order_id, $odetail_id, $price)
	{
		parent::update_odetail_price($order_id, $odetail_id, $price);
	}

	public function update_odetail_tracking($order_id, $odetail_id, $tracking)
	{
		parent::update_odetail_tracking($order_id, $odetail_id, $tracking);
	}

	public function update_odetail_pricedelivery($order_id, $odetail_id, $pricedelivery)
	{
		parent::update_odetail_pricedelivery($order_id, $odetail_id, $pricedelivery);
	}

	public function update_joint_pricedelivery($order_id, $joint_id, $cost)
	{
		parent::update_joint_pricedelivery($order_id, $joint_id, $cost);
	}

	public function updateProduct($order_id, $odetail_id)
	{
		parent::updateProduct($order_id, $odetail_id);
	}

	public function joinProducts($order_id)
	{
		parent::joinProducts($order_id);
	}

	public function removeJoint($order_id, $joint_id)
	{
		parent::removeJoint($order_id, $joint_id);
	}

	public function showOpenPayments($order_id)
	{
		parent::showPayments($order_id, 'open');
	}

	public function showPayedPayments($order_id)
	{
		parent::showPayments($order_id, 'payed');
	}

	public function history()
	{
		parent::showPaymentHistory();
	}

	public function filterBalance()
	{
		$this->filter('Balance', 'balance');
	}

	public function addPaymentFoto()
	{
		parent::addPaymentFoto();
	}

	public function deletePaymentFoto($o2i_id, $filename)
	{
		parent::deletePaymentFoto($o2i_id, $filename);
	}

	public function moveProducts($old_order_id, $new_order_id)
	{
		parent::moveProducts($old_order_id, $new_order_id);
	}
	// EOF: перенаправление обработчиков в базовый контроллер
}