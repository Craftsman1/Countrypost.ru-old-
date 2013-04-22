<? require_once BASE_CONTROLLERS_PATH . 'SyspayBaseController' . EXT;

class Syspay extends SyspayBaseController {
	private static $taxMap = array(
		'immediate' => array(
			'vi' => 'RK',
			'wmr' => 'WM',
			'qw' => 'QIWI',
			'ya' => 'RK',
			'ek' => 'RK',
			'rbk' => 'RK',
			'mm' => 'RK',
			'mr' => 'RK',
			'zp' => 'RK',
			'ca' => 'RK',
		),
		'dollar' => array(
			'wmz' => 'WMZ',
			'pp'  => 'PP'
		),
	);

	function __construct()
	{
		parent::__construct();	
	}
	
	function index()
	{
		if ( ! Check::user())
		{
			Func::redirect(BASEURL);
		}
		else
		{
			Func::redirect(BASEURL."{$this->user->user_group}/history");
		}
	}
		
	public function showSuccess()
	{
		try
		{
			if (empty($this->user->user_id))
			{
				View::showChild($this->viewpath.'/pages/showSuccess');
			}
			else
			{
				$this->load->model('PaymentModel', 'Payments');
				$order_id = $this->Payments->getLastPayedOrder($this->user->user_id);
				Func::redirect(BASEURL . "{$this->user->user_group}/order/$order_id");
			}
		}
		catch(Exception $ex)
		{
			Func::redirect(BASEURL);
		}
	}
	
	public function showFail()
	{
		
		View::showChild($this->viewpath.'/pages/showFail');
	}
	
	public function showResult($user_id = null){
		
		if (isset($_POST['OutSum']))
		{
			$this->getResultRK();
		}
		elseif (isset($_POST['operation_xml']))
		{
			$this->getResultLP($user_id);
		}
		elseif (isset($_POST['WMI_PAYMENT_NO']))
		{
			$this->getResultW1();
		}
		elseif (isset($_POST['LMI_PAYMENT_AMOUNT']))
		{
			$this->showResultWM();
		}
		else
		{
			View::showChild($this->viewpath.'/pages/showFail');
			return;
		}
	}

	public function showResultRK()
	{
		$this->getResultRK();
	}

    // paypal
    public function showResultPP()
	{
        //$this->callbackPP();
        $this->showSuccess();
    }

    public function callbackPP()
	{
        $req = 'cmd=_notify-validate';

        foreach ($_POST as $key => $value) $req .= "&$key=".urlencode(stripslashes($value));

        $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
        $host = PP_TEST ? PP_TEST_URL : PP_URL;
        preg_match("/https*?:\/\/([^\/]+)/i", $host, $host);
        $host = $host[1];
        $fp = fsockopen("ssl://$host", 443, $errno, $errstr, 60);

        if (!$fp) {
            //echo 'HTTP ERROR<br>';
            $this->showFail();
        } else {
            fputs ($fp, $header.$req);
            while (!feof($fp)) {
                $res = fgets($fp, 1024);
                if (strcmp($res, 'VERIFIED') == 0) {
                    if ($this->savePPOrder()) $this->showSuccess();
                    else $this->showFail();
                } else if (strcmp($res, 'INVALID') == 0) {
                    //echo 'Ошибка при ответе от PayPal';
                    $this->showFail();
                }
            }
            fclose ($fp);
        }
    }
    
