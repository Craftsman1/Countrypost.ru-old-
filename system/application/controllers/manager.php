<? require_once BASE_CONTROLLERS_PATH . 'BaseController' . EXT;

class Manager extends BaseController {

	function __construct()
	{
		parent::__construct();

		$user = Check::user();

		if (empty($user) OR $user->user_group !== 'manager')
		{
			Func::redirect(BASEURL);
		}

		Breadcrumb::setCrumb(array('/' => 'Главная'), 0);
		Breadcrumb::setCrumb(array('/manager/orders/' => 'Мои заказы'), 1, TRUE);
	}
	
	function index()
	{
		Func::redirect(BASEURL.$this->cname.'/orders');
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

	public function updateOrderStatus($param1, $param2, $order, $status)
	{
		parent::updateOrderStatus($order, $status);

		$view_status = $this->Orders->getViewStatus($this->user->user_group, $status);
		parent::showOrders($view_status);
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

	public function showScreen($oid = null)
	{
		header('Content-type: image/jpg');
		$this->load->model('OdetailModel', 'OdetailModel');
		if ($Detail = $this->OdetailModel->getInfo(array('odetail_id' => intval($oid))))
		{
			$order = $this->getPrivilegedOrder($Detail->odetail_order,
				'Заказ не найден. Попробуйте еще раз.'
			);

			readfile("{$_SERVER['DOCUMENT_ROOT']}/upload/orders/{$order->order_client}/$oid.jpg");
		}
		die();
	}

	public function filterOrders()
	{
		$this->filter('Orders', 'orders/0/ajax');
	}
	
	public function filterPaymentHistory()
	{
		$this->filter('paymentHistory', 'history');
	}

	public function history()
	{
		parent::showPaymentHistory();
	}

	public function addBid($order_id, $bid_id = 0)
	{
		try 
		{
			if (empty($order_id) OR
				! is_numeric($order_id))
			{				
				throw new Exception('Невозможно добавить предложение.');
			}
						
			// 1. роли и разграничение доступа: новое предложение можно добавлять только в публичный заказ,
			// а редактировать можно любой
			if (empty($bid_id) OR
				! is_numeric($bid_id))
			{
				$order = $this->getPublicOrder(
				$order_id,
				'Заказ не найден. Попробуйте еще раз.');
			}
			else if ( ! ($order = $this->getPublicOrder($order_id, FALSE)))
			{
				$order = $this->getPrivilegedOrder(
					$order_id,
					'Заказ не найден. Попробуйте еще раз.');
			}

			$this->getOrderFotoCount($order);

			// 2. погнали
			$this->load->model('BidModel', 'Bids');

			if (empty($bid_id) OR
				! is_numeric($bid_id))
			{
				$bid = new stdClass();
			}
			else
			{
				$bid = $this->Bids->getById($bid_id);
			}

			$bid->order_id = $order_id;
			$bid->manager_id = $this->user->user_id;
			$bid->client_id = $order->order_client;
			$bid->foto_tax = Check::int('foto_tax');
			$bid->requested_foto_count = $order->requested_foto_count;

			// 3. допрасходы
			$extra_tax_counter = Check::int('extra_tax_counter');

			for ($i = 0; $i < $extra_tax_counter; $i++)
			{
				$bid_extra = new stdClass();
				$bid_extra->extra_name = Check::str('extra_tax_name' . $i, 255, 1, 'Дополнительные расходы');
				$bid_extra->extra_tax = Check::int('extra_tax_value' . $i);

				if ($bid_extra->extra_tax)
				{
					$bid_extras[] = $bid_extra;
				}
			}

			if (isset($bid_extras))
			{
				$bid->bid_extras = $bid_extras;
			}

			Check::reset_empties();

			// 4. доставка
			if ($order->order_country_to)
			{
				$bid->delivery_cost = Check::int('delivery_cost');
				$bid->delivery_name = Check::str('delivery_name', 255, 1);
			}
			else
			{
				$bid->delivery_cost = 0;
				$bid->delivery_name = '';
			}

			// 5. комиссия
			$bid->manager_tax = Check::int('manager_tax');
			$bid->manager_tax_percentage = Check::int('manager_tax_percentage');

			// 6. пересчитываем и сохраняем предложение
			if ( ! $this->Bids->recalculate($bid, $order, TRUE))
			{
				throw new Exception('Невожможно пересчитать предложение. Попробуйте еще раз.');
			}

			if ( ! ($bid = $this->Bids->addBid($bid)))
			{
				throw new Exception('Ошибка сохранения предложения. Обратитесь к администрации сервиса.');
			}

			// костыль
			if ($bid_id)
			{
				$bid->bid_id = $bid_id;
			}

			$this->load->model('ManagerModel', 'Managers');
			$this->processStatistics($bid, array(), 'manager_id', $this->user->user_id, 'manager');

			// 7. сохраняем допрасходы
			if (isset($bid_extras))
			{
				$this->Bids->addBidExtras($bid, $bid_extras);
			}
			else
			{
				$this->Bids->addBidExtras($bid, FALSE);
			}

			// 8. комменты
			$comment = new stdClass();
			$comment->message = Check::txt('comment', 8096, 1);

			if ( ! empty($comment->message) AND
				$comment->message != '<p></p>')
			{
				$this->load->model('BidCommentModel', 'Comments');

				$comment->bid_id = $bid->bid_id;
				$comment->user_id = $this->user->user_id;

				$new_comment = $this->Comments->addComment($comment);
				if ( ! $new_comment)
				{
					throw new Exception('Комментарий не добавлен. Попробуйте еще раз.');
				}

				$new_comment->statistics = $bid->statistics;
				
				$bid->comments = array();
				$bid->comments[] = $new_comment;
			}

			// 9. пересчитываем и сохраняем предложение и заказ
			if ( ! $this->Orders->recalculate($order, TRUE))
			{
				throw new Exception('Невозможно пересчитать стоимость заказа. Попробуйте еще раз.');
			}

			$this->Orders->saveOrder($order);

			// 10. отрисовка предложения
			$this->load->model('CountryModel', 'Countries');
			$this->load->model('OdetailModel', 'Odetails');
			$view['countries'] = $this->Countries->getArray();

			$view['selfurl'] = BASEURL . $this->cname . '/';
			$view['viewpath'] = $this->viewpath;

			if (isset($bid_extras))
			{
				$bid->extra_taxes = $bid_extras;
			}

			$view['bid'] = $bid;
			$view['order'] = $order;
			$view['odetails'] = $this->Odetails->getOrderDetails($order_id);
			
			$this->Orders->prepareOrderView($view);
			$view['user_data'] = $this->Managers->getById($this->user->user_id);
			
			$this->load->view("/main/elements/orders/bid", $view);
		}
		catch (Exception $e)
		{
		}
	}

	public function removeBid($order_id, $bid_id)
	{
		try
		{
			if (empty($order_id) OR
				empty($bid_id) OR
				! is_numeric($order_id) OR
				! is_numeric($bid_id))
			{
				throw new Exception('Невозможно удалить предложение.');
			}

			$this->load->model('BidModel', 'Bids');

			// роли и разграничение доступа
			$order = $this->getPublicOrder(
				$order_id, FALSE);

			if (empty($order))
			{
				$order = $this->getPrivilegedOrder(
					$order_id,
					'Заказ не найден. Попробуйте еще раз.');
			}

			if ( ! empty($order->payed_date))
			{
				throw new Exception('Невозможно отказаться от оплаченного заказа.');
			}

			$bid = $this->Bids->getPrivilegedBid(
				$bid_id,
				$this->user->user_id,
				$this->user->user_group);

			if (empty($bid) OR
				$bid->order_id != $order_id)
			{
				throw new Exception('Предложение не найдено.');
			}

			// погнали
			$bid->status = 'deleted';
			$this->Bids->addBid($bid);

			// меняем статус заказа
			if ($order->order_status != 'pending')
			{
				$order->order_status = 'pending';
				$order->order_manager = 0;

				// пересчитываем заказ
				$this->load->model('OrderModel', 'Orders');
				$this->load->model('OdetailModel', 'Odetails');
				$this->load->model('OdetailJointModel', 'Joints');

				if ( ! $this->Orders->recalculate($order))
				{
					throw new Exception('Невозможно пересчитать стоимость заказа. Попробуйте еще раз.');
				}

				$this->Orders->saveOrder($order);
			}

			// отправляем пересчитанные детали заказа
			$response['is_error'] = FALSE;
		}
		catch (Exception $e)
		{
			$response['is_error'] = TRUE;
			$response['message'] = $e->getMessage();
		}

		print(json_encode($response));
	}

	public function addBidComment($bid_id, $comment_id = null)
	{
		parent::addBidComment($bid_id, $comment_id);
	}

	public function addPaymentComment($payment_id, $comment_id = NULL)
	{
		parent::addPaymentComment($payment_id, $comment_id);
	}
	
	public function saveProfilePhoto()
	{
		try
		{
			// находим пользователя
			$this->load->model('UserModel', 'User');
			$user = $this->User->getById($this->user->user_id);

			// находим партнера
			$this->load->model('ManagerModel', 'Manager');
			$manager 		= $this->Manager->getById($this->user->user_id);
			$userfile		= (isset($_FILES['userfile']) AND ! $_FILES['userfile']['error']);
			
			
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

                $manager->avatar = "/upload/avatars/".$this->user->user_id.".jpg";
			}
			// наконец, все сохраняем
			$manager = $this->Manager->updateManager($manager);

            if (!$manager)
			{ 
				throw new Exception('Клиент не сохранен. Попробуйте еще раз.');
			}
			
			// коммитим транзакцию
			if ($this->db->trans_status() === FALSE) 
			{ 
				throw new Exception('Невозможно сохранить данные партнера. Попробуйте еще раз.');
			} 
			echo $manager->avatar.'?r='.rand(0,99999);		
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
			$this->load->model('ManagerModel', 'Manager');
			$manager = $this->Manager->getById($this->user->user_id);

			// валидация пользовательского ввода
			$manager->cashback_limit 		= Check::int('limit');
			$manager->is_mail_forwarding 	= Check::chkbox('mf');
			$manager->is_internal_payments 	= Check::chkbox('payments');
			$manager->about_me				= Check::str('about', 65535, 0);
			$manager->skype					= Check::str('skype', 255, 0);
			$manager->website				= Check::str('website', 4096, 0);
			
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
			$manager->manager_country		= Check::int('country');
			$manager->city					= Check::str('city', 255, 1);
			
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
			$manager->order_mail_forwarding_tax = Check::float('mf_tax');
			$manager->min_order_tax = Check::int('min_order_tax');
			$manager->join_tax = Check::int('join_tax');
			$manager->foto_tax = Check::int('foto_tax');
			$manager->insurance_tax = Check::int('insurance_tax');
			$manager->pricelist_description = Check::str('pricelist_message', 65535, 1);

			Check::reset_empties();
			$manager->order_tax = Check::float('order_tax');
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
			//print_r($e);die();
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
			$manager->manager_address_name = Check::str('address_name', 255, 1, $manager->manager_name);
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

	public function update_odetail_status($order_id, $odetail_id, $status)
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
			$editable_statuses = $this->Orders->getEditableStatuses($this->user->user_group);

			if ( ! in_array($order->order_status, $editable_statuses))
			{
				throw new Exception('Заказ недоступен.');
			}

			// валидация пользовательского ввода
			$statuses = $this->Odetails->getAllStatuses();

			if (empty($statuses[$order->order_type][$status]))
			{
				throw new Exception('Некорректный статус.');
			}

			// находим товар
			$odetail = $this->Odetails->getPrivilegedOdetail(
				$order_id,
				$odetail_id,
				$this->user->user_id,
				$this->user->user_group);

			if (empty($odetail))
			{
				throw new Exception('Товар не найден.');
			}

			$odetail->odetail_status = $status;

			// сохранение результатов
			$this->Odetails->addOdetail($odetail);

			// пересчитываем заказ
			if ( ! $this->Orders->recalculate($order))
			{
				throw new Exception('Невозможно пересчитать стоимость заказа. Попробуйте еще раз.');
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

	// BOF: перенаправление обработчиков в базовый контроллер
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

	public function update_payment_amount($order_id, $payment_id, $amount)
	{
		parent::update_payment_amount($order_id, $payment_id, $amount);
	}

	public function update_payment_status($order_id, $payment_id, $status)
	{
		parent::update_payment_status($order_id, $payment_id, $status);
	}

	public function update_all_payment_status($payment_id, $status)
	{
		parent::update_all_payment_status($payment_id, $status);
	}
	// EOF: перенаправление обработчиков в базовый контроллер

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
					'/manager/showOpenPayments' .
						'/' .
						($this->uri->segment(3) ? $this->uri->segment(3) : 0);
			}
			else
			{
				$this->paging_base_url =
					'/manager/' .
						($this->uri->segment(2) ? $this->uri->segment(2) : 0) .
						'/' .
						($this->uri->segment(3) ? $this->uri->segment(3) : 0);
			}

			$this->paging_uri_segment = 4;
			$this->paging_offset = $this->uri->segment(4);
		}
		elseif ($handler == 'update_payment_status')
		{
			$page_status = ucfirst($this->uri->segment(5));
			$this->paging_base_url = "/manager/show{$page_status}Payments/" . $this->uri->segment(3);
			$this->paging_offset = 0;
		}
		elseif ($handler == 'update_all_payment_status')
		{
			$page_status = ucfirst($this->uri->segment(4));
			$this->paging_base_url = "/manager/showAll{$page_status}Payments/";
			$this->paging_offset = 0;
		}
		else
		{
			parent::init_paging();
		}
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

	public function payments()
	{
		parent::showAllPayments();
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

	public function deletePayment($o2i_id)
	{
		parent::deletePayment($o2i_id);
	}

	public function updatePayment($o2i_id)
	{
		parent::updatePayment($o2i_id);
	}

	public function getNewBid($order_id)
	{
		try
		{
			// безопасность
			if ( ! is_numeric($order_id))
			{
				throw new Exception('Доступ запрещен.');
			}

			// роли и разграничение доступа
			$view['order'] = $this->getPublicOrder(
				$order_id,
				'Заказ не найден. Попробуйте еще раз.');

			// фото
			$this->getOrderFotoCount($view['order']);

			// посредник
			$this->load->model('ManagerModel', 'Managers');
			$view['manager'] = $this->Managers->getById($this->user->user_id);

			$this->processStatistics($view['manager'],
				array(),
				'manager_user',
				$view['manager']->manager_user,
				'manager');

			// юзер
			$this->load->model('UserModel', 'Users');
			$view['user'] = $this->Users->getById($this->user->user_id);

			// отрисовка
			$view['viewpath'] = $this->viewpath;
			$this->load->view($this->viewpath.'ajax/showNewBid', $view);
		}
		catch (Exception $e)
		{
			print_r($e);
		}
	}

	private function getOrderFotoCount($order)
	{
		$order->requested_foto_count = 0;

		$this->load->model('OdetailModel', 'Odetails');
		$odetails = $this->Odetails->getOrderDetails($order->order_id);

		if ($odetails)
		{
			foreach($odetails as $odetail)
			{
				if ($odetail->odetail_foto_requested)
				{
					$order->requested_foto_count++;
				}
			}
		}
	}
}