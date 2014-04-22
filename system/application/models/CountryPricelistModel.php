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
class CountryPricelistModel extends BaseModel implements IModel{

	protected 	$properties			= null;
	protected	$table				= 'country_pricelist';
	protected	$PK					= 'country_id';
	
	/**
	 * конструктор
	 */
	function __construct()
    {
    	$this->properties	= new stdClass();
    	$this->properties->country_id				='';
    	$this->properties->description				='';
    	
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
     * Get list
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
	 * Get country_pricelist by id
	 *
	 * @return array
	 */
	public function getById($id){
		$r = $this->select(array(
			$this->getPK()	=> (int) $id,
		));					
		
		return ((count($r==1) &&  $r) ? array_shift($r) : false);
	}
	
	public function saveCountryPricelist($country)
	{
		$props = $this->getPropertyList();
		
		foreach ($props as $prop){
			if (isset($country->$prop)){
				$this->_set($prop, $country->$prop);
			}
		}
		
		$new_id = $this->save(true);
		
		if (!$new_id) return false;
		
		return $this->getById($new_id);
	}
}
?>