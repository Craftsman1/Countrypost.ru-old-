<? if (!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

abstract class BaseController extends Controller 
{
	public $user;
	public $cname;
	public $viewpath;
	
	var $per_page = 10;
	var $per_page_o2o = 50;
	var $page = 0;
	var $paging_count = 0;
	var $paging_uri_segment = 0;
	var $paging_offset = 0;
	var $paging_base_url = '';	 

	/**
	 * специальная переменная, что-то вроде интерфейсного обекта,
	 * служит для унифицированной передачи данных по какой либо операции
	 */
	public $result;
	
	public function __construct()
	{
		header("Content-Type: text/html; charset=UTF-8");
		parent::Controller();

		$this->user			= Check::user();
		$this->cname		= $this->uri->rsegment(1) ? $this->uri->rsegment(1) : 'main';
		$this->viewpath		= '/'.$this->cname.'/';
		$this->load->helper('humanForm');

		/**
		 * получаем данные из стека, если они там есть
		 */
		if (Stack::size('result'))
		{
			$this->result = Stack::shift('result');
		}
		else
		{
			$this->result		= new stdClass();
			$this->result->e	= 0; // код ошибки
			$this->result->m	= ''; // сообщение
			$this->result->d	= ''; // возвращаемые данные			
		}
		
		View::$main_view	= '/'.$this->cname.'/index';

		// Обновляем баланс
		if ($this->user)
		{
			$user = $this->User->getById($this->user->user_id);
			$this->session->set_userdata((array) $user);
			$this->user	= Check::user();
		}
		
		View::$data	= array(
			'user'		=> $this->user,
			'pageinfo'	=> array(
				'cname'		=> $this->uri->rsegment(1),
				'mname'		=> $this->uri->rsegment(2),
				'params'	=> $this->uri->uri_to_assoc(),
			),
			/**
			 * example: /admin/
			 * на самом деле, тк у нас зонная модель доступа,
			 * тут лучше отталкиваться от группы к которой пренадлежит пользователь
			 * а не от названия основного контролера
			 * (возможно придется переделать, тк я уже вижу как минимум одну проблемму,
			 * которая скорее всего возникнит)
			 * однако, не забываем что эти значения всегда можно переопределить
			 * 
			 */
			'viewpath'	=> $this->viewpath,
			// example: http://omni.kio.samaraauto.ru/kio.php/admin/
			'selfurl'	=> $this->config->item('base_url').$this->cname.'/',
			// postback
			'result'	=> $this->result,
			// добавляем информацию для счётчиков в меню
			'user_count' => $this->User->getUserCount(),
		);

		//подгружаем доп данные для клиента
		if ($this->user AND $this->user->user_group	== 'client'){
			$this->loadClientData();
		}
	}

	/**
	 * Данные по адресам и о самом клиенте
	 * 
	 */
	private function loadClientData(){

		// подгружаем клиента
		$this->load->model('ClientModel', '__Clients');
		$this->__client	= $this->__Clients->getById($this->user->user_id);
		View::$data['client'] = $this->__client;

		/*
		// подгружаем партнеров
		$this->load->model('ManagerModel', '__Managers');
		$this->__partners = $this->__Managers->getClientManagersById($this->user->user_id);
		
		if (is_array($this->__partners))
		{
			$this->__partners = Func::reIndexArrayOfObjects($this->__partners, $this->__Managers->getPK());
		}
			
		if (is_array($this->__partners)) 
		{
			foreach ($this->__partners as $partner)
			{
				$partner->country_address = Func::CorrectCountryAdjective($partner->country_name);
				$partner->country_address2 = Func::CorrectCountryAdjective2($partner->country_name);
			}
		}
		
		View::$data['partners']	= $this->__partners;
		*/
	}	
	
	protected function showOrders($orderStatus = NULL, $pageName = NULL)
	{
		try
		{
			$this->load->model('OrderModel', 'Orders');
		    
			// роли и разграничение доступа
			if ($this->user->user_group == 'admin')
			{
				// обработка фильтра
				$view['filter'] = $this->initFilter($orderStatus.'Orders');
				$view['filter']->order_statuses = $this->Orders->getFilterStatuses();
				
				$this->load->model('ManagerModel', 'Managers');
				$view['managers'] = $this->Managers->getManagersData();
				
				// отображаем заказы
				$view['orders'] = $this->Orders->getOrders(
					$view['filter'], 
					$orderStatus);
					
				// доступ к выбору заказов
				if ($orderStatus == 'not_payed')
				{
					$view['unassigned_orders'] = $this->Orders->getUnassignedOrders(
						$view['filter'], 
						$orderStatus);
				}
			}
			else if ($this->user->user_group == 'manager')
			{
				// обработка фильтра
				$view['filter'] = $this->initFilter('Orders');
				$view['filter']->order_statuses = $this->Orders->getFilterStatuses();

				// достаем статус из фильтра
				if (empty($orderStatus))
				{
					$orderStatus = $this->Orders->getViewStatus(
						$this->user->user_group,
						$view['filter']->id_status);
				}

				// находим заказы по статусу и фильтру
				$view['orders'] = $this->Orders->getOrders(
					$view['filter'],
					$orderStatus,
					NULL,
					$this->user->user_id);

				$view['orders_count'] = count($view['orders']);

				// страны для фильтра
				$this->load->model('CountryModel', 'Country');
				$view['countries'] = $this->Country->getList();
			}
			else if ($this->user->user_group == 'client')
			{
				// отображаем заказы
				$this->load->model('CountryModel', 'CountryModel');
				$this->load->model('OdetailModel', 'OdetailModel');
				
				$Orders		= $this->Orders->getOrders(NULL, $orderStatus, $this->user->user_id, NULL, ($orderStatus == 'open'));
				$Odetails	= $this->OdetailModel->getFilteredDetails(array('odetail_client' => $this->user->user_id, 'odetail_order' => 0));
				$Countries	= $this->CountryModel->getClientAvailableCountries($this->user->user_id);

				$view = array (
					'orders'	=> $Orders,
					'Odetails'	=> $Odetails,
					'Countries'	=> $Countries,
				);
				
				$view['payable_statuses'] = $this->Orders->getPayableStatuses($this->user->user_group);
				
				$this->load->model('CurrencyModel', 'Currencies');
				$view += array (
					'cur_currency'=>DEFAULT_CURRENCY,
					'currencies'=>$this->Currencies->getExchangeCurrencies(),
					'rate_usd' => $this->Currencies->getExchangeRate(DEFAULT_CURRENCY, 'USD', 'client'),
					'rate_kzt' => $this->Currencies->getExchangeRate(DEFAULT_CURRENCY, 'KZT', 'client'),
					'rate_uah' => $this->Currencies->getExchangeRate(DEFAULT_CURRENCY, 'UAH', 'client'),
					'rate_rur' => $this->Currencies->getExchangeRate(DEFAULT_CURRENCY, 'RUB', 'client')
				);
			}

			$view['order_types'] = $this->Orders->getOrderTypes();

			// показываем статистику
			$this->putStatistics($view);
			
			if ( ! $view['orders'])
			{
				$this->result->m = 'Заказы не найдены.';
				Stack::push('result', $this->result);
			}
			
			/* пейджинг */
			$per_page = isset($this->session->userdata['orders_per_page']) ?
			$this->session->userdata['orders_per_page'] : NULL;
			$per_page = isset($per_page) ? $per_page : $this->per_page;
			$this->per_page = $per_page;

			$this->init_paging();
			$this->paging_count = $view['orders'] ? count($view['orders']) : 0;
			
			if ($view['orders'])
			{
				$view['orders'] = array_slice($view['orders'], $this->paging_offset, $this->per_page);
			}
			
			$view['pager'] = $this->get_paging();
			$view['per_page'] = $per_page;
			$view['statuses'] = $this->Orders->getAllStatuses();
			$view['view_status'] = ucfirst($orderStatus);

			if (empty($pageName))
			{
				$pageName = "show{$view['view_status']}Orders";
			}
			
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}
		
		// парсим шаблон
		if ($this->uri->segment(4) == 'ajax')
		{
			$view['selfurl'] = $this->config->item('base_url').$this->cname.'/';
			$view['viewpath'] = $this->viewpath;
			$this->load->view($this->viewpath."ajax/$pageName", $view);
		}
		else
		{
			View::showChild($this->viewpath."pages/showOrders", $view);
		}
	}
	
	protected function showOrderComments($flag = FALSE)
	{
		try
		{
			// безопасность
			if ( ! $this->user OR
				 ! $this->user->user_id OR
				 ! is_numeric($this->uri->segment(3)))
			{
				throw new Exception('Доступ запрещен.');
			}
		
			// роли и разграничение доступа
			$order = $this->getPrivilegedOrder(
				$this->uri->segment(3), 
				'Невозможно отобразить комментарии. Заказ недоступен.');
			
			$this->load->model('ManagerModel', 'Managers');
			$view['Managers']	=	$this->Managers->getById($order->order_manager);
			
			$this->load->model('ClientModel', 'Clients');
			$view['Clients']	=	$this->Clients->getById($order->order_client);

			// показываем комментарии к заказу
			$this->load->model('OCommentModel', 'Comments');
			$view['comments'] = $this->Comments->getCommentsByOrderId($this->uri->segment(3));
			
			// сбрасываем флаг нового комментария
			if ($this->user->user_group == 'client' AND
				$order->comment_for_client)
			{
				$order->comment_for_client = 0;
				$view['order'] = $this->Orders->saveOrder($order);
			}
			else if ($this->user->user_group == 'manager' AND
				$order->comment_for_manager)
			{
				$order->comment_for_manager = 0;
				$view['order'] = $this->Orders->saveOrder($order);
			}
			else
			{
				$view['order'] = $order;
			}

			if ( ! $view['order'])
			{
				throw new Exception('Ошибка отображения комментариев. Попробуйте еще раз.');
			}
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
			
			// открываем новые заказы
			Func::redirect($this->config->item('base_url').$this->cname.'/showNewOrders');
			return;
		}

		// отображаем комментарии
		if ($flag === TRUE) return $view;
		
		View::showChild($this->viewpath.'/pages/showOrderComments', $view);
	}
	
	protected function showOrderDetails()
	{
		try
		{
			// безопасность
			if ( ! is_numeric($this->uri->segment(3)))
			{
				throw new Exception('Доступ запрещен.');
			}

			// роли и разграничение доступа
			// на этом этапе незалогиненные пользователи попадают на главную страницу
			$view['order'] = $this->getPrivilegedOrder(
				$this->uri->segment(3), 
				'Заказ не найден. Попробуйте еще раз.');

			// подгружаем модели
			$this->load->model('BidModel', 'Bids');
			$this->load->model('BidCommentModel', 'Comments');
			$this->load->model('ManagerModel', 'Managers');
			$this->load->model('ClientModel', 'Clients');
			$this->load->model('OdetailModel', 'Odetails');
			$this->load->model('OdetailJointModel', 'Joints');
			$this->load->model('CountryModel', 'Countries');
			$this->load->model('AddressModel', 'Addresses');
			$this->load->model('CurrencyModel', 'Currencies');

			$chosen_bid = FALSE;
			$statistics = array();

			// детали заказа
			$view['odetails'] = $this->Odetails->getOrderDetails($view['order']->order_id);
			$view['joints'] = $this->Joints->getOrderJoints($view['order']->order_id);
			$this->Orders->prepareOrderView($view);

			// страны
			$view['countries'] = $this->Countries->getArray();

			// предложения: никаких ограничений доступа, показываем все
			$view['bids'] = $this->Bids->getBids($view['order']->order_id);
			
			$view['exchangeRates'] = array (
			'rate_usd' => $this->Currencies->getExchangeRate($view['order']->order_currency, 'USD', 'client'),
			'rate_kzt' => $this->Currencies->getExchangeRate($view['order']->order_currency, 'KZT', 'client'),
			'rate_uah' => $this->Currencies->getExchangeRate($view['order']->order_currency, 'UAH', 'client'),
			'rate_rur' => $this->Currencies->getExchangeRate($view['order']->order_currency, 'RUB', 'client')
			);

			foreach ($view['bids'] as $bid)
			{
				// находим допрасходы
				$bid->extra_taxes = $this->Bids->getBidExtras($bid->bid_id);

				// статистика предложения
				$this->processStatistics($bid, $statistics, 'manager_id', $bid->manager_id, 'manager');

				// выбранное клиентом предложение
				if ($bid->manager_id == $view['order']->order_manager)
				{
					$chosen_bid = $bid;
				}

				// комментарии
				$bid->comments = $this->Comments->getCommentsByBidId($bid->bid_id);
                // кол-во новых комментариев
                $bid->new_comments = count($this->Comments->getNewCommentsByOrderIdForUser($bid->bid_id,$this->user->user_id));

				if (empty($bid->comments))
				{
					continue;
				}

				// находим данные комментатора для каждого коммента
				foreach ($bid->comments as $comment)
				{
					if ($comment->user_id == $bid->client_id)
					{
						$this->processStatistics($comment, $statistics, 'user_id', $comment->user_id, 'client');
					}
					else if ($comment->user_id == $bid->manager_id)
					{
						$this->processStatistics($comment, $statistics, 'user_id', $comment->user_id, 'manager');
					}
				}
			}

			$view['order']->bid = $chosen_bid;

			// находим клиента
			$view['client'] = $this->Clients->getClientById($view['order']->order_client);
			if ($this->user->user_group == 'manager')	$view['user_data'] = $this->Managers->getById($this->user->user_id);
			if ($this->user->user_group == 'client')	$view['user_data'] = $this->Clients->getClientById($this->user->user_id);

			$this->processStatistics($view['client'], $statistics, 'client_user', $view['client']->client_user, 'client');

			// если клиент выбрал предложение, достаем для него данные посредника
			if ($this->user->user_group == 'client' AND
				$chosen_bid)
			{
				$this->processStatistics($view['order'], $statistics, 'order_manager', $view['order']->order_manager, 'manager');
			}

			// адреса клиента
			$view['addresses'] = $this->Addresses->getAddressesByUserId($view['order']->order_client);

			// статусы, в которых можно редактировать/оплачивать заказ, перемещать товары
			if (isset($this->user->user_group))
			{
				$view['editable_statuses'] = $this->getEditableOrderStatuses();
				$view['payable_statuses'] = $this->Orders->getPayableStatuses($this->user->user_group);
				$view['neighbour_orders'] = $this->Orders->getNeighbourOrders(
					$view['order']->order_id,
					$this->user->user_group);
			}

			// статусы заказов и товаров, сгруппированные по типу заказа
			$view['statuses'] = $this->Orders->getAllStatuses();
			$view['odetail_statuses'] = $this->Odetails->getAllStatuses();
			$view['order_types'] = $this->Orders->getOrderTypes();
			$view['joinable_types'] = $this->Orders->getJoinableTypes();

			// заявки на оплату
			$view = $this->getOrders2In($view, 'open');

			// кнопка добавить предложение для посредника
			$view['bids_accepted'] = (empty($chosen_bid) AND ($this->user->user_group == 'manager'));

			if ($view['bids_accepted'])
			{
				foreach ($view['bids'] as $bid)
				{
					if ($bid->manager_id == $this->user->user_id)
					{
						$view['bids_accepted'] = FALSE;
						break;
					}
				}
			}

			// для формы нового предложения у посредника
			if (isset($this->user->user_id) AND $this->user->user_group == 'manager')
			{
				$this->Orders->prepareNewBidView($view['order'], $this->user->user_id);
			}

			if ($view['bids_accepted'])
			{
				$new_bid = new stdClass();
				$new_bid->bid_id = 0;
				$new_bid->manager_id = isset($this->user->user_id) ? $this->user->user_id : 0;
				$new_bid->total_cost = $view['order']->order_total_cost;
				$new_bid->manager_tax = $view['order']->manager_tax;
				$new_bid->foto_tax = $view['order']->foto_tax;
				$new_bid->delivery_cost = $view['order']->order_delivery_cost;
				$new_bid->delivery_name = '';
				
				if ($new_bid->manager_id)
				{
					$this->processStatistics($new_bid, $statistics, 'manager_id', $new_bid->manager_id, 'manager');
				}
				
				$view['new_bid'] = $new_bid;
			}
			
			// крошки
			$this->showOrderBreadcrumb($view['order'], $view['bids']);
		}
		catch (Exception $e) 
		{
			// если чтото свалилось, уходим на главную
			Func::redirect($this->config->item('base_url'));
		}

		// показываем заказ
		View::showChild($this->viewpath.'/pages/showOrderDetails', $view);
	}

	protected function getOrders2In($view, $status = 'open')
	{
		$this->load->model('CurrencyModel', 'Currencies');
		$this->load->model('PaymentServiceModel', 'Services');

		$Orders = $this->getPrivilegedOrders2In($view, $status, FALSE);

		/* пейджинг */
		$this->init_paging();
		$this->paging_count = count($Orders);

		// заявки на пополнение
		if ($Orders)
		{
			$Orders = array_slice($Orders, $this->paging_offset, $this->per_page);

			$this->load->model('ManagerModel', 'Managers');
			$statistics = array();

			foreach ($Orders as $o2i)
			{
				if ( ! empty($o2i->order2in_to) AND
					is_numeric($o2i->order2in_to))
				{
					if ($this->user->user_group == 'client')
					{
						$this->processStatistics(
							$o2i,
							$statistics,
							'order2in_to',
							$o2i->order2in_to,
							'manager');
					}
					else
					{
						$this->processStatistics(
							$o2i,
							$statistics,
							'order2in_user',
							$o2i->order2in_user,
							'client');
					}
				}
			}
		}

		// отдаем результат
		$payments_counters = $this->Orders2In->getCounters(
			$view['order']->order_id,
			$this->user->user_id,
			$this->user->user_group);

		$view += array (
			'Orders2In' => $Orders,
			'Orders2InStatuses'	=> $this->Orders2In->getStatuses(),
			'orders2InFoto' => $this->Orders2In->getOrders2InFoto($Orders),
			'services'	=> $this->Services->getInServices(),
			'open_orders2in' => $payments_counters['open'],
			'payed_orders2in' => $payments_counters['payed'],
			'pager' => $this->get_paging()
		);

		return $view;
	}

	protected function showPayments($order_id, $status, $is_silent = FALSE)
	{
		try
		{
			// безопасность
			if ( ! is_numeric($order_id))
			{
				throw new Exception('Доступ запрещен.');
			}

			// роли и разграничение доступа
			$view['order'] = $this->getPrivilegedOrder(
				$order_id,
				'Заказ не найден. Попробуйте еще раз.');

			// подгружаем модели
			$this->load->model('BidModel', 'Bids');
			$this->load->model('BidCommentModel', 'Comments');
			$this->load->model('ManagerModel', 'Managers');
			$this->load->model('ClientModel', 'Clients');
			$this->load->model('OdetailModel', 'Odetails');
			$this->load->model('OdetailJointModel', 'Joints');
			$this->load->model('CountryModel', 'Countries');
			$this->load->model('AddressModel', 'Addresses');

			$chosen_bid = FALSE;
			$statistics = array();

			// детали заказа
			$view['odetails'] = $this->Odetails->getOrderDetails($view['order']->order_id);
			$view['joints'] = $this->Joints->getOrderJoints($view['order']->order_id);
			$this->Orders->prepareOrderView($view);

			// страны
			$view['Countries'] = $this->Countries->getClientAvailableCountries($view['order']->order_client);

			// предложения: никаких ограничений доступа, показываем все
			$view['bids'] = $this->Bids->getBids($view['order']->order_id);

			foreach ($view['bids'] as $bid)
			{
				// находим допрасходы
				$bid->extra_taxes = $this->Bids->getBidExtras($bid->bid_id);

				// статистика предложения
				$this->processStatistics($bid, $statistics, 'manager_id', $bid->manager_id, 'manager');

				// выбранное клиентом предложение
				if ($bid->manager_id == $view['order']->order_manager)
				{
					$chosen_bid = $bid;
				}

				// комментарии
				$bid->comments = $this->Comments->getCommentsByBidId($bid->bid_id);

				if (empty($bid->comments))
				{
					continue;
				}

				// находим данные комментатора для каждого коммента
				foreach ($bid->comments as $comment)
				{
					if ($comment->user_id == $bid->client_id)
					{
						$this->processStatistics($comment, $statistics, 'user_id', $comment->user_id, 'client');
					}
					else if ($comment->user_id == $bid->manager_id)
					{
						$this->processStatistics($comment, $statistics, 'user_id', $comment->user_id, 'manager');
					}
				}
			}

			$view['order']->bid = $chosen_bid;

			// находим клиента
			$view['client'] = $this->Clients->getClientById($view['order']->order_client);

			$this->processStatistics($view['client'], $statistics, 'client_user', $view['client']->client_user, 'client');

			// если клиент выбрал предложение, достаем для него данные посредника
			if ($this->user->user_group == 'client' AND
				$chosen_bid)
			{
				$this->processStatistics($view['order'], $statistics, 'order_manager', $view['order']->order_manager, 'manager');
			}

			// адреса клиента
			$view['addresses'] = $this->Addresses->getAddressesByUserId($view['order']->order_client);

			// статусы, в которых можно редактировать заказ
			if (isset($this->user->user_group))
			{
				$view['editable_statuses'] = $this->getEditableOrderStatuses();
				$view['payable_statuses'] = $this->Orders->getPayableStatuses($this->user->user_group);
			}

			// статусы заказов и товаров, сгруппированные по типу заказа
			$view['statuses'] = $this->Orders->getAllStatuses();
			$view['odetail_statuses'] = $this->Odetails->getAllStatuses();
			$view['order_types'] = $this->Orders->getOrderTypes();
			$view['joinable_types'] = $this->Orders->getJoinableTypes();

			// статусы заказов и товаров, сгруппированные по типу заказа
			$view['order_types'] = $this->Orders->getOrderTypes();

			// заявки на оплату
			$view = $this->getOrders2In($view, $status);

			// отрисовка
			$view['selfurl'] = $this->config->item('base_url') . $this->cname . '/';
			$view['viewpath'] = $this->viewpath;

			$status = ucfirst($status);

			if ($is_silent)
			{
				return View::get("{$this->viewpath}ajax/show{$status}Orders2In", $view);
			}
			else
			{
				$this->load->view($this->viewpath . "ajax/show{$status}Orders2In", $view);
			}
		}
		catch (Exception $e)
		{
			//
		}
	}

	protected function showAllPayments($status = 'open', $is_silent = FALSE)
	{
		try
		{
			// подгружаем модели
			$this->load->model('ManagerModel', 'Managers');
			$this->load->model('ClientModel', 'Clients');
			$this->load->model('OrderModel', 'Orders');
			$this->load->model('CountryModel', 'Countries');
			$this->load->model('PaymentServiceModel', 'Services');

			// страны
			$view = array();
			$view['Countries'] = $this->Countries->getList();

			// статусы заказов и товаров, сгруппированные по типу заказа
			$view['order_types'] = $this->Orders->getOrderTypes();

			// заявки на оплату
			$view['filter'] = $this->initFilter('allPayments');
			$Orders = $this->getAllPrivilegedOrders2In($view['filter'], $status, FALSE);

			/* пейджинг */
			$this->init_paging();
			$this->paging_count = count($Orders);

			$statistics = array();

			if ($Orders)
			{
				$Orders = array_slice($Orders, $this->paging_offset, $this->per_page);

				$statistics = array();

				foreach ($Orders as $o2i)
				{
					// роли и разграничение доступа
					$order = $this->getPrivilegedOrder(
						$o2i->order_id,
						"Заявка недоступна.");

					if ( ! empty($o2i->order2in_to) AND
						is_numeric($o2i->order2in_to))
					{
						$this->processStatistics(
							$o2i,
							$statistics,
							'order2in_user',
							$o2i->order2in_user,
							'client');
					}

					$client = new stdClass();
					$client->client_id = $order->order_client;

					$this->processStatistics($client, $statistics, 'client_id', $client->client_id, 'client');
					$o2i->client = $client;

					$manager = new stdClass();
					$manager->manager_id = $order->order_manager;

					$this->processStatistics($manager, $statistics, 'manager_id', $manager->manager_id, 'manager');
					$o2i->manager = $manager;

					$this->Orders->prepareOrderView(array(
						'order' => $order
					));

					$o2i->order_type = $order->order_type;
					$o2i->order_currency = $order->order_currency;
				}
			}

			// готовим результат к отрисовке
			$payments_counters = $this->Orders2In->getCounters(
				0,
				$this->user->user_id,
				$this->user->user_group,
				$view['filter']);

			$view += array (
				'Orders2In' => $Orders,
				'Orders2InStatuses'	=> $this->Orders2In->getStatuses(),
				'orders2InFoto' => $this->Orders2In->getOrders2InFoto($Orders),
				'services'	=> $this->Services->getInServices(),
				'open_orders2in' => $payments_counters['open'],
				'payed_orders2in' => $payments_counters['payed'],
				'pager' => $this->get_paging()
			);

			// крошки
			Breadcrumb::setCrumb(array(
				"/{$this->user->user_group}/payments"  =>
				"Заявки на оплату"), 1,	TRUE);

			// отрисовка
			$status = ucfirst($status);

			if ($this->uri->segment(4) == 'ajax')
			{
				$view['selfurl'] = $this->config->item('base_url').$this->cname.'/';
				$view['viewpath'] = $this->viewpath;
				$this->load->view($this->viewpath."ajax/showAll{$status}Payments", $view);
			}
			elseif ($is_silent)
			{
				return View::get("{$this->viewpath}/ajax/showAll{$status}Payments", $view);
			}
			else
			{
				View::showChild("{$this->viewpath}/pages/showAllPayments", $view);
			}
		}
		catch (Exception $e)
		{
			//
		}
	}

	protected function showCountrypostTaxes()
	{
		$this->load->model('TaxModel', 'Taxes');

		if ($this->user->user_group == 'manager')
		{
			$view['filter'] = $this->initFilter('taxes');

			$view['taxes'] = $this->Taxes->getFilteredTaxes(
				$view['filter']->condition,
				$view['filter']->from,
				$view['filter']->to,
				" AND (dealers.user_id = {$this->user->user_id})");

			$view['total_usd'] = $this->Taxes->getTotalUSD($view, FALSE);

			if ($totals = $this->Taxes->getTotalLocal($view, FALSE))
			{
				$view['total_local'] = $totals['total_local'];
				$view['total_currency'] = $totals['total_currency'];
			}
		}
		else if ($this->user->user_group == 'admin')
		{
			$view['filter'] = $this->initFilter('taxes');
			$view['taxes'] = $this->Taxes->getFilteredTaxes(
				$view['filter']->condition,
				$view['filter']->from,
				$view['filter']->to);

			$view['total_usd'] = $this->Taxes->getTotalUSD($view, FALSE);

			if ($totals = $this->Taxes->getTotalLocal($view, FALSE))
			{
				$view['total_local'] = $totals['total_local'];
				$view['total_currency'] = $totals['total_currency'];
			}
		}

		/* пейджинг */
		$per_page = isset($this->session->userdata['taxes_per_page']) ? $this->session->userdata['taxes_per_page']
			: $this->per_page;
		$this->per_page = $per_page;
		$view['per_page'] = $per_page;

		$this->init_paging();
		$this->paging_count = count($view['taxes']);

		if (isset($view['taxes']) AND $view['taxes'])
		{
			$view['taxes'] = array_slice($view['taxes'], $this->paging_offset, $this->per_page);
		}

		$view['pager'] = $this->get_paging();

		// собираем платежные системы
		$view['statuses'] = $this->Taxes->getStatuses();
		$view['user'] = $this->user;

		// крошки
		Breadcrumb::setCrumb(array(
			"/{$this->user->user_group}/taxes"  =>
			"Баланс Countrypost.ru"), 1,	TRUE);

		// парсим шаблон
		if ($this->uri->segment(4) == 'ajax')
		{
			$view['selfurl'] = $this->config->item('base_url').$this->cname.'/';
			$view['viewpath'] = $this->viewpath;
			$this->load->view($this->viewpath."ajax/showCountrypostTaxes", $view);
		}
		else
		{
			View::showChild($this->viewpath.'pages/showCountrypostTaxes', $view);
		}
}

	protected function showPublicOrder()
	{
		try
		{
			// безопасность
			if ( ! is_numeric($this->uri->segment(3)))
			{
				throw new Exception('Доступ запрещен.');
			}

			// роли и разграничение доступа
			$view['order'] = $this->getPublicOrder(
				$this->uri->segment(3),
				'Заказ не найден. Попробуйте еще раз.');

			// детали заказа
			$this->load->model('OdetailModel', 'Odetails');
			$this->load->model('OdetailJointModel', 'Joints');

			$view['editable_statuses'] = array();
			$view['odetails'] = $this->Odetails->getOrderDetails($view['order']->order_id);
			$view['joints'] = $this->Joints->getOrderJoints($view['order']->order_id);

			$this->load->model('CountryModel', 'Countries');

			$this->Orders->prepareOrderView($view);

			// страны
			// TODO: проверить, используется ли $view['Countries'] и удалить
			$view['Countries'] = $this->Countries->getClientAvailableCountries($view['order']->order_client);
			$view['countries'] = $this->Countries->getArray();

			$view['order_statuses'] = $this->Orders->getAvailableOrderStatuses();
			$view['order_types'] = $this->Orders->getOrderTypes();

			// предложения
			$this->load->model('BidModel', 'Bids');
			$this->load->model('BidCommentModel', 'Comments');
			$this->load->model('ManagerModel', 'Managers');
			$this->load->model('ClientModel', 'Clients');
			$this->load->model('CurrencyModel', 'Currencies');

			$view['exchangeRates'] = array (
				'rate_usd' => $this->Currencies->getExchangeRate($view['order']->order_currency, 'USD', 'client'),
				'rate_kzt' => $this->Currencies->getExchangeRate($view['order']->order_currency, 'KZT', 'client'),
				'rate_uah' => $this->Currencies->getExchangeRate($view['order']->order_currency, 'UAH', 'client'),
				'rate_rur' => $this->Currencies->getExchangeRate($view['order']->order_currency, 'RUB', 'client')
			);

			// предложения: никаких ограничений доступа, показываем все
			$view['bids'] = $this->Bids->getBids($view['order']->order_id);
			$statistics = array();

			// комментарии и статистика
			if (empty($this->user->user_group) OR
				$this->user->user_group == 'manager' OR
				$this->user->user_group == 'client')
			{
				foreach ($view['bids'] as $bid)
				{
					// статистика видна всем
					$this->processStatistics($bid, $statistics, 'manager_id', $bid->manager_id, 'manager');

					// а комменты только партнеру и клиенту
                    $bid->new_comments = 0;
					if (isset($this->user->user_group))
					{
						$bid->comments = $this->Comments->getCommentsByBidId($bid->bid_id);

						if (empty($bid->comments))
						{
							continue;
						}

						foreach ($bid->comments as $comment)
						{
							if ($comment->user_id == $bid->client_id)
							{
								$this->processStatistics($comment, $statistics, 'user_id', $comment->user_id, 'client');
							}
							else if ($comment->user_id == $bid->manager_id)
							{
								$this->processStatistics($comment, $statistics, 'user_id', $comment->user_id, 'manager');
							}
						}
					}


					// находим допрасходы
					$bid->extra_taxes = $this->Bids->getBidExtras($bid->bid_id);
				}
			}

			// клиент
			$view['client'] = $this->Clients->getClientById($view['order']->order_client);
			$view['bids_accepted'] = FALSE;

			$this->processStatistics($view['client'], $statistics, 'client_user', $view['client']->client_user, 'client');

			if ($view['order']->order_manager AND
				isset($this->user) AND
				$this->user->user_group == 'client')
			{
				$this->processStatistics($view['order'], $statistics, 'order_manager', $view['order']->order_manager, 'manager');
			}

			// партнер
			if (isset($this->user->user_group) AND
				$this->user->user_group == 'manager' AND
					empty($view['order']->order_manager))
			{
				$view['bids_accepted'] = TRUE;

				foreach ($view['bids'] as $bid)
				{
					if ($bid->manager_id == $this->user->user_id)
					{
						$view['bids_accepted'] = FALSE;
						break;
					}
				}
			}

			if (empty($this->user->user_group))
			{
				$view['bids_accepted'] = TRUE;
			}

			// для формы нового предложения
			if ($view['bids_accepted'] AND
				isset($this->user->user_group))
			{
				$this->Orders->prepareNewBidView($view['order'], $this->user->user_id);

				$view['new_bid'] = $this->generateNewBid($view['order'], $statistics);
			}

			// крошки
			$this->showOrderBreadcrumb($view['order'], $view['bids']);
		}
		catch (Exception $e)
		{
			// если вдруг чтото не сработало, уходим на главную
			Func::redirect($this->config->item('base_url'));
		}

		// показываем детали заказа
		View::showChild('/main/pages/showOrderDetails', $view);
	}

	protected function generateNewBid($order, $statistics = array())
	{
		$new_bid = new stdClass();
		$new_bid->bid_id = 0;
		$new_bid->manager_id = $this->user->user_id;
		$new_bid->total_cost = $order->order_total_cost;
		$new_bid->manager_tax = $order->manager_tax;
		$new_bid->foto_tax = $order->foto_tax;
		$new_bid->delivery_cost = 0;
		$new_bid->delivery_name = '';

		$this->processStatistics($new_bid, $statistics, 'manager_id', $this->user->user_id, 'manager');

		return $new_bid;
	}

	protected function getOrderClient()
	{
		return isset($this->user->user_id) ?
			$this->user->user_id :
			UserModel::getTemporaryKey();
	}

	protected function addProductToOrder($order)
	{
		try
		{
			$this->load->model('OrderModel', 'Orders');
			$this->load->model('OdetailModel', 'OdetailModel');

			// необязательные поля
			Check::reset_empties();
			$detail = new OdetailModel();

			$detail->odetail_order 					= $order->order_id;
			$detail->odetail_status                 = 'processing';
			$detail->odetail_client					= $order->order_client;
			$detail->odetail_manager				= $order->order_manager;

			$detail->odetail_shop				    = Check::str('oshop', 255, 0);
			$detail->odetail_volume				    = Check::float('ovolume', 0);
			$detail->odetail_tnved				    = Check::str('otnved', 255, 1);
			$detail->odetail_insurance				= Check::chkbox('insurance');
			$detail->odetail_comment                = Check::str('ocomment', 255, 0);
			$detail->odetail_tracking               = Check::str('otracking', 80, 0);
			$detail->odetail_product_amount			= Check::int('oamount');
			$detail->odetail_product_color			= Check::str('ocolor', 32, 0);
			$detail->odetail_product_size			= Check::str('osize', 32, 0);
			$detail->odetail_country				= Check::str('ocountry', 255, 1);
			$detail->odetail_foto_requested			= Check::chkbox('foto_requested');
			$detail->odetail_search_requested		= Check::chkbox('search_requested');
			$detail->odetail_link					= Check::str('olink', 500, 1);
			$detail->odetail_product_name			= Check::str('oname', 255, 1);
			$detail->odetail_price					= Check::float('oprice');
			$detail->odetail_pricedelivery			= Check::float('odeliveryprice');
			$detail->odetail_weight					= Check::float('oweight');

			$detail->odetail_img					= Check::str('userfileimg', 4096, 1);
			$userfile								= (isset($_FILES['userfile']) AND ! $_FILES['userfile']['error']);

			if (empty($userfile) AND
				empty($detail->odetail_img))
			{
				$detail->odetail_img = 0;
			}

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
					$this->mail_forwardingProductCheck($order, $detail);
					break;
			}

			// погнали
			$detail = $this->OdetailModel->addOdetail($detail);

			// загружаем файл
			if (isset($userfile) AND $userfile)
			{
				$this->uploadOrderScreenshot($detail, $order->order_client);
			}

			// пересчитываем заказ
			if ( ! $this->Orders->recalculate($order))
			{
				throw new Exception('Невожможно пересчитать стоимость заказа. Попоробуйте еще раз.');
			}

			$this->Orders->saveOrder($order);

			return array(
				'detail' => $detail,
				'error' => 0
			);
		}
		catch (Exception $e)
		{
			return array(
				'detail' => isset($detail) ? $detail : FALSE,
				'error' => $e->getMessage()
			);
		}
	}

	protected function addProductManualAjax()
	{
		try
		{
			$order = $this->updateOrder();
			$result = $this->addProductToOrder($order);

			$view = new stdClass();
			$view->error = $result['error'];

			if (empty($view->error))
			{
				$view->odetail_id = $result['detail']->odetail_id;
				$view->weight = $order->order_weight;
				$view->products_cost = $order->order_products_cost;
				$view->delivery_cost = $order->order_delivery_cost;

				if ($order->order_country_to)
				{
					$country = $this->Countries->getById($order->order_country_to);

					if ($country)
					{
						$view->country = $country->country_name;
					}
				}

				$view->city = $order->order_city_to;

				$view->product = View::get('client/ajax/odetail', array(
					'odetail'				=> $result['detail'],
					'odetail_joint_id'		=> 0,
					'odetail_joint_count'	=> 0,
					'is_joinable'			=> FALSE,
					'is_editable'			=> TRUE,
					'order'					=> $order,
					'selfurl'				=> $this->config->item('base_url') . $this->cname . '/',
					'viewpath'				=> $this->viewpath
				));
			}

			// возвращаем json с инфой по заказу и товару
			//echo json_encode($view, JSON_HEX_TAG); // для PHP 5.3.x
			echo str_replace(array("<", ">", "&"), array('\u003c', '\u003e', '\u0026'), json_encode($view)); // для PHP 5.2.x
		}
		catch (Exception $e)
		{
			echo json_encode(array('error' => $e->getMessage()));
		}
	}

	protected function updateOrder($order_id = 0)
	{
		// 1. валидация
		if (empty($order_id))
		{
			$order_id = Check::int('order_id');

			if (empty($order_id))
			{
				throw new Exception('Заказ не найден.');
			}
		}

		// 2. Находим заказ
		// TODO: добавить проверку доступа
		$this->load->model('OrderModel', 'Orders');
		$order = $this->Orders->getById($order_id);

		if (empty($order))
		{
			throw new Exception('Заказ не найден.');
		}

		// 3. заполняем заказ
		$order->order_manager       = Check::int('dealer_id');
		$order->order_country_from  = Check::int('country_from');
		$order->order_country_to	= Check::int('country_to');
		$order->order_city_to		= Check::str('city_to', 40, 0);
		$order->preferred_delivery	= Check::str('preferred_delivery', 255, 0);

		// 4. проверяем выбранного посредника
		if ($order->order_manager)
		{
			$this->load->model('ManagerModel', 'Managers');
			$manager = $this->Managers->getById($order->order_manager);

			if (empty($manager) OR
				$manager->manager_status != 1 OR
				$manager->is_mail_forwarding == 0)
			{
				throw new Exception('Посредник не найден.');
			}

			$order->order_country_from = $manager->manager_country;
		}

		// 5. проверяем страну заказа, необходимо для вычисления валюты
		if (empty($order->order_country_from))
		{
			throw new Exception('Выберите страну.');
		}

		// 6. сохраняем и отдаем заказ
		$this->Orders->saveOrder($order);

		return $order;
	}

	protected function showO2iComments()
	{
		try
		{
			// безопасность
			if ( ! $this->user OR
				 ! $this->user->user_id OR
				!is_numeric($this->uri->segment(3)))
			{
				throw new Exception('Доступ запрещен.');
			}
		
			$this->load->model('Order2InModel', 'O2i');
			
			// роли и разграничение доступа
			if ($this->user->user_group == 'admin')
			{
			    $o2i = $this->O2i->getById($this->uri->segment(3));
			}
			else if ($this->user->user_group == 'client')
			{
				$o2i = $this->O2i->getClientsO2iById($this->uri->segment(3), $this->user->user_id);
			}
			else if ($this->user->user_group == 'manager')
			{
				$o2i = $this->O2i->getManagersO2iById($this->uri->segment(3), $this->user->user_id);
			}
			
			if ( ! $o2i)
			{
				throw new Exception('Невозможно отобразить комментарии. Соответствующая заявка недоступна.');
			}

			// сбрасываем флаг нового комментария
			if ($this->user->user_group == 'client' AND
				$o2i->order2in_2clientcomment)
			{
				$o2i->order2in_2clientcomment = 0;
				$this->O2i->addOrder($o2i);
			}
			else if ($this->user->user_group == 'manager' AND
				$o2i->order2in_2admincomment)
			{
				$o2i->order2in_2admincomment = 0;
				$this->O2i->addOrder($o2i);
			}

			// показываем комментарии к заявке
			$this->load->model('O2ICommentsModel', 'Comments');
			$view['comments'] = $this->Comments->getCommentsByO2iId($this->uri->segment(3));
			$view['o2i'] = $o2i;

			if ( ! $view['o2i'])
			{
				throw new Exception('Ошибка отображения комментариев. Попробуйте еще раз.');
			}

			// крошки
			if ($this->user->user_group == 'admin')
			{
				Breadcrumb::setCrumb(array(
					"/admin/payments"  =>
					"Заявки на оплату"), 2);
			}
			else
			{
				Breadcrumb::setCrumb(array(
					"/{$this->user->user_group}/order/{$o2i->order_id}"  =>
					"Заказ №{$o2i->order_id}"), 2);
			}
			Breadcrumb::setCrumb(array(
				"/{$this->user->user_group}/payment/" . $this->uri->segment(3) =>
				"Заявка на оплату №" . $this->uri->segment(3)), 3,	TRUE);

			// находим данные комментатора для каждого коммента
			$this->load->model('OrderModel', 'Orders');
			$this->load->model('UserModel', 'Users');
			$this->load->model('ClientModel', 'Clients');
			$this->load->model('ManagerModel', 'Managers');
			$statistics = array();

			if ($view['comments'])
			{
				foreach ($view['comments'] as $comment)
				{
					if ($comment->o2icomment_user == 1)
					{
						continue;
					}

					if ($this->user->user_group == 'admin')
					{
						$user = $this->Users->getById($comment->o2icomment_user);
					}

					if (($comment->o2icomment_user == $this->user->user_id AND
							$this->user->user_group == 'client') OR
						($comment->o2icomment_user != $this->user->user_id AND
							$this->user->user_group == 'manager') OR
						($this->user->user_group == 'admin' AND
							$user->user_group == 'client'))
					{
						$this->processStatistics($comment, $statistics, 'o2icomment_user', $comment->o2icomment_user, 'client');
					}
					else if (($comment->o2icomment_user == $this->user->user_id AND
						$this->user->user_group == 'manager') OR
						($comment->o2icomment_user != $this->user->user_id AND
							$this->user->user_group == 'client') OR
						($this->user->user_group == 'admin' AND
							$user->user_group == 'manager'))
					{
						$this->processStatistics($comment, $statistics, 'o2icomment_user', $comment->o2icomment_user, 'manager');
					}
				}
			}

			// находим заказ, посредника и клиента
			$order = $this->getPrivilegedOrder(
				$o2i->order_id,
				'Заказ не найден. Попробуйте еще раз.');

			$client = new stdClass();
			$client->client_id = $order->order_client;

			$this->processStatistics($client, $statistics, 'client_id', $client->client_id, 'client');
			$view['client'] = $client;

			$manager = new stdClass();
			$manager->manager_id = $order->order_manager;

			$this->processStatistics($manager, $statistics, 'manager_id', $manager->manager_id, 'manager');
			$view['manager'] = $manager;

			// рисуем заявку
			$this->load->model('PaymentServiceModel', 'Services');

			$view['order'] = $order;
			$this->Orders->prepareOrderView($view);
			$view['order_types'] = $this->Orders->getOrderTypes();
			$view['statuses'] = $this->O2i->getStatuses();
			$view['orders2InFoto'] = $this->O2i->getOrders2InFoto(array($o2i));
			$view['services'] = $this->Services->getInServices();
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
			return;
		}

		// отображаем комментарии
		View::showChild($this->viewpath.'pages/showPayment', $view);
	}
	
	protected function addBidComment($bid_id, $comment_id = NULL)
	{
		try
		{
			if (!is_numeric($bid_id))
			{
				throw new Exception('Доступ запрещен.');
			}
		
			// роли и разграничение доступа
			$this->load->model('BidModel', 'Bids');
			$bid = $this->getPrivilegedBid(
				$bid_id, 
				'Невозможно добавить комментарий. Предложение не найдено.');

			// валидация пользовательского ввода
			$this->load->model('BidCommentModel', 'Comments');
			
			if (is_numeric($comment_id)) 
			{
				$comment = $this->Comments->getById($comment_id);
				if ( ! $comment OR 
					$comment->user_id != $this->user->user_id) 
				{
					throw new Exception('Невозможно изменить комментарий. Комментарий не найден.');
				}
				
				$comment->message = Check::txt('comment_update', 8096, 1);
			}
			else
			{
				$comment = new stdClass();
				$comment->bid_id = $bid_id;
				$comment->message = Check::txt('comment', 8096, 1);
				$comment->user_id = $this->user->user_id;
			}
			
			$empties = Check::get_empties();
		
			if ($empties) 
			{
				throw new Exception('Текст комментария отсутствует. Введите текст и попробуйте еще раз.');
			}
			
			// сохранение результатов
			$comment = $this->Comments->addComment($comment);
			
			if ( ! $comment AND
				! is_numeric($comment_id))
			{
				throw new Exception('Комментарий не добавлен. Попробуйте еще раз.');
			}

            if ($this->user->user_group == 'client')
            {
                // уведомление на почту посреднику
                $managerInfo = $this->Bids->getManagerInfo($bid->manager_id);

                $email_data["manager_name"] = $managerInfo->manager_name;
                $email_data["order_id"] = $bid->order_id;
                $msg = $this->load->view("/mail/email_4", $email_data, true);

                $this->load->library('email');
                $this->email->from('info@countrypost.ru', 'Countrypost.ru');
                $this->email->to($managerInfo->user_email);
                $this->email->subject('Countrypost.ru');
                $this->email->message($msg);
                $this->email->send();
            }else if ($this->user->user_group == 'manager'){
                // уведомление на почту клиенту
                $clientInfo = $this->Bids->getClientInfo($bid->client_id);

                $email_data["client_name"] = $clientInfo->client_name;
                $email_data["order_id"] = $bid->order_id;
                $msg = $this->load->view("/mail/email_3", $email_data, true);

                $this->load->library('email');
                $this->email->from('info@countrypost.ru', 'Countrypost.ru');
                $this->email->to($clientInfo->user_email);
                $this->email->subject('Countrypost.ru');
                $this->email->message($msg);
                $this->email->send();
            }

			$this->load->model('ClientModel', 'Clients');
			$this->load->model('ManagerModel', 'Managers');
			$this->load->model('CountryModel', 'Countries');
			$this->processStatistics($comment, array(), 'user_id', $this->user->user_id, $this->user->user_group);
			if ($this->user->user_group == 'manager')	$view['user'] = $this->Managers->getById($this->user->user_id);
			if ($this->user->user_group == 'client')	$view['user'] = $this->Clients->getClientById($this->user->user_id);
		
			$view['comment'] = $comment;
			$view['countries'] = $this->Countries->getArray();

			$this->load->view('/main/elements/orders/comment', $view);
			
			/*
			// выставляем флаг нового комментария
			if ($this->user->user_group == 'manager')
			{
				$bid->comment_for_client = TRUE;
			}
			else if ($this->user->user_group == 'client')
			{
				$bid->comment_for_manager = TRUE;
			}
			else if ($this->user->user_group == 'admin')
			{
				$bid->comment_for_manager = TRUE;
				$bid->comment_for_client = TRUE;
			}
			
			$this->Bids->saveBid($bid);*/
		}
		catch (Exception $e) 
		{
			$this->db->trans_rollback();
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}
	}

	protected function addPaymentComment($o2i_id, $comment_id = null)
	{
		try
		{
			if ( ! is_numeric($o2i_id) OR
				empty($o2i_id))
			{
				throw new Exception('Доступ запрещен.');
			}

			// роли и разграничение доступа
			$this->load->model('Order2InModel', 'Payments');
			$this->load->model('O2ICommentsModel', 'Comments');

			if ( ! ($payment = $this->Payments->getById($o2i_id)))
			{
				throw new Exception('Невозможно сохранить комментарий. Заявка не найдена.');
			}
			$order = $this->getPrivilegedOrder(
				$payment->order_id,
				'Невозможно сохранить комментарий. Заказ не найден.');

			if ($order->order_client != $this->user->user_id AND
				$order->order_manager != $this->user->user_id AND
				$this->user->user_group != 'admin')
			{
				throw new Exception('Невозможно сохранить комментарий. Заявка не найдена.');
			}

			// валидация пользовательского ввода
			if (is_numeric($comment_id))
			{
				$comment = $this->Comments->getById($comment_id);

				if ( ! $comment OR
					($comment->user_id != $this->user->user_id AND
						$this->user->user_group != 'admin'))
				{
					throw new Exception('Невозможно сохранить комментарий. Комментарий не найден.');
				}

				$comment->o2icomment_text = Check::txt('comment_update', 8096, 1);
			}
			else
			{
				$comment = new stdClass();
				$comment->o2icomment_order2in = $o2i_id;
				$comment->o2icomment_text = Check::txt('comment', 8096, 1);
				$comment->o2icomment_user = $this->user->user_id;
			}

			$empties = Check::get_empties();

			if ($empties)
			{
				throw new Exception('Текст комментария отсутствует. Введите текст и попробуйте еще раз.');
			}

			// сохранение результатов
			$comment = $this->Comments->addComment($comment);

			if ( ! $comment AND
				! is_numeric($comment_id))
			{
				throw new Exception('Комментарий не добавлен. Попробуйте еще раз.');
			}

			// выставляем флаг нового комментария
			if ($this->user->user_group == 'admin')
			{
				$payment->order2in_2clientcomment = TRUE;
				$payment->order2in_2admincomment = TRUE;
			}
			else if ($this->user->user_group == 'manager')
			{
				$payment->order2in_2clientcomment = TRUE;
			}
			else
			{
				$payment->order2in_2admincomment = TRUE;
			}

			$this->Payments->addOrder($payment);

			// отрисовка коммента
			$this->load->model('ClientModel', 'Clients');
			$this->load->model('ManagerModel', 'Managers');

			if ($this->user->user_group != 'admin')
			{
				$this->processStatistics($comment, array(), 'o2icomment_user', $this->user->user_id, $this->user->user_group);
			}

			$view['comment'] = $comment;
			$this->load->view('/main/elements/payments/comment', $view);
		}
		catch (Exception $e) 
		{
			print($e->getMessage());
		}
	}

	protected function filter($filterType, $pageName)
	{
		try
		{
			// достаем из сессии последнюю фильтрацию
			$filter	= $this->initFilter($filterType);

			// собираем общие для всех фильтров параметры (прочистить)
			if (isset($_POST['manager_user'])) $filter->manager_user						= Check::int('manager_user');
			if (isset($_POST['client_country'])) $filter->client_country					= Check::int('client_country');
			if (isset($_POST['search_id'])) $filter->search_id								= Check::txt('search_id', 11, 1, '');
			if (isset($_POST['search_client'])) $filter->search_client						= Check::txt('search_client', 11, 1, '');
			if (isset($_POST['id_type'])) $filter->id_type									= Check::txt('id_type', 13, 5, '');
			if (isset($_POST['id_status'])) $filter->id_status								= Check::txt('id_status', 20, 1, '');

			if ( ! isset($filter->id_type) OR $filter->id_type == '')
			{
				$filter->search_id = '';
				$filter->search_client = '';
			}

			// погнали
			switch ($filterType)
			{
				case ('paymentHistory') :
					$filter = $this->processPaymentHistoryFilter();
					break;
				case ('allPayments') :
					$filter = $this->processAllPaymentsFilter();
					break;
				case ('taxes') :
					$filter = $this->processTaxesFilter();
					break;
				case ('UnassignedOrders') :
					$filter = $this->processUnassignedOrdersFilter($filter);
					break;
				case ('Dealers') :
					$filter = $this->processDealersFilter($filter);
					break;
				case ('Clients') :
					$filter = $this->processClientsFilter($filter);
					break;
				case ('Orders') :
					$filter = $this->processOrdersFilter($filter);
					break;
				case ('Balance') :
					$filter = $this->processBalanceFilter($filter);
					break;
			}

			// кладем фильтрацию назад сессию
			$_SESSION[$filterType.'Filter'] = $filter;
		}
		catch (Exception $e) 
		{
		}

		// уходим на страницу с результатами фильтрации
		Func::redirect($this->config->item('base_url').$this->cname.'/'.$pageName);
	}
	
	protected function initFilter($filterType)
	{
		// если ничего не фильтровали, инициализируем фильтр
		if ( ! isset($_SESSION[$filterType.'Filter']))
		{
			// инициализируем общие для всех фильтров параметры (прочистить)
			$filter = new stdClass();
			$filter->manager_user			= '';
			$filter->client_country			= '';
			$filter->period					= '';
			$filter->search_id				= '';
			$filter->search_client			= '';
			$filter->id_type				= '';
			$filter->id_status				= '';
			//$filter->pricelist_country_from	= '';
			//$filter->pricelist_country_to	= '';

			// погнали
			switch ($filterType)
			{
				case ($filterType == 'Orders') :
					$filter->search_id = '';
					$filter->id_type = '';
					$filter->id_status = '';
					$filter->country_to = '';
					break;
				case ($filterType == 'paymentHistory') :
					$filter = $this->initPaymentHistoryFilter($filter);
					break;
				case 'allPayments' :
					$filter = $this->initAllPaymentsFilter($filter);
					break;
				case ($filterType == 'taxes') :
					$filter = $this->initTaxesFilter($filter);
					break;
			}

			// кладем фильтр в сессию
			$_SESSION[$filterType.'Filter'] = $filter;
		}

		return $_SESSION[$filterType.'Filter'];
	}

	protected function initPaymentHistoryFilter()
	{
		$filter = new stdClass();
		$filter->payment_from = '';
		$filter->user_from = '';
		$filter->user_to = '';
		$filter->payment_to = '';
		$filter->condition = array();
		$filter->sfield		= '';
		$filter->sdate		= '';
		$filter->svalue		= '';
		$filter->status		= '';
		$filter->from		= '';
		$filter->to			= '';

		return $filter;
	}

	protected function initTaxesFilter()
	{
		$filter = new stdClass();
		$filter->tax_from = '';
		$filter->condition = array();
		$filter->sfield		= '';
		$filter->sdate		= '';
		$filter->svalue		= '';
		$filter->status		= '';
		$filter->from		= '';
		$filter->to		= '';

		return $filter;
	}

	protected function initAllPaymentsFilter()
	{
		$filter = new stdClass();
		$filter->sfield		= '';
		$filter->svalue		= '';
		$filter->from		= '';
		$filter->to			= '';
		$filter->condition = array();

		return $filter;
	}

	protected function processTaxesFilter()
	{
		$filter = $this->initTaxesFilter();

		// сброс фильтра
		if (isset($_POST['resetFilter']) AND $_POST['resetFilter'] == '1')
		{
			return $filter;
		}

		$filter->svalue		= Check::str('svalue', 255, 1, '');
		$filter->sfield		= Check::str('sfield', 20, 1, '');
		$filter->status		= Check::str('status', 20, 1, '');
		$filter->from		= Check::str('from', 10, 10, '');
		$filter->to			= Check::str('to', 10, 10, '');

		if ($filter->sfield)
		{
			switch ($filter->sfield)
			{
				case 'manager_id' :
					if (is_numeric($filter->svalue))
					{
						$filter->condition['like'] = array('manager_id' => $filter->svalue);
					}
					break;
				case 'order_id' :
					if (is_numeric($filter->svalue))
					{
						$filter->condition['like'] = array('order_id' => $filter->svalue);
					}
					break;
				case 'manager_login' :
					$filter->condition['like'] = array('dealers.user_login' => $filter->svalue);
					break;
				case 'tax_id' :
					if (is_numeric($filter->svalue))
					{
						$filter->condition['like'] = array('tax_id' => $filter->svalue);
					}
					break;
				case 'amount' :
					if (is_numeric($filter->svalue))
					{
						$filter->condition["like"] = array('amount' => $filter->svalue);
					}
					break;
			}
		}

		if ($filter->status)
		{
			$filter->condition["status"] = $filter->status;
		}

		return $filter;
	}

	protected function processAllPaymentsFilter()
	{
		$filter = $this->initAllPaymentsFilter();

		// сброс фильтра
		if (isset($_POST['resetFilter']) AND $_POST['resetFilter'] == '1')
		{
			return $filter;
		}

		$filter->svalue		= Check::str('svalue', 255, 1, '');
		$filter->sfield		= Check::str('sfield', 20, 1, '');
		$filter->from		= Check::str('from', 10, 10, '');
		$filter->to			= Check::str('to', 10, 10, '');

		if ($filter->sfield)
		{
			switch ($filter->sfield)
			{
				case 'client_id' :
					if (is_numeric($filter->svalue))
					{
						$filter->condition['like'] = array('order2in_user' => $filter->svalue);
					}
					break;
				case 'order2in_id' :
					$filter->condition['like'] = array('order2in_id' => $filter->svalue);
					break;
			}
		}

		return $filter;
	}

	protected function deleteOrder()
	{
		try
		{
			if ( ! $this->user OR
				 ! $this->user->user_id OR
				!is_numeric($this->uri->segment(3)))
			{
				throw new Exception('Доступ запрещен.');
			}
			
			// роли и разграничение доступа
			$order = $this->getPrivilegedOrder(
				$this->uri->segment(3), 
				'Заказ не найден. Попробуйте еще раз.');

			// сохранение результатов
			$order->order_status = 'deleted';
			$deleted_order = $this->Orders->saveOrder($order);
			
			if ( ! $deleted_order)
			{
				throw new Exception('Заказ не удален. Попробуйте еще раз.');
			}
			
			// уведомления, удаленный заказ
			$this->load->model('ManagerModel', 'Managers');
			$this->load->model('UserModel', 'Users');
			$this->load->model('ClientModel', 'Clients');
/*
			Mailer::sendAdminNotification(
				Mailer::SUBJECT_ORDER_DELETED_STATUS, 
				Mailer::ORDER_DELETED_NOTIFICATION,
				0,
				$order->order_id, 
				0,
				NULL,
				NULL,
				NULL);

			if ($order->order_manager)
			{
				Mailer::sendManagerNotification(
					Mailer::SUBJECT_ORDER_DELETED_STATUS, 
					Mailer::ORDER_DELETED_NOTIFICATION,
					$order->order_manager, 
					$order->order_id, 
					0,
					NULL,
					$this->Managers,
					NULL);
			}

			Mailer::sendClientNotification(
				Mailer::SUBJECT_ORDER_DELETED_STATUS, 
				Mailer::ORDER_DELETED_NOTIFICATION,
				$order->order_id, 
				$order->order_client, 
				NULL,
				$this->Clients);
*/
			$this->result->m = 'Заказ успешно удален.';
			Stack::push('result', $this->result);
		}
		catch (Exception $e)
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}
		
		// открываем заказы
		Func::redirect($this->config->item('base_url').$this->cname.'/showOpenOrders');
	}
	
	protected function updateOrderStatus($param, $order_status)
	{
		// заказ или нет?
		if (stripos($param, 'order_status') === FALSE) return;
		
		$order_id = str_ireplace('order_status', '', $param);
		if (!is_numeric($order_id)) return;
			
		$Orders = $this->getOrderModel();
		// роли и разграничение доступа
		if ($this->user->user_group == 'admin')
		{
			$order = $this->Orders->getById($order_id);

			if ($order->order_status == 'payed')
			{
				if (!isset($_POST['payed'.$order_id]) OR !is_numeric($_POST['payed'.$order_id]))
				{
					throw new Exception('Сумма оплаты одного или нескольких заказов не определена. Попоробуйте еще раз.');
				}
				
				$order->order_cost_payed = (float)$_POST['payed'.$order_id];
			}
		}
		else if ($this->user->user_group == 'manager')
		{
			$order = $Orders->getManagerOrderById($order_id, $this->user->user_id);
		}
		
		if ( ! $order)
		{
			throw new Exception('Заказ не найдены. Попоробуйте еще раз.');
		}
			
		// меняем статус заказа
		$recent_status = $order->order_status;
		$order->order_status = $order_status;
		
		// сохранение результатов
		$new_order = $this->Orders->saveOrder($order);
/*
		// уведомления
		if ($recent_status != $order_status)
		{
			$status_caption = $this->Orders->getOrderStatusDescription($order->order_status);

			if ($this->user->user_group != 'admin')
			{
				Mailer::sendAdminNotification(
					Mailer::SUBJECT_NEW_ORDER_STATUS, 
					Mailer::NEW_ORDER_STATUS_NOTIFICATION, 
					0,
					$order->order_id, 
					0,
					"http://countrypost.ru/admin/showOrderDetails/{$order->order_id}",
					NULL,
					$this->Users,
					$status_caption);
			}
			
			if ($this->user->user_group != 'manager')
			{
				Mailer::sendManagerNotification(
					Mailer::SUBJECT_NEW_ORDER_STATUS, 
					Mailer::NEW_ORDER_STATUS_NOTIFICATION, 
					$order->order_manager,
					$order->order_id, 
					0,
					"http://countrypost.ru/manager/showOrderDetails/{$order->order_id}",
					$this->Managers,
					NULL,
					$status_caption);
			}

			Mailer::sendClientNotification(
				Mailer::SUBJECT_NEW_ORDER_STATUS, 
				Mailer::NEW_ORDER_STATUS_NOTIFICATION, 
				$order->order_id, 
				$order->order_client,
				"http://countrypost.ru/client/showOrderDetails/{$order->order_id}",
				$this->Clients,
				$status_caption);
		}*/
	}
	
	protected function updateOdetailStatuses()
	{
		try
		{
			if ( ! $this->user OR
				 ! $this->user->user_id OR
				!isset($_POST['order_id']) OR
				!is_numeric($_POST['order_id']))
			{
				throw new Exception('Доступ запрещен.');
			}
		
			$order_id = $_POST['order_id'];

			// роли и разграничение доступа
			$order = $this->getPrivilegedOrder(
				$order_id, 
				'Невозможно изменить статусы товаров. Заказ не найден.');
			
			// находим курс конвертации
			$this->load->model('CountryModel', 'Countries');
			$this->load->model('CurrencyModel', 'Currencies');
			$country = $this->Countries->getById($order->order_country);
		
			if ( ! $country)
			{
				throw new Exception('Невозможно конвертировать цену в доллары. Курс не найден.');
			}
			
			$cross_rate = $this->Currencies->getById($country->country_currency);
			
			if ( ! $cross_rate)
			{
				throw new Exception('Невозможно конвертировать цену в доллары. Попробуйте еще раз.');
			}
			
			// итерируем по товарам
			$this->load->model('OdetailModel', 'Odetails');
			$this->load->model('OdetailJointModel', 'Joints');
			
			$this->db->trans_begin();
			
			$total_price = 0;
			$total_pricedelivery = 0;
			$total_price_usd = 0;
			$total_pricedelivery_usd = 0;
			$joint_skip_deliveries = array();
			
			// поиск параметров в запросе
			foreach($_POST as $key=>$value)
			{
				// обычные товары
				if (stripos($key, 'odetail_status') === 0)
				{
					$odetail_id = str_ireplace('odetail_status', '', $key);
					if (!is_numeric($odetail_id)) continue;

					// сохранение результатов
					$odetail = $this->Odetails->getById($odetail_id);
					if ( ! $odetail) 
					{
						throw new Exception('Некоторые товары не найдены. Попоробуйте еще раз.');
					}
					
					// инициализация, такие товары контролировать на предмет конвертации стоимости вряд ли стоит
					if (empty($odetail->odetail_price)) 
					{
						$odetail->odetail_price = 
							isset($_POST['odetail_price'.$odetail_id]) ?
							$_POST['odetail_price'.$odetail_id] :
							0;
					}
					
					if (empty($odetail->odetail_pricedelivery)) 
					{
						$odetail->odetail_pricedelivery = 
							isset($_POST['odetail_pricedelivery'.$odetail_id]) ?
							$_POST['odetail_pricedelivery'.$odetail_id] :
							0;
					}
					
					if (empty($odetail->odetail_price_usd))
					{
						$odetail->odetail_price_usd = BaseController::convert($cross_rate, (float)$odetail->odetail_price);
					}
					
					if (empty($odetail->odetail_pricedelivery_usd))
					{
						$odetail->odetail_pricedelivery_usd = BaseController::convert($cross_rate, (float)$odetail->odetail_pricedelivery);
					}
					
					// конвертируем валюту для невыкупленных, доставленных и недоставленных товаров
					$current_status = $odetail->odetail_status;
					
					if ($current_status != 'purchased' AND $current_status != 'received' AND $current_status != 'not_delivered')
					{
						$odetail->odetail_price = 
							isset($_POST['odetail_price'.$odetail_id]) ?
							$_POST['odetail_price'.$odetail_id] :
							$odetail->odetail_price;
							
						$odetail->odetail_pricedelivery = 
							isset($_POST['odetail_pricedelivery'.$odetail_id]) ?
							$_POST['odetail_pricedelivery'.$odetail_id] :
							$odetail->odetail_pricedelivery;

						$odetail->odetail_price_usd = BaseController::convert($cross_rate, (float)$odetail->odetail_price);
						$odetail->odetail_pricedelivery_usd = BaseController::convert($cross_rate, (float)$odetail->odetail_pricedelivery);
					
						// размораживаем доставку у объединенных
						if ($odetail->odetail_joint_id)
						{
							$joint_skip_deliveries[$odetail->odetail_joint_id] = 0;
						}
					}
					else 
					{
						// замораживаем доставку у объединенных
						if ($odetail->odetail_joint_id AND 
							empty($joint_skip_deliveries[$odetail->odetail_joint_id]))
						{
							$joint_skip_deliveries[$odetail->odetail_joint_id] = 1;
						}
					}
					
					// меняем статус
					$odetail->odetail_status = $value;
					
					// подсчет сумм цен
					$total_price += $odetail->odetail_price;
					$total_price_usd += $odetail->odetail_price_usd;
					
					// для объединенных товаров доставку считаем ниже
					if ( ! $odetail->odetail_joint_id)
					{
						$total_pricedelivery += $odetail->odetail_pricedelivery;
						$total_pricedelivery_usd += $odetail->odetail_pricedelivery_usd;
					}
						
					// сносим флаг изменения товара клиентом
					if ($this->user->user_group == 'manager' OR
						$this->user->user_group == 'admin')
					{
						OdetailModel::unmarkUpdatedByClient($order, $odetail, $this->getOrderModel());
					}
					
					// сохраняем товар
					$new_odetail = $this->Odetails->addOdetail($odetail);
				}
			}
	
			// собираем объединенные товары
			foreach ($joint_skip_deliveries as $odetail_joint_id => $skip)
			{
				$joint = $this->Joints->getById($odetail_joint_id);
				if ( ! $joint) 
				{
					throw new Exception('Некоторые товары не найдены. Попоробуйте еще раз.');
				}
				
				// инициализация
				$odetail_joint_cost = isset($_POST["odetail_joint_cost$odetail_joint_id"]) ? $_POST["odetail_joint_cost$odetail_joint_id"] : NULL;

				if (!empty($odetail_joint_cost) AND
					is_numeric($odetail_joint_cost))
				{
					$joint->odetail_joint_cost = $odetail_joint_cost;
					$joint->odetail_joint_cost_usd = BaseController::convert($cross_rate, (float)$joint->odetail_joint_cost);
			
					$this->Joints->addOdetailJoint($joint);
				}
				
				// суммируем доставку
				$total_pricedelivery += $joint->odetail_joint_cost;
				$total_pricedelivery_usd += $joint->odetail_joint_cost_usd;
			}			
			
			// меняем статус заказа
			$status = $this->Odetails->getTotalStatus($order_id);
			
			if ( ! $status)
			{
				throw new Exception('Статус заказа не определен. Попоробуйте еще раз.');
			}
			
			$recent_status = $order->order_status;
			
			if ($recent_status != 'payed')
			{
				$order->order_status = $this->Orders->calculateOrderStatus($status);
			}
			
			$is_new_status = ($recent_status != $order->order_status);
			
			// считаем стоимость заказа
			$total_price_usd = ceil($total_price_usd);
			$total_pricedelivery_usd = ceil($total_pricedelivery_usd);
			
			$order->order_products_cost_local = $total_price;
			$order->order_delivery_cost_local = $total_pricedelivery;
			$order->order_products_cost = $total_price_usd;
			$order->order_delivery_cost = $total_pricedelivery_usd;
			$this->load->model('ConfigModel', 'Config');
			$order = $this->Orders->calculateCost($order, $this->Config);
				
			$new_order = $this->Orders->saveOrder($order);
			
			$this->db->trans_commit();
			
			$this->result->e = 1;			
			$this->result->m = 'Статусы товаров, цены и местная доставка успешно изменены.';
			
			if ($is_new_status)
			{
				// уведомления
				$this->load->model('ManagerModel', 'Managers');
				$this->load->model('ClientModel', 'Clients');
				$this->load->model('UserModel', 'Users');
				
				$status_caption = $this->Orders->getOrderStatusDescription($order->order_status);

				if ($this->user->user_group != 'admin')
				{
					Mailer::sendAdminNotification(
						Mailer::SUBJECT_NEW_ORDER_STATUS, 
						Mailer::NEW_ORDER_STATUS_NOTIFICATION, 
						0,
						$order->order_id, 
						0,
						"http://countrypost.ru/admin/showOrderDetails/{$order->order_id}",
						NULL,
						$this->Users,
						$status_caption);
				}
				
				if ($this->user->user_group != 'manager')
				{
					Mailer::sendManagerNotification(
						Mailer::SUBJECT_NEW_ORDER_STATUS, 
						Mailer::NEW_ORDER_STATUS_NOTIFICATION, 
						$order->order_manager,
						$order->order_id, 
						0,
						"http://countrypost.ru/manager/showOrderDetails/{$order->order_id}",
						$this->Managers,
						NULL,
						$status_caption);
				}

				Mailer::sendClientNotification(
					Mailer::SUBJECT_NEW_ORDER_STATUS, 
					Mailer::NEW_ORDER_STATUS_NOTIFICATION, 
					$order->order_id, 
					$order->order_client,
					"http://countrypost.ru/client/showOrderDetails/{$order->order_id}",
					$this->Clients,
					$status_caption);
			}
		}
		catch (Exception $e) 
		{
			$this->db->trans_rollback();
			$this->result->e = -1;	
			$this->result->m = $e->getMessage();
		}

		// ставим флаг для вывода сообщения в определенном месте страницы
		$this->result->join_status = 1;
		Stack::push('result', $this->result);

		// открываем детали заказа
		if (isset($order_id))
		{
			Func::redirect($this->config->item('base_url').$this->cname.'/showOrderDetails/'.$order_id);
		}
		else
		{
			Func::redirect($this->config->item('base_url').$this->cname);
		}
	}
	
	protected function sendOrderConfirmation($order_id)
	{
		try
		{
			// роли и разграничение доступа
			$order = $this->getPrivilegedOrder(
				$order_id, 
				'Невозможно отправить уведомление. Заказ не найден.');

			// уведомления
			$this->load->model('ClientModel', 'Clients');
			
			Mailer::sendClientNotification(
				Mailer::SUBJECT_ORDER_COMPLETE, 
				Mailer::ORDER_COMPLETE_NOTIFICATION, 
				$order->order_id, 
				$order->order_client,
				"http://countrypost.ru/client/showOrderDetails/{$order->order_id}",
				$this->Clients);
			
			$order->confirmation_sent = 1;
			$this->Orders->addOrder($order);
		}
		catch (Exception $e) 
		{
			$this->result->e = -1;			
			$this->result->m = $e->getMessage();
		}
		
		// открываем детали заказа
		if (isset($order_id))
		{
			Func::redirect($this->config->item('base_url').$this->cname.'/showOrderDetails/'.$order_id);
		}
		else
		{
			Func::redirect($this->config->item('base_url').$this->cname);
		}
	}
	
	protected function deleteOrder2in($oid)
	{
		try 
		{
			// безопасность
			if (!isset($oid) OR
				!is_numeric($oid))
			{
				throw new Exception('Доступ запрещен.');
			}
		
			// валидация пользовательского ввода
			$this->load->model('Order2InModel', 'Order2in');
		
			$o2o = $this->Order2in->getById((int) $oid);
			
			// роли и доступ
			if ($this->user->user_group == 'admin')
			{
				if ( ! $o2o)
				{
					throw new Exception('Заявка не найдена. Попробуйте еще раз.');
				}
			}
			else if ( ! $o2o OR $o2o->order2in_status != 'processing' OR $o2o->order2in_user != $this->user->user_id)
			{
				throw new Exception('Заявка не найдена. Попробуйте еще раз.');
			}
			
			// сохранение результата
			$this->db->trans_begin();
			/*
			if ($this->user->user_group == 'admin' AND $o2o->order2in_status == 'payed')
			{
				$payment_obj = new stdClass();
				$payment_obj->payment_from			= $o2o->order2in_user;
				$payment_obj->payment_to			= 0;
				$payment_obj->payment_amount_from	= $o2o->order2in_amount;
				$payment_obj->payment_amount_to		= $o2o->order2in_amount;
				$payment_obj->payment_amount_tax	= 0;
				$payment_obj->payment_purpose		= 'отмена заявки на ввод';
				$payment_obj->payment_comment		= '№ '.$o2o->order2in_id;
				
				$this->load->model('PaymentModel', 'Payment');
					
				if ( ! $this->Payment->makePayment($payment_obj)) 
				{
					throw new Exception('Ошибка снятия денег со счета клиента. Попробуйте еще раз.');
				}
				
				// сохраняем результат в сессии
				if ($this->user->user_group == 'admin')
				{
					$this->session->set_userdata(array('user_coints' => $this->user->user_coints + $payment_obj->payment_amount_from));
				}
				else
				{
					$this->session->set_userdata(array('user_coints' => $this->user->user_coints - $payment_obj->payment_amount_from));
				}
			}*/
			
			$o2o->order2in_status = 'deleted';
			
			if ( ! $this->Order2in->addOrder($o2o)) 
			{
				throw new Exception('Заявка не удалена. Попробуйте еще раз.');
			}		
			
			$this->db->trans_commit();
			$this->result->r = 1;
			$this->result->message = 'Заявка успешно удалена.';
		}
		catch (Exception $e)
		{
			$this->db->trans_rollback();
			$this->result->r = $e->getCode();
			$this->result->message = $e->getMessage();
		}

		Stack::push('result', $this->result);
		
		if ($this->user->user_group == 'admin')
		{
			Func::redirect($this->config->item('base_url').'syspay/showClientOpenOrders2In');
		}
		else
		{
			Func::redirect($this->config->item('base_url').'syspay');
		}
	}

	protected function deletePayment($payment_id)
	{
		try
		{
			// безопасность
			if ( ! isset($payment_id) OR
				! is_numeric($payment_id))
			{
				throw new Exception('Доступ запрещен.');
			}

			// валидация пользовательского ввода
			$this->load->model('Order2InModel', 'Payments');

			// роли и разграничение доступа
			$payment = $this->getPrivilegedOrder2In(
				$payment_id,
				'Заявка не найдена. Попробуйте еще раз.');

			if ($payment->order2in_status == 'payed' AND
				$this->user->user_group != 'admin')
			{
				throw new Exception('Невозможно удалить выплаченную заявку.');
			}

			// сохранение результата
			$payment->order2in_status = 'deleted';

			if ( ! $this->Payments->addOrder($payment))
			{
				throw new Exception('Заявка не удалена. Попробуйте еще раз.');
			}
		}
		catch (Exception $e)
		{
		}

		Func::redirect($_SERVER['HTTP_REFERER']);
	}

	public function getPayments()
	{
		if ( ! $this->user)	return FALSE;
		
		$this->load->model('PaymentModel', 'Payment');
	}

	protected function delOrderComment($order_id, $comment_id){
		
		try
		{
			(int)	$order_id;
			(int)	$comment_id;
			
			// роли и разграничение доступа
			$this->getPrivilegedOrder(
				$order_id, 
				'Невозможно удалить комментарий. Соответствующий заказ недоступен.');
			
			// сохранение результатов
			$this->load->model('OCommentModel', 'Comments');
			
			if ( ! $this->Comments->delComment($comment_id))
			{
				throw new Exception('Комментарий не удален. Попробуйте еще раз.');
			}			

		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}
		
		// открываем комментарии к посылке
		Func::redirect($this->config->item('base_url').$this->cname.'/showOrderDetails/'.$order_id);
	}
	
	protected function get_paging($per_page = NULL)
	{
		$config['uri_segment'] = $this->paging_uri_segment;
		$config['base_url'] = $this->paging_base_url;
		$config['per_page'] = isset($per_page) ? $per_page : $this->per_page;
		$config['total_rows'] = $this->paging_count;

		$this->pagination->initialize($config);

		$pagerViewData = array(
			'pagination' => $this->pagination,
			'baseUrl' => $this->paging_base_url
		);
		$pagerView = $this->load->view('main/elements/pager', $pagerViewData, TRUE);

		return $pagerView;
	}

	protected function init_paging()
	{
		$this->load->helper('url');
		$this->load->library('pagination');

		$segment_count = $this->uri->total_segments();
		
		if ($this->uri->segment(1) == 'syspay')
		{
			$this->paging_base_url = '/syspay/'.$this->uri->segment(2);
		}
		else if ($this->uri->segment(1) == 'dealers')
		{
			$this->paging_base_url = '/dealers/index';
		}
		else if ($this->uri->segment(1) == 'clients')
		{
			$this->paging_base_url = '/clients/index';
		}
		else if ($segment_count == 1)
		{
			$this->paging_base_url = '/admin/showNewPackages';
		}
		else if ($this->user)
		{
			$this->paging_base_url = '/'.$this->user->user_group.'/'.$this->uri->segment(2);
		}

		if ($this->uri->segment(1) == 'main' AND
			$this->uri->segment(2) == 'showCategory')
		{
			$this->paging_base_url = '/main/showCategory/'.
				($this->uri->segment(3) ? $this->uri->segment(3) : 0).'/'.
				($this->uri->segment(4) ? $this->uri->segment(4) : 0);
			$this->paging_uri_segment = 5;
			$this->paging_offset = $this->uri->segment(5);
		}
		else
		{
			$this->paging_uri_segment = 3;
			$this->paging_offset = $this->uri->segment(3);
		}
	}
	
	protected function showPaymentHistory()
	{
		$this->load->model('PaymentModel', 'Payment');

		if ($this->user->user_group == 'client')
		{
			$view['Payments'] = $this->Payment->getFilteredPayments("(user_to.user_id={$this->user->user_id} OR
			user_from.user_id = '{$this->user->user_id}')
			AND payment_type = 'order'");
		}
		else if ($this->user->user_group == 'manager')
		{
			$view['filter'] = $this->initFilter('paymentHistory');
			$view['Payments'] = $this->Payment->getFilteredPayments(
				$view['filter']->condition, 
				$view['filter']->from, 
				$view['filter']->to,
				" AND (user_to.user_id={$this->user->user_id} OR user_from.user_id={$this->user->user_id}) 
				AND (payment_amount_from <> 0 
					OR payment_amount_to <> 0 
					OR (payment_type <> 'order' AND payment_type <> 'package' AND payment_type <> 'extra_payment'))");

			$view['total_usd'] = $this->Payment->getTotalUSD($view['Payments']);

			if ($totals = $this->Payment->getTotalLocal($view))
			{
				$view['total_local'] = $totals;
			}
		}
		else if ($this->user->user_group == 'admin')
		{
			$view['filter'] = $this->initFilter('paymentHistory');
			$view['Payments'] = $this->Payment->getFilteredPayments(
				$view['filter']->condition,
				$view['filter']->from,
				$view['filter']->to);

			$view['total_usd'] = $this->Payment->getTotalUSD($view['Payments']);

			if ($totals = $this->Payment->getTotalLocal($view))
			{
				$view['total_local'] = $totals;
			}
		}

		/* пейджинг */
		$per_page = isset($this->session->userdata['payments_per_page']) ? $this->session->userdata['payments_per_page'] : $this->per_page;
		$this->per_page = $per_page;
		$view['per_page'] = $per_page;

		$this->init_paging();
		$this->paging_count = count($view['Payments']);
	
		if (isset($view['Payments']) AND $view['Payments'])
		{
			$view['Payments'] = array_slice($view['Payments'], $this->paging_offset, $this->per_page);
		}			
			
		$view['pager'] = $this->get_paging();

		// собираем платежные системы
		$this->load->model('PaymentServiceModel', 'Services');
		$view['services'] = $this->Services->getList();		
		$view['statuses'] = $this->Payment->getStatuses();
		$view['countrypost_statuses'] = $this->Payment->getCountrypostStatuses();
		$view['user'] = $this->user;

		// крошки
		Breadcrumb::setCrumb(array(
			"/{$this->user->user_group}/history"  =>
			"Статистика платежей"), 1,	TRUE);

		// парсим шаблон
		if ($this->uri->segment(4) == 'ajax')
		{
        	$view['selfurl'] = $this->config->item('base_url').$this->cname.'/';
			$view['viewpath'] = $this->viewpath;
			$this->load->view($this->viewpath."ajax/showPaymentHistory", $view);
		}
		else
		{
			View::showChild($this->viewpath.'pages/showPaymentHistory', $view);
		}
	}
	
	protected function joinProducts($order_id)
	{
		try
		{
			if ( ! is_numeric($order_id))
			{
				throw new Exception('Доступ запрещен.');
			}
		
			// безопасность и разграничение доступа
			$order = $this->getPrivilegedOrder(
				$order_id, 
				'Невозможно объединить товары. Указанный заказ не найден.');

			$this->load->model('OdetailModel', 'Odetails');
			$this->load->model('OdetailJointModel', 'Joints');

			// позволяет ли текущий статус объединение
			$editable_statuses = $this->getEditableOrderStatuses();

			if ( ! in_array($order->order_status, $editable_statuses))
			{
				throw new Exception('Заказ недоступен.');
			}

			// погнали
			$joint = $this->Joints->generateJoint();

			// ищем товары в запросе
			foreach($_POST as $param => $value)
			{
				$odetail_id = $this->getCheckboxId($param, 'join');

				// находим товар
				$odetail = $this->getPrivilegedOdetail($order_id, $odetail_id);

				if ( ! $odetail)
				{
					throw new Exception('Невозможно объединить товары. Некоторые товары отсутствуют в товаре.');
				}
				
				// вычисляем суммарную стоимость
				$joint->cost += $odetail->odetail_pricedelivery;
				$joint->count++;
				
				// удаляем старые джоинты
				if ($odetail->odetail_joint_id)
				{
					$this->Odetails->clearJoints($odetail->odetail_joint_id);
				}
				
				// сохраняем товар
				$odetail->odetail_joint_id = $joint->joint_id;
			
				$this->Odetails->addOdetail($odetail);
			}

			if ($joint->count < 2)
			{
				throw new Exception('Выберите хотя бы 2 товара для объединения.');
			}

			$this->Joints->addOdetailJoint($joint);

			// пересчитываем заказ
			if ( ! $this->Orders->recalculate($order))
			{
				throw new Exception('Невозможно пересчитать стоимость заказа. Попоробуйте еще раз.');
			}

			$this->Orders->saveOrder($order);
		}
		catch (Exception $e) 
		{
		}

		// открываем детали заказа
		if (isset($order) AND $order)
		{
			Func::redirect($this->config->item('base_url')."{$this->cname}/order/{$order->order_id}");
		}
		else
		{
			Func::redirect($this->config->item('base_url'));
		}
	}
	
	protected function removeJoint($order_id, $joint_id)
	{
		try
		{
			if ( ! is_numeric($joint_id) OR
				! is_numeric($order_id))
			{
				throw new Exception('Доступ запрещен.');
			}
		
			// безопасность: проверяем связку менеджера и заказа
			$order = $this->getPrivilegedOrder(
				$order_id, 
				'Заказ не найден.');

			$this->load->model('OdetailModel', 'Odetails');
			$this->load->model('OdetailJointModel', 'Joint	s');

			// погнали
			$this->db->trans_begin();
			
			$this->Odetails->clearJoints($joint_id);

			// пересчитываем заказ
			if ( ! $this->Orders->recalculate($order))
			{
				throw new Exception('Невозможно пересчитать стоимость заказа. Попоробуйте еще раз.');
			}

			$this->Orders->saveOrder($order);

			// закрываем транзакцию
			if ($this->db->trans_status() !== FALSE)
			{
				$this->db->trans_commit();
			}
		}
		catch (Exception $e) 
		{
		}

		// открываем детали заказа
		if (isset($order) AND $order)
		{
			Func::redirect($this->config->item('base_url') . "{$this->cname}/order/{$order->order_id}");
		}
		else
		{
			Func::redirect($this->config->item('base_url'));
		}
	}

    protected function moveProducts($old_order_id, $new_order_id)
	{
		try
		{
			// безопасность и разграничение доступа
			$old_order = $this->getMovableOrder(
				$old_order_id,
				'Невозможно переместить выбранные товары. Исходный заказ не найден.');

			$new_order = $this->getMovableOrder(
				$new_order_id,
				'Невозможно переместить выбранные товары. Новый заказ не найден.');

			$this->load->model('OdetailModel', 'Odetails');

			// ищем товары в запросе
			foreach($_POST as $param => $value)
			{
				$odetail_id = $this->getCheckboxId($param, 'join');

				// находим товар
				$odetail = $this->getPrivilegedOdetail($old_order_id, $odetail_id);

				if ( ! $odetail)
				{
					throw new Exception('Товар не найден.');
				}

				// сохраняем товар
				$odetail->odetail_order = $new_order_id;

				// удаляем старые джоинты
				if ($odetail->odetail_joint_id)
				{
					$this->Odetails->clearJoints($odetail->odetail_joint_id);
					$odetail->odetail_joint_id = 0;
				}

				$this->Odetails->addOdetail($odetail);
			}

			// пересчитываем заказы
			if ( ! $this->Orders->recalculate($old_order))
			{
				throw new Exception('Невозможно пересчитать стоимость заказа. Попоробуйте еще раз.');
			}

			$this->Orders->saveOrder($old_order);

			if ( ! $this->Orders->recalculate($new_order))
			{
				throw new Exception('Невозможно пересчитать стоимость заказа. Попоробуйте еще раз.');
			}

			$this->Orders->saveOrder($new_order);
		}
		catch (Exception $e)
		{
		}
	}

	public function updatePerPage($per_page)
	{
		if (!is_numeric($per_page) OR
			 ! $this->user)
		{
			throw new Exception('Доступ запрещен.');
		}
	
		$this->session->set_userdata(array('new_packages_per_page' => $per_page));
		Func::redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function updatePaymentsPerPage($per_page)
	{
		if (!is_numeric($per_page) OR
			 ! $this->user)
		{
			throw new Exception('Доступ запрещен.');
		}
	
		$this->session->set_userdata(array('payments_per_page' => $per_page));
		Func::redirect($_SERVER['HTTP_REFERER']);
	}

    public function updateTaxesPerPage($per_page)
	{
		if (!is_numeric($per_page) OR
			 ! $this->user)
		{
			throw new Exception('Доступ запрещен.');
		}

		$this->session->set_userdata(array('taxes_per_page' => $per_page));
		Func::redirect($_SERVER['HTTP_REFERER']);
	}

    protected function deleteProduct($odid)
    {
        try
        {
            if ( ! is_numeric($odid))
            {
                throw new Exception('Доступ запрещен.');
            }

            // безопасность: проверяем связку клиента и товара
            $this->load->model('OdetailModel', 'ODetails');
            $this->load->model('OdetailJointModel', 'Joints');

            $odetail = $this->ODetails->getById($odid);

            if ( ! $odetail)
            {
                throw new Exception('Товар не найден. Попробуйте еще раз.');
            }

            // находим заказ
            $order = $this->getPrivilegedOrder(
                $odetail->odetail_order,
                'Невозможно изменить статусы товаров. Заказ не найден.');

            $odetail->odetail_status = 'deleted';

            // удаляем товар из объединенной доставки
            if ($odetail->odetail_joint_id)
            {
                $joint = $this->Joints->getById($odetail->odetail_joint_id);

                if ( ! $joint)
                {
                    throw new Exception('Товар не найден. Попробуйте еще раз.');
                }

                // сносим объединенную посылку
                if ($joint->count < 3)
                {
                    $this->ODetails->clearJoints($joint->joint_id);
                }
                // или правим ее
                else
                {
                    $joint->count = $joint->count - 1;
                    $this->Joints->addOdetailJoint($joint);
                }
            }

			$deleted_odetail = $this->ODetails->addOdetail($odetail);

            if ( ! $deleted_odetail)
            {
                throw new Exception('Товар не удален. Попробуйте еще раз.');
            }

			// пересчитываем заказ
			if ( ! $this->Orders->recalculate($order))
			{
				throw new Exception('Невозможно пересчитать стоимость заказа. Попоробуйте еще раз.');
			}

			$this->Orders->saveOrder($order);
		}
        catch (Exception $e)
        {
        }
		if(isset($_SERVER['HTTP_REFERER']))
		{
			Func::redirect($_SERVER['HTTP_REFERER']);
		}
		else
		{
			Func::redirect(base_url());
		}
    }

    protected function deleteNewProduct($oid, $odid)
    {
        try
        {
            // безопасность: проверяем связку клиента и товара
            $this->load->model('OdetailModel', 'ODetails');
            $this->load->model('OdetailJointModel', 'Joints');

            if (empty($this->user))
            {
                $odetail = $this->ODetails->getById($odid);
            }
            else if ($this->user->user_group == 'client')
            {
                $odetail = $this->ODetails->getClientOdetailById($oid, $odid, $this->user->user_id);

                if (empty($odetail))
                {
                    $client_id = UserModel::getTemporaryKey(true);
                    $odetail = $this->ODetails->getClientOdetailById($oid, $odid, $client_id);
                }
            }
            else if ($this->user->user_group == 'admin')
            {
                $odetail = $this->ODetails->getById($odid);
            }

            if ( ! $odetail)
            {
                throw new Exception('Товар не найден. Попробуйте еще раз.');
            }

            // находим заказ
            $order = $this->getNewOrder(
                $odetail->odetail_order,
                'Невозможно изменить статусы товаров. Заказ не найден.');

            // сохранение результатов
            if ((!empty($this->user) AND $this->user->user_group == 'client') OR empty($this->user))
            {
                OdetailModel::markUpdatedByClient($order, $odetail, $this->getOrderModel());
            }

            $this->db->trans_begin();

            // удаляем товар из объединенной доставки
            if ($odetail->odetail_joint_id)
            {
                $joint = $this->Joints->getById($odetail->odetail_joint_id);

                if ( ! $joint)
                {
                    throw new Exception('Товар не найден. Попробуйте еще раз.');
                }

                // сносим объединенную посылку
                if ($joint->count < 3)
                {
                    $this->ODetails->clearJoints($joint->joint_id);
                }
                // или правим ее
                else
                {
                    $joint->odetail_joint_count = $joint->count - 1;
                    $this->Joints->addOdetailJoint($joint);
                }
            }

            $odetail->odetail_status = 'deleted';

            // TODO : Возвращает BOOL значение а не объект, работает каким-то чудом =)
            $deleted_odetail = $this->ODetails->addOdetail($odetail);

            if ( ! $deleted_odetail)
            {
                throw new Exception('Товар не удален. Попробуйте еще раз.');
            }

            $this->db->trans_commit();

            // открываем заказы
            Stack::push('result', $this->result);
            $this->result->e = 1;
            $this->result->join_status = 1;
            $this->result->m = 'Товар успешно удален.';

            echo json_encode($this->result);
            exit;
        }
        catch (Exception $e)
        {
            $this->db->trans_rollback();

            $this->result->e = -1;
            $this->result->m = $e->getMessage();

            echo json_encode($this->result);
            exit;
        }

        //Func::redirect($_SERVER['HTTP_REFERER']);
    }
	
	private function putStatistics(&$view)
	{
		// TODO: оптимизировать. выборка всех заказов не нужна, только счетчики
		$this->load->model('OrderModel', 'Orders');

		if ($this->user->user_group == 'client')
		{
			$view['new_orders'] = $this->Orders->getOrders(NULL, 'open', $this->user->user_id, NULL);
			$view['payed_orders'] = $this->Orders->getOrders(NULL, 'payed', $this->user->user_id, NULL);
			$view['sent_orders'] = $this->Orders->getOrders(NULL, 'sent', $this->user->user_id, NULL);
			$view['bid_orders'] = 0;
		}
		else if ($this->user->user_group == 'manager')
		{
			$view['new_orders'] = $this->Orders->getOrders(NULL, 'open', NULL, $this->user->user_id);
			$view['payed_orders'] = $this->Orders->getOrders(NULL, 'payed', NULL, $this->user->user_id);
			$view['sent_orders'] = $this->Orders->getOrders(NULL, 'sent', NULL, $this->user->user_id);
			$view['bid_orders'] = $this->Orders->getOrders(NULL, 'bid', NULL, $this->user->user_id);
		}

		$view['new_orders'] = intval($view['new_orders']) ? count($view['new_orders']) : 0;
		$view['payed_orders'] = intval($view['payed_orders']) ? count($view['payed_orders']) : 0;
		$view['sent_orders'] = intval($view['sent_orders']) ? count($view['sent_orders']) : 0;
		$view['bid_orders'] = intval($view['bid_orders']) ? count($view['bid_orders']) : 0;
	}
	
	protected function getPrivilegedOrder($order_id, $validate = TRUE)
	{
		$order = FALSE;
		$model = $this->getOrderModel();

		// залогиненным показываем только их заказ, либо заказ с их предложением
		if (isset($this->user->user_group))
		{
			switch ($this->user->user_group)
			{
				case 'manager' : 
					$order = $model->getManagerOrderById($order_id, $this->user->user_id);
					break;
				case 'client' : 
					$order = $model->getClientOrderById($order_id, $this->user->user_id);
					break;
				case 'admin' : 
					$order = $model->getById($order_id);
					break;
			}
		}
		else
		{
			$order = $model->getIsCreatingOrder($order_id, UserModel::getTemporaryKey());
		}

		// валидация
		if ($validate AND
			empty($order))
		{
			throw new Exception($validate);
		}

		return $order;
	}
/*
	protected function getPrivilegedProduct($odetail_id, $validate = TRUE)
	{
		$order = FALSE;
		$model = $this->getOdetailModel();

		// залогиненным показываем только их заказ, либо заказ с их предложением
		if (isset($this->user->user_group))
		{
			switch ($this->user->user_group)
			{
				case 'client' :
					$order = $model->getClientOrderById($order_id, $this->user->user_id);
					break;
				// TODO: добавить логику админа и посредника
			}
		}

		// валидация
		if ($validate AND
			empty($odetail))
		{
			throw new Exception($validate);
		}

		return $order;
	}
*/
	protected function getMovableOrder($order_id, $validate = TRUE)
	{
		if ( ! is_numeric($order_id))
		{
			throw new Exception('Доступ запрещен.');
		}

		// безопасность и разграничение доступа
		$order = $this->getPrivilegedOrder(
			$order_id,
			$validate);

		// позволяет ли текущий статус перенос
		$editable_statuses = $this->getEditableOrderStatuses();

		if ( ! in_array($order->order_status, $editable_statuses))
		{
			throw new Exception("Заказ №$order_id недоступен для переноса товаров.");
		}

		return $order;
	}

	protected function getCheckboxId($param, $checkboxName)
	{
		if (stripos($param, $checkboxName) === FALSE)
		{
			return FALSE;
		}

		$id = str_ireplace($checkboxName, '', $param);

		if ( ! is_numeric($id))
		{
			return FALSE;
		}

		return $id;
	}

    protected function getPrivilegedOrders2In($view, $status = 'open', $validate = TRUE)
	{
		$orders = FALSE;
		$model = $this->getOrder2InModel();

		// залогиненным показываем только их заказ, либо заказ с их предложением
		if (isset($this->user->user_group))
		{
			switch ($this->user->user_group)
			{
				case 'manager' :
					$orders = $model->getFilteredOrders(array(
						'order2in_to' => $this->user->user_id,
						'order_id' => $view['order']->order_id,
						'is_countrypost' => 0
					), $status);
					break;
				case 'client' :
					$orders = $model->getFilteredOrders(array(
						'order2in_user' => $this->user->user_id,
						'order_id' => $view['order']->order_id
					), $status);
					break;
				case 'admin' :
					break;
			}
		}

		// валидация
		if ($validate AND
			empty($orders))
		{
			throw new Exception($validate);
		}

		return $orders;
	}

    protected function getAllPrivilegedOrders2In($filter, $status = 'open', $validate = TRUE)
	{
		$orders = FALSE;
		$model = $this->getOrder2InModel();

		// залогиненным показываем только их заказ, либо заказ с их предложением
		if (isset($this->user->user_group))
		{
			switch ($this->user->user_group)
			{
				case 'manager' :
					$orders = $model->getFilteredOrders(array(
						'order2in_to' => $this->user->user_id,
						'is_countrypost' => 0
					), $status);
					break;
				case 'client' :
					$orders = $model->getFilteredOrders(array(
						'order2in_user' => $this->user->user_id,
					), $status);
					break;
				case 'admin' :
					$orders = $model->getFilteredOrders($filter->condition,
						$status,
						$filter->from,
						$filter->to);
					break;
			}
		}

		// валидация
		if ($validate AND
			empty($orders))
		{
			throw new Exception($validate);
		}

		return $orders;
	}

    protected function getPrivilegedOrder2In($o2i_id, $validate = TRUE)
	{
		$o2i = FALSE;
		$model = $this->getOrder2InModel();

		// залогиненным показываем только их заказ, либо заказ с их предложением
		if (isset($this->user->user_group))
		{
			switch ($this->user->user_group)
			{
				case 'manager' :
					$o2i = $model->getManagersO2iById($o2i_id, $this->user->user_id);
					break;
				case 'client' :
					$o2i = $model->getClientsO2iById($o2i_id, $this->user->user_id);
					break;
				case 'admin' :
					$o2i = $model->getById($o2i_id);
					break;
			}
		}

		// валидация
		if ($validate AND
			empty($o2i))
		{
			throw new Exception($validate);
		}

		return $o2i;
	}

    protected function getNewOrder($order_id, $validate = TRUE)
    {
        $order = FALSE;
        $model = $this->getOrderModel();

        // залогиненным показываем только их заказ, либо заказ с их предложением
        if (isset($this->user->user_group) AND $this->user->user_group != 'manager')
        {
            switch ($this->user->user_group)
            {
                case 'client' :
                    $order = $model->getClientOrderById($order_id, $this->user->user_id);
                    break;
                case 'admin' :
                    $order = $model->getById($order_id);
                    break;
            }
        }
        // если пользователь не авторизован пробуем достать временный заказ
        elseif (empty($this->user))
        {
            $temp_user_id = UserModel::getTemporaryKey();
            $order = $model->getClientOrderById($order_id, $temp_user_id);
        }

        // валидация
        if ($validate AND
            empty($order))
        {
            throw new Exception($validate);
        }

        return $order;
    }
	
	protected function getPublicOrder($order_id, $validate = TRUE)
	{
		$model = $this->getOrderModel();

		// без логина показываем только новые заказы без выбранного посредника
		$order = $model->getById($order_id);

		if (empty($order) OR
			$order->order_status == 'deleted' OR
			$order->order_manager OR
			$order->order_status != 'pending')
		{
			$order = FALSE;
		}

		// валидация
		if ($validate AND
			empty($order))
		{
			throw new Exception($validate);
		}

		return $order;
	}

	private function getPrivilegedBid($bid_id, $validate = TRUE)
	{
		$bid = $this->Bids->getPrivilegedBid(
			$bid_id,
			$this->user->user_id,
			$this->user->user_group
		);

		if ($validate AND
			empty($bid))
		{
			throw new Exception($validate);
		}
		
		return $bid;
	}
	
	private function getOrderModel()
	{
		if (empty($this->Orders))
		{
			$this->load->model('OrderModel', 'Orders');
		}

		return $this->Orders;
	}
	
	private function getOdetailModel()
	{
		if (empty($this->Odetails))
		{
			$this->load->model('OdetailModel', 'Odetails');
		}

		return $this->Odetails;
	}

	private function getOrder2InModel()
	{
		if (empty($this->Orders2In))
		{
			$this->load->model('Order2InModel', 'Orders2In');
		}

		return $this->Orders2In;
	}

	protected function processStatistics($personal_data, $statistics, $id_field_name = 'user_id', $user_id = 0, $user_group = 'manager')
	{
		if (isset($statistics[$personal_data->$id_field_name]))
		{
			// нашли в кэше
			$personal_data->statistics = $statistics[$personal_data->$id_field_name];
		}
		else if (isset($personal_data->$id_field_name))
		{
			if ($user_group == 'manager')
			{
				$personal_data->statistics = $this->Managers->getStatistics($personal_data->$id_field_name);
			}
			else if ($user_group == 'client')
			{
                $personal_data->statistics = $this->Clients->getStatistics($personal_data->$id_field_name);
			}
		
			// кэш данных пользователей
            $statistics[$personal_data->$id_field_name] = $personal_data->statistics;
		}
	}

	protected function uploadOrderScreenshot($odetail, $client_id)
	{
		$max_width	= 1024;
		$max_height	= 768;
		$filename	= $_SERVER['DOCUMENT_ROOT'] . "/upload/orders/$client_id/{$odetail->odetail_id}.jpg";

		$config['upload_path']			= $_SERVER['DOCUMENT_ROOT']."/upload/orders/$client_id";
		$config['allowed_types']		= 'gif|jpeg|jpg|png|GIF|JPEG|JPG|PNG';
		$config['max_size']				= '3072';
		$config['encrypt_name'] 		= TRUE;

		$this->uploadImageGeneric($max_width, $max_height, $config, $filename);
	}

	private function uploadImageGeneric($max_width, $max_height, $config, $filename)
	{
			// создаем новый каталог для картинок клиента
		if ( ! is_dir($config['upload_path']))
		{
			mkdir($config['upload_path'], 0777);
		}

		$this->load->library('upload', $config);

		// загружаем файл
		if ( ! $this->upload->do_upload())
		{
			throw new Exception(strip_tags(trim($this->upload->display_errors())));
		}

		$uploadedImg = $this->upload->data();

		// прибиваем старый файл
		if (file_exists($filename))
		{
			unlink($filename);
		}

		// копируем новый из папки temp
		if ( ! rename($uploadedImg['full_path'], $filename))
		{
			throw new Exception("Переименуйте файл и загрузите его еще раз.");
		}

		$imageInfo = getimagesize($filename);

		// ресайзим большие картинки
		if ($imageInfo[0] > $max_width OR $imageInfo[1] > $max_height)
		{
			$config['image_library']	= 'gd2';
			$config['source_image']		= $filename;
			$config['maintain_ratio']	= TRUE;
			$config['width']			= $max_width;
			$config['height']			= $max_height;

			$this->load->library('image_lib', $config);
			$this->image_lib->resize();
		}
	}

	protected function update_odetail_weight($order_id, $odetail_id, $weight)
	{
		try
		{
			if ( ! is_numeric($order_id) OR
				! is_numeric($odetail_id) OR
				! is_numeric($weight))
			{
				throw new Exception('Доступ запрещен.');
			}

			// роли и разграничение доступа
			$order = $this->getPrivilegedOrder(
				$order_id,
				"Заказ недоступен.");

			$this->load->model('OrderModel', 'Orders');
			$this->load->model('OdetailModel', 'Odetails');
			$this->load->model('OdetailJointModel', 'Joints');

			// позволяет ли текущий статус редактирование
			$editable_statuses = $this->getEditableOrderStatuses();

			if ( ! in_array($order->order_status, $editable_statuses))
			{
				throw new Exception('Заказ недоступен.');
			}

			// находим товар
			$odetail = $this->getPrivilegedOdetail($order_id, $odetail_id);

			if (empty($odetail))
			{
				throw new Exception('Товар не найден.');
			}

			$odetail->odetail_weight = $weight;

			// сохранение результатов
			$this->Odetails->addOdetail($odetail);

			// пересчитываем заказ
			if ( ! $this->Orders->recalculate($order))
			{
				throw new Exception('Невозможно пересчитать стоимость заказа. Попоробуйте еще раз.');
			}

			$this->Orders->saveOrder($order);

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

	protected function update_odetail_price($order_id, $odetail_id, $price)
	{
		try
		{

            if ( ! is_numeric($order_id) OR
				! is_numeric($odetail_id) OR
				! is_numeric($price))
			{
				throw new Exception('Доступ запрещен.');
			}

			// роли и разграничение доступа
			$order = $this->getPrivilegedOrder(
				$order_id,
				"Заказ недоступен.");

			$this->load->model('OrderModel', 'Orders');
			$this->load->model('OdetailModel', 'Odetails');
			$this->load->model('OdetailJointModel', 'Joints');

			// позволяет ли текущий статус редактирование
			$editable_statuses = $this->getEditableOrderStatuses();

			if ( ! in_array($order->order_status, $editable_statuses))
			{
				throw new Exception('Заказ недоступен.');
			}

			// находим товар
			$odetail = $this->getPrivilegedOdetail($order_id, $odetail_id);

			if (empty($odetail))
			{
				throw new Exception('Товар не найден.');
			}

            $price = 4321;
            $price = ReformatBigNumber($price);
            $odetail->odetail_price = $price;

			// сохранение результатов
			$this->Odetails->addOdetail($odetail);

			// пересчитываем заказ
			if ( ! $this->Orders->recalculate($order))
			{
				throw new Exception('Невозможно пересчитать стоимость заказа. Попоробуйте еще раз.');
			}

			$this->Orders->saveOrder($order);

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

	protected function update_odetail_tracking($order_id, $odetail_id, $tracking)
	{
		try
		{
			if ( ! is_numeric($order_id) OR
				! is_numeric($odetail_id))
			{
				throw new Exception('Доступ запрещен.');
			}

			// роли и разграничение доступа
			$order = $this->getPrivilegedOrder(
				$order_id,
				"Заказ недоступен.");

			$this->load->model('OrderModel', 'Orders');
			$this->load->model('OdetailModel', 'Odetails');
			$this->load->model('OdetailJointModel', 'Joints');

			// позволяет ли текущий статус редактирование
			$editable_statuses = $this->getEditableOrderStatuses();

			if ( ! in_array($order->order_status, $editable_statuses))
			{
				throw new Exception('Заказ недоступен.');
			}

			// находим товар
			$odetail = $this->getPrivilegedOdetail($order_id, $odetail_id);

			if (empty($odetail))
			{
				throw new Exception('Товар не найден.');
			}

			$odetail->odetail_tracking = $tracking;

			// сохранение результатов
			$this->Odetails->addOdetail($odetail);

			// пересчитываем заказ
			if ( ! $this->Orders->recalculate($order))
			{
				throw new Exception('Невозможно пересчитать стоимость заказа. Попоробуйте еще раз.');
			}

			$this->Orders->saveOrder($order);

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

	protected function update_odetail_pricedelivery($order_id, $odetail_id, $pricedelivery)
	{
		try
		{
			if ( ! is_numeric($order_id) OR
				! is_numeric($odetail_id) OR
				! is_numeric($pricedelivery))
			{
				throw new Exception('Доступ запрещен.');
			}

			// роли и разграничение доступа
			$order = $this->getPrivilegedOrder(
				$order_id,
				"Заказ недоступен.");

			$this->load->model('OrderModel', 'Orders');
			$this->load->model('OdetailModel', 'Odetails');
			$this->load->model('OdetailJointModel', 'Joints');

			// позволяет ли текущий статус редактирование
			$editable_statuses = $this->getEditableOrderStatuses();

			if ( ! in_array($order->order_status, $editable_statuses))
			{
				throw new Exception('Заказ недоступен.');
			}

			// находим товар
			$odetail = $this->getPrivilegedOdetail($order_id, $odetail_id);

			if (empty($odetail))
			{
				throw new Exception('Товар не найден.');
			}

			$odetail->odetail_pricedelivery = $pricedelivery;

			// сохранение результатов
			$this->Odetails->addOdetail($odetail);

			// пересчитываем заказ
			if ( ! $this->Orders->recalculate($order))
			{
				throw new Exception('Невозможно пересчитать стоимость заказа. Попоробуйте еще раз.');
			}

			$this->Orders->saveOrder($order);

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

	protected function update_joint_pricedelivery($order_id, $joint_id, $cost)
	{
		try
		{
			if ( ! is_numeric($order_id) OR
				! is_numeric($joint_id) OR
				! is_numeric($cost))
			{
				throw new Exception('Доступ запрещен.');
			}

			// роли и разграничение доступа
			$order = $this->getPrivilegedOrder(
				$order_id,
				"Заказ недоступен.");

			$this->load->model('OrderModel', 'Orders');
			$this->load->model('OdetailModel', 'Odetails');
			$this->load->model('OdetailJointModel', 'Joints');

			// позволяет ли текущий статус редактирование
			$editable_statuses = $this->getEditableOrderStatuses();

			if ( ! in_array($order->order_status, $editable_statuses))
			{
				throw new Exception('Заказ недоступен.');
			}

			// находим товар
			$joint = $this->Joints->getPrivilegedJoint(
				$order_id,
				$joint_id,
				$this->user->user_id,
				$this->user->user_group);

			if (empty($joint))
			{
				throw new Exception('Товары не найдены.');
			}

			$joint->cost = $cost;

			// сохранение результатов
			$this->Joints->addOdetailJoint($joint);

			// пересчитываем заказ
			if ( ! $this->Orders->recalculate($order))
			{
				throw new Exception('Невозможно пересчитать стоимость заказа. Попоробуйте еще раз.');
			}

			$this->Orders->saveOrder($order);

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

	protected function update_payment_status($order_id, $payment_id, $status)
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

			// валидация пользовательского ввода
			$statuses = $this->Payments->getStatuses();

			if (empty($statuses[$status]))
			{
				throw new Exception('Некорректный статус.');
			}

			// роли и разграничение доступа
			$payment = $this->getPrivilegedOrder2In(
				$payment_id,
				'Заявка не найдена. Попробуйте еще раз.');

			if ($payment->order2in_status == 'payed' AND
				$this->user->user_group != 'admin')
			{
				throw new Exception('Выплаченные заявки запрещено изменять.');
			}

			$payment->order2in_status = $status;

			// проверяем переводились ли уже деньги
			$is_money_sent = $payment->is_money_sent;

			// сохранение результатов
			$this->Orders2In->updateStatus($payment_id, $payment->order2in_status);

			// оплата заказа
			// ВНИМАНИЕ!! деньги переводятся только 1 раз, а статус заявки меняется неограниченно
			if ( ! $is_money_sent AND $payment->order2in_status == 'payed')
			{
				$order->order_cost_payed += $payment->order2in_amount;
				$this->processOrderPayment($order, $payment);
				// перевод остатков
				$this->Orders->payOrderWithExcessOrders($order, $payment);
			}

			// пересчитываем заказ
			if ( ! $this->Orders->recalculate($order))
			{
				throw new Exception('Невозможно пересчитать стоимость заказа. Попоробуйте еще раз.');
			}

			$this->Orders->saveOrder($order);

			// отправляем пересчитанные детали заказа
			$response = $this->prepareOrderUpdateJSON($order);

			$view_status = $this->Orders->getViewStatus($this->user->user_group, $status);

			$response['snippet'] = $this->showPayments(
				$order->order_id,
				$view_status,
				TRUE);
		}
		catch (Exception $e)
		{
			$response['is_error'] = TRUE;
			$response['message'] = $e->getMessage();
		}

		print(json_encode($response));
	}

	protected function processOrderPayment($order, $payment)
	{
		// записываем платеж в историю
		$this->load->model('PaymentModel', 'History');
		$this->History->processOrderPayment($order, $payment, ($order->order_cost_payed > 0));
	}

	protected function update_all_payment_status($payment_id, $status)
	{
		try
		{
			if ( ! is_numeric($payment_id))
			{
				throw new Exception('Доступ запрещен.');
			}

			// роли и разграничение доступа
			$payment = $this->getPrivilegedOrder2In(
				$payment_id,
				'Заявка не найдена. Попробуйте еще раз.');

			if ($payment->order2in_status == 'payed' AND
				$this->user->user_group != 'admin')
			{
				throw new Exception('Выплаченные заявки запрещено изменять.');
			}

			$payment->order2in_status = $status;

			// роли и разграничение доступа
			$order = $this->getPrivilegedOrder(
				$payment->order_id,
				"Заказ недоступен.");

			// валидация пользовательского ввода
			$this->load->model('Order2InModel', 'Payments');
			$statuses = $this->Payments->getStatuses();

			if (empty($statuses[$status]))
			{
				throw new Exception('Некорректный статус.');
			}


			// проверяем переводились ли уже деньги
			$is_money_sent = $payment->is_money_sent;

			// сохранение результатов
			$this->Orders2In->updateStatus($payment_id, $payment->order2in_status);

			// оплата заказа
			// ВНИМАНИЕ!! деньги переводятся только 1 раз, а статус заявки меняется неограниченно
			if ( ! $is_money_sent AND $payment->order2in_status == 'payed')
			{
				$order->order_cost_payed += $payment->order2in_amount;
				$this->processOrderPayment($order, $payment);
			}

			// пересчитываем заказ
			if ( ! $this->Orders->recalculate($order))
			{
				throw new Exception('Невозможно пересчитать стоимость заказа. Попоробуйте еще раз.');
			}

			$this->Orders->saveOrder($order);

			// отправляем пересчитанные детали заказа
			$response = $this->prepareOrderUpdateJSON($order);

			$view_status = $this->Orders->getViewStatus($this->user->user_group, $status);

			$response['snippet'] = $this->showAllPayments(
				$view_status,
				TRUE);
		}
		catch (Exception $e)
		{
			$response['is_error'] = TRUE;
			$response['message'] = $e->getMessage();
		}

		print(json_encode($response));
	}

	protected function update_payment_amount($order_id, $payment_id, $amount)
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

			$payment->order2in_amount = $amount;

			// сохранение результатов
			$this->Orders2In->updateAmount($payment_id, $payment->order2in_amount);

			// пересчитываем заказ
			if ( ! $this->Orders->recalculate($order))
			{
				throw new Exception('Невозможно пересчитать стоимость заказа. Попоробуйте еще раз.');
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

	public function updateProduct($order_id, $odetail_id)
	{
		try
		{
			if ( ! is_numeric($order_id) OR
				! is_numeric($odetail_id))
			{
				throw new Exception('Доступ запрещен.');
			}

			// роли и разграничение доступа
			$order = $this->getPrivilegedOrder(
				$order_id,
				"Заказ недоступен.");

			$this->load->model('OrderModel', 'Orders');
			$this->load->model('OdetailModel', 'Odetails');

			// позволяет ли текущий статус редактирование
			$editable_statuses = $this->getEditableOrderStatuses();

			if ( ! in_array($order->order_status, $editable_statuses))
			{
				throw new Exception('Заказ недоступен.');
			}

			// находим товар
			$odetail = $this->getPrivilegedOdetail($order_id, $odetail_id);

			if (empty($odetail))
			{
				throw new Exception('Товар не найден.');
			}

			// парсим пользовательский ввод
			$odetail->odetail_link				= Check::str('link', 500, 1, '');
			$odetail->odetail_product_name		= Check::str('name', 255, 0, '');
			$odetail->odetail_shop				= Check::str('shop', 255, 0, '');
			$odetail->odetail_product_color		= Check::str('color', 255, 0, '');
			$odetail->odetail_product_size		= Check::str('size', 255, 0, '');
			$odetail->odetail_volume			= Check::float('volume');
			$odetail->odetail_tnved				= Check::int('tnved');
			$odetail->odetail_product_amount	= Check::int('amount');
			$odetail->odetail_comment			= Check::str('comment', 255, 1, '');
			$odetail->odetail_foto_requested	= Check::chkbox('foto_requested');
			$odetail->odetail_search_requested	= Check::chkbox('search_requested');
			$odetail->odetail_insurance			= Check::chkbox('insurance');

			// проверяем, загружается картинка или ссылка
			$img_selector = Check::str('img_selector', 4, 4, '');
			$is_file_uploaded = ($img_selector == 'file') ? TRUE : FALSE;

			if ($is_file_uploaded)
			{
				$userfile = isset($_FILES['userfile']) AND  ! $_FILES['userfile']['error'];
				$odetail->odetail_img = NULL;
			}
			else
			{
				$userfile = FALSE;
				$odetail->odetail_img = Check::str('img', 4096, 1, 0);
			}

			// ссылка на товар, обязательна только в онлайн заказах
			if (empty($odetail->odetail_link))
			{
				if ($order->order_type == 'online')
				{
					throw new Exception('Добавьте ссылку на товар.');
				}
				else
				{
					$odetail->odetail_link = '';
				}
			}

			if ($is_file_uploaded AND
				empty($userfile))
			{
				if (isset($_FILES['userfile']) AND
					$_FILES['userfile']['error'] == 1)
				{
					throw new Exception('Максимальный размер картинки 3MB.');
				}
				else
				{
					throw new Exception('Загрузите или добавьте ссылку на скриншот.');
				}
			}

			$client_id = $order->order_client;

			// открываем транзакцию
			$this->db->trans_begin();

			$this->Odetails->updateOdetail($odetail);

			// загружаем файл
			if (isset($userfile) AND $userfile)
			{
				$this->uploadOrderScreenshot($odetail, $client_id);
			}

			// закрываем транзакцию
			$this->db->trans_commit();

			// отправляем cообщение на страницу
			$response['message'] = "Описание товара №{$odetail->odetail_id} сохранено.";
		}
		catch (Exception $e)
		{
			$response['is_error'] = TRUE;
			$response['message'] = $e->getMessage();
		}

		print(json_encode($response));
	}

	protected function updatePayment($o2i_id)
	{
		try
		{
			if ( ! is_numeric($o2i_id))
			{
				throw new Exception('Доступ запрещен.');
			}

			// роли и разграничение доступа
			$payment = $this->getPrivilegedOrder2In(
				$o2i_id,
				'Невозможно сохранить статус заявки. Заявка недоступна.');

			// валидация пользовательского ввода
			$payment->order2in_status = Check::str('payment_status', 20, 1, '');

			// валидируем статус
			$statuses = $this->Orders2In->getStatuses();

			if (empty($statuses[$payment->order2in_status]))
			{
				throw new Exception('Некорректный статус.');
			}

			// проверяем переводились ли уже деньги
			$is_money_sent = $payment->is_money_sent;

			// сохранение результатов
			$this->Orders2In->updateStatus($o2i_id, $payment->order2in_status);

			// оплата заказа
			// ВНИМАНИЕ!! деньги переводятся только 1 раз, а статус заявки меняется неограниченно
			if ( ! $is_money_sent AND $payment->order2in_status == 'payed')
			{
				$order = $this->getPrivilegedOrder(
					$payment->order_id,
					'Невозможно оплатить заказ. Заказ не найден.'
				);

				$is_repay = ($order->order_cost_payed > 0);
				$order->order_cost_payed += $payment->order2in_amount;

				// записываем платеж в историю
				$this->load->model('PaymentModel', 'History');
				$this->History->processOrderPayment($order, $payment, $is_repay);

				// пересчитываем заказ
				if ( ! $this->Orders->recalculate($order))
				{
					throw new Exception('Невозможно пересчитать стоимость заказа. Попоробуйте еще раз.');
				}

				$this->Orders->saveOrder($order);
			}
		}
		catch (Exception $e)
		{
			print_r($e);
		}
	}

	protected function addPaymentFoto()
	{
		try
		{
			$userfile	= isset($_FILES['userfile']) && ! $_FILES['userfile']['error'];

			Check::reset_empties();
			$o2i_id		= Check::int('order_id');
			$empties	= Check::get_empties();

			if ($empties)
			{
				throw new Exception('Заявка не найдена.');
			}

			if ( ! $this->getPrivilegedOrder2In($o2i_id))
			{
				throw new Exception('Заявка не найдена.');
			}

			if ( ! $userfile)
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

		Func::redirect($_SERVER['HTTP_REFERER']);
	}

	protected function deletePaymentFoto($o2i_id, $filename)
	{
		try
		{
			if (empty($o2i_id) OR
				empty($filename))
			{
				throw new Exception('Заявка не найдена.');
			}

			$path = "{$_SERVER['DOCUMENT_ROOT']}/upload/orders2in/$o2i_id/$filename";
			$this->load->model('Order2InModel', 'Order2in');

			// роли и разграничение доступа
			if ($this->getPrivilegedOrder2In(
					$o2i_id,
					'Заявка не найдена. Попробуйте еще раз.') AND
				is_file($path))
			{
				unlink($path);
			}
		}
		catch (Exception $e)
		{
		}

		Func::redirect($_SERVER['HTTP_REFERER']);
	}

	protected function prepareOrderUpdateJSON($order)
	{
		$this->load->model('AddressModel', 'Addresses');

		$view['addresses'] = empty($this->user->user_id) ?
			FALSE :
			$this->Addresses->getAddressesByUserId($this->user->user_id);

		$view['statuses'] = $this->Orders->getAllStatuses();
		$view['order'] = $order;

		if (isset($this->user->user_group) AND
			$this->user->user_group != 'admin')
		{
			$view['editable_statuses'] = $this->getEditableOrderStatuses();
			$view['payable_statuses'] = $this->Orders->getPayableStatuses($this->user->user_group);
		}

		// пакуем предложения
		if (isset($order->bids) AND
			is_array($order->bids))
		{
			foreach($order->bids as $bid)
			{
				$bids[] = array(
					'id' => $bid->bid_id,
					'total' => $bid->total_cost,
					'tax' => $bid->manager_tax,
					'foto' => $bid->foto_tax,
					'delivery' => $bid->delivery_cost,
					'extra' => $bid->extra_tax);
			}
		}

		// находим клиента
		if (isset($this->user->user_group) AND
			$this->user->user_group == 'manager')
		{
			$this->load->model('ClientModel', 'Clients');
			$view['client'] = $this->Clients->getClientById($view['order']->order_client);
			$this->processStatistics($view['client'], array(), 'client_user', $view['client']->client_user, 'client');
		}

		// рисуем новую форму заказа
		$this->Orders->prepareOrderView($view);
		$order_details = (empty($this->user->user_group) OR $this->user->user_group == 'admin') ?
			'' :
			View::get("/{$this->user->user_group}/ajax/showOrderInfoAjax", $view);

		// собираем все вместе
		return array(
			'order_details' => $order_details,
			'products_cost' => $order->order_products_cost,
			'delivery_cost' => $order->order_delivery_cost,
			'weight' => $order->order_weight,
			'status' => $order->order_status,
			'bids' => (isset($bids) ? $bids : FALSE)
		);
	}

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

		if ( ! $detail->odetail_product_amount)
		{
			$detail->odetail_product_amount = 1;
		}
	}

	protected function offlineProductCheck($detail)
	{
		if (empty($detail->odetail_product_name))
		{
			throw new Exception('Добавьте наименование товара.');
		}

		if (empty($detail->odetail_price))
		{
			throw new Exception('Добавьте цену товара.');
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

	protected function mail_forwardingProductCheck($order, $detail)
	{
		if (empty($order->order_manager))
		{
			throw new Exception('Выберите посредника.');
		}

		if (empty($detail->odetail_product_name))
		{
			throw new Exception('Добавьте наименование товара.');
		}

		if (empty($detail->odetail_tracking))
		{
			throw new Exception('Добавьте Tracking номер.');
		}
	}

	protected function getEditableOrderStatuses()
	{
		if (isset($this->user->user_group))
		{
			return $this->Orders->getEditableStatuses($this->user->user_group);
		}
		else
		{
			return array('pending');
		}
	}

	protected function getPrivilegedOdetail($order_id, $odetail_id)
	{
		if (isset($this->user->user_id))
		{
			return $this->Odetails->getPrivilegedOdetail(
				$order_id,
				$odetail_id,
				$this->user->user_id,
				$this->user->user_group);
		}
		else
		{
			return $this->Odetails->getPrivilegedOdetail(
				$order_id,
				$odetail_id,
				UserModel::getTemporaryKey(),
				'client');
		}
	}

	protected function saveProfilePhoto()
	{
		try
		{
			if (isset($_FILES['userfile']) AND ! $_FILES['userfile']['error'])
			{
				$config['upload_path']			= $_SERVER['DOCUMENT_ROOT']."/upload/avatars";
				$config['allowed_types']		= 'gif|jpeg|jpg|png|GIF|JPEG|JPG|PNG';
				$config['max_size']				= '3072';
				$config['encrypt_name'] 		= TRUE;

				$max_width	= 1024;
				$max_height	= 768;
				$filename	= "{$config['upload_path']}/{$this->user->user_id}.jpg";

				$this->uploadImageGeneric($max_width, $max_height, $config, $filename);
			}
		}
		catch (Exception $e)
		{
		}

		echo "/main/avatar/{$this->user->user_id}";
	}

    protected function Country_Order_Prio()
    {
        // выводим на верх списка некоторые странны
        $result_country_priory = array();
		$result_country1 = array();
        $result_country = $this->Country->getList();
        $i = 0;
        foreach($result_country as $row)
        {
            if ($row->country_id == 3 || $row->country_id == 7 || $row->country_id == 5)
            {
                $result_country_priory[$row->country_id] = $row;
            }else{
                $result_country1[$i] = $row;
                $i++;
            }
        }
        list($result_country_priory[3],$result_country_priory[7],$result_country_priory[5]) = array($result_country_priory[3],$result_country_priory[5], $result_country_priory[7]);
        $result_country = array_merge($result_country_priory,$result_country1);
        return $result_country;
    }

    protected function processPaymentHistoryFilter()
    {
        $filter = $this->initPaymentHistoryFilter();

        // сброс фильтра
        if (isset($_POST['resetFilter']) AND $_POST['resetFilter'] == '1')
        {
            return $filter;
        }

        $filter->svalue		= Check::str('svalue', 255, 1, '');
        $filter->sfield		= Check::str('sfield', 20, 1, 'manager_id');
        $filter->status		= Check::str('status', 20, 1, '');
        $filter->from		= Check::str('from', 10, 10, '');
        $filter->to			= Check::str('to', 10, 10, '');

        if ($filter->sfield)
        {
            switch ($filter->sfield)
            {
                case 'client_id' :
                    if (is_numeric($filter->svalue))
                    {
                        $filter->condition['like'] = array('payment_from' => $filter->svalue);
                    }
                    break;
                case 'manager_id' :
                    if (is_numeric($filter->svalue))
                    {
                        $filter->condition['like'] = array('payment_to' => $filter->svalue);
                    }
                    break;
                case 'order_id' :
                    $filter->condition['payment_type'] = 'order';
                    $filter->condition['like'] = array('payment_comment' => $filter->svalue);
                    break;
                case 'client_login' :
                    $filter->condition['like'] = array('user_from.user_login' => $filter->svalue);
                    break;
                case 'manager_login' :
                    $filter->condition['like'] = array('user_to.user_login' => $filter->svalue);
                    break;
                case 'payment_id' :
                    $filter->condition['like'] = array('payment_id' => $filter->svalue);
                    break;
                case 'payment_amount' :
                    $filter->condition["payment_amount_from"] = $filter->svalue;
                    break;
            }
        }

        if ($filter->status)
        {
            $filter->condition["status"] = $filter->status;
        }

        return $filter;
    }
}
?>