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
class OdetailModel extends BaseModel implements IModel{

	protected  $properties			= null;				// array of properties
	protected  $table				= 'odetails';			// table name
	protected  $PK					= 'odetail_id';		// primary key name	

	private $statuses = array(
		'processing' => 'Обрабатывается',//'Ждем прибытия',
		'available' => 'Есть в наличии',///'Не оплачено','Получено'
		'not_available' => 'Нет в наличии',
		'not_available_color' => 'Нет данного цвета',
		'not_available_size' => 'Нет данного размера',
		'not_available_count' => 'Нет указанного кол-ва',
		'bought' => 'Выкуплен',
		'sent_by_seller' => 'Отправлен продавцом',
		'completed' => 'Получен',
		'exchange' => 'Обмен',
		'return' => 'Возврат',
		'deleted' => 'Удален'
		);

	private $online_statuses = array(
		'processing' => 'Обрабатывается',
		'available' => 'Есть в наличии',
		'not_available' => 'Нет в наличии',
		'not_available_color' => 'Нет данного цвета',
		'not_available_size' => 'Нет данного размера',
		'not_available_count' => 'Нет указанного кол-ва',
		'bought' => 'Выкуплен',
		'sent_by_seller' => 'Отправлен продавцом',
		'completed' => 'Получен',
		'exchange' => 'Обмен',
		'return' => 'Возврат'
	);

	private $offline_statuses = array(
		'processing' => 'Обрабатывается',
		'available' => 'Есть в наличии',
		'not_available' => 'Нет в наличии',
		'not_available_color' => 'Нет данного цвета',
		'not_available_size' => 'Нет данного размера',
		'not_available_count' => 'Нет указанного кол-ва',
		'bought' => 'Выкуплен',
		'sent_by_seller' => 'Отправлен продавцом',
		'completed' => 'Получен',
		'exchange' => 'Обмен',
		'return' => 'Возврат'
	);

	private $service_statuses = array(
		'processing' => 'Обрабатывается',
		'available' => 'Не оплачено',
		'completed' => 'Выполнено'
	);

	private $delivery_statuses = array(
		'processing' => 'Ждем прибытия',
		'available' => 'Получено'
	);

	private $mail_forwarding_statuses = array(
		'processing' => 'Ждем прибытия',
		'available' => 'Получено',
		'exchange' => 'Обмен',
		'return' => 'Возврат'
	);

	// дублирует код из модели заказа. пока оставляем на правах кэширования этого массива
	private $order_types = array(
		'offline',
		'online',
		'service',
		'delivery',
		'mail_forwarding'
	);

	/**
	 * конструктор
	 *
	 */
	function __construct()
    {
    	$this->properties	= new stdClass();
    	$this->properties->odetail_id				='';
    	$this->properties->odetail_client			='';
    	$this->properties->odetail_manager			='';
    	$this->properties->odetail_order			='';
    	$this->properties->odetail_country			='';
    	$this->properties->odetail_link				='';
    	$this->properties->odetail_product_name		='';
    	$this->properties->odetail_product_color	='';
    	$this->properties->odetail_product_size		='';
    	$this->properties->odetail_product_amount	='';
    	$this->properties->odetail_comment			='';
    	$this->properties->odetail_status			='';
		$this->properties->odetail_price			=0;
		$this->properties->odetail_pricedelivery    =0;
    	$this->properties->odetail_img				='';
    	$this->properties->odetail_joint_id			=0;
    	$this->properties->odetail_weight			=0;
 		$this->properties->updated_by_client		=0;
 		$this->properties->odetail_foto_requested	=0;

		parent::__construct();
    }