    public function savePPOrder()
	{
        PayLog::put('PP');

        $addLog    = '';

        $tax_usd            = $_POST['custom'];
        $user_id            = $_POST['item_number'];
        $amount_usd         = $_POST['mc_gross'];
        $pp_transfer_id     = $_POST['txn_id'];
        $transfer_order_id  = $_POST['invoice'];
        $user_from          = $_POST['payer_email'];
        $sign               = $_POST['verify_sign'];

        $payment = new stdClass();
        $payment->payment_from              = "[PP] payer_email]: $user_from";
        $payment->payment_to                = $user_id;
        $payment->payment_tax               = $tax_usd;
        $payment->payment_amount_rur        = '';
        $payment->payment_amount_from       = $amount_usd;
        $payment->payment_amount_tax        = $tax_usd;
        $payment->payment_amount_to         = $amount_usd;
        $payment->payment_purpose           = 'зачисление на счет пользователя';
        $payment->payment_comment           = "PP Transfer ID: $pp_transfer_id";
        $payment->payment_type              = 'in';
        $payment->payment_status            = 'complite';
        $payment->payment_transfer_info     = "PP Transfer ID: $pp_transfer_id";
        $payment->payment_transfer_order_id = $transfer_order_id;
        $payment->payment_transfer_sign     = $sign;
        $payment->payment_service_id        = 'pp';

        $this->load->model('PaymentModel', 'Payment');
        $this->Payment->_load($payment);
        $r = $this->Payment->makeCharge();

        if (is_object($r)) {
            //echo 'NO ->>'.$r->getMessage();
            $addLog    = "Status: FAIL! ".$r->getMessage()."\n";
        } elseif((int)$r) {
            //echo "YES";
            $addLog    = "Status: OK! [payment_id=$r]\n";
        } else {
            //echo "NO ->> unknown merchant error!\n" ;
            $addLog    = "Status: FAIL! (unknown merchant error)\n";
        }

        PayLog::put('PP', $addLog);

        return true;
    }

	private static function getTax($payment_system, $section)
	{
		if (!empty(self::$taxMap[$section]) &&
			!empty(self::$taxMap[$section][$payment_system]))
		{
			return constant(self::$taxMap[$section][$payment_system].'_IN_TAX');
		}
		
		return 0;
	}
	
	private static function getExtra($payment_system, $section)
	{
		if (!empty(self::$taxMap[$section]) &&
			!empty(self::$taxMap[$section][$payment_system]))
		{
			return constant(self::$taxMap[$section][$payment_system].'_IN_EXTRA');
		}
		
		return 0;
	}
	
	public function showGate($order_id)
	{
		if ( ! $this->user)
		{
			Func::redirect('/main');
			return false;
		}

		// идентификатор платежа, формируется именно таким образом специально!
		$number			= $this->user->user_id + time();
		$section		= Check::str('section', 20, 1);
		$amount			= Check::int('total_ru');
		$amount_usd		= Check::float('total_usd');
		$amount_local	= Check::float('total_local');
		$payment_system	= Check::str('payment_selector', 3, 2);
		$tax	 		= 0;//self::getTax($payment_system, $section);
		$extra	 		= 0;//self::getExtra($payment_system, $section);

		// заполняем форму
		$this->getPrivilegedOrder($order_id,
			'Заказ недоступен.');

		$view_form	= array(
			'order_id'		=> $order_id,
			'number'		=> $number,
			'amount'		=> $amount,
			'amount_local'	=> $amount_local,
			'amount_usd'	=> $amount_usd,
			'User_tax'		=> $tax * $amount_usd * 0.01,
			'tax'			=> $tax,
			'extra'			=> $extra
        );

		if ($payment_system == 'qw')
		{
			$this->backupPayment($view_form, 'qw');
			View::show('/syspay/elements/form_immediate_qw', array(
				'psform' => $view_form));
		}
		else
		{
			View::show('/syspay/gate', array(
				'ps' => "{$section}_$payment_system",
				'psform' => $view_form
			));
		}
	}
	
	private function backupPayment($details, $payment_system)
	{//print_r($_POST);die();
		$this->load->model('PaymentDetailsModel', 'Payments');
		
		$payment = new stdClass();
		$payment->payment_details_number = $details['number'];
    	$payment->payment_details_user = $this->user->user_id;
    	$payment->payment_details_payment_system = $payment_system;
    	$payment->payment_details_amount = $details['amount_local'];
    	$payment->payment_details_amount_rur = $details['amount'];
    	$payment->order_id = $details['order_id'];
    	$payment->status = 'sent_by_client';

		if ( ! $this->Payments->addPayment($payment))
		{
			throw new Exception('Невозможно сохранить детали платежа. Попробуйте еще раз.');
		}
	}
	
	/**
	 * показывает описание платежной системы
	 *
	 */
	public function showPays($pay_id = null){
		
		View::showChild('syspay/pages/showPays', array('pay_id'	=> (int)$pay_id));
	}
	
