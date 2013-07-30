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
class ClientModel extends BaseModel implements IModel{

	protected 	$properties			= null;				// array of properties
	protected	$table				= 'clients';		// table name
	protected	$PK					= 'client_user';	// primary key name	
	
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

 		$this->properties->client_name			='';
    	$this->properties->client_user			='';
    	$this->properties->client_surname		='';
    	$this->properties->client_otc			='';
    	$this->properties->client_country		='';
    	$this->properties->client_town			='';
    	$this->properties->client_index			='';
    	$this->properties->client_address		='';
    	$this->properties->client_phone_country ='';
    	$this->properties->client_phone_city	='';
    	$this->properties->client_phone_value	='';
    	$this->properties->client_phone			='';
    	$this->properties->skype			    ='';
        $this->properties->notifications_on	    ='';
        $this->properties->about_me     		='';

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
	public function addClientData($user_id, $client_obj){
		
		$props = $this->getPropertyList();
		
		foreach ($props as $prop){
			if (isset($client_obj->$prop)){
				$this->_set($prop, $client_obj->$prop);
			}
		}
		
		$this->_set($this->getPK(), $user_id);
		
		/**
		 * if primary key of table is not AI,
		 * insert_id will return false
		 */
		$this->save(true);
		
		if ($user_id){
			return $this->getInfo(array($user_id));
		}
		
		return false;
	}
	
