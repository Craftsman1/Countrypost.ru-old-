<?
require_once(MODELS_PATH.'Base/BaseModel.php');

class TaxModel extends BaseModel implements IModel
{
	protected $properties	= null;
	protected $table = 'taxes';
	protected $PK = 'tax_id';
	private $statuses = array(
		'not_payed' => 'К выплате',
		'payed' => 'Выплачено'
	);


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

	public function getStatuses()
	{
		return $this->statuses;
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

	public function generateTax($order)
	{
		// 1. проверяем, не добавлена ли уже комиссия за данный заказ
		$duplicated_taxes = $this->db->query("
			SELECT 1
			FROM
				taxes
			WHERE
				manager_id = $order->order_manager AND
<<<<<<< HEAD
				order_id = $order->order_id AND
=======
				order_id = $order->order_id
>>>>>>> parent of 6c2ba62... Задачи: 16+37+35+33+30+31
				status <> 'deleted'
			LIMIT 1")->result();

		if ( ! empty($duplicated_taxes))
		{
			return;
		}

		// 2. переносим комиссию на счет посредника
		$ci = get_instance();
		$ci->load->model('OrderModel', 'Orders');

		$tax = new stdClass();
		$tax->order_id = $order->order_id;
		$tax->manager_id = $order->order_manager;
		$tax->amount_usd = $order->countrypost_tax_usd;
		$tax->usd_conversion_rate = $order->usd_conversion_rate;
		$tax->amount = $order->countrypost_tax;

		$tax->currency = $ci->Orders->getOrderCurrency($order->order_id);

		$this->saveTax($tax);
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

	public function getFilteredTaxes($filter = array(), $from = NULL, $to = NULL, $extra_where = NULL)
	{
		$where = '1';

		// обход полей фильтра
		if (is_string($filter))
		{
			$where	= $filter;
		}
		else
		{
			foreach ($filter as $key => $val)
			{
				if ($key == 'like' AND
					is_array($val))
				{
					foreach ($val as $key1 => $val1)
					{
						$where .= " AND $key1 LIKE '%$val1%'";
					}
				}
				else
				{
					$where .= " AND $key = '$val'";
				}
			}
		}

		// фильтр дат
		if ($from AND $to)
		{
			$from_date = new DateTime($from);
			$from_date = $from_date->format('Y-m-d H:i:s');
			$to_date = new DateTime($to);
			$to_date->modify('+1 day');
			$to_date = $to_date->format('Y-m-d H:i:s');

			$where .= " AND `usd_conversion_date` BETWEEN '$from_date' AND '$to_date'";
		}
		else if ($from)
		{
			$from_date = new DateTime($from);
			$from_date = $from_date->format('Y-m-d H:i:s');

			$where .= " AND `usd_conversion_date` >= '$from_date'";
		}
		else if ($to)
		{
			$to_date = new DateTime($to);
			$to_date->modify('+1 day');
			$to_date = $to_date->format('Y-m-d H:i:s');

			$where .= " AND `usd_conversion_date` < '$to_date'";
		}

		// дополнительные условия
		if ( ! empty($extra_where))
		{
			$where .= $extra_where;
		}

		/*print_r("
			SELECT
				`{$this->table}`.*,
				`users`.`user_login`
			FROM `{$this->table}`
				LEFT OUTER JOIN `users`
					ON `users`.`user_id` = `{$this->table}`.`manager_id`
			WHERE $where
			ORDER BY `tax_id` DESC
		");//die();
*/
		// погнали
		return $this->db->query("
			SELECT
				`{$this->table}`.*,
				dealers.`user_login` dealer_login,
				clients.`user_login` client_login,
				clients.`user_id` client_id,
				order_type
			FROM `{$this->table}`
				LEFT OUTER JOIN `users` dealers
					ON dealers.`user_id` = `{$this->table}`.`manager_id`
				LEFT OUTER JOIN `orders`
					ON `orders`.`order_id` = `{$this->table}`.`order_id`
				LEFT OUTER JOIN `users` clients
					ON clients.`user_id` = `orders`.`order_client`
			WHERE $where
			ORDER BY `tax_id` DESC
		")->result();
	}

	public function getTotalLocal($view, $is_admin = TRUE)
	{
		if (empty($view['taxes']))
		{
			return FALSE;
		}

		if ($is_admin AND
			(empty($view['filter']->sfield) OR
				($view['filter']->sfield != 'manager_id' AND
					$view['filter']->sfield != 'manager_login')))
		{
			return FALSE;
		}

		$total = 0;
		$currency = '';

		foreach ($view['taxes'] as $tax)
		{
			if ($tax->status == 'not_payed')
			{
				$total += $tax->amount;
			}

			if (empty($currency) AND
				! empty($tax->currency))
			{
				$currency = $tax->currency;
			}
		}

		$result['total_local'] = $total;
		$result['total_currency'] = $currency;

		return $result;
	}

	public function getTotalUSD($view, $is_admin = TRUE)
	{
		if (empty($view['taxes']))
		{
			return FALSE;
		}

		if ($is_admin AND
			(empty($view['filter']->sfield) OR
				($view['filter']->sfield != 'manager_id' AND
					$view['filter']->sfield != 'manager_login')))
		{
			return FALSE;
		}

		$total = 0;

		foreach ($view['taxes'] as $tax)
		{
			if ($tax->status == 'not_payed')
			{
				$total += $tax->amount_usd;
			}
		}

		return $total;
	}
}
?>