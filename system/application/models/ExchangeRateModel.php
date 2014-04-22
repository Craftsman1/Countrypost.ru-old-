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
    	$this->properties->exchange_rate_id		= '';
    	$this->properties->rate					= '';
    	$this->properties->min_client_rate		= '';
    	$this->properties->min_manager_rate		= '';
    	$this->properties->client_extra_tax		= '';
    	$this->properties->manager_extra_tax	= '';
    	$this->properties->currency_from		= '';
    	$this->properties->currency_to			= '';
    	$this->properties->service_name			= '';
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

		return TRUE;
	}

	public function getByCurrencies($currency_from, $currency_to)
	{
		$result = $this->db->query("
			SELECT `exchange_rates`.*
			FROM `exchange_rates`
			WHERE
				`exchange_rates`.`currency_from` = '$currency_from' AND
				`exchange_rates`.`currency_to` = '$currency_to'
			LIMIT 1"
		)->result();

		$bulk_rate = new stdClass();

		$bulk_rate->exchange_rate_id = 0;
		$bulk_rate->currency_from = $currency_from;
		$bulk_rate->currency_to = $currency_to;

		return ((count($result) == 1 AND  $result) ?
			$result[0] :
			$bulk_rate
		);
	}

	public function updateCrossRate($crossRate,$currencyFrom,$currencyTo)
	{
		$this->db->where('currency_from', $currencyFrom );
		$this->db->where('currency_to', $currencyTo );
		$this->db->set('rate', $crossRate);
		$this->db->limit(1);
		return $this->db->update('exchange_rates');
	}
}
?>