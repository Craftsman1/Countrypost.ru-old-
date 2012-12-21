<? require_once(MODELS_PATH.'Base/BaseModel.php');

class OrderModel extends BaseModel implements IModel{

	protected   $properties			= NULL;				// array of properties
	protected   $table				= 'orders';			// table name
	protected   $PK					= 'order_id';		// primary key name	
	
    private $order_statuses = array(
		'pending'   => 'Посредник не выбран',
		'processing'   => 'Обрабатывается',
		'not_payed' => 'Не оплачен',
		'not_available' => 'Нет в наличии',
		'payed' => 'Оплачен',
		'bought' => 'Выкуплен',
		'completed' => 'Выполнен',
		'deleted' => 'Удален'
	);

	private $editable_statuses = array(
		'client' => array(
			'pending',
			'processing',
			'not_payed'),
		'manager' => array(
			'processing',
			'not_payed',
			'not_available',
			'payed',
			'bought',
			'completed')
	);

	private $payable_statuses = array(
		'client' => array(
			'not_payed',
			'payed',
			'bought'),
		'manager' => array()
	);

	private $online_order_statuses = array(
		'pending'   => 'Посредник не выбран',
		'processing'   => 'Обрабатывается',
		'not_payed' => 'Не оплачен',
		'not_available' => 'Нет в наличии',
		'payed' => 'Оплачен',
		'bought' => 'Выкуплен',
		'completed' => 'Отправлен',
	);

	private $offline_order_statuses = array(
		'pending'   => 'Посредник не выбран',
		'processing'   => 'Обрабатывается',
		'not_payed' => 'Не оплачен',
		'not_available' => 'Нет в наличии',
		'payed' => 'Оплачен',
		'bought' => 'Выкуплен',
		'completed' => 'Отправлен',
	);

	private $service_order_statuses = array(
		'pending'   => 'Посредник не выбран',
		'processing'   => 'Обрабатывается',
		'not_payed' => 'Не оплачен',
		'payed' => 'Оплачен',
		'completed' => 'Выполнен',
	);

	private $delivery_order_statuses = array(
		'pending'   => 'Посредник не выбран',
		'processing' => 'Ждем прибытия',
		'not_payed' => 'Не оплачен',
		'payed' => 'Оплачен',
		'completed' => 'Отправлен',
	);

	private $mail_forwarding_order_statuses = array(
		'pending'   => 'Посредник не выбран',
		'processing' => 'Ждем прибытия',
		'not_payed' => 'Не оплачен',
		'payed' => 'Оплачен',
		'completed' => 'Отправлен',
	);

	private $filter_statuses = array(
		'pending'   => 'Посредник не выбран',
		'processing'   => 'Обрабатывается',
		'not_payed' => 'Не оплачен',
		'not_available' => 'Нет в наличии',
		'payed' => 'Оплачен',
		'bought' => 'Выкуплен',
		'completed' => 'Выполнен',
	);

	private $order_types = array(
		'offline'   => 'Offline заказ', 
		'online' => 'Online заказ', 
		'service' => 'Услуга',
		'delivery' => 'Доставка',
		'mail_forwarding' => 'Mail Forwarding'
	);

	private $joinable_types = array(
		'offline'   => 'Offline заказ',
		'online' => 'Online заказ',
		'service' => 'Услуга',
		'delivery' => 'Доставка'
	);

	private $order_view_router = array(
		'client'   => array(
			'open' => array(
				'pending',
				'processing',
				'not_payed',
				'not_available'),
			'payed' => array(
				'payed',
				'bought'),
			'sent' => array(
				'completed')
		),
		'manager' => array(
			'bid' => array(
				'pending'),
			'open' => array(
				'processing',
				'not_payed',
				'not_available'),
			'payed' => array(
				'payed',
				'bought'),
			'sent' => array(
				'completed')
		)
	);

	public function getRoutedStatuses($user_group, $status)
    {
		$router = $this->order_view_router[$user_group];

		return $router[$status];
    }

	public function getViewStatus($user_group, $order_status)
    {
		$router = $this->order_view_router[$user_group];

		foreach ($router as $view_status => $order_statuses)
		{
			foreach ($order_statuses as $status)
			{
				if ($order_status == $status)
				{
					return $view_status;
				}
			}

		}

		return 'open';
    }

	public function getEditableStatuses($user_group)
    {
		return $this->editable_statuses[$user_group];
    }

	public function getPayableStatuses($user_group)
    {
		return $this->payable_statuses[$user_group];
    }

