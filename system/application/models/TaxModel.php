<?
require_once(MODELS_PATH.'Base/BaseModel.php');

class TaxModel extends BaseModel implements IModel
{
	protected $properties	= null;
	protected $table = 'taxes';
	protected $PK = 'tax_id';
	
	/**
	 * конструктор
	 */
	function __construct()
    {
    	$this->properties						= new stdClass();
    	$this->properties->tax_id				='';
    	$this->properties->country_id			='';
    	$this->properties->package				='';
    	$this->properties->package_disconnected	='';
    	$this->properties->order				='';
    	$this->properties->package_joint		='';
    	$this->properties->package_declaration	='';
    	$this->properties->package_insurance	='';
    	$this->properties->min_order			='';
    	$this->properties->max_package_insurance='';
    	$this->properties->package_foto			='';
    	$this->properties->package_foto_system	='';
    	
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
		return $sql ? $sql : FALSE;
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
	
	public function getById($id)
	{
		$r = $this->select(array(
			$this->getPK()	=> (int) $id,
		));					
		
		return ((count($r==1) &&  $r) ? array_shift($r) : FALSE);
	}
		
	public function getByCountryId($country_id)
	{
		$r = $this->select(array(
			'country_id' => (int) $country_id,
		));					
		
		return ((count($r==1) &&  $r) ? array_shift($r) : FALSE);
	}
		
	public function saveTax($tax)
	{
		$props = $this->getPropertyList();
		
		foreach ($props as $prop)
		{
			if (isset($tax->$prop))
			{
				if (empty($tax->$prop))
				{
					$this->db->set($prop, $tax->$prop);
				}
				else
				{
					$this->_set($prop, $tax->$prop);
				}
			}
		}
		
		$new_id = $this->save(TRUE);
		
		if ( ! $new_id)
		{
			return FALSE;
		}
		
		return $this->getInfo(array($new_id));
	}
	
	public function getTaxes()
	{
		$result = $this->db->query("
			SELECT `taxes`.*, `countries`.`country_id`, `countries`.`country_name`
			FROM `taxes`
				LEFT JOIN `countries` ON `taxes`.`country_id` = `countries`.`country_id`")
		->result();

		return $result ? $result : FALSE;	
	}	
}
?>