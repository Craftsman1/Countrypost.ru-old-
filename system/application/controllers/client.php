<?php require_once BASE_CONTROLLERS_PATH.'ClientBaseController'.EXT;

class Client extends ClientBaseController {
	function Client()
	{
		parent::__construct();

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
			'package_open'			=> ($openp?count($openp):0),
			'package_payed'			=> ($payedp?count($payedp):0),
			'package_sent'			=> ($sentp?count($sentp):0),
			'taobao_register_tax'	=> $config['taobao_register_tax']->config_value,
			'alipay_refill_tax'		=> $config['alipay_refill_tax']->config_value,
			'taobao_payment_tax'	=> $config['taobao_payment_tax']->config_value,
			'cny_rate'				=> $cny_rate->cbr_cross_rate,
		));
	}
	
	public function showShop()
	{	
		foreach ($_POST as $key => $val){
			$$key = $val;
		}
		
		$error		= new stdClass();
		$error->m	= '';
		$shop = array();
		
		try{								
			if (!$sname || !$surl) 
				throw new Exception(iconv('UTF-8', 'Windows-1251', 'Пожалуйста, введите название магазина!'));
				
			$shop['name'] = $sname;
				
			if (!Check::url($surl))
				throw new Exception(iconv('UTF-8', 'Windows-1251', 'Пожалуйста, введите адрес магазина!'));
			
			$shop['url'] = substr($surl, 7);
			
		}catch (Exception $e){	
			$error->m	= $e->getMessage();				
		}
		
		$view = array(
			'error'		=> $error,
			'shop'		=> $shop			
		);
		
		$this->load->model('OdetailModel', 'OdetailModel');
		$Odetails = $this->OdetailModel->getFilteredDetails(array('odetail_client' => $this->user->user_id, 'odetail_order' => 0));
		
		if (count($Odetails)) {
			$view['country'] = false;
		}
		else {
			$view['country'] = true;
			$this->load->model('CountryModel', 'CountryModel');
			$view['countries'] = $this->CountryModel->getClientAvailableCountries($this->user->user_id);
		}		
		
		View::showChild($this->viewpath.'/pages/show_shop', $view);
	}
	
	public function addProductManual() {
		
		Check::reset_empties();
		$detail									= new stdClass();
		$detail->odetail_link					= Check::str('olink', 500, 1);
		$detail->odetail_img					= Check::str('userfileimg', 500, 1);
		$userfile								= isset($_FILES['userfile']) && !$_FILES['userfile']['error'];
		$detail->odetail_shop_name				= Check::str('shop', 255, 0);
		$detail->odetail_product_name			= Check::str('oname', 255, 0);
		$detail->odetail_product_amount			= Check::int('oamount');
		$detail->odetail_product_color			= Check::str('ocolor', 32, 0);
		$detail->odetail_product_size			= Check::str('osize', 32, 0);
		$detail->odetail_client					= $this->user->user_id;
		$detail->odetail_order					= Check::int('order_id');
		$detail->odetail_manager				= 0;
		$country_manager						= Check::str('ocountry', 255, 1);
		$empties								= Check::get_empties();		
		
		try 
		{
			// обязательны для заполнения:
			// olink
			// ocountry
			// userfileimg либо клиентская картинка
			if (!$detail->odetail_link)
			{
				throw new Exception('Неверная ссылка на товар!');
			}
				
			if (!$detail->odetail_product_amount)
			{
				$detail->odetail_product_amount = 1;
			}				
			
			if ($empties &&
				!$detail->odetail_img && 
				!$userfile)
			{
				throw new Exception('Загрузите или добавьте ссылку на скриншот.');
			}
			
			if ($userfile)
			{
				unset($detail->odetail_img);
			}
				
			$this->load->model('OdetailModel', 'OdetailModel');
			$Odetails = $this->OdetailModel->getFilteredDetails(array('odetail_client' => $this->user->user_id, 'odetail_order' => 0));
				
			if (count($Odetails)) {
				$detail->odetail_manager = $Odetails[0]->odetail_manager;
			}
			else {
				$this->load->model('CountryModel', 'CountryModel');
				$Countries = $this->CountryModel->getClientAvailableCountries($this->user->user_id);

				foreach ($Countries as $Country) {
					if ($Country->country_id == $country_manager) {
						$detail->odetail_manager = $Country->manager_user;
					}
				}

				if (!$detail->odetail_manager)
					throw new Exception('Ошибка переданных данных');
			}
			
			// открываем транзакцию
			$this->db->trans_begin();	

			$detail = $this->OdetailModel->addOdetail($detail);

			// если заказ уже создан, вычисляем его статус
			if ($detail->odetail_order)
			{
				// находим заказ
				$this->load->model('OrderModel', 'Orders');

				$order = $this->Orders->getClientOrderById($detail->odetail_order, $this->user->user_id);
				
				if (!$order)
				{
					throw new Exception('Невозможно изменить статусы товаров. Заказ не найден.');
				}
		
				// вычисляем общий статус товаров
				$status = $this->OdetailModel->getTotalStatus($detail->odetail_order);
				
				if (!$status)
				{
					throw new Exception('Статус заказа не определен. Попоробуйте еще раз.');
				}
				
				$order->order_status = $this->Orders->calculateOrderStatus($status);
				
				// меняем статус заказа
				$new_order = $this->Orders->saveOrder($order);
				
				if (!$new_order)
				{
					throw new Exception('Невожможно изменить статус заказа. Попоробуйте еще раз.');
				}
			}
			
 			// загружаем файл
			if ($userfile)
			{
				$old = umask(0);
				// загрузка файла
				//$config['upload_path'] = BASEPATH.'../upload/orders/'.$this->user->user_id.'/';
				if (!is_dir($_SERVER['DOCUMENT_ROOT']."/upload/orders/{$this->user->user_id}")){
					mkdir($_SERVER['DOCUMENT_ROOT']."/upload/orders/{$this->user->user_id}",0777);
				}

				$config['upload_path']			= $_SERVER['DOCUMENT_ROOT'].'/upload/orders/'.$this->user->user_id;
				$config['allowed_types']		= 'gif|jpeg|jpg|png|GIF|JPEG|JPG|PNG';
				$config['max_size']				= '3072';
				$config['encrypt_name'] 		= TRUE;
				$max_width						= 1024;
				$max_height						= 768;
				$this->load->library('upload', $config);

				if (!$this->upload->do_upload()) {
					throw new Exception(strip_tags(trim($this->upload->display_errors())));
				}
				
				$uploadedImg = $this->upload->data();
				if (!rename($uploadedImg['full_path'],$_SERVER['DOCUMENT_ROOT'].'/upload/orders/'.$this->user->user_id.'/'.$detail->odetail_id.'.jpg')){
					throw new Exception("Bad file name!");
				}
				
				$uploadedImg	= $_SERVER['DOCUMENT_ROOT'].'/upload/orders/'.$this->user->user_id.'/'.$detail->odetail_id.'.jpg';
				$imageInfo		= getimagesize($uploadedImg);
				if ($imageInfo[0]>$max_width || $imageInfo[1]>$max_height){
					
					$config['image_library']	= 'gd2';
					$config['source_image']		= $uploadedImg;
					$config['maintain_ratio']	= TRUE;
					$config['width']			= $max_width;
					$config['height']			= $max_height;
					
					$this->load->library('image_lib', $config); // загружаем библиотеку
					
					$this->image_lib->resize(); // и вызываем функцию
				}
			}
			
			// закрываем транзакцию
			$this->db->trans_commit();

			if ($detail->odetail_order > 0 )
			{
				Func::redirect(BASEURL.$this->cname.'/showOrderDetails/'.$detail->odetail_order);
			}
			else
			{
				Func::redirect(BASEURL.$this->cname.'/showBasket');
			}
			return;
			
		}catch (Exception $e){
			$this->db->trans_rollback();
			$this->result->m = $e->getMessage();		
			Stack::push('result', $this->result);

			if ($detail->odetail_order > 0 )
			{
				Func::redirect(BASEURL.$this->cname.'/showOrderDetails/'.$detail->odetail_order);
			}
			else
			{
				Func::redirect(BASEURL.$this->cname.'/showBasket');
			}
			return;
		}
		
		Func::redirect(BASEURL.$this->cname.'/showOpenOrders');
	}
	
	public function addProductManualAjax() 
	{
		parent::addProductManualAjax();
	}

	public function addBillFoto()
	{
		Check::reset_empties();
		$userfile	= isset($_FILES['userfile']) && !$_FILES['userfile']['error'];
		$o2i_id		= Check::int('order_id');
		$empties	= Check::get_empties();		
		
		try 
		{
			if ($empties)
				throw new Exception('Заявка не найдена.');
				
			if (!$userfile)
			{
				throw new Exception('Файл не загружен.');
			}
			
 			// загружаем файл
			if ($userfile)
			{
				$old = umask(0);

				if (!is_dir($_SERVER['DOCUMENT_ROOT']."/upload/orders2in/$o2i_id")){
					mkdir($_SERVER['DOCUMENT_ROOT']."/upload/orders2in/$o2i_id",0777);
				}

				$config['upload_path']			= $_SERVER['DOCUMENT_ROOT'].'/upload/orders2in/'.$o2i_id;
				$config['allowed_types']		= 'gif|jpg|png';
				$config['max_size']				= '4096';
				$config['encrypt_name'] 		= false;
				$this->load->library('upload', $config);

				if (!$this->upload->do_upload()) {
					throw new Exception(strip_tags(trim($this->upload->display_errors())));
				}
			}
		}
		catch (Exception $e){
			$this->result->m = $e->getMessage();		
			Stack::push('result', $this->result);
		}
		
		Func::redirect(BASEURL.'syspay/showOpenOrders2In');
	}
	
	public function deleteBillFoto($o2i_id, $filename) 
	{
		Check::reset_empties();
		$detail		= new stdClass();
		
		try 
		{
			$path = $_SERVER['DOCUMENT_ROOT']."/upload/orders2in/$o2i_id/$filename";
			$this->load->model('Order2InModel', 'Order2in');
		
			if ($o2i = $this->Order2in->getInfo(array('order2in_user' => $this->user->user_id, 'order2in_id' => intval($o2i_id)))
				&& is_file($path))
			{
				unlink($path);
			}
		}
		catch (Exception $e){
			$this->result->m = $e->getMessage();		
			Stack::push('result', $this->result);
		}
		
		Func::redirect(BASEURL.'syspay/showOpenOrders2In');
	}
	
	public function addProduct() {
		
		Check::reset_empties();
		$detail							= new stdClass();
		$detail->odetail_link			= Check::str('olink', 500, 10);
		$detail->odetail_shop_name		= Stack::shift('shop', true);
		$detail->odetail_product_name	= Check::str('oname', 255, 1);
		$detail->odetail_product_amount	= Check::int('oamount');
		$detail->odetail_product_color	= Check::str('ocolor', 255, 1);
		$detail->odetail_product_size	= Check::str('osize', 255, 1);
		$detail->odetail_client			= $this->user->user_id;
		$detail->odetail_order			= 0;
		$detail->odetail_manager		= 0;
		$x1								= Check::int('x1');
		$x2								= Check::int('x2');
		$y1								= Check::int('y1');
		$y2								= Check::int('y2');
		$width							= Check::int('sh_width');
		$fname							= Check::str('fname', 255, 1);
		$empties						= Check::get_empties();
		
		$country_manager				= Check::str('ocountry', 255, 1);
		
		try {
			if ($empties)
				throw new Exception('Ошибка переданных данных! Одно или несколько полей не заполнено!');
				
			$this->load->model('OdetailModel', 'OdetailModel');
			$Odetails = $this->OdetailModel->getFilteredDetails(array(
																'odetail_client' => $this->user->user_id, 
																'odetail_order' => 0
			));
			
			if (count($Odetails)) {
				$detail->odetail_manager = $Odetails[0]->odetail_manager;
			}
			else {
				
				if (!$country_manager)
					throw new Exception('Не указанна страна.');
				
				$this->load->model('CountryModel', 'CountryModel');
				$Countries = $this->CountryModel->getClientAvailableCountries($this->user->user_id);
				foreach ($Countries as $Country) {
					if ($Country->country_id == $country_manager) {
						$detail->odetail_manager = $Country->manager_user;
					}
				}
				if (!$detail->odetail_manager)
					throw new Exception('Ошибка переданных данных');
			}
			
			$this->db->trans_begin();	
			
			$detail->odetail_link = str_replace($this->config->item('base_url').'proxy/?url=', '', $detail->odetail_link);
			$detail->odetail_link = urldecode($detail->odetail_link);
			
			if (strpos($detail->odetail_link, 'http://') !== 0)
				$detail->odetail_link = 'http://'.$detail->odetail_link;
				
			$detail = $this->OdetailModel->addOdetail($detail);
			
			$this->OdetailModel->makeScreenshot($detail, $x1, $y1, $x2+$x1, $y2+$y1, $width);
			
			$this->db->trans_commit();
			
			Func::redirect(BASEURL.$this->cname.'/showBasket');
			
		}catch (Exception $e){
			$this->db->trans_rollback();
			$this->result->m = $e->getMessage();		
			Stack::push('result', $this->result);
		}
		
		$this->proxy($detail->odetail_link);
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
	
	public function showBasket() 
	{		
		$this->load->model('OdetailModel', 'OdetailModel');
		$Odetails = $this->OdetailModel->getNewDetails($this->user->user_id);
		
		if (empty($Odetails))
		{
			Func::redirect(BASEURL.$this->cname.'/showOpenOrders');
		}
		
		$odetail = $Odetails[0];
		
		$this->load->model('CountryModel', 'CountryModel');
		$Countries	= $this->CountryModel->getClientAvailableCountries($this->user->user_id);
		$Country = $this->CountryModel->getById($odetail->odetail_country);
		
		$view = array(
			'Odetails'	=> $Odetails,
			'Countries'	=> $Countries,
			'Country'	=> $Country
		);
		
		View::showChild($this->viewpath.'/pages/show_basket', $view);
	}
	
	public function showScreen($oid=null) 
	{
        header('Content-type: image/jpg');
        $this->load->model('OdetailModel', 'OdetailModel');
        if ($Detail = $this->OdetailModel->getInfo(array('odetail_client' => $this->user->user_id, 'odetail_id' => intval($oid)))) {
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
	
	public function showOrder2InFoto($oid, $filename) {
		header('Content-type: image/jpg');
		$this->load->model('Order2InModel', 'Order2in');
		if ($o2i = $this->Order2in->getInfo(array('order2in_user' => $this->user->user_id, 'order2in_id' => intval($oid)))) {
			readfile($_SERVER['DOCUMENT_ROOT'].'/upload/orders2in/'.$oid.'/'.$filename);
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
	
	public function showAddBalance()
	{
		Func::redirect('/syspay');
		return;
	}
	
	public function showOutMoney() 
	{
		$this->load->model('Order2outModel', 'Order2out');
		$this->load->model('CurrencyModel', 'Currencies');
		$this->load->model('PaymentServiceModel', 'Services');
		
		$Orders = $this->Order2out->getUserOrders($this->user->user_id);
		
		/* пейджинг */
		$this->per_page = $this->per_page_o2o;
		$this->init_paging();		
		$this->paging_count = count($Orders);
		
		if ($Orders)
		{
			$Orders = array_slice($Orders, $this->paging_offset, $this->per_page);
		}
		
		$view = array (
			'Orders' => $Orders,
			'statuses'	=> $this->Order2out->getStatuses(),
			'services'	=> $this->Services->getOutServices(),
			'usd' => (float) ceil($this->Currencies->getRate('USD') * 100) / 100,
			'pager' => $this->get_paging()
		);
		
		// парсим шаблон
		if ($this->uri->segment(4) == 'ajax')
		{
        	$view['selfurl'] = BASEURL.$this->cname.'/';
			$view['viewpath'] = $this->viewpath;
			$this->load->view($this->viewpath."ajax/showOutMoney", $view);
		}
		else
		{
			View::showChild($this->viewpath."pages/showOutMoney", $view);
		}
	}
	
	public function order2out() 
	{
		Check::reset_empties();	
		$order2out	= new stdClass();
		$order2out->order2out_ammount = Check::int('ammount_raw');
		$order2out->order2out_ammount_rur = Check::int('ammount');
		$order2out->order2out_payment_service = Check::txt('payment_service', 2, 2);
		
		// input validation
		if (isset($order2out->order2out_payment_service))
		{
			$service = $order2out->order2out_payment_service;
			// webmoney
			if ($service == 'wm')
			{
				$order2out->order2out_details = 'Номер кошелька: '.Check::txt('wm_number', 12, 12);
			}
			// liqpay
			else if ($service == 'lp')
			{
				$order2out->order2out_details = 'Номер телефона: '.Check::txt('lp_number', 9, 9);
			}
			// qiwi
			else if ($service == 'qw')
			{
				$order2out->order2out_details = 'Номер телефона (кошелька): '.Check::txt('qw_number', 10, 10);
			}
			// sberbank
			else if ($service == 'bm')
			{
				$bm_surname = Check::txt('bm_surname', 127, 1);
				$bm_name = Check::txt('bm_name', 127, 1);
				$bm_otc = Check::txt('bm_otc', 127, 1);
				$bm_bik = Check::txt('bm_bik', 9, 9);
				$bm_target = Check::txt('bm_target', 127, 1);
				$bm_number = Check::txt('bm_number', 20, 20);
			}
		}
		
		$empties = Check::get_empties();
		
		try
		{
			if ($order2out->order2out_ammount <= 0)
			{
				throw new Exception('Введите сумму платежа.');
			}
			else if ($this->user->user_coints < $order2out->order2out_ammount)
			{
				throw new Exception('У Вас недостаточно средств для вывода.');
			}
			
			if ($empties)
			{
				if ($service == 'lp')
				{
					throw new Exception('Номер телефона имеет неправильный формат.');
				}
				else if ($service == 'qw')
				{
					throw new Exception('Номер телефона (кошелька) имеет неправильный формат.');
				}
				else if ($service == 'wm')
				{
					throw new Exception('Номер кошелька имеет неправильный формат.');
				}
				else if ($service == 'bm')
				{
					if (!$bm_surname)
					{
						throw new Exception('Фамилия не заполнена.');
					}
					else if (!$bm_surname)
					{
						throw new Exception('Имя не заполнено.');
					}
					else if (!$bm_otc)
					{
						throw new Exception('Отчество не заполнено.');
					}
					else if (!$bm_number)
					{
						throw new Exception('Счет имеет неправильный формат.');
					}
					else if (!$bm_bik)
					{
						throw new Exception('БИК банка имеет неправильный формат.');
					}
					else if (!$bm_target)
					{
						throw new Exception('Назначение платежа не заполнено.');
					}
				}
				else
				{
					throw new Exception('Выберите способ вывода.');
				}
			}
			
			if ($service == 'bm')
			{
				$order2out->order2out_details = 
					'ФИО: '.$bm_surname.' '.$bm_name.' '.$bm_otc.'<br />'.
					'Счет: '.$bm_number.'<br />'.
					'БИК: '.$bm_bik.'<br />'.
					'Назначение: '.$bm_target;
			}
			
			$order2out->order2out_tax = 0;
			$order2out->order2out_user = $this->user->user_id;
			$order2out->order2out_status = 'processing';
			
			$this->db->trans_begin();
			
			$this->load->model('Order2outModel', 'Order2out');
			$order2out = $this->Order2out->addOrder($order2out);

			if (!$order2out) 
			{
				throw new Exception('Ошибка создания заявки на вывод.');
			}
			
			$tax = strtoupper($service).'_OUT_TAX'; 
			
			$payment_obj = new stdClass();
			$payment_obj->payment_from			= $this->user->user_id;
			$payment_obj->payment_to			= $order2out->order2out_details;
			$payment_obj->payment_amount_from	= $order2out->order2out_ammount;
			$payment_obj->payment_amount_to		= 0;
			$payment_obj->payment_amount_tax	= $order2out->order2out_ammount * constant($tax) / 100;
			$payment_obj->payment_amount_rur	= $order2out->order2out_ammount_rur;
			$payment_obj->payment_purpose		= 'заявка на вывод';
			$payment_obj->payment_type			= 'out';
			$payment_obj->payment_service_id	= $service;
			$payment_obj->payment_comment		= '№ '.$order2out->order2out_id;

			$this->load->model('PaymentModel', 'Payment');
			
			if (!$this->Payment->makePayment($payment_obj)) 
			{
				throw new Exception('Ошибка перевода средств между счетами. Попробуйте еще раз.');
			}			
			
			$this->session->set_userdata(array('user_coints' => $this->user->user_coints - $payment_obj->payment_amount_from));
			$this->db->trans_commit();
			$this->result->m = 'Заявка на вывод денег успешно добавлена.';

			// уведомления
			$this->load->model('UserModel', 'Users');
			
			Mailer::sendAdminNotification(
				Mailer::SUBJECT_NEW_ORDER2OUT, 
				Mailer::NEW_ORDER2OUT_CLIENT_NOTIFICATION, 
				0,
				$order2out->order2out_id, 
				$order2out->order2out_user,
				"http://countrypost.ru/admin/showClientOrdersToOut",
				null,
				$this->Users);
		}
		catch (Exception $e)
		{
			$this->db->trans_rollback();
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			$this->result->order_details = $this->getOrder2outDetails();
		}
		
		Stack::push('result', $this->result);		
		Func::redirect(BASEURL.$this->cname.'/showOutMoney');
	}
	
	public function addOrder2In() 
	{
		Check::reset_empties();
		
		$order2in = new stdClass();
		$order2in->order2in_createtime = date('Y-m-d H:i:s');
		$order2in->order2in_amount = Check::float('total_usd');
		$order2in->order2in_payment_service = Check::txt('payment_service', 3, 2);
		$order2in->order2in_details = Check::txt('account', 20, 1);
		
		// input validation
		if (isset($order2in->order2in_payment_service))
		{
			$service = $order2in->order2in_payment_service;
			
			$this->load->model('CurrencyModel', 'Currencies');
			
			switch ($service)
			{
				case 'bm' :
				case 'qw' :
				case 'sv' :
				case 'vtb' :
					$order2in->order2in_amount_local = Check::int('total_ru');
					$currency = $this->Currencies->getById('RUR');
					$order2in->order2in_currency = 'руб.';
					break;
				case 'bta' :
				case 'ccr' :
				case 'kkb' :
				case 'nb' :
				case 'tb' :
				case 'atf' :
				case 'ab' :
					$order2in->order2in_amount_local = Check::int('total_kzt');
					$currency = $this->Currencies->getById('KZT');
					$order2in->order2in_currency = '<em class="tenge">&nbsp;&nbsp;&nbsp;</em>';
					break;
				case 'pb' :
					$order2in->order2in_amount_local = Check::int('total_uah');
					$currency = $this->Currencies->getById('UAH');
					$order2in->order2in_currency = '<em class="grivna">&nbsp;&nbsp;&nbsp;</em>';
					break;
			}
		}
		
		$empties = Check::get_empties();

		try
		{
			if ($order2in->order2in_amount <= 0 OR
				$order2in->order2in_amount_local <= 0)
			{
				throw new Exception('Введите сумму платежа.');
			}
			
			$order2in->order2in_user = $this->user->user_id;
			$order2in->order2in_status = 'processing';
			$order2in->order2in_tax = $order2in->order2in_amount * constant(strtoupper($service).'_IN_TAX') * 0.01;
			
			$this->load->model('Order2InModel', 'Order2in');
			$order2in = $this->Order2in->addOrder($order2in);

			if ( ! $order2in) 
			{
				throw new Exception('Ошибка создания заявки на вывод.');
			}
			
			$this->result->m = 'Заявка на пополнение счета успешно добавлена.';
			
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
				$service == 'vtb')
			{
				$userfile	= isset($_FILES['userfile']) && ! $_FILES['userfile']['error'];
				$o2i_id		= $order2in->order2in_id;
				$empties	= Check::get_empties();		
				
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
			$this->result->e	= $e->getCode();			
			$this->result->m	= $e->getMessage();
		}
		
		Stack::push('result', $this->result);
		Func::redirect(BASEURL.'syspay/showOpenOrders2In');
	}
	
	private function getOrder2outDetails()
	{
		$order2out = new stdClass();
		$order2out->ammount = $_POST['ammount_raw'];
		$order2out->payment_service = $_POST['payment_service'];
		
		$order2out->wm_number = $_POST['wm_number'];
		$order2out->lp_number = $_POST['lp_number'];
		$order2out->qw_number = $_POST['qw_number'];
		$order2out->bm_surname = $_POST['bm_surname'];
		$order2out->bm_name = $_POST['bm_name'];
		$order2out->bm_otc = $_POST['bm_otc'];
		$order2out->bm_bik = $_POST['bm_bik'];
		$order2out->bm_target = $_POST['bm_target'];
		$order2out->bm_number = $_POST['bm_number'];
		
		return $order2out;
	}
	
	private function getOrder2inDetails()
	{
		$order2in = new stdClass();
		$order2in->bm_amount = $_POST['bm_amount'];
		$order2in->payment_service = $_POST['payment_service'];
		
		$order2in->bm_surname = $_POST['bm_surname'];
		$order2in->bm_name = $_POST['bm_name'];
		$order2in->bm_otc = $_POST['bm_otc'];
		$order2in->bm_date = $_POST['bm_date'];
		$order2in->bm_number = $_POST['bm_number'];
		
		return $order2in;
	}
	
	public function deleteOrder2out($oid) 
	{
		parent::deleteOrder2out($oid);
	}
	
	public function deleteOrder2in($oid) 
	{
		parent::deleteOrder2in($oid);
	}
	
	public function createOrder2out() {
		
		// ищем макс id заказа на вывод
		$this->load->model('Order2outModel', 'Order2out');
		$last_id = $this->Order2out->getMaxId();		
		echo($last_id[0]->max ? $last_id[0]->max + 1 : 1);
		die();
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
	
	private function putScreenshot($url, $fname)
	{
		Stack::clear('screenshot');
		//@unlink("/home/omni/kio.teralabs.ru/html/upload/orders/{$this->user->user_id}/tmp/");
		
		if (!is_dir($_SERVER['DOCUMENT_ROOT']."/upload/orders/{$this->user->user_id}/tmp/")){
			mkdir($_SERVER['DOCUMENT_ROOT']."/upload/orders/{$this->user->user_id}/tmp/", 0777, true);
		}
		
		exec("wkhtmltoimage-amd64 --load-error-handling ignore --width 1266 '$url' ".$_SERVER['DOCUMENT_ROOT']."/upload/orders/{$this->user->user_id}/tmp/$fname.jpg");
	}
	
	public function proxy($url=null) 
	{
		$this->output->enable_profiler(false);
		
		error_reporting(E_ERROR);
		header("Content-Type: text/html; charset=windows-1251");
		parse_str($_SERVER['QUERY_STRING'],$_GET);
		
		if (!$url){
			$url	= @$_GET['url'];
		}
		
		preg_match("/^.+?\.(jpg|gif|png|jpeg|bmp)$/",$url,$img_ch);
		preg_match("/^.+?\.(css|js)$/",$url,$res_ch);
		$url		= (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0  || strpos($src, '//') === 0) ? $url : 'http://'.$url;
		$parse		= parse_url($url);
		$host		= $parse['host'];
		$server_host= $_SERVER['HTTP_HOST'];

		if (!Stack::last('curHost'))
			Stack::push('curHost',$host);

		$fname = md5(time().$this->user->user_id.$this->user->user_group);
		$this->putScreenshot($url, $fname);
		
		$this->load->model('OdetailModel', 'OdetailModel');
		$Odetails	= $this->OdetailModel->getFilteredDetails(array(
																'odetail_client' => $this->user->user_id, 
																'odetail_order' => 0
		));
		
		$this->load->model('CountryModel', 'CountryModel');
		$Countries	= $this->CountryModel->getClientAvailableCountries($this->user->user_id);
			
		View::show($this->viewpath.'proxy3', array(
													'fname'			=> $fname,
													'Odetails'		=> $Odetails,
													'Countries'		=> $Countries,
													'url'			=> $url,
													'server_host'	=> $server_host,
													'host'			=> $host,
		));
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
		$this->load->model('CurrencyModel', 'Currencies');
		$this->load->model('Order2InModel', 'Order2in');
		$this->load->model('PaymentServiceModel', 'Services');

		$Orders = $this->Order2in->getFilteredOrders(array('order2in_user' => $this->user->user_id), 'open');

		/* пейджинг */
		$this->per_page = $this->per_page_o2o;
		$this->init_paging();
		$this->paging_count = count($Orders);

		if ($Orders)
		{
			$Orders = array_slice($Orders, $this->paging_offset, $this->per_page);
		}

		$view = array (
			'Orders2In' => $Orders,
			'Orders2InStatuses'	=> $this->Order2in->getStatuses(),
			'Orders2InFoto' => $this->Order2in->getOrders2InFoto($Orders),
			'services'	=> $this->Services->getInServices(),
			'usd' => ceil($this->Currencies->getRate('USD') * 100) * 0.01,
			'kzt' => ceil($this->Currencies->getCrossRate('KZT') * 100) * 0.01,
			'uah' => ceil($this->Currencies->getCrossRate('UAH') * 100) * 0.01,
			'pager' => $this->get_paging()
		);


		// парсим шаблон
		if ($this->uri->segment(4) == 'ajax')
		{
			$view['selfurl'] = BASEURL.$this->cname.'/';
			$view['viewpath'] = $this->viewpath;
			$this->load->view('/client/ajax/showOpenOrders2In', $view);
		}
		else
		{
			View::showChild('/client/pages/payOrder', $view);
		}
	}

	public function payOrderDirect($order_id)
	{
		try
		{
			if (! is_numeric($order_id))
			{
				throw new Exception('Доступ запрещен.');
			}

			// заказ
			$order = $this->getPrivilegedOrder(
				$order_id,
				'Заказ недоступен.');

			// позволяет ли текущий статус оплату
			$view['payable_statuses'] = $this->Orders->getPayableStatuses($this->user->user_group);

			if ( ! in_array($order->order_status, $view['payable_statuses']))
			{
				throw new Exception('Текущий статус заказа не позволяет его оплатить.');
			}

			// находим местную валюту
			$this->load->model('CurrencyModel', 'Currency');
			$this->load->model('ClientModel', 'Clients');
			$this->load->model('AddressModel', 'Addresses');
			$this->load->model('ManagerModel', 'Managers');

			$currency = $this->Currency->getCurrencyByCountry($order->order_country);
			
			// добавление платежа партнеру
			$payment_manager = new stdClass();
			$payment_manager->payment_from				= $order->order_client;
			$payment_manager->payment_to				= $order->order_manager;
			$payment_manager->payment_amount_from		= $order->order_cost;
			$payment_manager->payment_amount_to			= 
				$order->order_products_cost +
				$order->order_delivery_cost + 
				$order->order_manager_comission;
			$payment_manager->payment_amount_tax		= $order->order_manager_comission;
			$payment_manager->payment_tax				= $order->order_comission;
			$payment_manager->payment_purpose			= 'оплата заказа';
			$payment_manager->payment_comment			= '№ '.$order->order_id;
			$payment_manager->payment_type				= 'order';
			$payment_manager->payment_transfer_order_id	= $this->user->user_id.date('Y').date('m').date('d').date('h').date('i').date('s');
			
			// добавление платежа партнеру в местной валюте
			$payment_manager_local = new stdClass();
			$payment_manager_local->payment_from		= $order->order_client;
			$payment_manager_local->payment_to			= $order->order_manager;
			$payment_manager_local->payment_amount_from	= 0;
			$payment_manager_local->payment_amount_to	= $order->order_manager_cost_local;
			$payment_manager_local->payment_amount_tax	= $order->order_manager_comission_local;
			$payment_manager_local->payment_tax			= 0;
			$payment_manager_local->payment_purpose		= 'оплата заказа в местной валюте';
			$payment_manager_local->payment_comment		= '№ '.$order->order_id;
			$payment_manager_local->payment_type		= 'order';
			$payment_manager_local->payment_currency	= $currency->currency_symbol;
			$payment_manager_local->payment_transfer_order_id	= '';
			
			// добавление платежа системе
			$payment_system = new stdClass();
			$payment_system->payment_from				= $order->order_client;
			$payment_system->payment_to					= 1;
			$payment_system->payment_amount_from		= 0;
			$payment_system->payment_amount_to			= $order->order_system_comission;
			$payment_system->payment_amount_tax			= $order->order_system_comission;
			$payment_system->payment_purpose			= 'комиссия системы за оплату заказа';
			$payment_system->payment_comment			= '№ '.$order->order_id;
			$payment_system->payment_type				= 'order';
			$payment_system->payment_transfer_order_id	= '';
			
			$this->load->model('PaymentModel', 'Payment');
			
			// погнали
			$this->db->trans_begin();

			if (!$this->Payment->makePayment($payment_manager, true) ||
				!$this->Payment->makePayment($payment_system, true) ||
				!$this->Payment->makePaymentLocal($payment_manager_local, true)) 
			{
				throw new Exception('Ошибка оплаты заказа. Попробуйте еще раз.');
			}			
			
			// ставим статус оплачен заказу
			// и сохраняем детали платежа для последующей доплаты или возврата средств
			$order->order_status = 'payed';
			$order->order_cost_payed = $order->order_cost;
			$order->order_manager_comission_payed = $order->order_manager_comission;
			$order->order_system_comission_payed = $order->order_system_comission;
			
			$order->order_manager_cost_payed_local = $order->order_manager_cost_local;
			$order->order_manager_comission_payed_local = $order->order_manager_comission_local;
			
			$this->Orders->saveOrder($order);
			
			if ($this->db->trans_status() !== FALSE)
			{
				$this->db->trans_commit();
			}
			
			$this->session->set_userdata(array('user_coints' => $this->user->user_coints - $order->order_cost));
			$this->result->m = 'Заказ успешно оплачен.';

			// уведомления
			$this->load->model('UserModel', 'Users');
/*
			Mailer::sendAdminNotification(
				Mailer::SUBJECT_NEW_ORDER_STATUS, 
				Mailer::NEW_ORDER_STATUS_NOTIFICATION,
				0,
				$order->order_id, 
				0,
				"http://countrypost.ru/admin/showPayedOrders/{$order->order_id}",
				null,
				null,
				'Оплачен');

			Mailer::sendManagerNotification(
				Mailer::SUBJECT_NEW_ORDER_STATUS, 
				Mailer::NEW_ORDER_STATUS_NOTIFICATION,
				$order->order_manager, 
				$order->order_id, 
				0,
				"http://countrypost.ru/manager/showPayedOrders/{$order->order_id}",
				$this->Managers,
				null,
				'Оплачен');

			Mailer::sendClientNotification(
				Mailer::SUBJECT_NEW_ORDER_STATUS, 
				Mailer::NEW_ORDER_STATUS_NOTIFICATION,
				$order->order_id, 
				$order->order_client, 
				"http://countrypost.ru/client/showOpenOrders/{$order->order_id}",
				$this->Clients,
				'Оплачен');
*/
		}
		catch (Exception $e)
		{
			$this->db->trans_rollback();
		
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
		}
		
		// открываем детали заказа
		if (isset($order_id))
		{
			Func::redirect(BASEURL . $this->cname . '/order/' . $order_id);
		}
		else
		{
			Func::redirect(BASEURL . $this->cname);
		}
	}
	
	// доплата за новые товары в заказе
	public function repayOrder()
	{
		try
		{
			if (!$this->user ||
				!$this->user->user_id ||
				!is_numeric($this->uri->segment(3)))
			{
				throw new Exception('Доступ запрещен.');
			}
			
			// безопасность: проверяем связку клиента и заказа
			$this->load->model('OrderModel', 'Orders');
			$order = $this->Orders->getClientOrderById($this->uri->segment(3), $this->user->user_id);

			if (!$order)
			{
				throw new Exception('Заказ не найден. Попробуйте еще раз.');
			}			
			else if ($order->order_status != 'payed')
			{
				throw new Exception('Невозможно доплатить за неоплаченный заказ.');
			}			

			// находим местную валюту
			$this->load->model('CurrencyModel', 'Currency');
			$currency = $this->Currency->getCurrencyByCountry($order->order_country);
			
			// добавление платежа партнеру
			$payment_manager = new stdClass();
			$payment_manager->payment_from				= $order->order_client;
			$payment_manager->payment_to				= $order->order_manager;
			$payment_manager->payment_amount_from		= $order->order_cost - $order->order_cost_payed;
			$payment_manager->payment_amount_to			= 
				$order->order_manager_cost 
				- $order->order_cost_payed 
				+ $order->order_system_comission_payed;
			$payment_manager->payment_amount_tax		= 
				$order->order_manager_comission
				- $order->order_manager_comission_payed;
			$payment_manager->payment_tax				= 
				$order->order_comission
				- $order->order_comission;
			$payment_manager->payment_purpose			= 'доплата заказа';
			$payment_manager->payment_comment			= '№ '.$order->order_id;
			$payment_manager->payment_type				= 'order';
			$payment_manager->payment_transfer_order_id	= $this->user->user_id.date('Y').date('m').date('d').date('h').date('i').date('s');
			
			// добавление платежа партнеру в местной валюте
			$payment_manager_local = new stdClass();
			$add_local_money = ($order->order_manager_cost_local - $order->order_manager_cost_payed_local >= 0);
			
			if ($add_local_money)
			{
				$payment_manager_local->payment_from		= $order->order_client;
				$payment_manager_local->payment_to			= $order->order_manager;
				$payment_manager_local->payment_amount_from	= 0;
				$payment_manager_local->payment_amount_to	= $order->order_manager_cost_local - $order->order_manager_cost_payed_local;
				$payment_manager_local->payment_amount_tax	= $order->order_manager_comission_local - $order->order_manager_comission_payed_local;
			}
			else
			{
				$payment_manager_local->payment_from		= $order->order_manager;
				$payment_manager_local->payment_to			= $order->order_client;
				$payment_manager_local->payment_amount_from	= $order->order_manager_cost_payed_local - $order->order_manager_cost_local;
				$payment_manager_local->payment_amount_to	= 0;
				$payment_manager_local->payment_amount_tax	= $order->order_manager_comission_payed_local - $order->order_manager_comission_local;
			}
			
			$payment_manager_local->payment_purpose		= 'доплата заказа в местной валюте';
			$payment_manager_local->payment_comment		= '№ '.$order->order_id;
			$payment_manager_local->payment_type		= 'order';
			$payment_manager_local->payment_currency	= $currency->currency_symbol;
			$payment_manager_local->payment_transfer_order_id	= '';
			
			// добавление платежа системе
			$payment_system = new stdClass();
			$payment_system->payment_from				= $order->order_client;
			$payment_system->payment_to					= 1;
			$payment_system->payment_amount_from		= 0;
			$payment_system->payment_amount_to			= 
				$order->order_system_comission
				- $order->order_system_comission;
			$payment_system->payment_amount_tax			= 
				$order->order_system_comission
				- $order->order_system_comission_payed;
			$payment_system->payment_purpose			= 'доплата комиссии системы за заказ';
			$payment_system->payment_comment			= '№ '.$order->order_id;
			$payment_system->payment_type				= 'order';
			$payment_system->payment_transfer_order_id	= '';
			
			$this->load->model('PaymentModel', 'Payment');
			
			// погнали
			$this->db->trans_begin();

			if (!$this->Payment->makePayment($payment_manager, true) ||
				!$this->Payment->makePayment($payment_system, true) ||
				!$this->Payment->makePaymentLocal($payment_manager_local, true)) 
			{
				throw new Exception('Ошибка доплаты за заказ. Попробуйте еще раз.');
			}			
			
			// ставим статус оплачен заказу
			$order->order_status = 'payed';
			$order->order_cost_payed = $order->order_cost;
			$order->order_manager_comission_payed = $order->order_manager_comission;
			$order->order_system_comission_payed = $order->order_system_comission;
			
			$order->order_manager_cost_payed_local = $order->order_manager_cost_local;
			$order->order_manager_comission_payed_local = $order->order_manager_comission_local;

			$this->Orders->saveOrder($order);
			
			if ($this->db->trans_status() !== FALSE)
			{
				$this->db->trans_commit();
			}
			
			$this->session->set_userdata(array('user_coints' => $this->user->user_coints - $order->order_cost + $order->order_cost_payed));
			$this->result->m = 'Заказ успешно оплачен.';
		}
		catch (Exception $e)
		{
			$this->db->trans_rollback();
		
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
		}
		
		// открываем заказы
		Stack::push('result', $this->result);
		Func::redirect(BASEURL.$this->cname.'/showOpenOrders');
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
	
	public function showPayedOutMoney()
	{
		$this->load->model('Order2outModel', 'Order2out');
		$this->load->model('CurrencyModel', 'Currencies');
		$this->load->model('PaymentServiceModel', 'Services');

		$Orders = $this->Order2out->getPayedUserOrders($this->user->user_id);
		
		/* пейджинг */
		$this->per_page = $this->per_page_o2o;
		$this->init_paging();		
		$this->paging_count = count($Orders);
		
		if ($Orders)
		{
			$Orders = array_slice($Orders, $this->paging_offset, $this->per_page);
		}
		
		$view = array (
			'Orders' => $Orders,
			'statuses'	=> $this->Order2out->getStatuses(),
			'services'	=> $this->Services->getOutServices(),
			'usd' => (float) ceil($this->Currencies->getRate('USD') * 100) / 100,
			'pager' => $this->get_paging()
		);
		
		// парсим шаблон
		if ($this->uri->segment(4) == 'ajax')
		{
        	$view['selfurl'] = BASEURL.$this->cname.'/';
			$view['viewpath'] = $this->viewpath;
			$this->load->view($this->viewpath."ajax/showPayedOutMoney", $view);
		}
		else
		{
			View::showChild($this->viewpath."pages/showPayedOutMoney", $view);
		}
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

	public function addBidComment($bid_id, $comment_id = null)
	{
		parent::addBidComment($bid_id, $comment_id);
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

			$this->load->view("/client/ajax/showOrderInfoAjax", $view);
		}
		catch (Exception $e)
		{
			//echo $e->getMessage();
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

	// BOF: перенаправление обработчиков в базовый контроллер
	public function showO2oComments()
	{
		parent::showO2oComments();
	}

	public function addO2oComment()
	{
		parent::addO2oComment();
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

	public function showO2iComments()
	{
		parent::showO2iComments();
	}

	public function addOrderComment($order_id, $comment_id = null)
	{
		parent::addOrderComment($order_id, $comment_id);
	}

	public function updateProductAjax()
	{
		parent::updateProductAjax();
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
	// EOF: перенаправление обработчиков в базовый контроллер
}