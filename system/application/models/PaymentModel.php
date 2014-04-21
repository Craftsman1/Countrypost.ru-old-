<?
require_once(MODELS_PATH.'Base/BaseModel.php');
/**
 * @author omni
 * 
 */
class PaymentModel extends BaseModel implements IModel{

	protected 	$properties			= NULL;				// array of properties
	protected	$table				= 'payments';		// table name
	protected	$PK					= 'payment_id';		// primary key name	

	private $statuses				= array(
		'sent_by_client' => 'Переведено клиентом',
		'not_payed' => 'К выплате',
		'payed' => 'Выплачено'
	);

	private $countrypost_statuses	= array(
		'not_payed' => 'К выплате',
		'payed' => 'Выплачено'
	);

	/**
	 * конструктор
	 *
	 */
	function __construct()
    {
    	$this->properties	= new stdClass();
    	$this->properties->payment_id					='';
    	$this->properties->payment_amount_rur			='';
    	$this->properties->payment_from					='';
    	$this->properties->payment_to					='';
    	$this->properties->payment_tax					='';
    	$this->properties->payment_amount_from			='';
    	$this->properties->payment_amount_to			='';
    	$this->properties->payment_amount_tax			='';
    	$this->properties->excess_amount				='';
    	$this->properties->payment_purpose				='';
    	$this->properties->payment_details				='';
    	$this->properties->payment_time					='';
    	$this->properties->payment_comment				='';
    	$this->properties->payment_type					='';
    	$this->properties->payment_status				='';
    	$this->properties->payment_transfer_info		='';
    	$this->properties->payment_transfer_order_id	='';
    	$this->properties->payment_transfer_sign		='';
    	$this->properties->payment_service_id			='';
    	$this->properties->payment_service_name			='';
    	$this->properties->payment_currency				='';
    	$this->properties->order_id						='';
    	$this->properties->order2in_id					='';
		$this->properties->amount_usd					= '';
		$this->properties->usd_conversion_rate			= '';
		$this->properties->status						= '';

        parent::__construct();
    }

	public function getStatuses()
	{
		return $this->statuses;
	}
    
