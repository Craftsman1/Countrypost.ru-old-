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
    	$this->properties->manager_max_clients		='';
    	$this->properties->manager_max_orders		='';
    	$this->properties->manager_name				='';
    	$this->properties->manager_surname			='';
    	$this->properties->manager_otc				='';
    	$this->properties->manager_addres			='';
    	$this->properties->manager_address_local	='';
    	$this->properties->manager_address_description ='';
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
		$this->properties->min_order_tax			='';
		$this->properties->join_tax					='';
		$this->properties->foto_tax					='';
		$this->properties->insurance_tax			='';
		$this->properties->package_tax				='';
		$this->properties->package_disconnected_tax	='';
		$this->properties->package_foto_tax			='';
		$this->properties->package_foto_system_tax	='';
		$this->properties->pricelist_description	='';
		$this->properties->payments_description		='';
		$this->properties->rating	='';
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
		
		if (!empty($filters->country_from) OR
			!empty($filters->is_mail_forwarding) OR
			!empty($filters->is_cashback)) :
			$where = ((!empty($filters->country_from)) ? ' AND `'.$this->table.'`.manager_country = '.$filters->country_from.' ' : '').
				((!empty($filters->is_mail_forwarding)) ? ' AND `'.$this->table.'`.is_mail_forwarding = 1 ' : '').
				((!empty($filters->is_cashback)) ? ' AND `'.$this->table.'`.is_cashback = 1 ' : '');
		endif;

		return $this->db->query('
			SELECT `'.$this->table.'`.*, 
				`users`.`user_login`, 
				`users`.`user_coints`, 
				COUNT(c2m.manager_id) AS `clients_count`,
				`currencies`.`currency_symbol`
			FROM `'.$this->table.'`
				LEFT JOIN `c2m` ON `c2m`.`manager_id` = `'.$this->table.'`.`manager_user`
				INNER JOIN `users` ON `users`.`user_id` = `'.$this->table.'`.`manager_user`				
				INNER JOIN `countries` ON `countries`.`country_id` = `'.$this->table.'`.`manager_country`				
				INNER JOIN `currencies` ON `currencies`.`currency_name` = `countries`.`country_currency`
			WHERE `users`.`user_deleted` = 0 '.$where.'
			GROUP BY `'.$this->table.'`.`manager_user`
		')->result();
	}
	
	public function getManagerData($id) {
		$result = $this->db->query('
			SELECT `'.$this->table.'`.*, `users`.`user_login`, `users`.`user_email`, `users`.`user_coints`, COUNT(c2m.manager_id) AS `clients_count`
			FROM `'.$this->table.'`
				LEFT JOIN `c2m` ON `c2m`.`manager_id` = `'.$this->table.'`.`manager_user`
				INNER JOIN `users` ON `users`.`user_id` = `'.$this->table.'`.`manager_user`				
			WHERE `users`.`user_deleted` = 0 AND `users`.`user_id` = '.$id.'
			GROUP BY `'.$this->table.'`.`manager_user`
		')->result();
		
		return ((count($result==1) &&  $result) ? array_shift($result) : FALSE);
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
			if (isset($manager_obj->$prop))
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
	 * Список партенров с незаполненными до максимума клиентами по странам
	 */
	public function getIncompleteManagers() {
		$managers = array();
	 
		$rows = $this->db->query('
			SELECT *
			FROM 	(
						SELECT `'.$this->table.'`.`manager_user`, `'.$this->table.'`.`manager_country`, `'.$this->table.'`.`manager_max_clients`, COUNT(c2m.manager_id) AS `clients_count`, `'.$this->table.'`.`last_client_added`
						FROM `'.$this->table.'`
							LEFT JOIN `c2m` ON `c2m`.`manager_id` = `'.$this->table.'`.`manager_user`
						WHERE `'.$this->table.'`.`manager_status` = 1
						GROUP BY `'.$this->table.'`.`manager_user`
					) AS `managers_data`
			WHERE manager_max_clients > clients_count
		')->result();
		
		foreach ($rows as $row) {
			if (!array_key_exists($row->manager_country, $managers) || $managers[$row->manager_country]->last_client_added > $row->last_client_added)
				$managers[$row->manager_country] = $row;
		}
		return $managers;
	}
	
	public function getCompleteManagers($ids) {
		$managers = array();
		$rows = $this->db->query('
			SELECT `'.$this->table.'`.`manager_user`, 
				`'.$this->table.'`.`manager_country`, 
				`'.$this->table.'`.`manager_max_clients`, 
				COUNT(c2m.manager_id) AS `clients_count`, 
				`'.$this->table.'`.`last_client_added`
				FROM `'.$this->table.'`
					LEFT JOIN `c2m` ON `c2m`.`manager_id` = `'.$this->table.'`.`manager_user`
				WHERE `'.$this->table.'`.`manager_status` = 1 AND `'.$this->table.'`.`manager_country` IN ('.implode(', ', $ids).')
				GROUP BY `'.$this->table.'`.`manager_user`
		')->result();
		
		foreach ($rows as $row) 
		{
			if (!array_key_exists($row->manager_country, $managers) || $managers[$row->manager_country]->last_client_added > $row->last_client_added)
			{
				$managers[$row->manager_country] = $row;
			}
		}
		
		return $managers;
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
	
	public function getActiveManagers() {		
		$result = $this->select(array('manager_status' => '1'));
		
		return ((count($result) > 0 &&  $result) ? $result : FALSE);		
	}
	
	public function makePaymentLocal($manager, $amount) 
	{		
		$manager->manager_balance_local += $amount;
		
		return $this->updateManager($manager);
	}
	
	public function getClientManagersById($client_id)
	{
		$result = $this->db->query('
			SELECT DISTINCT `'.$this->table.'`.*, `users`.`user_login`, `countries`.`country_name`
			FROM `'.$this->table.'`
				LEFT JOIN `c2m` ON `c2m`.`manager_id` = `'.$this->table.'`.`manager_user`
				INNER JOIN `users` ON `users`.`user_id` = `'.$this->table.'`.`manager_user`				
				INNER JOIN `countries` ON `countries`.`country_id` = `'.$this->table.'`.`manager_country`
				INNER JOIN `pricelist` pl ON `pl`.`pricelist_country_from` = `managers`.`manager_country` and `pl`.`pricelist_country_to` = (
					SELECT client_country FROM clients WHERE client_user='.$client_id.'
				)
			WHERE `users`.`user_deleted` = 0
				AND `c2m`.`client_id` = \''.$client_id.'\'
		')->result();
		return ($result) ? $result : FALSE;		
	}
	
	public function fixMaxClientsCount($manager_id)
	{
		$manager = $this->db->query('
			SELECT `'.$this->table.'`.*, COUNT(c2m.manager_id) AS `clients_count`
			FROM `'.$this->table.'`
				LEFT JOIN `c2m` ON `c2m`.`manager_id` = `'.$this->table.'`.`manager_user`
				INNER JOIN `users` ON `users`.`user_id` = `'.$this->table.'`.`manager_user`				
			WHERE `users`.`user_deleted` = 0 AND `users`.`user_id` = \''.$manager_id.'\'
			GROUP BY `'.$this->table.'`.`manager_user`
		')->result();
		
		if ($manager && count($manager) == 1)
		{
			$manager = $manager[0];
			if ($manager->clients_count > $manager->manager_max_clients)
			{
				$manager->manager_max_clients = $manager->clients_count;
				unset($manager->clients_count);
				$manager = $this->updateManager($manager);
			}
		
			return $manager;
		}
		
		return FALSE;		
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
		$fullname = trim("{$manager->manager_surname} {$manager->manager_name}  {$manager->manager_otc}");

		if (empty($fullname))
		{
			$fullname = $manager->user_login;
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
				AND `orders`.`order_status` IN ('sended')
			GROUP BY
				`{$this->table}`.`manager_user`"
		)->result();
		
		$completed_orders = ($result) ? $result[0] : FALSE;
		$statistics->completed_orders = FALSE;
		
		if ($completed_orders)
		{
			$statistics->completed_orders = $completed_orders->completed_orders;
		}
		
		// login
		$result = $this->db->query(
			"SELECT 
				user_login login, 
				user_email email
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