	public function getOrderTypes()
    {
	    return $this->order_types;
    }

	public function getJoinableTypes()
    {
	    return $this->joinable_types;
    }

	public function getAllStatuses()
    {
		$statuses = array();

		foreach ($this->order_types as $key => $value)
		{
			$name = "{$key}_order_statuses";
			$statuses[$key] = $this->$name;
		}

	    return $statuses;
    }

	public function getOrderStatusDescription($order_status)
    {
		if (empty($order_status))
		{
			return '';
		}

		return $this->order_statuses[$order_status];
	}
	
	public function getAvailableOrderStatuses()
    {
	    return $this->order_statuses;
    }

	public function getFilterStatuses()
    {
	    return $this->filter_statuses; 
    }

	/**
	 * конструктор
	 *
	 */
	function __construct()
    {
    	$this->properties	= new stdClass();
    	$this->properties->order_id					= '';
    	$this->properties->order_type				= '';
    	$this->properties->order_client				= '';
    	$this->properties->order_model				= '';
    	$this->properties->order_manager			= '';
    	$this->properties->order_weight				= '';
    	$this->properties->order_cost				= '';
    	$this->properties->order_cost_payed			= '';
    	$this->properties->order_date				= '';
    	$this->properties->order_status				= '';
    	$this->properties->order_shop_name			= '';
    	$this->properties->comment_for_manager		= '';
    	$this->properties->comment_for_client		= '';
    	$this->properties->order_address			= '';
    	$this->properties->order_delivery_cost		= '';
    	$this->properties->order_products_cost		= '';
    	$this->properties->order_comission			= '';
		$this->properties->order_manager_comission	= '';
		$this->properties->order_system_comission	= '';
		$this->properties->order_manager_comission_payed	= '';
		$this->properties->order_system_comission_payed	= '';
    	$this->properties->order_country_from		= '';
    	$this->properties->order_country_to			= '';
		$this->properties->order_manager_cost		= '';
		$this->properties->order_payed_to_manager	= '';
		$this->properties->confirmation_sent		= '';
		$this->properties->updated_by_client		= '';
		
    	$this->properties->order_city_to			= '';
		$this->properties->preferred_delivery		= '';
		$this->properties->tracking_no		= '';
		$this->properties->payed_date		= '';
		$this->properties->sent_date		= '';
		$this->properties->address_id		= '';
        $this->properties->is_creating      = '';

		parent::__construct();
    }
    
   /**
     * @see IModel
     * Инкапсуляция
     *
     * @return string
     */
	public function getPK()
	{
		return $this->PK;
	}
	
    /**
     * @see IModel
     * Инкапсуляция
     *
     * @return string
     */	
	public function getTable()
	{
		return $this->table;
	}     
    
    
    /**
     * Get user list
     *
     */
	public function getList()
	{
		$sql = $this->select();
		return ($sql)?($sql):(false);
	}
	
	/**
	 * Get order by id
	 *
	 * @return array
	 */
	public function getById($id){
		$r = $this->select(array(
			$this->getPK()	=> (int) $id,
		));					
		
		return ((count($r==1) &&  $r) ? array_shift($r) : false);
	}
	
	/**
	 * Get property list
	 *
	 * @return array
	 */
	public function getPropertyList()
	{
		return array_keys((array) $this->properties);
	}	
	
	public function addOrder($order_obj)
	{
		$props = $this->getPropertyList();
		
		foreach ($props as $prop)
		{
			if (isset($order_obj->$prop))
			{
				$this->_set($prop, $order_obj->$prop);
			}
		}
		
		$new_id = $this->save(TRUE);
		
		// TODO : На этом этапе можно смело удалять все заказы с `orders`.order_client = $_SESSION['temporary_user_id'] 
		//        если не предусмотрен иной сборщик мусора 
		
		if ($new_id){
			return $this->getInfo(array($new_id));
		}
		
		return false;
	}
	
