<?php
require_once BASE_CONTROLLERS_PATH.'ManagerBaseController'.EXT;

class Manager extends ManagerBaseController {

	function __construct()
	{
		parent::__construct();	
	}
	
	function index()
	{
		Func::redirect(BASEURL.$this->cname.'/showNewPackages');
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
	
	public function autocompletePackage($query)
	{
		$this->load->model('PackageModel', 'Package');
		$ids = $this->Package->autocompleteManager(intval($query), $this->user->user_id);
		
		if ($ids)
		{
			echo "[$ids]";
		}
		else
		{
			echo '[]';
		}
	}
	
	public function showAddPackage()
	{
		parent::showAddPackage();
	}
	
	public function addPackage()
	{
		parent::addPackage();
	}
	
	public function updateOdetailStatuses()
	{
		parent::updateOdetailStatuses();
	}
	
	public function updatePdetailStatuses()
	{
		parent::updatePdetailStatuses();
	}
	
	public function updateWeight()
	{
		parent::updateWeight();
	}
	
	public function updatePackagesTrackingNo()
	{
		parent::updateWeight(FALSE);
		
		try
		{
			$this->load->model('PackageModel', 'Packages');
			$this->db->trans_begin();
			
			foreach($_POST as $key=>$value)
			{
				// поиск параметров в запросе
				if (stripos($key, 'package') === FALSE) continue;
			
				$package_id = str_ireplace('package', '', $key);
				if (!is_numeric($package_id)) continue;

				// безопасность: проверяем связку менеджера и посылки
				$this->load->model('PackageModel', 'Packages');
				$this->load->model('OrderModel', 'Orders');
				$package = $this->Packages->getManagerPackageById($package_id, $this->user->user_id);

				if ( ! $package)
				{
					throw new Exception('Одна или несколько посылок не найдены. Попоробуйте еще раз.');
				}
					
				// валидация пользовательского ввода
				Check::reset_empties();
				$package->package_status		= 'sent';
				$package->package_trackingno 	= Check::txt('package_trackingno'.$package_id, 255, 1);
				$empties						= Check::get_empties();
		
				if ($empties) 
				{
					throw new Exception('Некоторые Tracking № отсутствуют. Попробуйте еще раз.');
				}
				
				// закрываем связанный заказ
				if ($package->order_id)
				{
					$order = $this->Orders->getById($package->order_id);
					
					if ( ! $order)
					{
						throw new Exception('Невозможно закрыть или открыть связанный с посылкой заказ. Заказ не найден.');
					}
					
					if ($order->order_status != 'sended')
					{
						$order->order_status = 'sended';
						$this->Orders->saveOrder($order);
					}
				}

				// сохранение результатов
				$new_package = $this->Packages->savePackage($package);
				
				if (!$new_package)
				{
					throw new Exception('Некоторые посылки не отправлены. Попоробуйте еще раз.');
				}
			}
			
			$this->db->trans_commit();
			
			$this->result->m = 'Посылки успешно отправлены.';
			Stack::push('result', $this->result);
		}
		catch (Exception $e) 
		{
			$this->db->trans_rollback();
			
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}

		// открываем оплаченные посылки
		Func::redirect(BASEURL.$this->cname.'/showPayedPackages');
	}
	
	public function closeOrders()
	{
		try
		{
			if (!$this->user ||
				!$this->user->user_id)
			{
				throw new Exception('Доступ запрещен.');
			}
			
			$this->load->model('OrderModel', 'Orders');
			$this->db->trans_begin();
			
			foreach($_POST as $key=>$value)
			{
				// поиск параметров в запросе
				if (stripos($key, 'order') === FALSE) continue;
			
				$order_id = str_ireplace('order', '', $key);
				if (!is_numeric($order_id)) continue;

				// безопасность: проверяем связку менеджера и заказа
				$this->load->model('OrderModel', 'Orders');
				$order = $this->Orders->getManagerOrderById($order_id, $this->user->user_id);

				if (!$order)
				{
					throw new Exception('Один или несколько заказов не найдены. Попоробуйте еще раз.');
				}
					
				// сохранение результатов
				$order->order_status = 'sended';
				$new_order = $this->Orders->saveOrder($order);
			}
			
			$this->db->trans_commit();
			
			$this->result->m = 'Заказы успешно закрыты.';
			Stack::push('result', $this->result);
			
			// уведомления
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
				null,
				$this->Users,
				'Отправлен');

			Mailer::sendClientNotification(
				Mailer::SUBJECT_NEW_ORDER_STATUS, 
				Mailer::NEW_ORDER_STATUS_NOTIFICATION, 
				$order->order_id, 
				$order->order_client,
				"http://countrypost.ru/client/showOrderDetails/{$order->order_id}",
				$this->Clients,
				'Отправлен');

		}
		catch (Exception $e)
		{
			$this->db->trans_rollback();
			
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}

		// открываем заказы
		Func::redirect(BASEURL.$this->cname.'/showPayedOrders');
	}
	
