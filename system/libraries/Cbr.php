<?
/*
** Класс CBR
** Производит запрос курсов валют с Веб службы Центробанка России
*/
class CBR {
	const		WSDL = "http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL";	// WSDL службы Центробанка
	protected	$soap;
	protected	$soapDate;
	public		$currencyCodes = array();	
	public 		$tempFolder = "tmp/cbr";
	
	public function __construct()
	{
		$this->soap = new SoapClient(CBR::WSDL);
	}
	
	protected function getSOAPDate($timeStamp, $withTime = false)
	{
		$soapDate = date("Y-m-d", $timeStamp);
		return ($withTime) ? $soapDate .  "T" . date("H:i:s", $timeStamp) :	$soapDate . "T00:00:00";
	}
	
	protected function getBaseXML($date)
	{
		$currentDate = self::getSOAPDate($date);

		if ($currentDate != $this->soapDate)
		{
			$this->soapDate		= $currentDate;
			$params["On_date"]	= $currentDate;
			$response			= $this->soap->GetCursOnDateXML($params);
			
			return $response->GetCursOnDateXMLResult->any;
		}
		
		return false;		
	}	

	public function getRate($currency, $date = 0)
	{
		// обновляем курсы 2 раза в день
		if ( ! $date)
		{
			$time = time();
			$date	= $time - ($time % (60 * 60 * 12));
		}
		
		$xml = simplexml_load_string($this->getXML($date));
		$result = $xml->xpath('/ValuteData/ValuteCursOnDate[VchCode="'.$currency->currency_name.'"]');
		
		if (count($result) == 0)
		{
			return 0;
		}
		
		$result = array_shift($result);
		$v = $result->Vcurs;
		$factor = $result->Vnom;
		
		return str_replace(',', '.', $v) * (100 + $currency->currency_tax) * 0.01 / $factor;
	}
	
	public function getCrossRate($currency_from, $currency_to, $date = 0)
	{
		if ( ! $date)
		{
			$time = time();
			$date	= $time - ($time % (60 * 60 * 24));
		}
		
		$xml = simplexml_load_string($this->getXML($date));

		$result_from = $xml->xpath('/ValuteData/ValuteCursOnDate[VchCode="'.$currency_from->currency_name . '"]');
		$result_to = $xml->xpath('/ValuteData/ValuteCursOnDate[VchCode="'.$currency_to->currency_name.'"]');
		
		if (count($result_from) == 0)
		{
			return 1;
		}
		
		$result_from = array_shift($result_from);
		$factor_from = $result_from->Vnom;
		$result_from = $result_from->Vcurs;
		
		if (count($result_to) == 0)
		{
			return 1;
		}
		
		$result_to = array_shift($result_to);
		$factor_to = $result_to->Vnom;
		$result_to = $result_to->Vcurs;
		
		$v = (str_replace(',', '.', $result_to)  * $factor_from) / 
			(str_replace(',', '.', $result_from) * $factor_to);
		
		return $v * (100 + $currency_from->currency_tax) * 0.01;
	}
	
	public function getCurrencyCodes()
	{
		$xml	= simplexml_load_string($this->getXML(time()));
		$xPath	= "/ValuteData/ValuteCursOnDate";
		$allCurrencies = $xml->xpath($xPath);
		
		foreach ($allCurrencies as $currency)
		{
			$code = trim($currency->VchCode);
			$name = trim($currency->Vname);
			$this->currencyCodes[$code] = $name;
		}
		
		return ($this->currencyCodes);
	}
	
	public function getAllCurrencyInfo()
	{
		$xml = simplexml_load_string($this->getXML(time()));
		$xPath = "/ValuteData/ValuteCursOnDate";
		return $xml->xpath($xPath);
	}
	
	protected function getXML($date)
	{
		$cacheFile = md5($this->getSOAPDate($date)) . ".xml";
		
		if ($this->tempFolder)
			$cacheFile = $this->tempFolder . $cacheFile;
			
		if (!file_exists($cacheFile))
		{
			$result = $this->getBaseXML($date);
			file_put_contents($cacheFile, $result);
		}
		else
		{
			$result =  file_get_contents($cacheFile);
		}
		
		return $result;	
	}
	
	public function getRates($currencyModel)
	{
		try
		{
			// читаем минимальные курсы
			$ci	= get_instance();
			$ci->load->model("ConfigModel", "ConfigModel");
			$config = $ci->ConfigModel->getConfig();
			$currencies = $currencyModel->getArray();
			
			foreach ($currencies as $currency)
			{
				$currency->cbr_exchange_rate = $this->getRate($currency);
				$currency->cbr_cross_rate = str_replace(',', '', number_format($this->getCrossRate($currency, $currencies['USD']), 2));
				
				// проверяем минимальные курсы
				self::normalizeCurrency($currency, $config);
				
				$currencyModel->saveCurrency($currency);				
			}
			
			return true;
		}
		catch (Exception $ex)
		{
			return false;
		}
	}
	
	protected static function normalizeCurrency($currency, $config)
	{
		if (isset($config["min_{$currency->currency_name}_rate"]))
		{
			$min_rate = $config["min_{$currency->currency_name}_rate"]->config_value;
			
			if ($currency->currency_name == 'USD' || $currency->currency_name == 'EUR')
			{
				if ($min_rate > $currency->cbr_exchange_rate)
				{
					$currency->cbr_exchange_rate = $min_rate;
				}
			}
			else
			{
				if ($min_rate > $currency->cbr_cross_rate)
				{
					$currency->cbr_cross_rate = $min_rate;
				}
			}
		}
	}	
}
?>