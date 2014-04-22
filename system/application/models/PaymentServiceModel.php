<?
require_once(MODELS_PATH.'Base/BaseModel.php');
/**
 * @author tua
 * 
 * модель для комментариев к заказу
 * 1. в модели не делаем проверок на валидность i\o это должно делаться в контролере
 * 2. допустимы только ошибки уровня БД
 * 3. разрешатся передавать списки параметров функции, только в случает отсутствия публичного 
 * атрибута соответствующего объекта
 *
 */
class PaymentServiceModel extends BaseModel implements IModel{

	protected 	$properties			= null;					// array of properties
	protected	$table				= 'payment_services';	// table name
	protected	$PK					= 'payment_service_id';	// primary key name	
	
	/**
	 * конструктор
	 *
	 */
	function __construct()
    {
    	$this->properties									= new stdClass();
    	$this->properties->payment_service_id				= '';
    	$this->properties->payment_service_name				= '';
    	$this->properties->payment_service_inprompt			= '';
    	$this->properties->payment_service_outprompt		= '';
		
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
	 * Get service by id
	 *
	 * @return array
	 */
	public function getById($id){
		$r = $this->select(array(
			$this->getPK()	=> (int) $id,
		));					
		
		return ((count($r==1) &&  $r) ? array_shift($r) : false);
	}
	
	public function getOutServices()
	{
		return $this->db->query('
			SELECT `'.$this->table.'`.*
			FROM `'.$this->table.'`
			WHERE `payment_service_outprompt` IS NOT NULL
		')->result();

	}
	
	public function getInServices()
	{
		return $this->db->query('
			SELECT `'.$this->table.'`.*
			FROM `'.$this->table.'`
			WHERE `payment_service_inprompt` IS NOT NULL
		')->result();

	}
}
?>