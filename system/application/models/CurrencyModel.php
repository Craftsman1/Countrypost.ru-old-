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
class CurrencyModel extends BaseModel implements IModel{

	protected 	$properties			= null;				// array of properties
	protected	$table				= 'currencies';			// table name
	protected	$PK					= 'currency_name';		// primary key name	
	
	/**
	 * конструктор
	 *
	 */
	function __construct()
    {
    	$this->properties	= new stdClass();
    	$this->properties->currency_name		= '';
    	$this->properties->currency_symbol		= '';
    	$this->properties->cbr_exchange_rate	= '';
    	$this->properties->cbr_cross_rate		= '';
    	
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
	 * Get country by id
	 *
	 * @return array
	 */
	public function getById($id){
		$r = $this->select(array(
			$this->getPK()	=> $id,
		));					
		
		return ((count($r==1) &&  $r) ? array_shift($r) : false);
	}
	
	/**
	 * Добавление/изменение валюты
	 * Выкидывает исключения на некорректные данные
	 * 
	 * @param (object) 	- $currency
	 * @return (mixed)	- объект country или false в случае ошибки записи в базу
	 */
	public function saveCurrency($currency){
		$props = $this->getPropertyList();
		
		foreach ($props as $prop){
			if (isset($currency->$prop)){
				$this->_set($prop, $currency->$prop);
			}
		}
		
		$new_id = $this->save(true);
		
		if (!$new_id) return false;
		
		return $this->getInfo(array($new_id));
	}
	
	/**
	 * Get country by currency
	 *
	 * @return array
	 */
	public function getCountryByCurrency($currency) 
	{
		$result = $this->db->query('
			SELECT `countries`.*
			FROM `countries`
			WHERE `countries`.`country_currency`="'.$currency.'"
			LIMIT 1'
		)->result();

		return ((count($result) > 0 &&  $result) ? $result[0] : false);		
	}
	
	public function getCurrencyByCountry($country_id)
	{
		$result = $this->db->query('
			SELECT `currencies`.*
			FROM `currencies`
				INNER JOIN `countries` ON `countries`.`country_currency` = `currencies`.`currency_name`
			WHERE `countries`.`country_id`="'.$country_id.'"
			LIMIT 1'
		)->result();

		return ((count($result) > 0 &&  $result) ? $result[0] : false);		
	}
	
	public function getExchangeRate($currency_from, $currency_to, $default_rate = 1)
	{
		$result = $this->db->query("
			SELECT `exchange_rates`.rate
			FROM `exchange_rates`
			WHERE
				`exchange_rates`.`currency_from` = '$currency_from' AND
				`exchange_rates`.`currency_to` = '$currency_to'
			LIMIT 1"
		)->result();

		return ((count($result) == 1 &&  $result) ? $result[0]->rate : $default_rate);
	}

	public function getRate($currency)
	{
		$currency = $this->getById($currency);
		
		if (!$currency) return '';
		return $currency->cbr_exchange_rate;
	}
	
	public function getCrossRate($currency)
	{
		$currency = $this->getById($currency);
		
		if (!$currency) return '';
		return $currency->cbr_cross_rate;
	}
	
	public function getArray()
	{
		$list = $this->getList();
		
		$result = array();
		
		foreach($list as $currency)
		{
			$result[$currency->currency_name] = $currency;
		}
		
		return $result;
	}
}
?>