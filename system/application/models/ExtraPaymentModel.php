<?
require_once(MODELS_PATH.'Base/BaseModel.php');
/**
 * @author tua
 * 
 * модель для дополнительных платежей
 * 1. в модели не делаем проверок на валидность i\o это должно делаться в контролере
 * 2. допустимы только ошибки уровня БД
 * 3. разрешатся передавать списки параметров функции, только в случает отсутствия публичного 
 * атрибута соответствующего объекта
 *
 */
class ExtraPaymentModel extends BaseModel implements IModel{

	protected 	$properties			= null;				// array of properties
	protected	$table				= 'extra_payments';		// table name
	protected	$PK					= 'extra_payment_id';	// primary key name	
	
	/**
	 * конструктор
	 *
	 */
	function __construct()
    {
    	$this->properties	= new stdClass();
    	$this->properties->extra_payment_id				='';
    	$this->properties->extra_payment_from			='';
    	$this->properties->extra_payment_to				='';
    	$this->properties->extra_payment_from_login		='';
    	$this->properties->extra_payment_to_login		='';
    	$this->properties->extra_payment_type			='';
    	$this->properties->extra_payment_purpose		='';
    	$this->properties->extra_payment_comment		='';
    	$this->properties->extra_payment_amount			='';
    	$this->properties->extra_payment_amount_ru		='';
    	$this->properties->extra_payment_amount_local	='';
    	$this->properties->extra_payment_comission		='';
    	$this->properties->extra_payment_comission_local='';
    	$this->properties->extra_payment_currency		='';
    	$this->properties->extra_payment_date			='';
    	$this->properties->extra_payment_status			='';
		
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
		$sql = $this->select(array('extra_payment_status' => 'completed'), null, null, 'extra_payment_id DESC');
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
	
	public function addPayment($com_obj){
		
		$props = $this->getPropertyList();
				
		foreach ($props as $prop){
			if (isset($com_obj->$prop)){
				$this->_set($prop, $com_obj->$prop);
			}
		}
		
		$new_id = $this->save(true);
		
		return (isset($new_id) && $new_id) ? $new_id : false;
	}
	
	/**
	 * Get comment by id
	 *
	 * @return array
	 */
	public function getById($id){
		$r = $this->select(array(
			$this->getPK()	=> (int) $id,
		));					
		
		return ((count($r==1) &&  $r) ? array_shift($r) : false);
	}
}
?>