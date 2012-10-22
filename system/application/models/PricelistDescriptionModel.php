<?
require_once(MODELS_PATH.'Base/BaseModel.php');
/**
 * @author tua
 * 
 * модель для прайслиста
 * 1. в модели не делаем проверок на валидность i\o это должно делаться в контролере
 * 2. допустимы только ошибки уровня БД
 * 3. разрешатся передавать списки параметров функции, только в случает отсутствия публичного 
 * атрибута соответствующего объекта
 *
 */
class PricelistDescriptionModel extends BaseModel implements IModel{

	protected 	$properties	= null;							// array of properties
	protected	$table		= 'pricelist_description';		// table name
	protected	$PK			= 'pricelist_description_id';	// primary key name	
	
	/**
	 * конструктор
	 */
	function __construct()
    {
    	$this->properties	= new stdClass();
    	$this->properties->pricelist_description_id	= '';
    	$this->properties->pricelist_country_from	= '';
    	$this->properties->pricelist_country_to		= '';
    	$this->properties->pricelist_description	= '';

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
     * Get delivery list
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
	
	/**
	 * Get pricelist by id
	 *
	 * @return array
	 */
	public function getById($id){
		$r = $this->select(array(
			$this->getPK()	=> (int) $id,
		));					
		
		return ((count($r==1) &&  $r) ? array_shift($r) : FALSE);
	}
	
	public function getDescription($countryFrom, $countryTo)
	{		
		$result = $this->db->query("
			SELECT `pricelist_description`.*
			FROM `pricelist_description`
			WHERE 
				`pricelist_description`.`pricelist_country_from` = $countryFrom AND
				`pricelist_description`.`pricelist_country_to` = $countryTo
			LIMIT 1"
		)->result();

		return ((count($result) > 0 &&  $result) ? $result[0] : FALSE);		
	}
	
	public function saveDescription($description)
	{		
		$props = $this->getPropertyList();

		foreach ($props as $prop)
		{
			if (empty($description->$prop))
			{
				$this->db->set($prop, NULL);
			}
			else
			{
				$this->_set($prop, $description->$prop);
			}
		}
		
		$new_id = $this->save(true);
		
		if ( ! $new_id) return false;
		
		return $this->getInfo(array($new_id));
	}
}
?>