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
class ExchangeRateModel extends BaseModel implements IModel{

	protected 	$properties			= null;				// array of properties
	protected	$table				= 'exchange_rates';			// table name
	protected	$PK					= 'exchange_rate_id';		// primary key name
	
	/**
	 * конструктор
	 *
	 */
	function __construct()
    {
    	$this->properties	= new stdClass();
    	$this->properties->exchange_rates	= '';
    	$this->properties->currency_from	= '';
    	$this->properties->currency_to		= '';
    	$this->properties->service_name		= '';
    	
        parent::__construct();
    }
    
   	public function getPK()
	{
		return $this->PK;
	}	
	
    public function getTable()
	{
		return $this->table;
	}
    
    public function getList()
	{
		$sql = $this->select();
		return ($sql)?($sql):(false);
	}

	public function getPropertyList()
	{
		return array_keys((array) $this->properties);
	}
	
	public function getById($id)
	{
		$r = $this->select(array(
			$this->getPK()	=> $id,
		));					
		
		return ((count($r==1) &&  $r) ? array_shift($r) : false);
	}
	
	public function saveRate($rate)
	{
		$props = $this->getPropertyList();
		
		foreach ($props as $prop){
			if (isset($rate->$prop)){
				$this->_set($prop, $rate->$prop);
			}
		}
		
		$new_id = $this->save(true);
		
		if ( ! $new_id) return false;
		
		return $this->getById($new_id);
	}
	
	public function updateRates($rates)
	{
		if (empty($rates) OR
			! is_array($rates))
		{
			return FALSE;
		}

		foreach ($rates as $rate)
		{
			$this->saveRate($rate);
		}
	}
}
?>