	public function getCountrypostStatuses()
	{
		return $this->countrypost_statuses;
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
	
	
	public function getPaymentsByUser($user_id, $vector = 'from'){
		
		$vectors = array('from', 'to');
		
		if (!in_array($vector, $vectors))
			return false;
		
			
		$p = $this->select(array(
									'payment_'.$vector	=> $user_id
		),NULL,NULL,array(
									'payment_time'		=> 'asc'
		));
		
		return $p;
		
	}	
	
	/**
	 * Перевод денег внутри системы
	 *
	 * @param	object	$payment_obj - детали платежа
	 * @param	object	$skip_inner_tran - использовать внешнюю транзакцию или открыть свою
	 * @return 	mixed
	 */
	public function makePayment(
		$payment_obj, 
		$skip_inner_tran = NULL)
	{
		try
		{
			// если платеж пустой, просто выходим
			if (!$payment_obj->payment_amount_from && !$payment_obj->payment_amount_to)
			{
				return true;
			}
			
			// инициализация платежа
			$payment_obj = self::validate($payment_obj);
			
			$this->_load($payment_obj);
			$ci = get_instance();
			$ci->load->model('UserModel', 'User');
			
			// валидация платежа
			$ufrom	= $this->_get('payment_from') > 0 ? $ci->User->getById($this->_get('payment_from')) : false;
			$uto	= $this->_get('payment_to') > 0 ? $ci->User->getById($this->_get('payment_to')) : false;
			
			if ($ufrom)
			{
				// если $ufrom->user_id == 1 - значит это системный аккаунт, на нем баланс не проверяем
				if ($this->_get('payment_amount_from') < 0)
				{
					throw new Exception('Сумма не может быть отрицательной', -2);
				}
			}
			
			if ($uto)
			{
				if ($this->_get('payment_amount_to') < 0)
				{
					throw new Exception('Сумма платежа не может быть отрицательной.', -2);
				}
			}
			
			// определение направления платежа
			if (!$this->_get('payment_type'))
			{
				if ($ufrom->user_group == 'system' && $uto)
				{
					$this->_set('payment_type', 'in');
				}
				elseif ($ufrom && !$uto)
				{
					$this->_set('payment_type', 'out');
				}
				else
				{
					$this->_set('payment_type', 'inner');
				}
			}
			
			// открываем транзакцию
			if (!isset($skip_inner_tran)) 
			{
				$this->db->trans_begin();
			}
			
			// переводим деньги на счет получателя
			if ($uto && 
				$this->_get('payment_amount_to') &&
				!$ci->User->chargeCoints($uto->user_id, $this->_get('payment_amount_to')))
			{
				throw new Exception('Невозможно зачислить средства на счет.', -4);
			}
			
			// снимаем деньги со счета отправителя
			// значение суммы должно быть отрицательным
			if ($ufrom && 
				$this->_get('payment_amount_from') &&
				!$ci->User->chargeCoints($ufrom->user_id, -$this->_get('payment_amount_from')))
			{
				throw new Exception('Невозможно списать средства со счета.', -5);
			}

			// сохраняем историю платежа
			$payment_id = $this->makePaymentLog($this->_get());
			
			if (!$payment_id)
			{
				throw new Exception('Невозможно сохранить историю платежа.', -6);
			}

			// закрываем транзакцию
			if (!isset($skip_inner_tran))
			{
				if ($this->db->trans_status() !== FALSE)
				{
					$this->db->trans_commit();
					return $payment_id;
				}
				else
				{
					throw new Exception('Transaction fail!', -7);
				}
			}
			
			return $payment_id;
		}
		catch (Exception $e)
		{
			// откатываем транзакцию
			if (!isset($skip_inner_tran))
			{
				$this->db->trans_rollback();
			}
			
			throw $e;
		}
	}	
	
	/**
	 * Перевод денег внутри системы
	 *
	 * @param	object	$payment_obj - детали платежа
	 * @param	object	$skip_inner_tran - использовать внешнюю транзакцию или открыть свою
	 * @return 	mixed
	 */
	public function makePaymentLocal(
		$payment_obj, 
		$skip_inner_tran = NULL)
	{//return true;
		try
		{
			// инициализация платежа
			$payment_obj = self::validate($payment_obj);
			
			$this->_load($payment_obj);
			$ci = get_instance();
			$ci->load->model('ManagerModel', 'Manager');
			
			$ufrom	= $this->_get('payment_from') ? $ci->Manager->getById($this->_get('payment_from')) : false;
			$uto	= $this->_get('payment_to')	? $ci->Manager->getById($this->_get('payment_to')) : false;
			
			// валидация платежа
			if ($ufrom)
			{
				if ($ufrom->manager_balance_local < $this->_get('payment_amount_from'))
				{
					//throw new Exception('Недостаточно денег на счету в местной валюте.', -1);
				}
				if ($this->_get('payment_amount_from') < 0)
				{
					throw new Exception('Сумма платежа в местной валюте не может быть отрицательной.', -2);
				}
			}
			
			if ($uto)
			{
				if ($this->_get('payment_amount_to') < 0)
				{
					throw new Exception('Сумма платежа в местной валюте не может быть отрицательной.', -2);
				}
			}
			
			// открываем транзакцию
			if (!isset($skip_inner_tran)) 
			{
				$this->db->trans_begin();
			}
			
			// переводим деньги на счет получателя
			if ($uto && 
				$this->_get('payment_amount_to') &&
				!$ci->Manager->makePaymentLocal($uto, $this->_get('payment_amount_to')))
			{
				throw new Exception('Невозможно зачислить средства на счет в местной валюте.', -4);
			}
			
			// снимаем деньги со счета отправителя
			// значение суммы должно быть отрицательным
			if ($ufrom && 
				$this->_get('payment_amount_from') &&
				!$ci->Manager->makePaymentLocal($ufrom, -$this->_get('payment_amount_from')))
			{
				throw new Exception('Невозможно списать средства со счета в местной валюте.', -5);
			}

			// сохраняем историю платежа
			$payment_id = $this->makePaymentLog($this->_get());
			
			if (!$payment_id)
			{
				throw new Exception('Невозможно сохранить историю платежа.', -6);
			}

			// закрываем транзакцию
			if (!isset($skip_inner_tran))
			{
				if ($this->db->trans_status() !== FALSE)
				{
					$this->db->trans_commit();
					return $payment_id;
				}
				else
				{
					throw new Exception('Невозможно закрыть транзакцию в местной валюте.', -7);
				}
			}
			
			return $payment_id;
		}
		catch (Exception $e)
		{
			// откатываем транзакцию
			if (!isset($skip_inner_tran))
			{
				$this->db->trans_rollback();
			}
			
			throw $e;
		}
	}	
	
	/**
	 * Зачисление денег на счет
	 *
	 * @param unknown_type $payment_obj
	 * @param unknown_type $skip_inner_tran
	 * @return unknown
	 */
	public function makeCharge($payment_obj = NULL, $skip_inner_tran = NULL)
	{
		try
		{
			// инициализация платежа
			if ($payment_obj)
			{
				$this->_load($payment_obj);
			}
			//var_dump($this->_get('payment_amount_from'));die();
			//var_dump($payment_obj);die();
			$ci = get_instance();
			$ci->load->model('UserModel', 'User');
			
			// валидация платежа
			$ufrom	= $this->_get('payment_from'); // строчное значение - название платежной ситемы и внутреннего номера кошелька в ней
			$uto	= $this->_get('payment_to') > 0 ? $ci->User->getById($this->_get('payment_to')) : false;
			$system	= $ci->User->getById(1);

			if (!$ufrom)
			{
				throw new Exception('Source not found');
			}
			
			if ($system->user_group !== 'admin')
			{
				throw new Exception('Не определен системный счет!');
			}
	
			// опускаем транзакцию
			if (!isset($skip_inner_tran)) 
			{
				$this->db->trans_begin();
			}
			
			$this->_set('payment_type', 'in');
			
			// переводим деньги на счет
			if ($uto && 
				!$ci->User->chargeCoints($uto->user_id, $this->_get('payment_amount_to')))
			{
				throw new Exception('Невозможно зачислить средства на счет.');
			}
			
			// переводим комиссию на счет системы
			if ($uto && 
				$ufrom && 
				$this->_get('payment_amount_tax') &&
				!$ci->User->chargeCoints($system->user_id, $this->_get('payment_amount_tax')))
			{
				throw new Exception('Невозможно начислить комиссию.');
			}

			// сохраняем историю платежа
			$payment_id = $this->makePaymentLog($this->_get());
			
			if (!$payment_id)
			{
				throw new Exception('Невозможно сохранить историю платежа.');
			}

			
			// опускаем транзакцию
			if (!isset($skip_inner_tran))
			{
				if ($this->db->trans_status() !== FALSE)
				{
					$this->db->trans_commit();
					return $payment_id;
				}
				else
				{
					throw new Exception('Transaction fail!');
				}
			}
			
			return $payment_id;
		}
		catch (Exception $e)
		{
			// опускаем транзакцию
			if (!isset($skip_inner_tran))
			{
				$this->db->trans_rollback();
			}
			
			// прокидываем исключение вверх по цепочке
			throw $e;
		}
	}	
	
	/**
	 * Запись истории перевода в базу
	 *
	 */
	private function makePaymentLog($payment_obj = NULL)
	{
		if ($payment_obj)
		{
			$this->_load($payment_obj);
		}
		
		$payment_id = $this->insert();
		
		if ($payment_id){
			return $payment_id;
		}
		
		return false;
	}

	public function updatePayment($payment)
	{
		$props = $this->getPropertyList();

		foreach ($props as $prop)
		{
			if (isset($payment->$prop))
			{
				$this->_set($prop, $payment->$prop);
			}
		}

		$new_id = $this->save(true);

		if ( ! $new_id) return false;

		return $this->getById($new_id);
	}

	public function getById($id)
	{
		$r = $this->select(array(
			$this->getPK()	=> $id,
		));

		return ((count($r==1) &&  $r) ? array_shift($r) : false);
	}

	/**
	 * Сводка по статистике платежей для админа
	 */
	public function getSummaryStat() {
		$week_day = intval(date('w'));
		$stat = array(
			'day'	=> $this->getStatForPeriod(date('Y-m-d 00:00:00'), date('Y-m-d H:i:s')),
			'week' 	=> $this->getStatForPeriod( $week_day ? date('Y-m-d 00:00:00', time()-($week_day-1)*24*60*60) : date('Y-m-d 00:00:00', time()-6*24*60*60), date('Y-m-d H:i:s')),		
			'month' => $this->getStatForPeriod(date('Y-m-01 00:00:00'), date('Y-m-d H:i:s'))
		);
		return $stat;
	}
	
	/**
	 * Получаем суммарную статистику платежей за выбранный период
	 */
	public function getStatForPeriod($from, $to) {
		$res = $this->db->query('
			SELECT SUM(`payment_amount_from`) AS `stat`
			FROM `'.$this->table."`
			WHERE `payment_time` BETWEEN '$from' AND '$to' AND `payment_from` = 1
		")->result();
		
		$negative = $res[0]->stat ? $res[0]->stat : 0;

		$res = $this->db->query('
			SELECT SUM(`payment_amount_from`) AS `stat`
			FROM `'.$this->table."`
			WHERE `payment_time` BETWEEN '$from' AND '$to' AND `payment_to` = 1
		")->result();
		
		$positive = $res[0]->stat ? $res[0]->stat : 0;
		
		return $positive - $negative;
	}
	
	/**
	 * Получаем платежи пользователей системе
	 */
	public function getRefillPayments() 
	{
		return $this->db->query('
			SELECT `'.$this->table.'`.*, `users`.`user_login`
			FROM `'.$this->table.'`
				INNER JOIN `users` ON `'.$this->table.'`.`payment_from` = `users`.`user_id`
			ORDER BY `payment_time` DESC
		')->result();
	}

	public function getTotalUSD($payments)
	{
		$total_usd = 0;

		foreach ($payments as $payment)
		{
			if ($payment->status == 'not_payed')
			{
				$total_usd += $payment->amount_usd;
			}
		}

		return $total_usd;
	}

	public function getTotalLocal($view)
	{
		if (empty($view['Payments']))
		{
			return FALSE;
		}

		$totals = array();

		foreach ($view['Payments'] as $payment)
		{
			if ($payment->status == 'not_payed')
			{
				if (empty($totals[$payment->payment_currency]))
				{
					$totals[$payment->payment_currency] = $payment->payment_amount_to;
				}
				else
				{
					$totals[$payment->payment_currency] += (float)($payment->payment_amount_to);
				}
			}
		}

		$totals_strings = array();

		if ($totals)
		{
			foreach ($totals as $currency => $amount)
			{
				$totals_strings []= $amount . ' ' . $currency;
			}
		}

		return implode(' + ', $totals_strings);
	}

	public function getFilteredPayments($filter = array(), $from = NULL, $to = NULL, $extra_where = NULL)
	{
		$where = '1';
		
		// обход полей фильтра
		if (is_string($filter))
		{
			$where	= $filter;
		}
		else
		{
			foreach ($filter as $key=>$val) 
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
		if ($from && $to) 
		{
			$from_date = new DateTime($from);
			$from_date = $from_date->format('Y-m-d H:i:s');
			$to_date = new DateTime($to);
			$to_date->modify('+1 day');
			$to_date = $to_date->format('Y-m-d H:i:s');

			$where .= " AND `payment_time` BETWEEN '$from_date' AND '$to_date'";
		}
		else if ($from)
		{
			$from_date = new DateTime($from);
			$from_date = $from_date->format('Y-m-d H:i:s');

			$where .= " AND `payment_time` >= '$from_date'";
		}
		else if ($to)
		{
			$to_date = new DateTime($to);
			$to_date->modify('+1 day');
			$to_date = $to_date->format('Y-m-d H:i:s');

			$where .= " AND `payment_time` < '$to_date'";
		}

		
		// дополнительные условия
		if ( ! empty($extra_where))
		{
			$where .= $extra_where;
		}
		
		// погнали
		return $this->db->query('
			SELECT `'.$this->table.'`.*, `user_from`.`user_login` user_from, `user_to`.`user_login` user_to
			FROM `'.$this->table.'`
				LEFT OUTER JOIN `users` `user_to` ON `'.$this->table.'`.`payment_to` = `user_to`.`user_id` 
				LEFT OUTER JOIN `users` `user_from` ON `user_from`.`user_id` = `'.$this->table.'`.`payment_from` 
			WHERE '.$where.'
			ORDER BY `payment_id` DESC
		')->result();
	}
	
	private static function validate($payment)
	{
		if ($payment->payment_amount_from < 0 OR
			$payment->payment_amount_to < 0)
		{
			$temp = $payment->payment_amount_from;
			$payment->payment_amount_from = - $payment->payment_amount_to;
			$payment->payment_amount_to = - $temp;

			$temp = $payment->payment_from;
			$payment->payment_from = $payment->payment_to;
			$payment->payment_to = $temp;
			
			if ($payment->payment_amount_tax < 0)
			{
				$payment->payment_amount_tax *= -1;
			}			
		}
		
		return $payment;
	}

	public function processOrderPayment($order, $o2i, $is_repay)
	{
		$history = new stdClass();
		$history->payment_from				= $order->order_client;
		$history->payment_to				= $order->order_manager;
		$history->payment_amount_from		= $o2i->order2in_amount;
		$history->payment_amount_to			= $o2i->order2in_amount;
		$history->payment_details			= $o2i->order2in_details;
		$history->payment_purpose			= $is_repay ?
			'доплата заказа' :
			'оплата заказа';
		$history->payment_comment			= '№ ' . $order->order_id;
		$history->payment_type				= 'order';
		$history->payment_transfer_order_id	= $this->user->user_id.date('Y').date('m').date('d').date('h').date('i').date('s');
		$history->payment_currency			= strval($o2i->order2in_currency);
		$history->order_id					= $o2i->order_id;
		$history->order2in_id				= $o2i->order2in_id;
		$history->payment_service_id		= $o2i->order2in_payment_service;
		$history->payment_service_name		= $o2i->payment_service_name;
		$history->excess_amount				= $o2i->excess_amount;

		if ($o2i->is_countrypost)
		{
			$history->status = 'not_payed';
		}
		else
		{
			$history->status = 'sent_by_client';
		}

		// собираем валюты и курсы
		$ci = get_instance();
		$ci->load->model('OrderModel', 'Orders');
		$ci->load->model('CurrencyModel', 'Currencies');
		$ci->load->model('TaxModel', 'Taxes');

		if ($order->order_country_from AND
			$order->order_country_to)
		{
			$order->order_currency = $ci->Orders->getOrderCurrency($order->order_id);
			$history->payment_currency = $order->order_currency;
			$exchange_rate = $ci->Currencies->getExchangeRate('USD', $order->order_currency, 'manager');

			// округляем до центов в пользу посредника
			$history->amount_usd = ceil(
				floatval($o2i->order2in_amount) /
					floatval($exchange_rate) *
					100) *
				0.01;

			$history->usd_conversion_rate = $exchange_rate;
		}

		// погнали
		if ( ! $this->makePayment($history, true))
		{
			throw new Exception('Ошибка оплаты заказа. Попробуйте еще раз.');
		}

		if (empty($order->payed_date))
		{
			$order->payed_date = date('Y-m-d H:i:s');
		}

		// генерируем комиссию посредника
		$ci->Taxes->generateTax($order);
	}

	public function processImmediateOrderPayment($order, $payment)
	{
		$history = $payment;

		// собираем валюты и курсы
		$ci = get_instance();
		$ci->load->model('OrderModel', 'Orders');
		$ci->load->model('CurrencyModel', 'Currencies');
		$ci->load->model('TaxModel', 'Taxes');

		if ($order->order_country_from AND
			$order->order_country_to)
		{
			$order->order_currency = $ci->Orders->getOrderCurrency($order->order_id);
			$history->payment_currency = $order->order_currency;
			$exchange_rate = $ci->Currencies->getExchangeRate('USD', $order->order_currency, 'manager');

			// округляем до центов в пользу посредника
			$history->amount_usd = ceil(
				floatval($history->payment_amount_from) /
					floatval($exchange_rate) *
					100) *
				0.01;

			$history->usd_conversion_rate = $exchange_rate;
		}

		// погнали
		if ( ! $this->makePayment($history, true))
		{
			throw new Exception('Ошибка оплаты заказа. Попробуйте еще раз.');
		}

		if (empty($order->payed_date))
		{
			$order->payed_date = date('Y-m-d H:i:s');
		}

		// генерируем комиссию посредника
		$ci->Taxes->generateTax($order);
	}

	public function getLastPayedOrder($user_id)
	{
		$order = $this->db->query("
			SELECT order_id
			FROM `{$this->table}`
			WHERE payment_from = $user_id
			ORDER BY `payment_id` DESC
			LIMIT 1
		")->result();

		if (empty($order))
		{
			return FALSE;
		}

		$order = $order[0];
		return $order->order_id;
	}
}
?>