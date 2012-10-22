<?
require_once(MODELS_PATH.'Base/BaseModel.php');
/**
 * @author omni
 * 
 */
class PaymentDetailsModel extends BaseModel implements IModel{

	protected $properties = null;				// array of properties
	protected $table = 'payment_details';		// table name
	protected $PK = 'payment_details_id';		// primary key name	
	
	/**
	 * конструктор
	 *
	 */
	function __construct()
    {
    	$this->properties	= new stdClass();
    	$this->properties->payment_details_id = '';
    	$this->properties->payment_details_number = '';
    	$this->properties->payment_details_user = '';
    	$this->properties->payment_details_payment_system = '';
    	$this->properties->payment_details_amount = '';
    	$this->properties->payment_details_amount_rur = '';
    	$this->properties->payment_details_tax = '';
    	
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
	
	public function getPaymentByNumber($number)
	{
		$result = $this->select(array('payment_details_number' => $number));
		return (count($result) == 1 &&  $result) ? $result[0] : false;
	}
	
	public function addPayment($order_obj)
	{
		$props = $this->getPropertyList();
		
		foreach ($props as $prop)
		{
			if (isset($order_obj->$prop))
			{
				$this->_set($prop, $order_obj->$prop);
			}
		}
		
		$new_id = $this->save(true);
		
		if ($new_id)
		{
			return $this->getInfo(array($new_id));
		}
		
		return false;
	}
}
?>