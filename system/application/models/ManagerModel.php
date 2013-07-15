<?
require_once(MODELS_PATH.'Base/BaseModel.php');
/**
 * @author omni
 * 
 * моделька для магазина
 * 1. в модели не делаем проверок на валидность i\o это должно делаться в контролере
 * 2. допустимы только ошибки уровня БД
 * 3. разрешатся передавать списки параметров функции, только в случает отсутствия публичного 
 * атрибута соответствующего объекта
 *
 */
class ManagerModel extends BaseModel implements IModel{

	protected 	$properties			= null;				// array of properties
	protected	$table				= 'managers';			// table name
	protected	$PK					= 'manager_user';		// primary key name	
	
	private $statuses = array(
		1	=> 'В работе',
		2	=> 'Приостановлен'
	);
	
	public function getStatuses() {
		return $this->statuses;
	}
	
	/**
	 * конструктор
	 *
	 */
	function __construct()
    {
    	$this->properties	= new stdClass();
    	$this->properties->manager_user				='';
    	$this->properties->manager_country			='';
    	$this->properties->manager_max_orders		='';
    	$this->properties->manager_name				='';
    	$this->properties->manager_address			='';
    	$this->properties->manager_address_local	='';
    	$this->properties->manager_address_description ='';
    	$this->properties->manager_address_name ='';
    	$this->properties->city ='';
    	$this->properties->manager_phone			='';
    	$this->properties->manager_skype			='';
    	$this->properties->manager_status			='';
    	$this->properties->user_login				='';
    	$this->properties->country_name				='';
    	$this->properties->created					='';
		$this->properties->manager_credit           =0;
 		$this->properties->manager_credit_date      ='';
		$this->properties->manager_credit_local     =0;
 		$this->properties->manager_credit_date_local='';
		$this->properties->manager_balance_local	=0;
		$this->properties->manager_description		='';
		$this->properties->order_tax				='';
		$this->properties->order_mail_forwarding_tax='';
		$this->properties->min_order_tax			='';
		$this->properties->join_tax					='';
		$this->properties->foto_tax					='';
		$this->properties->insurance_tax			='';
		$this->properties->pricelist_description	='';
		$this->properties->payments_description		='';
		$this->properties->rating	='';
		$this->properties->buy_rating	='';
		$this->properties->pack_rating	='';
		$this->properties->consolidation_rating	='';
		$this->properties->communication_rating	='';
		$this->properties->buy_rating_count	='';
		$this->properties->pack_rating_count	='';
		$this->properties->consolidation_rating_count	='';
		$this->properties->communication_rating_count	='';
		$this->properties->website	='';
		$this->properties->skype	='';
		$this->properties->created	='';
		$this->properties->about_me	='';
		$this->properties->is_cashback	='';
		$this->properties->cashback_limit	='';
		$this->properties->is_mail_forwarding ='';
		$this->properties->is_internal_payments ='';

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
		return ($sql)?($sql):(FALSE);
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
	
	/**
	 * Add client data
	 *
	 * @param	int		$user_id
	 * @param	object	$client_obj
	 * @return	object 
	 */
	public function addManagerData($user_id, $manager_obj){
		
		$props = $this->getPropertyList();
		
		foreach ($props as $prop){
			if (isset($manager_obj->$prop)){
				$this->_set($prop, $manager_obj->$prop);
			}
		}
		
		$this->_set($this->getPK(), $user_id);
		
		/**
		 * if primary key of table is not AI,
		 * insert_id will return FALSE
		 */
		$this->save(true);
		
		if ($user_id){
			return $this->getInfo(array($user_id));
		}
		
		return FALSE;
	}	
	
	public function getManagersData($filters = null) 
	{
		$where = '';
		
		if ( ! empty($filters->country_from) OR
			! empty($filters->is_mail_forwarding) OR
			! empty($filters->is_cashback))
		{
			$where = ((!empty($filters->country_from)) ? ' AND `'.$this->table.'`.manager_country = '.$filters->country_from.' ' : '').
				((!empty($filters->is_mail_forwarding)) ? ' AND `'.$this->table.'`.is_mail_forwarding = 1 ' : '').
				((!empty($filters->is_cashback)) ? ' AND `'.$this->table.'`.is_cashback = 1 ' : '');
		}

		return $this->db->query('
			SELECT `'.$this->table.'`.*, 
				`users`.`user_login`, 
				`users`.`user_coints`, 
				12345 AS `clients_count`,
				`countries`.`country_currency`
			FROM `'.$this->table.'`
				INNER JOIN `users` ON `users`.`user_id` = `'.$this->table.'`.`manager_user`
				INNER JOIN `countries` ON `countries`.`country_id` = `'.$this->table.'`.`manager_country`				
			WHERE `users`.`user_deleted` = 0 '.$where.'
			GROUP BY `'.$this->table.'`.`manager_user`
            ORDER BY rating DESC
		')->result();
	}
	
	public function getById($id){
		$r = $this->select(array(
			$this->getPK()	=> (int) $id,
		));					
		
		return ((count($r==1) &&  $r) ? array_shift($r) : FALSE);
	}
	
	public function updateManager($manager_obj) {
		$props = $this->getPropertyList();
		
		foreach ($props as $prop)
		{
			if (isset($manager_obj->$prop) && !empty($manager_obj->$prop))
			{
				$this->_set($prop, $manager_obj->$prop);
			}
			else if ($prop != 'user_login' && $prop != 'country_name')
			{
				$this->db->set($prop, null);
			}
		}
		
		if ($this->save(true))
		{
			return $this->getInfo(array($manager_obj->manager_user));
		}
		
		return FALSE;
	}
	
	/**
	 * Список партнеров
	 */
    public function getManagers() {
        $managers = $count = array();
        $rows_count = $this->db->query('SELECT `'.$this->table.'`.manager_user as manager_id,(SELECT count(c2m.manager_id ) FROM `c2m` WHERE c2m.manager_id=`'.$this->table.'`.manager_user) as count FROM `'.$this->table.'`')->result();

        foreach($rows_count as $r){
            $count[$r->manager_id]=$r->count;
        }

        $rows = $this->db->query('
			SELECT `'.$this->table.'`.*
						FROM `'.$this->table.'`
		 				WHERE `'.$this->table.'`.`manager_status` = 1
						GROUP BY `'.$this->table.'`.`manager_user`		
		')->result();
        $last = array();
        /*print_r($rows);
        echo "<br/>";*/
        $managers['all'] = array();
        $managers['addons'] = array();
        foreach ($rows as $row) {
            if((!array_key_exists($row->manager_country, $last) || $last[$row->manager_country]->last_client_added > $row->last_client_added))
                $last[$row->manager_country] = $row;
            if ((!array_key_exists($row->manager_country, $managers['all']) || $managers['all'][$row->manager_country]->last_client_added > $row->last_client_added) &&($count[$row->manager_user]<$row->manager_max_clients)) {
                //if ((!array_key_exists($row->manager_country, $managers) || $managers[$row->manager_country]->last_client_added > $row->last_client_added)) {
                $managers['all'][$row->manager_country] = $row;
                /*
                if(isset($count[$row->manager_user]) && $count[$row->manager_user]>$row->manager_max_clients){
                    $managers['addons'][$row->manager_country] = $row;
                }*/
            }

        }
        /*print_r($count);
        echo "<br/>";
        print_r($last);
        echo "<br/>";
        print_r($managers);
        echo "<br/>";*/
        foreach ($last as $k => $country_manager){
            if (!isset($managers['all'][$k])){
                $managers['all'][$k] = $country_manager;
                $managers['addons'][$k] = $country_manager;
            }
        }
        /*print_r($managers);
        echo "<br/>";
        die();*/
        return $managers;
    }

    /* Список посредников для автокомплит */
    public function getAutocomplitManagers($query, $is_mail_forwarding = FALSE)
    {
        $like_query = $this->db->escape("%$query%");
		$mf_filter = ($is_mail_forwarding ?
			'a.is_mail_forwarding = 1' :
			'1 = 1');

        $managers = $this->db->query(
            "SELECT
                a.manager_user as id,
                b.user_login as login,
                a.manager_name as fio
            FROM `".$this->table."` a
            LEFT JOIN `users` b ON b.user_id = a.manager_user
            WHERE $mf_filter
            HAVING
                fio LIKE $like_query OR
                a.manager_user LIKE $like_query OR
                b.user_login LIKE $like_query"
        )->result();

        return $managers;
    }
	
    public function getMailForwardingManagers()
    {
        $managers = $this->db->query(
            "SELECT
                manager_user,
                user_login,
                manager_name,
                country_name_en
            FROM
            	`managers`
            LEFT JOIN
            	`users` ON `users`.user_id = `managers`.manager_user
            LEFT JOIN
            	`countries` ON countries.country_id = managers.manager_country
            WHERE
            	is_mail_forwarding = 1
            ORDER BY
            	managers.manager_country ASC, managers.manager_name ASC
        ")->result();

        return $managers;
    }

	public function getActiveManagers() {
		$result = $this->select(array('manager_status' => '1'));
		
		return ((count($result) > 0 &&  $result) ? $result : FALSE);		
	}
	
	public function makePaymentLocal($manager, $amount) 
	{		
		$manager->manager_balance_local += $amount;
		
		return $this->updateManager($manager);
	}
	
	public function isOrdersAllowed($manager)
	{
		// быстрая проверка
		if (is_null($manager->manager_max_orders))
		{
			return TRUE;
		}
		elseif (empty($manager->manager_max_orders))
		{
			return FALSE;
		}
		
		// считаем разницу между разрешенным максимальным количеством заказов и текущим количеством
		$active_order_count = $this->db->query(
			"SELECT COUNT(`orders`.`order_id`) AS `active_order_count`
			FROM `orders`
			WHERE 
				`orders`.`order_manager` = {$manager->manager_user}
				AND NOT (`orders`.`order_status` IN ('deleted', 'sended'))"
		)->result();
		
		if (empty($active_order_count))
		{
			return TRUE;
		}
		
		$active_order_count = $active_order_count[0];
		$active_order_count = $active_order_count->active_order_count;
		
		// разрешаем добавлять заказы только если разрешено больше, чем уже добавлено
		return ($manager->manager_max_orders > $active_order_count);
	}
	
	public function getCountryManagers($country_id)
	{
		$result = $this->db->query(
			"SELECT DISTINCT `{$this->table}`.`manager_user`
			FROM `{$this->table}`
			WHERE 
				`{$this->table}`.`manager_country` = $country_id
				AND `{$this->table}`.`manager_status` = 1"
		)->result();
		
		return ($result) ? $result : FALSE;		
	}

	public function getManagerDeliveries($manager_id)
	{
		$result = $this->db->query(
			"SELECT description, country_id
			FROM manager_pricelists
			WHERE
				manager_id = $manager_id
			ORDER BY country_id"
		)->result();

		if ($result)
		{
			$ordered_result = array();

			foreach ($result as $delivery)
			{
				$ordered_result[$delivery->country_id] = $delivery->description;
			}
		}

		return ($result) ? $ordered_result : FALSE;
	}

    public function saveManagerDelivery($manager_id,$country_id,$payments_description)
    {
        $result = $this->db->query(
            "SELECT description, country_id
			FROM manager_pricelists
			WHERE
				manager_id = $manager_id
				AND country_id = $country_id"
        )->result();

        if ($result)
        {
            // update
            $data = array('description' => $payments_description);
            $where = "manager_id = $manager_id AND country_id = $country_id";
            $str = $this->db->update_string('manager_pricelists', $data, $where);
            $result = $this->db->query($str);
        }else{
            // insert
            $data = array('manager_id' => $manager_id, 'country_id' => $country_id, 'description' => $payments_description);
            $str = $this->db->insert_string('manager_pricelists', $data);
            $result = $this->db->query($str);
        }

        return ($result) ? $result : FALSE;

    }

	public function isOrdersLimitReached($manager_id)
	{
		$active_orders = $this->db->query(
			"SELECT COUNT(*) as c
			FROM orders
			WHERE
				order_manager = $manager_id
				AND order_status IN ('proccessing', 'not_available', 'not_payed', 'payed', 'not_delivered')"
		)->result();

		if ($active_orders AND count($active_orders))
		{
			$active_orders = $active_orders[0];
			$active_orders = $active_orders->c;
		}
		else
		{
			$acttive_orders = 0;
		}

		$manager_limit = $this->db->query(
			"SELECT manager_max_orders
			FROM managers
			WHERE
				manager_user = $manager_id"
		)->result();

		if ($manager_limit AND count($manager_limit))
		{
			$manager_limit = $manager_limit[0];
			$manager_limit = $manager_limit->manager_max_orders;
		}
		else
		{
			$manager_limit = 0;
		}

		if ($manager_limit == NULL)
		{
			return FALSE;
		}

		return (($manager_limit - $active_orders) <= 0);
	}
	
	public static function getFullName($manager, $user = NULL)
	{
		$fullname = '';

        if ($manager)
        {
            $fullname = trim($manager->manager_name);

            if (empty($fullname) AND isset($manager->user_login))
            {
                $fullname = $manager->user_login;
            }
        }
		
		return $fullname;
	}
	
	public function getStatistics($manager_id)
	{
		$statistics = $this->getById($manager_id);

		// counters
		$result = $this->db->query(
			"SELECT 
				COUNT(orders.order_id) completed_orders
			FROM `{$this->table}`
				LEFT JOIN `orders` ON `orders`.`order_manager` = `{$this->table}`.`manager_user`
			WHERE 
				`{$this->table}`.`manager_user` = {$manager_id}
				AND `orders`.`order_status` = 'completed'
			GROUP BY
				`{$this->table}`.`manager_user`"
		)->result();
		
		$completed_orders = ($result) ? $result[0] : FALSE;
		$statistics->completed_orders = 0;
		
		if ($completed_orders)
		{
			$statistics->completed_orders = $completed_orders->completed_orders;
		}
		
		// login
		$result = $this->db->query(
			"SELECT 
				user_login login, 
				user_email email,
				positive_reviews,
				neutral_reviews,
				negative_reviews
			FROM `{$this->table}`
				LEFT JOIN `users` ON `users`.`user_id` = `{$this->table}`.`manager_user`
			WHERE 
				`{$this->table}`.`manager_user` = {$manager_id}"
		)->result();
		
		$user = ($result) ? $result[0] : FALSE;
		
		if ($user)
		{
            $statistics->login = $user->login;
			$statistics->email = $user->email;
			$statistics->positive_reviews = $user->positive_reviews;
			$statistics->neutral_reviews = $user->neutral_reviews;
			$statistics->negative_reviews = $user->negative_reviews;
		}

		$statistics->fullname = $this->getFullName($statistics);

		$result = $this->db->query(
			"SELECT 
				countries.*
			FROM countries
			WHERE 
				countries.country_id = {$statistics->manager_country}"
		)->result();
		
		$country = ($result) ? $result[0] : FALSE;
		
		if ($country)
		{
			$statistics->currency = $country->country_currency;
		}
		
		return $statistics;
	}
}
?>