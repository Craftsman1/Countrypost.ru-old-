<? require_once(MODELS_PATH.'Base/BaseModel.php');

class BidModel extends BaseModel implements IModel{

	protected 	$properties			= null;				// array of properties
	protected	$table				= 'bids';			// table name
	protected	$PK					= 'bid_id';		// primary key name	
	
	/**
	 * конструктор
	 *
	 */
	function __construct()
    {
    	$this->properties	= new stdClass();
    	$this->properties->bid_id				='';
    	$this->properties->client_id			='';
    	$this->properties->manager_id			='';
    	$this->properties->order_id				='';
    	$this->properties->manager_tax_type		='';
    	$this->properties->manager_tax			='';
    	$this->properties->foto_tax				='';
    	$this->properties->delivery_cost		='';
    	$this->properties->delivery_name		='';
    	$this->properties->extra_tax			='';
    	$this->properties->total_cost			='';
    	$this->properties->status				='';
    	$this->properties->created				='';
    	$this->properties->manager_tax_percentage	='';
    	$this->properties->foto_tax_percentage	='';
    	$this->properties->requested_foto_count	='';
    	$this->properties->countrypost_tax	='';
    	$this->properties->countrypost_tax_usd	='';
    	$this->properties->usd_conversion_rate	='';

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
	
	public function addBid($bid_obj) {
		$props = $this->getPropertyList();
		
		foreach ($props as $prop){
			if (isset($bid_obj->$prop)){
				$this->_set($prop, $bid_obj->$prop);
			}
		}
		
		$new_id = $this->save(true);
		
		if ($new_id){
			return $this->getInfo(array($new_id));
		}
		
		return false;
	}

	public function addBidExtras($bid, $extras)
	{//print_r($bid);die();
		$this->db->query("
			DELETE FROM `bid_extras`
			WHERE
				bid_id = $bid->bid_id
		");

		if ( ! empty($extras))
		{
			foreach ($extras as $extra)
			{
				$this->db->query("
					INSERT INTO `bid_extras` (
						bid_id,
						extra_name,
						extra_tax
					)
					VALUES (
						$bid->bid_id,
						'$extra->extra_name',
						'$extra->extra_tax'
					)
				");
			}
		}
	}

	public function getById($id)
	{
		$r = $this->select(array(
			$this->getPK()	=> (int) $id,
		));					
		
		return ((count($r==1) &&  $r) ? array_shift($r) : false);
	}
	
	public function isBidAllowed($order, $manager_id)
	{
		if ($order->order_manager)
		{
			return FALSE;
		}

		$result = $this->db->query("
			SELECT 1
			FROM bids
			INNER JOIN orders ON orders.order_id = bids.order_id
			WHERE
				bids.order_id = '$order->order_id' AND
				bids.manager_id = '$manager_id' AND
				bids.status != 'deleted'
		")->result();

		return empty($result);
	}

	public function getBids($order_id)
	{
		return $this->db->query("
			SELECT `{$this->table}`.*
			FROM `{$this->table}`
			WHERE
				order_id = '$order_id' AND
				status != 'deleted'
			ORDER BY created DESC
		")->result();
	}

	public function getBidExtras($bid_id)
	{
		$result = $this->db->query("
			SELECT `bid_extras`.*
			FROM `bid_extras`
			WHERE
				bid_id = '$bid_id' AND
				status != 'deleted'
		")->result();

		return ((count($result) > 0 AND $result) ? $result : FALSE);
	}

	public function getPrivilegedBid($bid_id, $user_id, $user_group)
	{
		if ($user_group == 'client')
		{
			return $this->getClientBidById($bid_id, $user_id);
		}
		else if ($user_group == 'manager')
		{
			return $this->getManagerBidById($bid_id, $user_id);
		}
		else if ($user_group == 'admin')
		{
			return $this->getById($bid_id);
		}

		return FALSE;
	}

	private function getClientBidById($bid_id, $user_id)
	{
		$result = $this->db->query("
			SELECT `{$this->table}`.*
			FROM `{$this->table}`
			WHERE
				bid_id = '$bid_id' AND
				client_id = '$user_id' AND
				status != 'deleted'
			ORDER BY created DESC
		")->result();
		
		return ((count($result) > 0 &&  $result) ? $result[0] : FALSE);
	}
	
	private function getManagerBidById($bid_id, $user_id)
	{
		$result = $this->db->query("
			SELECT `{$this->table}`.*
			FROM `{$this->table}`
			WHERE
				bid_id = '$bid_id' AND
				manager_id = '$user_id' AND
				status != 'deleted'
			ORDER BY created DESC
		")->result();
		
		return ((count($result) > 0 &&  $result) ? $result[0] : FALSE);
	}

	public function recalculate($bid, $order, $skip_manager_tax = FALSE)
	{
		try
		{
			// 1. собираем допрасходы
			$bid->extra_tax = 0;

			if (isset($bid->bid_extras) AND
				is_array($bid->bid_extras))
			{
				foreach (($bid->bid_extras) as $bid_extra)
				{
					$bid->extra_tax += $bid_extra->extra_tax;
				}
			}

			// 2. комиссия посредника
			$ci = get_instance();
			$ci->load->model('ManagerModel', 'Managers');

			if ( ! ($manager = $ci->Managers->getById($bid->manager_id)))
			{
				return FALSE;
			}

			// 2.1 значения комиссий для типов "стоимость товаров" и "стоимость товаров + местная доставка"
			$products_delivery_tax = ceil(
				($order->order_products_cost + $order->order_delivery_cost) *
					$manager->order_tax *
					0.01);

			$products_tax = ceil(
				$order->order_products_cost *
					$manager->order_tax *
					0.01);


			// 2.2 минимальная комиссия посредника
			if ($products_delivery_tax < $manager->min_order_tax)
			{
				$products_delivery_tax = $manager->min_order_tax;
			}

			if ($products_tax < $manager->min_order_tax)
			{
				$products_tax = $manager->min_order_tax;
			}

			// 2.3 основная логика расчета комиссии посредника
			if ( ! $skip_manager_tax)
			{
				if ($order->order_type == 'mail_forwarding' OR
					$order->order_type == 'service' OR
					$order->order_type == 'delivery')
				{
					// ничего не меняем, комиссия в этих заказах ВСЕГДА задана вручную
				}
				else if ($order->order_type == 'online' OR
					$order->order_type == 'offline')
				{
					if ($bid->manager_tax_type == 'products_delivery')
					{
						$bid->manager_tax = $products_delivery_tax;
					}
					else if ($bid->manager_tax_type == 'products')
					{
						$bid->manager_tax = $products_tax;
					}
					else if ($bid->manager_tax_type == 'custom')
					{
						// ничего не меняем, если задана комиссия вручную
					}
				}
			}

			// 3. комиссия countrypost
			$ci->load->model('OrderModel', 'Orders');
			$countrypost_tax = $ci->Orders->generateCountrypostTax($order);
			$bid->countrypost_tax = $countrypost_tax->countrypost_tax;
			$bid->countrypost_tax_usd = $countrypost_tax->countrypost_tax_usd;
			$bid->usd_conversion_rate = $countrypost_tax->usd_conversion_rate;

			// 4. подбиваем сумму
			$bid->total_cost =
				$order->order_products_cost +
				$order->order_delivery_cost +
				$bid->countrypost_tax +
				$bid->manager_tax +
				$bid->foto_tax +
				$bid->extra_tax +
				$bid->delivery_cost;
		}
		catch (Exception $e)
		{
			return FALSE;
		}

		return $bid;
	}
}
?>