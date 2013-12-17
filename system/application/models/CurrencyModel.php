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
	
	public function getExchangeRateByCountries($country_from, $country_to, $user_group, $default_rate = 1)
	{
		$result = $this->db->query("
			SELECT `exchange_rates`.*
			FROM `exchange_rates`
			INNER JOIN countries country_from
				ON country_from.country_currency = exchange_rates.currency_from
			INNER JOIN countries country_to
				ON country_to.country_currency = exchange_rates.currency_to
			WHERE
				`country_from`.`country_id` = $country_from AND
				`country_from`.`country_id` = $country_to
			LIMIT 1"
		)->result();

		if (empty($result))
		{
			return $this->getCrossRateByCountries($country_from, $country_to, $user_group, $default_rate, $result);
		}
		else
		{
			return $this->calculateExchangeRate($user_group, $default_rate, $result);
		}
	}

	public function getExchangeRate($currency_from, $currency_to, $user_group, $default_rate = 1)
	{
		$result = $this->db->query("
			SELECT `exchange_rates`.*
			FROM `exchange_rates`
			WHERE
				`exchange_rates`.`currency_from` = '$currency_from' AND
				`exchange_rates`.`currency_to` = '$currency_to'
			LIMIT 1"
		)->result();

		return $this->calculateExchangeRate($user_group, $default_rate, $result);
	}

	private function calculateExchangeRate($user_group, $default_rate, $raw_rate)
	{
		if (count($raw_rate) == 1 AND $raw_rate)
		{
			$min_rate_caption = "min_{$user_group}_rate";
			$extra_tax_caption = "{$user_group}_extra_tax";

			$rate = $raw_rate[0]->rate *
				(100 + $raw_rate[0]->$extra_tax_caption) *
				0.01;

			if ($rate < $raw_rate[0]->$min_rate_caption)
			{
				return $raw_rate[0]->$min_rate_caption;
			}

			return $rate;
		}

		return $default_rate;
	}

	private function getCrossRateByCountries($country_from, $country_to, $user_group, $default_rate)
	{
		$rate_from = $this->db->query("
			SELECT `exchange_rates`.rate
			FROM `exchange_rates`
			INNER JOIN countries
				ON country_currency = exchange_rates.currency_from
			WHERE
				exchange_rates.currency_to = 'USD' AND
				countries.country_id = $country_from
			LIMIT 1"
		)->result();

		$rate_to = $this->db->query("
			SELECT `exchange_rates`.rate
			FROM `exchange_rates`
			INNER JOIN countries
				ON country_currency = exchange_rates.currency_from
			WHERE
				exchange_rates.currency_to = 'USD' AND
				countries.country_id = $country_to
			LIMIT 1"
		)->result();

		$crossrate = $rate_from[0]->rate / $rate_to[0]->rate;

		return $crossrate;
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

	public function getExchangeCurrencies($currencies = array())
	{
		$this->db->where_in('currency_name', $currencies);

        $query = $this->db->get($this->table);

		if($query->num_rows()>0)
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}
}
?>