	public function updateOrderDetails()
	{
		parent::updateOrderDetails();
	}
	public function updatePackageDetails()
	{
		parent::updatePackageDetails();
	}
	
	public function showNewPackages()
	{
		$this->showPackages('open', 'showNewPackages');
	}
	
	public function showPayedPackages()
	{
		$this->showPackages('payed', 'showPayedPackages');
	}
	
	public function showSentPackages()
	{
		$this->showPackages('sent', 'showSentPackages');
	}
	
	public function showOpenOrders()
	{
		$this->showOrders('not_payed', 'showOpenOrders');
	}
	
	public function showPayedOrders()
	{
		$this->showOrders('payed', 'showPayedOrders');
	}
	
	public function showSentOrders()
	{
		$this->showOrders('sended', 'showSentOrders');
	}

	public function addPackageComment($package_id, $comment_id = null)
	{
		try
		{
			if (!is_numeric($package_id))
			{
				throw new Exception('Доступ запрещен.');
			}
		
			// безопасность: проверяем связку менеджера и посылки
			$this->load->model('PackageModel', 'Packages');
			$package = $this->Packages->getManagerPackageById($package_id, $this->user->user_id);

			if (!$package)
			{
				throw new Exception('Невозможно добавить комментарий. Партнер не обрабатывает данную посылку.');
			}

			// валидация пользовательского ввода
			$this->load->model('PCommentModel', 'Comments');
			
			if (is_numeric($comment_id)) 
			{
				$pcomment = $this->Comments->getById($comment_id);
				if (!$pcomment) 
				{
					throw new Exception('Невозможно изменить комментарий. Комментарий не найден.');
				}
				
				$pcomment->pcomment_comment	= Check::txt('comment_update', 8096, 1);
			}
			else
			{
				$pcomment				= new stdClass();
				$pcomment->pcomment_comment	= Check::txt('comment', 8096, 1);
			}
			
			$pcomment->pcomment_package	= $package_id;
			$pcomment->pcomment_user	= $this->user->user_id;
			$empties					= Check::get_empties();
		
			if ($empties) 
			{
				throw new Exception('Текст комментария отсутствует. Попробуйте еще раз.');
			}
			
			// сохранение результатов
			if (!$this->Comments->addComment($pcomment) &&
				!is_numeric($comment_id))
			{
				throw new Exception('Комментарий не добавлен. Попробуйте еще раз.');
			}			
			
			// выставляем флаг нового комментария
			$package->comment_for_client = TRUE;
			$this->Packages->savePackage($package);

			// уведомления
			$this->load->model('ManagerModel', 'Managers');
			$this->load->model('UserModel', 'Users');
			$this->load->model('ClientModel', 'Clients');

			Mailer::sendClientNotification(
				Mailer::SUBJECT_NEW_COMMENT, 
				Mailer::NEW_PACKAGE_COMMENT_NOTIFICATION, 
				$package->package_id, 
				$package->package_client,
				"http://countrypost.ru/client/showPackageDetails/{$package->package_id}#comments",
				$this->Clients);

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
	
	public function delPackageComment($package_id, $comment_id)
	{
		parent::delPackageComment((int) $package_id, (int) $comment_id);
	}

	public function delOrderComment($package_id, $comment_id){
		parent::delOrderComment((int) $package_id, (int) $comment_id);
	}

	public function deletePackage()
	{
		parent::deletePackage();
	}

	public function editPackageAddress()
	{
		parent::editPackageAddress();
	}

	public function order()
	{
		parent::showOrderDetails();
	}
	
	public function showDeclaration()
	{
		parent::showDeclaration();
	}
	
	public function joinProducts($order_id)
	{
		parent::joinProducts($order_id);
	}
	
	public function removeOdetailJoint($order_id, $odetail_joint_id)
	{
		parent::removeOdetailJoint($order_id, $odetail_joint_id);
	}
	
	public function previewDeclaration($package_id){
		
		$this->load->model('PackageModel', 'Packages');
		$this->load->model('DeclarationModel', 'Declarations');
		
		(int) $package_id;
		$declarations	= null;
		
		$package		= $this->Packages->getManagerPackageById($package_id, $this->user->user_id);
		
		if ($package){
			$declarations	= $this->Declarations->getDeclarationsByPackageId($package_id);	
		}
		
		View::showChild($this->viewpath.'/pages/previewPackageDeclaration', array(
			'package'		=> $package,
			'declarations'	=> $declarations
		));
	}
	
	public function showPackageDetails()
	{
		parent::showPackageDetails();
	}	
	
	public function addOrderComment($order_id, $comment_id = null)
	{
		parent::addOrderComment($order_id, $comment_id);
	}
	
	public function addBidComment($bid_id, $comment_id = null)
	{
		parent::addBidComment($bid_id, $comment_id);
	}
	
	public function saveDeclaration()
	{
		parent::saveDeclaration();
	}	

	public function updatePackageAddress()
	{
		parent::updatePackageAddress();
	}
	
	public function updateNewPackagesStatus()
	{
		$this->updateStatus('not_payed', 'showNewPackages', 'PackageModel');
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
					'pdetail_manager' => $this->user->user_id,
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
			if (! $uploaded)
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
			$joint = $this->Joints->getManagerJoint($pdetail_joint_id, $this->user->user_id);
							
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
				'pdetail_manager' => intval($this->user->user_id),
			))) 
		{
			$pdetail = $pdetail[0];
			readfile(UPLOAD_DIR . "packages/$package_id/$pdetail_id/$filename");
		}
		
