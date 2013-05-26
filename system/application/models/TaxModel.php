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

		$this->properties->manager_id			='';
		$this->properties->order_id				='';
		$this->properties->status				='';
		$this->properties->amount				='';
		$this->properties->amount_usd			='';
		$this->properties->usd_conversion_rate	='';
		$this->properties->usd_conversion_date	='';
		$this->properties->currency				='';

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

	public function getFilteredTaxes($filter)
	{

	}

	public function generateTax($order)
	{
		$tax = new stdClass();
		$tax->order_id = $order->order_id;
		$tax->manager_id = $order->order_manager;

		$ci = get_instance();
		$ci->load->model('OrderModel', 'Orders');

		$tax->amount_usd = $ci->Orders->getCountrypostTax();

		if ($order->order_country_from)
		{
			$tax->currency = $ci->Orders->getOrderCurrency($order->order_id);
			$tax->usd_conversion_rate = $ci->Currencies->getExchangeRate('USD', $tax->currency, 'manager');

			// округляем до центов в пользу countrypost.ru
			$tax->amount = ceil(
				floatval($tax->amount_usd) *
					floatval($tax->usd_conversion_rate) *
					100) *
				0.01;

			$this->saveTax($tax);
		}
	}

	public function getCountrypostBalance($manager_id)
	{
		$result = $this->db->query("
			SELECT SUM(amount) AS 'balance', currency
			FROM
				taxes
			WHERE
				manager_id = $manager_id AND
				status = 'not_payed'
			GROUP BY
				currency")->result();

		if (empty($result))
		{
			return FALSE;
		}

		return round($result[0]->balance, 2) . ' ' . $result[0]->currency;
	}

	public function getAdminBalance()
	{
		$result = $this->db->query("
			SELECT SUM(amount_usd) AS 'balance'
			FROM
				taxes
			WHERE
				status = 'not_payed'")->result();

		if (empty($result))
		{
			return FALSE;
		}

		return round($result[0]->balance, 2) . ' USD';
	}
}
?>