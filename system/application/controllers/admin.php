<?php
require_once BASE_CONTROLLERS_PATH.'AdminBaseController'.EXT;

class Admin extends AdminBaseController {

	function __construct()
	{
		parent::__construct();

		Breadcrumb::setCrumb(array('/' => 'Главная'), 0, TRUE);
	}
	
	function index()
	{
		Func::redirect(BASEURL);
	}
	
	public function autocompleteClient($query)
	{
		$this->load->model('ClientModel', 'Clients');
		$ids = $this->Clients->autocomplete(intval($query));
		
		if ($ids)
		{
			echo "[$ids]";
		}
		else
		{
			echo '[]';
		}
	}
	
	public function autocompletePackage($query)
	{
		$this->load->model('PackageModel', 'Package');
		$ids = $this->Package->autocomplete(intval($query));
		
		if ($ids)
		{
			echo "[$ids]";
		}
		else
		{
			echo '[]';
		}
	}
	
	public function showPaymentHistory()
	{
		$this->load->model('PaymentModel', 'Payments');
		$view['filter'] = $this->initFilter('paymentHistory');
		$view['Payments'] = $this->Payments->getFilteredPayments($view['filter']->condition, $view['filter']->from, $view['filter']->to);

		/* пейджинг */
		$this->init_paging();		
		$this->paging_count = count($view['Payments']);
		$per_page = isset($this->session->userdata['payments_per_page']) ? $this->session->userdata['payments_per_page'] : $this->per_page;
		$this->per_page = $per_page;
		$view['per_page'] = $per_page;
	
		if (isset($view['Payments']) && $view['Payments'])
		{
			$view['Payments'] = array_slice($view['Payments'], $this->paging_offset, $this->per_page);
		}			
			
		$view['pager'] = $this->get_paging();

		// собираем платежные системы
		$this->load->model('PaymentServiceModel', 'Services');
		$view['services'] = $this->Services->getList();		
		
		// парсим шаблон
		if ($this->uri->segment(4) == 'ajax')
		{
        	$view['selfurl'] = BASEURL.$this->cname.'/';
			$view['viewpath'] = $this->viewpath;
			$this->load->view($this->viewpath."ajax/showPaymentHistory", $view);
		}
		else
		{
			View::showChild($this->viewpath.'pages/showPaymentHistory', $view);
		}
	}
	
	public function extraPayments()
	{
		$this->load->model('ManagerModel', 'Managers');
		$view['managers'] = $this->Managers->getManagersData();

		$this->load->model('ExtraPaymentModel', 'ExtraPayments');
		$view['payments'] = $this->ExtraPayments->getList();

		/* пейджинг */
		$this->init_paging();		
		$this->paging_count = count($view['payments']);
	
		if (isset($view['payments']) && $view['payments'])
		{
			$view['payments'] = array_slice($view['payments'], $this->paging_offset, $this->per_page);
		}			
			
		$view['pager'] = $this->get_paging();

		// парсим шаблон
		if ($this->uri->segment(4) == 'ajax')
		{
        	$view['selfurl'] = BASEURL.$this->cname.'/';
			$view['viewpath'] = $this->viewpath;
			$this->load->view($this->viewpath."ajax/showExtraPayments", $view);
		}
		else
		{
			View::showChild($this->viewpath.'/pages/extraPayments', $view);
		}
	}
	
	public function showClientOrdersToOut()
	{
		$this->load->model('Order2outModel', 'Order2out');
		$this->load->model('PaymentServiceModel', 'Services');

		$filter = $this->initFilter('openClientO2o');

		$Orders = $this->Order2out->getClientFilteredOrders($filter);
		
		/* пейджинг */
		$this->init_paging();		
		$this->paging_count = count($Orders);
	
		if ($Orders)
		{
			$Orders = array_slice($Orders, $this->paging_offset, $this->per_page);
		}			
			
		$view = array(
			'Orders'	=> $Orders,
			'statuses'	=> $this->Order2out->getStatuses(),
			'services'	=> $this->Services->getOutServices(),
			'filter'	=> $filter,
			'pager'		=> $this->get_paging()
		);
		
		// парсим шаблон
		if ($this->uri->segment(4) == 'ajax')
		{
        	$view['selfurl'] = BASEURL.$this->cname.'/';
			$view['viewpath'] = $this->viewpath;
			$this->load->view($this->viewpath."ajax/showClientOrdersToOut", $view);
		}
		else
		{
			View::showChild($this->viewpath.'/pages/showClientOrdersToOut', $view);
		}
	}
	
	public function showClientPayedOrdersToOut()
	{
		$this->load->model('Order2outModel', 'Order2out');
		$this->load->model('PaymentServiceModel', 'Services');

		$filter = $this->initFilter('payedClientO2o');

		$Orders = $this->Order2out->getClientFilteredOrders($filter);
		
		/* пейджинг */
		$this->init_paging();		
		$this->paging_count = count($Orders);
	
		if ($Orders)
		{
			$Orders = array_slice($Orders, $this->paging_offset, $this->per_page);
		}			
			
		$view = array(
			'Orders'	=> $Orders,
			'statuses'	=> $this->Order2out->getStatuses(),
			'services'	=> $this->Services->getOutServices(),
			'filter'	=> $filter,
			'pager'		=> $this->get_paging()
		);
		
		// парсим шаблон
		if ($this->uri->segment(4) == 'ajax')
		{
        	$view['selfurl'] = BASEURL.$this->cname.'/';
			$view['viewpath'] = $this->viewpath;
			$this->load->view($this->viewpath."ajax/showClientPayedOrdersToOut", $view);
		}
		else
		{
			View::showChild($this->viewpath.'/pages/showClientPayedOrdersToOut', $view);
		}
	}
	
	public function showManagerOrdersToOut()
	{
		$this->load->model('Order2outModel', 'Order2out');
		$this->load->model('PaymentServiceModel', 'Services');

		$filter = $this->initFilter('openManagerO2o');

		$Orders = $this->Order2out->getManagerFilteredOrders($filter);
		
		/* пейджинг */
		$this->init_paging();		
		$this->paging_count = count($Orders);
	
		if ($Orders)
		{
			$Orders = array_slice($Orders, $this->paging_offset, $this->per_page);
		}			
			
		$view = array(
			'Orders'	=> $Orders,
			'statuses'	=> $this->Order2out->getStatuses(),
			'services'	=> $this->Services->getOutServices(),
			'filter'	=> $filter,
			'pager'		=> $this->get_paging()
		);
		
		// парсим шаблон
		if ($this->uri->segment(4) == 'ajax')
		{
        	$view['selfurl'] = BASEURL.$this->cname.'/';
			$view['viewpath'] = $this->viewpath;
			$this->load->view($this->viewpath."ajax/showManagerOrdersToOut", $view);
		}
		else
		{
			View::showChild($this->viewpath.'/pages/showManagerOrdersToOut', $view);
		}
	}
	
	public function showManagerPayedOrdersToOut()
	{
		$this->load->model('Order2outModel', 'Order2out');
		$this->load->model('PaymentServiceModel', 'Services');

		$filter = $this->initFilter('payedManagerO2o');

		$Orders = $this->Order2out->getManagerFilteredOrders($filter);
		
		/* пейджинг */
		$this->init_paging();		
		$this->paging_count = count($Orders);
	
		if ($Orders)
		{
			$Orders = array_slice($Orders, $this->paging_offset, $this->per_page);
		}			
			
		$view = array(
			'Orders'	=> $Orders,
			'statuses'	=> $this->Order2out->getStatuses(),
			'services'	=> $this->Services->getOutServices(),
			'filter'	=> $filter,
			'pager'		=> $this->get_paging()
		);
		
		// парсим шаблон
		if ($this->uri->segment(4) == 'ajax')
		{
        	$view['selfurl'] = BASEURL.$this->cname.'/';
			$view['viewpath'] = $this->viewpath;
			$this->load->view($this->viewpath."ajax/showManagerPayedOrdersToOut", $view);
		}
		else
		{
			View::showChild($this->viewpath.'/pages/showManagerPayedOrdersToOut', $view);
		}
	}
	
	public function searchOrders2out() 
	{
		$this->load->model('Order2outModel', 'Order2out');
		$Orders = $this->Order2out->getClientFilteredOrders(array(@$_POST['sfield'] => @$_POST['svalue']));
		
		$view = array(
			'Orders'	=> $Orders,
			'statuses'	=> $this->Order2out->getStatuses(),
			'status'	=> 'none'
		);
		
		View::showChild($this->viewpath.'/pages/order_to_out', $view);
	}
	
	public function saveOrders2out($page) 
	{
		$ids = Check::idsByFilter('status_');
		
		// ищем заявки с такими id
		if (count($ids)) {
			$this->load->model('Order2outModel', 'Order2out');
			$Orders = $this->Order2out->getOrdersByIds($ids);
			
			$updated = 0;
			
			foreach ($Orders as $Order) 
			{
				if ($Order->order2out_status != $_POST['status_'.$Order->order2out_id]) 
				{
					$this->Order2out->_set('order2out_status', $_POST['status_'.$Order->order2out_id]);
					$this->Order2out->_set('order2out_id', $Order->order2out_id);
					if ($this->Order2out->save())
						$updated++;	
				}
			}
			
			if($updated) {
				$this->result->r = 1;
				$this->result->m = 'Заявок успешно обновлено: '.$updated;		
				Stack::push('result', $this->result);
			}
			
		}
		Func::redirect(BASEURL.$this->cname."/$page");
	}
	
	public function saveOrders2in($page) 
	{
		$ids = Check::idsByFilter('status_');
		$updated = 0;
		$this->load->model('Order2InModel', 'Order2in');
		$this->load->model('PaymentModel', 'Payment');
	
		if (count($ids)) 
		{
			$Orders = $this->Order2in->getOrdersByIds($ids);
			
			// поддержка старой логики
			if (empty($Order->order2in_amount_local))
			{
				if ( ! empty($Order->order2in_amount_rur))
				{
					$Order->order2in_amount_local = $Order->order2in_amount_rur;
				}
				else if ( ! empty($Order->order2in_amount_kzt))
				{
					$Order->order2in_amount_local = $Order->order2in_amount_kzt;
				}
			}
			
			foreach ($Orders as $Order) 
			{
				$new_amount = isset($_POST['amount_'.$Order->order2in_id]) ? 
					$_POST['amount_'.$Order->order2in_id] : 
					$Order->order2in_amount;
				$new_amount_local = isset($_POST['amount_local_'.$Order->order2in_id]) ?
					$_POST['amount_local_'.$Order->order2in_id] :
					$Order->order2in_amount_local;
				$new_status = $_POST['status_'.$Order->order2in_id];
				$changed = false;
				
				// ищем заявки с измененной суммой
				if ($Order->order2in_amount != $new_amount) 
				{
					$Order->order2in_amount = $new_amount;
					$Order->order2in_amount_local = $new_amount_local;
					$changed = true;
				}			
				
				// ищем заявки с измененным статусом
				if ($Order->order2in_status != $new_status) 
				{
					if ($new_status == "payed")
					{
						$payment_obj = new stdClass();
						$payment_obj->payment_amount_tax	= $Order->order2in_tax;
						$payment_obj->payment_comment		= '№ '.$Order->order2in_id;
						$payment_obj->payment_amount_rur	= $Order->order2in_amount_local;
						$payment_obj->payment_service_id	= $Order->order2in_payment_service;
						$payment_obj->payment_type			= 'in';
						$payment_obj->payment_amount_from	= $Order->order2in_amount;
						$payment_obj->payment_amount_to		= $Order->order2in_amount;
								
						if ($new_status == "payed")
						{
							$payment_obj->payment_from			= $Order->order2in_details;
							$payment_obj->payment_to			= $Order->order2in_user;
							$payment_obj->payment_purpose		= 'заявка на ввод';
						}
						else
						{
							$payment_obj->payment_from			= $Order->order2in_user;
							$payment_obj->payment_to			= $Order->order2in_details;
							$payment_obj->payment_purpose		= 'отмена заявки на ввод';
						}
						
						if ( ! $this->Payment->makePayment($payment_obj)) 
						{
							throw new Exception('Ошибка зачисления/списания денег со счета клиента. Попробуйте еще раз.');
						}
					}
					
					$Order->order2in_status = $new_status;
					$changed = true;
				}
				
				if ($changed && $this->Order2in->addOrder($Order))
				{
					$updated++;	
				}
			}
		}

		if ($updated) 
		{
			$this->result->r = 1;
			$this->result->m = 'Заявок успешно обновлено: '.$updated;		
			Stack::push('result', $this->result);
		}

		Func::redirect(BASEURL."syspay/$page");
	}
	
	public function deleteOrder2out($oid) 
	{
		parent::deleteOrder2out($oid);
	}
	
	#---------------------------------------------------------------------------
	#
	#	News
	#
	#---------------------------------------------------------------------------
	public function showEditNews()
	{
		if ( ! isset($this->News))
			$this->load->model('NewsModel', 'News');
		
		$news	= $this->News->select(null, 10);
				
		if ( ! $news) $news = array();
			
		View::showChild($this->viewpath.'/pages/news', array('news'=> $news));
	}
	
	public function saveNews(){
		Check::reset_empties();
		$title	= Check::txt('title',	8096,1);
		$body	= Check::txt('body',	8096,1);
		
		
		// fild all fields
		if ( ! Check::get_empties()){
			$this->load->model('NewsModel', 'News');
			$this->News->_set('news_title', $title);
			$this->News->_set('news_body', $body);
			
			$id		= Check::int('id');
			if ($id) 
				$this->News->_set('news_id',	$id);			
			
			if ( ! $this->News->save()){
				$this->result->e	= -1;
				$this->result->m	= 'Невозожно добавить запись.';
			}else{
				$this->result->e	= 1;
				$this->result->m	= 'Запись успешно добавлна.';
			}
		}else{
			$this->result->e	= -1;
			$this->result->m	= 'Невозожно добавить запись. Возможно незаполнено одно или несколько полей.';
		}
		
		Stack::push('result', $this->result);
		
		Func::redirect(BASEURL.$this->cname.'/showEditNews');
		
	}
	
	public function deleteNews($news_id){
		
		$id	= Check::var_int($news_id);
		
		if ($id){
			$this->load->model('NewsModel', 'News');
			if ($this->News->delete($id)){
				$this->result->e	= 1;
				$this->result->m	= 'Запись успешно удалена';
			}else{
				$this->result->e	= -1;
				$this->result->m	= "Не существует записи с указанным ID($id) или запись не может быть удалена.";
			}
		}else{
			$this->result->e		= -2;
			$this->result->m		= "Не корректный ID($news_id).";
		}
		
		Stack::push('result',$this->result);
		
		Func::redirect(BASEURL.$this->cname.'/showEditNews');		

	}
	