		die();
	}
	
	public function showPdetailJointFoto($package_id, $pdetail_joint_id, $filename)
	{
		header('Content-type: image/jpg');
		$this->load->model('PdetailJointModel', 'Joints');

		if ($this->Joints->getManagerJoint($pdetail_joint_id, $this->user->user_id)) 
		{
			readfile(UPLOAD_DIR . "packages/$package_id/joint_$pdetail_joint_id/$filename");
		}

		die();
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
	
	public function showPdetailScreenshot($pid=null) 
	{
		header('Content-type: image/jpg');
		$this->load->model('PdetailModel', 'PdetailModel');
		if ($Detail = $this->PdetailModel->getInfo(array('pdetail_id' => intval($pid)))) 
		{
			readfile("{$_SERVER['DOCUMENT_ROOT']}/upload/packages/{$Detail->pdetail_package}/{$Detail->pdetail_id}.jpg");
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

	public function refundPackage()
	{
		parent::refundPackage();
	}	

	public function sendOrderConfirmation($order_id){
		parent::sendOrderConfirmation((int) $order_id);
	}
	
	public function addProductManualAjax() 
	{
		parent::addProductManualAjax();
	}
	
	public function addProductManualAjaxP() 
	{
		parent::addProductManualAjaxP();
	}
	
	public function deleteProductP($id)
	{
		parent::deleteProductP($id);
	}
	
	public function deliverPackage($package_id)
	{
		try
		{
			if ( ! $this->user OR
				! $this->user->user_id OR
				! is_numeric($package_id))
			{
				throw new Exception('Доступ запрещен.');
			}
			
			// безопасность: проверяем связку клиента и заказа
			$this->load->model('PackageModel', 'Packages');
			$package = $this->Packages->getManagerPackageById($package_id, $this->user->user_id);

			if ( ! $package)
			{
				throw new Exception('Посылка не найдена. Попробуйте еще раз.');
			}
			
			$package->package_status = 'not_payed';
			$this->Packages->savePackage($package);			
		}
		catch (Exception $e)
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
		}
		
		Func::redirect($_SERVER['HTTP_REFERER']);
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
	
	public function filterPayedOrders()
	{
		$this->filter('payedOrders', 'showPayedOrders');
	}
	
	public function filterSentOrders()
	{
		$this->filter('sendedOrders', 'showSentOrders');
	}
	
	public function acceptOrder($order_id)
	{
		parent::connectOrderToManager($order_id, $this->user->user_id);
	}
	
	public function filterPaymentHistory()
	{
		$this->filter('paymentHistory', 'showPaymentHistory');
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
}