	public function getAllStatuses()
	{
		$statuses = array();

		foreach ($this->order_types as $type)
		{
			$name = "{$type}_statuses";
			$statuses[$type] = $this->$name;
		}

		return $statuses;
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
	
	public function addOdetail($odetail_obj) {
		$props = $this->getPropertyList();
		
		foreach ($props as $prop){
			if (isset($odetail_obj->$prop)){
				$this->_set($prop, $odetail_obj->$prop);
			}
			else
			{
				$this->db->set($prop, null);
			}
		}
		
		$new_id = $this->save(true);
		
		if ($new_id)
		{
			return $this->getById($new_id);
		}
		
		return FALSE;
	}
	
	public function updateOdetail($odetail_obj) {
		$props = $this->getPropertyList();
		
		foreach ($props as $prop)
		{
			$this->db->set($prop, $odetail_obj->$prop);	
		}
		
		$this->db->where($this->getPK(), $odetail_obj->odetail_id);
		$result = $this->db->update($this->table);
		
		if (isset($result) && $result)
		{
			return $result;
		}
		
		return FALSE;
	}
	
	public function getFilteredDetails($filter) {
		
		$where = 1;
		if (count($filter)) {
			$where = '';			
			foreach ($filter as $key=>$val) {
				$where .= "`$key` = '$val' AND ";
			}
			$where = substr($where, 0, strlen($where)-5);
		}
		
		return $this->db->query('
			SELECT `'.$this->table.'`.*, `countries`.`country_name`, `countries`.`country_id`
			FROM `'.$this->table.'`
				INNER JOIN `managers` ON `'.$this->table.'`.`odetail_manager` = `managers`.`manager_user` 
				INNER JOIN `countries` ON `managers`.`manager_country` = `countries`.`country_id`
			WHERE '.$where
		)->result();
	}
	
	public function getNewDetails($odetail_client) 
	{
		return $this->db->query('
			SELECT `'.$this->table.'`.*
			FROM `'.$this->table.'`
			WHERE `odetail_order` = 0 AND `odetail_client` = '.$odetail_client
		)->result();
	}
	
	public function getUnimportedDetails($order_id, $odetail_ids)
	{
		$where = 1;
		if (count($odetail_ids))
		{
			$where = '`odetail_id` NOT IN (' . implode(', ', $odetail_ids) . ')';			
		}
		
		return $this->db->query('
			SELECT `'.$this->table.'`.*
			FROM `'.$this->table.'`
			WHERE `odetail_status` <> "deleted" AND `odetail_order` = ' . $order_id . ' AND '.$where
		)->result();
	}
	
	public function makeScreenshot($odetail_obj, $x1, $y1, $x2, $y2, $width) {
		if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/upload/orders/'.$odetail_obj->odetail_client.'/')) {
			mkdir($_SERVER['DOCUMENT_ROOT'].'/upload/orders/'.$odetail_obj->odetail_client.'/', 0777);
		}
		
		exec('wkhtmltoimage-amd64 --width '.$width.'  --crop-x '.$x1.' --crop-y '.$y1.' --crop-w '.($x2-$x1).' --crop-h '.($y2-$y1).' '.escapeshellarg($odetail_obj->odetail_link).' '.$_SERVER['DOCUMENT_ROOT'].'/upload/orders/'.$odetail_obj->odetail_client.'/'.$odetail_obj->odetail_id.'.jpg');
		
		var_dump('wkhtmltoimage-amd64 --width '.$width.'  --crop-x '.$x1.' --crop-y '.$y1.' --crop-w '.($x2-$x1).' --crop-h '.($y2-$y1).' '.escapeshellarg($odetail_obj->odetail_link).' '.$_SERVER['DOCUMENT_ROOT'].'/upload/orders/'.$odetail_obj->odetail_client.'/'.$odetail_obj->odetail_id.'.jpg');
		if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/upload/orders/'.$odetail_obj->odetail_client.'/'.$odetail_obj->odetail_id.'.jpg')) {
			throw new Exception('Ошибка создания скриншота',134);
		}
	}
	
	/**
	 * Формируем заказы юзера (ставим им статус proccessing)
	 */
	public function checkoutClientDetails($client_id, $order_id) 
	{
		return $this->db->update($this->table, array('odetail_order' => $order_id), array('odetail_client' => $client_id, 'odetail_order' => 0));
	}
	
	public function getById($id){
		$r = $this->select(array(
			$this->getPK()	=> (int) $id,
		));					
		
		return ((count($r==1) &&  $r) ? array_shift($r) : FALSE);
	}

	public function getAvailableOrderDetailsStatuses()
	{
		return $this->statuses;
	}

	/**
	 * Get order details
	 *
	 * @return array
	 */
	public function getOrderDetails($id)
	{
		$result = $this->db->query("
			SELECT
				`odetails`.*, `odetail_joints`.`cost`, `odetail_joints`.`count`
			FROM
				`odetails`
			LEFT OUTER JOIN `odetail_joints` ON
				`odetail_joints`.`joint_id` = `odetails`.`odetail_joint_id`
			WHERE
				`odetails`.`odetail_status` <> 'deleted'
				AND `odetails`.`odetail_order` = '$id'
			ORDER BY 
				`odetail_joints`.`joint_id` DESC,
				`odetails`.`odetail_id` ASC
		")->result();

		// статус джоинтов
		if (count($result) > 0 &&  $result)
		{
			$joint_statuses = array();
		
			// расчет статуса
			foreach ($result as $odetail)
			{
				if ($odetail->odetail_joint_id)
				{
					if ($odetail->odetail_status != 'purchased' &&
						$odetail->odetail_status != 'received' && 
						$odetail->odetail_status != 'not_delivered')
					{
						$joint_statuses[$odetail->odetail_joint_id] = 1;
					}
					else if (empty($joint_statuses[$odetail->odetail_joint_id]))
					{
						$joint_statuses[$odetail->odetail_joint_id] = 0;
					}
				}
			}
			
			// запись статуса
			foreach ($result as $odetail)
			{
				if ($odetail->odetail_joint_id)
				{
					$odetail->odetail_joint_enabled = $joint_statuses[$odetail->odetail_joint_id];
				}
			}
		}
		
		return ((count($result) > 0 &&  $result) ? $result : FALSE);
	}
	
	public function getOrderDetailsTotals($order_details_array)
	{    
		$totals = new stdClass();
		$totals->order_products_cost = 0; 
		$totals->order_delivery_cost = 0;
		$totals->order_product_weight = 0;
		$totals->odetail_joint_id = 0;
		$totals->odetail_joint_count = 0;
		
		foreach($order_details_array as $odetail) :			
			$totals->order_products_cost += $odetail->odetail_price;
			$totals->order_product_weight += $odetail->odetail_weight;
			
			if (!$odetail->odetail_joint_id) : 
				$totals->order_delivery_cost += $odetail->odetail_pricedelivery;
			elseif ($odetail_joint_id != $odetail->odetail_joint_id) :
				$totals->odetail_joint_id = $odetail->odetail_joint_id;
				$totals->odetail_joint_count = $odetail->odetail_joint_count;
				$totals->order_delivery_cost += $odetail->odetail_joint_cost;
			endif;
			
		endforeach; //foreach($odetails as $odetail) :
		
		return $totals;
	}

	public function clearJoints($id)
	{
		$this->db->query("
			UPDATE
				`odetails`
			SET
				`odetails`.`odetail_joint_id` = 0
			WHERE
				`odetails`.`odetail_joint_id` = '$id' AND
				`odetails`.`odetail_status` <> 'deleted'
		");
	
		$this->db->query("
			DELETE FROM `odetail_joints`
			WHERE `odetail_joints`.`joint_id` = '$id'
		");
	}

	/**
	 * Calculate order status
	 *
	 * @return array
	 */
	public function getTotalStatus($id){
		$row = $this->db->query('
			SELECT MAX(`odetails`.`odetail_status`) as `status`
			FROM `odetails`
			WHERE `odetails`.`odetail_order` = '.intval($id).'
				AND `odetails`.`odetail_status` <> "deleted"
			GROUP BY `odetails`.`odetail_order`
		')->result();
		
		if (!$row || count($row) != 1)
		{
			return 'not_available';
		}

		return $row[0]->status;		
	}
	
	public function setStatus($id, $status){
		$this->db->query('
			UPDATE `odetails` 
			SET `odetail_status` = \''.$status.'\'
			WHERE `odetails`.`odetail_id` = '.intval($id).'
		');
		
		return ;
	}

	public function getPrivilegedOdetail($order_id, $odetail_id, $user_id, $user_group)
	{
		if ($user_group == 'client')
		{
			return $this->getClientOdetailById($order_id, $odetail_id, $user_id);
		}
		else if ($user_group == 'manager')
		{
			return $this->getManagerOdetailById($order_id, $odetail_id, $user_id);
		}

		return FALSE;
	}

	public function getClientOdetailById($order_id, $odetail_id, $client_id)
	{
		$row = $this->db->query("
			SELECT `odetails`.*
			FROM `odetails`
			INNER JOIN `orders` ON `odetails`.`odetail_order` = `orders`.`order_id`
			WHERE
				`odetails`.`odetail_id` = '$odetail_id'
				AND `orders`.`order_id` = '$order_id'
				AND `orders`.`order_client` = '$client_id'
				AND `orders`.`order_status` <> 'deleted'
				AND `odetails`.`odetail_status` <> 'deleted'
			LIMIT 1
		")->result();

		if (empty($row) OR count($row) != 1)
		{
			return FALSE;
		}

		return $row[0];		
	}
	
	public function getManagerOdetailById($order_id, $odetail_id, $manager_id)
	{
		$row = $this->db->query("
			SELECT `odetails`.*
			FROM `odetails`
			INNER JOIN `orders` ON `odetails`.`odetail_order` = `orders`.`order_id`
			WHERE
				`odetails`.`odetail_id` = '$odetail_id'
				AND `orders`.`order_id` = '$order_id'
				AND `orders`.`order_manager` = '$manager_id'
				AND `orders`.`order_status` <> 'deleted'
				AND `odetails`.`odetail_status` <> 'deleted'
			LIMIT 1
		")->result();

		if (empty($row) OR count($row) != 1)
		{
			return FALSE;
		}

		return $row[0];		
	}
	
	public static function markUpdatedByClient($order, $odetail, $model)
	{
		if ($order->order_status == 'payed')
		{
			$odetail->updated_by_client = 1;
			$odetail->odetail_status = 'processing';
			$odetail->odetail_price = 0;
			$odetail->odetail_price_usd = 0;
			$odetail->odetail_pricedelivery = 0;
			$odetail->odetail_pricedelivery_usd = 0;
			
			$order->updated_by_client = 1;			
			$model->saveOrder($order);
		}
	}

	public static function unmarkUpdatedByClient($order, $odetail, $model)
	{
		$odetail->updated_by_client = 0;
		$order->updated_by_client = 0;
		$model->saveOrder($order);
	}
}
?>