	#---------------------------------------------------------------------------
	#
	#	FAQ
	#
	#---------------------------------------------------------------------------
	public function showEditFAQ()
	{
		$this->load->model('FaqModel', 'Faq');
		$this->load->model('FaqSectionModel', 'FaqSections');
			
		$faq_sections = $this->FaqSections->getList();
		
		foreach ($faq_sections as $faq_section)
		{
			$faq_section->questions = $this->Faq->getBySectionId($faq_section->faq_section_id);

		}
		
		////////////
		
		$faq	= $this->Faq->select(null, 10);
		if ( ! $faq) $faq = array();

		View::showChild($this->viewpath.'/pages/faq', array('faq'=> $faq, 'faq_sections' => $faq_sections));
	}
	
	public function saveFaq()
	{
		Check::reset_empties();
		$question	= Check::txt('question',	8096,1);
		$answer		= Check::txt('answer',		8096,1);
		$section	= Check::int('section');
		
		// fild all fields
		if ( ! Check::get_empties()){
			$this->load->model('FaqModel', 'Faq');
			
			$id	= Check::int('id');
			
			if ($id) 
			{
				$this->Faq->_set('faq_id',			$id);
			}
			
			$this->Faq->_set('faq_question',	$question);
			$this->Faq->_set('faq_answer',		$answer);
			$this->Faq->_set('faq_section_id',	$section);
			
			if ( ! $this->Faq->save()){
				$this->result->e	= -1;
				$this->result->m	= 'Невозожно добавить запись.';
			}else{
				$this->result->e	= 1;
				$this->result->m	= 'Запись успешно добавлна.';
			}
		}else{
			$this->result->e	= -1;
			$this->result->m	= 'Невозожно добавить запись. Возможно, одно или несколько полей не заполнены.';
		}
		
		Stack::push('result', $this->result);
		
		Func::redirect(BASEURL.$this->cname.'/showEditFaq');
		
	}
	
	public function addFaqSection()
	{
		Check::reset_empties();
		$section = Check::txt('faq_section_name', 255, 1);
		
		// fild all fields
		if ( ! Check::get_empties())
		{
			$this->load->model('FaqSectionModel', 'Section');
			$this->Section->_set('faq_section_name', $section);
			
			if ( ! $this->Section->save())
			{
				$this->result->e = -1;
				$this->result->m = 'Невозожно добавить запись.';
			}
			else
			{
				$this->result->e = 1;
				$this->result->m = 'Запись успешно добавлна.';
			}
		}
		else
		{
			$this->result->e = -1;
			$this->result->m = 'Невозожно добавить запись. Возможно, одно или несколько полей не заполнены.';
		}
		
		Stack::push('result', $this->result);		
		Func::redirect(BASEURL.$this->cname.'/showEditFaq');		
	}
	
	public function deleteFaq($faq_id){
		
		$id	= Check::var_int($faq_id);
		
		if ($id){
			$this->load->model('FaqModel', 'Faq');
			if ($this->Faq->delete($id)){
				$this->result->e	= 1;
				$this->result->m	= 'Запись успешно удалена';
			}else{
				$this->result->e	= -1;
				$this->result->m	= "Не существует записи с указанным ID($id) или запись не может быть удалена.";
			}
		}else{
			$this->result->e		= -2;
			$this->result->m		= "Не корректный ID($faq_id).";
		}
		
		Stack::push('result',$this->result);
		
		Func::redirect(BASEURL.$this->cname.'/showEditFaq');

	}
	
	#---------------------------------------------------------------------------
	#
	#	Tariffs
	#
	#---------------------------------------------------------------------------
	public function showEditServicesPrice()
	{
		$this->load->model("ConfigModel", "Config");
		$this->load->model("TaxModel", "Taxes");
		$this->load->model("CountryPricelistModel", "CountryPricelist");
		
		View::showChild($this->viewpath.'/pages/services', array(
			'config'=> $this->Config->getConfig(),
			'taxes'	=> $this->Taxes->getTaxes(),
			'country_pricelist'	=> $this->CountryPricelist->getList()
		));
	}
	
	public function saveServicesPrice()
	{
		$this->load->model("ConfigModel", "ConfigModel");
		
		$conf = array(
			'min_USD_rate' => Check::txt('min_USD_rate', 11, 1),
			'min_EUR_rate' => Check::txt('min_EUR_rate', 11, 1),
			'min_CNY_rate' => Check::txt('min_CNY_rate', 11, 1),
			'min_JPY_rate' => Check::txt('min_JPY_rate', 11, 1),
			'min_TRY_rate' => Check::txt('min_TRY_rate', 11, 1),
			'min_UAH_rate' => Check::txt('min_UAH_rate', 11, 1),
			'min_KZT_rate' => Check::txt('min_KZT_rate', 11, 1),
			'min_KRW_rate' => Check::txt('min_KRW_rate', 11, 1)						
		);
		
		$this->result->d = array();
		
		// сохраняем конфиг
		foreach ($conf as $key => $value)
		{
			if ( ! $this->ConfigModel->setConfig($key, $value))
			{
				$this->result->e		= -1;
				$this->result->m		= 'Can`t save one or more options!';
				$this->result->d[$key]	= $value;
			}
			else
			{
				$this->result->m		= SAVE_SUCCESS;
			}
		}
		
		// сохраняем тарифы по странам
		$this->load->model("TaxModel", "Taxes");
		$taxes = $this->Taxes->getList();
		
		foreach ($taxes as $tax)
		{
			$tax->package				= Check::float('package'.$tax->country_id, 0);
			$tax->package_disconnected	= Check::float('package_disconnected'.$tax->country_id, 0);
			$tax->order					= Check::float('order'.$tax->country_id, 0);
			$tax->package_joint			= Check::float('package_joint'.$tax->country_id, 0);
			$tax->package_declaration	= Check::float('package_declaration'.$tax->country_id, 0);
			$tax->package_insurance		= Check::float('package_insurance'.$tax->country_id, 0);
			$tax->min_order				= Check::float('min_order'.$tax->country_id, 0);
			$tax->max_package_insurance = Check::float('max_package_insurance'.$tax->country_id, 0);
			$tax->package_foto			= Check::float('package_foto'.$tax->country_id, 0);
			$tax->package_foto_system	= Check::float('package_foto_system'.$tax->country_id, 0);
						
			$this->Taxes->saveTax($tax);
			//print_r($tax);die();
		}
		
		// сохраняем наши тарифы
		$country_id = Check::int('country_id');
		
		$this->load->model("CountryPricelistModel", "CountryPricelist");
		$pricelist = $this->CountryPricelist->getById($country_id);
		
		$pricelist->country_id = $country_id;		
		$pricelist->description = Check::str('country_pricelist', 1000000);
		
		$this->CountryPricelist->saveCountryPricelist($pricelist);
		
		Stack::push('result',$this->result);
		Func::redirect(BASEURL.$this->cname.'/showEditServicesPrice');
	}