	/**
	 * Get client by id
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
	 * Get client list by manager id
	 *
	 */
	public function getClientsByManagerId($uid)
	{
		$row = $this->db->query('
			SELECT `clients`.*
			FROM `clients`
			INNER JOIN `c2m` on `clients`.`client_user` = `c2m`.`client_id`
			WHERE `c2m`.`manager_id` = '.intval($uid).'
		')->result();

		return $row;
	}

	public function getClients($filter = null) 
	{
		$managerFilter = '';
		$countryFilter = '';
		$clientIdFilter = '';
		$clientLoginFilter = '';
		$packagePeriodFilter = '';
		$orderPeriodFilter = '';
		
		// обработка фильтра
		if (isset($filter))
		{
			if (is_numeric($filter->manager_user))
			{
				$managerFilter = ' AND `managers`.`manager_user` = \''.$filter->manager_user.'\'';
			}

			if (is_numeric($filter->client_country))
			{
				$countryFilter = ' AND `clients`.`client_country` = \''.$filter->client_country.'\'';
			}

			if ($filter->id_type == 'login')
			{
				$clientLoginFilter = ' AND `users`.`user_login` = \''.$filter->search_client.'\'';				
			}

			if ($filter->id_type == 'client_number')
			{
				$clientIdFilter = ' AND `clients`.`client_user` = \''.$filter->search_client.'\'';				
			}

			if ($filter->period == 'day' ||
				$filter->period == 'week' ||
				$filter->period == 'month')
			{
				$packagePeriodFilter = ' AND TIMESTAMPDIFF('.strtoupper($filter->period).', `packages`.`package_date`, NOW()) < 1';
				$orderPeriodFilter = ' AND TIMESTAMPDIFF('.strtoupper($filter->period).', `orders`.`order_date`, NOW()) < 1';
			}
		}
	
		// выборка
		return $this->db->query('
			SELECT `'.$this->table.'`.*, 
				`users`.`user_login`, 
				`users`.`user_coints`, 
				p.package_count,
				p_payed.package_payed_count,
				p_sent.package_sent_count,
				o.order_count,
				o_payed.order_payed_count,
				o_sent.order_sent_count
			FROM `'.$this->table.'`
				INNER JOIN `users` ON `users`.`user_id` = `'.$this->table.'`.`client_user`				
				INNER JOIN `c2m` ON `c2m`.`client_id` = `'.$this->table.'`.`client_user`
				INNER JOIN `managers` ON `managers`.`manager_user` = `c2m`.`manager_id`
				INNER JOIN `users` AS users2 ON users2.`user_id` = `managers`.`manager_user`
				LEFT JOIN (SELECT `packages`.`package_client`,
						COUNT(`package_id`) AS package_count
						FROM `packages`
						WHERE `packages`.`package_status` <> \'deleted\''.$packagePeriodFilter.'
						GROUP BY `packages`.`package_client`) AS p
					ON p.`package_client` = `'.$this->table.'`.`client_user`
				LEFT JOIN (SELECT `packages`.`package_client`,
						COUNT(`package_id`) AS package_payed_count
						FROM `packages`
						WHERE `packages`.`package_status` = "payed"'.$packagePeriodFilter.'
						GROUP BY `packages`.`package_client`) AS p_payed
					ON p_payed.`package_client` = `'.$this->table.'`.`client_user`
				LEFT JOIN (SELECT `packages`.`package_client`,
						COUNT(`package_id`) AS package_sent_count
						FROM `packages`
						WHERE `packages`.`package_status` = "sent"'.$packagePeriodFilter.'
						GROUP BY `packages`.`package_client`) AS p_sent
					ON p_sent.`package_client` = `'.$this->table.'`.`client_user`
				LEFT JOIN (SELECT `orders`.`order_client`,
						COUNT(`order_id`) AS order_count
						FROM `orders`
						WHERE `orders`.`order_status` <> \'deleted\''.$orderPeriodFilter.'
						GROUP BY `orders`.`order_client`) AS o
					ON o.`order_client` = `'.$this->table.'`.`client_user`
				LEFT JOIN (SELECT `orders`.`order_client`,
						COUNT(`order_id`) AS order_payed_count
						FROM `orders`
						WHERE `orders`.`order_status` = "payed"'.$orderPeriodFilter.'
						GROUP BY `orders`.`order_client`) AS o_payed
					ON o_payed.`order_client` = `'.$this->table.'`.`client_user`
				LEFT JOIN (SELECT `orders`.`order_client`,
						COUNT(`order_id`) AS order_sent_count
						FROM `orders`
						WHERE `orders`.`order_status` = "sended"'.$orderPeriodFilter.'
						GROUP BY `orders`.`order_client`) AS o_sent
					ON o_sent.`order_client` = `'.$this->table.'`.`client_user`
			WHERE `users`.`user_deleted` = 0 AND users2.`user_deleted` = 0'
				.$countryFilter
				.$managerFilter
				.$clientLoginFilter
				.$clientIdFilter.
			' GROUP BY `users`.`user_id`'
		)->result();
	}
	
	public function getClientsCount() 
	{
		$r = $this->db->query('
			SELECT COUNT(*) AS count
			FROM `'.$this->table.'`
				INNER JOIN `users` ON `users`.`user_id` = `'.$this->table.'`.`client_user`				
			WHERE `users`.`user_deleted` = 0'
		)->result();
		
		return ((count($r==1) &&  $r) ? $r[0]->count : false);
	}
	
	public function autocomplete($query) 
	{
		$r = $this->db->query('
			SELECT GROUP_CONCAT(`client_user` SEPARATOR ",") AS ids
			FROM (
				SELECT `client_user` 
				FROM `'.$this->table."`
				WHERE `client_user` LIKE '$query%'
				LIMIT 10) 
			AS A"			
		)->result();
		
		return ((count($r == 1) &&  $r) ? $r[0]->ids : false);
	}
	
	public function autocompleteManager($query, $manager_id) 
	{
		$r = $this->db->query("
			SELECT GROUP_CONCAT(`client_user` SEPARATOR ',') AS ids
			FROM (
				SELECT `client_user` 
				FROM `{$this->table}`
				INNER JOIN `c2m` 
					ON `c2m`.`client_id` =  `{$this->table}`.`client_user`
				WHERE `client_user` LIKE '$query%'
					AND `c2m`.`manager_id` = {$manager_id}
				LIMIT 10) 
			AS A"			
		)->result();
		
		return ((count($r == 1) &&  $r) ? $r[0]->ids : false);
	}
	
	public function getClientById($uid) 
	{
		$r = $this->db->query('
			SELECT `'.$this->table.'`.*, `users`.`user_email`
			FROM `'.$this->table.'`
				INNER JOIN `users` ON `users`.`user_id` = `'.$this->table.'`.`client_user`				
			WHERE `users`.`user_deleted` = 0
				AND `users`.`user_id` = "'.$uid.'"'
		)->result();
		
		return ((count($r==1) &&  $r) ? $r[0] : false);
	}
	
	public function updateClient($user_obj) {
		$props = $this->getPropertyList();

		foreach ($props as $prop){
			if (isset($user_obj->$prop) && !empty($user_obj->$prop)){
				$this->_set($prop, $user_obj->$prop);
			}
            else if ($prop != 'client_country')
            {
                $this->db->set($prop, null);
            }
		}

		$new_id = $this->save(true);

		if (!$new_id) return false;
		
		return $this->getInfo(array($user_obj->client_user));
	}
	
	public function getFullName($statistics)
	{
		$fullname = '';

		/*if ( ! empty($statistics))
		{
			$fullname = trim($statistics->client_surname . ' ' . 
			$statistics->client_name . ' ' . 
			$statistics->client_otc);

			if (empty($fullname))
			{
				$ci = get_instance();
				$ci->load->model('UserModel', 'Users');
		
				$user = $ci->Users->getById($statistics->client_user);
                if (empty($fullname) AND isset($user->user_login))
                {
                    $fullname = $user->user_login;
                }
			}
		}*/
        $fullname = $statistics->client_name;
		return $fullname;
	}
	
	public function getStatistics($client_id)
	{
		$statistics = $this->getById($client_id);

        $statistics->client_surname = "";
        $statistics->client_name = "";
        $statistics->client_otc = "";
        $statistics->client_user = $client_id;
        $statistics->login = "";

		// login
		$result = $this->db->query(
			"SELECT 
				user_login login, 
				user_email email,
				positive_reviews,
				neutral_reviews,
				negative_reviews
			FROM `{$this->table}`
				LEFT JOIN `users` ON `users`.`user_id` = `{$this->table}`.`client_user`
			WHERE 
				`{$this->table}`.`client_user` = {$client_id}"
		)->result();

		$user = ($result) ? $result[0] : FALSE;

        // counters
        $result = $this->db->query(
                "SELECT count(order_id) order_count
                  FROM orders
                  WHERE order_client = {$client_id}"
        )->result();
        $order_count = ($result) ? $result[0] : FALSE;
        $statistics->order_count = 0;
        $statistics->order_count = $order_count->order_count;

		if ($user)
		{
            $statistics->login = $user->login;
			$statistics->email = $user->email;
			$statistics->positive_reviews = $user->positive_reviews;
			$statistics->neutral_reviews = $user->neutral_reviews;
			$statistics->negative_reviews = $user->negative_reviews;
		}

        $statistics->fullname = $this->getFullName($statistics);

        return $statistics;
	}
	
	public function getClientsData($filters = null) 
	{
		$where = '';
		
		if (!empty($filters->country_from) OR
			!empty($filters->client_id) OR
			!empty($filters->login)) :
			$where = ((!empty($filters->country_from)) ? ' AND `'.$this->table.'`.client_country = '.$this->db->escape($filters->country_from).' ' : '').
				((!empty($filters->client_id)) ? ' AND `'.$this->table.'`.client_user = '.$this->db->escape($filters->client_id).' ' : '').
				((!empty($filters->login)) ? ' AND `users`.user_login LIKE '."'%".$filters->login."%'".' ' : '');
		endif;
		
		return $this->db->query('
			SELECT `'.$this->table.'`.*, 
				`users`.`user_login`, 
				`users`.`user_coints`, 
				12345 AS `clients_count`,
				`countries`.`country_currency`
			FROM `'.$this->table.'`
				INNER JOIN `users` ON `users`.`user_id` = `'.$this->table.'`.`client_user`
				INNER JOIN `countries` ON `countries`.`country_id` = `'.$this->table.'`.`client_country`				
			WHERE `users`.`user_deleted` = 0 '.$where.'
			GROUP BY `'.$this->table.'`.`client_user`
		')->result();
	}
}
?>