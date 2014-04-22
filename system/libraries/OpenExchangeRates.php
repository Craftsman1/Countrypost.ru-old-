<?
/*
** Грузит курсы доллара и конвертит их во все остальные курсы c
** http://openexchangerates.org/api/latest.json?app_id=5261519322ea4686ad3fdc183f3fbabc
*/
class OpenExchangeRates {
	const URL = "http://openexchangerates.org/api/latest.json?app_id=5261519322ea4686ad3fdc183f3fbabc";
	public $tempFolder = "tmp/openexchangerates";
	
	public function __construct()
	{
	}
	
	public function getRates()
	{
		try
		{
			$currencies = array('USD', 'RUB', 'KZT', 'UAH');

			// делаем запрос всех курсов доллара
			$response = file_get_contents(OpenExchangeRates::URL);
			$json = json_decode($response);
			$all_rates = array();

			if (empty($json->rates))
			{
				return FALSE;
			}

			$rates = self::objectToArray($json->rates);
//print_r($rates);die();
			// формируем кросскурсы
			$ci	= get_instance();
			$ci->load->model("ExchangeRateModel", "Rates");

			foreach ($currencies as $currency)
			{
				foreach ($rates as $rate_currency => $rate)
				{
					if ($currency == $rate_currency)
					{
						continue;
					}

					// прямой курс
					$db_rate = $ci->Rates->getByCurrencies(
						$currency,
						$rate_currency
					);

					$db_rate->rate = ($rates[$rate_currency] / $rates[$currency]);
					$db_rate->service_name = 'openexchangerates';

					$all_rates []= $db_rate;

					// обратный
					$db_rate = $ci->Rates->getByCurrencies(
						$rate_currency,
						$currency
					);

					$db_rate->rate = ($rates[$currency] / $rates[$rate_currency]);
					$db_rate->service_name = 'openexchangerates';

					$all_rates []= $db_rate;
				}
			}

			// сохраняем все в базу
			if ( ! $ci->Rates->updateRates($all_rates))
			{
				return FALSE;
			}

			return TRUE;
		}
		catch (Exception $ex)
		{print_r($ex->getMessage());
			return FALSE;
		}
	}

	private static function objectToArray($d)
	{
		if (is_object($d))
		{
			// Gets the properties of the given object
			// with get_object_vars function
			$d = get_object_vars($d);
		}

		return $d;
	}
}
?>