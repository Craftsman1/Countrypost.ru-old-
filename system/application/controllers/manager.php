<?php
require_once BASE_CONTROLLERS_PATH.'ManagerBaseController'.EXT;

class Manager extends ManagerBaseController {

	function __construct()
	{
		parent::__construct();
		Breadcrumb::setCrumb(array('/' => 'Главная'), 0);
		Breadcrumb::setCrumb(array('/manager/orders/' => 'Мои заказы'), 1, TRUE);
	}
	
	function index()
	{
		Func::redirect(BASEURL.$this->cname.'/orders');
	}

	public function autocompleteClient($query)
	{
		$this->load->model('ClientModel', 'Clients');
		$ids = $this->Clients->autocompleteManager(intval($query), $this->user->user_id);
		
		if ($ids)
		{
			echo "[$ids]";
		}
		else
		{
			echo '[]';
		}
	}
	
	public function updateOdetailStatuses()
	{
		parent::updateOdetailStatuses();
	}
	
	protected function processOrdersFilter($filter)
	{
		$filter->search_id	= Check::txt('search_id', 11, 1, '');
		$filter->id_type = Check::txt('id_type', 13, 5, '');
		$filter->id_status = Check::txt('id_status', 20, 1, '');
		$filter->country_to = Check::int('country_to');

		if (empty($filter->id_type))
		{
			$filter->search_id = '';
			$filter->search_client = '';
		}

		return $filter;
	}

	public function updateOrderDetails()
	{
		parent::updateOrderDetails();
	}

	public function updateOpenOrderStatus($param1, $param2, $order, $status)
	{
		parent::updateOrderStatus($order, $status);
		$this->showOpenOrders();
	}

	public function updatePayedOrderStatus($param1, $param2, $order, $status)
	{
		parent::updateOrderStatus($order, $status);
		$this->showPayedOrders();
	}

	public function updateSentOrderStatus($param1, $param2, $order, $status)
	{
		parent::updateOrderStatus($order, $status);
		$this->showSentOrders();
	}

	public function orders()
	{
		$this->showOrders();
	}

	public function showOpenOrders()
	{
		$this->showOrders('open');
	}
	
	public function showPayedOrders()
	{
		$this->showOrders('payed');
	}
	
	public function showSentOrders()
	{
		$this->showOrders('sent');
	}

	public function showBidOrders()
	{
		$this->showOrders('bid');
	}

	public function order()
	{
		parent::showOrderDetails();
	}

	protected function showOrderBreadcrumb($order, $bids)
	{
		$index = 1;

		if ($order->order_manager == $this->user->user_id)
		{
			$index = 2;
		}
		else if (empty($order->order_manager) AND
			$bids)
		{
			foreach ($bids as $bid)
			{
				if ($bid->manager_id == $this->user->user_id)
				{
					$index = 2;
					break;
				}
			}
		}

		Breadcrumb::setCrumb(array(
			"/manager/order/{$order->order_id}" => "Заказ №{$order->order_id}"
		), $index, TRUE);
	}

	public function joinProducts($order_id)
	{
		parent::joinProducts($order_id);
	}
	
	public function removeOdetailJoint($order_id, $odetail_joint_id)
	{
		parent::removeOdetailJoint($order_id, $odetail_joint_id);
	}
	
	public function addOrderComment($order_id, $comment_id = null)
	{
		parent::addOrderComment($order_id, $comment_id);
	}
	
	public function addBidComment($bid_id, $comment_id = null)
	{
		parent::addBidComment($bid_id, $comment_id);
	}
	
	public function showScreen($oid=null)
	{
		header('Content-type: image/jpg');
		$this->load->model('OdetailModel', 'OdetailModel');
		if ($Detail = $this->OdetailModel->getInfo(array('odetail_id' => intval($oid)))) 
		{
			readfile("{$_SERVER['DOCUMENT_ROOT']}/upload/orders/".$Detail->odetail_client."/$oid.jpg");
		}
		die();
	}
	