	// ROBOKASSA
/*
Оповещение об оплате (ResultURL)

В случае успешного проведения оплаты робот системы проводит запрос по Result URL, с указанием следующих параметров (методом, выбранным в настройках):

OutSum=nOutSum&
InvId=nInvId&
SignatureValue=sSignatureValue
[&пользовательские_параметры]

Если в настройках в качестве метода отсылки данных был выбран Email, то в случае успешного проведения оплаты робот системы отправит вам сообщение на email, указанный в качестве ResultURL, с указанием параметров, указанных выше.

nOutSum
    -полученная сумма. Сумма будет передана в той валюте, которая была указана при регистрации магазина. Формат представления числа - разделитель точка.
nInvId
    - номер счета в магазине
sSignatureValue
    - контрольная сумма MD5 - строка представляющая собой 32-разрядное число в 16-ричной форме и любом регистре (всего 32 символа 0-9, A-F). Формируется по строке, содержащей некоторые параметры, разделенные ':', с добавлением sMerchantPass2 - (устанавливается через интерфейс администрирования) т.е. nOutSum:nInvId:sMerchantPass2[:пользовательские параметры, в отсортированном порядке]
    К примеру если при инициализации операции были переданы пользовательские параметры shpb=xxx и shpa=yyy то подпись формируется из строки ...:sMerchantPass2:shpa=yyy:shpb=xxx 


Скрипт, находящийся по Result URL должен проверить правильность контрольной суммы и соответствия суммы платежа ожидаемой сумме. При формировании строки подписи учтите формат представления суммы (в строку при проверке подписи вставляйте именно полученное значение, без какого-либо форматирования).

Данный запрос производится после получения денег, однако, до того как пользователь сможет перейти на Success URL. Перед скриптом магазина, расположенным по Success URL обязательно отрабатывает скрипт запроса к Result URL.

Факт успешности сообщения магазину об исполнении операции определяется по результату, возвращаемому обменному пункту. Результат должен содержать "OKnMerchantInvId", т.е. для счета #5 должен быть возвращен текст "OK5".

В случае невозможности связаться со скриптом по адресу Result URL (связь прерывается по time-out-у либо по отсутствию DNS-записи, либо получен не ожидаемый ответ) на email-адрес администратора магазина отправляется письмо и запрос Result URL считается завершенным успешно. В случае системаческого отсутствия связи между серверами магазина и обменного пункта лучше использовать метод определения оплаты с применением интерфейсов XML, а самый желательный и защищенный способ - совмещенный.
*/
	private function getResultRK()
	{
		$this->output->enable_profiler(false);
		
		try
		{
			$user_id		= Check::int('ShpUser');
			$amount			= Check::float('OutSum');
			$raw_amount		= Check::str('OutSum', 32, 1);
			$amount_usd		= Check::int('ShpAmount');
			$order_id		= Check::int('ShpComment');
			$tax_usd		= Check::float('ShpTax');
			$raw_tax_usd	= Check::str('ShpTax', 32, 1);
			$ptransfer		= Check::int('InvId');
			$rawSign		= Check::str('SignatureValue', 320,32);

			Check::reset_empties();
			if (Check::get_empties())
			{
				throw new Exception('Invalid params/one or more fields is empty!');
			}

			$sign = strtoupper(md5(join(':', array($raw_amount,
				$ptransfer,
				RK_PASS2,
				'ShpAmount='.$amount_usd,
				'ShpComment='.$order_id,
				'ShpTax='.$raw_tax_usd,
				'ShpUser='.$user_id))));
			
			if ($sign != $rawSign)
			{
				throw new Exception("Invalid signum! [$rawSign<==>$sign]");
			}
				
			##########	
			// TODO: OK
			##########
			$payment_obj = new stdClass();
			$payment_obj->payment_from				= $user_id;
			//$payment_obj->payment_to				= '[RK] ' . $ptransfer;// зачисление на счет пользователя
			$payment_obj->payment_tax				= 0;
			$payment_obj->payment_amount_rur		= $amount;
			$payment_obj->payment_amount_from		= $amount_usd;
			$payment_obj->payment_amount_tax		= $tax_usd;
			$payment_obj->payment_amount_to			= $amount_usd;
			$payment_obj->payment_purpose			= 'оплата заказа';
			$payment_obj->payment_comment			= '№ ' . $order_id;
			$payment_obj->payment_type				= 'order';
			$payment_obj->payment_status			= 'complite';
			$payment_obj->payment_transfer_info		= 'RK Transfer';
			$payment_obj->payment_transfer_order_id	= $ptransfer;
			$payment_obj->payment_transfer_sign		= $rawSign;
			$payment_obj->payment_service_id		= 'rk';
			$payment_obj->status					= 'not_payed';
			$payment_obj->order_id					= $order_id;

			$this->payOrder($order_id, $payment_obj, $amount_usd);

			$status	= 'OK' . $ptransfer;
		}
		catch (Exception $e)
		{
			##########	
			// TODO: FAIL!
			##########
			$status	= 'Fail! ('.$e->getMessage().')';
		}

		PayLog::put('RK', "Status:$status\n");
	}

