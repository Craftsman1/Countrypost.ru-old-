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

			$rates = $json->rates;

			// формируем кросскурсы
			foreach ($currencies as $currency)
			{
				foreach ($rates as $rate_currency => $rate)
				{
					if ($currency == $rate_currency)
					{
						continue;
					}

					$all_rates []= array(
						'currency_from' => $currency,
						'currency_to' => $rate_currency,
						'rate' => $rates->$currency / $rate,
						'service_name' => 'openexchangerates'
					);

					$all_rates []= array(
						'currency_from' => $rate_currency,
						'currency_to' => $currency,
						'rate' => $rate / $rates->$currency,
						'service_name' => 'openexchangerates'
					);
				}
			}

			// сохраняем все в базу
			$ci	= get_instance();
			$ci->load->model("ExchangeRateModel", "Rates");

			if ( ! $ci->Rates->updateRates($all_rates))
			{
				return FALSE;
			}

			return TRUE;
		}
		catch (Exception $ex)
		{
			return FALSE;
		}
	}
}
?>