	public function showOutMoney()
	{
		$this->load->model('Order2outModel', 'Order2out');
		$this->load->model('CurrencyModel', 'Currencies');
		$this->load->model('CountryModel', 'Countries');
		$this->load->model('ManagerModel', 'Managers');
		
		$Orders = $this->Order2out->getUserOrders($this->user->user_id);
		
		/* пейджинг */
		$this->per_page = $this->per_page_o2o;
		$this->init_paging();		
		$this->paging_count = count($Orders);
		
		if ($Orders)
		{
			$Orders = array_slice($Orders, $this->paging_offset, $this->per_page);
		}
		
		$manager = $this->Managers->getById($this->user->user_id);
		$country = $this->Countries->getById($manager->manager_country);
		
		$view = array (
			'Orders' => $Orders,
			'statuses'	=> $this->Order2out->getStatuses(),
			'rate' => $this->Currencies->getCrossRate($country->country_currency),
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
	
	public function showPayedOutMoney() 
	{
		$this->load->model('Order2outModel', 'Order2out');
		$this->load->model('CurrencyModel', 'Currencies');
		$this->load->model('CountryModel', 'Countries');
		$this->load->model('ManagerModel', 'Managers');
		
		$Orders = $this->Order2out->getPayedUserOrders($this->user->user_id);
		
		/* пейджинг */
		$this->per_page = $this->per_page_o2o;
		$this->init_paging();		
		$this->paging_count = count($Orders);
		
		if ($Orders)
		{
			$Orders = array_slice($Orders, $this->paging_offset, $this->per_page);
		}
		
		$manager = $this->Managers->getById($this->user->user_id);
		$country = $this->Countries->getById($manager->manager_country);
		
		$view = array (
			'Orders' => $Orders,
			'statuses'	=> $this->Order2out->getStatuses(),
			'rate' => $this->Currencies->getCrossRate($country->country_currency),
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
	
	public function order2out() 
	{
		// превалидация
		$is_local_account = empty($_POST['ignore_local']);
		$is_usd_account = empty($_POST['ignore_usd']);
		
		if (!$is_local_account &&
			!$is_usd_account)
		{
			throw new Exception('Введите сумму платежа.');
		}
		
		Check::reset_empties();	
		$order2out = new stdClass();
	
		// сначала проверяем доллары
		if ($is_usd_account)
		{
			$order2out->order2out_ammount = Check::int('ammount');
			$empties = Check::get_empties();
		}
			
		// потом местную валюту
		if ($is_local_account)
		{
			$order2out->order2out_currency = $this->session->userdata('manager_currency');
			$order2out->order2out_ammount_local = Check::int('ammount_local');
		}
		
		$empties = Check::get_empties();
		
		try
		{
			// валидация
			if ($empties)
			{
				throw new Exception('Введите сумму платежа.');
			}
			
			if ($is_local_account)
			{
				$this->load->model('ManagerModel', 'Managers');
				$manager = $this->Managers->getById($this->user->user_id);
				
				if ($manager->manager_balance_local < $order2out->order2out_ammount_local)
				{
					throw new Exception('У Вас недостаточно средств в местной валюте для вывода.');
				}
			}
			
			if ($is_usd_account)
			{
				$this->load->model('UserModel', 'Users');
				$user = $this->Users->getById($this->user->user_id);
				
				if ($user->user_coints < $order2out->order2out_ammount)
				{
					throw new Exception('У Вас недостаточно средств для вывода.');
				}
			}
			
			// добавляем заявку
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

			// создаем платеж в долларах
			$this->load->model('PaymentModel', 'Payment');

			if ($is_usd_account)
			{
				$payment_obj = new stdClass();
				$payment_obj->payment_from			= $this->user->user_id;
				$payment_obj->payment_to			= 0;
				$payment_obj->payment_amount_from	= $order2out->order2out_ammount;
				$payment_obj->payment_amount_to		= 0;
				$payment_obj->payment_amount_tax	= 0;
				$payment_obj->payment_purpose		= 'заявка на вывод';
				$payment_obj->payment_type			= 'salary';
				$payment_obj->payment_comment		= '№ '.$order2out->order2out_id;

				if (!$this->Payment->makePayment($payment_obj)) 
				{
					throw new Exception('Ошибка перевода средств. Попробуйте еще раз.');
				}			
				
				$this->session->set_userdata(array('user_coints' => $this->user->user_coints - $payment_obj->payment_amount_from));
			}
			
			// создаем платеж в местной валюте
			if ($is_local_account)
			{
				$payment_obj = new stdClass();
				$payment_obj->payment_from			= $this->user->user_id;
				$payment_obj->payment_to			= 0;
				$payment_obj->payment_amount_from	= $order2out->order2out_ammount_local;
				$payment_obj->payment_amount_to		= 0;
				$payment_obj->payment_amount_tax	= 0;
				$payment_obj->payment_purpose		= 'заявка на вывод';
				$payment_obj->payment_type			= 'salary';
				$payment_obj->payment_comment		= '№ '.$order2out->order2out_id;

				$payment_obj->payment_currency = $order2out->order2out_currency;
			
				if (!$this->Payment->makePaymentLocal($payment_obj)) 
				{
					throw new Exception('Ошибка перевода средств в местной валюте. Попробуйте еще раз.');
				}			
				
				$this->session->set_userdata(array('manager_balance_local' => $this->session->userdata('manager_balance_local') - $payment_obj->payment_amount_from));
			}

			$this->db->trans_commit();
			$this->result->type = 1;
			$this->result->m = 'Заявка на вывод денег успешно добавлена.';
			$this->result->e = 1;
			
			// уведомления
			$this->load->model('ManagerModel', 'Managers');
			
			Mailer::sendAdminNotification(
				Mailer::SUBJECT_NEW_ORDER2OUT, 
				Mailer::NEW_ORDER2OUT_MANAGER_NOTIFICATION, 
				$order2out->order2out_user,
				$order2out->order2out_id, 
				0,
				"http://countrypost.ru/admin/showManagerOrdersToOut",
				$this->Managers,
				null);
		}
		catch (Exception $e)
		{
			$this->db->trans_rollback();
			$this->result->type = 1;
			$this->result->e	= -1;
			$this->result->m	= $e->getMessage();
		}
		
		Stack::push('result', $this->result);		
		Func::redirect(BASEURL.$this->cname.'/showOutMoney');
	}
	
	public function deleteOrder2out($oid) 
	{
		parent::deleteOrder2out($oid);
	}
	
	public function showO2oComments()
	{
		parent::showO2oComments();
	}
	
	public function showO2iComments()
	{
		parent::showO2iComments();
	}
	
	public function addO2oComment()
	{
		parent::addO2oComment();
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
			$order = $this->Orders->getManagerOrderById($this->uri->segment(3), $this->user->user_id);

			if (!$order)
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
			
			$this->session->set_userdata(array('user_coints' => ($this->user->user_coints - $payment_manager->payment_amount_from)));
			$this->session->set_userdata(array('manager_balance_local' => ($this->session->userdata('manager_balance_local') - $payment_manager_local->payment_amount_from)));
			$this->result->m = 'Недоставленные товары успешно возмещены клиенту.';

		}
		catch (Exception $e)
		{
			$this->db->trans_rollback();
		
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
		}
		
		// открываем заказы
		Stack::push('result', $this->result);
		Func::redirect($_SERVER['HTTP_REFERER']);
	}

	public function sendOrderConfirmation($order_id)
	{
		parent::sendOrderConfirmation((int) $order_id);
	}
	
	public function addProductManualAjax() 
	{
		parent::addProductManualAjax();
	}
	
	public function filterOrders()
	{
		$this->filter('Orders', 'orders/0/ajax');
	}
	
	public function acceptOrder($order_id)
	{
		parent::connectOrderToManager($order_id, $this->user->user_id);
	}
	
	public function filterPaymentHistory()
	{
		$this->filter('paymentHistory', 'showPaymentHistory');
	}
	
	public function updateProductAjax()
	{
		parent::updateProductAjax();
	}
	
	public function addBid()
	{
		try 
		{
			$order_id = Check::int('order_id');
			if (empty($order_id))
			{				
				throw new Exception('Невозможно создать заказ.');
			}
						
			$this->load->model('OrderModel', 'Orders');
			$order = $this->Orders->getById($order_id);

			if (empty($order))
			{				
				throw new Exception('Заказ не найден.');
			}
			
			if ( ! empty($order->order_manager))
			{				
				throw new Exception('Извините, посредник уже выбран.');
			}
			
			Check::reset_empties();
			$bid = new stdClass();

			$bid->order_id = $order_id;
			$bid->manager_id = $this->user->user_id;
			$bid->client_id = $order->order_client;
			$bid->manager_tax = Check::float('manager_tax');
			$bid->delivery_cost = Check::float('order_delivery_cost');
			$bid->delivery_name = Check::str('delivery_name', 255, 1);
			$bid->total_cost = Check::float('order_total_cost');
			
			$empties = Check::get_empties();
			$bid->foto_tax = Check::float('foto_tax');
			
			if ($empties) 
			{
				throw new Exception('Некоторые поля не заполнены. Попробуйте еще раз.');
			}

			$this->load->model('BidModel', 'Bids');
			
			if (!($bid = $this->Bids->addBid($bid))) 
			{
				throw new Exception('Ошибка создания предложения. Обратитесь к администрации сервиса.');
			}

			$this->load->model('ManagerModel', 'Managers');
			$this->processStatistics($bid, array(), 'manager_id', $this->user->user_id, 'manager');
			
			// комменты
			$comment = new stdClass();
			$comment->bid_id = $bid->bid_id;
			$comment->message = Check::txt('comment', 8096, 1);
			$comment->user_id = $this->user->user_id;

			// сохранение результатов
			if ( ! empty($comment->message) AND
				$comment->message != '<p></p>')
			{
				$this->load->model('BidCommentModel', 'Comments');

				$new_comment = $this->Comments->addComment($comment);
				if ( ! $new_comment)
				{
					throw new Exception('Комментарий не добавлен. Попробуйте еще раз.');
				}

				$new_comment->statistics = $bid->statistics;
				
				$bid->comments = array();
				$bid->comments[] = $new_comment;
			}
			
			$this->load->model('CountryModel', 'Countries');
			$this->load->model('OdetailModel', 'Odetails');

			$view['selfurl'] = BASEURL.$this->cname.'/';
			$view['viewpath'] = $this->viewpath;
			$view['bid'] = $bid;
			$view['order'] = $order;
			$view['odetails'] = $this->Odetails->getOrderDetails($view['order']->order_id);
			
			$this->Orders->prepareOrderView($view, 
				$this->Countries, 
				$this->Odetails);
			
			$this->load->view("/main/elements/orders/bid", $view);
		}
		catch (Exception $e)
		{
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
			$this->load->model('ManagerModel', 'Manager');
			$manager = $this->Manager->getById($this->user->user_id);

			// валидация пользовательского ввода
			$manager->cashback_limit = Check::int('limit');

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
			
			$manager->manager_name			= Check::str('fio', 255, 0);
			$manager->manager_surname		= NULL;
			$manager->manager_otc			= NULL;
			$manager->manager_country		= Check::int('country');
			$manager->skype					= Check::str('skype', 255, 0);
			$manager->website				= Check::str('website', 4096, 0);
			$manager->about_me				= Check::str('about', 65535, 0);
			$manager->city					= Check::str('city', 255, 1);
			$manager->is_mail_forwarding 	= Check::chkbox('mf');
			$manager->is_internal_payments 	= Check::chkbox('payments');
			
			$empties = Check::get_empties();			
			
			if ($empties)
			{
				throw new Exception('Одно или несколько полей не заполнено. Попробуйте еще раз.');
			}
			
			$this->db->trans_begin();
					
			// наконец, все сохраняем
			$user = $this->User->updateUser($user);
			$manager = $this->Manager->updateManager($manager);
			
			if ( ! $user || ! $manager)
			{
				throw new Exception('Партнер не сохранен. Попробуйте еще раз.');
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

	public function saveBlog()
	{
		try
		{
			// находим новость
			$blog_id = Check::int('blog_id');
			$this->load->model('BlogModel', 'Blogs');
			
			if ($blog_id)
			{
				$blog = $this->Blogs->getById($blog_id);
			}
			else
			{
				$blog = new stdClass();
			}

			// валидация пользовательского ввода
			Check::reset_empties();
			$blog->title = Check::str('title', 255, 1);
			$blog->message = Check::str('message', 65535, 1);
			$blog->user_id = $this->user->user_id;
			
			$empties = Check::get_empties();			
			
			if ($empties)
			{
				throw new Exception('Одно или несколько полей не заполнено. Попробуйте еще раз.');
			}
			
			// сохраняем
			$blog = $this->Blogs->addBlog($blog);
			
			if (empty($blog))
			{
				throw new Exception('Новость не сохранена. Попробуйте еще раз.');
			}
		}
		catch (Exception $e) 
		{
		}
	}

	public function savePricelist()
	{
		try
		{
			// находим партнера
			$this->load->model('ManagerModel', 'Managers');
			$manager = $this->Managers->getById($this->user->user_id);

			// валидация пользовательского ввода
			Check::reset_empties();
			$manager->order_tax = Check::int('order_tax');
			$manager->min_order_tax = Check::int('min_order_tax');
			$manager->join_tax = Check::int('join_tax');
			$manager->foto_tax = Check::int('foto_tax');
			$manager->insurance_tax = Check::int('insurance_tax');
			$manager->pricelist_description = Check::str('pricelist_message', 65535, 1);

			$empties = Check::get_empties();

			if ($empties)
			{
				throw new Exception('Одно или несколько полей не заполнено. Попробуйте еще раз.');
			}

			// сохраняем
			$manager = $this->Managers->updateManager($manager);

			if (empty($manager))
			{
				throw new Exception('Тарифы не сохранены. Попробуйте еще раз.');
			}
		}
		catch (Exception $e)
		{
		}
	}

	public function savePaymentTypes()
	{
		try
		{
			// находим партнера
			$this->load->model('ManagerModel', 'Managers');
			$manager = $this->Managers->getById($this->user->user_id);

			// валидация пользовательского ввода
			Check::reset_empties();
			$manager->payments_description = Check::str('payment_types', 65535, 0);

			$empties = Check::get_empties();

			if ($empties)
			{
				throw new Exception('Одно или несколько полей не заполнено. Попробуйте еще раз.');
			}

			// сохраняем
			$manager = $this->Managers->updateManager($manager);

			if (empty($manager))
			{
				throw new Exception('Способы оплаты не сохранены. Попробуйте еще раз.');
			}
		}
		catch (Exception $e)
		{
		}
	}

	public function saveDelivery()
	{
		try
		{
			// валидация пользовательского ввода
			Check::reset_empties();
			$payments_description = Check::str('delivery_description', 65535, 0);
			$country_id	= Check::int('delivery_country');

			$empties = Check::get_empties();

			if ($empties)
			{
				throw new Exception('Одно или несколько полей не заполнено. Попробуйте еще раз.');
			}

			// сохраняем
			$this->load->model('ManagerModel', 'Managers');
			$this->Managers->saveManagerDelivery($this->user->user_id, $country_id, strval($payments_description));
		}
		catch (Exception $e)
		{
		}
	}

	public function saveAddress()
	{
		try
		{
			// находим партнера
			$this->load->model('ManagerModel', 'Managers');
			$manager = $this->Managers->getById($this->user->user_id);

			// валидация пользовательского ввода
			Check::reset_empties();
			$manager->manager_address = Check::str('address_en', 1024, 1);
			$manager->manager_address_local = Check::str('address', 1024, 1);
			$manager->manager_phone = Check::str('phone', 255, 1);
			$manager->manager_address_description = Check::str('address_description', 65535, 1);

			$empties = Check::get_empties();

			if ($empties)
			{
				throw new Exception('Одно или несколько полей не заполнено. Попробуйте еще раз.');
			}

			// сохраняем
			$manager = $this->Managers->updateManager($manager);

			if (empty($manager))
			{
				throw new Exception('Адреса не сохранены. Попробуйте еще раз.');
			}
		}
		catch (Exception $e)
		{
			print_r($manager);
		}
	}

	public function updatePerPage($per_page)
	{
		if ( ! is_numeric($per_page))
		{
			throw new Exception('Доступ запрещен.');
		}

		$this->session->set_userdata(array('orders_per_page' => $per_page));
		Func::redirect($_SERVER['HTTP_REFERER']);
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

			// позволяет ли текущий статус редактирование
			$editable_statuses = $this->Orders->getEditableStatuses($this->user->user_group);

			if ( ! in_array($order->order_status, $editable_statuses))
			{
				throw new Exception('Доступ запрещен.');
			}

			// валидация пользовательского ввода
			$old_status = $order->order_status;
			$old_tracking_no = $order->tracking_no;

			$order->order_status = Check::str('order_status', 20, 1, '');
			$order->tracking_no = Check::str('tracking_no', 255, 1, '');

			$statuses = $this->Orders->getAllStatuses();

			// валидируем статус
			if (empty($statuses[$order->order_type][$order->order_status]) OR
				$order->order_status == 'pending')
			{
				throw new Exception('Некорректный статус.');
			}

			// сохранение результатов
			$this->Orders->saveOrder($order);
		}
		catch (Exception $e)
		{
			print_r($e);
		}
	}

	public function closeOrder($order_id)
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

			// позволяет ли текущий статус редактирование
			$editable_statuses = $this->Orders->getEditableStatuses($this->user->user_group);

			if ( ! in_array($order->order_status, $editable_statuses))
			{
				throw new Exception('Доступ запрещен.');
			}

			// валидация пользовательского ввода
			$order->order_status = 'completed';
			$order->tracking_no = Check::str('tracking_no', 255, 1, '');
			$order->sent_date = date('Y-m-d H:i:s');

			// сохранение результатов
			$this->Orders->saveOrder($order);
		}
		catch (Exception $e)
		{
			print_r($e);
		}
	}
}