	private function payOrder($order_id, $payment_obj, $amount_usd)
	{
		// находим заказ
		$this->load->model('OrderModel', 'Orders');
		$this->load->model('CurrencyModel', 'Currencies');

		$order = $this->Orders->getById($order_id);

		if (empty($order))
		{
			throw new Exception('Заказ не найден.');
		}

		// собираем оставшиеся данные
		$order->order_cost_payed += $amount_usd;
		$payment_obj->payment_to = $order->order_manager;

		// валюты и курсы
		if ($order->order_country_from AND
			$order->order_country_to)
		{
			$order->order_currency = $this->Orders->getOrderCurrency($order->order_id);
			$payment_obj->payment_currency = $order->order_currency;
			$exchange_rate = $this->Currencies->getExchangeRate('USD', $order->order_currency, 'manager');

			// округляем до центов в пользу посредника
			$payment_obj->amount_usd = ceil(
				floatval($payment_obj->payment_amount_from) /
					floatval($exchange_rate) *
					100) *
				0.01;

			$payment_obj->usd_conversion_rate = $exchange_rate;
		}

		// добавляем платеж в статистику
		$this->processOrderPayment($order, $payment_obj);

		// пересчитываем заказ
		if ( ! $this->Orders->recalculate($order))
		{
			throw new Exception('Невожможно пересчитать стоимость заказа. Попоробуйте еще раз.');
		}

		$this->Orders->saveOrder($order);
	}

	protected function processOrderPayment($order, $payment)
	{
		// записываем платеж в историю
		$this->load->model('PaymentModel', 'History');
		$this->History->processImmediateOrderPayment($order, $payment);
	}

	// Liqpay
	private function getResultLP($User_id)
	{
		$resp_sig = $_POST['signature'];
		$enc_resp = base64_decode($_POST['operation_xml']);
		$gen_sig = base64_encode(sha1(LP_MERCHANT_SIG2 .
			($enc_resp) .
			LP_MERCHANT_SIG2, 1));
		$status	= 'FAIL!';

		if ($gen_sig == $resp_sig)
		{
			/**
			 * сдесь производим транзакции внутри системы
			 */
			$paymentXML			= new SimpleXMLElement($enc_resp);
			Check::reset_empties();

			$user_id			= (int) $User_id;
			$amount				= (int) $paymentXML->amount;
			$LP_transfer_id		= (int) $paymentXML->transaction_id;
			$transfer_order_id	= (int) $paymentXML->order_id;
			$status				= Check::var_str((string) $paymentXML->status,16,1);
			$action				= Check::var_str((string) $paymentXML->action,16,1);
			$user_comment		= Check::var_str((string) $paymentXML->description,512,0);
			$payment_from		= Check::var_str((string) $paymentXML->sender_phone,16,0);


					if ($status == 'success' && !Check::get_empties() && $action == 'server_url'){

						// конвертируем в валюту сайта
						$this->load->model('CurrencyModel', 'Currencies');
						$usd = $this->Currencies->getById('USD');
						$amount_usd								= $amount / (float) $usd->cbr_exchange_rate;
						$amount_to_usd							= $amount / (1+(RK_IN_TAX / 100)) / (float) $usd->cbr_exchange_rate;
						$tax_usd								= $amount_usd - $amount_to_usd;

						$payment_obj = new stdClass();
						$payment_obj->payment_from				= 'LP[sender_phone]:'.$payment_from;// зачисление на счет пользователя
						$payment_obj->payment_to				= $user_id;
						$payment_obj->payment_tax				= LP_IN_TAX.'%';
						$payment_obj->payment_amount_rur		= $amount;
						$payment_obj->payment_amount_from		= $amount_usd;
						$payment_obj->payment_amount_tax		= $tax_usd;
						$payment_obj->payment_amount_to			= $amount_usd;
						$payment_obj->payment_purpose			= 'зачисление на счет пользователя';
						$payment_obj->payment_comment			= Func::utf2win($user_comment);
				    	$payment_obj->payment_type				= 'in';
				    	$payment_obj->payment_status			= 'complite';
				    	$payment_obj->payment_transfer_info		= 'LP Transfer ID:'.$LP_transfer_id;
				    	$payment_obj->payment_transfer_order_id	= $transfer_order_id;
				    	$payment_obj->payment_transfer_sign		= $resp_sig;

						try{
					    	$this->load->model('PaymentModel', 'Payment');
							$this->Payment->_load($payment_obj);
							$r = $this->Payment->makeCharge();

							if (is_object($r)){
								$status	= "FAIL";
								$desc	= $r->getMessage();
								$addLog	= "Status: FAIL! ".$r->getMessage()."\n";
							}elseif((int)$r){
								$status	= "OK";
								$desc	= "Заказ #" . $transfer_order_id . " оплачен!";
								$addLog	= "Status: OK! [payment_id=$r]\n";
							}else{
								$status	= "FAIL";
								$desc	= "unknown merchant error!";
								$addLog	= "Status: FAIL! (unknown merchant error)\n";
							}

						}catch (Exception $e){
							$status	= "FAIL";
							$desc	= $e->getMessage();
							$addLog	= "Status: FAIL! ($desc)\n";
						}

					}elseif ($action == 'result_url'){
						$desc = 'перенаправление клиента';
						if ($status == 'success') $this->showSuccess();
						elseif ($status == 'failure') $this->showFail();
						elseif ($status	== 'wait_secure') $this->showWaitLp();
					}
		}
		
		$addLog	= $enc_resp."\nUser_id:$user_id\nStatus:	$status ($desc)\n";
		
		PayLog::put('LP',$addLog);
		
		return $status == 'success' ? 1 : 0;
	}

