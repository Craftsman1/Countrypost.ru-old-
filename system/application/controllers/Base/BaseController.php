<?php
if (!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}
/**
 * Базовый контроллер
 *
 */

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
	 * 
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
			$this->load->model('UserModel', 'User');
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
			'selfurl'	=> BASEURL.$this->cname.'/',
			// postback
			'result'	=> $this->result,
			// добавляем информацию для счётчиков в меню
			'user_count' => $this->User->getUserCount(),
		);
		
		//подгружаем доп данные для клиента
		if ($this->user && $this->user->user_group	== 'client'){
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
	}	
	
	private function updateDeclarationItem($declaration_id)
	{
		if (!is_numeric($declaration_id) ||
			!isset($_POST['declaration_amount'.$declaration_id]) ||
			!isset($_POST['declaration_cost'.$declaration_id])) return;

		// находим товар в декларации
		$declaration = $this->Declarations->getById($declaration_id);

		if (!$declaration)
		{
			throw new Exception('Невозможно сохранить декларацию. Некоторые товары не найдены.');
		}

		// удаление товара из декларации
		if ($_POST['declaration_item'.$declaration_id] == '')
		{
			$deleted = $this->Declarations->delete($declaration_id);
				
			if (!$deleted)
			{
				throw new Exception('Невозможно сохранить декларацию. Попоробуйте еще раз.');
			}
			
			return;
		}
			
		// валидация пользовательского ввода
		Check::reset_empties();
		$declaration->declaration_item 		= Check::txt('declaration_item'.$declaration_id, 8096, 1);
		$declaration->declaration_amount 	= Check::int('declaration_amount'.$declaration_id);
		$declaration->declaration_cost 		= Check::float('declaration_cost'.$declaration_id);
		$empties							= Check::get_empties();
		
		if ($empties)
		{
			throw new Exception('Некоторые поля декларации не заполнены. Попробуйте еще раз.');
		}
				
		// изменение деталей товара
		$new_declaration = $this->Declarations->saveDeclaration($declaration);

		if ($new_declaration === FALSE)
		{
			throw new Exception('Невозможно сохранить декларацию. Попоробуйте еще раз.');
		}
	}
	
	private function insertDeclarationItem($declaration_id)
	{
		// сохраняем только заполненные товары
		if (!is_numeric($declaration_id) ||
			!isset($_POST['new_item'.$declaration_id]) ||
			$_POST['new_item'.$declaration_id] == '') return;

		// валидация пользовательского ввода
		$declaration = new stdClass();
		$declaration->declaration_item 		= Check::txt('new_item'.$declaration_id, 8096, 1);
		$declaration->declaration_amount 	= Check::int('new_amount'.$declaration_id);
		$declaration->declaration_cost 		= Check::float('new_cost'.$declaration_id);
		$declaration->declaration_package	= $this->uri->segment(3);
		$empties							= Check::get_empties();

		if ($empties)
		{
			throw new Exception('Некоторые поля декларации не заполнены. Попробуйте еще раз.');
		}

		// сохранение деталей товара
		$declaration->declaration_id = '';
		$new_declaration = $this->Declarations->saveDeclaration($declaration);
				
		if (!$new_declaration)
		{
			throw new Exception('Невозможно сохранить декларацию. Попоробуйте еще раз.');
		}
	}

	protected function showPackages($packageStatus, $pageName, $showDeliveryList=FALSE)
	{
		try
		{
			$this->load->model('PackageModel', 'Packages');

			// роли и разграничение доступа
			if ($this->user->user_group == 'admin')
			{
				// обработка фильтра
				$view['filter'] = $this->initFilter($packageStatus.'Packages');
				$view['filter']->package_statuses = $this->Packages->getFilterStatuses();
				
				$this->load->model('ManagerModel', 'Managers');
				$view['managers'] = $this->Managers->getManagersData();
				
				// отображаем посылки
				$view['packages'] = $this->Packages->getPackages($view['filter'], $packageStatus, NULL, NULL);
			}
			else if ($this->user->user_group == 'manager')
			{
				// обработка фильтра
				$view['filter'] = $this->initFilter($packageStatus.'Packages');
				$view['filter']->package_statuses = $this->Packages->getFilterStatuses();
				
				$this->load->model('ManagerModel', 'Managers');
				$view['manager'] = $this->Managers->getById($this->user->user_id);

				// отображаем посылки
				$view['packages'] = $this->Packages->getPackages($view['filter'], $packageStatus, NULL, $this->user->user_id);
			}
			else if ($this->user->user_group == 'client')
			{
				// отображаем посылки
				$view['packages'] = $this->Packages->getPackages(NULL, $packageStatus, $this->user->user_id, NULL);
				
				// отображение способов доставки
				if ($showDeliveryList)
				{
					$this->load->model('DeliveryModel', 'Deliveries');
					$view['deliveries'] = $this->Deliveries->getList();
					
					if (!$view['deliveries'])
					{
						$this->result->m = 'Невозможно отобразить посылки. Способы доставки не доступны.';
						Stack::push('result', $this->result);
					}
					
					$view['packages'] = $this->Packages->getAvailableDeliveries($view['packages'], $this->Deliveries);
				}

				// показываем статистику
				$this->putStatistics($view);
				
				//
				$this->load->model('ClientModel', 'Clients');
				$this->load->model('ManagerModel', 'Managers');
				$view['managers'] = $this->Managers->getManagersData();
				
				if ( ! $view['managers'])
				{
					throw new Exception('Партнеры не найдены. Попробуйте еще раз.');
				}

				@$view['client'] = $this->__client;
				@$view['partners'] = $this->__partners;

			}
			
			if ( ! $view['packages'])
			{
				$this->result->m = 'Посылки не найдены.';
			}
			
			// статусы
			$view['package_statuses'] = $this->Packages->getStatuses();

			/* пейджинг */
			$this->init_paging();		
			$this->paging_count = count($view['packages']);
			$per_page = isset($this->session->userdata['new_packages_per_page']) ? $this->session->userdata['new_packages_per_page'] : NULL;
			$per_page = (isset($per_page) && ($packageStatus == 'not_payed' || $packageStatus == 'open')) ? $per_page : $this->per_page;
			
			if ($view['packages'])
			{
				$view['packages'] = array_slice($view['packages'], $this->paging_offset, $per_page);
			}
			
			$view['pager'] = $this->get_paging($per_page);
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}

		// парсим шаблон
		$view['per_page'] = $per_page;
		
		if ($this->uri->segment(4) == 'ajax')
		{
        	$view['selfurl'] = BASEURL.$this->cname.'/';
			$view['viewpath'] = $this->viewpath;
			$this->load->view($this->viewpath."ajax/$pageName", $view);
		}
		else
		{
			View::showChild($this->viewpath."pages/$pageName", $view);
		}
	}
	
	protected function showOrders($orderStatus, $pageName)
	{
		try
		{
			if (!$this->user ||
				!$this->user->user_id)
			{
				throw new Exception('Доступ запрещен.');
			}
			
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
				$view['filter'] = $this->initFilter($orderStatus.'Orders');
				$view['filter']->order_statuses = $this->Orders->getFilterStatuses();
				
				// доступ к выбору заказов
				if ($orderStatus == 'not_payed')
				{
					$this->load->model('ManagerModel', 'Managers');
					$manager = $this->Managers->getById($this->user->user_id);
					$view['acceptOrderAllowed'] = $this->Managers->isOrdersAllowed($manager);

					// отображаем заказы
					$view['orders'] = $this->Orders->getOrders(
						$view['filter'], 
						$orderStatus, 
						NULL, 
						$this->user->user_id);
						
					$view['unassigned_orders'] = $this->Orders->getUnassignedOrders(
						$view['filter'], 
						$orderStatus, 
						NULL, 
						$this->user->user_id, 
						$manager->manager_country);
				}
				else
				{
					// отображаем заказы
					$view['orders'] = $this->Orders->getOrders($view['filter'], $orderStatus, NULL, $this->user->user_id);
				}
			}
			else if ($this->user->user_group == 'client')
			{
				// отображаем заказы
				$this->load->model('CountryModel', 'CountryModel');
				$this->load->model('OdetailModel', 'OdetailModel');
				
				$Orders		= $this->Orders->getOrders(NULL, $orderStatus, $this->user->user_id, NULL, ($orderStatus == 'open'));
				$Odetails	= $this->OdetailModel->getFilteredDetails(array('odetail_client' => $this->user->user_id, 'odetail_order' => 0));
				$Countries	= $this->CountryModel->getClientAvailableCountries($this->user->user_id);
				$statuses	= $this->Orders->getAvailableOrderStatuses();
						
				$view = array (
					'orders'	=> $Orders,
					'Odetails'	=> $Odetails,
					'statuses'	=> $statuses,
					'Countries'	=> $Countries,
				);
				
				// общие суммы активных товаров и заказов
				$this->load->model('ClientModel', 'Client');
				$view['hasActiveOrdersOrPackages'] = $this->Client->hasActiveOrdersOrPackages($this->user->user_id);
				
				// показываем статистику
				$this->putStatistics($view);
			}
			
			if ( ! $view['orders'])
			{
				$this->result->m = 'Заказы не найдены.';
				Stack::push('result', $this->result);
			}
			
			/* пейджинг */
			$this->init_paging();		
			$this->paging_count = count($view['orders']);
			
			if ($view['orders'])
			{
				$view['orders'] = array_slice($view['orders'],$this->paging_offset,$this->per_page);
			}
			
			$view['pager'] = $this->get_paging();
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
			$view['selfurl'] = BASEURL.$this->cname.'/';
			$view['viewpath'] = $this->viewpath;
			$this->load->view($this->viewpath."ajax/$pageName", $view);
		}
		else
		{
			View::showChild($this->viewpath."pages/$pageName", $view);
		}
	}
	
	protected function showOrderComments($flag = FALSE)
	{
		try
		{
			// безопасность
			if (!$this->user ||
				!$this->user->user_id ||
				!is_numeric($this->uri->segment(3)))
			{
				throw new Exception('Доступ запрещен.');
			}
		
			// роли и разграничение доступа
			$order = $this->getPrivilegedOrder(
				$this->uri->segment(3), 
				'Невозможно отобразить комментарии. Соответствующий заказ недоступен.');
			
			$this->load->model('ManagerModel', 'Managers');
			$view['Managers']	=	$this->Managers->getById($order->order_manager);
			
			$this->load->model('ClientModel', 'Clients');
			$view['Clients']	=	$this->Clients->getById($order->order_client);

			// показываем комментарии к заказу
			$this->load->model('OCommentModel', 'Comments');
			$view['comments'] = $this->Comments->getCommentsByOrderId($this->uri->segment(3));
			
			// сбрасываем флаг нового комментария
			if ($this->user->user_group == 'client' &&
				$order->comment_for_client)
			{
				$order->comment_for_client = 0;
				$view['order'] = $this->Orders->saveOrder($order);
			}
			else if ($this->user->user_group == 'manager' &&
				$order->comment_for_manager)
			{
				$order->comment_for_manager = 0;
				$view['order'] = $this->Orders->saveOrder($order);
			}
			else
			{
				$view['order'] = $order;
			}

			if (!$view['order'])
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
			Func::redirect(BASEURL.$this->cname.'/showNewOrders');
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
			$view['order'] = $this->getPrivilegedOrder(
				$this->uri->segment(3), 
				'Невозможно отобразить детали заказа. Попробуйте еще раз.');

			// детали заказа
			$this->load->model('OdetailModel', 'Odetails');
			$view['odetails_statuses'] = $this->Odetails->getAvailableOrderDetailsStatuses();		    
			$view['odetails'] = $this->Odetails->getOrderDetails($view['order']->order_id);
			
			$this->load->model('CountryModel', 'Countries');
			
			$this->Orders->prepareOrderView($view, 
				$this->Countries, 
				$this->Odetails);	
			
			// страны
			$view['Countries'] = $this->Countries->getClientAvailableCountries($view['order']->order_client);
			
			$view['order']->order_status_desc = $this->Orders->getOrderStatusDescription($view['order']->order_status);
			$view['order_statuses'] = $this->Orders->getAvailableOrderStatuses();
			
			// предложения
			$this->load->model('BidModel', 'Bids');
			$this->load->model('BidCommentModel', 'Comments');
			$this->load->model('ManagerModel', 'Managers');
			$this->load->model('ClientModel', 'Clients');
			
			$view['bids'] = $this->Bids->getBids($view['order']->order_id);
			$chosen_bid = FALSE;
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
					
					if ($bid->manager_id == $view['order']->order_manager)
					{
						$chosen_bid = $bid;
					}
					
					// а комменты только партнеру и клиенту
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
				}
				
				$view['order']->bid = $chosen_bid;
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
				($this->user->user_group == 'manager' OR 
				$this->user->user_group == 'client'))
			{
				// кнопка добавить предложение
				if ($this->user->user_group == 'manager')
				{
					if (empty($view['order']->order_manager))
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
				}
			}
			
			if (empty($this->user->user_group))
			{
				$view['bids_accepted'] = TRUE;
			}
			
			// для формы нового предложения
			if ($view['bids_accepted'] AND
				(empty($this->user->user_group) OR
				$this->user->user_group == 'manager'))
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
			Breadcrumb::setCrumb(array('http::://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] => 'Заказ №' . $view['order']->order_id), 1, TRUE);
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}

		// показываем детали заказа
		if (empty($this->user))
		{
			View::showChild('/main/pages/showOrderDetails', $view);
		}
		else
		{
			View::showChild($this->viewpath.'/pages/showOrderDetails', $view);
		}
	}

	protected function showPackageDetails()
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
									
			$this->load->model('PackageModel', 'Packages');
			$this->load->model('ManagerModel', 'Managers');
			$this->load->model('CountryModel', 'Countries');
			
			// роли и разграничение доступа
			if ($this->user->user_group == 'admin')
			{
			    $view['package'] = $this->Packages->getById($this->uri->segment(3));
			}
			else if ($this->user->user_group == 'manager')
			{
				$view['package'] = $this->Packages->getManagerPackageById($this->uri->segment(3), $this->user->user_id);
			}
			else if ($this->user->user_group == 'client')
			{
				$view['package'] = $this->Packages->getClientPackageById($this->uri->segment(3), $this->user->user_id);
			}
			
			$view['manager'] = $this->Managers->getManagerData($view['package']->package_manager);
			
			if ( ! $view['package'])
			{
				throw new Exception('Невозможно отобразить детали посылки. Попробуйте еще раз.');
			}

			$view['package_country'] = $this->Countries->getById($view['manager']->manager_country);
			$view['back_handler'] = self::getPackageBackHandler($view['package'], $this->user->user_group);

			// показываем детали посылки
			$this->load->model('PdetailModel', 'Pdetails');
			$this->load->model('PdetailJointModel', 'PdetailJoints');
			
			$view['pdetails_statuses'] = $this->Pdetails->getStatuses();
			$view['package_statuses'] = $this->Packages->getStatuses();
						
			$view['pdetails'] = $this->Pdetails->getPackageDetails($view['package']->package_id);
			
			if ( ! $view['pdetails'])
			{
				$this->result->m = 'Товары посылки не найдены.';
				Stack::push('result', $this->result);
			}

			if (is_array($view['pdetails']))
			{
				$view['packFotos'] = $this->Pdetails->getPackagesFoto($view['pdetails']);
				$view['jointFotos'] = $this->PdetailJoints->getJointsFoto($view['pdetails']);
			}
			
			//комментарии
			$this->load->model('PCommentModel', 'Comments');
			$view['comments'] = $this->Comments->getCommentsByPackageId($view['package']->package_id);
		
			// сбрасываем флаг нового комментария
			$package = $view['package'];
			
			if ($this->user->user_group == 'client' &&
				$package->comment_for_client)
			{
				$package->comment_for_client = 0;
				$this->Packages->savePackage($package);
			}
			else if ($this->user->user_group == 'manager' &&
				$package->comment_for_manager)
			{
				$package->comment_for_manager = 0;
				$this->Packages->savePackage($package);
			}
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}

		// показываем детали посылки
		View::showChild($this->viewpath.'/pages/showPackageDetails', $view);
	}
	
	protected function addProductManualAjax() 
	{
		$this->load->model('OrderModel', 'Orders');
		$this->load->model('OdetailModel', 'OdetailModel');
		
		Check::reset_empties();
		$detail = new OdetailModel();
		$detail->odetail_order = Check::int('order_id');
		
		if ( ! $detail->odetail_order &&
			$this->user->user_group != 'client')
		{
			throw new Exception('Заказ не найден.');
		}
		
		// находим заказ и клиента
		if (empty($this->user))
		{
			$client_id = 0;
		}
		else if ($this->user->user_group == 'client')
		{
			$client_id = $this->user->user_id;
		}
		
		Check::reset_empties();
		$detail->odetail_link					= Check::str('olink', 500, 1);
		$detail->odetail_product_name			= Check::str('oname', 255, 1);
		$detail->odetail_price					= Check::float('oprice');
		$detail->odetail_pricedelivery			= Check::float('odeliveryprice');
		$detail->odetail_weight					= Check::float('oweight');	
		$empties								= Check::get_empties();	
		
		$detail->odetail_img					= Check::str('userfileimg', 500, 1);
		$userfile								= isset($_FILES['userfile']) && !$_FILES['userfile']['error'];
		$detail->odetail_product_amount			= Check::int('oamount');
		$detail->odetail_product_color			= Check::str('ocolor', 32, 0);
		$detail->odetail_product_size			= Check::str('osize', 32, 0);
		$detail->odetail_client					= $client_id;
		$detail->odetail_manager				= 0;
		$detail->odetail_country				= Check::str('ocountry', 255, 1);
		$detail->odetail_foto_requested			= Check::chkbox('foto_requested');
		
		try 
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
				
			if (empty($detail->odetail_pricedelivery))
			{
				throw new Exception('Добавьте местную доставку товара.');
			}
				
			if (empty($detail->odetail_weight))
			{
				throw new Exception('Добавьте примерный вес товара.');
			}
				
			if (empty($detail->odetail_country))
			{
				throw new Exception('Выберите страну.');
			}
				
			if ( ! $detail->odetail_product_amount)
			{
				$detail->odetail_product_amount = 1;
			}
			
			if ($empties &&
				!$detail->odetail_img && 
				!$userfile)
			{
				if (isset($_FILES['userfile']) && 
					isset($_FILES['userfile']['error']) &&
					$_FILES['ofile']['error'] == 1)
				{
					throw new Exception('Максимальный размер картинки 3MB.');
				}
				else
				{
					throw new Exception('Загрузите или добавьте ссылку на скриншот.');
				}
			}
			
			if ($userfile)
			{
				unset($detail->odetail_img);
			}
				
			$Odetails = $this->OdetailModel->getFilteredDetails(array('odetail_client' => $client_id, 'odetail_order' => 0));
				
			// открываем транзакцию
			$this->db->trans_begin();	

			$detail = $this->OdetailModel->addOdetail($detail);

			/*
			// если заказ уже создан, вычисляем его статус
			if ($detail->odetail_order)
			{
				// находим заказ
				if ( ! isset($order))
				{
					$order = $this->Orders->getClientOrderById($detail->odetail_order, $client_id);
				}
				
				if ( ! isset($order) || ! $order)
				{
					throw new Exception('Невозможно изменить статусы товаров. Заказ не найден.');
				}
		
				// вычисляем общий статус товаров
				$status = $this->OdetailModel->getTotalStatus($detail->odetail_order);
				
				if (!$status)
				{
					throw new Exception('Статус заказа не определен. Попоробуйте еще раз.');
				}

				$recent_status = $order->order_status;
				$order->order_status = $this->Orders->calculateOrderStatus($status);
				$is_new_status = ($recent_status != $order->order_status && $recent_status != 'payed');
				
				if ($is_new_status)
				{
					$status_caption = $this->Orders->getOrderStatusDescription($order->order_status);
					
					// меняем статус заказа
					$new_order = $this->Orders->saveOrder($order);
					
					if (!$new_order)
					{
						throw new Exception('Невожможно изменить статус заказа. Попоробуйте еще раз.');
					}
				}
			}
			*/
 			// загружаем файл
			if ($userfile)
			{
				$old = umask(0);
				// загрузка файла
				if (!is_dir($_SERVER['DOCUMENT_ROOT']."/upload/orders/$client_id")){
					mkdir($_SERVER['DOCUMENT_ROOT']."/upload/orders/$client_id",0777);
				}

				$config['upload_path']			= $_SERVER['DOCUMENT_ROOT']."/upload/orders/$client_id";
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
				if (!rename($uploadedImg['full_path'],$_SERVER['DOCUMENT_ROOT']."/upload/orders/$client_id/{$detail->odetail_id}.jpg")){
					throw new Exception("Bad file name!");
				}
				
				$uploadedImg	= $_SERVER['DOCUMENT_ROOT']."/upload/orders/$client_id/{$detail->odetail_id}.jpg";
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
			
			// уведомления
			if (isset($is_new_status) && $is_new_status)
			{
				$this->load->model('ManagerModel', 'Managers');
				$this->load->model('UserModel', 'Users');
				$this->load->model('ClientModel', 'Clients');

				Mailer::sendAdminNotification(
					Mailer::SUBJECT_NEW_ORDER_STATUS, 
					Mailer::NEW_ORDER_STATUS_NOTIFICATION,
					0,
					$order->order_id, 
					0,
					"http://countrypost.ru/admin/showOrderDetails/{$order->order_id}",
					NULL,
					NULL,
					$status_caption);

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

				Mailer::sendClientNotification(
					Mailer::SUBJECT_NEW_ORDER_STATUS, 
					Mailer::NEW_ORDER_STATUS_NOTIFICATION,
					$order->order_id, 
					$order->order_client, 
					"http://countrypost.ru/client/showOrderDetails/{$order->order_id}",
					$this->Clients,
					$status_caption);
			}
			
			// возвращаем номер товара
			print($detail->odetail_id);
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}
	
	public function moveProducts() 
	{
		try 
		{
			$this->load->model('PackageModel', 'Packages');
			$this->load->model('PdetailModel', 'Pdetails');
			
			Check::reset_empties();
			$package_to_id	= Check::int('package_to');
			$package_from_id	= Check::int('package_id');
			
			if (empty($package_to_id) OR
				empty($package_from_id))
			{
				throw new Exception('Посылка назначения не указана. Попробуйте еще раз.');
			}
			
			// разграничение доступа
			if ($this->user->user_group == 'admin')
			{
				$package_from = $this->Packages->getById($package_from_id);
				$package_to = $this->Packages->getById($package_to_id);
			}
			else if ($this->user->user_group == 'manager')
			{
				$package_from = $this->Packages->getManagerPackageById($package_from_id, $this->user->user_id);
				$package_to = $this->Packages->getManagerPackageById($package_to_id, $this->user->user_id);
			}
			
			if (empty($package_from) OR 
				empty($package_to))
			{
				throw new Exception('Доступ запрещен.');
			}
			
			$movingPdetails = array();
			$movingFotos = array();
			$movingScreenshots = array();

			// погнали
			foreach ($_POST as $key => $value) 
			{
				if (strpos($key, 'pdetail_id') === 0 AND
					$key != 'pdetail_id')
				{
					$pdetail_id = str_ireplace('pdetail_id', '', $key);
					$pdetails = $this->Pdetails->getFilteredDetails(
						array(
							'pdetail_id' => $pdetail_id,
							'pdetail_package' => $package_from->package_id),
						TRUE);
					
					if (empty($pdetails))
					{
						throw new Exception('Некоторые товары не найдены. Попробуйте еще раз.');
					}
					
					$pdetail = $pdetails[0];
					
					// собираем фото
					$movingFotos += $this->Pdetails->getPackagesFoto($pdetails);
					
					// собираем скриншоты
					if (empty($pdetail->pdetail_img))
					{
						$movingScreenshots[] = $pdetail->pdetail_id;
					}
					
					// и переносим посылку
					$pdetail->pdetail_package = $package_to->package_id;
					$pdetail->pdetail_client = $package_to->package_client;
					$pdetail->pdetail_manager = $package_to->package_manager;
					
					$this->Pdetails->updatepdetail($pdetail);
				}
			}
			
			// копируем фото посылок
			if (count($movingFotos))
			{
				if ( ! is_dir(UPLOAD_DIR."packages/{$package_to->package_id}"))
				{
					mkdir(UPLOAD_DIR."packages/{$package_to->package_id}", 0777, TRUE);
				}
				
				foreach ($movingFotos as $pdetail_id => $files)
				{
					if ( ! is_dir(UPLOAD_DIR."packages/{$package_to->package_id}/$pdetail_id"))
					{
						mkdir(UPLOAD_DIR."packages/{$package_to->package_id}/$pdetail_id", 0777, TRUE);
					}
					
					// копируем все фото товара
					foreach ($files as $file)
					{
						if (file_exists(UPLOAD_DIR."packages/{$package_from->package_id}/$pdetail_id/$file"))
						{
							copy(
								UPLOAD_DIR."packages/{$package_from->package_id}/$pdetail_id/$file", 
								UPLOAD_DIR."packages/{$package_to->package_id}/$pdetail_id/$file");
						}
					}
				}
			}
			
			// копируем скриншоты товаров
			foreach ($movingScreenshots as $pdetail_id)
			{
				if (file_exists(UPLOAD_DIR."packages/{$package_from->package_id}/$pdetail_id.jpg"))
				{
					if ( ! is_dir(UPLOAD_DIR."packages/{$package_to->package_id}"))
					{
						mkdir(UPLOAD_DIR."packages/{$package_to->package_id}", 0777, TRUE);
					}
					
					copy(
						UPLOAD_DIR."packages/{$package_from->package_id}/$pdetail_id.jpg", 
						UPLOAD_DIR."packages/{$package_to->package_id}/$pdetail_id.jpg");
				}
			}
			
			// пересчитываем посылки
			$this->Packages->recalculatePackage($package_from);
			$this->Packages->recalculatePackage($package_to);
		}
		catch (Exception $e)
		{
			// todo: add exception message output
			//print_r($e);
		}		
	}
	
	protected function addProductManualAjaxP() 
	{		
		$this->load->model('PackageModel', 'Packages');

		Check::reset_empties();
		$detail = new stdClass();
		$detail->pdetail_package = Check::int('package_id');
		
		if (empty($detail->pdetail_package))
		{
			throw new Exception('Посылка не найдена.');
		}
		
		// находим посылку и ее клиента
		if ($this->user->user_group == 'client')
		{
			$package = $this->Packages->getClientPackageById($detail->pdetail_package, $this->user->user_id);
		}
		else if ($this->user->user_group == 'manager')
		{
			$package = $this->Packages->getManagerPackageById($detail->pdetail_package, $this->user->user_id);
		}
		else if ($this->user->user_group == 'admin')
		{
			$package = $this->Packages->getById($detail->pdetail_package);
		}
		
		if (empty($package))
		{
			throw new Exception('Посылка не найдена.');
		}
		
		if ($this->user->user_group == 'client' AND 
			$package->package_client != $this->user->user_id)
		{
			throw new Exception('Посылка не найдена.');
		}
	
		$client_id = $package->package_client;

		if (empty($client_id))
		{
			throw new Exception('Клиент посылки не найден.');
		}

		// валидация
		Check::reset_empties();
		$detail->pdetail_link					= Check::str('olink', 500, 1);
		$detail->pdetail_img					= Check::str('userfileimg', 500, 1);
		$userfile								= isset($_FILES['userfile']) && empty($_FILES['userfile']['error']);
		$detail->pdetail_shop_name				= Check::str('shop', 255, 0);
		$detail->pdetail_product_name			= Check::str('oname', 255, 0);
		$detail->pdetail_product_amount			= (isset($_POST['oamount']) &&
														$_POST['oamount'] &&
														is_numeric($_POST['oamount'])) ? 
														$_POST['oamount'] :
														1;
		$detail->pdetail_product_color			= Check::str('ocolor', 32, 0);
		$detail->pdetail_product_size			= Check::str('osize', 32, 0);
		$detail->pdetail_client					= $client_id;
		$detail->pdetail_manager				= $package->package_manager;
		$detail->pdetail_status					= $package->order_id ? 'delivered' : 'processing';
		
		$empties								= Check::get_empties();		
		
		try 
		{
			// обязательны для заполнения:
			// olink
			// ocountry
			// userfileimg либо клиентская картинка
			if ( ! $detail->pdetail_link)
			{
				throw new Exception('Добавьте ссылку на товар.');
			}
			
			if ($empties &&
				!$detail->pdetail_img && 
				!$userfile)
			{
				if (isset($_FILES['userfile']) && 
					isset($_FILES['userfile']['error']) &&
					$_FILES['userfile']['error'] == 1)
				{
					throw new Exception('Максимальный размер картинки 3MB.');
				}
				else
				{
					throw new Exception('Загрузите или добавьте ссылку на скриншот.');
				}
			}
			
			if ($userfile)
			{
				unset($detail->pdetail_img);
			}
				
			$this->load->model('PdetailModel', 'PdetailModel');
			
			// открываем транзакцию
			$this->db->trans_begin();	

			$detail = $this->PdetailModel->addPdetail($detail);

			// пересчитываем статус и стоимость посылки
			$recent_status = $package->package_status;
			$new_package = $this->Packages->recalculatePackage($package);

			$status = $new_package->package_status;
			$is_new_status = ($recent_status != $new_package->package_status);
		
 			// загружаем файл
			if ($userfile)
			{
				$old = umask(0);
				// загрузка файла
				if (!is_dir($_SERVER['DOCUMENT_ROOT']."/upload/packages/{$detail->pdetail_package}"))
				{
					mkdir($_SERVER['DOCUMENT_ROOT']."/upload/packages/{$detail->pdetail_package}",0777);
				}
				
				$config['upload_path']			= $_SERVER['DOCUMENT_ROOT']."/upload/packages/{$detail->pdetail_package}";
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
				if ( ! rename(
						$uploadedImg['full_path'],
						$_SERVER['DOCUMENT_ROOT']."/upload/packages/{$detail->pdetail_package}/{$detail->pdetail_id}.jpg"))
				{
					throw new Exception("Bad file name!");
				}
				
				$uploadedImg	= $_SERVER['DOCUMENT_ROOT']."/upload/packages/{$detail->pdetail_package}/{$detail->pdetail_id}.jpg";
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
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}
	
	protected function showDeclaration()
	{
		try
		{
			// безопасность
			if (!$this->user ||
				!$this->user->user_id ||
				!is_numeric($this->uri->segment(3)))
			{
				throw new Exception('Доступ запрещен.');
			}
		
			$this->load->model('PackageModel', 'Packages');

			// роли и разграничение доступа
			if ($this->user->user_group == 'admin')
			{
			    $package = $this->Packages->getById($this->uri->segment(3));
			}
			else if ($this->user->user_group == 'manager')
			{
				$package = $this->Packages->getManagerPackageById($this->uri->segment(3), $this->user->user_id);
			}
			else if ($this->user->user_group == 'client')
			{
				$package = $this->Packages->getClientPackageById($this->uri->segment(3), $this->user->user_id);
			}			
		
			if (!$package)
			{
				throw new Exception('Невозможно отобразить декларацию. Соответствующая ей посылка недоступна.');
			}

			// показываем декларацию к посылке
			$view['package'] = $package;
			$this->load->model('DeclarationModel', 'Declarations');
			$view['declarations'] = $this->Declarations->getDeclarationsByPackageId($this->uri->segment(3));
			$view['back_handler'] = self::getPackageBackHandler($view['package'], $this->user->user_group);
			
			// подгружаем стоимости комиссий
			$this->load->model('ConfigModel', 'Config');
			$view['config'] = $this->Config->getConfig();
			
			// и курс
			$this->load->model('CurrencyModel', 'Currency');
			$view['usd'] = $this->Currency->getRate('USD');
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
			
			// открываем новые посылки
			if ($this->user->user_group == 'client')
			{
				Func::redirect(BASEURL.$this->cname.'/showOpenPackages');
			}
			else
			{
				Func::redirect(BASEURL.$this->cname.'/showNewPackages');
			}
			
			return;
		}

		View::showChild($this->viewpath.'/pages/showPackageDeclaration', $view);
	}
	
	protected function showO2oComments()
	{
		try
		{
			// безопасность
			if (!$this->user ||
				!$this->user->user_id ||
				!is_numeric($this->uri->segment(3)))
			{
				throw new Exception('Доступ запрещен.');
			}
		
			$this->load->model('Order2outModel', 'O2o');
			
			// роли и разграничение доступа
			if ($this->user->user_group == 'admin')
			{
			    $o2o = $this->O2o->getById($this->uri->segment(3));
			}
			else// if ($this->user->user_group == 'client')
			{
				$o2o = $this->O2o->getClientsO2oById($this->uri->segment(3), $this->user->user_id);
			}
			
			if (!$o2o)
			{
				throw new Exception('Невозможно отобразить комментарии. Соответствующая заявка недоступна.');
			}

			// определяем роль создателя заявки на вывод
			$this->load->model('ClientModel', 'Clients');
			
			if ($this->Clients->getById($o2o->order2out_user))
			{
				$o2o->is_client_o2o = TRUE;
			}
			else
			{
				$this->load->model('ManagerModel', 'Managers');
				
				if ($this->Managers->getById($o2o->order2out_user))
				{
					$o2o->is_manager_o2o = TRUE;
				}
			}

			// показываем комментарии к заявке
			$this->load->model('O2CommentModel', 'Comments');
			$view['comments'] = $this->Comments->getCommentsByO2oId($this->uri->segment(3));
			$view['o2o'] = $o2o;

			if (!$view['o2o'])
			{
				throw new Exception('Ошибка отображения комментариев. Попробуйте еще раз.');
			}
			
			// сбрасываем флаг нового комментария
			if ($this->user->user_group == 'client' &&
				$o2o->comment_for_client)
			{
				$o2o->comment_for_client = 0;
				$this->O2o->addOrder($o2o);
			}
			else if ($this->user->user_group == 'admin' &&
				$o2o->comment_for_admin)
			{
				$o2o->comment_for_admin = 0;
				$this->O2o->addOrder($o2o);
			}
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);

			// открываем новые заказы
			//Func::redirect(BASEURL.$this->cname.'/showNewOrders');
			return;
		}

		// отображаем комментарии
		View::showChild($this->viewpath.'/pages/showO2oComments', $view);
	}
	
	protected function showO2iComments()
	{
		try
		{
			// безопасность
			if (!$this->user ||
				!$this->user->user_id ||
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
			
			if (!$o2i)
			{
				throw new Exception('Невозможно отобразить комментарии. Соответствующая заявка недоступна.');
			}

			// показываем комментарии к заявке
			$this->load->model('O2ICommentsModel', 'Comments');
			$view['comments'] = $this->Comments->getCommentsByO2iId($this->uri->segment(3));
			$view['o2i'] = $o2i;

			if (!$view['o2i'])
			{
				throw new Exception('Ошибка отображения комментариев. Попробуйте еще раз.');
			}
			
			// сбрасываем флаг нового комментария
			if ($this->user->user_group == 'client' &&
				$o2i->order2in_2clientcomment)
			{
				$o2i->order2in_2clientcomment = 0;
				$this->O2i->addOrder($o2i);
			}
			else if ($this->user->user_group == 'admin' &&
				$o2i->order2in_2admincomment)
			{
				$o2i->order2in_2admincomment = 0;
				$this->O2i->addOrder($o2i);
			}
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
			return;
		}

		// отображаем комментарии
		View::showChild($this->viewpath.'/pages/showO2iComments', $view);
	}
	
	protected function addOrderComment($order_id, $comment_id = NULL)
	{
		try
		{
			if (!is_numeric($order_id))
			{
				throw new Exception('Доступ запрещен.');
			}
		
			// роли и разграничение доступа
			$order = $this->getPrivilegedOrder(
				$order_id, 
				'Невозможно добавить комментарий. Соответствующий заказ недоступен.');

			// валидация пользовательского ввода
			$this->load->model('OCommentModel', 'Comments');
			
			if (is_numeric($comment_id)) 
			{
				$ocomment = $this->Comments->getById($comment_id);
				if (!$ocomment) 
				{
					throw new Exception('Невозможно изменить комментарий. Комментарий не найден.');
				}
				
				$ocomment->ocomment_comment	= Check::txt('comment_update', 8096, 1);
			}
			else
			{
				$ocomment				= new stdClass();
				$ocomment->ocomment_comment	= Check::txt('comment', 8096, 1);
				$ocomment->ocomment_user	= $this->user->user_id;
			}
			
			$ocomment->ocomment_order	= $this->uri->segment(3);
			$empties					= Check::get_empties();
		
			if ($empties) 
			{
				throw new Exception('Текст комментария отсутствует. Попробуйте еще раз.');
			}
			
			// сохранение результатов
			$this->db->trans_begin();
			$new_comment = $this->Comments->addComment($ocomment);
			
			if (!$new_comment &&
				!is_numeric($comment_id))
			{
				throw new Exception('Комментарий не добавлен. Попробуйте еще раз.');
			}			
			
			// выставляем флаг нового комментария
			if ($this->user->user_group == 'manager')
			{
				$order->comment_for_client = TRUE;
			}
			else if ($this->user->user_group == 'client')
			{
				$order->comment_for_manager = TRUE;
			}
			else if ($this->user->user_group == 'admin')
			{
				$order->comment_for_manager	= TRUE;
				$order->comment_for_client	= TRUE;
			}
			
			$this->Orders->saveOrder($order);
			$this->db->trans_commit();
			
			// уведомления
			$this->load->model('ManagerModel', 'Managers');
			$this->load->model('UserModel', 'Users');
			$this->load->model('ClientModel', 'Clients');
			
			if ($this->user->user_group != 'manager')
			{
				Mailer::sendManagerNotification(
					Mailer::SUBJECT_NEW_COMMENT, 
					Mailer::NEW_ORDER_COMMENT_NOTIFICATION, 
					$order->order_manager,
					$order->order_id, 
					0,
					"http://countrypost.ru/manager/showOrderDetails/{$order->order_id}#comments",
					$this->Managers,
					NULL);
			}
			
			if ($this->user->user_group != 'client')
			{
				Mailer::sendClientNotification(
					Mailer::SUBJECT_NEW_COMMENT, 
					Mailer::NEW_ORDER_COMMENT_NOTIFICATION, 
					$order->order_id, 
					$order->order_client,
					"http://countrypost.ru/client/showOrderDetails/{$order->order_id}#comments",
					$this->Clients,
					$this->Users);
			}
		}
		catch (Exception $e) 
		{
			$this->db->trans_rollback();
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}
		
		// открываем комментарии к посылке
		Func::redirect(BASEURL.$this->cname."/showOrderDetails/{$this->uri->segment(3)}#comments");
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
			
			if ( ! $comment &&
				! is_numeric($comment_id))
			{
				throw new Exception('Комментарий не добавлен. Попробуйте еще раз.');
			}

			$this->load->model('ClientModel', 'Clients');
			$this->load->model('ManagerModel', 'Managers');
			$this->processStatistics($comment, array(), 'user_id', $this->user->user_id, $this->user->user_group);
			
			$view['comment'] = $comment;
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

	protected function addO2oComment()
	{
		try
		{
			// безопасность
			if (!$this->user ||
				!$this->user->user_id ||
				!is_numeric($this->uri->segment(3)))
			{
				throw new Exception('Доступ запрещен.');
			}
		
			$this->load->model('Order2outModel', 'O2o');
			
			// роли и разграничение доступа
			if ($this->user->user_group == 'admin')
			{
				$o2o = $this->O2o->getById($this->uri->segment(3));
			}
			else// if ($this->user->user_group == 'client')
			{
				$o2o = $this->O2o->getClientsO2oById($this->uri->segment(3), $this->user->user_id);
			}

			if (!$o2o)
			{
				throw new Exception('Невозможно добавить комментарий. Соответствующая заявка недоступна.');
			}

			// валидация пользовательского ввода
			$o2comment						= new stdClass();
			$o2comment->o2comment_comment	= Check::txt('comment', 8096, 1);
			$o2comment->o2comment_order2out	= $this->uri->segment(3);
			$o2comment->o2comment_user		= $this->user->user_id;
			$empties						= Check::get_empties();

			if ($empties) 
			{
				throw new Exception('Текст комментария отсутствует. Попробуйте еще раз.');
			}
			
			// сохранение результатов
			$this->load->model('O2CommentModel', 'Comments');
			
			$this->db->trans_begin();
			$new_comment = $this->Comments->addComment($o2comment);
			
			if (!$new_comment)
			{
				throw new Exception('Комментарий не добавлен. Попробуйте еще раз.');
			}			
			
			// выставляем флаг нового комментария
			if ($this->user->user_group == 'admin')
			{
				$o2o->comment_for_client = TRUE;
			}
			else
			{
				$o2o->comment_for_admin = TRUE;
			}
			
			$o2o = $this->O2o->addOrder($o2o);

			$this->db->trans_commit();
		}
		catch (Exception $e) 
		{
			$this->db->trans_rollback();
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}
		
		// открываем комментарии к посылке
		Func::redirect(BASEURL.$this->cname.'/showO2oComments/'.$this->uri->segment(3));
	}

	protected function addO2iComment()
	{
		try
		{
			// безопасность
			if (!$this->user ||
				!$this->user->user_id ||
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

			if (!$o2i)
			{
				throw new Exception('Невозможно добавить комментарий. Соответствующая заявка недоступна.');
			}

			// валидация пользовательского ввода
			$o2comment						= new stdClass();
			$o2comment->o2icomment_text	= Check::txt('comment', 8096, 1);
			$o2comment->o2icomment_time	= date('Y-m-d H:i:s');
			$o2comment->o2icomment_order2in	= $this->uri->segment(3);
			$o2comment->o2icomment_user		= $this->user->user_id;
			$empties						= Check::get_empties();

			if ($empties) 
			{
				throw new Exception('Текст комментария отсутствует. Попробуйте еще раз.');
			}
			
			// сохранение результатов
			$this->load->model('O2ICommentsModel', 'Comments');
			
			$this->db->trans_begin();
			$new_comment = $this->Comments->addComment($o2comment);
			
			if (!$new_comment)
			{
				throw new Exception('Комментарий не добавлен. Попробуйте еще раз.');
			}			
			
			// выставляем флаг нового комментария
			if ($this->user->user_group == 'admin')
			{
				$o2i->order2in_2clientcomment = TRUE;
			}
			else
			{
				$o2i->order2in_2admincomment = TRUE;
			}
			
			$o2i = $this->O2i->addOrder($o2i);

			$this->db->trans_commit();
		}
		catch (Exception $e) 
		{
			$this->db->trans_rollback();
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}
		
		// открываем комментарии к посылке
		Func::redirect(BASEURL.$this->cname.'/showO2iComments/'.$this->uri->segment(3));
	}

	protected function filter($filterType, $pageName)
	{
		try
		{
			// валидация пользовательского ввода
			$filter	= $this->initFilter($filterType);
			
			if (isset($_POST['manager_user'])) $filter->manager_user						= Check::int('manager_user');
			if (isset($_POST['client_country'])) $filter->client_country					= Check::int('client_country');
			if (isset($_POST['search_id'])) $filter->search_id								= Check::txt('search_id', 11, 1, '');
			if (isset($_POST['search_client'])) $filter->search_client						= Check::txt('search_client', 11, 1, '');
			if (isset($_POST['pricelist_delivery'])) $filter->pricelist_delivery			= Check::int('pricelist_delivery');
			if (isset($_POST['period'])) $filter->period									= Check::txt('period', 5, 3, '');
			if (isset($_POST['id_type'])) $filter->id_type									= Check::txt('id_type', 13, 5, '');
			if (isset($_POST['id_status'])) $filter->id_status								= Check::txt('id_status', 20, 1, '');
			
			if (Check::int('our_pricelist')) 
			{
				$filter->our_pricelist = Check::int('our_pricelist');
				$filter->pricelist_country_from	= '';
				$filter->pricelist_country_to = '';
			}
			else
			{
				$filter->our_pricelist = '';
				if (isset($_POST['pricelist_country_from'])) $filter->pricelist_country_from	= Check::int('pricelist_country_from');
				if (isset($_POST['pricelist_country_to'])) $filter->pricelist_country_to		= Check::int('pricelist_country_to');
			}
			
			if (isset($_POST['clientO2oSearchType'])) 
			{
				unset($filter->order2out_id);
				unset($filter->user_login);
				unset($filter->order2out_user);
				
				$clientO2oSearchType			= Check::txt('clientO2oSearchType', 14, 10, '');
				$clientO2oSearchValue			= Check::txt('clientO2oSearchValue', 32, 1, '');
				
				if ($clientO2oSearchValue)
				{
					if ($clientO2oSearchType == 'order2out_id')
					{
						$filter->order2out_id	= $clientO2oSearchValue;
					}
					else if ($clientO2oSearchType == 'user_login')
					{
						$filter->user_login	= $clientO2oSearchValue;
					}
					else if ($clientO2oSearchType == 'order2out_user')
					{
						$filter->order2out_user	= $clientO2oSearchValue;
					}
				}

				$filter->order2out_status		= Check::txt('order2out_status', 32, 1, '');
			}
					
			if (!isset($filter->id_type) || $filter->id_type == '')
			{
				$filter->search_id = '';
				$filter->search_client = '';
			}
			
			if ($filterType == 'paymentHistory')
			{
				$filter = $this->initPaymentHistoryFilter($filter);
			}
			
			if ($filterType == 'UnassignedOrders')
			{
				$filter = $this->initUnassignedOrdersFilter($filter);
			}
			
			if ($filterType == 'Dealers')
			{
				$filter = $this->initDealersFilter($filter);
			}
		
			$_SESSION[$filterType.'Filter'] = $filter;
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}
		
		// открываем новые посылки
		Func::redirect(BASEURL.$this->cname.'/'.$pageName);
	}
	
	protected function initFilter($filterType)
	{//$_SESSION[$filterType.'Filter'] = null;die();
	
		if ( ! isset($_SESSION[$filterType.'Filter']))
		{
			$filter = new stdClass();
			$filter->manager_user			= '';
			$filter->client_country			= '';
			$filter->period					= '';
			$filter->search_id				= '';
			$filter->search_client			= '';
			$filter->id_type				= '';
			$filter->id_status				= '';
			$filter->our_pricelist			= '';
			$filter->pricelist_country_from	= '';
			$filter->pricelist_country_to	= '';
			$filter->pricelist_delivery		= '';
			
			if ($filterType == 'openClientO2o')
			{
				$filter->order2out_status = 'processing';
			
			}
			else if ($filterType == 'payedClientO2o')
			{
				$filter->order2out_status = 'payed';
			}
			else if ($filterType == 'openManagerO2o')
			{
				$filter->order2out_status = 'processing';
			}
			else if ($filterType == 'payedManagerO2o')
			{
				$filter->order2out_status = 'payed';
			}
			else if ($filterType == 'paymentHistory')
			{
				$filter = $this->initPaymentHistoryFilter($filter);
			}
			
			$_SESSION[$filterType.'Filter'] = $filter;
		}	

		return $_SESSION[$filterType.'Filter'];
	}
	
	private function initPaymentHistoryFilter(&$filter)
	{
		$filter->payment_from = '';
		$filter->user_from = '';
		$filter->user_to = '';
		$filter->payment_to = '';
		$filter->from = NULL;
		$filter->to = NULL;
		$filter->condition = array();
		$filter->sfield		= '';
		$filter->sdate		= '';
		$filter->stype		= '';
		$filter->svalue		= '';
		$filter->sservice	= '';

		// сброс фильтра
		if (isset($_POST['resetFilter']) && $_POST['resetFilter'] == '1')
		{
			return $filter;
		}
		
		if (isset($_POST['sdate']))
		{
			switch ($_POST['sdate']) {
				case 'day':
					$filter->from = date('Y-m-d 00:00:00');
					break;
				case 'week':
					$filter->from = intval(date('w')) ? date('Y-m-d 00:00:00', time()-(intval(date('w'))-1)*24*60*60) : date('Y-m-d 00:00:00', time()-6*24*60*60);
					break;
				case 'month':
					$filter->from = date('Y-m-01 00:00:00');
					break;
			}
		}
		
		if ($filter->from)
		{
			$filter->to = date('Y-m-d H:i:s');
		}
	
		$filter->sfield		= Check::str('sfield',64,1);
		$filter->sdate		= Check::str('sdate',5,3);
		$filter->stype		= Check::str('stype',7,2);
		$filter->svalue		= Check::str('svalue',64,1);
		$filter->sservice	= Check::str('sservice',7,2);
				
		if ($filter->sfield == 'id')
		{
			switch ($filter->stype)
			{
				case 'from' :
					if (is_numeric($filter->svalue))
					{
						$column = "user_from.user_id";
					}
					break;
				case 'to' :
					if (is_numeric($filter->svalue))
					{
						$column = "user_to.user_id";
					}
					break;
				case 'package' :
					$filter->condition['payment_type'] = 'package';
					$filter->condition['like'] = array('payment_comment' => $filter->svalue);
					break;
				case 'order' :
					$filter->condition['payment_type'] = 'order';
					$filter->condition['like'] = array('payment_comment' => $filter->svalue);
					break;
			}
		}
		
		if ($filter->sfield == 'login')
		{
			$column = "user_{$filter->stype}.user_login";
		}

		if (isset($column))
		{
			$filter->condition[$column] = $filter->svalue;
		}
		
		// тип услуги, за которую произведена оплата
		if (isset($filter->sservice) &&
			($filter->sservice == 'package' || 
			$filter->sservice == 'order' || 
			$filter->sservice == 'in' || 
			$filter->sservice == 'out' || 
			$filter->sservice == 'salary'))
		{
			$filter->condition['payment_type'] = $filter->sservice;
		}
		
		return $filter;
	}
	
	protected function addPackage($order_id = NULL)
	{
		try
		{
			// валидация пользовательского ввода
			Check::reset_empties();
			$package = new stdClass();
			
			if ($this->user->user_group == 'admin' || $this->user->user_group == 'client')
			{
			    $package->package_manager	= Check::int('package_manager');
	
				if (empty($package->package_manager)) 
				{
					throw new Exception('Выберите страну.');
				}			    
			}
			else if ($this->user->user_group == 'manager')
			{
				$package->package_manager	= $this->user->user_id;
			}
			
			$package->package_client			= ($this->user->user_group == 'client'?$this->user->user_id:Check::int('package_client'));
			$package->package_trackingno		= Check::str('package_trackingno', 25);
			$package->declaration_status		= 'not_completed';
			$package->package_status			= ($this->user->user_group == 'client' ? 'processing' : 'not_payed');
			$package->join_count				= 0;
			$package->package_comission 		= 0;
			$package->package_declaration_cost	= 0;
			$package->package_delivery_cost		= 0;
			$package->package_delivery_cost_local	= 0;
			$package->package_join_cost			= 0;
			$package->package_special_cost		= 0;
			$package->package_special_cost_usd	= 0;
			$package->order_id					= 0;
			$package->package_join_count		= 0;
			
			// страховка
			$package->package_insurance 		= PACKAGE_DEFAULT_INSURANCE;
			
			if ($package->package_insurance)
			{
				$package->package_insurance_cost 	= PACKAGE_DEFAULT_INSURANCE_AMOUNT;
			}
			
			Check::reset_empties();
			$package->package_weight			= Check::float('package_weight');
			$empties							= Check::get_empties();

			if (is_array($empties)) 
			{
				throw new Exception('Введите вес.');
			}
			
			$this->load->model('ManagerModel', 'Managers');
			$manager = $this->Managers->getById($package->package_manager);
			
			if (!$manager) 
			{
				throw new Exception('Невозможно добавить посылку. Партнер не найден.');
			}
			
			$this->load->model('ClientModel', 'Clients');
			$client = $this->Clients->getById($package->package_client);
			
			if (!$client) 
			{
				throw new Exception("Невозможно добавить посылку. Клиент {$package->package_client} не найден.");
			}
			
			$package->package_country_from		= $manager->manager_country;
			$package->package_country_to		= $client->client_country;

			$this->load->model('ConfigModel', 'Config');
			$this->load->model('PackageModel', 'Packages');
			
			// вычисляем адрес посылки
			$this->load->model('ClientModel', 'Clients');
			$client = $this->Clients->getById($package->package_client);
			if (!$client) 
			{
				throw new Exception('Клиент не найден. Попробуйте еще раз.');
			}
			
			$this->load->model('CountryModel', 'Countries');
			$country = $this->Countries->getById($client->client_country);
			if (!$country) 
			{
				throw new Exception('Страна назначения не найдена. Попробуйте еще раз.');
			}
			
			if (empty($client->client_address) || empty($client->client_town)) 
			{
				$package->package_address = "Адрес не заполнен!";
			}
			else 
			{
				$package->package_address = sprintf('%s %s / %s, %s, %s, %s<br />Тел. %s', 
				$client->client_surname,
				$client->client_name,
				$client->client_index,
				$client->client_address,
				$client->client_town,
				$country->country_name,
				$client->client_phone);
			}
		
			// привязываем заказ
			if ( ! empty($_POST['order_id']))
			{
				$package->order_id = $_POST['order_id'];
			}
			
			// сохранение результатов
			$this->load->model('PackageModel', 'Packages');
			
			$new_package = $this->Packages->savePackage($package);
			
			if (!$new_package)
			{
				throw new Exception('Посылка не добавлена. Попробуйте еще раз.');
			}
			
			// привязываем посылку
			if ( ! empty($_POST['order_id']))
			{
				$this->load->model('OrderModel', 'Orders');
				$order = $this->Orders->getById($_POST['order_id']);
				
				if (!$order ||
					$order->order_client != $package->package_client ||
					$order->order_manager != $package->package_manager)
				{
					throw new Exception('Посылка не добавлена. Соответствующий заказ не найден.');
				}
				
				$order->package_id = $new_package->package_id;
				$this->Orders->saveOrder($order);
			}

			$this->load->model('OdetailModel', 'Odetails');
			$this->load->model('PdetailModel', 'Pdetails');
			
			// копируем товары заказа в посылку
			if ($package->order_id)
			{
				$odetails = $this->Odetails->getOrderDetails($package->order_id);
				
				if ( ! $odetails)
				{
					throw new Exception('Посылка не добавлена. Товары заказа не найдены.');
				}
				
				foreach ($odetails as $odetail)
				{
					$pdetail = new stdClass();
					$pdetail->odetail_id = $odetail->odetail_id;
					$pdetail->pdetail_client = $odetail->odetail_client;
					$pdetail->pdetail_manager = $odetail->odetail_manager;
					$pdetail->pdetail_package = $new_package->package_id;
					$pdetail->pdetail_link = $odetail->odetail_link;
					$pdetail->pdetail_shop_name = $odetail->odetail_shop_name;
					$pdetail->pdetail_product_name = $odetail->odetail_product_name;
					$pdetail->pdetail_product_color = $odetail->odetail_product_color;
					$pdetail->pdetail_product_size = $odetail->odetail_product_size;
					$pdetail->pdetail_product_amount = $odetail->odetail_product_amount;
					$pdetail->pdetail_status = 'delivered';

					// копируем скриншот из заказа в посылку
					if (isset($odetail->odetail_img))
					{
						$pdetail->pdetail_img = $odetail->odetail_img;
						$this->Pdetails->addpdetail($pdetail);
					}
					else
					{
						$pdetail = $this->Pdetails->addpdetail($pdetail);
						
						$old = umask(0);
						$odetail_file = $_SERVER['DOCUMENT_ROOT']."/upload/orders/{$odetail->odetail_client}/{$odetail->odetail_id}.jpg";
						$package_folder = $_SERVER['DOCUMENT_ROOT']."/upload/packages/{$new_package->package_id}";
						$pdetail_file = "$package_folder/{$pdetail->pdetail_id}.jpg";
						
						// загрузка файла
						if (file_exists($odetail_file))
						{
							if ( ! is_dir($package_folder))
							{
								mkdir($package_folder, 0777);
							}

							if ( ! copy($odetail_file, $pdetail_file))
							{
								//throw new Exception("Bad file name!");
								// чтобы не ломать создание посылки изза потеряной картинки в товаре заказа, проглатываем ошибку копирования
							}
						}
					}
				}
			}
			
			// уведомления
			if ($this->user->user_group != 'admin')
			{
				Mailer::sendAdminNotification(
					Mailer::SUBJECT_NEW_PACKAGE, 
					Mailer::NEW_PACKAGE_NOTIFICATION, 
					0,
					$new_package->package_id, 
					0,
					"http://countrypost.ru/admin/showPackageDetails/{$new_package->package_id}",
					NULL,
					NULL);
			}
			
			if ($this->user->user_group != 'manager')
			{
				Mailer::sendManagerNotification(
					Mailer::SUBJECT_NEW_PACKAGE, 
					Mailer::NEW_PACKAGE_MANAGER_NOTIFICATION, 
					$package->package_manager,
					$new_package->package_id, 
					0,
					"http://countrypost.ru/manager/showPackageDetails/{$new_package->package_id}",
					$this->Managers,
					NULL);
			}

			Mailer::sendClientNotification(
				Mailer::SUBJECT_NEW_PACKAGE, 
				Mailer::NEW_PACKAGE_NOTIFICATION, 
				$new_package->package_id, 
				$package->package_client,
				"http://countrypost.ru/client/showPackageDetails/{$new_package->package_id}",
				$this->Clients);
		}
		catch (Exception $e) 
		{
			$this->result->e = -1;			
			$this->result->m = $e->getMessage();
			
			
			if ($this->user->user_group != 'client' OR
				$this->uri->segment(3) != 'ajax')	
			{
				Stack::push('result', $this->result);
				Func::redirect(BASEURL.$this->cname.'/showAddPackage');
			}
			else
			{
				echo $e->getMessage();
				return;
			}
		}

		// открываем новые посылки
		if ($this->user->user_group == 'client')
		{
			if ($this->uri->segment(3) != 'ajax')
			{
				Func::redirect(BASEURL.$this->cname."/showOpenPackages");
			}
			else
			{
				echo '';
			}
		} 
		else 
		{
			Func::redirect(BASEURL.$this->cname.'/showNewPackages');
		}
	}
	
	protected function saveDeclaration()
	{
		try
		{
			if (!$this->user ||
				!$this->user->user_id ||
				!is_numeric($this->uri->segment(3)))
			{
				throw new Exception('Доступ запрещен.');
			}
		
			$this->load->model('PackageModel', 'Packages');
	
			// роли и разграничение доступа
			if ($this->user->user_group == 'admin')
			{
			    $package = $this->Packages->getById($this->uri->segment(3));
			}
			else if ($this->user->user_group == 'manager')
			{
				$package = $this->Packages->getManagerPackageById($this->uri->segment(3), $this->user->user_id);
			}
			else if ($this->user->user_group == 'client')
			{
				$package = $this->Packages->getClientPackageById($this->uri->segment(3), $this->user->user_id);
			}
			
			if (!$package)
			{
				throw new Exception('Невозможно сохранить декларацию. Соответствующая посылка не найдена.');
			}

			if ($package->package_status == 'sent')
			{
				throw new Exception('Невозможно изменять декларацию для отправленных посылок.');
			}
			
			if ($this->user->user_group == 'manager' && $package->declaration_status != 'help')
			{
				throw new Exception('Вы не можете изменить декларацию без запроса клиента.');
			}			

			$this->load->model('DeclarationModel', 'Declarations');

			// итерируем по товарам в декларации
			$this->db->trans_begin();
			
			foreach($_POST as $key=>$value)
			{
				if (stripos($key, 'declaration_item') === 0) 
				{
					$declaration_id = str_ireplace('declaration_item', '', $key);
					$this->updateDeclarationItem($declaration_id);
				}
				else if (stripos($key, 'new_item') === 0) 
				{
					$declaration_id = str_ireplace('new_item', '', $key);
					$this->insertDeclarationItem($declaration_id);
				}
			}
			
			// вычисляем статус декларации
			$declarations = $this->Declarations->getDeclarationsByPackageId($package->package_id);
			
			if ($package->declaration_status == 'completed' &&
				!(isset($declarations) &&
				$declarations))
			{
				$package->declaration_status = 'not_completed';
			}
			else if ($package->declaration_status == 'not_completed' &&
				isset($declarations) &&
				$declarations)
			{
				$package->declaration_status = 'completed';
			}
			
			// вычисляем стоимость посылки
			$this->load->model('ConfigModel', 'Config');
			$this->load->model('PricelistModel', 'Pricelist');
			
			$package = $this->Packages->calculateCost($package, $this->Config, $this->Pricelist);
			
			if (!$package) 
			{
				throw new Exception('Невозможно сохранить декларацию. Стоимость посылки не определена.');
			}
			
			// сохраняем посылку
			$package = $this->Packages->savePackage($package);

			if (!$package)
			{
				throw new Exception('Декларация не сохранена. Попробуйте еще раз.');
			}
			
			$this->db->trans_commit();
			
			// выводим сообщение
			$this->result->m = 'Декларация успешно сохранена.';			
			Stack::push('result', $this->result);
		}
		catch (Exception $e) 
		{
			$this->db->trans_rollback();
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}
		
		// открываем декларацию
		Func::redirect(BASEURL.$this->cname.'/showDeclaration/'.$this->uri->segment(3));
	}
	
	protected function deletePackage()
	{
		try
		{
			if (!$this->user ||
				!$this->user->user_id ||
				!is_numeric($this->uri->segment(3)))
			{
				throw new Exception('Доступ запрещен.');
			}
			
			$this->load->model('PackageModel', 'Packages');

			// роли и разграничение доступа
			if ($this->user->user_group == 'admin')
			{
			    $package = $this->Packages->getById($this->uri->segment(3));
			}
			else if ($this->user->user_group == 'manager')
			{
				$package = $this->Packages->getManagerPackageById($this->uri->segment(3), $this->user->user_id);
			}
			else if ($this->user->user_group == 'client')
			{
				$package = $this->Packages->getClientPackageById($this->uri->segment(3), $this->user->user_id);
			}
			
			if (!$package)
			{
				throw new Exception('Посылка не найдена. Попробуйте еще раз.');
			}

			// сохранение результатов
			$package->package_status	= 'deleted';
			$deleted_package = $this->Packages->savePackage($package);
			
			if (!$deleted_package)
			{
				throw new Exception('Посылка не удалена. Попробуйте еще раз.');
			}			

			$this->result->m = 'Посылка успешно удалена.';
			Stack::push('result', $this->result);
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}
		
		// открываем новые посылки
		if($this->user->user_group != 'client') Func::redirect(BASEURL.$this->cname.'/showNewPackages');
		else Func::redirect(BASEURL.$this->cname.'/showOpenPackages');
	}
	
	protected function deleteOrder()
	{
		try
		{
			if (!$this->user ||
				!$this->user->user_id ||
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
			
			if (!$deleted_order)
			{
				throw new Exception('Заказ не удален. Попробуйте еще раз.');
			}
			
			// уведомления, удаленный заказ
			$this->load->model('ManagerModel', 'Managers');
			$this->load->model('UserModel', 'Users');
			$this->load->model('ClientModel', 'Clients');

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
		Func::redirect(BASEURL.$this->cname.'/showOpenOrders');
	}
	
	protected function showAddPackage()
	{
		try
		{
			if (!$this->user ||
				!$this->user->user_id)
			{
				throw new Exception('Доступ запрещен.');
			}
			
			$this->load->model('ClientModel', 'Clients');

			$view = array();
			
//			// отображаем список партнеров для админа
//			if ($this->user->user_group == 'admin')
//			{
				$this->load->model('ManagerModel', 'Managers');
				$view['managers'] = $this->Managers->getManagersData();
				
				if (!$view['managers'])
				{
					throw new Exception('Партнеры не найдены. Попробуйте еще раз.');
				}
//			}

			@$view['client'] = $this->__client;
			@$view['partners'] = $this->__partners;
			
			if($this->user->user_group == 'client') {
				$this->load->model('PdetailModel', 'Pdetails');
				$view['pdetails'] = $this->Pdetails->getFilteredDetails(array('pdetail_client'=>$this->user->user_id,'pdetail_package'=>0),TRUE);
				foreach($view['pdetails'] as $key => $val)
				{
					$view['pdetails'][$key]->pdetail_status_desc = $this->Pdetails->getPackageDetailsStatusDescription($val->pdetail_status);
				}
				
			}
						
			View::showChild($this->viewpath.'/pages/showAddPackage', $view);
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
			Func::redirect(BASEURL.$this->cname);
		}
	}
	
	protected function editPackageAddress()
	{
		try
		{
			// безопасность
			if (!$this->user ||
				!$this->user->user_id ||
				!is_numeric($this->uri->segment(3)))
			{
				throw new Exception('Доступ запрещен.');
			}
		
			$this->load->model('PackageModel', 'Packages');
			
			// роли и разграничение доступа
			if ($this->user->user_group == 'admin')
			{
			    $view['package'] = $this->Packages->getById($this->uri->segment(3));
			}
			else if ($this->user->user_group == 'manager')
			{
				$view['package'] = $this->Packages->getManagerPackageById($this->uri->segment(3), $this->user->user_id);
			}
			else if ($this->user->user_group == 'client')
			{
				$view['package'] = $this->Packages->getClientPackageById($this->uri->segment(3), $this->user->user_id);
			}
			
			if (!$view['package'])
			{
				throw new Exception('Невозможно изменить адрес посылки. Соответствующая посылка недоступна.');
			}
			
			// безопасность: редактирование только неоплаченных или неполученных посылок
			if ($view['package']->package_status != 'not_payed' && 
				$view['package']->package_status != 'not_delivered' && 
				$view['package']->package_status != 'processing')
			{
				throw new Exception('Невозможно изменить адрес посылки. Соответствующая посылка уже оплачена либо еще не получена партнером.');
			}
			
			// отображаем список стран
			$this->load->model('CountryModel', 'Countries');

			$view['countries'] = $this->Countries->getDeliveryCountries($view['package']->package_country_from);
			
			if (!$view['countries']) 
			{
				throw new Exception('Невозможно изменить адрес посылки. Список стран недоступен.');
			}
			
			View::showChild($this->viewpath.'/pages/editPackageAddress', $view);
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
			Func::redirect(BASEURL.$this->cname);
		}
	}

	protected function updatePackageAddress()
	{
		try
		{
			// безопасность
			if (!$this->user ||
				!$this->user->user_id ||
				!is_numeric($this->uri->segment(3)))
			{
				throw new Exception('Доступ запрещен.');
			}
		
			$this->load->model('PackageModel', 'Packages');
			
			// роли и разграничение доступа
			if ($this->user->user_group == 'admin')
			{
			    $package = $this->Packages->getById($this->uri->segment(3));
			}
			else if ($this->user->user_group == 'manager')
			{
				$package = $this->Packages->getManagerPackageById($this->uri->segment(3), $this->user->user_id);
			}
			else if ($this->user->user_group == 'client')
			{
				$package = $this->Packages->getClientPackageById($this->uri->segment(3), $this->user->user_id);
			}
			
			if (!$package)
			{
				throw new Exception('Невозможно изменить адрес посылки. Соответствующая посылка недоступна.');
			}
			
			// безопасность: редактирование только неоплаченных посылок
			if ($package->package_status != 'not_payed' && $package->package_status != 'not_delivered')
			{
				throw new Exception('Невозможно изменить адрес посылки. Соответствующая посылка уже оплачена.');
			}
			
			// валидация пользовательского ввода
			$prev_country = $package->package_country_to;
			
			Check::reset_empties();
			$package->package_country_to		= Check::int('package_country_to');
			$package->package_address			= Check::txt('package_address', 255, 1, '');
			$empties							= Check::get_empties();
			
			if (is_array($empties)) 
			{
				throw new Exception('Одно или несколько полей не заполнено.');
			}
			
			// проверка доступности способа доставки
			$filter = new stdClass();
			
			$filter->pricelist_country_from = $package->package_country_from;
			$filter->pricelist_country_to = $package->package_country_to;
			$filter->pricelist_delivery = '';
			
			$this->load->model('PricelistModel', 'Pricelist');
			$pricelist = $this->Pricelist->getPricelist($filter);
			
			if (!$pricelist)
			{
				throw new Exception('Невозможно изменить адрес посылки. Доставка в выбранную страну недоступна.');
			}
			
			// вычисляем стоимость посылки
			if ($prev_country != $package->package_country_to)
			{
				$package->package_delivery = 0;
				$package->package_delivery_cost = 0;

				$this->load->model('ConfigModel', 'Config');
				$this->load->model('PackageModel', 'Packages');
				
				$package = $this->Packages->calculateCost($package, $this->Config);
				
				if (!$package) 
				{
					throw new Exception('Невозможно изменить адрес посылки. Ошибка вычисления стоимости посылки.');
				}
			}
			
			// сохранение результатов
			$this->load->model('PackageModel', 'Packages');
			$new_package = $this->Packages->savePackage($package);
			
			if (!$new_package)
			{
				throw new Exception('Адрес посылки не изменен. Попробуйте еще раз.');
			}			

			// открываем новые посылки
			$this->result->m = 'Адрес посылки успешно изменен.';			
			Stack::push('result', $this->result);
			
			if ($this->user->user_group == 'admin' ||
				$this->user->user_group == 'manager')
			{
				Func::redirect(BASEURL.$this->cname.'/showNewPackages');
			}
			else if ($this->user->user_group == 'client')
			{
				Func::redirect(BASEURL.$this->cname.'/showOpenPackages');
			}
			
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
			Func::redirect(BASEURL.$this->cname.'/editPackageAddress/'.$this->uri->segment(3));
		}
	}
	
	protected function updateOrderDetails()
	{
		try
		{
			if (!$this->user ||
				!$this->user->user_id ||
				!isset($_POST['order_id']) ||
				!is_numeric($_POST['order_id']))
			{
				throw new Exception('Доступ запрещен.');
			}
			
			$order_id = $_POST['order_id'];
			
			// роли и разграничение доступа
			$order = $this->getPrivilegedOrder(
				$order_id, 
				'Невозможно сохранить детали заказа. Заказ недоступен.');

			// валидация пользовательского ввода
			Check::reset_empties();
			$order->order_products_cost	= Check::float('order_products_cost');
			$order->order_delivery_cost	= Check::float('order_delivery_cost');
			$empties					= Check::get_empties();
	
			if ($empties) 
			{
				throw new Exception('Некоторые поля не заполнены. Попробуйте еще раз.');
			}
			
			// вычисляем стоимость заказа
			if (FALSE &&
				$this->user->user_group == 'admin' &&
				$order_cost != $order->order_cost)
			{
				$order->order_cost = $order_cost;
			}
			else
			{
				$this->load->model('ConfigModel', 'Config');
				$order = $this->Orders->calculateCost($order, $this->Config);
				
				if (!$order) 
				{
					throw new Exception('Невозможно вычислить стоимость заказа. Попробуйте еще раз.');
				}
			}
		
			// вычисляем стоимость международной доставки
			$this->load->model('PricelistModel', 'Pricelist');
			$this->Orders->setAvailableDeliveries($order, $this->Pricelist);
			$order->package_delivery_cost = '';
			
			if ($order->delivery_list)
			{
				foreach ($order->delivery_list as $delivery)
				{
					$order->package_delivery_cost .= $delivery->delivery_name.': '.$delivery->delivery_price.'р<br />';
				}
			}

			// сохранение результатов
			$new_order = $this->Orders->saveOrder($order);
			
			if (!$new_order)
			{
				throw new Exception('Заказ не сохранен. Попробуйте еще раз.');
			}
			
			$this->result->m = 'Заказ успешно сохранен.';
			Stack::push('result', $this->result);
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}
		
		// открываем детали заказа
		Func::redirect(BASEURL.$this->cname.'/showOrderDetails/'.$order_id);
	}

	protected function updatePackageDetails()
	{
		try
		{
			if (!$this->user ||
				!$this->user->user_id ||
				!isset($_POST['package_id']) ||
				!is_numeric($_POST['package_id']))
			{
				throw new Exception('Доступ запрещен.');
			}
			
			$this->load->model('PackageModel', 'Packages');
			$package_id = $_POST['package_id'];
			
			// роли и разграничение доступа
			if ($this->user->user_group == 'admin')
			{
			    $package = $this->Packages->getById($package_id);
			}
			else if ($this->user->user_group == 'manager')
			{
				$package = $this->Packages->getManagerPackageById($package_id, $this->user->user_id);
			}
			else if ($this->user->user_group == 'client')
			{
				$package = $this->Packages->getClientPackageById($package_id, $this->user->user_id);
			}
			
			if (!$package)
			{
				throw new Exception('Невозможно сохранить детали заказа. Заказ недоступен.');
			}

			// валидация пользовательского ввода
			Check::reset_empties();
			$package->package_trackingno	= Check::str('trackingno',32);
			
			if ($this->user->user_group == 'admin' OR
				$this->user->user_group == 'manager')
			{
				$package->package_special_comment	= Check::str('special_comment',255);
				$package->package_special_cost	= floatval($_POST['special_cost']);

				// находим курс конвертации
				$this->load->model('CountryModel', 'Countries');
				$this->load->model('CurrencyModel', 'Currencies');
				$this->load->model('ManagerModel', 'Managers');
				$manager = $this->Managers->getById($package->package_manager);
				$country = $this->Countries->getById($manager->manager_country);
			
				if ( ! $country)
				{
					throw new Exception('Невозможно конвертировать цену в доллары. Курс не найден.');
				}
				
				$cross_rate = $this->Currencies->getById($country->country_currency);
				
				if ( ! $cross_rate)
				{
					throw new Exception('Невозможно конвертировать цену в доллары. Попробуйте еще раз.');
				}
				
				// конвертим доп.платежи и округляем до центов
				$package->package_special_cost_usd = $this->convert($cross_rate,(float)$package->package_special_cost);
				$package->package_special_cost_usd = ceil($package->package_special_cost_usd * 100) * 0.01;

				// WTF?? так и не докопался почему special_cost не хочет обнуляться без этого
				if ($package->package_special_cost_usd == 0)
				{
					$package->package_special_cost = 0;
				}

				// вычисляем стоимость посылки
				$new_package = $this->Packages->recalculatePackage($package);
			}
			else
			{
				// сохранение результатов
				$new_package = $this->Packages->savePackage($package);
			}
			
			if ( ! $new_package)
			{
				throw new Exception('Посылка не сохранена. Попробуйте еще раз.');
			}
			
			$this->result->m = 'Посылка успешно сохранена.';
			Stack::push('result', $this->result);
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}
		
		Func::redirect($_SERVER['HTTP_REFERER']);
	}
	
	
	protected function updateStatus($status, $pageName, $modelName)
	{
		try
		{
			if (!$this->user ||
				!$this->user->user_id)
			{
				throw new Exception('Доступ запрещен.');
			}
			
			// проверка сохранения статусов деклараций
			if (isset($_POST['declaration_status']) &&
				($_POST['declaration_status'] == 'completed' ||
				$_POST['declaration_status'] == 'not_completed'))
			{
				$declaration_status = $_POST['declaration_status'];
			}
			
			$this->load->model('ManagerModel', 'Managers');
			$this->load->model('ClientModel', 'Clients');
			
			// итерируем по посылкам
			$this->db->trans_begin();
			$this->load->model('OrderModel', 'Orders');			
			
			if ($modelName == 'PackageModel')
			{
				$this->load->model('PackageModel', 'Packages');
				
				foreach($_POST as $key => $value)
				{
					$this->updatePackageStatus($key, $value);
				}
			}
			// итерируем по заказам
			else if ($modelName == 'OrderModel')
			{
				foreach($_POST as $key => $value)
				{
					$this->updateOrderStatus($key, $value);
				}
			}

			$this->db->trans_commit();
		}
		catch (Exception $e) 
		{
			$this->db->trans_rollback();

			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}

		// открываем посылки
		Func::redirect($_SERVER['HTTP_REFERER']);
	}

	private function updatePackageStatus($param, $value)
	{
		// посылка или нет?
		$is_package = FALSE;
		$statuses = $this->Packages->getStatuses();
		
		if (stripos($param, 'package_status') !== FALSE)
		{		
			$package_id = str_ireplace('package_status', '', $param);
			
			if (is_numeric($package_id))
			{				
				$package_status = $_POST['package_status'.$package_id];
			
				if (empty($statuses[$package_status]))
				{
					throw new Exception('Статус одной или нескольких посылок не определен. Попоробуйте еще раз.');
				}
				
				$is_package = TRUE;
			}
		}
		
		// декларация или нет?
		$is_declaration = FALSE;
		
		if ( ! $is_package &&
			isset($_POST['declaration_status']) &&
			stripos($param, 'help') !== FALSE)
		{		
			$package_id = str_ireplace('help', '', $param);
			
			if (is_numeric($package_id))
			{	
				$declaration_status = $_POST['declaration_status'];
				
				if ($declaration_status != 'not_completed' && 
					$declaration_status != 'completed')
				{
					throw new Exception('Статус деклараций не определен. Попоробуйте еще раз.');
				}
				
				$is_declaration = TRUE;
			}
		}
		
		// если не посылка и не декларация, выходим
		if ( ! $is_package AND ! $is_declaration) return;		

		// роли и разграничение доступа
		if ($this->user->user_group == 'admin')
		{
			$package = $this->Packages->getById($package_id);
		}
		else if ($this->user->user_group == 'manager')
		{
			$package = $this->Packages->getManagerPackageById($package_id, $this->user->user_id);
		}
		
		if ( ! $package)
		{
			throw new Exception('Одна или несколько посылок не найдены. Попоробуйте еще раз.');
		}
			
		// меняем статус посылки
		$is_status_changed = FALSE;
		// $is_order_closing = FALSE;
		// $is_order_opening = FALSE;
		$new_order_status = '';
		
		if ($is_package AND
			$package->package_status != $package_status)
		{
			// проверяем, менять ли статус связанного заказа
			if ($this->user->user_group == 'admin' AND
				$package->order_id)
			{
				if ($package->package_status == 'sent')
				{
					$new_order_status = 'payed';
				}
				else if ($package_status == 'sent')
				{
					$new_order_status = 'sended';
				}
			}
			
			$package->package_status = $package_status;
			$is_status_changed = TRUE;			
		}
		
		// меняем статус декларации
		if ($is_declaration)
		{
			$package->declaration_status = $declaration_status;
		}
		
		// добавляем trackingno
		if (isset($_POST['send_package'.$package_id]))
		{
			Check::reset_empties();
			$package->package_status		= 'sent';
			$package->package_trackingno 	= Check::txt('package_trackingno'.$package_id, 255, 1);
			
			// закрываем связанный заказ
			if ($package->order_id)
			{
				$new_order_status = 'sended';
			}
			
			$empties						= Check::get_empties();
	
			if ($empties) 
			{
				throw new Exception('Некоторые Tracking № отсутствуют. Попробуйте еще раз.');
			}
		}			
		
		// сохранение результатов
		$new_package = $this->Packages->savePackage($package);
		
		if ( ! $new_package)
		{
			throw new Exception('Статусы посылок/деклараций не изменены. Попоробуйте еще раз.');
		}
		
		// закрываем связанный заказ
		if ($new_order_status)
		{
			$order = $this->Orders->getById($package->order_id);
			
			if ( ! $order)
			{
				throw new Exception('Невозможно закрыть или открыть связанный с посылкой заказ. Заказ не найден.');
			}
			
			if ($order->order_status != $new_order_status)
			{
				$order->order_status = $new_order_status;
				$this->Orders->saveOrder($order);
			}
		}

		// уведомления
		$status_caption = $this->Packages->getPackageStatusDescription($package->package_status);
		if ($is_status_changed)
		{
			if ($this->user->user_group != 'admin')
			{
				Mailer::sendAdminNotification(
					Mailer::SUBJECT_NEW_PACKAGE_STATUS, 
					Mailer::NEW_PACKAGE_STATUS_NOTIFICATION, 
					0,
					$package->package_id, 
					0,
					"http://countrypost.ru/admin/showPackageDetails/{$package->package_id}",
					NULL,
					$this->Users,
					$status_caption);
			}
			
			if ($this->user->user_group != 'manager')
			{
				Mailer::sendManagerNotification(
					Mailer::SUBJECT_NEW_PACKAGE_STATUS, 
					Mailer::NEW_PACKAGE_STATUS_NOTIFICATION, 
					$package->package_manager,
					$package->package_id, 
					0,
					"http://countrypost.ru/manager/showPackageDetails/{$package->package_id}",
					$this->Managers,
					NULL,
					$status_caption);
			}

			Mailer::sendClientNotification(
				Mailer::SUBJECT_NEW_PACKAGE_STATUS, 
				Mailer::NEW_PACKAGE_STATUS_NOTIFICATION, 
				$package->package_id, 
				$package->package_client,
				"http://countrypost.ru/client/showPackageDetails/{$package->package_id}",
				$this->Clients,
				$status_caption);
		}
	}

	private function updateOrderStatus($param, $value)
	{
		// заказ или нет?
		if (stripos($param, 'order_status') === FALSE) return;
		
		$order_id = str_ireplace('order_status', '', $param);
		if (!is_numeric($order_id)) return;
			
		$order_status = $_POST['order_status'.$order_id];
		
		if ($order_status != 'proccessing' && 
			strpos($order_status, 'not_available') === FALSE && 
			$order_status != 'not_payed' && 
			$order_status != 'not_delivered' && 
			$order_status != 'payed' && 
			$order_status != 'sended')
		{
			throw new Exception('Статус одного или нескольких заказов не определен. Попоробуйте еще раз.');
		}
		
		// роли и разграничение доступа
		if ($this->user->user_group == 'admin')
		{
			$order = $this->Orders->getById($order_id);

			if ($order->order_status == 'payed')
			{
				if (!isset($_POST['payed'.$order_id]) || !is_numeric($_POST['payed'.$order_id]))
				{
					throw new Exception('Сумма оплаты одного или нескольких заказов не определена. Попоробуйте еще раз.');
				}
				
				$order->order_cost_payed = (float)$_POST['payed'.$order_id];
			}
		}
		else if ($this->user->user_group == 'manager')
		{
			$order = $this->Orders->getManagerOrderById($order_id, $this->user->user_id);
		}
		
		if (!$order)
		{
			throw new Exception('Один или несколько заказов не найдены. Попоробуйте еще раз.');
		}
			
		// меняем статус заказа
		$recent_status = $order->order_status;
		$order->order_status = $order_status;
		
		// сохранение результатов
		$new_order = $this->Orders->saveOrder($order);

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
		}
	}
	
	protected function updateOdetailStatuses()
	{
		try
		{
			if (!$this->user ||
				!$this->user->user_id ||
				!isset($_POST['order_id']) ||
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
		
			if (!$country)
			{
				throw new Exception('Невозможно конвертировать цену в доллары. Курс не найден.');
			}
			
			$cross_rate = $this->Currencies->getById($country->country_currency);
			
			if (!$cross_rate)
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
					if (!$odetail) 
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
					
					if ($current_status != 'purchased' && $current_status != 'received' && $current_status != 'not_delivered')
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
						if ($odetail->odetail_joint_id && 
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
					if (!$odetail->odetail_joint_id)
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
				if (!$joint) 
				{
					throw new Exception('Некоторые товары не найдены. Попоробуйте еще раз.');
				}
				
				// инициализация
				$odetail_joint_cost = isset($_POST["odetail_joint_cost$odetail_joint_id"]) ? $_POST["odetail_joint_cost$odetail_joint_id"] : NULL;

				if (!empty($odetail_joint_cost) &&
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
			
			if (!$status)
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
			Func::redirect(BASEURL.$this->cname.'/showOrderDetails/'.$order_id);
		}
		else
		{
			Func::redirect(BASEURL.$this->cname);
		}
	}
	
	protected function updatePdetailStatuses()
	{
		try
		{
			if (!$this->user ||
				!$this->user->user_id ||
				!isset($_POST['package_id']) ||
				!is_numeric($_POST['package_id']))
			{
				throw new Exception('Доступ запрещен.');
			}
		
			$package_id = $_POST['package_id'];
			$this->load->model('PackageModel', 'Packages');

			// роли и разграничение доступа
			if ($this->user->user_group == 'admin')
			{
			    $package = $this->Packages->getById($package_id);
			}
			else if ($this->user->user_group == 'manager')
			{
				$package = $this->Packages->getManagerPackageById($package_id, $this->user->user_id);
			}
			else if ($this->user->user_group == 'client')
			{
				$package = $this->Packages->getClientPackageById($package_id, $this->user->user_id);
			}
			
			if ( ! $package)
			{
				throw new Exception('Невозможно изменить статусы товаров. Заказ не найден.');
			}
				
			// итерируем по товарам
			$this->load->model('PdetailModel', 'Pdetails');
			$this->db->trans_begin();
			
			// поиск параметров в запросе
			foreach ($_POST as $key => $value)
			{
				if (($this->user->user_group == 'admin' OR
					$this->user->user_group == 'manager') AND 
					stripos($key, 'pdetail_status') === 0)
				{
					$pdetail_id = str_ireplace('pdetail_status', '', $key);
					if ( ! is_numeric($pdetail_id)) continue;

					// сохранение результатов
					$pdetail = $this->Pdetails->getById($pdetail_id);
					if ( ! $pdetail) 
					{
						throw new Exception('Некоторые товары не найдены. Попоробуйте еще раз.');
					}
					
					$pdetail->pdetail_status = $value;
					$new_pdetail = $this->Pdetails->updatePdetail($pdetail);
				}
				
				if (stripos($key, 'pdetail_special_boxes') === 0)
				{
					$pdetail_id = str_ireplace('pdetail_special_boxes', '', $key);
					if ( ! is_numeric($pdetail_id)) continue;

					// сохранение результатов
					$pdetail = $this->Pdetails->getById($pdetail_id);
					if ( ! $pdetail) 
					{
						throw new Exception('Некоторые товары не найдены. Попоробуйте еще раз.');
					}
															
					$pdetail->pdetail_special_boxes = intval($value);
					$new_pdetail = $this->Pdetails->updatePdetail($pdetail);
				}
				
				if (stripos($key, 'pdetail_special_invoices') === 0)
				{
					$pdetail_id = str_ireplace('pdetail_special_invoices', '', $key);
					if ( ! is_numeric($pdetail_id)) continue;

					// сохранение результатов
					$pdetail = $this->Pdetails->getById($pdetail_id);
					if (!$pdetail) 
					{
						throw new Exception('Некоторые товары не найдены. Попоробуйте еще раз.');
					}
															
					$pdetail->pdetail_special_invoices = intval($value);
					$new_pdetail = $this->Pdetails->updatePdetail($pdetail);
				}
			}
			
			// меняем статус заказа
			$this->Packages->recalculatePackage($package);
			$this->db->trans_commit();
			
			$this->result->e = 1;			
			$this->result->m = 'Статусы товаров и посылки успешно изменены.';
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
			Func::redirect(BASEURL.$this->cname.'/showOrderDetails/'.$order_id);
		}
		else
		{
			Func::redirect(BASEURL.$this->cname);
		}
	}
	
	protected function updateWeight($redirect = TRUE)
	{
		try
		{
			if (!$this->user ||
				!$this->user->user_id)
			{
				throw new Exception('Доступ запрещен.');
			}
		
			$this->load->model('ConfigModel', 'Config');
			$this->load->model('PackageModel', 'Packages');

			// итерируем по товарам
			$this->db->trans_begin();
			
			// поиск параметров в запросе
			foreach($_POST as $key=>$value)
			{
				// обычные товары
				if (stripos($key, 'package_weight') === 0)
				{
					$package_id = str_ireplace('package_weight', '', $key);
					if (!is_numeric($package_id)) continue;

					// сохранение результатов
					$package = $this->Packages->getById($package_id);
					if (!$package) 
					{
						throw new Exception('Некоторые товары не найдены. Попоробуйте еще раз.');
					}
		
					if ($package->package_weight != $value)
					{
						$package->package_weight = $value;
						$this->load->model('PricelistModel', 'Pricelist');
						$package = $this->Packages->calculateCost($package, $this->Config, $this->Pricelist);

						$this->Packages->savePackage($package);
					}
				}
			}

			$this->db->trans_commit();
			
			$this->result->e = 1;			
			$this->result->m = 'Статусы товаров, цены и местная доставка успешно изменены.';
		}
		catch (Exception $e) 
		{
			$this->db->trans_rollback();
			$this->result->e = -1;			
			$this->result->m = $e->getMessage();
		}

		Stack::push('result', $this->result);
		
		if ($redirect)
		{
			Func::redirect($_SERVER['HTTP_REFERER']);
		}
	}
	
	protected function deleteOrder2out($oid) 
	{
		try 
		{
			// безопасность
			if (!isset($oid) ||
				!is_numeric($oid))
			{
				throw new Exception('Доступ запрещен.');
			}
		
			// валидация пользовательского ввода
			$this->load->model('Order2outModel', 'Order2out');
		
			$o2o = $this->Order2out->getById((int) $oid);
			
			// роли и доступ
			if ($this->user->user_group == 'admin')
			{
				if (!$o2o)
				{
					throw new Exception('Заявка не найдена. Попробуйте еще раз.');
				}
			}
			else if (!$o2o || 
				$o2o->order2out_user != $this->user->user_id ||
				$o2o->order2out_status != 'processing')
			{
				throw new Exception('Заявка не найдена. Попробуйте еще раз.');
			}
			
			// долларовый счет или в местной валюте
			$is_usd_account = !empty($o2o->order2out_ammount) && (empty($o2o->order2out_currency) || !empty($o2o->order2out_ammount_local));
			$is_local_account = isset($o2o->order2out_currency);
			
			$this->load->model('ManagerModel', 'Manager');
			$this->load->model('PaymentModel', 'Payment');

			$tax = 0;
			$payment_obj = new stdClass();
			
			// партнерская заявка
			if ($this->Manager->getById($o2o->order2out_user))
			{
				$payment_obj->payment_from		= 0;
				$payment_obj->payment_type		= 'salary';
			}
			// клиентская заявка
			else
			{
				$payment_obj->payment_from		= $o2o->order2out_details;
				$payment_obj->payment_type		= 'out';
				$tax = isset($o2o->order2out_payment_service) ? constant(strtoupper($o2o->order2out_payment_service).'_OUT_TAX') : 0;
			}
			
			// заполняем структуру
			$payment_obj->payment_to			= $o2o->order2out_user;
			$payment_obj->payment_amount_from	= 0;
			$payment_obj->payment_amount_to		= $o2o->order2out_ammount;
			$payment_obj->payment_amount_tax	= $tax * $o2o->order2out_ammount / 100;
			$payment_obj->payment_amount_rur	= $o2o->order2out_ammount_rur;
			$payment_obj->payment_service_id	= $o2o->order2out_payment_service;
			$payment_obj->payment_purpose		= 'отмена заявки на вывод';
			$payment_obj->payment_comment		= '№ '.$o2o->order2out_id;
			
			$this->db->trans_begin();

			//платеж в долларах
			if ($is_usd_account)
			{
				if (!$this->Payment->makePayment($payment_obj)) 
				{
					throw new Exception('Невозможно отменить заявку на вывод. Попробуйте еще раз.');
				}
				
				// сохраняем результат в сессии
				if ($this->user->user_group != 'admin')
				{
					$this->session->set_userdata(array('user_coints' => $this->user->user_coints + $payment_obj->payment_amount_to));
				}
			}
			
			//платеж в местной валюте
			if ($is_local_account)
			{
				$payment_obj->payment_currency = $o2o->order2out_currency;
				// поддерживаем старые заявки
				$payment_obj->payment_amount_to	= empty($o2o->order2out_ammount_local) ?
					$o2o->order2out_ammount :
					$o2o->order2out_ammount_local;
			
				if (!$this->Payment->makePaymentLocal($payment_obj)) 
				{
					throw new Exception('Невозможно отменить заявку на вывод. Попробуйте еще раз.');
				}
				
				// сохраняем результат в сессии
				if ($this->user->user_group == 'manager')
				{
					$this->session->set_userdata(array('manager_balance_local' => 
						$this->session->userdata('manager_balance_local') + 
						$payment_obj->payment_amount_to));
				}
			}
			
			// удаляем заявку
			$o2o->order2out_status = 'deleted';
			
			if (!$this->Order2out->addOrder($o2o)) 
			{
				throw new Exception('Ошибка удаления заявки на вывод. Попробуйте еще раз.');
			}		
			
			$this->db->trans_commit();
			$this->result->r = 1;
			$this->result->message = 'Заявка успешно удалена.';
		}
		catch (Exception $e)
		{
			$this->db->trans_rollback();
			$this->result->r = -1;
			$this->result->message = $e->getMessage();
		}

		Stack::push('result', $this->result);		
		Func::redirect($_SERVER['HTTP_REFERER']);
	}
	
	protected function deleteOrder2in($oid) 
	{
		try 
		{
			// безопасность
			if (!isset($oid) ||
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
				if (!$o2o)
				{
					throw new Exception('Заявка не найдена. Попробуйте еще раз.');
				}
			}
			else if (!$o2o || $o2o->order2in_status != 'processing' || $o2o->order2in_user != $this->user->user_id)
			{
				throw new Exception('Заявка не найдена. Попробуйте еще раз.');
			}
			
			// сохранение результата
			$this->db->trans_begin();
			/*
			if ($this->user->user_group == 'admin' && $o2o->order2in_status == 'payed')
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
					
				if (!$this->Payment->makePayment($payment_obj)) 
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
			
			if (!$this->Order2in->addOrder($o2o)) 
			{
				throw new Exception('Ошибка удаления заявки на вывод. Попробуйте еще раз.');
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
			Func::redirect(BASEURL.'syspay/showClientOpenOrders2In');
		}
		else
		{
			Func::redirect(BASEURL.'syspay');
		}
	}
	
	
	public function getPayments(){
		
		if (!$this->user)	return FALSE;
		
		$this->load->model('PaymentModel', 'Payment');
		
	}

	
	/**
	 * Достаем фото посылки по имени файла и ИД посылки, последний нужен для секурности и поиска нужного каталога
	 *
	 * @param (int)		$pid
	 * @param (string)	$filename
	 */
	protected function showPackagePhoto($pid,$filename) {
		
		header('Content-type: image/jpg');
		
		$filename	= Check::var_str($filename, 255,1);
		(int) $pid;
		
		$this->load->model('PackageModel', 'Package');
		$package	= $this->Package->getById($pid);
		
		if ($this->user->user_group == 'admin'){

		}
		else if ($this->user->user_group == 'manager'){
			if ($this->user->user_id != $package->package_manager) die();
		}
		else if ($this->user->user_group == 'client'){
			if ($this->user->user_id != $package->package_client) die();
		}else{
			die();
		}

		if (!$package || $pid != $package->package_id){
			die();
		}
		
		
		if (file_exists(UPLOAD_DIR.'packages/'.$package->package_manager.'/'.$pid.'/'.$filename)){
			readfile(UPLOAD_DIR.'packages/'.$package->package_manager.'/'.$pid.'/'.$filename);
		}
		
		die();
	}
	
	
	protected function delPackageComment($package_id, $comment_id)
	{
		try
		{
		
			(int) $comment_id;
			(int) $package_id;
			$this->load->model('PackageModel', 'Packages');
			
			if ($this->user->user_group	== 'manager'){
				$package = $this->Packages->getManagerPackageById($package_id, $this->user->user_id);
				
			}elseif ($this->user->user_group == 'client'){
				$package = $this->Packages->getClientPackageById($package_id, $this->user->user_id);
				
			}elseif ($this->user->user_group == 'admin'){
				$package = $this->Packages->getById($package_id);
				
			}else{
				throw new Exception('Доступ запрешен.');
			}
			

			if (!$package)
			{
				throw new Exception('Невозможно удалить комментарий. Посылка не найдена.');
			}

			
			// сохранение результатов
			$this->load->model('PCommentModel', 'Comments');
			
			if (!$this->Comments->delComment($comment_id))
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
		Func::redirect(BASEURL.$this->cname.'/showPackageDetails/'.$this->uri->segment(3).'#comments');
	}
	
	protected function delOrderComment($order_id, $comment_id){
		
		try
		{
			(int)	$order_id;
			(int)	$comment_id;
			
			// роли и разграничение доступа
			$order = $this->getPrivilegedOrder(
				$order_id, 
				'Невозможно удалить комментарий. Соответствующий заказ недоступен.');
			
			// сохранение результатов
			$this->load->model('OCommentModel', 'Comments');
			
			if (!$this->Comments->delComment($comment_id))
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
		Func::redirect(BASEURL.$this->cname.'/showOrderDetails/'.$order_id);		
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
		/*
		типы урлов без пейджинга:
		http://countrypost.ru/client/showOpenPackages	
		с пейджингом +1 сегмент
		*/
		
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
		else if ($segment_count == 1)
		{
			$this->paging_base_url = '/admin/showNewPackages';
		}
		else if ($this->user)
		{
			$this->paging_base_url = '/'.$this->user->user_group.'/'.$this->uri->segment(2);
		}

		if ($this->uri->segment(1) == 'main' &&
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
	
	protected function convert($cross_rate, $price)
	{
		return $price == 0 ? 0 : ($price / $cross_rate->cbr_cross_rate);
	}
	
	public function showPaymentHistory()
	{
		$this->load->model('PaymentModel', 'Payment');
		
		if ($this->user->user_group == 'client')
		{
			$view['Payments'] = $this->Payment->getFilteredPayments("(user_to.user_id={$this->user->user_id} OR user_from.user_id={$this->user->user_id}) AND (ISNULL(payment_currency) OR payment_type = 'salary') AND (payment_amount_from <> 0 OR payment_amount_to <> 0 OR (payment_type <> 'order' AND payment_type <> 'package' AND payment_type <> 'extra_payment'))");
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
					
			$per_page = isset($this->session->userdata['payments_per_page']) ? $this->session->userdata['payments_per_page'] : $this->per_page;
			$this->per_page = $per_page;
			$view['per_page'] = $per_page;
		}

		/* пейджинг */
		$this->init_paging();		
		$this->paging_count = count($view['Payments']);
	
		if (isset($view['Payments']) && $view['Payments'])
		{
			$view['Payments'] = array_slice($view['Payments'], $this->paging_offset, $this->per_page);
		}			
			
		$view['pager'] = $this->get_paging();

		// собираем платежные системы
		$this->load->model('PaymentServiceModel', 'Services');
		$view['services'] = $this->Services->getList();		
		$view['user'] = $this->user;
		
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
	
	protected function addInsurance($add = 1)
	{
		try
		{
			if (!$this->user ||
				!$this->user->user_id ||
				!is_numeric($this->uri->segment(3)))
			{
				throw new Exception('Доступ запрещен.');
			}
		
			// безопасность: проверяем связку клиента и посылки
			$this->load->model('PackageModel', 'Packages');
			
			if ($this->user->user_group == 'client')
			{
				$package = $this->Packages->getClientPackageById($this->uri->segment(3), $this->user->user_id);
			}
			else if ($this->user->user_group == 'admin')
			{
				$package = $this->Packages->getById($this->uri->segment(3));
			}
			
			if (!$package)
			{
				throw new Exception('Невозможно сохранить декларацию. Посылка недоступна.');
			}

			// меняем статус страховки
			$package->package_insurance = $add;
			$this->load->model('ConfigModel', 'Config');
			
			if ($add)
			{
				$package->package_insurance_cost = Check::int('insurance_amount');
				$empties						 = Check::get_empties();
				
				if ($empties)
				{
					throw new Exception('Стоимость страховки не определена. Попробуйте еще раз.');
				}
			}
			else
			{
				$package->package_insurance_cost = 0;
			}
			
			// вычисляем стоимость посылки
			$this->Packages->recalculatePackage($package);
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}
		
		// открываем посылки
		Func::redirect($_SERVER['HTTP_REFERER']);
	}
	
	protected function addDeclarationHelp()
	{
		try
		{
			if (!$this->user ||
				!$this->user->user_id ||
				!is_numeric($this->uri->segment(3)))
			{
				throw new Exception('Доступ запрещен.');
			}
		
			// безопасность: проверяем связку клиента и посылки
			$this->load->model('PackageModel', 'Packages');
			if ($this->user->user_group == 'admin')
			{
				$package = $this->Packages->getById($this->uri->segment(3));
			}
			else if ($this->user->user_group == 'client')
			{
				$package = $this->Packages->getClientPackageById($this->uri->segment(3), $this->user->user_id);
			}
			
			if (!$package)
			{
				throw new Exception('Невозможно сохранить декларацию. Посылка недоступна.');
			}

			// меняем статус декларации
			$package->declaration_status = 'help';
			$this->Packages->recalculatePackage($package);
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
	
	protected function joinProducts($order_id)
	{
		try
		{
			if (!is_numeric($order_id))
			{
				throw new Exception('Доступ запрещен.');
			}
		
			// безопасность: проверяем связку менеджера и посылки
			$order = $this->getPrivilegedOrder(
				$order_id, 
				'Невозможно объединить товары. Указанный заказ не найден.');

			$this->load->model('OdetailModel', 'Details');
			$this->load->model('OdetailJointModel', 'Joints');

			// погнали
			$this->db->trans_begin();
			
			$joint = $this->Joints->generateOdetailJoint();

			// ищем товары в запросе
			foreach($_POST as $param=>$value)
			{
				if (stripos($param, 'join') === FALSE)
				{
					continue;
				}
				
				$odetail_id = str_ireplace('join', '', $param);

				if (!is_numeric($odetail_id))
				{	
					continue;
				}

				// находим товар
				$odetail = $this->Details->getClientOdetailById($odetail_id, $order->order_client);
				
				if (!$odetail)
				{
					throw new Exception('Невозможно объединить товары. Некоторые товары отсутствуют в товаре.');
				}
				
				// вычисляем суммарную стоимость
				$joint->odetail_joint_cost += $odetail->odetail_pricedelivery;
				$joint->odetail_joint_count++;
				
				// удаляем старые джоинты
				if ($odetail->odetail_joint_id)
				{
					$this->Details->clearJoints($odetail->odetail_joint_id);
				}
				
				// сохраняем товар
				$odetail->odetail_joint_id = $joint->odetail_joint_id;
			
				$this->Details->addOdetail($odetail);
			}

			if ($joint->odetail_joint_count < 2)
			{
				throw new Exception('Выберите хотя бы 2 товара для объединения.');
			}

			$this->Joints->addOdetailJoint($joint);
			
			// закрываем транзакцию
			if ($this->db->trans_status() !== FALSE)
			{
				$this->db->trans_commit();
			}
			
			$this->result->e = 1;
			$this->result->m = 'Местная доставка выбранных товаров успешно объединена.';
			$this->result->join_status = 1;
		}
		catch (Exception $e) 
		{
			$this->db->trans_rollback();

			$this->result->join_status = -1;
			$this->result->e = -1;
			$this->result->m = $e->getMessage();
		}

		Stack::push('result', $this->result);
		
		// открываем детали заказа
		if (isset($order) && $order)
		{
			Func::redirect(BASEURL."{$this->cname}/showOrderDetails/{$order->order_id}");
		}
		else
		{
			Func::redirect(BASEURL.$this->cname);
		}
	}
	
	protected function removeOdetailJoint($order_id, $odetail_joint_id)
	{
		try
		{
			if (!is_numeric($odetail_joint_id) ||
				!is_numeric($order_id))
			{
				throw new Exception('Доступ запрещен.');
			}
		
			// безопасность: проверяем связку менеджера и заказа
			$order = $this->getPrivilegedOrder(
				$order_id, 
				'Невозможно отменить объединение товаров. Указанный заказ не найден.');

			$this->load->model('OdetailModel', 'Details');

			// погнали
			$this->db->trans_begin();
			
			$this->Details->clearJoints($odetail_joint_id);

			// закрываем транзакцию
			if ($this->db->trans_status() !== FALSE)
			{
				$this->db->trans_commit();
			}
			
			$this->result->e = 1;
			$this->result->m = 'Объединение местной доставки успешно отменено.';
			$this->result->join_status = 1;
		}
		catch (Exception $e) 
		{
			$this->db->trans_rollback();

			$this->result->join_status = -1;
			$this->result->e = -1;
			$this->result->m = $e->getMessage();
		}

		Stack::push('result', $this->result);
		
		// открываем детали заказа
		if (isset($order) && $order)
		{
			Func::redirect(BASEURL."{$this->cname}/showOrderDetails/{$order->order_id}");
		}
		else
		{
			Func::redirect(BASEURL.$this->cname);
		}
	}
	
	public function updatePerPage($per_page)
	{
		if (!is_numeric($per_page) ||
			!$this->user)
		{
			throw new Exception('Доступ запрещен.');
		}
	
		$this->session->set_userdata(array('new_packages_per_page' => $per_page));
		Func::redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function updatePaymentsPerPage($per_page)
	{
		if (!is_numeric($per_page) ||
			!$this->user)
		{
			throw new Exception('Доступ запрещен.');
		}
	
		$this->session->set_userdata(array('payments_per_page' => $per_page));
		Func::redirect($_SERVER['HTTP_REFERER']);
	}
	
	protected function deleteProduct($odid)
	{
		try
		{
			/*
			if (!$this->user ||
				!$this->user->user_id ||
				!is_numeric($odid))
			{
				throw new Exception('Доступ запрещен.');
			}
			*/
			// безопасность: проверяем связку клиента и товара
			$this->load->model('OdetailModel', 'ODetails');
			$this->load->model('OdetailJointModel', 'Joints');
				
			if (empty($this->user))
			{
				$odetail = $this->ODetails->getById($odid);
			}
			else if ($this->user->user_group == 'client')
			{
				$odetail = $this->ODetails->getClientOdetailById($odid, $this->user->user_id);
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
			$order = $this->getPrivilegedOrder(
				$odetail->odetail_order, 
				'Невозможно изменить статусы товаров. Заказ не найден.');

			// сохранение результатов
			if ($this->user->user_group == 'client')
			{
				OdetailModel::markUpdatedByClient($order, $detail, $this->getOrderModel());
			}
			
			$odetail->odetail_status = 'deleted';
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
				if ($joint->odetail_joint_count < 3)
				{
					$this->ODetails->clearJoints($joint->odetail_joint_id);
				}
				// или правим ее
				else
				{
					$joint->odetail_joint_count = $joint->odetail_joint_count - 1;
					$this->Joints->addOdetailJoint($joint);
				}
			}
			
			$deleted_odetail = $this->ODetails->addOdetail($odetail);
			
			if ( ! $deleted_odetail)
			{
				throw new Exception('Товар не удален. Попробуйте еще раз.');
			}

			$is_order_deleted = TRUE;
/*			
			// меняем статус заказа
			if ( ! $this->ODetails->getOrderDetails($order->order_id))
			{
				$order->order_status = 'deleted';
				$is_order_deleted = TRUE;
			}
			else
			{
				$status = $this->ODetails->getTotalStatus($order->order_id);
				
				if (!$status)
				{
					throw new Exception('Невозможно изменить статус заказа. Попоробуйте еще раз.');
				}
				
				$recent_status = $order->order_status;
				
				if ($recent_status != 'payed' && $recent_status != 'sent')
				{
					$order->order_status = $this->Orders->calculateOrderStatus($status);
					$is_new_status = ($recent_status != $order->order_status);
					$status_caption = $this->Orders->getOrderStatusDescription($order->order_status);
				}
			}
			
			// пересчитываем стоимость заказа
			$this->load->model('ConfigModel', 'Config');
			
			if (!$this->Orders->recalculate($order, $this->ODetails, $this->Joints, $this->Config))
			{
				throw new Exception('Невожможно пересчитать стоимость заказа. Попоробуйте еще раз.');
			}
			
			// сохраняем изменения в заказе
			$new_order = $this->Orders->saveOrder($order);
			
			if (!$new_order)
			{
				throw new Exception('Невожможно изменить статус заказа. Попоробуйте еще раз.');
			}
*/
			$this->db->trans_commit();
			
			// уведомления, только если остались товары в заказе
			if (isset($is_new_status) && $is_new_status && !isset($is_order_deleted))
			{
				$this->load->model('ManagerModel', 'Managers');
				$this->load->model('UserModel', 'Users');
				$this->load->model('ClientModel', 'Clients');

				/*
				Mailer::sendAdminNotification(
					Mailer::SUBJECT_NEW_ORDER_STATUS, 
					Mailer::NEW_ORDER_STATUS_NOTIFICATION,
					0,
					$order->order_id, 
					0,
					"http://countrypost.ru/admin/showOrderDetails/{$order->order_id}",
					NULL,
					NULL,
					$status_caption);

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

				Mailer::sendClientNotification(
					Mailer::SUBJECT_NEW_ORDER_STATUS, 
					Mailer::NEW_ORDER_STATUS_NOTIFICATION,
					$order->order_id, 
					$order->order_client, 
					"http://countrypost.ru/client/showOrderDetails/{$order->order_id}",
					$this->Clients,
					$status_caption);
				*/
			}
		}
		catch (Exception $e)
		{
			$this->db->trans_rollback();
			
			$this->result->e = -1;			
			$this->result->m = $e->getMessage();
		}
		
		// открываем заказы
		Stack::push('result', $this->result);
		$this->result->e = 1;
		$this->result->join_status = 1;

		if (isset($is_order_deleted))
		{
			// уведомления, удаленный заказ
			$this->load->model('ManagerModel', 'Managers');
			$this->load->model('UserModel', 'Users');
			$this->load->model('ClientModel', 'Clients');

			Mailer::sendAdminNotification(
				Mailer::SUBJECT_ORDER_DELETED_STATUS, 
				Mailer::ORDER_DELETED_NOTIFICATION,
				0,
				$order->order_id, 
				0,
				NULL,
				NULL,
				NULL);

			Mailer::sendManagerNotification(
				Mailer::SUBJECT_ORDER_DELETED_STATUS, 
				Mailer::ORDER_DELETED_NOTIFICATION,
				$order->order_manager, 
				$order->order_id, 
				0,
				NULL,
				$this->Managers,
				NULL);

			Mailer::sendClientNotification(
				Mailer::SUBJECT_ORDER_DELETED_STATUS, 
				Mailer::ORDER_DELETED_NOTIFICATION,
				$order->order_id, 
				$order->order_client, 
				NULL,
				$this->Clients);
			
			$this->result->m = 'Заказ успешно удален.';
			Func::redirect(BASEURL.$this->cname.'/showOpenOrders');
		}
		else
		{
			$this->result->m = 'Товар успешно удален.';
			Func::redirect($_SERVER['HTTP_REFERER']);
		}
	}
	
	protected function deleteProductP($pdid)
	{
		try
		{
			if ( ! $this->user ||
				! $this->user->user_id ||
				! is_numeric($pdid))
			{
				throw new Exception('Доступ запрещен.');
			}
			
			// безопасность: проверяем связку клиента и товара
			$this->load->model('PdetailModel', 'PDetails');
						
			if ($this->user->user_group == 'client')
			{
				$pdetail = $this->PDetails->getFilteredDetails(array('pdetail_id'=>$pdid, 'pdetail_client'=>$this->user->user_id), TRUE);
			}
			else if ($this->user->user_group == 'manager')
			{
				$pdetail = $this->PDetails->getFilteredDetails(array('pdetail_id'=>$pdid, 'pdetail_manager'=>$this->user->user_id), TRUE);
			}
			else if ($this->user->user_group == 'admin')
			{
				$pdetail = $this->PDetails->getById($pdid);
			}
			
			if (is_array($pdetail) AND
				count($pdetail))
			{
				$pdetail = $pdetail[0];
			}
			
			if (empty($pdetail))
			{
				throw new Exception('Товар не найден. Попробуйте еще раз.');
			}

			// находим посылку
			$this->load->model('PackageModel', 'Packages');

			if ($this->user->user_group == 'client')
			{
				$package = $this->Packages->getClientPackageById($pdetail->pdetail_package, $this->user->user_id);
			}
			else if ($this->user->user_group == 'manager')
			{
				$package = $this->Packages->getManagerPackageById($pdetail->pdetail_package, $this->user->user_id);
			}
			else if ($this->user->user_group == 'admin')
			{
				$package = $this->Packages->getById($pdetail->pdetail_package);
			}
			
			if (empty($package))
			{
				throw new Exception('Невозможно изменить статусы товаров. Посылка не найдена.');
			}

			// сохранение результатов
			$pdetail->pdetail_status = 'deleted';
			
			//$this->db->trans_begin();
			
			// удаляем товар из объединенной доставки
			$this->load->model('PdetailJointModel', 'Joints');
			
			if ( ! empty($pdetail->pdetail_joint_id))
			{
				$joint = $this->Joints->getById($pdetail->pdetail_joint_id);
				
				if ( ! empty($joint))
				{
					$pdetail->pdetail_joint_id = 0;
					$joint->pdetail_joint_count = intval($joint->pdetail_joint_count) - 1;
					$this->Joints->saveJoint($joint);					
				}
			}

			// сохраняем результат
			$deleted_pdetail = $this->PDetails->updatepdetail($pdetail);

			if ( ! $deleted_pdetail)
			{
				throw new Exception('Товар не удален. Попробуйте еще раз.');
			}

			// пересчитываем статус и стоимость посылки
			if ($package) 
			{
				$recent_status = $package->package_status;
				$package = $this->Packages->recalculatePackage($package);
				$status = $package->package_status;
				
				$is_new_status = ($recent_status != $package->package_status);
				$status_caption = $this->Packages->getPackageStatusDescription($package->package_status);
			}
			
			//$this->db->trans_commit();
			
			// уведомления, только если остались товары в посылке
			if (isset($is_new_status) AND 
				$is_new_status)
			{
				$this->load->model('ManagerModel', 'Managers');
				$this->load->model('UserModel', 'Users');
				$this->load->model('ClientModel', 'Clients');

				Mailer::sendAdminNotification(
					Mailer::SUBJECT_NEW_PACKAGE_STATUS, 
					Mailer::NEW_PACKAGE_STATUS_NOTIFICATION,
					0,
					$package->package_id, 
					0,
					"http://countrypost.ru/admin/showPackageDetails/{$package->package_id}",
					NULL,
					NULL,
					$status_caption);

				Mailer::sendManagerNotification(
					Mailer::SUBJECT_NEW_PACKAGE_STATUS, 
					Mailer::NEW_PACKAGE_STATUS_NOTIFICATION,
					$package->package_manager, 
					$package->package_id, 
					0,
					"http://countrypost.ru/manager/showPackageDetails/{$package->package_id}",
					$this->Managers,
					NULL,
					$status_caption);

				Mailer::sendClientNotification(
					Mailer::SUBJECT_NEW_PACKAGE_STATUS, 
					Mailer::NEW_PACKAGE_STATUS_NOTIFICATION,
					$package->package_id, 
					$package->package_client, 
					"http://countrypost.ru/client/showOrderDetails/{$package->package_id}",
					$this->Clients,
					$status_caption);
			}
		}
		catch (Exception $e)
		{
			$this->db->trans_rollback();
			
			$this->result->e = -1;			
			$this->result->m = $e->getMessage();
			
			die();
		}
		
		Stack::push('result', $this->result);
		$this->result->e = 1;
		$this->result->join_status = 1;

		$this->result->m = 'Товар успешно удален.';
		Func::redirect($_SERVER['HTTP_REFERER']);
	}
	
	protected function updateProductAjax() 
	{
		Check::reset_empties();
		$detail									= new stdClass();
		$detail->odetail_id						= Check::int('odetail_id');
		$empties								= Check::get_empties();		
		if ($empties)
		{
			throw new Exception('Товар не найден.');
		}

		$view['odetail_id'] = $detail->odetail_id;
		
		// находим товар
		$this->load->model('OdetailModel', 'OdetailModel');
		
		if ($this->user->user_group == 'client')
		{
			$detail = $this->OdetailModel->getClientOdetailById($detail->odetail_id, $this->user->user_id);
		}
		else if ($this->user->user_group == 'manager')
		{
			$detail = $this->OdetailModel->getManagerOdetailById($detail->odetail_id, $this->user->user_id);
		}
		else if ($this->user->user_group == 'admin')
		{
			$detail = $this->OdetailModel->getById($detail->odetail_id);
		}
		
		if (!$detail)
		{
			throw new Exception('Товар не найден.');
		}

		// безопасность: проверяем связку товара и заказа
		$order = $this->getPrivilegedOrder(
			$detail->odetail_order, 
			'Заказ не найден.');
		
		// парсим пользовательский ввод
		Check::reset_empties();
		$detail->odetail_link					= Check::str('olink'.$detail->odetail_id, 500, 1);
		$detail->odetail_shop_name				= Check::str('shop'.$detail->odetail_id, 255, 0);
		$detail->odetail_product_name			= Check::str('oname'.$detail->odetail_id, 255, 0);
		$detail->odetail_product_color			= Check::str('ocolor'.$detail->odetail_id, 32, 0);
		$detail->odetail_product_size			= Check::str('osize'.$detail->odetail_id, 32, 0);
		$detail->odetail_foto_requested			= Check::chkbox('foto_requested');
		
		$detail->odetail_product_amount			= (isset($_POST['oamount'.$detail->odetail_id]) &&
													$_POST['oamount'.$detail->odetail_id] &&
													is_numeric($_POST['oamount'.$detail->odetail_id])) ? 
													$_POST['oamount'.$detail->odetail_id] :
													1;
		$is_img_changed							= isset($_POST['img'.$detail->odetail_id]) ? $_POST['img'.$detail->odetail_id] : FALSE;
		
		if ($this->user->user_group == 'client')
		{
			OdetailModel::markUpdatedByClient($order, $detail, $this->getOrderModel());
		}
		
		if ($is_img_changed)
		{
			if ($is_img_changed == 2)
			{
				$userfile = isset($_FILES['userfile']) && !$_FILES['userfile']['error'];
				$detail->odetail_img = NULL;
			}
			else
			{
				$userfile = FALSE;
				$detail->odetail_img = Check::str('userfileimg'.$detail->odetail_id, 500, 1);
			}
		}
		
		$empties = Check::get_empties();		
		
		try 
		{
			// обязательны для заполнения:
			// olink
			// userfileimg либо клиентская картинка
			if ($detail->odetail_link === FALSE)
			{
				throw new Exception('Добавьте ссылку на товар.');
			}
				
			if ($empties &&
				$is_img_changed)
			{
				if (isset($_FILES['userfile']) && 
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

			$this->OdetailModel->updateOdetail($detail);

			// если заказ уже создан, меняем его статус
			// только у клиента!
			if ($this->user->user_group == 'client')
			{
				// вычисляем общий статус товаров
				$status = $this->OdetailModel->getTotalStatus($detail->odetail_order);
				
				if (!$status)
				{
					throw new Exception('Статус заказа не определен. Попоробуйте еще раз.');
				}

				$recent_status = $order->order_status;
				$order->order_status = $this->Orders->calculateOrderStatus($status);
				$is_new_status = ($recent_status != $order->order_status && $recent_status != 'payed');
				
				if ($is_new_status)
				{
					$status_caption = $this->Orders->getOrderStatusDescription($order->order_status);
					
					// меняем статус заказа
					$new_order = $this->Orders->saveOrder($order);
					
					if (!$new_order)
					{
						throw new Exception('Невожможно изменить статус заказа. Попоробуйте еще раз.');
					}
				}
			}
			
 			// загружаем файл
			if (isset($userfile) && $userfile)
			{
				$old = umask(0);
				// загрузка файла
				if (!is_dir($_SERVER['DOCUMENT_ROOT']."/upload/orders/$client_id")){
					mkdir($_SERVER['DOCUMENT_ROOT']."/upload/orders/$client_id",0777);
				}

				$config['upload_path']			= $_SERVER['DOCUMENT_ROOT']."/upload/orders/$client_id";
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
				
				// на сервере - '/upload/orders/'
				$filename = $_SERVER['DOCUMENT_ROOT']."/upload/orders/$client_id/{$detail->odetail_id}.jpg";
				
				if (file_exists($filename))
				{
					unlink($filename);
				}
				
				if (!rename($uploadedImg['full_path'], $filename))
				{
					throw new Exception("Bad file name!");
				}
				
				$imageInfo = getimagesize($filename);
				
				if ($imageInfo[0]>$max_width || $imageInfo[1]>$max_height){
					
					$config['image_library']	= 'gd2';
					$config['source_image']		= $filename;
					$config['maintain_ratio']	= TRUE;
					$config['width']			= $max_width;
					$config['height']			= $max_height;
					
					$this->load->library('image_lib', $config); // загружаем библиотеку
					
					$this->image_lib->resize(); // и вызываем функцию
				}
			}
			
			// закрываем транзакцию
			$this->db->trans_commit();
			
			// уведомления
			if (isset($is_new_status) && $is_new_status)
			{
				$this->load->model('ManagerModel', 'Managers');
				$this->load->model('UserModel', 'Users');
				$this->load->model('ClientModel', 'Clients');

				Mailer::sendAdminNotification(
					Mailer::SUBJECT_NEW_ORDER_STATUS, 
					Mailer::NEW_ORDER_STATUS_NOTIFICATION,
					0,
					$order->order_id, 
					0,
					"http://countrypost.ru/admin/showOrderDetails/{$order->order_id}",
					NULL,
					NULL,
					$status_caption);

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

				Mailer::sendClientNotification(
					Mailer::SUBJECT_NEW_ORDER_STATUS, 
					Mailer::NEW_ORDER_STATUS_NOTIFICATION,
					$order->order_id, 
					$order->order_client, 
					"http://countrypost.ru/client/showOrderDetails/{$order->order_id}",
					$this->Clients,
					$status_caption);
					
				$view['new_order_status'] = $status_caption;
			}
			
			// парсим шаблон
			$detail->status_name = $this->OdetailModel->getOrderDetailsStatusDescription($detail->odetail_status);
			$view['odetail'] = $detail;
		}
		catch (Exception $e)
		{
			$view['error'] = $e->getMessage();
		}
		
		$view['selfurl'] = BASEURL.$this->cname.'/';
		$view['viewpath'] = $this->viewpath;
		$this->load->view($this->viewpath."ajax/showProduct", $view);
	}

	public function updateProductAjaxP() 
	{
		try 
		{
			Check::reset_empties();
			$detail									= new stdClass();
			$detail->pdetail_id						= Check::int('pdetail_id');
			$empties								= Check::get_empties();		

			if ($empties)
			{
				throw new Exception('Товар не найден.');
			}

			$view['pdetail_id'] = $detail->pdetail_id;
			
			// находим товар
			$this->load->model('PdetailModel', 'PdetailModel');
			
			if ($this->user->user_group == 'client')
			{
				$detail = $this->PdetailModel->getClientPdetailById($detail->pdetail_id, $this->user->user_id);
			}
			else if ($this->user->user_group == 'manager')
			{
				$detail = $this->PdetailModel->getManagerPdetailById($detail->pdetail_id, $this->user->user_id);
			}
			else if ($this->user->user_group == 'admin')
			{
				$detail = $this->PdetailModel->getById($detail->pdetail_id);
			}
			
			if (empty($detail))
			{
				throw new Exception('Товар не найден.');
			}
			
			// находим посылку и ее клиента
			$this->load->model('PackageModel', 'Packages');

			if ($this->user->user_group == 'client')
			{
				$package = $this->Packages->getClientPackageById($detail->pdetail_package, $this->user->user_id);
			}
			else if ($this->user->user_group == 'manager')
			{
				$package = $this->Packages->getManagerPackageById($detail->pdetail_package, $this->user->user_id);
			}
			else if ($this->user->user_group == 'admin')
			{
				$package = $this->Packages->getById($detail->pdetail_package);
			}
			
			if ( ! $package)
			{
				throw new Exception('Посылка не найдена.');
			}		
				
			// парсим пользовательский ввод
			Check::reset_empties();
			
			$detail->pdetail_link					= Check::str('olink'.$detail->pdetail_id, 500, 1);
			$detail->pdetail_product_name			= Check::str('oname'.$detail->pdetail_id, 255, 0);
			$detail->pdetail_product_color			= Check::str('ocolor'.$detail->pdetail_id, 32, 0);
			$detail->pdetail_product_size			= Check::str('osize'.$detail->pdetail_id, 32, 0);		
			$detail->pdetail_product_amount			= (isset($_POST['oamount'.$detail->pdetail_id]) &&
														$_POST['oamount'.$detail->pdetail_id] &&
														is_numeric($_POST['oamount'.$detail->pdetail_id])) ? 
														$_POST['oamount'.$detail->pdetail_id] :
														1;
														
			// 13.	Доп. услуги сейчас заблокированы. http://clip2net.com/s/1zpya Разблокируются только, если нажать редактировать товар. Нужно чтобы:
			// •	в акк клиента они были разблокированы (для статусов Ждем прибытия, Получено, Не оплачено). Для статусов посылки Оплачен, Отправлен заблокировано.
			// •	в акк партнера всегда заблокировано, но отображается что было выбрано
			// •	в акк админа всегда разблокировано и редактируемо.
			if ($this->user->user_group == 'admin' OR 
				($this->user->user_group == 'client' AND 
					($package->package_status == 'processing' OR
					$package->package_status == 'delivered' OR
					$package->package_status == 'not_delivered')))
			{
				$detail->pdetail_special_boxes			=  Check::chkbox('pdetail_special_boxes'.$detail->pdetail_id);
				$detail->pdetail_special_invoices  		=  Check::chkbox('pdetail_special_invoices'.$detail->pdetail_id);
			}
			
			if (($this->user->user_group == 'admin' OR 
				$this->user->user_group == 'manager') AND
				! empty($_POST['pdetail_status'.$detail->pdetail_id]))
			{
				$detail->pdetail_status = Check::str('pdetail_status'.$detail->pdetail_id, 32, 0);
			}
														
			$is_img_changed = empty($_POST['img'.$detail->pdetail_id]) ? FALSE : $_POST['img'.$detail->pdetail_id];
			
			if ($is_img_changed)
			{
				if ($is_img_changed == 2)
				{
					$userfile = isset($_FILES['userfile']) && !$_FILES['userfile']['error'];
					$detail->pdetail_img = NULL;
				}
				else
				{
					$userfile = FALSE;
					$detail->pdetail_img = Check::str('userfileimg'.$detail->pdetail_id, 500, 1);
				}
			}
			
			$empties = Check::get_empties();		
		
			// обязательны для заполнения:
			// olink
			// userfileimg либо клиентская картинка
			if ($detail->pdetail_link === FALSE)
			{
				throw new Exception('Добавьте ссылку на товар.');
			}
				
			if ($empties AND
				$is_img_changed)
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
			
			$client_id = $package->package_client;
			
			// открываем транзакцию
			$this->db->trans_begin();	
			$this->PdetailModel->updatePdetail($detail);
			
			// пересчитываем посылку
			$recent_status = $package->package_status;
			$package = $this->Packages->recalculatePackage($package);
			
			$status = $package->package_status;
			$is_new_status = ($recent_status != $status);
			
			if ($is_new_status)
			{
				$status_caption = $this->Packages->getPackageStatusDescription($package->package_status);
			}
			
 			// загружаем файл
			if (isset($userfile) && $userfile)
			{
				$old = umask(0);
				// загрузка файла
				if (!is_dir($_SERVER['DOCUMENT_ROOT']."/upload/packages/{$detail->pdetail_package}"))
				{
					mkdir($_SERVER['DOCUMENT_ROOT']."/upload/packages/{$detail->pdetail_package}",0777);
				}

				$config['upload_path']			= $_SERVER['DOCUMENT_ROOT']."/upload/packages/{$detail->pdetail_package}";
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
				$filename = $_SERVER['DOCUMENT_ROOT']."/upload/packages/{$detail->pdetail_package}/{$detail->pdetail_id}.jpg";
				
				if (file_exists($filename))
				{
					unlink($filename);
				}
				
				if ( ! rename($uploadedImg['full_path'], $filename))
				{
					throw new Exception("Bad file name!");
				}
				
				$imageInfo = getimagesize($filename);
				
				if ($imageInfo[0]>$max_width || $imageInfo[1]>$max_height){
					
					$config['image_library']	= 'gd2';
					$config['source_image']		= $filename;
					$config['maintain_ratio']	= TRUE;
					$config['width']			= $max_width;
					$config['height']			= $max_height;
					
					$this->load->library('image_lib', $config); // загружаем библиотеку
					
					$this->image_lib->resize(); // и вызываем функцию
				}
			}
			
			// закрываем транзакцию
			$this->db->trans_commit();
			
			// уведомления
			if (isset($is_new_status) && $is_new_status)
			{
				$view['new_package_status'] = $status_caption;
			}
						
			// парсим шаблон
			$view['pdetail'] = $detail;
			$view['package'] = $package;
		}
		catch (Exception $e)
		{
			$view['error'] = $e->getMessage();
		}
		
		$view['selfurl'] = BASEURL.$this->cname.'/';
		$view['viewpath'] = $this->viewpath;
		$view['pdetails_statuses'] = $this->PdetailModel->getStatuses();
		$this->load->view($this->viewpath."ajax/showProductP", $view);
	}
	
	protected function refundPackage()
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
			$this->load->model('PackageModel', 'Packages');
			
			if ($this->user->user_group == 'admin')
			{
				$package = $this->Packages->getById($this->uri->segment(3));
			}
			else if ($this->user->user_group == 'manager')
			{
				$package = $this->Packages->getManagerPackageById($this->uri->segment(3), $this->user->user_id);
			}

			if (!$package)
			{
				throw new Exception('Посылка не найдена. Попробуйте еще раз.');
			}			


			if (!$package)
			{
				throw new Exception('Посылка не найдена. Попробуйте еще раз.');
			}			

			// находим местную валюту
			$this->load->model('CurrencyModel', 'Currency');
			$currency = $this->Currency->getCurrencyByCountry($package->package_country_from);
			
			// добавление платежа партнера клиенту
			$payment_manager = new stdClass();
			$payment_manager->payment_from				= $package->package_manager;
			$payment_manager->payment_to				= $package->package_client;
			$payment_manager->payment_amount_from		= $package->package_manager_cost_payed - $package->package_manager_cost;
			$payment_manager->payment_amount_to			= $package->package_manager_cost_payed - $package->package_manager_cost;
			$payment_manager->payment_amount_tax		= $package->package_manager_comission_payed - $package->package_manager_comission;
			$payment_manager->payment_purpose			= 'возмещение недоставленных посылок';
			$payment_manager->payment_comment			= '№ '.$package->package_id;
			$payment_manager->payment_type				= 'package';
			$payment_manager->payment_transfer_package_id	= $this->user->user_id.date('Y').date('m').date('d').date('h').date('i').date('s');

			// платеж партнеру в местной валюте
			$payment_manager_local = new stdClass();
			$sub_local_money = ($package->package_manager_cost_payed_local - $package->package_manager_cost_local >= 0);
						
			if ($sub_local_money)
			{
				$payment_manager_local->payment_from		= $package->package_manager;
				$payment_manager_local->payment_to			= $package->package_client;
				$payment_manager_local->payment_amount_from	= $package->package_manager_cost_payed_local - $package->package_manager_cost_local;
				$payment_manager_local->payment_amount_to	= 0;
				$payment_manager_local->payment_amount_tax	= $package->package_manager_comission_payed_local - $package->package_manager_comission_local;
			}
			else
			{
				$payment_manager_local->payment_from		= $package->package_client;
				$payment_manager_local->payment_to			= $package->package_manager;
				$payment_manager_local->payment_amount_from	= 0;
				$payment_manager_local->payment_amount_to	= $package->package_manager_cost_local - $package->package_manager_cost_payed_local;
				$payment_manager_local->payment_amount_tax	= $package->package_manager_comission_local - $package->package_manager_comission_payed_local;
			}
			
			$payment_manager_local->payment_tax			= 0;
			$payment_manager_local->payment_purpose		= 'возмещение недоставленных посылок в местной валюте';
			$payment_manager_local->payment_comment		= '№ '.$package->package_id;
			$payment_manager_local->payment_type		= 'package';
			$payment_manager_local->payment_currency	= $currency->currency_symbol;
			$payment_manager_local->payment_transfer_order_id	= '';
			
			// платеж системе
			$payment_system = new stdClass();
			$payment_system->payment_from				= 1;
			$payment_system->payment_to					= $package->package_client;
			$payment_system->payment_amount_from		= $package->package_system_comission_payed - $package->package_system_comission;
			$payment_system->payment_amount_to			= $package->package_system_comission_payed - $package->package_system_comission;
			$payment_system->payment_amount_tax			= $package->package_system_comission_payed - $package->package_system_comission;
			$payment_system->payment_purpose			= 'возмещение недоставленных посылок';
			$payment_system->payment_comment			= '№ '.$package->package_id;
			$payment_system->payment_type				= 'package';
			$payment_system->payment_transfer_order_id	= '';

			$this->load->model('PaymentModel', 'Payment');
			
			// погнали
			$this->db->trans_begin();

			if (!$this->Payment->makePayment($payment_manager, TRUE) ||
				!$this->Payment->makePayment($payment_system, TRUE) ||
				!$this->Payment->makePaymentLocal($payment_manager_local, TRUE)) 
			{
				throw new Exception('Ошибка возмещения средств. Попробуйте еще раз.');
			}			
			
			// сохраняем данные об оплате
			$package->package_cost_payed = $package->package_cost;
			$package->package_manager_comission_payed = $package->package_manager_comission;
			$package->package_manager_cost_payed = $package->package_manager_cost;
			$package->package_system_comission_payed = $package->package_system_comission;
			$package->package_manager_cost_payed_local = $package->package_manager_cost_local;
			$package->package_manager_comission_payed_local = $package->package_manager_comission_local;

			$payed_package = $this->Packages->savePackage($package);
			
			if ($this->db->trans_status() !== FALSE)
			{
				$this->db->trans_commit();
			}

			// меняем баланс в сессии
			if ($this->user->user_group == 'admin')
			{
				$this->session->set_userdata(array('user_coints' => ($this->user->user_coints - $payment_system->payment_amount_from)));
			}
			else if ($this->user->user_group == 'manager')
			{
				$this->session->set_userdata(array('user_coints' => ($this->user->user_coints - $payment_manager->payment_amount_from)));
				$this->session->set_userdata(array('manager_balance_local' => ($this->session->userdata('manager_balance_local') - $payment_manager_local->payment_amount_from)));
			}
			
			$this->result->m = 'Стоимость посылки успешно возмещена клиенту.';
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
	
	private static function getPackageBackHandler($package, $user_group)
	{
		switch ($package->package_status) 
		{
			case 'sent' : return 'showSentPackages';
			case 'payed' : return 'showPayedPackages';
			default : 
			{
				switch ($user_group) 
				{
					case 'client' : return 'showOpenPackages';
					case 'admin' : return 'showNewPackages';
					case 'manager' : return 'showNewPackages';
				}
			}
		}
		
		return '';
	}

	private static function getOrderBackHandler($order, $user_group)
	{
		switch ($order->order_status) 
		{
			case 'sended' : return 'showSentOrders';
			case 'payed' : return 'showPayedOrders';
			default : return 'showOpenOrders';
		}
		
		return '';
	}
	
	private function putStatistics(&$view)
	{
		$this->load->model('OrderModel', 'Orders');
		$this->load->model('PackageModel', 'Packages');
		
		$view['new_packages'] = $this->Packages->getPackages(NULL, 'open', $this->user->user_id, NULL);
		$view['payed_packages'] = $this->Packages->getPackages(NULL, 'payed', $this->user->user_id, NULL);
		$view['sent_packages'] = $this->Packages->getPackages(NULL, 'sent', $this->user->user_id, NULL);

		$view['new_orders'] = $this->Orders->getOrders(NULL, 'open', $this->user->user_id, NULL, TRUE);
		$view['payed_orders'] = $this->Orders->getOrders(NULL, 'payed', $this->user->user_id, NULL, FALSE);
		$view['sent_orders'] = $this->Orders->getOrders(NULL, 'sended', $this->user->user_id, NULL, FALSE);
		
		$view['new_packages'] = $view['new_packages'] ? count($view['new_packages']) : 0;
		$view['payed_packages'] = $view['payed_packages'] ? count($view['payed_packages']) : 0;
		$view['sent_packages'] = $view['sent_packages'] ? count($view['sent_packages']) : 0;
		$view['new_orders'] = $view['new_orders'] ? count($view['new_orders']) : 0;
		$view['payed_orders'] = $view['payed_orders'] ? count($view['payed_orders']) : 0;
		$view['sent_orders'] = $view['sent_orders'] ? count($view['sent_orders']) : 0;
	}
	
	protected function joinPackageFotos()
	{
		try
		{
			$this->load->model('PackageModel', 'Packages');
			$this->load->model('PdetailModel', 'Pdetails');
			$this->load->model('PdetailJointModel', 'Joints');

			// итерируем по посылкам
			$joined_pdetails = 0;
			$pdetail_ids = array();
			$joint = FALSE;
			
			// валидация
			Check::reset_empties();
			$package_id = Check::int('package_id');
			if (Check::get_empties())
			{
				throw new Exception('Невозможно объединить фото посылок. Посылка не найдена.');
			}
			
			$this->db->trans_begin();
							
			foreach($_POST as $key=>$value)
			{
				if (stripos($key, 'pdetail_id') === 0) 
				{
					// находим посылку
					$pdetail_id = str_ireplace('pdetail_id', '', $key);
					
					if ( ! is_numeric($pdetail_id)) 
					{
						continue;
					}

					// роли и разграничение доступа
					if ($this->user->user_group == 'admin')
					{
						$pdetail = $this->Pdetails->getById($pdetail_id);
					}
					else if ($this->user->user_group == 'manager')
					{
						$pdetail = $this->Pdetails->getManagerpdetailById($pdetail_id, $this->user->user_id);
					}
					else if ($this->user->user_group == 'client')
					{
						$pdetail = $this->Pdetails->getClientpdetailById($pdetail_id, $this->user->user_id);
					}

					if (empty($pdetail))
					{
						throw new Exception('Невозможно объединить фото товаров. Некоторые товары не найдены.');
					}

					// генерим объединенное фото
					if (empty($joint))
					{
						$joint = $this->Joints->generate($package_id);

						if (empty($joint))
						{
							throw new Exception('Невозможно объединить фото товаров. Попробуйте еще раз.');
						}
					}
					
					// убираем товар из предыдущего объединения
					if ( ! empty($pdetail->pdetail_joint_id))
					{
						$prevJoint = $this->Joints->getById($pdetail->pdetail_joint_id);
						
						if ( ! empty($prevJoint))
						{
							$prevJoint->pdetail_joint_count = intval($prevJoint->pdetail_joint_count) - 1;
							$this->Joints->saveJoint($prevJoint);					
						}
					}
					
					// сохраняем товар
					$pdetail->pdetail_joint_id = $joint->pdetail_joint_id;
					$this->Pdetails->updatepdetail($pdetail);
					$joined_pdetails++;
				}
			}
			
			// счетчик товаров
			$joint->pdetail_joint_count = $joined_pdetails;
			$this->Joints->saveJoint($joint);
			
			// зачистка: сносим объединения в которых не осталось товаров
			$this->Joints->cleanupJoints($package_id);
			
			// вычисляем статус и стоимость объединенной посылки
			if ($this->user->user_group == 'admin')
			{
			    $package = $this->Packages->getById($package_id);
			}
			else if ($this->user->user_group == 'manager')
			{
				$package = $this->Packages->getManagerPackageById($package_id, $this->user->user_id);
			}
			else if ($this->user->user_group == 'client')
			{
				$package = $this->Packages->getClientPackageById($package_id, $this->user->user_id);
			}
			
			if (empty($package))
			{
				throw new Exception('Невозможно вычислить стоимость посылки. Попробуйте еще раз.');
			}
			
			$this->Packages->recalculatePackage($package);
			
			// закрываем транзакцию
			$this->db->trans_commit();
		
			$this->result->m = "Фото товаров успешно объединены.";
			$this->result->e = 1;
			Stack::push('result', $this->result);
			
			// открываем посылки
			Func::redirect($_SERVER['HTTP_REFERER']);
		}
		catch (Exception $e) 
		{
			$this->db->trans_rollback();			
			$this->result->e = -1;
			$this->result->m = $e->getMessage();
			print($this->result->m);
			Stack::push('result', $this->result);
		}
	}
	
	protected function deletePdetailJoint($package_id, $pdetail_joint_id)
	{
		try
		{
			if (!$this->user ||
				!$this->user->user_id ||
				!isset($pdetail_joint_id) ||
				!is_numeric($pdetail_joint_id) ||
				!isset($package_id) ||
				!is_numeric($package_id))
			{
				throw new Exception('Доступ запрещен.');
			}
			
			// проверяем доступ к посылке
			$this->load->model('PackageModel', 'Packages');

			if ($this->user->user_group == 'admin')
			{
			    $package = $this->Packages->getById($package_id);
			}
			else if ($this->user->user_group == 'manager')
			{
				$package = $this->Packages->getManagerPackageById($package_id, $this->user->user_id);
			}
			else if ($this->user->user_group == 'client')
			{
				$package = $this->Packages->getClientPackageById($package_id, $this->user->user_id);
			}
			
			if (empty($package))
			{
				throw new Exception('Невозможно отменить объединение фото. Посылка не найдена.');
			}

			// находим объединие фото
			$this->load->model('PdetailJointModel', 'Joints');
			
			$joint = $this->Joints->getById($pdetail_joint_id);
			
			if (empty($joint) OR
				$joint->package_id != $package_id)
			{
				throw new Exception('Невозможно отменить объединение фото. Попробуйте еще раз.');
			}
			
			// погнали
			$this->Joints->deleteJoint($joint);
			
			$this->Packages->recalculatePackage($package);
		}
		catch (Exception $e)
		{
			$this->result->m = $e->getMessage();		
			Stack::push('result', $this->result);
		}
		
		Func::redirect($_SERVER['HTTP_REFERER']);
	}
	
	protected function connectOrderToManager($order_id, $manager_id, $skip_redirect = FALSE)
	{
		try 
		{
			// валидация
			$this->load->model('OrderModel', 'Orders');
			$order = $this->Orders->getById($order_id);
				
			if (empty($order) OR
				! empty($order->order_manager))
			{
				throw new Exception('Невозможно принять заказ на обработку. Заказ не найден.');
			}
			
			$this->load->model('ManagerModel', 'Manager');
			if ($this->Manager->isOrdersLimitReached($manager_id))
			{
				throw new Exception('Невозможно принять заказ на обработку. Достигнут максимум количества заказов.');
			}
			
			// привязываем заказ
			$order->order_manager = $manager_id;
			$this->Orders->addOrder($order);
			
			// привязываем товары
			$this->load->model('OdetailModel', 'Odetails');
			$odetails = $this->Odetails->getOrderDetails($order_id);
				
			if ( ! empty($odetails))
			{
				foreach ($odetails as $odetail)
				{
					$odetail->odetail_manager = $order->order_manager;
					$this->Odetails->addOdetail($odetail);
				}
			}
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
		
		if ( ! $skip_redirect)
		{
			Func::redirect($_SERVER['HTTP_REFERER']);
		}
	}

	private function getPrivilegedOrder($order_id, $validate = TRUE)
	{
		
		$model = $this->getOrderModel();
		
		if (empty($this->user))
		{
			$order = $model->getById($order_id);
			
			if ($order->order_manager OR
				$order->order_status != 'proccessing')
			{
				$order = FALSE;
			}
		}
		else
		{
			$user_id = $this->user->user_id;
			
			switch ($this->user->user_group)
			{
				case 'manager' : 
					$order = $model->getById($order_id);//$model->getManagerOrderById($order_id, $user_id);
					break;
				case 'client' : 
					$order = $model->getClientOrderById($order_id, $user_id);
					break;
				case 'admin' : 
					$order = $model->getById($order_id);
					break;
			}
		}
		
		if ($validate AND
			empty($order))
		{
			throw new Exception($validate);
		}
		
		return $order;
	}
	
	private function getPrivilegedBid($bid_id, $validate = TRUE)
	{
		$user_id = $this->user->user_id;
		$model = $this->Bids;
		
		switch ($this->user->user_group)
		{
			case 'manager' : 
				$bid = $model->getManagerBidById($bid_id, $user_id);
				break;
			case 'client' : 
				$bid = $model->getClientBidById($bid_id, $user_id);
				break;
			case 'admin' : 
				$bid = $model->getById($bid_id);
				break;
		}
		
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
		
		//print_r($personal_data->statistics);
		//print_r($comment);
					//print_r($bid);
		//die();
					
	}
}
?>