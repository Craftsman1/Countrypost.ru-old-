<?php
require_once BASE_CONTROLLERS_PATH.'BaseController'.EXT;
		
class Cron extends BaseController {
	function __construct()
	{
		parent::__construct();	

		$this->config->load('cron');
	}
	
	public function sendAdminCommentsNotifications()
	{
		$this->load->model('UserModel', 'Users');
		
		self::sendAdminPCommentsNotifications($this->Users);
		self::sendAdminOCommentsNotifications($this->Users);
	}
	
	private function sendAdminPCommentsNotifications($users)
	{
		try
		{
			$this->load->model('PCommentModel', 'PComments');
			
			if ($comments = $this->PComments->getUnansweredComments($this->config->item('admin_comment_notification_delay')))
			{
				foreach ($comments as $comment)
				{
					Mailer::sendAdminNotification(
						Mailer::SUBJECT_NEW_COMMENT, 
						Mailer::NEW_PACKAGE_COMMENT_NOTIFICATION, 
						0,
						$comment->pcomment_package, 
						0,
						"http://countrypost.ru/admin/showPackageDetails/{$comment->pcomment_package}#comments",
						null,
						$this->Users);
						
					$pcomment = $this->PComments->getById($comment->pcomment_id);
					
					if ($pcomment)
					{
						$pcomment->pcomment_admin_notification_sent = 1;
						$this->PComments->addComment($pcomment);
					}					

					print("Отправлено уведомление о комментарии к посылке №{$comment->pcomment_package}<br />");
				}
			}
		}
		catch (Exception $ex)
		{
			print_r($ex);
		}
	}

	private function sendAdminOCommentsNotifications($users)
	{
		try
		{
			$this->load->model('OCommentModel', 'OComments');
			
			if ($comments = $this->OComments->getUnansweredComments($this->config->item('admin_comment_notification_delay')))
			{
				foreach ($comments as $comment)
				{
					Mailer::sendAdminNotification(
						Mailer::SUBJECT_NEW_COMMENT, 
						Mailer::NEW_ORDER_COMMENT_NOTIFICATION, 
						0,
						$comment->ocomment_order, 
						0,
						"http://countrypost.ru/admin/showOrderDetails/{$comment->ocomment_order}#comments",
						null,
						$users);
						
					$ocomment = $this->OComments->getById($comment->ocomment_id);
					
					if ($ocomment)
					{
						$ocomment->ocomment_admin_notification_sent = 1;
						$this->OComments->addComment($ocomment);
					}
					
					print("Отправлено уведомление о комментарии к заказу №{$comment->ocomment_order}<br />");
				}
			}
		}
		catch (Exception $ex)
		{
			print_r($ex);
		}
	}

	public function updateCrossRateCNYToUAH()
	{
		$this->crossExchangeRateUpdate('USD', 'UAH', 'CNY');
	}
	public function crossExchangeRateUpdate($currencyFrom, $crossRateTo, $crossRateCurrencyFrom) {
		$this->load->library('curl');
		$data = $this->curl->get('https://api.privatbank.ua/p24api/pubinfo?exchange', array('coursid'=>5));
		$dataObject = simplexml_load_string($data);
		foreach($dataObject->row as $value ){
			$attributes = $value->children()->exchangerate;
			if ( $attributes['ccy'] == $currencyFrom && $attributes['base_ccy'] == $crossRateTo ) {
				$this->load->model('ExchangeRateModel');
				$crossRateFromValue = $this->ExchangeRateModel->getByCurrencies($currencyFrom, $crossRateCurrencyFrom);
				$crossRateToValue = $this->ExchangeRateModel->getByCurrencies($currencyFrom, $crossRateTo);
				$crossRateFromValue = ((float)$crossRateFromValue->rate) ? (float)$crossRateFromValue->rate:1;
				$crossRateToValue = ((float)$crossRateToValue->rate) ? (float)$crossRateToValue->rate:1;
				$crossRate =  ((1015/$crossRateFromValue)*$crossRateToValue)/1015;
				$updateResult = $this->ExchangeRateModel->updateCrossRate($crossRate, $crossRateCurrencyFrom, $crossRateTo);
				if ($updateResult == 'not updated' ) {
					echo 'Кросс-курс CNY к UAH не был обновлен. Возможно поле отстуствует в базе данных';
				} else {
					$crossRateNew = $this->ExchangeRateModel->getByCurrencies($crossRateCurrencyFrom, $crossRateTo);
					echo 'Кросс-курс CNY к UAH обновлен. Текущий курс ' . $crossRateNew->rate;
				}
			}
		}
	}
}