	private function showWaitLP()
	{
		View::showChild($this->viewpath.'/pages/showWaitLP');
	}

	// Единый кошелек
	private function getResultW1()
	{
		
		#
		$this->output->enable_profiler(false);
		error_reporting(~E_ALL);
		#
		PayLog::put('W1');
		
		$state	= "";
		$desc	= "";
		
		// Проверка наличия необходимых параметров в POST-запросе
		if (!isset($_POST["WMI_SIGNATURE"])){
			$state	= "RETRY";
			$desc	= "Отсутствует параметр WMI_SIGNATURE";
			
		}elseif (!isset($_POST["WMI_PAYMENT_NO"])){
			$state	= "RETRY";
			$desc	= "Отсутствует параметр WMI_PAYMENT_NO";
			
			
		}elseif (!isset($_POST["WMI_ORDER_STATE"])){
			$state	= "RETRY";
			$desc	= "Отсутствует параметр WMI_ORDER_STATE";
		}
		
		// Извлечение всех параметров POST-запроса, кроме WMI_SIGNATURE
		foreach($_POST as $name => $value)
		{
			if ($name !== "WMI_SIGNATURE") $params[$name] = $value;
		}
		
		// Сортировка массива по именам ключей в порядке возрастания
		// и формирование сообщения, путем объединения значений формы
		uksort($params, "strcasecmp"); $values = "";
		
		$values	= join(null, $params);
		
		// Формирование подписи для сравнения ее с параметром WMI_SIGNATURE
		$signature = base64_encode(pack("H*", sha1($values . W1_KEY)));
		
		//Сравнение полученной подписи с подписью W1
		if ($signature == $_POST["WMI_SIGNATURE"])
		{
			if (strtoupper($_POST["WMI_ORDER_STATE"]) == "ACCEPTED" || strtoupper($_POST["WMI_ORDER_STATE"]) == "PROCESSING"){
			
				##########	
				// TODO: OK
				##########
				
					$user_comment		= Check::str('User_comment', 512,1);
					$user_id			= Check::int('User_id');
					$amount_usd			= Check::int('User_amount');
					$tax_usd			= Check::float('User_tax');
					$amount				= Check::float('WMI_PAYMENT_AMOUNT');
					$w1_transfer_id		= Check::str('WMI_ORDER_ID', 64,1);
					$transfer_order_id	= Check::int('WMI_PAYMENT_NO');
					$user_from			= Check::str('WMI_TO_USER_ID', 64,1);// не смотря на название поля, это кошелек клиента
					
					$user_comment		= Check::var_str(base64_decode(substr($user_comment, 6)), 512,1);
					
					
					$payment_obj = new stdClass();
					$payment_obj->payment_from				= '[W1] WMI_TO_USER_ID: '.$user_from;// зачисление на счет пользователя
					$payment_obj->payment_to				= $user_id;
					$payment_obj->payment_tax				= W1_IN_TAX.'%';
					$payment_obj->payment_amount_rur		= $amount;
					$payment_obj->payment_amount_from		= $amount_usd;
					$payment_obj->payment_amount_tax		= $tax_usd;
					$payment_obj->payment_amount_to			= $amount_usd;
					$payment_obj->payment_purpose			= 'зачисление на счет пользователя';
					$payment_obj->payment_comment			= Func::utf2win($user_comment);
			    	$payment_obj->payment_type				= 'in';
			    	$payment_obj->payment_status			= 'complite';
			    	$payment_obj->payment_transfer_info		= 'W1 Transfer ID: '.$w1_transfer_id;
			    	$payment_obj->payment_transfer_order_id	= $transfer_order_id;
			    	$payment_obj->payment_transfer_sign		= $signature;
			    	$payment_obj->payment_service_id		= 'w1';
			    	
					$this->load->model('PaymentModel', 'Payment');
					$this->Payment->_load($payment_obj);
					$r = $this->Payment->makeCharge();
					
					if (is_object($r)){
						$state	= "CANCEL";
						$desc	= $r->getMessage();
						$addLog	= "Status: FAIL! ".$r->getMessage()."\n";
					}elseif((int)$r){
						$state	= "OK";
						$desc	= "Заказ #" . $_POST["WMI_PAYMENT_NO"] . " оплачен!";
						$addLog	= "Status: OK! [payment_id=$r]\n";
					}else{
						$state	= "CANCEL";
						$desc	= "unknown merchant error!";
						$addLog	= "Status: FAIL! (unknown merchant error)\n";
					}
				
//			}else if (strtoupper($_POST["WMI_ORDER_STATE"]) == "PROCESSING"){
//				
//				##########
//				// TODO: OK with manual commit
//				##########
//				
//				$state	= "OK";
//				$desc	= "Заказ #" . $_POST["WMI_PAYMENT_NO"] . " оплачен!";
//		
//				// Данная ситуация возникает, если в платежной форме WMI_AUTO_ACCEPT=0.
//				// В этом случае интернет-магазин может принять оплату или отменить ее.
				
			}else if (strtoupper($_POST["WMI_ORDER_STATE"]) == "REJECTED"){
				
				##########
				// TODO: FAIL
				##########
		
				$state	= "OK";
				$desc	= "Заказ #" . $_POST["WMI_PAYMENT_NO"] . " отменен!";
				
			}else{
				// Случилось что-то странное, пришло неизвестное состояние заказа
		
				$state	= "RETRY";
				$desc	= "Неверное состояние ". $_POST["WMI_ORDER_STATE"];
				
			}
			
		}else{
			// Подпись не совпадает, возможно вы поменяли настройки интернет-магазина... или вас пытаются наипать...
			$state	= "CANCEL";
			$desc	= "Неверная подпись " . $_POST["WMI_SIGNATURE"]. "<==>$signature";
		}

		$addLog	= "Answer:\"WMI_RESULT=$state&WMI_DESCRIPTION=".(urlencode($desc))."\"\nDecoded Desc:\"$desc\"\n";
		
		PayLog::put('W1',$addLog);
		
		echo "WMI_RESULT=".$state.'&WMI_DESCRIPTION='.urlencode($desc);
		
	}