	public function getClientOrders($client_id)
	{
		return $this->db->query('
			SELECT `'.$this->table.'`.*, `countries`.`country_name`
			FROM `'.$this->table.'`
				INNER JOIN `countries` ON `orders`.`order_country` = `countries`.`country_id`
		')->result();
	}
	
	/**
	 * Get filtered orders
	 *
	 * @return array
	 */
	public function getOrders(
		$filter = NULL, 
		$orderStatus,// = 'open',
		$clientId = NULL, 
		$managerId = NULL)
	{
		// инициализация параметров фильтра
		$countryFromFilter = '';
		$countryToFilter = '';
		$managerFilter = '';
		$periodFilter = '';
		$idFilter = '';
		$clientIdAccess = '';
		$managerIdAccess = '';
		$bidJoin = '';
		$bidFilter = '';
		$user_group = '';
		$managerJoin = '';

		// обработка ограничения доступа клиента и менеджера
		if (isset($clientId))
		{
			$user_group = "client";
			$clientIdAccess = " AND `orders`.`order_client` = $clientId";
			$managerJoin = "LEFT OUTER JOIN `managers` on `managers`.`manager_user` = `orders`.`order_manager`";
		}
		else if (isset($managerId))
		{
			$user_group = "manager";
			$managerIdAccess = " AND `orders`.`order_manager` = $managerId";
			$managerJoin = "INNER JOIN `managers` on `managers`.`manager_user` = `orders`.`order_manager`";
		}

		// обработка статуса
		$statuses = $this->getRoutedStatuses($user_group, $orderStatus);
		$statusFilter = '`orders`.`order_status` IN ("' . implode('","', $statuses) . '")';

		// обработка новых заказов с предложениями посредника
		if ($user_group == "manager" AND
			$orderStatus == 'bid')
		{
			$managerIdAccess = " AND `orders`.`order_manager` = '0'";
			$bidJoin = "INNER JOIN bids ON bids.order_id = orders.order_id";
			$bidFilter = "AND bids.status <> 'deleted' AND bids.manager_id = '$managerId'";
			$managerJoin = '';
		}

		// обработка фильтра
		if (isset($filter))
		{
			if (is_numeric($filter->manager_user))
			{
				$managerFilter = ' AND `managers`.`manager_user` = '.$filter->manager_user;
			}

			if (is_numeric($filter->search_id))
			{
				if ($filter->id_type == 'order')
				{
					$idFilter = ' AND `orders`.`order_id` = '.$filter->search_id;
				}
				else if ($filter->id_type == 'client')
				{
					$idFilter = ' AND `orders`.`order_client` = '.$filter->search_id;
				}
			}

			if ($filter->period == 'day' ||
				$filter->period == 'week' ||
				$filter->period == 'month')
			{
				$periodFilter = ' AND TIMESTAMPDIFF('.strtoupper($filter->period).', `orders`.`order_date`, NOW()) < 1';
			}

			if ( ! empty($filter->id_status))
			{
				$statusFilter .= ' AND `orders`.`order_status` = "'.$filter->id_status.'"';
			}

			if ( ! empty($filter->country_from) AND
				is_numeric($filter->country_from))
			{
				$countryFromFilter = ' AND `orders`.`order_country_from` = '.$filter->country_from;
			}

			if ( ! empty($filter->country_to) AND
				is_numeric($filter->country_to))
			{
				$countryToFilter = ' AND `orders`.`order_country_to` = '.$filter->country_to;
			}
		}
		
		// выборка
		$result = $this->db->query(
			"SELECT 
				`orders`.*, 
				@package_day:=TIMESTAMPDIFF(DAY, `orders`.`order_date`, NOW()) as package_day,
				`users`.`user_login`  as `client_login`,
				c1.`country_name` as `order_country_from`,
				c1.`country_name_en` as `order_country_from_en`,
				c2.`country_name` as `order_country_to`,
				c2.`country_name_en` as `order_country_to_en`,
				c1.`country_currency` as currency,
				TIMESTAMPDIFF(HOUR, `orders`.`order_date`, NOW() - INTERVAL @package_day DAY) as `package_hour`
			FROM `orders`
			INNER JOIN `users` on `users`.`user_id` = `orders`.`order_client`
			$managerJoin
			INNER JOIN `countries` AS c1 on `orders`.`order_country_from` = c1.`country_id`
			LEFT OUTER JOIN `countries` AS c2 on `orders`.`order_country_to` = c2.`country_id`
			$bidJoin
			WHERE
				$statusFilter
				$managerFilter
				$periodFilter
				$idFilter
				$clientIdAccess
				$managerIdAccess
				$bidFilter
				$countryFromFilter
				$countryToFilter
			ORDER BY `orders`.`order_date` DESC"
		)->result();

		// отдаем результат
		return ((count($result) > 0 &&  $result) ? $result : false);		
	}

	public function getUnassignedOrders($filter = NULL, $clientId = NULL) 
	{
		// инициализация параметров фильтра
		$idFilter = '';
		$clientIdAccess = '';
		$managerIdAccess = '';
		$countryFromFilter = '';
		$countryToFilter = '';
		$orderTypeFilter = '';
		$requests_selector = '';
		$requests_join = '';
		$requests_group = '';

		// обработка фильтра
		if (isset($filter))
		{
			if ( ! empty($filter->order_id) AND
				is_numeric($filter->order_id))
			{
				$idFilter = ' AND `orders`.`order_id` = '.$filter->order_id;
			}

			if ( ! empty($filter->country_from) AND
				is_numeric($filter->country_from))
			{
				$countryFromFilter = ' AND `orders`.`order_country_from` = '.$filter->country_from;
			}

			if ( ! empty($filter->country_to) AND
				is_numeric($filter->country_to))
			{
				$countryToFilter = ' AND `orders`.`order_country_to` = '.$filter->country_to;
			}

			if ( ! empty($filter->order_type))
			{
				$orderTypeFilter = " AND `orders`.`order_type` = '{$filter->order_type}'";
			}

			if ( ! empty($filter->requests_count))
			{
				$requests_selector = ", COUNT(r.manager_id) request_count, SUM(r.manager_id = '$clientId') request_sent";
				$requests_join = " LEFT OUTER JOIN `bids` AS r on r.`order_id` = orders.`order_id` AND r.status !=
				'deleted'";
				$requests_group = ' GROUP BY orders.order_id';
			}
		}
		
		// выборка
		$result = $this->db->query(
			"SELECT 
				`orders`.*, 
				@package_day:=TIMESTAMPDIFF(DAY, `orders`.`order_date`, NOW()) as package_day,
				''  as `order_manager_login`, 
				c.`country_name` as `order_country_from`,
				c.`country_name_en` as `order_country_from_en`,
				c.`country_currency` as currency,	
				c2.`country_name` as `order_country_to`,
				c2.`country_name_en` as `order_country_to_en`,
				TIMESTAMPDIFF(HOUR, `orders`.`order_date`, NOW() - INTERVAL @package_day DAY) as `package_hour`
				$requests_selector
			FROM `orders`
			INNER JOIN `countries` AS c on `orders`.`order_country_from` = c.`country_id`
			LEFT OUTER JOIN `countries` AS c2 on `orders`.`order_country_to` = c2.`country_id`
			$requests_join
			WHERE 1
				$idFilter
				$clientIdAccess
				$countryFromFilter
				$countryToFilter
				$orderTypeFilter

				AND `orders`.`order_status` = 'pending'
				AND `orders`.`order_manager` = 0
			$requests_group
			ORDER BY `orders`.`order_date` DESC"
		)->result();

		// отдаем результат
		return ((count($result) > 0 &&  $result) ? $result : false);		
	}
		
	public function getUnassignedOrdersCount(
		$filter = NULL, 
		$orderStatus = 'open', 
		$clientId = NULL, 
		$managerId = NULL, 
		$countryFrom = NULL, 
		$countryTo = NULL) 
	{
		// инициализация параметров фильтра
		$managerFilter = '';
		$idFilter = '';
		$clientIdAccess = '';
		$managerIdAccess = '';
		
		// обработка фильтра
		if (isset($filter))
		{
			if (is_numeric($filter->manager_user))
			{
				$managerFilter = ' AND `managers`.`manager_user` = '.$filter->manager_user;
			}

			if (is_numeric($filter->search_id))
			{
				if ($filter->id_type == 'order')
				{
					$idFilter = ' AND `orders`.`order_id` = '.$filter->search_id;
				}
				else if ($filter->id_type == 'client')
				{
					$idFilter = ' AND `orders`.`order_client` = '.$filter->search_id;
				}
			}

			if ($filter->period == 'day' ||
				$filter->period == 'week' ||
				$filter->period == 'month')
			{
				$periodFilter = ' AND TIMESTAMPDIFF('.strtoupper($filter->period).', `orders`.`order_date`, NOW()) < 1';
			}

			if ( ! empty($filter->id_status))
			{
				$statusFilter = '`orders`.`order_status` = "'.$filter->id_status.'"';
			}
		}
		
		// обработка ограничения доступа клиента и менеджера
		$countryFilter = '';
		
		if (isset($clientId))
		{
			$clientIdAccess = " AND `orders`.`order_client` = $clientId";
		}
		else if (isset($managerId))
		{
			$managerIdAccess = " AND `orders`.`order_manager` = $managerId";
		}		
		
		// выборка: тянем несвязанные с партнером заказы ИЗ ЕГО СТРАНЫ
		$result = $this->db->query(
			"SELECT COUNT(*) AS orders_count
			FROM `orders`
			INNER JOIN `countries` on `orders`.`order_country_from` = `countries`.`country_id`
			WHERE 
				$idFilter
				$clientIdAccess
				$countryFilter
				`orders`.`order_status` = 'pending'
				AND `orders`.`order_manager` = 0
			ORDER BY `orders`.`order_date` DESC"
		)->result();
		
		// отдаем результат
		if ((count($result) > 0 &&  $result))
		{
			$orders_count = $result[0];
			return $orders_count->orders_count;
		}
		
		return 0;
	}
		
	/**
	 * Возвращает заказ, если он есть у партнера
	 *
	 * @return array
	 */
	public function getManagerOrderById($order_id, $manager_id)
	{
		$order = $this->getById($order_id);
		
		if (empty($order) OR
			$order->order_status == 'deleted')
		{
			return FALSE;
		}

		// нашли заказ у посредника
		if ($order->order_manager == $manager_id)
		{
			return $order;
		}

		// ищем предложения
		else if (empty($order->order_manager) AND
			$order->order_status == 'pending')
		{
			$result = $this->db->query("
				SELECT 1
				FROM `bids`
				WHERE
					`bids`.`status` <> 'deleted'
					AND `bids`.`order_id` = $order_id
					AND `bids`.`manager_id` = $manager_id
				GROUP BY `bids`.`manager_id`, `bids`.`order_id`
			")->result();

			// отдаем результат
			if ((count($result > 0) &&  $result))
			{
				return $order;
			}
		}

		return FALSE;
	}
	
	/**
	 * Возвращает заказ, если он есть у клиента
	 *
	 * @return array
	 */
	public function getClientOrderById($order_id, $client_id)
	{
		$order = $this->getById($order_id);

        $temp_client_id = UserModel::getTemporaryKey(false);
		
		// Если пользователь создавший заказ совпадает с авторизованным пользователем в рамках текущей сессии
		if ($order AND
            $order->order_status != 'deleted' AND
                ($order->order_client == $client_id OR
                    ($temp_client_id AND
                     $order->order_client == $temp_client_id)))
		{
			// Заменяем временное значение ID клиента на ID реального клиента 
			// если пользователь авторизовался в процессе оформления заказа
			if ($temp_client_id AND
				$order->order_client == $temp_client_id)
			{
				$order->order_client = $client_id;

                // то-же проделать с деталями заказа.
                $this->db->query(
                    "
                    UPDATE  `odetails` SET
                      `odetails`.`odetail_client` = {$order->order_client},
                      `odetails`.`odetail_manager` = {$order->order_manager},
                      `odetails`.`odetail_country` = {$order->order_country_from}
                    WHERE  `odetails`.`odetail_order` = {$order->order_id}
                    "
                );

                // перекидываем картинки в папку пользователя
                $details = $this->db->query(
                    "
                    SELECT `odetail_id`, `odetail_img` FROM  `odetails`
                    WHERE  `odetail_order` = {$order->order_id}
                    "
                )->result();

                if ($details)
                {
                    foreach ($details as $odetail) :
                        if ($odetail->odetail_img === null AND
                            file_exists('{$_SERVER["DOCUMENT_ROOT"]}/upload/orders/{$client_id}/{$odetail->odetail_id}.jpg'))
                        {
                            copy('{$_SERVER["DOCUMENT_ROOT"]}/upload/orders/{$client_id}/{$odetail->odetail_id}.jpg',
                                '{$_SERVER["DOCUMENT_ROOT"]}/upload/orders/{$odetail->odetail_client}/{$odetail->odetail_id}.jpg');
                        }
                    endforeach;
                }
			}
			
			return $order;
		}

		return FALSE;
	}

    /**
     * Возвращает заказ, если он есть у клиента
     *
     * @return object
     */
    public function getClientBlankOrders($client_id)
    {
        // выборка
        $orders = $this->db->query(
            "SELECT
                `orders` . * ,
                `users`.`user_login` AS `client_login` ,
                `countries`.`country_currency` AS `order_currency`
            FROM `orders`
            LEFT JOIN `users` ON `users`.`user_id` = `orders`.`order_client`
            INNER JOIN `countries` ON `countries`.`country_id` = `orders`.`order_country_from`
            WHERE `orders`.`is_creating` = 1 AND `orders`.`order_type` = 'online' AND `orders`.`order_client` = '".$client_id."'
            UNION ALL
            SELECT
                `orders` . * ,
                `users`.`user_login` AS `client_login` ,
                `countries`.`country_currency` AS `order_currency`
            FROM `orders`
            LEFT JOIN `users` ON `users`.`user_id` = `orders`.`order_client`
            INNER JOIN `countries` ON `countries`.`country_id` = `orders`.`order_country_from`
            WHERE `orders`.`is_creating` = 1 AND `orders`.`order_type` = 'offline' AND `orders`.`order_client` = '".$client_id."'
            UNION ALL
            SELECT
                `orders` . * ,
                `users`.`user_login` AS `client_login` ,
                `countries`.`country_currency` AS `order_currency`
            FROM `orders`
            LEFT JOIN `users` ON `users`.`user_id` = `orders`.`order_client`
            INNER JOIN `countries` ON `countries`.`country_id` = `orders`.`order_country_from`
            WHERE `orders`.`is_creating` = 1 AND `orders`.`order_type` = 'delivery' AND `orders`.`order_client` = '".$client_id."'
            UNION ALL
            SELECT
                `orders` . * ,
                `users`.`user_login` AS `client_login` ,
                `countries`.`country_currency` AS `order_currency`
            FROM `orders`
            LEFT JOIN `users` ON `users`.`user_id` = `orders`.`order_client`
            INNER JOIN `countries` ON `countries`.`country_id` = `orders`.`order_country_from`
            WHERE `orders`.`is_creating` = 1 AND `orders`.`order_type` = 'service' AND `orders`.`order_client` = '".$client_id."'
            UNION ALL
            SELECT
                `orders` . * ,
                `users`.`user_login` AS `client_login` ,
                `countries`.`country_currency` AS `order_currency`
            FROM `orders`
            LEFT JOIN `users` ON `users`.`user_id` = `orders`.`order_client`
            LEFT JOIN `countries` ON `countries`.`country_id` = `orders`.`order_country_from`
            WHERE `orders`.`is_creating` = 1 AND `orders`.`order_type` = 'mail_forwarding' AND `orders`.`order_client` = '".$client_id."'
            "
        )->result();

        if ($orders)
        {
            foreach ($orders as $k=>$order) :
                $odetails = $this->db->query(
                    "SELECT *
                    FROM `odetails`
                    WHERE `odetails`.`odetail_order` = {$order->order_id} AND odetail_status <> 'deleted'
                    ORDER BY `odetails`.`odetail_id` DESC "
                )->result();

                $orders[$k]->details = $odetails;
            endforeach;

            return $orders;
        }

        return false;
    }
	
	/**
	 * Добавление/изменение заказа
	 * Выкидывает исключения на некорректные данные
	 * 
	 * @param (object) 	- $order
	 * @return (mixed)	- объект order или FALSE в случае ошибки записи в базу
	 */
	public function saveOrder($order)
	{
		$props = $this->getPropertyList();
		
		foreach ($props as $prop)
		{
			if (isset($order->$prop))
			{
				$this->_set($prop, $order->$prop);
			}
		}
		
		$new_id = $this->save(TRUE);
		
		if (!$new_id) return FALSE;
		
		return $this->getInfo(array($new_id));
	}
	
	/**
	 * Get sent orders by manager id
	 *
	 * @return array
	 */
	public function getSentOrdersByManagerId($id){
		
		$result = $this->select(array('order_manager' => $id, 'order_status' => 'sended'));
		
		return ((count($result) > 0 &&  $result) ? $result : FALSE);
	}
	
	/**
	 * Updates order with available deliveries
	 *
	 * @return array
	 */
	public function setAvailableDeliveries($order, $pricelist) 
	{
		if (!$order->order_country_from ||
			!$order->order_country_to)
		{
			$order->delivery_list = FALSE;
		}
		else
		{		
			$order->delivery_list = $pricelist->getPricesByWeight(
				$order->order_weight,
				$order->order_country_from, 
				$order->order_country_to);
		}
	}
	
	/**
	 * Рассчитывает стоимость заказа
	 *
	 * @return array
	 */
	public function calculateCost($order)
	{
		$ci = get_instance();
		$ci->load->model('CurrencyModel', 'Currencies');
		$ci->load->model('CountryModel', 'Countries');
		$ci->load->model('ManagerModel', 'Manager');
		$ci->load->model('TaxModel', 'Taxes');
			
		$country = $ci->Countries->getById($order->order_country);
		$cross_rate = $ci->Currencies->getById($country->country_currency);
			
		// комиссии для страны
		$tax = $ci->Taxes->getByCountryId($order->order_country);
			
		if ($tax === FALSE)
		{
			throw new Exception('Невозможно рассчитать стоимость заказа. Данные для расчета недоступны.');
		}		
		
		// комиссия партнера, %
		$manager = $ci->Manager->getById($order->order_manager);
		
		if ( ! $manager)
		{
			throw new Exception('Невозможно рассчитать стоимость заказа. Менеджер не найден.');
		}

		$manager_tax = isset($manager->order_tax) ? $manager->order_tax : 0.5 * $tax->order;

		// полная комиссия
		$order->order_comission = ceil(
			($order->order_products_cost + $order->order_delivery_cost) * 
			$tax->order *
			0.01);
			
		// минимальные комиссии: пополам админу и партнеру
		if ($order->order_comission < $tax->min_order)
		{
			$order->order_comission = $tax->min_order;
			
			$order->order_manager_comission = 
				$tax->min_order * 
				0.5;
			
			$order->order_manager_comission_local = 
				$order->order_manager_comission *
				$cross_rate->cbr_cross_rate;
		}
		// простые комиссии: партнеру его %, админу все остальное
		else
		{
			$order->order_manager_comission = 
				($order->order_products_cost + $order->order_delivery_cost) * 
				$manager_tax * 
				0.01;
			
			$order->order_manager_comission_local = 
				($order->order_products_cost_local + $order->order_delivery_cost_local) * 
				$manager_tax *
				0.01;
		}
		
		// комиссия системы
		$order->order_system_comission = $order->order_comission - $order->order_manager_comission;
		
		// стоимость, которую оплатит клиент
		$order->order_cost = 
			$order->order_products_cost +
			$order->order_delivery_cost +
			$order->order_comission;
				
		// стоимость для выплаты партнеру		
		$order->order_manager_cost = 
			$order->order_products_cost +
			$order->order_delivery_cost +
			$order->order_manager_comission;
						
		// она же в местной валюте
		$order->order_manager_cost_local = 
			$order->order_products_cost_local +
			$order->order_delivery_cost_local +
			$order->order_manager_comission_local;
						
		return $order->order_cost ? $order : FALSE;
	}
	
	/**
	 * Вычисляет статус заказа
	 *
	 * @return string
	 */
	public function calculateOrderStatus($status)
	{
		if ($status == 'processing')
		{
			return 'processing';
		}
		else if ($status == 'not_available' ||
			$status == 'not_delivered' ||
			$status == 'payed' ||
			$status == 'sended')
		{
			return $status;
		}
		else if ($status == 'purchased' || $status == 'received')
		{
			return 'payed';
		}
		else 
		{
			return 'not_payed';
		}
	}
	
	// полный пересчет стоимости заказа
	// например, при удалении товара
	public function recalculate($order, $OdetailModel, $OdetailJointModel)
	{
		try
		{
			$order = $this->calculateTotals($order, $OdetailModel, $OdetailJointModel);
		
			if (empty($order))
			{
				return FALSE;
			}

			// Дописать пересчет стоимости заказа
			//$order = $this->calculateCost($order, $config);

			if (empty($order))
			{
				return FALSE;
			}
		}
		catch (Exception $e) 
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	private function calculateTotals($order, $OdetailModel, $OdetailJointModel)
	{
		$total_price = 0;
		$total_pricedelivery = 0;
		$total_weight = 0;
		$joints = array();
	
		$odetails = $OdetailModel->getOrderDetails($order->order_id);
		$total_status = $order->order_status;

		// вычисляем статус, если выбран посредник
		if ($order->order_status != 'pending')
		{
			$has_not_available_status = FALSE;
			$has_processing_status = FALSE;
			$has_available_status = FALSE;
			$has_bought_status = FALSE;

			foreach ($odetails as $odetail)
			{
				switch ($odetail->odetail_status)
				{
					case 'processing' :
						$has_processing_status = TRUE;
						break;
					case 'available' :
						$has_available_status = TRUE;
						break;
					case 'not_available' :
					case 'not_available_color' :
					case 'not_available_size' :
					case 'not_available_count' :
						$has_not_available_status = TRUE;
						break;
					case 'bought' :
						$has_bought_status = TRUE;
						break;
				}
			}

			if ($has_not_available_status)
			{
				$total_status = 'not_available';
			}
			else if ($has_processing_status)
			{
				$total_status = 'processing';
			}
			else if ($has_available_status)
			{
				$total_status = 'not_payed';
			}
			else if ($has_bought_status)
			{
				$total_status = 'bought';
			}
		}

		// одиночные товары
		foreach ($odetails as $odetail)
		{
			// подсчет сумм цен
			$total_price += $odetail->odetail_price;
			$total_weight += $odetail->odetail_weight;

			// для объединенных товаров доставку считаем ниже
			if ($odetail->odetail_joint_id)
			{
				if ( ! in_array($odetail->odetail_joint_id, $joints))
				{
					$joints[] = $odetail->odetail_joint_id;
				}
			}
			else
			{
				$total_pricedelivery += $odetail->odetail_pricedelivery;
			}
		}

		// объединенные товары
		foreach ($joints as $joint_id)
		{
			$joint = $OdetailJointModel->getById($joint_id);
			if (empty($joint))
			{
				throw new Exception('Некоторые товары не найдены.');
			}
			
			// суммируем доставку
			$total_pricedelivery += $joint->cost;
		}

		// считаем стоимость заказа
		$order->order_weight = $total_weight;
		$order->order_status = $total_status;

		$order->order_products_cost = $total_price;
		$order->order_delivery_cost = $total_pricedelivery;
		$order->order_cost =
			$order->order_products_cost +
			$order->order_delivery_cost;


		return $order;
	}

	public function getOrderFotos($client_id, $odetails)
	{
		$fotos	= array();
		
		if ( ! is_numeric($client_id))
		{
			return $fotos;
		}
		
		$scandir = UPLOAD_DIR."orders/{$client_id}/";
		
		if (is_dir($scandir))
		{
			foreach ($odetails as $odetail)
			{
				$filename = "{$odetail->odetail_id}.jpg";
				$filepath = $scandir . $filename;
				
				if (is_file($filepath))
				{
					$fotos[$filename] = $filepath;
				}
			}
		}
	
		return $fotos;
	}
	
	public function prepareOrderView($view)
	{
		$ci = get_instance();
		$ci->load->model('CountryModel', 'Countries');

		// ищем страны для заказа
		$order_country_from = $ci->Countries->getById($view['order']->order_country_from);
		$order_country_to = $ci->Countries->getById($view['order']->order_country_to);

		$view['order']->order_currency = strval($order_country_from->country_currency);
		$view['order']->order_country_from = strval($order_country_from->country_name);
		$view['order']->order_country_to = $order_country_to ? strval($order_country_to->country_name) : '';
	}

	public function prepareNewBidView($order, $manager_id, $just_logged_in = FALSE)
	{
		$ci = get_instance();
		$ci->load->model('CountryModel', 'Countries');
		$ci->load->model('ManagerModel', 'Managers');
		$ci->load->model('OdetailModel', 'Odetails');

		// ищем посредника
		$manager = $ci->Managers->getById($manager_id);

		if ($just_logged_in)
		{
			// ищем страны и валюту для заказа
			$order_country_from = $ci->Countries->getById($order->order_country_from);
			$order_country_to = $ci->Countries->getById($order->order_country_to);

			$order->order_currency = strval($order_country_from->country_currency);
			$order->order_country_from = strval($order_country_from->country_name);
			$order->order_country_to = $order_country_to ? strval($order_country_to->country_name) : '';
		}

		// считаем сколько заказано фото
		$order->requested_foto_count = 0;

		$odetails = $ci->Odetails->getOrderDetails($order->order_id);

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

		// расчитываем комиссии
		// 1. комиссия посредника
		$order->manager_tax = ceil(
			($order->order_products_cost + $order->order_delivery_cost) *
			$manager->order_tax *
			0.01);

		if ($order->manager_tax < $manager->min_order_tax)
		{
			$order->manager_tax = $manager->min_order_tax;
		}

		// 2. комиссия за фото
		$order->foto_tax = $order->requested_foto_count * $manager->foto_tax;

		// 3. стоимость предложения
		$order->order_total_cost =
			$order->order_products_cost +
			$order->order_delivery_cost +
			$order->manager_tax +
			$order->foto_tax;

		// 4. заполняем данные для динамических расчетов
		$order->manager_tax_percentage = $manager->order_tax;
		$order->manager_foto_tax_percentage = $manager->foto_tax;
	}
}
?>