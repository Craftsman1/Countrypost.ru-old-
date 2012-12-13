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
class BidModel extends BaseModel implements IModel{

	protected 	$properties			= null;				// array of properties
	protected	$table				= 'bids';			// table name
	protected	$PK					= 'bid_id';		// primary key name	
	
	/**
	 * конструктор
	 *
	 */
	function __construct()
    {
    	$this->properties	= new stdClass();
    	$this->properties->bid_id				='';
    	$this->properties->client_id			='';
    	$this->properties->manager_id			='';
    	$this->properties->order_id			='';
    	$this->properties->manager_tax			='';
    	$this->properties->foto_tax			='';
    	$this->properties->delivery_cost	='';
    	$this->properties->delivery_name	='';
    	$this->properties->extra_tax	='';
    	$this->properties->total_cost	='';
    	$this->properties->status	='';
    	$this->properties->created	='';
    	
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
	
	public function addBid($bid_obj) {
		$props = $this->getPropertyList();
		
		foreach ($props as $prop){
			if (isset($bid_obj->$prop)){
				$this->_set($prop, $bid_obj->$prop);
			}
		}
		
		$new_id = $this->save(true);
		
		if ($new_id){
			return $this->getInfo(array($new_id));
		}
		
		return false;
	}

	public function getById($id)
	{
		$r = $this->select(array(
			$this->getPK()	=> (int) $id,
		));					
		
		return ((count($r==1) &&  $r) ? array_shift($r) : false);
	}
	
	public function getBids($order_id) 
	{
		return $this->db->query("
			SELECT `{$this->table}`.*
			FROM `{$this->table}`
			WHERE
				order_id = '$order_id' AND
				status != 'deleted'
			ORDER BY created DESC
		")->result();
	}

	public function getPrivilegedBid($bid_id, $user_id, $user_group)
	{
		if ($user_group == 'client')
		{
			return $this->getClientBidById($bid_id, $user_id);
		}
		else if ($user_group == 'manager')
		{
			return $this->getManagerBidById($bid_id, $user_id);
		}
		else if ($user_group == 'admin')
		{
			return $this->getById($bid_id);
		}

		return FALSE;
	}

	private function getClientBidById($bid_id, $user_id)
	{
		$result = $this->db->query("
			SELECT `{$this->table}`.*
			FROM `{$this->table}`
			WHERE
				bid_id = '$bid_id' AND
				client_id = '$user_id' AND
				status != 'deleted'
			ORDER BY created DESC
		")->result();
		
		return ((count($result) > 0 &&  $result) ? $result[0] : FALSE);
	}
	
	private function getManagerBidById($bid_id, $user_id)
	{
		$result = $this->db->query("
			SELECT `{$this->table}`.*
			FROM `{$this->table}`
			WHERE
				bid_id = '$bid_id' AND
				manager_id = '$user_id' AND
				status != 'deleted'
			ORDER BY created DESC
		")->result();
		
		return ((count($result) > 0 &&  $result) ? $result[0] : FALSE);
	}
}
?>