	// WMR
	public function showResultWM()
	{
		$this->output->enable_profiler(false);
		PayLog::put('WM');
		
		$addLog	= '';

		if (Check::int('LMI_PREREQUEST'))
		{
			##########
			// TODO: делаем какую-нить пакость, если она нужна...
			##########
			echo "YES";
		}
		else
		{
			// впринципе нам не надо проверять по предзапросу сумму перевода и некоторые другие данные, тк мы начислим именно столько сколько нам перевели
			$signStr = WM_PURSE .
				$_POST['LMI_PAYMENT_AMOUNT'] .
				$_POST['LMI_PAYMENT_NO'] .
				$_POST['LMI_MODE'] .
				$_POST['LMI_SYS_INVS_NO'] .
				$_POST['LMI_SYS_TRANS_NO'] .
				$_POST['LMI_SYS_TRANS_DATE'] .
				WM_SECRET_KEY .
				$_POST['LMI_PAYER_PURSE'] .
				$_POST['LMI_PAYER_WM'];

			$sign = strtoupper(md5($signStr));
			
			if ($sign != $_POST['LMI_HASH'])
			{
				##########
				// TODO: FAIL! НЕ ВЕРНАЯ ПОДПИСЬ!
				##########
				echo 'NO';
				$addLog	= "Status: FAIL! Не верная цифровая подпись!\nSignStr:$signStr\nCalcSign:$sign\nRespSign:".$_POST['LMI_HASH']."\n";
			}
			else
			{
				##########	
				// TODO: OK
				##########
				$amount				= Check::float('User_comment');
				$tax_usd			= Check::float('User_tax');
				$user_id			= Check::int('User_id');
				$order_id			= Check::int('order');

				$amount_ru			= Check::int('LMI_PAYMENT_AMOUNT');
				$wm_transfer_id		= Check::int('LMI_SYS_TRANS_NO');
				$transfer_order_id	= Check::int('LMI_PAYMENT_NO');

				$payment_obj = new stdClass();
				$payment_obj->payment_from				= $user_id;
				$payment_obj->payment_tax				= WM_IN_TAX.'%';
				$payment_obj->payment_amount_rur		= $amount_ru;
				$payment_obj->payment_amount_from		= $amount;
				$payment_obj->payment_amount_tax		= $tax_usd;
				$payment_obj->payment_amount_to			= $amount;
				$payment_obj->payment_purpose			= 'оплата заказа';
				$payment_obj->payment_comment			= '№ ' . $order_id;
				$payment_obj->payment_type				= 'order';
				$payment_obj->payment_status			= 'complite';
				$payment_obj->payment_transfer_info		= 'WM Transfer ID: '.$wm_transfer_id;
				$payment_obj->payment_transfer_order_id	= $transfer_order_id;
				$payment_obj->payment_transfer_sign		= $sign;
				$payment_obj->payment_service_id		= 'wm';
				$payment_obj->order_id					= $order_id;
				$payment_obj->status					= 'not_payed';

				$this->payOrder($order_id, $payment_obj, $amount);
			}
		}
		
		PayLog::put('WM', $addLog);
		return;
	}