	#---------------------------------------------------------------------------
	#
	#	Partners
	#
	#---------------------------------------------------------------------------
	public function showPartners($operation=null, $uid=null) 
	{
		try
		{
			$this->load->model('ManagerModel', 'Manager');
			$managers = $this->Manager->getManagersData();
			
			/* пейджинг */
			$this->init_paging();		
			$this->paging_count = count($managers);
		
			if ($managers)
			{
				$managers = array_slice($managers,$this->paging_offset,$this->per_page);
			}
			
			$this->load->model('CountryModel', 'Country');
			$Countries	= $this->Country->getList();
			$countries = array();
			foreach ($Countries as $Country)
			{
				$countries[$Country->country_id] = $Country->country_name;
			}
			
			$view = array(
				'managers' 	=> $managers,
				'countries'	=> $countries,
				'statuses'	=> $this->Manager->getStatuses(),
				'pager'		=> $this->get_paging()
			);
		
			// парсим шаблон
			if ($this->uri->segment(4) == 'ajax')
			{
				$view['selfurl'] = BASEURL.$this->cname.'/';
				$view['viewpath'] = $this->viewpath;
				$this->load->view($this->viewpath."ajax/showPartners", $view);
			}
			else
			{
				View::showChild($this->viewpath."pages/showPartners", $view);
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
	
	public function showClients() 
	{
		try
		{
			// обработка фильтра
			$view['filter'] = $this->initFilter('clients');
				
			// получаем список клиентов
			$this->load->model('ClientModel', 'Client');
			$view['clients'] = $this->Client->getClients($view['filter']);
			
			if ( ! $view['clients'])
			{
				$this->result->m = 'Клиенты не найдены. Попробуйте еще раз.';
				Stack::push('result', $this->result);
			}
			
			$view['clients_count'] = $this->Client->getClientsCount();

			if ( ! $view['clients_count'])
			{
				throw new Exception('Количество клиентов не определено. Попробуйте еще раз.');
			}

			// получаем список партнеров
			$this->load->model('ManagerModel', 'Managers');
			$view['managers'] = $this->Managers->getManagersData();
		
			if ( ! $view['managers'])
			{
				throw new Exception('Партнеры не найдены. Попробуйте еще раз.');
			}

			/* пейджинг */
			$this->init_paging();		
			$this->paging_count = count($view['clients']);
		
			if ($view['clients'])
			{
				$view['clients'] = array_slice($view['clients'],$this->paging_offset,$this->per_page);
			}
			$view['pager'] = $this->get_paging();
			
			// получаем связку клиентов и партнеров
			$this->load->model('C2mModel', 'C2m');
			
			foreach ($view['clients'] as $client)
			{
				$client->managers = $this->Managers->getClientManagersById($client->client_user);
			}
			
			// получаем список стран
			$this->load->model('CountryModel', 'Country');
			$view['countries']	= $this->Country->getList();
			
			if ( ! $view['countries'])
			{
				throw new Exception('Страны не найдены. Попробуйте еще раз.');
			}

			$view['country_list'] = array();
			foreach ($view['countries'] as $country)
			{
				$view['country_list'][$country->country_id] = $country->country_name;
			}

			// страны для фильтра
			$view['countries']	= $this->Country->getToCountries();
			
			if (empty($view['countries']))
			{
				throw new Exception('Страны не найдены. Попробуйте еще раз.');
			}

			// парсим шаблон
			if ($this->uri->segment(4) == 'ajax')
			{
				$view['selfurl'] = BASEURL.$this->cname.'/';
				$view['viewpath'] = $this->viewpath;
				$this->load->view($this->viewpath."ajax/showClients", $view);
			}
			else
			{
				View::showChild($this->viewpath."pages/showClients", $view);
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
	
	public function showCountries() 
	{
		try
		{
			$this->load->model('CountryModel', 'Country');
			$view['countries'] = $this->Country->getCountriesWithDelivery();
			
			if ( ! $view['countries'])
			{
				throw new Exception('Страны не найдены. Попробуйте еще раз.');
			}
			
			View::showChild($this->viewpath.'/pages/showCountries', $view);
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
			Func::redirect(BASEURL.$this->cname);
		}		
	}
	
	public function deletePartner($uid=null) 
	{
		$this->load->model('UserModel', 'User');
		
		$_u = $this->User->getById((int) $uid);
		if ($_u && $_u->user_group == 'manager'){
			try {
				$this->User->deleteUser($_u);
				
				$this->load->model('ManagerModel', 'Manager');
				$_m = $this->Manager->getById((int) $uid);
				
				//обновляем связки клиент-менеджер			
				if ($_m->manager_status == 1) {					
					$neighbor_managers = $this->Manager->select(array('manager_country' => $_m->manager_country, 'manager_status' => 1));
					$this->load->model('C2mModel', 'C2m');
					if (count($neighbor_managers) == 1) { // партнер единственный в стране, удаляем связки
						$this->C2m->deletePartnerRelations($_m->manager_user);
					}
					else {
						$all_count = $this->C2m->getPartnerClientsCount($uid);
						if ($all_count) {
							$updated_managers_count = count($neighbor_managers)-1;
							$updated_managers = array(); //массив где ключ - id партнера, а значение - кол-во необходимых для обновления связок
							$base = floor($all_count / $updated_managers_count);
							foreach ($neighbor_managers as $neighbor) {
								if ($neighbor->manager_user != $_m->manager_user)
									$updated_managers[$neighbor->manager_user] = $base;
							}
							if ($delta = ($all_count % $updated_managers_count)) {
								foreach ($updated_managers as $key=>$value) {
									if ($delta) {
										$updated_managers[$key] = $value+1;
										$delta--;
									}
								}
							}
							
							// обновляем связки
							foreach ($updated_managers as $key=>$value) {
								$this->C2m->changePartner($_m->manager_user, $key, $value);
								$manager = $this->Manager->fixMaxClientsCount($key);
								
								if ( ! $manager)
								{
									throw new Exception('Невозможно удалить партнера. Попробуйте еще раз.');
								}
							}
						}
					}
				}				
				
				$_m->manager_status = 2;
				$this->Manager->updateManager($_m);
				
				
				$this->result->r = 1;
				$this->result->m = 'Партнер успешно удален';
			} catch (Exception $e){
				$this->result->r = $e->getCode();
				$this->result->m = $e->getMessage();
			}
		}else{
			$this->result->r = -2;
			$this->result->m = 'Партнер не найден';
		}
		
		Stack::push('result', $this->result);
		
		Func::redirect(BASEURL.$this->cname.'/showPartners');
	}
	
	public function deleteClient($uid) 
	{
		try 
		{
			if ( ! $uid ||
				! is_numeric($uid))
			{
				throw new Exception('Доступ запрещен.');
			}
			
			// валидация пользовательского ввода
			$this->load->model('UserModel', 'User');
			$this->db->trans_begin();
			
			$user = $this->User->getById((int)$uid);
		
			if ( ! $user ||
				$user->user_group != 'client')
			{
				throw new Exception('Клиент не найден. Попробуйте еще раз.');
			}
			
			// удаляем клиента
			$user = $this->User->deleteUser($user);
			
			if ( ! $user)
			{
				throw new Exception('Невозможно удалить клиента. Попробуйте еще раз.');
			}
				
			//обновляем связки клиент-менеджер
			$this->load->model('C2mModel', 'C2m');
			$this->C2m->deleteClientRelations($uid);
					
			// коммитим транзакцию
			if ($this->db->trans_status() === FALSE) 
			{
				throw new Exception('Невозможно удалить клиента. Попробуйте еще раз.');
			}
			
			$this->result->m = 'Клиент успешно удален.';
			$this->db->trans_commit();			
		} 
		catch (Exception $e)
		{
			$this->db->trans_rollback();
			
			$this->result->r = $e->getCode();
			$this->result->m = $e->getMessage();
		}
		
		Stack::push('result', $this->result);
		
		Func::redirect(BASEURL.$this->cname.'/showClients');
	}
	
	public function deletePricelistCountries($from, $to) 
	{
		try 
		{
			// валидация пользовательского ввода
			if ( ! $from ||
				! is_numeric($from) ||
				! $to ||
				! is_numeric($to))
			{
				throw new Exception('Доступ запрещен.');
			}
			
			$this->load->model('PricelistModel', 'Pricelist');
			
			// удаление тарифов
			$pricelist = $this->Pricelist->deletePricelistCountries($from, $to);
		
			if ( ! $pricelist)
			{
				throw new Exception('Тариф не удален. Попробуйте еще раз.');
			}
			
			$this->result->m = 'Тариф успешно удален.';
		} 
		catch (Exception $e)
		{
			$this->result->r = $e->getCode();
			$this->result->m = $e->getMessage();
		}
		
		Stack::push('result', $this->result);
		Func::redirect(BASEURL.$this->cname.'/editPricelist');
	}
	
	public function deleteCountry($uid) 
	{
		try 
		{
			if ( ! $uid ||
				! is_numeric($uid))
			{
				throw new Exception('Доступ запрещен.');
			}
			
			// валидация пользовательского ввода
			$this->load->model('CountryModel', 'Country');
			
			$country = $this->Country->getById((int)$uid);
		
			if ( ! $country)
			{
				throw new Exception('Страна не найдена. Попробуйте еще раз.');
			}
			
			// удаляем страну
			$deleted = $this->Country->delete($uid);
				
			if ( ! $deleted)
			{
				throw new Exception('Невозможно удалить страну. Попоробуйте еще раз.');
			}
	
			$this->result->m = 'Страна успешно удалена.';
		} 
		catch (Exception $e)
		{
			$this->db->trans_rollback();
			
			$this->result->r = $e->getCode();
			$this->result->m = $e->getMessage();
		}
		
		Stack::push('result', $this->result);
		
		Func::redirect(BASEURL.$this->cname.'/showCountries');
	}
	
	public function moveClients() 
	{
		try
		{
			// безопасность
			if ( ! isset($_POST['newPartnerId']) ||
				! is_numeric($_POST['newPartnerId']))
			{
				throw new Exception('Доступ запрещен.');
			}
				
			$newPartnerId = $_POST['newPartnerId'];
			
			// находим партнера
			$this->load->model('ManagerModel', 'Manager');
			$manager = $this->Manager->getById($newPartnerId);

			if ( ! $manager)
			{
				throw new Exception('Новый партнер не найден. Попробуйте еще раз.');
			}

			// итерируем по перемещаемым клиентам
			$this->load->model('C2mModel', 'C2M');
			$this->load->model('ClientModel', 'Clients');
			$this->db->trans_start();
			
			foreach($_POST as $key=>$value)
			{
				if (stripos($key, 'move') === 0) 
				{
					// находим клиента
					$client_id = str_ireplace('move', '', $key);
					if ( ! is_numeric($client_id))
					{
						continue;
					}
			
					$client = $this->Clients->getClientById($client_id);

					if ( ! $client)
					{
						throw new Exception('Некоторые клиенты не найдены. Попробуйте еще раз.');
					}
					
					// валидация пользовательского ввода
					$relation = $this->C2M->getC2M($client_id, $manager->manager_user);

					if ($relation)
					{
						throw new Exception('Некоторые клиенты не могут быть перемещены. Новый и старый партнеры совпадают.');
					}
					
					// сохраняем результат
					$relation = $this->C2M->moveClient($client_id, $manager->manager_user);
					
					if ( ! $relation)
					{
						throw new Exception('Некоторые клиенты не могут быть перемещены. Попробуйте еще раз.');
					}
				}
			}
			
			// вычисляем максимальное число клиентов
			$manager = $this->Manager->fixMaxClientsCount($manager->manager_user);
				
			if ( ! $manager)
			{
				throw new Exception('Ошибка вычисления максимального числа клиентов у партнера. Попробуйте еще раз.');
			}

			// коммитим транзакцию
			if ($this->db->trans_status() === FALSE) 
			{
				throw new Exception('Невозможно переместить клиентов. Попробуйте еще раз.');
			}
					
			$this->db->trans_commit();
			$this->result->m = 'Клиенты успешно перемещены.';
		}
		catch (Exception $e)
		{
			$this->db->trans_rollback();
			
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
		}		

		Stack::push('result', $this->result);
		Func::redirect(BASEURL.$this->cname.'/showClients');
	}
	
	public function editClient($uid) 
	{
		try
		{
			// безопасность
			if ( ! is_numeric($uid))
			{
				throw new Exception('Доступ запрещен.');
			}
				
			// пользователь
			$this->load->model('UserModel', 'User');
			$view['client_user'] = $this->User->getById($uid);			

			if ( ! $view['client_user'] || 
				$view['client_user']->user_deleted)
			{
				throw new Exception('Пользователь не найден. Попробуйте еще раз.');
			}
			
			// клиент
			$this->load->model('ClientModel', 'Client');
			$view['client'] = $this->Client->getClientById($uid);			

			if ( ! $view['client'])
			{
				throw new Exception('Клиент не найден. Попробуйте еще раз.');
			}
			
			// страны
			$this->load->model('CountryModel', 'Country');
			$view['countries'] = $this->Country->getList();
			
			if ( ! $view['countries'])
			{
				throw new Exception('Страны не найдены. Попробуйте еще раз.');
			}
		
			// обработка фильтра
			$view['filter'] = $this->initFilter('editClient');
	
			// отображаем посылки и заказы
			$this->load->model('PackageModel', 'Packages');
			$view['packages'] = $this->Packages->getPackages($view['filter'], 'sent', $uid, null);		
			
			$this->load->model('OrderModel', 'Orders');
			$view['orders'] = $this->Orders->getOrders($view['filter'], 'sended', $uid, null);	

			if ( ! $view['packages'])
			{
				$view['packages'] = array();
			}
			
			if ($view['orders'])
			{
				$view['packages'] = array_merge($view['packages'], $view['orders']);
			}
			
			View::showChild($this->viewpath.'pages/editClient', $view);
		}
		catch (Exception $e)
		{
			$result->e	= $e->getCode();			
			$result->m	= $e->getMessage();
			
			Stack::push('result', $this->result);
			Func::redirect(BASEURL.$this->cname);
		}
	}
	
	public function editClientBalance($uid) 
	{
		try
		{
			// безопасность
			if ( ! is_numeric($uid))
			{
				throw new Exception('Доступ запрещен.');
			}
				
			// пользователь
			$this->load->model('UserModel', 'User');
			$view['client_user'] = $this->User->getById($uid);			

			if ( ! $view['client_user'] || 
				$view['client_user']->user_deleted)
			{
				throw new Exception('Пользователь не найден. Попробуйте еще раз.');
			}
			
			// клиент
			$this->load->model('ClientModel', 'Client');
			$view['client'] = $this->Client->getById($uid);			

			if ( ! $view['client'])
			{
				throw new Exception('Клиент не найден. Попробуйте еще раз.');
			}
			
			View::showChild($this->viewpath.'pages/editClientBalance', $view);
		}
		catch (Exception $e)
		{
			$result->e	= $e->getCode();			
			$result->m	= $e->getMessage();
			
			Stack::push('result', $this->result);
			Func::redirect(BASEURL.$this->cname.'/showClients');
		}
	}
	
	public function editCountry($uid) 
	{
		try
		{
			// безопасность
			if ( ! is_numeric($uid))
			{
				throw new Exception('Доступ запрещен.');
			}
				
			// страна
			$this->load->model('CountryModel', 'Country');
			$view['country'] = $this->Country->getById($uid);			

			if ( ! $view['country'])
			{
				throw new Exception('Страна не найдена. Попробуйте еще раз.');
			}
			
			View::showChild($this->viewpath.'pages/editCountry', $view);
		}
		catch (Exception $e)
		{
			$result->e	= $e->getCode();			
			$result->m	= $e->getMessage();
			
			Stack::push('result', $this->result);
			Func::redirect(BASEURL.$this->cname.'/showCountries');
		}
	}
	
	public function updateClient($uid)
	{
		try
		{
			// безопасность
			if ( ! is_numeric($uid))
			{
				throw new Exception('Доступ запрещен.');
			}
				
			// пользователь
			$this->load->model('UserModel', 'User');
			$user = $this->User->getById($uid);			

			if ( ! $user || 
				$user->user_deleted)
			{
				throw new Exception('Пользователь не найден. Попробуйте еще раз.');
			}
			
			// клиент
			$this->load->model('ClientModel', 'Client');
			$client = $this->Client->getClientById($uid);			

			if ( ! $client)
			{
				throw new Exception('Клиент не найден. Попробуйте еще раз.');
			}
			
			// валидация пользовательского ввода
			Check::reset_empties();
			$user->user_login		= Check::str('login',32,1);
			if (isset($_POST['password']) &&
				$_POST['password'])
			{
				$user->user_password = Check::str('password',32,1);
				if (isset($user->user_password))
				{
					$user->user_password = md5($user->user_password);
				}
			}
			$user->user_email			= Check::email(Check::str('email',128,6));
			
			$client->client_name		= Check::latin('name',128,1);
			$client->client_otc			= Check::latin('otc',128,1);
			$client->client_surname		= Check::latin('surname',128,1);
			$client->client_country		= Check::int('country');
			$client->client_index		= Check::int('index');
			$client->client_town		= Check::latin('town',64,1);
			$client->client_address		= Check::latin('address',512,1);
			$client->client_phone		= Check::int('phone');
			$empties					= Check::get_empties();
		
			if ( ! $user->user_email){
				throw new Exception('Не верный E-mail.', -13);
			}			
			
			if ($empties && in_array('_latin', $empties)){
				throw new Exception('Данные должны быть введены латиницей.', -14);
			}
		
			if ($empties){
				throw new Exception('Одно или несколько полей не заполнено.', -11);
			}
			
			// сохранение результата
			$this->db->trans_start();
			$user = $this->User->updateUser($user);
			
			if ( ! $user || 
				$user->user_deleted)
			{
				throw new Exception('Пользователь не сохранен. Попробуйте еще раз.');
			}
			
			$client = $this->Client->updateClient($client);
			
			// коммитим транзакцию
			if ($this->db->trans_status() === FALSE) 
			{
				throw new Exception('Невозможно сохранить данные партнера. Попробуйте еще раз.');
			}
					
			$this->db->trans_commit();
			$this->result->m = 'Клиент успешно сохранен.';
		}
		catch (Exception $e)
		{
			$this->db->trans_rollback();
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
		}
	
		Stack::push('result', $this->result);
		Func::redirect(BASEURL.$this->cname.'/editClient/'.$uid);
	}

	public function updateClientBalance($uid)
	{
		try
		{
			// безопасность
			if ( ! is_numeric($uid))
			{
				throw new Exception('Доступ запрещен.');
			}
				
			// пользователь
			$this->load->model('UserModel', 'User');
			$user = $this->User->getById($uid);			

			if ( ! $user || 
				$user->user_deleted)
			{
				throw new Exception('Пользователь не найден. Попробуйте еще раз.');
			}
			
			// валидация пользовательского ввода
			Check::reset_empties();
			$user->user_coints = Check::int('user_coints');
			$empties = Check::get_empties();
		
			if ($empties){
				throw new Exception('Введите корректный баланс.');
			}
			
			// сохранение результата
			$user = $this->User->updateUser($user);
			
			if ( ! $user || 
				$user->user_deleted)
			{
				throw new Exception('Пользователь не сохранен. Попробуйте еще раз.');
			}
			
			$this->result->m = 'Баланс успешно сохранен.';
		}
		catch (Exception $e)
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
		}
	
		Stack::push('result', $this->result);
		Func::redirect(BASEURL.$this->cname.'/editClientBalance/'.$uid);
	}

	public function updateCountry($uid)
	{
		try
		{
			// безопасность
			if ( ! is_numeric($uid))
			{
				throw new Exception('Доступ запрещен.');
			}
				
			// страна
			$this->load->model('CountryModel', 'Country');
			$country = $this->Country->getById($uid);			

			if ( ! $country)
			{
				throw new Exception('Страна не найдена. Попробуйте еще раз.');
			}
			
			// валидация пользовательского ввода
			Check::reset_empties();
			$country->country_name = Check::str('country_name', 64, 1);
			$empties = Check::get_empties();
		
			if ($empties){
				throw new Exception('Введите корректное название страны.');
			}
			
			// сохранение результата
			$country = $this->Country->saveCountry($country);
			
			/*if ( ! $country)
			{
				throw new Exception('Страна не сохранена. Попробуйте еще раз.');
			}*/
			
			$this->result->m = 'Страна успешно сохранена.';
		}
		catch (Exception $e)
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
		}
	
		Stack::push('result', $this->result);
		Func::redirect(BASEURL.$this->cname.'/editCountry/'.$uid);
	}

	public function updatePartner($uid) 
	{
		try
		{
			// безопасность
			if ( ! is_numeric($uid))
			{
				throw new Exception('Доступ запрещен.');
			}
				
			// находим пользователя
			$this->load->model('UserModel', 'User');
			$user = $this->User->getById((int) $uid);

			if ( ! $user)
			{
				throw new Exception('Пользователь не найден. Попробуйте еще раз.');
			}

			// находим партнера
			$this->load->model('ManagerModel', 'Manager');
			$manager = $this->Manager->getById((int) $uid);

			if ( ! $manager)
			{
				throw new Exception('Партнер не найден. Попробуйте еще раз.');
			}

			$prev_status	= $manager->manager_status;
			$prev_country	= $manager->manager_country;
			
			// валидация пользовательского ввода
			Check::reset_empties();
			$user->user_email = Check::email(Check::str('user_email',128,4));
			
			if (isset($_POST['user_password']) &&
				$_POST['user_password'])
			{
				$user->user_password = Check::str('user_password',32,1);
			
				if (isset($user->user_password))
				{
					$user->user_password = md5($user->user_password);
				}
			}
			
			// старый кредит партнера
			$current_credit = $manager->manager_credit;
			$current_credit_local = $manager->manager_credit_local;
			
			$manager->manager_name			= Check::str('manager_name',128,0);
			$manager->manager_surname		= Check::str('manager_surname',128,0);
			$manager->manager_otc			= Check::str('manager_otc',128,0);
			$manager->manager_country		= Check::int('manager_country');
			$manager->manager_addres		= Check::str('manager_addres',512,1);
			$manager->manager_address_local	= Check::str('manager_address_local',4096,0);
			$manager->manager_phone			= Check::str('manager_phone',1024,1);
			$manager->manager_skype			= Check::str('manager_skype',255,0);
			$manager->manager_max_clients	= Check::int('manager_max_clients');
			$manager->manager_status		= Check::int('manager_status');
			$manager->manager_credit		= Check::float('manager_credit');
			$manager->manager_credit_local	= Check::float('manager_credit_local');
			$manager->manager_description	= Check::str('description',4096,0);
			$manager->package_foto_tax		= Check::chkbox('package_foto_tax');
			$manager->package_foto_system_tax = Check::chkbox('package_foto_system_tax');
			
			if (isset($_POST['order_tax']))
			{
				$manager->order_tax			= Check::float('order_tax');
				$manager->order_tax 		= empty($manager->order_tax) ? 0 : $manager->order_tax;
			}
			else
			{
				unset($manager->order_tax);
			}
			
			if (isset($_POST['package_tax']))
			{
				$manager->package_tax		= Check::float('package_tax');
				$manager->package_tax 		= empty($manager->package_tax) ? 0 : $manager->package_tax;
			}
			else
			{
				unset($manager->package_tax);
			}
			
			if (isset($_POST['package_disconnected_tax']))
			{
				$manager->package_disconnected_tax = Check::float('package_disconnected_tax');
				$manager->package_disconnected_tax = empty($manager->package_disconnected_tax) ? 0 : $manager->package_disconnected_tax;
			}
			else
			{
				unset($manager->package_disconnected_tax);
			}
			
			if ( ! isset($_POST['manager_max_orders']) OR
				! is_numeric($_POST['manager_max_orders']))
			{
				unset($manager->manager_max_orders);
			}
			else
			{
				$manager->manager_max_orders = Check::int('manager_max_orders');
			}
			
			// костыли
			if (empty($manager->manager_name))
			{
				$manager->manager_name = null;
			}
			
			if (empty($manager->manager_surname))
			{
				$manager->manager_surname = null;
			}
			
			if (empty($manager->manager_otc))
			{
				$manager->manager_otc = null;
			}
			
			// меняем дату кредитов
			if ($current_credit != $manager->manager_credit)
			{
				$manager->manager_credit_date	= date("Y-m-d").' 00:00:00';
			}
			if ($current_credit_local != $manager->manager_credit_local)
			{
				$manager->manager_credit_date_local	= date("Y-m-d").' 00:00:00';
			}
			
			$empties = Check::get_empties();			
			
			if ($empties)
			{
				if ( ! $user->user_email)
				{
					throw new Exception('Не верный e-mail. Попробуйте еще раз.', -13);
				}
				else if ( ! in_array('_email',$empties))
				{
					throw new Exception('Одно или несколько полей не заполнено. Попробуйте еще раз.', -11);
				}
			}
			
			$this->db->trans_begin();
					
			// закидываем добавленный кредит на счета партнера, только если они уменьшились
			if ($manager->manager_credit < $current_credit)
			{
				$user->user_coints += $manager->manager_credit - $current_credit;
			}
			if ($manager->manager_credit_local < $current_credit_local)
			{
				$manager->manager_balance_local += $manager->manager_credit_local - $current_credit_local;
			}
			
			// наконец, все сохраняем
			$user = $this->User->updateUser($user);
			$manager = $this->Manager->updateManager($manager);
			
			if ( ! $user || ! $manager)
			{
				throw new Exception('Партнер не сохранен. Попробуйте еще раз.');
			}
			
			// вычисляем максимальное число клиентов
			if ($manager->manager_status == 1)
			{
				$manager = $this->Manager->fixMaxClientsCount($uid);
				
				if ( ! $manager)
				{
					throw new Exception('Ошибка вычисления максимального числа клиентов. Попробуйте еще раз.');
				}
			}			
			
			// сохраняем способы доставки
			$this->load->model('ManagerDeliveryModel', 'Delivery');
			$this->Delivery->clearManagerDelivery($manager->manager_user);
			
			if (isset($_POST['delivery']) && is_array($_POST['delivery']) && !empty($_POST['delivery'])){
				foreach ($_POST['delivery'] as $delivery_id => $delivery_name){
					if (is_numeric($delivery_id))
					{	
						$delivery = new stdClass();
						$delivery->manager_id = $manager->manager_user;
						$delivery->delivery_id = $delivery_id;
						$delivery = $this->Delivery->saveManagerDelivery($delivery);
						if (isset($delivery) && ! $delivery)
						{
							throw new Exception('Неизвестный способ доставки. Попробуйте еще раз.');
						}
					}
				}
			}
			
			// обновляем связки клиент-менеджер когда блочим клиента:
			// раскидываем клиентов менеджера по остальным менеджерам
			if ($prev_status == 1 && $manager->manager_status == 2)
			{
				$neighbour_managers = $this->Manager->select(array('manager_country' => $prev_country, 'manager_status' => 1));
				$this->load->model('C2mModel', 'C2M');
				// партнер единственный в стране, удаляем связки
				if ( ! $neighbour_managers) 
				{ 
					$this->C2M->deletePartnerRelations($manager->manager_user);
				}
				else 
				{
					$all_count = $this->C2M->getPartnerClientsCount($manager->manager_user);
					if ($all_count>0) 
					{
						$updated_managers_count = count($neighbour_managers);
						
						$updated_managers = array(); //массив где ключ - id партнера, а значение - кол-во необходимых для обновления связок
						$base = floor($all_count / $updated_managers_count);
					
						foreach ($neighbour_managers as $neighbor) {
							$updated_managers[$neighbor->manager_user] = $base;
						}
						
						if ($delta = ($all_count % $updated_managers_count)) 
						{
							foreach ($updated_managers as $key=>$value) 
							{
								if ($delta>0) 
								{
									$updated_managers[$key] = $value + 1;
									$delta--;
								}
							}
						}
						
						$mmm = $manager->manager_user;

						// обновляем связки
						foreach ($updated_managers as $key=>$value) 
						{
							
							$this->C2M->changePartner($mmm, $key, $value);
							
							// вычисляем максимальное число клиентов
							$manager = $this->Manager->fixMaxClientsCount($key);
								
							if ( ! $manager)
							{
								throw new Exception('Ошибка переноса клиентов к активным партнерам. Попробуйте еще раз.');
							}
						}
						
					}
				}
			}
			
			// коммитим транзакцию
			if ($this->db->trans_status() === FALSE) 
			{
				throw new Exception('Невозможно сохранить данные партнера. Попробуйте еще раз.');
			}
					
			$this->db->trans_commit();

			$this->result->m = 'Партнер успешно сохранен.';
			Stack::push('result', $this->result);
		}
		catch (Exception $e) 
		{
			$this->db->trans_rollback();
			
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
			// открываем детали партнера
			Func::redirect(BASEURL.$this->cname.'/showPartnerInfo/'.$uid);
			return;
		}
		
		// открываем детали партнера
		Func::redirect(BASEURL.$this->cname.'/showPartners');
	}
	
	public function showAddPartner(){
		$this->showPartnerInfo();
	}
	
	public function showPartnerInfo($partner_id = 0)
	{
		try
		{
			// находим страны
			$this->load->model('CountryModel', 'Country');		
			
			// при регистрации выводятся только те страны, в которые указана цена доставки
			$view['countries']  = $this->Country->getFromCountries();
			
			if ( ! $view['countries'])
			{
				throw new Exception('Страны не найдены. Попробуйте еще раз.');
			}

			// находим статусы
			$this->load->model('ManagerModel', 'Manager');		
			$view['statuses'] = $this->Manager->getStatuses();
				
			if ( ! $view['statuses'])
			{
				throw new Exception('Статусы не найдены. Попробуйте еще раз.');
			}
			
			if ((int)$partner_id)
			{
				$this->load->model('UserModel', 'User');
				$this->load->model('CurrencyModel', 'Currencies');		
				
				$view['manager_user']	= $this->User->getById($partner_id);
				$view['manager']	= $this->Manager->getById($partner_id);
				$view['manager']->currency_symbol = $this->Currencies->getCurrencyByCountry($view['manager']->manager_country)->currency_symbol;
			}

			// находим способы доставки партнера
			$this->load->model('ManagerDeliveryModel', 'MD');		
			$view['deliveries'] = $this->MD->getByManagerId($partner_id);
			
			if ( ! $view['deliveries'])
			{
				throw new Exception('Способы доставки не найдены. Попробуйте еще раз.');
			}
			
			$this->load->model('PackageModel', 'Package');
			$view['packages']	= $this->Package->getByManagerId($partner_id);

			// максимальная комиссия за посылку
			if ((int)$partner_id)
			{
				$this->load->model('CurrencyModel', 'Currencies');
				$this->load->model('TaxModel', 'Taxes');
					
				$country = $this->Country->getById($view['manager']->manager_country);
				$cross_rate = $this->Currencies->getById($country->country_currency);

				$tax = $this->Taxes->getByCountryId($view['manager']->manager_country);

				if ( ! $tax)
				{
					throw new Exception('Невозможно рассчитать максимальную комиссию посылки. Данные для расчета недоступны.');
				}
				
				$view['max_package_tax'] = $tax->package * $cross_rate->cbr_cross_rate;
				$view['max_package_disconnected_tax'] = $tax->package_disconnected * $cross_rate->cbr_cross_rate;
				$view['max_order_tax'] = $tax->order;
			}
			
			View::showChild($this->viewpath.'/pages/showPartnerInfo', $view);
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
			Func::redirect(BASEURL.$this->cname);
		}		
	}
	
	public function addPartner() 
	{
		$countries = '';
		if (Stack::size('all_countries') > 0)
		{
			$countries	= Stack::last('all_countries');
		}
		else
		{
			$this->load->model('CountryModel', 'Country');
			$countries  = $this->Country->getFromCountries();

		}
		
		$countries_ids = array();
		if ($countries) 
		{
			foreach ($countries as $country)
			{
				$countries_ids[] = $country->country_id;
			}
		}
		
		$this->load->model('ManagerModel', 'Manager');
		$statuses = $this->Manager->getStatuses();
		
		// находим способы доставки партнера
		$this->load->model('ManagerDeliveryModel', 'MD');		
		$deliveries = $this->MD->getByManagerId(0);
			
		if ( ! $deliveries)
		{
			throw new Exception('Способы доставки не найдены. Попробуйте еще раз.');
		}

		// валидация пользовательского ввода	
		Check::reset_empties();
		$user							= new stdClass();
		$user->user_login				= Check::str('user_login',32,1);
		$user->user_password			= Check::str('user_password',32,1);
		$user->user_email				= Check::email(Check::str('user_email',128,1));
		$user->user_group				= 'manager';
		$user->user_coints				= Check::float('manager_credit');
		
		$manager						= new stdClass();
		$manager->manager_name			= Check::str('manager_name',128,0);
		$manager->manager_surname		= Check::str('manager_surname',128,0);
		$manager->manager_otc			= Check::str('manager_otc',128,0);
		$manager->manager_country		= Check::int('manager_country');
		$manager->manager_addres		= Check::str('manager_addres',512,1);
		$manager->manager_address_local	= Check::str('manager_address_local',4096,0);
		$manager->manager_phone			= Check::str('manager_phone', 1024, 1);
		$manager->manager_skype			= Check::str('manager_skype', 1024, 0);
		$manager->manager_max_clients	= Check::int('manager_max_clients');
		$manager->manager_status		= Check::int('manager_status');
		$manager->manager_credit		= Check::float('manager_credit');
		$manager->manager_credit_date	= date("Y-m-d").' 00:00:00';
		$manager->manager_credit_local	= Check::float('manager_credit_local');
		$manager->manager_balance_local	= Check::float('manager_credit_local');
		$manager->manager_credit_date_local	= date("Y-m-d").' 00:00:00';
		$manager->manager_description	= Check::str('description',4096,0);
		$manager->package_foto_tax		= Check::chkbox('package_foto_tax');
		$manager->package_foto_system_tax= Check::chkbox('package_foto_system_tax');
		$empties						= Check::get_empties();

		if (isset($_POST['order_tax']))
		{
			$manager->order_tax			= Check::float('order_tax');
			$manager->order_tax 		= empty($manager->order_tax) ? 0 : $manager->order_tax;
		}
		
		if (isset($_POST['package_tax']))
		{
			$manager->package_tax		= Check::float('package_tax');
			$manager->package_tax 		= empty($manager->package_tax) ? 0 : $manager->package_tax;
		}
		
		if (isset($_POST['package_disconnected_tax']))
		{
			$manager->package_disconnected_tax = Check::float('package_disconnected_tax');
			$manager->package_disconnected_tax = empty($manager->package_disconnected_tax) ? 0 : $manager->package_disconnected_tax;
		}
		
		try
		{
			if ( ! $user->user_email)
			{
				throw new Exception('Не верный e-mail.', -13);
			}
			
			if ($empties && ! in_array('_email',$empties))
			{
				throw new Exception('Одно или несколько полей не заполнено.', -11);
			}
				
			
			if ( ! in_array($manager->manager_country, $countries_ids))
			{
				throw new Exception('Выберите страну.', -19);
			}
			if ( ! key_exists($manager->manager_status, $statuses))
			{
				throw new Exception('Выберите статус.', -20);
			}
			
			$this->load->model('UserModel', 'User');			
			
			if ($this->User->select(array('user_email'=> $user->user_email))){
				throw new Exception('Пользователь с такой электронной почтой уже существует!', -16);
			}
			
			$user->user_password = md5($user->user_password);
  
			// создаем пользователя и партнера
			$this->db->trans_begin();			

			$u = $this->User->addUser($user);
			
			if ($u)
				$this->Manager->addManagerData($u->user_id, $manager);
			
			if ($this->db->trans_status() === FALSE) {				
				throw new Exception('Регистрация партнера невозможна.',-3);
			}
			
			// добавляем партнеру клиентов
			if ($manager->manager_status == 1) 
			{
				$neighbour_managers = $this->Manager->select(array('manager_country' => $manager->manager_country, 'manager_status' => 1));
			
				// партнер единственный в стране, задаем его всем клиентам
				if (count($neighbour_managers) == 1) 
				{ 
					$this->load->model('ClientModel', 'Client');
					$clients = $this->Client->getList();
				
					if ($clients) 
					{
						// добавляем связи
						$this->load->model('C2mModel', 'C2m');
						foreach ($clients as $client) 
						{
							$relation = new stdClass();
							$relation->client_id = $client->client_user;
							$relation->manager_id = $u->user_id;
							
							$this->C2m->addRelation($relation);
						}
						
						// вычисляем максимальное число клиентов
						$manager = $this->Manager->fixMaxClientsCount($u->user_id);
							
						if ( ! $manager)
						{
							throw new Exception('Ошибка вычисления максимального числа клиентов. Попробуйте еще раз.');
						}
					}
				}
			}
			
			// сохраняем способы доставки
			$this->load->model('ManagerDeliveryModel', 'Delivery');
			if (isset($_POST['delivery']) && $_POST['delivery'])
			{		
				foreach ($_POST['delivery'] as $delivery_id => $value)
				{
					$delivery = new stdClass();
					$delivery->manager_id = $u->user_id;
					$delivery->delivery_id = $delivery_id;
					$delivery = $this->Delivery->saveManagerDelivery($delivery);
					if ( ! $delivery)
					{
						throw new Exception('Невозможно сохранить способы доставки. Попробуйте еще раз.');
					}
				}
			}
		
			$this->db->trans_commit();
			Func::redirect(BASEURL.$this->cname.'/showPartners');	
			return true;
		}
		catch (Exception $e)
		{
			$this->db->trans_rollback();
			
			$this->result->e	= $e->getCode();			
			$this->result->m	= $e->getMessage();
			
			switch ($this->result->e){
				case -1:	
					$user->user_login		= '';
					break;
				case -2:
				case -13:
					$user->user_email		= '';
					break;				
			}		
			$this->result->d	= $user;
		}		
		
		$view = array(
			'countries'		=> $countries,
			'manager'		=> $manager,
			'statuses'		=> $statuses,
			'deliveries'	=> $deliveries,
			'manager_user'	=> $user // переопределяем переменную $user (сейчас в ней хранится менеджер)
		);
		
		Stack::push('view', $view);
		View::showChild($this->viewpath.'pages/showPartnerInfo', $view);
		//Func::redirect(BASEURL.$this->cname.'/showPartnerInfo');
	}

	public function refreshSummary() {
		$this->load->model('PaymentModel', 'Payment');
		$stat = $this->Payment->getSummaryStat();
		Stack::clear('admin_summary_stat');
		Stack::push('admin_summary_stat', $stat);
		Func::redirect(BASEURL.$this->cname);
	}
	
	public function deleteOrder()
	{
		parent::deleteOrder();
	}
	
	public function filterNewPackages()
	{
		$this->filter('openPackages', 'showNewPackages');
	}
	
	public function filterPayedPackages()
	{
		$this->filter('payedPackages', 'showPayedPackages');
	}
	
	public function filterSentPackages()
	{
		$this->filter('sentPackages', 'showSentPackages');
	}
	
	public function filterOpenOrders()
	{
		$this->filter('not_payedOrders', 'showOpenOrders');
	}
	
	public function filterOpenClientO2o()
	{
		$this->filter('openClientO2o', 'showClientOrdersToOut');
	}
	
	public function filterPayedClientO2o()
	{
		$this->filter('payedClientO2o', 'showClientPayedOrdersToOut');
	}
	
	public function filterOpenManagerO2o()
	{
		$this->filter('openManagerO2o', 'showManagerOrdersToOut');
	}
	
	public function filterPayedManagerO2o()
	{
		$this->filter('payedManagerO2o', 'showManagerPayedOrdersToOut');
	}
	
	public function filterSentOrders()
	{
		$this->filter('sendedOrders', 'showSentOrders');
	}
	
	public function filterPayedOrders()
	{
		$this->filter('payedOrders', 'showPayedOrders');
	}
	
	public function filterClients()
	{
		$this->filter('clients', 'showClients');
	}
	
	public function filterPaymentHistory()
	{
		$this->filter('paymentHistory', 'showPaymentHistory');
	}
	
	public function updateOpenOrdersStatus()
	{
		$this->updateStatus('open', 'showOpenOrders', 'OrderModel');
	}
	
	public function updatePayedOrdersStatus()
	{
		$this->updateStatus('payed', 'showPayedOrders', 'OrderModel');
	}
	
	public function updateSentOrdersStatus()
	{
		$this->updateStatus('sended', 'showSentOrders', 'OrderModel');
	}
	
	public function updateOdetailStatuses()
	{
		parent::updateOdetailStatuses();
	}
	
	public function updatePdetailStatuses()
	{
		parent::updatePdetailStatuses();
	}
	
	public function showOpenOrders()
	{
		$this->showOrders('not_payed', 'showOpenOrders');
	}
	
	public function showSentOrders()
	{
		$this->showOrders('sended', 'showSentOrders');
	}
	
	public function showAddDelivery()
	{
		try
		{
			View::showChild($this->viewpath.'/pages/showAddDelivery');
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
			Func::redirect(BASEURL.$this->cname);
		}
	}
	
	public function showAddCountry()
	{
		try
		{
			View::showChild($this->viewpath.'/pages/showAddCountry');
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
			Func::redirect(BASEURL.$this->cname);
		}
	}
	
	public function addCountry()
	{
		try
		{
			if ( ! $this->user ||
				! $this->user->user_id)
			{
				throw new Exception('Доступ запрещен.');
			}
			
			// валидация пользовательского ввода
			Check::reset_empties();
			$country				= new stdClass();
			$country->country_name	= Check::txt('country_name', 32, 1);
			$empties				= Check::get_empties();
			
			if (is_array($empties)) 
			{
				throw new Exception('Заполните название страны.');
			}
			
			// сохранение результатов
			$this->load->model('CountryModel', 'Countries');
			$new_country = $this->Countries->saveCountry($country);
			
			if ( ! $new_country)
			{
				throw new Exception('Страна не добавлена. Попробуйте еще раз.');
			}			

			$this->result->m = 'Страна успешно добавлена.';
			Stack::push('result', $this->result);
			
			// открываем тарифы
			Func::redirect(BASEURL.$this->cname.'/editPricelist');
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
			Func::redirect(BASEURL.$this->cname.'/showAddDelivery');
		}
	}
	
	public function addDelivery()
	{
		try
		{
			if ( ! $this->user ||
				! $this->user->user_id)
			{
				throw new Exception('Доступ запрещен.');
			}
			
			// валидация пользовательского ввода
			Check::reset_empties();
			$delivery					= new stdClass();
			$delivery->delivery_name	= Check::txt('delivery_name', 32, 1);
			$delivery->delivery_time	= Check::txt('delivery_time', 32, 1);
			$empties					= Check::get_empties();
			
			if (is_array($empties)) 
			{
				throw new Exception('Одно или несколько полей не заполнено.');
			}
			
			// сохранение результатов
			$this->load->model('DeliveryModel', 'Deliveries');
			$new_delivery = $this->Deliveries->saveDelivery($delivery);
			
			if ( ! $new_delivery)
			{
				throw new Exception('Способ доставки не добавлен. Попробуйте еще раз.');
			}			

			// открываем тарифы
			Func::redirect(BASEURL.$this->cname.'/editPricelist');
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
			Func::redirect(BASEURL.$this->cname.'/showAddDelivery');
		}
	}
	
	public function editPricelist()
	{
		try
		{
			// обработка фильтра
			$view['filter'] = $this->initFilter('editPricelist');
			
			$this->load->model('CountryModel', 'Countries');
			$view['countries'] = $this->Countries->getList();
			
			$this->load->model('DeliveryModel', 'Deliveries');
			$view['deliveries'] = $this->Deliveries->getList();
			
			// выбираем валюту и курс
			if ($view['filter']->pricelist_country_from != '')
			{
				$country = $this->Countries->getById($view['filter']->pricelist_country_from);
				
				if ($country)
				{
					$this->load->model('CurrencyModel', 'Currencies');
					$view['currency'] = $this->Currencies->getById($country->country_currency);
				}
			}
			
			if ($view['filter']->pricelist_country_from == '' ||
				$view['filter']->pricelist_country_to == '' ||
				$view['filter']->pricelist_delivery == '')
			{
					throw new Exception('Выберите страны и способ доставки.');
			}
			
			$view['delivery'] = $this->Deliveries->getById($view['filter']->pricelist_delivery);
			
			if ( ! $view['delivery'])
			{
				throw new Exception('Способ доставки не найден. Попробуйте еще раз.');
			}
			
			// отображаем тарифы
			$this->load->model('PricelistModel', 'Pricelist');
			$view['pricelist'] = $this->Pricelist->getPricelist($view['filter']);
			
			// отображаем описание тарифа
			$this->load->model('PricelistDescriptionModel', 'PricelistDescription');
			$view['pricelist_description'] = $this->PricelistDescription->getDescription(
				$view['filter']->pricelist_country_from,
				$view['filter']->pricelist_country_to);
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}

		View::showChild($this->viewpath.'/pages/editPricelist', $view);
	}
	
	public function savePricelist()
	{
		try
		{
			if ( ! $this->user ||
				! $this->user->user_id ||
				! is_numeric($this->uri->segment(3)) ||
				! is_numeric($this->uri->segment(4)) ||
				! is_numeric($this->uri->segment(5)))
			{
				throw new Exception('Доступ запрещен.');
			}
			
			// отображаем описание тарифа
			$this->load->model('PricelistDescriptionModel', 'PricelistDescription');
			
			$pricelist_description = $this->PricelistDescription->getDescription(
				$this->uri->segment(3),
				$this->uri->segment(4));

			if (empty($pricelist_description))
			{
				$pricelist_description = new stdClass();
			}

			$pricelist_description->pricelist_country_from = $this->uri->segment(3);
			$pricelist_description->pricelist_country_to = $this->uri->segment(4);
			$pricelist_description->pricelist_description = Check::txt('description', 1000000, 1);
			
			$this->PricelistDescription->saveDescription($pricelist_description);
		
			// находим способ доставки
			$this->load->model('DeliveryModel', 'Deliveries');
			$delivery = $this->Deliveries->getById($this->uri->segment(5));
			
			if ( ! $delivery)
			{
				throw new Exception('Невозможно сохранить тарифы. Способ доставки не найден.');
			}
			
			$this->load->model('CountryModel', 'Countries');
			$countryFrom = $this->Countries->getById($this->uri->segment(3));
			$countryTo = $this->Countries->getById($this->uri->segment(4));
			
			if ( ! $countryFrom || ! $countryTo)
			{
				throw new Exception('Невозможно сохранить тарифы. Страны не найдены.');
			}

			// загружаем тарифы из файла
			$userfile = isset($_FILES['userfile']) && ! $_FILES['userfile']['error'];

			if ($userfile)
			{
				$this->parsePricelistFile($userfile);
				return;
			}

			$this->load->model('PricelistModel', 'Pricelist');
			$this->load->model('CountryModel', 'Countries');
			$this->load->model('CurrencyModel', 'Currencies');

			// итерируем по ценам в прайслисте
			$this->db->trans_begin();
			
			foreach($_POST as $key=>$value)
			{
				if (stripos($key, 'pricelist_weight') === 0) 
				{
					$price_id = str_ireplace('pricelist_weight', '', $key);
					$this->updatePricelistItem($price_id);
				}
				else if (stripos($key, 'new_weight') === 0) 
				{
					$price_id = str_ireplace('new_weight', '', $key);
					$this->insertPricelistItem($price_id, $this->uri->segment(3), $this->uri->segment(4), $this->uri->segment(5));
				}
			}
			
			$this->db->trans_commit();
			
			// выводим сообщение
			$this->result->m = 'Тарифы успешно сохранены.';
			Stack::push('result', $this->result);
		}
		catch (Exception $e) 
		{
			$this->db->trans_rollback();
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}
		
		// открываем прайслист
		Func::redirect(BASEURL.$this->cname.'/editPricelist');
	}
	
	private function parsePricelistFile()
	{
		// сохраняем файл
		try
		{
			$config['upload_path']			= $_SERVER['DOCUMENT_ROOT'].'/upload/pricelists';
			$config['allowed_types']		= 'xls';
			$config['max_size']				= 1024 * 1024;
			$config['encrypt_name'] 		= TRUE;
			$this->load->library('upload', $config);
			if ( ! $this->upload->do_upload()) {
				throw new Exception(strip_tags(trim($this->upload->display_errors())));
			}
		}
		catch (Exception $ex)
		{
			return;
		}

		// парсим файл
		$file = $this->upload->data();
		
		error_reporting(E_ALL ^ E_NOTICE);
		$this->load->library('Spreadsheet_Excel_Reader');
		$data = new Spreadsheet_Excel_Reader($file['full_path'], false);
		//echo $data->dump(true,true);
		
		$this->load->model('PricelistModel', 'Pricelist');
		$this->load->model('CountryModel', 'Countries');
		$this->load->model('CurrencyModel', 'Currencies');

		// итерируем по ценам в прайслисте
		$this->db->trans_begin();
		$this->Pricelist->clear($this->uri->segment(3), $this->uri->segment(4), $this->uri->segment(5));
		
		for ($i = 1; $i <= $data->rowcount(); $i++)
		{
			$this->parsePricelistItem($this->uri->segment(3), $this->uri->segment(4), $this->uri->segment(5), $data->val($i, 1), $data->val($i, 2));
		}
		
		$this->db->trans_commit();

		// открываем прайслист
		Func::redirect(BASEURL.$this->cname.'/editPricelist');
	}
		
	protected function updatePricelistItem($pricelist_id)
	{
		if ( ! is_numeric($pricelist_id) ||
			! isset($_POST['pricelist_weight'.$pricelist_id]) ||
			! isset($_POST['pricelist_price'.$pricelist_id])) return;

		// находим запись в тарифе
		$pricelist = $this->Pricelist->getById($pricelist_id);

		if ( ! $pricelist)
		{
			throw new Exception('Невозможно сохранить тариф. Некоторые записи не найдены.');
		}

		// удаление записи из тарифа
		if ($_POST['pricelist_weight'.$pricelist_id] == '')
		{
			$deleted = $this->Pricelist->delete($pricelist_id);
				
			if ( ! $deleted)
			{
				throw new Exception('Невозможно сохранить тариф. Попоробуйте еще раз.');
			}
			
			return;
		}
			
		// валидация пользовательского ввода
		Check::reset_empties();
		$convert							= isset($_POST['is_local_price']);
		$pricelist->pricelist_price 		= Check::float('pricelist_price'.$pricelist_id);
		$pricelist->pricelist_price_local	= Check::float('pricelist_price_local'.$pricelist_id);
		$pricelist->pricelist_weight 		= Check::float('pricelist_weight'.$pricelist_id);
		$empties							= Check::get_empties();
		
		if ($empties)
		{
			throw new Exception('Некоторые поля тарифа не заполнены. Попробуйте еще раз.');
		}
		
		// конвертация в доллары
		if ($convert)
		{
			$this->convert($pricelist);
		}
				
		// сохранение тарифа
		$new_pricelist = $this->Pricelist->savePricelist($pricelist);

		if ($new_pricelist === FALSE)
		{
			throw new Exception('Невозможно сохранить тариф. Попоробуйте еще раз.');
		}
	}
	
	protected function convert($pricelist)
	{
		$country = $this->Countries->getById($pricelist->pricelist_country_from);
		
		if ( ! $country)
		{
			throw new Exception('Невозможно конвертировать тариф в доллары. Курс не найден.');
		}
		
		$cross_rate = $this->Currencies->getById($country->country_currency);
		
		if ( ! $cross_rate)
		{
			throw new Exception('Невозможно конвертировать тариф в доллары. Попробуйте еще раз.');
		}
		
		// округляем
		$pricelist->pricelist_price = ceil($pricelist->pricelist_price_local / $cross_rate->cbr_cross_rate);
	}

	protected function insertPricelistItem($pricelist_id, $country_from, $country_to, $delivery)
	{
		// сохраняем только заполненные тарифы
		if ( ! is_numeric($pricelist_id) ||
			! isset($_POST['new_weight'.$pricelist_id]) ||
			! isset($_POST['new_price'.$pricelist_id])) return;

		// валидация пользовательского ввода
		$pricelist = new stdClass();
		$convert							= isset($_POST['is_local_price']);
		$pricelist->pricelist_price 		= Check::float('new_price'.$pricelist_id);
		$pricelist->pricelist_price_local	= Check::float('new_price_local'.$pricelist_id);
		$pricelist->pricelist_weight 		= Check::float('new_weight'.$pricelist_id);
		$pricelist->pricelist_country_from	= $country_from;
		$pricelist->pricelist_country_to	= $country_to;
		$pricelist->pricelist_delivery		= $delivery;
		$empties							= Check::get_empties();

		if ($empties)
		{
			throw new Exception('Некоторые поля тарифа не заполнены. Попробуйте еще раз.');
		}

		// конвертация в доллары
		if ($convert)
		{
			$this->convert($pricelist);
		}
				
		// сохранение тарифа
		$pricelist->pricelist_id = '';
		$new_pricelist = $this->Pricelist->savePricelist($pricelist);
				
		if ( ! $new_pricelist)
		{
			throw new Exception('Невозможно сохранить тариф. Попоробуйте еще раз.');
		}
	}
	
	protected function parsePricelistItem($country_from, $country_to, $delivery, $weight, $price)
	{
		// валидация пользовательского ввода
		$pricelist = new stdClass();
		$pricelist->pricelist_price_local	= floatVal($price);
		$pricelist->pricelist_weight 		= floatVal($weight);
		$pricelist->pricelist_country_from	= $country_from;
		$pricelist->pricelist_country_to	= $country_to;
		$pricelist->pricelist_delivery		= $delivery;
		
		if ($empties)
		{
			throw new Exception('Некоторые поля тарифа не заполнены. Попробуйте еще раз.');
		}

		// конвертация в доллары
		$this->convert($pricelist);
				
		// сохранение тарифа
		$pricelist->pricelist_id = '';
		$new_pricelist = $this->Pricelist->savePricelist($pricelist);
				
		if ( ! $new_pricelist)
		{
			throw new Exception('Невозможно сохранить тариф. Попоробуйте еще раз.');
		}
	}
	
	public function payPackageToManager()
	{
		try
		{
			// безопасность
			if ( ! $this->user ||
				! $this->user->user_id ||
				! is_numeric($this->uri->segment(3)))
			{
				throw new Exception('Доступ запрещен.');
			}
			
			// безопасность: проверяем существование посылки
			$this->load->model('PackageModel', 'Packages');
			$package = $this->Packages->getById($this->uri->segment(3));

			if ( ! $package ||
				$package->package_status != 'sent' ||
				$package->package_payed_to_manager)
			{
				throw new Exception('Посылка не найдена. Попробуйте еще раз.');
			}			

			// добавление платежа
			$payment_obj = new stdClass();
			$payment_obj->payment_from			= 1;
			$payment_obj->payment_to			= $package->package_manager;
			$payment_obj->payment_amount_from	= $package->package_manager_cost;
			$payment_obj->payment_amount_to		= $package->package_manager_cost;
			$payment_obj->payment_amount_tax	= 0;
			$payment_obj->payment_purpose		= 'выплата партнеру за посылку';
			$payment_obj->payment_comment		= '№ '.$package->package_id;
			
			$this->load->model('PaymentModel', 'Payment');
			
			$this->db->trans_begin();

			if ( ! $this->Payment->makePayment($payment_obj, true)) 
			{
				throw new Exception('Ошибка выплаты партнеру. Попробуйте еще раз.');
			}			
			
			// сохранение посылки
			$package->package_payed_to_manager = true;
			$payed_package = $this->Packages->savePackage($package);
			
			if ( ! $payed_package)
			{
				throw new Exception('Платеж не выполнен. Попробуйте еще раз.');
			}

			if ($this->db->trans_status() !== FALSE)
			{
				$this->db->trans_commit();
			}
			
			$this->result->m = 'Услуги партнера успешно оплачены.';
		}
		catch (Exception $e)
		{
			$this->db->trans_rollback();
		
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
		}
		
		// открываем партнера
		Stack::push('result', $this->result);
		
		if (isset($package) && isset($package->package_manager))
		{
			Func::redirect(BASEURL.$this->cname.'/editPartner/'.$package->package_manager);
		}
		else
		{
			Func::redirect(BASEURL.$this->cname);
		}
	}
	
	public function payOrderToManager()
	{
		try
		{
			// безопасность
			if ( ! $this->user ||
				! $this->user->user_id ||
				! is_numeric($this->uri->segment(3)))
			{
				throw new Exception('Доступ запрещен.');
			}
			
			// безопасность: проверяем существование заказа
			$this->load->model('OrderModel', 'Orders');
			$order = $this->Orders->getById($this->uri->segment(3));

			if ( ! $order ||
				$order->order_status != 'sended' ||
				$order->order_payed_to_manager)
			{
				throw new Exception('Заказ не найден. Попробуйте еще раз.');
			}			

			// добавление платежа
			$payment_obj = new stdClass();
			$payment_obj->payment_from			= 1;
			$payment_obj->payment_to			= $order->order_manager;
			$payment_obj->payment_amount_from	= $order->order_manager_cost;
			$payment_obj->payment_amount_to		= $order->order_manager_cost;
			$payment_obj->payment_amount_tax	= 0;
			$payment_obj->payment_purpose		= 'выплата партнеру за заказ';
			$payment_obj->payment_comment		= '№ '.$order->order_id;
			
			$this->load->model('PaymentModel', 'Payment');
			
			$this->db->trans_begin();

			if ( ! $this->Payment->makePayment($payment_obj, true)) 
			{
				throw new Exception('Ошибка выплаты партнеру. Попробуйте еще раз.');
			}			
			
			// сохранение посылки
			$order->order_payed_to_manager = true;
			$payed_order = $this->Orders->saveOrder($order);
			
			if ( ! $payed_order)
			{
				throw new Exception('Платеж не выполнен. Попробуйте еще раз.');
			}

			if ($this->db->trans_status() !== FALSE)
			{
				$this->db->trans_commit();
			}
			
			$this->result->m = 'Услуги партнера успешно оплачены.';
		}
		catch (Exception $e)
		{
			$this->db->trans_rollback();
		
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
		}
		
		// открываем партнера
		Stack::push('result', $this->result);
		
		if (isset($order) && isset($order->order_manager))
		{
			Func::redirect(BASEURL.$this->cname.'/editPartner/'.$order->order_manager);
		}
		else
		{
			Func::redirect(BASEURL.$this->cname);
		}
	}
	
	public function deletePackage()
	{
		parent::deletePackage();
	}
	
	public function editPackageAddress()
	{
		parent::editPackageAddress();
	}

	public function showOrderDetails()
	{
		parent::showOrderDetails();
	}
	public function showPackageDetails()
	{
		parent::showPackageDetails();
	}
	
	public function showDeclaration()
	{
		parent::showDeclaration();
	}
	
	public function addOrderComment($order_id, $comment_id = null)
	{
		parent::addOrderComment($order_id, $comment_id);
	}
	
	public function saveDeclaration()
	{
		parent::saveDeclaration();
	}
	
	public function filterEditPricelist()
	{
		$this->filter('editPricelist', 'editPricelist');
	}

	public function filterClientReport($client_id)
	{
		$this->filter('editClient', 'editClient/'.$client_id);
	}

	public function filterPartnerReport($partner_id)
	{
		$this->filter('editPartner', 'editPartner/'.$partner_id);
	}

	public function updatePackageAddress()
	{
		parent::updatePackageAddress();
	}
	
	public function updateOrderDetails()
	{
		parent::updateOrderDetails();
	}
	public function updatePackageDetails()
	{
		parent::updatePackageDetails();
	}
	
	public function showO2oComments()
	{
		parent::showO2oComments();
	}

	public function addO2oComment()
	{
		parent::addO2oComment();
	}	
	
	public function addPackageComment($package_id, $comment_id = null)
	{
		try
		{
			if ( ! is_numeric($package_id))
			{
				throw new Exception('Доступ запрещен.');
			}
				
			$this->load->model('PackageModel', 'Packages');
			$package = $this->Packages->getById((int) $package_id);

			if ( ! $package)
			{
				throw new Exception('Невозможно добавить комментарий. Посылка не найдена.');
			}
			
			$this->load->model('PCommentModel', 'Comments');
			
			// валидация пользовательского ввода
			if (is_numeric($comment_id)) 
			{
				$pcomment = $this->Comments->getById($comment_id);
				if ( ! $pcomment) 
				{
					throw new Exception('Невозможно изменить комментарий. Комментарий не найден.');
				}
				
				$pcomment->pcomment_comment	= Check::txt('comment_update', 8096, 1);
			}
			else
			{
				$pcomment				= new stdClass();
				$pcomment->pcomment_comment	= Check::txt('comment', 8096, 1);
				$pcomment->pcomment_user	= $this->user->user_id;
			}
				
			$pcomment->pcomment_package	= $package_id;
			$empties					= Check::get_empties();
		
			if ($empties) 
			{
				throw new Exception('Текст комментария отсутствует. Попробуйте еще раз.');
			}
			
			// сохранение результатов
			if (is_numeric($comment_id)) $pcomment->pcomment_id = $comment_id;

			if ( ! $this->Comments->addComment($pcomment) &&
				! is_numeric($comment_id))
			{
				throw new Exception('Комментарий не добавлен. Попробуйте еще раз.');
			}			
			
			// выставляем флаг нового комментария
			$package->comment_for_client	= TRUE;
			$package->comment_for_manager	= TRUE;
			$package = $this->Packages->savePackage($package);

			if ( ! $package)
			{
				throw new Exception('Комментарий не добавлен. Попробуйте еще раз.');
			}
			
			// уведомления
			$this->load->model('ManagerModel', 'Managers');
			$this->load->model('UserModel', 'Users');
			$this->load->model('ClientModel', 'Clients');
			
			Mailer::sendManagerNotification(
				Mailer::SUBJECT_NEW_COMMENT, 
				Mailer::NEW_PACKAGE_COMMENT_NOTIFICATION, 
				$package->package_manager,
				$package->package_id, 
				0,
				"http://countrypost.ru/manager/showPackageDetails/{$package->package_id}#comments",
				$this->Managers,
				null);

			Mailer::sendClientNotification(
				Mailer::SUBJECT_NEW_COMMENT, 
				Mailer::NEW_PACKAGE_COMMENT_NOTIFICATION, 
				$package->package_id, 
				$package->package_client,
				"http://countrypost.ru/client/showPackageDetails/{$package->package_id}#comments",
				$this->Clients,
				$this->Users);
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}
		
		// открываем комментарии к посылке
		Func::redirect($_SERVER['HTTP_REFERER'] . '#comments');
	}
	
	public function delPackageComment($package_id, $comment_id){
		parent::delPackageComment((int) $package_id, (int) $comment_id);
	}
	
	public function getDeliveries(){
		
		$this->load->model('ManagerDeliveryModel', 'MD');		
		$deliveries['items'] = $this->MD->getDeliveries($_POST['country_id']);
		echo json_encode($deliveries);
		exit;
	}
	
	public function deleteOrder2in($oid) 
	{
		parent::deleteOrder2in($oid);
	}

	public function showOrder2InFoto($oid, $filename) {
		header('Content-type: image/jpg');
		$this->load->model('Order2InModel', 'Order2in');
		if ($o2i = $this->Order2in->getInfo(array('order2in_id' => intval($oid)))) {
			readfile($_SERVER['DOCUMENT_ROOT'].'/upload/orders2in/'.$oid.'/'.$filename);
		}
		die();
	}

	public function addO2iComment()
	{
		parent::addO2iComment();
	}
	
	public function addInsurance($add = 1)
	{
		parent::addInsurance($add);
	}
	
	public function removeInsurance()
	{
		$this->addInsurance(0);
	}

	public function addDeclarationHelp()
	{
		parent::addDeclarationHelp();
	}

	public function removeDeclarationHelp()
	{
		try
		{
			$this->load->model('PackageModel', 'Packages');
			$package = $this->Packages->getById($this->uri->segment(3));
			
			if ( ! $package)
			{
				throw new Exception('Невозможно сохранить декларацию. Посылка недоступна.');
			}

			// меняем статус декларации
			$package->declaration_status = 'completed';

			// вычисляем стоимость посылки
			$this->load->model('ConfigModel', 'Config');
			$this->load->model('PricelistModel', 'Pricelist');
			
			$package = $this->Packages->calculateCost($package, $this->Config, $this->Pricelist);
			
			if ( ! $package) 
			{
				throw new Exception('Стоимость посылки не определена. Попробуйте еще раз.');
			}
			
			// сохраняем декларацию
			$package = $this->Packages->savePackage($package);

			if ( ! $package)
			{
				throw new Exception('Декларация не сохранена. Попробуйте еще раз.');
			}
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}
		
		// открываем посылки
		Func::redirect(BASEURL.$this->cname.'/showDeclaration/'.$this->uri->segment(3));
	}
	
	public function addPackageFoto($redirect)
	{
		$package_id	= Check::int('package_id');
		
		// загрузка файла
		$config['allowed_types']		= 'jpg|gif|jpeg|png';
		$config['max_size']				= '4096';
		//$config['max_width'] 			= '2048';
		//$config['max_height'] 		= '2048';
		$config['remove_spaces'] 		= FALSE;
		$config['overwrite'] 			= FALSE;
		$config['encrypt_name'] 		= TRUE;
		$max_width						= 1024;
		$max_height						= 768;
		
		try{
			$this->load->model('PackageModel', 'Package');
			$package	= $this->Package->getById($package_id);
			
			if ( ! $package || $package_id != $package->package_id){
				throw new Exception('Не верный номер посылки!');
			}
			
			$config['upload_path'] = UPLOAD_DIR.'packages/'.$package->package_manager.'/'.$package_id.'/';

			if ( ! is_dir($config['upload_path']) && !(mkdir($config['upload_path'], 0777, true) || chmod($config['upload_path'], 0777))){
				throw new Exception('Ошибка файловой системы. Обратитесь к администратору.');
			}
	
			$this->load->library('upload', $config);
			$uploaded = false;
			foreach(array('userfile1','userfile2','userfile3','userfile4','userfile5') as $val)
			{
				if ($this->upload->do_upload($val))	
				{
					$uploaded = true;
				
					$uploadedImg = $this->upload->data();
					$imageInfo = getimagesize($uploadedImg['full_path']);
					if ($imageInfo[0]>$max_width || $imageInfo[1]>$max_height)
					{
						$config['image_library']	= 'gd2';
						$config['source_image']		= $uploadedImg['full_path'];
						$config['maintain_ratio']	= TRUE;
						$config['width']			= $max_width;
						$config['height']			= $max_height;

						$this->load->library('image_lib', $config); // загружаем библиотеку
						$this->image_lib->resize(); // и вызываем функцию
					}
				}
			}
			if ( !  $uploaded)
			{
				throw new Exception((strip_tags(trim($this->upload->display_errors()))));
			}
			//$uFile	= $this->upload->data();
			//if ( ! rename($uFile["full_path"],$uFile["file_path"].$package_id.'.jpg')){
			//	throw new Exception('Can`t rename filename!');
			//}
		}catch (Exception $e){
			$this->result->m	= $e->getMessage();
			Stack::push('result', $this->result);
		}
		
		Func::redirect('/'.$this->cname."/{$redirect}");
	}
	
		public function showPackageFoto($pid, $filename){
		$this->showPackagePhoto($pid, $filename);
	}
	
	public function showScreen($oid=null) {
		header('Content-type: image/jpg');
		$this->load->model('OdetailModel', 'OdetailModel');
		if ($detail = $this->OdetailModel->getInfo(array('odetail_id' => intval($oid)))) 
		{
			readfile("{$_SERVER['DOCUMENT_ROOT']}/upload/orders/{$detail->odetail_client}/$oid.jpg");
		}
		die();
	}
	
	public function showPayedOrders()
	{
		$this->showOrders('payed', 'showPayedOrders');
	}
	
	public function refundOrder()
	{
		try
		{
			if ( ! is_numeric($this->uri->segment(3)))
			{
				throw new Exception('Доступ запрещен.');
			}
			
			// безопасность: проверяем связку клиента и заказа
			$this->load->model('OrderModel', 'Orders');
			$order = $this->Orders->getById($this->uri->segment(3));

			if ( ! $order)
			{
				throw new Exception('Заказ не найден. Попробуйте еще раз.');
			}			

			// находим местную валюту
			$this->load->model('CurrencyModel', 'Currency');
			$currency = $this->Currency->getCurrencyByCountry($order->order_country);
			
			// добавление платежа партнера клиенту
			$payment_manager = new stdClass();
			$payment_manager->payment_from				= $order->order_manager;
			$payment_manager->payment_to				= $order->order_client;
			$payment_manager->payment_amount_from		= $order->order_cost_payed - $order->order_cost - $order->order_system_comission_payed + $order->order_system_comission;
			$payment_manager->payment_amount_to			= $order->order_cost_payed - $order->order_cost - $order->order_system_comission_payed + $order->order_system_comission;
			$payment_manager->payment_amount_tax		= $order->order_cost_payed - $order->order_cost - $order->order_system_comission_payed + $order->order_system_comission;
			$payment_manager->payment_purpose			= 'возмещение недоставленных товаров';
			$payment_manager->payment_comment			= '№ '.$order->order_id;
			$payment_manager->payment_type				= 'order';
			$payment_manager->payment_transfer_order_id	= $this->user->user_id.date('Y').date('m').date('d').date('h').date('i').date('s');
			
			// добавление платежа партнеру в местной валюте
			$payment_manager_local = new stdClass();
			$payment_manager_local->payment_from		= $order->order_manager;
			$payment_manager_local->payment_to			= $order->order_client;
			$payment_manager_local->payment_amount_from	= $order->order_manager_cost_payed_local - $order->order_manager_cost_local;
			$payment_manager_local->payment_amount_to	= 0;
			$payment_manager_local->payment_amount_tax	= $order->order_manager_comission_payed_local - $order->order_manager_comission_local;
			$payment_manager_local->payment_purpose		= 'возмещение недоставленных товаров в местной валюте';
			$payment_manager_local->payment_comment		= '№ '.$order->order_id;
			$payment_manager_local->payment_type		= 'order';
			$payment_manager_local->payment_currency	= $currency->currency_symbol;
			$payment_manager_local->payment_transfer_order_id	= '';
			
			// добавление платежа системы клиенту
			$payment_system = new stdClass();
			$payment_system->payment_from				= 1;
			$payment_system->payment_to					= $order->order_client;
			$payment_system->payment_amount_from		= 0;
			$payment_system->payment_amount_to			= $order->order_system_comission_payed - $order->order_system_comission;
			$payment_system->payment_amount_tax			= $order->order_system_comission_payed - $order->order_system_comission;
			$payment_system->payment_purpose			= 'возмещение недоставленных товаров';
			$payment_system->payment_comment			= '№ '.$order->order_id;
			$payment_system->payment_type				= 'order';
			$payment_system->payment_transfer_order_id	= '';
			
			$this->load->model('PaymentModel', 'Payment');
			
			// погнали
			$this->db->trans_begin();

			if ( ! $this->Payment->makePayment($payment_manager, true) ||
				! $this->Payment->makePayment($payment_system, true) ||
				! $this->Payment->makePaymentLocal($payment_manager_local, true)) 
			{
				throw new Exception('Ошибка возмещения средств. Попробуйте еще раз.');
			}			
			
			// сохраняем данные об оплате
			$order->order_cost_payed = $order->order_cost;
			$order->order_manager_comission_payed = $order->order_manager_comission;
			$order->order_system_comission_payed = $order->order_system_comission;

			$order->order_manager_cost_payed_local = $order->order_manager_cost_local;
			$order->order_manager_comission_payed_local = $order->order_manager_comission_local;

			$payed_order = $this->Orders->saveOrder($order);
			
			if ($this->db->trans_status() !== FALSE)
			{
				$this->db->trans_commit();
			}
			
			$this->session->set_userdata(array('user_coints' => ($this->user->user_coints - $payment_system->payment_amount_to)));
			$this->result->m = 'Недоставленные товары успешно возмещены клиенту.';

		}
		catch (Exception $e)
		{
			print_r($e);die();
			$this->db->trans_rollback();
		
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
		}
		
		// открываем заказы
		Stack::push('result', $this->result);
		Func::redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function refundPackage()
	{
		parent::refundPackage();
	}	

	public function joinProducts($order_id)
	{
		parent::joinProducts($order_id);
	}
	
	public function removeOdetailJoint($order_id, $odetail_joint_id)
	{
		parent::removeOdetailJoint($order_id, $odetail_joint_id);
	}
	
	public function delOrderComment($package_id, $comment_id){
		parent::delOrderComment((int) $package_id, (int) $comment_id);
	}
	
	public function sendOrderConfirmation($order_id){
		parent::sendOrderConfirmation((int) $order_id);
	}
	
	public function deleteProduct($odid)
	{
		parent::deleteProduct($odid);
	}
	
	public function addProductManualAjax() 
	{
		parent::addProductManualAjax();
	}
	public function addProductManualAjaxP() 
	{
		parent::addProductManualAjaxP();
	}
	
	public function addExtraPayment() 
	{
		// превалидация
		Check::reset_empties();		
		$from = Check::txt('from', 7, 1);
		$to = Check::txt('to', 7, 1);
		$empties = Check::get_empties();
		
		try
		{
			if ($empties || ($from == '-' && $to == '-'))
			{
				throw new Exception('Выберите получателя и отправителя платежа.');
			}
			
			if ($from == $to)
			{
				throw new Exception('Переводы от партнера партнеру, от клиента клиенту и от администратора администратору не поддерживаются.');
			}

			// собираем данные по платежу
			Check::reset_empties();		
			$extra_payment = new stdClass();
			
			// получатели и отправители
			if ($from == '-')
			{
				$extra_payment->extra_payment_from = 0;
			}
			else if ($from == 'admin')
			{
				$extra_payment->extra_payment_from = 1;
			}
			else
			{
				$extra_payment->extra_payment_from = Check::int('payment_from');
			}
			
			if ($to == '-')
			{
				$extra_payment->extra_payment_to = 0;
			}
			else if ($to == 'admin')
			{
				$extra_payment->extra_payment_to = 1;
			}
			else
			{
				$extra_payment->extra_payment_to = Check::int('payment_to');
			}
			
			// ищем партнера и местную валюту
			if ($from == 'partner' || $to == 'partner')
			{
				$is_local_transfer = true;
				$this->load->model('ManagerModel', 'Managers');
				$this->load->model('UserModel', 'Users');
			
				$manager = $this->Managers->getById(($from == 'partner') ? $extra_payment->extra_payment_from : $extra_payment->extra_payment_to);
				$user = $this->Users->getById(($from == 'partner') ? $extra_payment->extra_payment_from : $extra_payment->extra_payment_to);
				
				if ( ! $manager || ! $user)
				{
					throw new Exception('Партнер не найден. Попробуйте еще раз.');
				}
				
				$this->load->model('CurrencyModel', 'Currency');
				$currency = $this->Currency->getCurrencyByCountry($manager->manager_country);

				if ( ! $manager)
				{
					throw new Exception('Местная валюта партнера не найдена. Попробуйте еще раз.');
				}
			}

			// платеж в долларах
			if ($from == 'partner')
			{
				$extra_payment->extra_payment_from_login = $user->user_login;
			}
			if ($to == 'partner')
			{
				$extra_payment->extra_payment_to_login = $user->user_login;
			}
			$extra_payment->extra_payment_type			= Check::txt('payment_type', 4096, 0);
			$extra_payment->extra_payment_purpose		= Check::txt('payment_purpose', 4096, 0);
			$extra_payment->extra_payment_comment		= Check::txt('payment_comment', 4096, 0);
			$extra_payment->extra_payment_amount		= Check::float('payment_amount');
			if (isset($_POST['payment_amount_ru']) && $_POST['payment_amount_ru'])
			{
				$extra_payment->extra_payment_amount_ru		= Check::float('payment_amount_ru');
			}
			$extra_payment->extra_payment_comission		= Check::txt('payment_comission', 4096, 0);
			
			// платеж в местной валюте
			if (isset($is_local_transfer))
			{
				if (isset($_POST['payment_amount_local']) && $_POST['payment_amount_local'])
				{
					$extra_payment->extra_payment_amount_local = Check::float('payment_amount_local');
					$extra_payment->extra_payment_comission_local = Check::txt('payment_comission_local', 4096, 0);
					$extra_payment->extra_payment_currency = $currency->currency_symbol;
				}
				else
				{
					unset($is_local_transfer);
				}				
			}
				
			// input validation
			$empties = Check::get_empties();
			
			if ($empties)
			{
				if (in_array('payment_from', $empties))
				{
					throw new Exception('Выберите отправителя платежа.');
				}
				else if (in_array('payment_to', $empties))
				{
					throw new Exception('Выберите получателя платежа.');
				}
				else if ((in_array('payment_amount', $empties)))
				{
					throw new Exception('Введите сумму платежа в долларах.');
				}
				else
				{
					throw new Exception('Некоторые поля не заполнены. Попробуйте еще раз.'.implode(', ', $empties));
				}
			}
		
			// переводим деньги
			$this->db->trans_begin();
			
			// платеж в долларах
			$payment = new stdClass();
			$payment->payment_from			= $extra_payment->extra_payment_from;
			$payment->payment_to			= $extra_payment->extra_payment_to;
			$payment->payment_amount_from	= ($from == '-') ? 0 : $extra_payment->extra_payment_amount;
			$payment->payment_amount_to		= ($to	 == '-') ? 0 : $extra_payment->extra_payment_amount;
			$payment->payment_purpose		= $extra_payment->extra_payment_purpose;
			$payment->payment_type			= 'extra_payment';
			$payment->payment_comment		= $extra_payment->extra_payment_comment;
			$payment->payment_amount_tax	= $extra_payment->extra_payment_comission;
			if (isset($extra_payment->extra_payment_amount_ru))
			{
				$payment->payment_amount_rur	= $extra_payment->extra_payment_amount_ru;
			}

			$this->load->model('PaymentModel', 'Payment');
			
			if ( ! $this->Payment->makePayment($payment)) 
			{
				throw new Exception('Ошибка перевода средств между счетами. Попробуйте еще раз.');
			}

			// платеж в местной валюте
			if (isset($is_local_transfer))
			{
				$payment_local = new stdClass();
				$payment_local->payment_from		= ($from == 'partner') ? $extra_payment->extra_payment_from : 0;
				$payment_local->payment_to			= ($to	 == 'partner') ? $extra_payment->extra_payment_to : 0;
				$payment_local->payment_amount_from	= ($from == 'partner') ? $extra_payment->extra_payment_amount_local : 0;
				$payment_local->payment_amount_to	= ($to	 == 'partner') ? $extra_payment->extra_payment_amount_local : 0;
				$payment_local->payment_amount_rur	= '';
				$payment_local->payment_purpose		= $extra_payment->extra_payment_purpose;
				$payment_local->payment_type		= 'extra_payment';
				$payment_local->payment_comment		= $extra_payment->extra_payment_comment;
				$payment_local->payment_amount_tax	= $extra_payment->extra_payment_comission_local;
				$payment_local->payment_currency	= $currency->currency_symbol;
			

				if ( ! $this->Payment->makePaymentLocal($payment_local)) 
				{
					throw new Exception('Ошибка перевода средств в местной валюте. Попробуйте еще раз.');
				}
			}

			// и сохраняем доп. платеж
			$this->load->model('ExtraPaymentModel', 'ExtraPayments');
			$saved_id = $this->ExtraPayments->addPayment($extra_payment);

			if ( ! $saved_id) 
			{
				throw new Exception('Перевод не осуществлен. Попробуйте еще раз.');
			}
			
			// списываем с админа
			if ($from == 'admin')
			{
				$this->session->set_userdata(array('user_coints' => $this->user->user_coints - $payment->payment_amount_from));
			}
			else if ($to == 'admin')
			{
				$this->session->set_userdata(array('user_coints' => $this->user->user_coints + $payment->payment_amount_to));
			}			
			
			$this->db->trans_commit();
			$this->result->e = 1;			
			$this->result->m = 'Платеж успешно добавлен.';
		}
		catch (Exception $e)
		{
			$this->db->trans_rollback();
			$this->result->e	= -1;			
			$this->result->m	= $e->getMessage();
		}
		
		Stack::push('result', $this->result);		
		Func::redirect(BASEURL.$this->cname.'/extraPayments');
	}
	
	public function deleteExtraPayment($id) 
	{
		try
		{
			if ( ! isset($id) ||
				! is_numeric($id))
			{
				throw new Exception('Платеж не найден. Попробуйте еще раз.');
			}
			
			// создание платежа
			$this->db->trans_begin();
			
			$this->load->model('ExtraPaymentModel', 'ExtraPayments');
			$extra_payment = $this->ExtraPayments->getById($id);

			if ( ! $extra_payment) 
			{
				throw new Exception('Перевод не найден. Попробуйте еще раз.');
			}
			
			// обратный платеж в долларах
			$payment = new stdClass();
			$payment->payment_from			= $extra_payment->extra_payment_to;
			$payment->payment_to			= $extra_payment->extra_payment_from;
			$payment->payment_amount_from	= ($extra_payment->extra_payment_to) ? $extra_payment->extra_payment_amount : 0;
			$payment->payment_amount_to		= ($extra_payment->extra_payment_from) ? $extra_payment->extra_payment_amount : 0;
			$payment->payment_purpose		= $extra_payment->extra_payment_purpose;
			$payment->payment_type			= 'extra_payment';
			$payment->payment_comment		= $extra_payment->extra_payment_comment;
			$payment->payment_amount_tax	= $extra_payment->extra_payment_comission;
			if (isset($extra_payment->extra_payment_amount_ru))
			{
				$payment->payment_amount_rur	= $extra_payment->extra_payment_amount_ru;
			}

			$this->load->model('PaymentModel', 'Payment');
			
			if ( ! $this->Payment->makePayment($payment)) 
			{
				throw new Exception('Ошибка перевода средств между счетами. Попробуйте еще раз.');
			}

			// обратный платеж в местной валюте
			if (isset($extra_payment->extra_payment_amount_local))
			{
				$payment_local = new stdClass();
				$payment_local->payment_from		= isset($extra_payment->extra_payment_to_login) ? $extra_payment->extra_payment_to : 0;
				$payment_local->payment_to			= isset($extra_payment->extra_payment_from_login) ? $extra_payment->extra_payment_from : 0;
				$payment_local->payment_amount_from	= isset($extra_payment->extra_payment_to_login) ? $extra_payment->extra_payment_amount_local : 0;
				$payment_local->payment_amount_to	= isset($extra_payment->extra_payment_from_login) ? $extra_payment->extra_payment_amount_local : 0;
				$payment_local->payment_amount_rur	= '';
				$payment_local->payment_purpose		= $extra_payment->extra_payment_purpose;;
				$payment_local->payment_type		= 'extra_payment';
				$payment_local->payment_comment		=$extra_payment->extra_payment_comment;
				$payment_local->payment_amount_tax	= $extra_payment->extra_payment_comission_local;
				$payment_local->payment_currency	= $extra_payment->extra_payment_currency;
			

				if ( ! $this->Payment->makePaymentLocal($payment_local)) 
				{
					throw new Exception('Ошибка перевода средств в местной валюте. Попробуйте еще раз.');
				}
			}
			
			// меняем статус доп. платежа
			$extra_payment->extra_payment_status = 'deleted';
			$this->ExtraPayments->addPayment($extra_payment);
			
			// списываем с админа
			if ($extra_payment->extra_payment_to == 1)
			{
				$this->session->set_userdata(array('user_coints' => $this->user->user_coints - $extra_payment->extra_payment_amount));
			}
			else if ($extra_payment->extra_payment_from == 1)
			{
				$this->session->set_userdata(array('user_coints' => $this->user->user_coints + $extra_payment->extra_payment_amount));
			}			
			
			$this->db->trans_commit();
			$this->result->e = 1;			
			$this->result->m = 'Платеж успешно удален.';
		}
		catch (Exception $e)
		{
			$this->db->trans_rollback();
			$this->result->e	= -1;			
			$this->result->m	= $e->getMessage();
		}
		
		Stack::push('result', $this->result);		
		Func::redirect(BASEURL.$this->cname.'/extraPayments');
	}
	
	public function addPdetailFoto()
	{
		try
		{
			if (empty($_POST['pdetail_id']) OR
				! is_numeric($_POST['pdetail_id']))
			{
				throw new Exception('Доступ запрещен.');
			}

			$pdetail_id = $_POST['pdetail_id'];
			$this->load->model('PdetailModel', 'Pdetails');
			$details = $this->Pdetails->getFilteredDetails(
				array(
					'pdetail_id' => $pdetail_id
				), 
				true);
							
			if (empty($details))
			{
				throw new Exception('Товар не найден.');
			}
			
			$pdetail = $details[0];
			
			// загрузка файла
			$config['upload_path']			= UPLOAD_DIR."packages/{$pdetail->pdetail_package}/$pdetail_id/";
			$config['allowed_types']		= 'jpg|gif|jpeg|png';
			$config['max_size']				= '4096';
			$config['remove_spaces'] 		= FALSE;
			$config['overwrite'] 			= FALSE;
			$config['encrypt_name'] 		= TRUE;
			$max_width						= 1024;
			$max_height						= 768;
			
			if ( ! is_dir($config['upload_path']) AND
				! (mkdir($config['upload_path'], 0777, true) OR
					chmod($config['upload_path'], 0777)))
			{
				throw new Exception('Ошибка файловой системы. Обратитесь к администратору.');
			}
	
			$this->load->library('upload', $config);
			$uploaded = false;
			
			foreach(array('userfile1','userfile2','userfile3','userfile4','userfile5') as $val)
			{
				if ($this->upload->do_upload($val))	
				{
					$uploaded = true;
				
					$uploadedImg = $this->upload->data();
					$imageInfo = getimagesize($uploadedImg['full_path']);
					if ($imageInfo[0]>$max_width || $imageInfo[1]>$max_height)
					{
						$config['image_library']	= 'gd2';
						$config['source_image']		= $uploadedImg['full_path'];
						$config['maintain_ratio']	= TRUE;
						$config['width']			= $max_width;
						$config['height']			= $max_height;

						$this->load->library('image_lib', $config); // загружаем библиотеку
						$this->image_lib->resize(); // и вызываем функцию
					}
				}
			}
			if ( ! $uploaded)
			{
				throw new Exception((strip_tags(trim($this->upload->display_errors()))));
			}
			
			$this->load->model('PackageModel', 'Packages');
			$package = $this->Packages->getById($pdetail->pdetail_package);
			$package = $this->Packages->recalculatePackage($package);
		}
		catch (Exception $e)
		{
			$this->result->m = $e->getMessage();
			Stack::push('result', $this->result);
		}
		
		Func::redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function addPdetailJointFoto()
	{
		try
		{
			if (empty($_POST['pdetail_joint_id']) OR
				! is_numeric($_POST['pdetail_joint_id']))
			{
				throw new Exception('Доступ запрещен.');
			}

			$pdetail_joint_id = $_POST['pdetail_joint_id'];
			
			$this->load->model('PdetailJointModel', 'Joints');
			$joint = $this->Joints->getById($pdetail_joint_id);
							
			if (empty($joint))
			{
				throw new Exception('Товар не найден.');
			}
			
			// загрузка файла
			$config['upload_path']			= UPLOAD_DIR."packages/{$joint->package_id}/joint_$pdetail_joint_id/";
			$config['allowed_types']		= 'jpg|gif|jpeg|png|JPG|GIF|JPEG|PNG';
			$config['max_size']				= '4096';
			$config['remove_spaces'] 		= FALSE;
			$config['overwrite'] 			= FALSE;
			$config['encrypt_name'] 		= TRUE;
			$max_width						= 1024;
			$max_height						= 768;
			
			if ( ! is_dir($config['upload_path']) AND
				! (mkdir($config['upload_path'], 0777, true) OR
					chmod($config['upload_path'], 0777)))
			{
				throw new Exception('Ошибка файловой системы. Обратитесь к администратору.');
			}
	
			$this->load->library('upload', $config);
			$uploaded = false;
			
			foreach(array('userfile1', 'userfile2', 'userfile3', 'userfile4', 'userfile5') as $val)
			{
				if ($this->upload->do_upload($val))	
				{
					$uploaded = true;
				
					$uploadedImg = $this->upload->data();
					$imageInfo = getimagesize($uploadedImg['full_path']);
					if ($imageInfo[0]>$max_width || $imageInfo[1]>$max_height)
					{
						$config['image_library']	= 'gd2';
						$config['source_image']		= $uploadedImg['full_path'];
						$config['maintain_ratio']	= TRUE;
						$config['width']			= $max_width;
						$config['height']			= $max_height;

						$this->load->library('image_lib', $config); // загружаем библиотеку
						$this->image_lib->resize(); // и вызываем функцию
					}
				}
			}
			if (! $uploaded)
			{
				throw new Exception((strip_tags(trim($this->upload->display_errors()))));
			}

			$this->load->model('PackageModel', 'Packages');
			$package = $this->Packages->getById($joint->package_id);
			$package = $this->Packages->recalculatePackage($package);
		}
		catch (Exception $e)
		{
			$this->result->m = $e->getMessage();
			Stack::push('result', $this->result);
		}
		
		Func::redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function showPdetailFoto($package_id, $pdetail_id, $filename)
	{
		header('Content-type: image/jpg');
		$this->load->model('PdetailModel', 'PdetailModel');

		if ($pdetail = $this->PdetailModel->getInfo(
			array(
				'pdetail_id' => intval($pdetail_id),
				'pdetail_package' => intval($package_id),
			))) 
		{
			readfile(UPLOAD_DIR . "packages/$package_id/$pdetail_id/$filename");
		}
		
		die();
	}
	
	public function showPdetailJointFoto($package_id, $pdetail_joint_id, $filename)
	{
		header('Content-type: image/jpg');
		$this->load->model('PdetailJointModel', 'Joints');

		if ($this->Joints->getById($pdetail_joint_id)) 
		{
			readfile(UPLOAD_DIR . "packages/$package_id/joint_$pdetail_joint_id/$filename");
		}

		die();
	}
	
	public function showPdetailScreenshot($pdetail_id) 
	{
		header('Content-type: image/jpg');
		$this->load->model('PdetailModel', 'PdetailModel');
		if ($Detail = $this->PdetailModel->getInfo(
			array(
				'pdetail_id' => intval($pdetail_id)
			))) 
		{
			readfile("{$_SERVER['DOCUMENT_ROOT']}/upload/packages/{$Detail->pdetail_package}/{$Detail->pdetail_id}.jpg");
		}
		
		die();
	}

	public function deleteProductP($id)
	{
		parent::deleteProductP($id);
	}
	
	public function exportOrder($order_id)
	{
		try
		{
			$this->load->model('OdetailModel', 'Odetails');
			$this->load->model('OrderModel', 'Order');
			
			$odetails = $this->Odetails->getOrderDetails($order_id);
			$order = $this->Order->getById($order_id);
			$fotos = $this->Order->getOrderFotos($order->order_client, $odetails);
			
			if ($odetails)
			{
				Excel::ExportOrder($order_id, $odetails, $fotos);
			}
		}
		catch(Exception $ex)
		{
			print_r($ex);
		}
	}

	public function exportPackage($package_id)
	{
		try
		{
			$this->load->model('PdetailModel', 'Pdetails');
			$this->load->model('PackageModel', 'Package');
			
			$pdetails = $this->Pdetails->getPackageDetails($package_id);
			$package = $this->Package->getById($package_id);
			$fotos = $this->Package->getPackageFotos($package->package_id, $pdetails);
			$joint_fotos = $this->Package->getPackageJointFotos($package->package_id, $pdetails);
			
			if ($pdetails)
			{
				Excel::ExportPackage($package_id, $pdetails, $fotos, $joint_fotos);
			}
		}
		catch(Exception $ex)
		{
			print_r($ex);
		}
	}

	public function importOrder($order_id)
	{
		try
		{
			$this->load->model('OdetailModel', 'Odetails');
			$this->load->model('OrderModel', 'Order');
			
			$order = $this->Order->getById($order_id);
			$odetails = $this->Odetails->getOrderDetails($order_id);
			$fotos = $this->Order->getOrderFotos($order->order_client, $odetails);
			
			if ($odetails)
			{
				Excel::ImportOrder(
					$order_id, 
					$order->order_client, 
					$order->order_manager,
					$order->order_country, 
					$odetails, 
					$fotos);
			}
		}
		catch(Exception $ex)
		{
		}
		
		Func::redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function joinPackageFotos()
	{
		parent::joinPackageFotos();
	}
	
	public function deletePdetailJoint($package_id, $pdetail_joint_id)
	{
		parent::deletePdetailJoint($package_id, $pdetail_joint_id);
	}

	public function updateProductAjax() 
	{
		parent::updateProductAjax();
	}

	public function payments()
	{
		parent::showAllPayments();
	}

	public function showOpenPayments($order_id)
	{
		parent::showPayments($order_id, 'open');
	}

	public function showPayedPayments($order_id)
	{
		parent::showPayments($order_id, 'payed');
	}

	public function showAllOpenPayments()
	{
		parent::showAllPayments('open');
	}

	public function showAllPayedPayments()
	{
		parent::showAllPayments('payed');
	}

	public function update_payment_amount($order_id, $payment_id, $amount)
	{
		parent::update_payment_amount($order_id, $payment_id, $amount);
	}

	public function update_payment_status($order_id, $payment_id, $status)
	{
		parent::update_payment_status($order_id, $payment_id, $status);
	}

	public function deletePayment($oid)
	{
		parent::deletePayment($oid);
	}

	public function payment()
	{
		parent::showO2iComments();
	}

	public function showPaymentFoto($oid, $filename)
	{
		$this->load->model('Order2InModel', 'Order2in');

		if ($o2i = $this->Order2in->getInfo(array(
			'order2in_to' => $this->user->user_id,
			'order2in_id' => intval($oid))))
		{
			header('Content-type: image/jpg');
			readfile($_SERVER['DOCUMENT_ROOT'] . "/upload/orders2in/$oid/$filename");
		}

		die();
	}

	public function updatePayment($o2i_id)
	{
		parent::updatePayment($o2i_id);
	}

	public function addPaymentComment($payment_id, $comment_id = NULL)
	{
		parent::addPaymentComment($payment_id, $comment_id);
	}

	public function addPaymentFoto()
	{
		parent::addPaymentFoto();
	}

	public function deletePaymentFoto($o2i_id, $filename)
	{
		parent::deletePaymentFoto($o2i_id, $filename);
	}

	public function update_payment_amount_local($order_id, $payment_id, $amount_local)
	{
		try
		{
			if ( ! is_numeric($order_id) OR
				! is_numeric($payment_id))
			{
				throw new Exception('Доступ запрещен.');
			}

			// роли и разграничение доступа
			$order = $this->getPrivilegedOrder(
				$order_id,
				"Заказ недоступен.");

			$this->load->model('OrderModel', 'Orders');
			$this->load->model('Order2InModel', 'Payments');

			// роли и разграничение доступа
			$payment = $this->getPrivilegedOrder2In(
				$payment_id,
				'Заявка не найдена. Попробуйте еще раз.');

			if ($payment->order2in_status == 'payed' AND
				$this->user->user_group != 'admin')
			{
				throw new Exception('Выплаченные заявки запрещено изменять.');
			}

			$payment->order2in_amount_local = $amount_local;

			// сохранение результатов
			$this->Orders2In->updateAmountLocal($payment_id, $payment->order2in_amount_local);

			// пересчитываем заказ
			if ( ! $this->Orders->recalculate($order))
			{
				throw new Exception('Невожможно пересчитать стоимость заказа. Попоробуйте еще раз.');
			}

			// отправляем пересчитанные детали заказа
			$response = $this->prepareOrderUpdateJSON($order);
		}
		catch (Exception $e)
		{
			$response['is_error'] = TRUE;
			$response['message'] = $e->getMessage();
		}

		print(json_encode($response));
	}
}