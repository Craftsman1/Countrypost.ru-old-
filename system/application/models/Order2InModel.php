<?
require_once(MODELS_PATH.'Base/BaseModel.php');

class Order2InModel extends BaseModel implements IModel{

	protected 	$properties			= null;				// array of properties
	protected	$table				= 'orders2in';			// table name
	protected	$PK					= 'order2in_id';		// primary key name	
	
	private $statuses = array(
		'processing'		=> 'Обрабатывается',
		'payed'				=> 'Выплачена',
		'not_delivered'		=> 'Не получено',
		'no_screenshot'		=> 'Нет скриншота'
	);
	
	/**
	 * конструктор
	 *
	 */
	function __construct()
    {
    	$this->properties	= new stdClass();
    	$this->properties->order2in_id				= '';
    	$this->properties->order_id					= '';
    	$this->properties->order2in_user			= '';
    	$this->properties->order2in_amount			= '';
    	$this->properties->order2in_amount_local	= '';
    	$this->properties->order2in_createtime		= '';
    	$this->properties->order2in_lastchange		= '';
    	$this->properties->order2in_status			= '';
    	$this->properties->order2in_2clientcomment	= '';
    	$this->properties->order2in_2admincomment	= '';
    	$this->properties->order2in_isnew			= '';
		$this->properties->order2in_payment_service	= '';
		$this->properties->payment_service_name		= '';
		$this->properties->order2in_from			= '';
		$this->properties->order2in_to				= '';
		$this->properties->order2in_details			= '';
		$this->properties->order2in_currency		= '';
		$this->properties->order_id					= '';
		$this->properties->is_countrypost			= '';
		$this->properties->is_money_sent			= '';
		$this->properties->excess_amount			= '';

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
	
	public function getById($id){
		$r = $this->select(array(
			$this->getPK()	=> (int) $id,
		));					
		
		return ((count($r==1) &&  $r) ? array_shift($r) : FALSE);
	}
	
	public function getClientsO2iById($id, $client_id)
	{
		$o2o = $this->getById($id);
		
		if ($o2o && $o2o->order2in_user == $client_id)
		{
			return $o2o;
		}
		
		return FALSE;
	}
	
	public function getManagersO2iById($id, $manager_id)
	{
		$o2o = $this->getById($id);

		if ($o2o &&
			$o2o->order2in_to == $manager_id &&
			$o2o->is_countrypost == 0)
		{
			return $o2o;
		}

		return FALSE;
	}

	public function addOrder($order_obj) {
		$props = $this->getPropertyList();
		foreach ($props as $prop){
			if (isset($order_obj->$prop)){
				$this->_set($prop, $order_obj->$prop);
			}
		}
		
		$new_id = $this->save(true);
		
		if ($new_id){
			return $this->getInfo(array($new_id));
		}
		
		return FALSE;
	}
	
	public function getMaxId() {
		return $this->db->query('
			SELECT MAX(`order2in_id`) AS `max`
			FROM `'.$this->table.'`
		')->result();
	}
	
	/*public function getUserOrders($user_id) {
		return $this->db->query('
			SELECT *
			FROM `'.$this->table.'`
			WHERE `order2in_user` = '.intval($user_id).'
			ORDER BY `order2in_id` DESC
		')->result();
	}*/
	
	public function getStatuses() {
		return $this->statuses;
	}
	
	public function updateStatus($order_id, $new_status)
	{
		$this->_set($this->PK, (int) $order_id);
		$this->_set('order2in_status', $new_status);

		if ($new_status == 'payed')
		{
			$this->_set('is_money_sent', 1);
		}

		return $this->save();
	}
	
	public function updateAmount($order_id, $new_amount)
	{
		$this->_set($this->PK, (int) $order_id);
		$this->_set('order2in_amount', $new_amount);

		return $this->save();
	}

	public function updateAmountLocal($order_id, $new_amount_local)
	{
		$this->_set($this->PK, (int) $order_id);
		$this->_set('order2in_amount_local', $new_amount_local);

		return $this->save();
	}

	public function updateCommentStatus($Order2InId, $new_status, $usertype){
		
		$o2i	= $this->getById((int) $Order2InId);
		if ($o2i)
		{
			$this->_set($this->PK, (int) $Order2InId);
			$this->_set("order2in_2{$usertype}comment", (int) $new_status);
			return $this->save(true);
		}
	}
	
	public function unsetOrder2InNewStatus($Order2InId){
		
		$this->_set($this->PK, (int) $Order2InId);
		$this->_set("order2in_isnew", 0);
		return $this->save();
	}

	private function initWhere($filter, $from = NULL, $to = NULL)
	{
		$where = '1';

		// обход полей фильтра
		if (is_string($filter))
		{
			$where	= $filter;
		}
		else
		{
			foreach ($filter as $key=>$val)
			{
				if ($key == 'like' AND
					is_array($val))
				{
					foreach ($val as $key1 => $val1)
					{
						$where .= " AND $key1 LIKE '%$val1%'";
					}
				}
				else
				{
					$where .= " AND $key = '$val'";
				}
			}
		}

		// фильтр дат
		if ($from AND $to)
		{
			$from_date = DateTime::createFromFormat('j.m.Y', $from);
			$from_date = $from_date->format('Y-m-d H:i:s');
			$to_date = DateTime::createFromFormat('j.m.Y', $to);
			$to_date->modify('+1 day');
			$to_date = $to_date->format('Y-m-d H:i:s');

			$where .= " AND `order2in_createtime` BETWEEN '$from_date' AND '$to_date'";
		}
		else if ($from)
		{
			$from_date = DateTime::createFromFormat('j.m.Y', $from);
			$from_date = $from_date->format('Y-m-d H:i:s');

			$where .= " AND `order2in_createtime` >= '$from_date'";
		}
		else if ($to)
		{
			$to_date = DateTime::createFromFormat('j.m.Y', $to);
			$to_date->modify('+1 day');
			$to_date = $to_date->format('Y-m-d H:i:s');

			$where .= " AND `order2in_createtime` < '$to_date'";
		}

		return $where;
	}

	public function getFilteredOrders($filter = array(), $status = NULL, $from = NULL, $to = NULL)
	{
		$where = $this->initWhere($filter, $from, $to);

		if (isset($status))
		{
			if ($status == 'open')
			{
				$where .= " AND `order2in_status` IN ('not_delivered', 'processing', 'no_screenshot')";
			}
			else
			{
				$where .= " AND `order2in_status` = '$status'";
			}
		}
		else
		{
			$where .= " AND `order2in_status` <> 'deleted'";
		}

		$result = $this->db->query('
			SELECT `'.$this->table.'`.*,
				`users`.`user_login`, 
				`clients`.`client_name`, 
				`clients`.`client_otc`, 
				`clients`.`client_surname`
			FROM `'.$this->table.'`
				INNER JOIN `users` ON `'.$this->table.'`.`order2in_user` = `users`.`user_id` 
				INNER JOIN `clients` ON `'.$this->table.'`.`order2in_user` = `clients`.`client_user` 
			WHERE '.$where.'
				AND `'.$this->table.'`.`order2in_status` <> "deleted"
			ORDER BY `order2in_id` DESC
		')->result();

		return ((count($result == 1) AND $result) ? $result : FALSE);
	}
	
	public function getCounters($order_id, $user_id, $user_group, $filter = NULL)
	{
		$role = ucfirst($user_group);
		$method = "get{$role}Counters";

		return $this->$method($order_id, $user_id, $filter);
	}

	protected function getClientCounters($order_id, $client_id)
	{
		$where_open = "`order2in_status` IN ('not_delivered', 'processing', 'no_screenshot')";

		$open = $this->db->query("
			SELECT COUNT(*) AS 'counter'
			FROM `{$this->table}`
				INNER JOIN `users` ON `{$this->table}`.`order2in_user` = `users`.`user_id`
				INNER JOIN `clients` ON `{$this->table}`.`order2in_user` = `clients`.`client_user`
			WHERE $where_open
				AND `{$this->table}`.`order2in_user` = '$client_id'
				AND `{$this->table}`.`order_id` = '$order_id'
		")->result();

		$result['open'] = (count($open == 1) AND $open) ?
			$open[0]->counter :
			0;

		$where_payed = "`order2in_status` = 'payed'";

		$payed = $this->db->query("
			SELECT COUNT(*) AS 'counter'
			FROM `{$this->table}`
				INNER JOIN `users` ON `{$this->table}`.`order2in_user` = `users`.`user_id`
				INNER JOIN `clients` ON `{$this->table}`.`order2in_user` = `clients`.`client_user`
			WHERE $where_payed
				AND `{$this->table}`.`order2in_user` = '$client_id'
				AND `{$this->table}`.`order_id` = '$order_id'
		")->result();

		$result['payed'] = (count($payed == 1) AND $payed) ?
			$payed[0]->counter :
			0;

		return $result;
	}

	protected function getManagerCounters($order_id, $manager_id)
	{
		$where_open = "`order2in_status` IN ('not_delivered', 'processing', 'no_screenshot')";
		$where_payed = "`order2in_status` = 'payed'";

		if ($order_id)
		{
			$where_open .= " AND `{$this->table}`.`order_id` = '$order_id'";
			$where_payed .= " AND `{$this->table}`.`order_id` = '$order_id'";
		}

		$open = $this->db->query("
			SELECT COUNT(*) AS 'counter'
			FROM `{$this->table}`
				INNER JOIN `users` ON `{$this->table}`.`order2in_to` = `users`.`user_id`
				INNER JOIN `managers` ON `{$this->table}`.`order2in_to` = `managers`.`manager_user`
			WHERE $where_open
				AND `{$this->table}`.`order2in_to` = '$manager_id'
				AND `{$this->table}`.`is_countrypost` = 0
		")->result();

		$result['open'] = (count($open == 1) AND $open) ?
			$open[0]->counter :
			0;

		$payed = $this->db->query("
			SELECT COUNT(*) AS 'counter'
			FROM `{$this->table}`
				INNER JOIN `users` ON `{$this->table}`.`order2in_to` = `users`.`user_id`
				INNER JOIN `managers` ON `{$this->table}`.`order2in_to` = `managers`.`manager_user`
			WHERE $where_payed
				AND `{$this->table}`.`order2in_to` = '$manager_id'
				AND `{$this->table}`.`is_countrypost` = 0
		")->result();

		$result['payed'] = (count($payed == 1) AND $payed) ?
			$payed[0]->counter :
			0;

		return $result;
	}

	protected function getAdminCounters($order_id, $user_id, $filter)
	{
		$where_filter = $this->initWhere($filter->condition,
			$filter->from,
			$filter->to);

		$where_open = "`order2in_status` IN ('not_delivered', 'processing', 'no_screenshot')";
		$where_payed = "`order2in_status` = 'payed'";

		$open = $this->db->query("
			SELECT COUNT(*) AS 'counter'
			FROM `{$this->table}`
			WHERE $where_open AND $where_filter
		")->result();

		$result['open'] = (count($open == 1) AND $open) ?
			$open[0]->counter :
			0;

		$payed = $this->db->query("
			SELECT COUNT(*) AS 'counter'
			FROM `{$this->table}`
			WHERE $where_payed AND $where_filter
		")->result();

		$result['payed'] = (count($payed == 1) AND $payed) ?
			$payed[0]->counter :
			0;

		return $result;
	}

	public function getOrdersByIds($ids) {
		return $this->db->query('
			SELECT `'.$this->table.'`.*
			FROM `'.$this->table.'`
			WHERE `order2in_id` IN('.implode(', ', $ids).')
			ORDER BY `order2in_id` DESC
		')->result();
	}
	
	/**
	 * Получить список фоток для каждой заявки
	 *
	 * @param object $arrayOfOrderObject
	 */
	public function getOrders2InFoto($arrayOfOrder2InObject)
	{
		if (empty($arrayOfOrder2InObject) OR
			! is_array($arrayOfOrder2InObject))
		{
			return array();
		}
		
		$o2iFotos	= array();
		foreach ($arrayOfOrder2InObject as $o2i){
			$scandir	= UPLOAD_DIR.'orders2in/'.$o2i->order2in_id.'/';
			if (is_dir($scandir)){
				foreach (scandir($scandir) as $scanFile){
					if ($scanFile != '.' && $scanFile != '..'){
						$o2iFotos[$o2i->order2in_id][]	= $scanFile;
					}
				}
			}
		}
		
		return $o2iFotos;
	}
}
?>