	// Qiwi RUB
	public function showResultQW()
	{
		$this->output->enable_profiler(false);
		PayLog::put('QW');
		$addLog	= '';

		// 300 это исключение для киви
		$resultCode = 300;

		// парсим хмл
		$i = file_get_contents('php://input');
		
		preg_match('/<login>(.*)?<\/login>/', $i, $m1);
		preg_match('/<password>(.*)?<\/password>/', $i, $m2);
		preg_match('/<txn>(.*)?<\/txn>/', $i, $m3);
		preg_match('/<status>(.*)?<\/status>/', $i, $m4);

		// сравнение нашего пароля с полученным,если не равны код "150"
		$hash = strtoupper(md5($m3[1] . strtoupper(md5(constant('QIWI_PASS')))));
		$resultCode = ($hash === $m2[1]) ? 0 : 150;
		
		if ($resultCode == 0 && $m4[1] == 60)
		{
			try
			{
				//login: $m1[1] password: $m2[1] txn: $m3[1] status: $m4[1]
				$this->load->model('PaymentDetailsModel', 'Payments');
				$payment = $this->Payments->getPaymentByNumber($m3[1]);
				
				if ( ! $payment)
				{
					throw new Exception('Детали платежа не найдены.');
				}
				
				$payment_obj = new stdClass();
				$payment_obj->payment_from				= $payment->payment_details_user;//'[QW] '.$payment->payment_details_number;
				//$payment_obj->payment_to				= $payment->payment_details_user;
				$payment_obj->payment_tax				= $payment->payment_details_tax;
				$payment_obj->payment_amount_rur		= $payment->payment_details_amount_rur;
				$payment_obj->payment_amount_from		= $payment->payment_details_amount;
				$payment_obj->payment_amount_tax		= $payment->payment_details_tax;
				$payment_obj->payment_amount_to			= $payment->payment_details_amount;
				$payment_obj->payment_purpose			= 'оплата заказа';
				$payment_obj->payment_type				= 'order';
				$payment_obj->payment_status			= 'complite';
				$payment_obj->payment_transfer_info		= 'QW Transfer ID: '.$payment->payment_details_number;
				$payment_obj->payment_transfer_order_id	= $payment->payment_details_number;
				$payment_obj->payment_transfer_sign		= $i;
				$payment_obj->payment_service_id		= $payment->payment_details_payment_system;
				$payment_obj->status					= 'not_payed';
				$payment_obj->order_id					= $payment->order_id;

				$this->payOrder($payment_obj->order_id, $payment_obj, $payment->payment_details_amount);
			}
			catch (Exception $ex)
			{
				print($ex->getMessage());
				$resultCode = 300;
				$addLog	= "Status: FAIL! ".$ex->getMessage()."\n";
			}
		}
		else
		{
			// не распарсился файл или неверный статус
			/*$resultCode = 300;
			$addLog	= "Status: FAIL! code: $resultCode, status: {$m4[1]}\n";*/
		}
		
		PayLog::put('QW', $addLog);
		
		// ответ для киви
		header('content-type: text/xml; charset=UTF-8');
		echo '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://client.ishop.mw.ru/"><SOAP-ENV:Body><ns1:updateBillResponse><updateBillResult>' . 
			$resultCode .
			'</updateBillResult></ns1:updateBillResponse></SOAP-ENV:Body></SOAP-ENV:Envelope>';
	}

	// WMZ
	public function showResultWMZ()
	{
		PayLog::put('WMZ');
		$addLog	= '';

		// обрабатываем предзапрос
		if (Check::int('LMI_PREREQUEST'))
		{
			echo "YES";
		}
		else
		{
			// впринципе нам не надо проверять по предзапросу сумму перевода и некоторые другие данные, тк мы начислим именно столько сколько нам перевели
			$signStr = WMZ_PURSE .
				$_POST['LMI_PAYMENT_AMOUNT'] .
				$_POST['LMI_PAYMENT_NO'] .
				$_POST['LMI_MODE'] .
				$_POST['LMI_SYS_INVS_NO'] .
				$_POST['LMI_SYS_TRANS_NO'] .
				$_POST['LMI_SYS_TRANS_DATE'] .
				WM_SECRET_KEY .
				$_POST['LMI_PAYER_PURSE'] .
				$_POST['LMI_PAYER_WM'];

			$sign = strtoupper(md5($signStr));
			
			if ($sign != $_POST['LMI_HASH'])
			{
				echo 'NO';
				$addLog	= "Status: FAIL! Не верная цифровая подпись!\nSignStr:$signStr\nCalcSign:$sign\nRespSign:".$_POST['LMI_HASH']."\n";
			}
			else
			{
				##########	
				// TODO: OK
				##########
				$tax_usd			= Check::float('User_tax');
				$user_id			= Check::int('User_id');
				$order_id			= Check::int('order');
				$amount_usd			= $_POST['LMI_PAYMENT_AMOUNT'];
				$amount_local		= Check::int('User_amount');
				$wm_transfer_id		= Check::int('LMI_SYS_TRANS_NO');
				$transfer_order_id	= Check::int('LMI_PAYMENT_NO');

				$payment_obj = new stdClass();
				$payment_obj->payment_from				= $user_id;
				$payment_obj->payment_tax				= $tax_usd;
				$payment_obj->payment_amount_rur		= $amount_local;
				$payment_obj->amount_usd				= $amount_usd;
				$payment_obj->payment_amount_from		= $amount_local;
				$payment_obj->payment_amount_tax		= $tax_usd;
				$payment_obj->payment_amount_to			= $amount_local;
				$payment_obj->payment_purpose			= 'оплата заказа';
				$payment_obj->payment_comment			= '№ ' . $order_id;
				$payment_obj->payment_type				= 'order';
				$payment_obj->payment_status			= 'complite';
				$payment_obj->payment_transfer_info		= 'WMZ Transfer ID: ' . $wm_transfer_id;
				$payment_obj->payment_transfer_order_id	= $transfer_order_id;
				$payment_obj->payment_transfer_sign		= $sign;
				$payment_obj->payment_service_id		= 'wmz';
				$payment_obj->status					= 'not_payed';
				$payment_obj->order_id					= $order_id;

				$this->payOrder($order_id, $payment_obj, $amount_local);
			}
		}
		
		PayLog::put('WMZ', $addLog);
		return;
	}
}