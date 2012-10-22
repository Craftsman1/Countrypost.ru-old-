<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// Класс рассылки уведомлений клиенту, партнеру и админу
// Все исключения гасятся для предотвращения влияния ошибок рассылки на основную логику

// В функции рассылки уведомлений передаем либо данные партнера, либо клиента
// в зависимости от того, кому шлем уведомление
	
class Mailer {
	const NEW_ORDER_NOTIFICATION = 'Уважаемый(ая) %s, 
	
Сообщаем Вам, что клиент %s создал заказ №%s. Для просмотра заказа перейдите по этой ссылке:
%s

С наилучшими пожеланиями, 
Команда Countrypost.ru';
	const NEW_PACKAGE_NOTIFICATION = 'Уважаемый(ая) %s, 
	
Сообщаем Вам, что партнер %s создал посылку №%s. Для просмотра посылки перейдите по этой ссылке:
%s

С наилучшими пожеланиями, 
Команда Countrypost.ru';
	const NEW_PACKAGE_MANAGER_NOTIFICATION = 'Уважаемый(ая) %1$s, 
	
Сообщаем Вам, что Администратор создал посылку №%3$s. Для просмотра посылки перейдите по этой ссылке:
%4$s

С наилучшими пожеланиями, 
Команда Countrypost.ru';
	const NEW_ORDER_COMMENT_NOTIFICATION = 'Уважаемый(ая) %1$s, 
	
Сообщаем Вам, что в заказе №%3$s был добавлен новый комментарий. Чтобы просмотреть комментарий, перейдите по этой ссылке:
%4$s

С наилучшими пожеланиями, 
Команда Countrypost.ru';
	const NEW_PACKAGE_COMMENT_NOTIFICATION = 'Уважаемый(ая) %1$s, 
	
Сообщаем Вам, что в посылке №%3$s был добавлен новый комментарий. Чтобы просмотреть комментарий, перейдите по этой ссылке:
%4$s

С наилучшими пожеланиями, 
Команда Countrypost.ru';
	const NEW_ORDER_STATUS_NOTIFICATION = 'Уважаемый(ая) %1$s,
	
Сообщаем Вам, что статус заказа №%3$s изменился на "%5$s". Для просмотра заказа перейдите по этой ссылке:
%4$s

С наилучшими пожеланиями, 
Команда Countrypost.ru';
	const NEW_PACKAGE_STATUS_NOTIFICATION = 'Уважаемый(ая) %1$s, 
	
Сообщаем Вам, что статус посылки №%3$s изменился на "%5$s". Для просмотра посылки перейдите по этой ссылке:
%4$s

С наилучшими пожеланиями, 
Команда Countrypost.ru';
	const ORDER_DELETED_NOTIFICATION = 'Уважаемый(ая) %1$s,
	
Сообщаем Вам, что заказ №%3$s удален.

С наилучшими пожеланиями, 
Команда Countrypost.ru';
	const PACKAGE_DELETED_NOTIFICATION = 'Уважаемый(ая) %1$s, 
	
Сообщаем Вам, что посылка №%3$s удалена.

С наилучшими пожеланиями, 
Команда Countrypost.ru';
	const NEW_ORDER2IN_CLIENT_NOTIFICATION = 'Уважаемый(ая) %s, 
	
Сообщаем Вам, что клиент %s добавил заявку на пополнение счета №%s. Для просмотра заявки перейдите по этой ссылке:
%s

С наилучшими пожеланиями, 
Команда Countrypost.ru';
	const NEW_ORDER2OUT_CLIENT_NOTIFICATION = 'Уважаемый(ая) %s, 
	
Сообщаем Вам, что клиент %s добавил заявку на вывод денег №%s. Для просмотра заявки перейдите по этой ссылке:
%s

С наилучшими пожеланиями, 
Команда Countrypost.ru';
	const NEW_ORDER2OUT_MANAGER_NOTIFICATION = 'Уважаемый(ая) %s, 
	
Сообщаем Вам, что партнер %s добавил заявку на вывод денег №%s. Для просмотра заявки перейдите по этой ссылке:
%s

С наилучшими пожеланиями, 
Команда Countrypost.ru';
	const ORDER_COMPLETE_NOTIFICATION = 'Уважаемый(ая) %1$s,
Сообщаем Вам, что Ваш заказ №%3$s полностью доставлен. Просмотреть все доставленные товары и их фотографии Вы можете в своем аккаунте на сайте в разделе "Посылки ожидающие отправки".
 
Чтобы мы смогли отправить Ваш заказ, Вам нужно все доставленные товары объединить в одну или несколько посылок (как вам удобно), заполнить декларацию и оплатить международную доставку. После это мы в кратчайшие сроки соберем Вашу посылку и отправим ее Вам. 

С наилучшими пожеланиями, 
Команда Countrypost.ru';
	const TAOBAO_REGISTRATION_NOTIFICATION = 'Добавлена новая заявка на регистрацию аккаунта на Taobao.com.
	
Клиент: %1$s
Email: %2$s
Логин: %3$s
Пароль: %4$s';
	const ALIPAY_REFILL_NOTIFICATION = 'Добавлена новая заявка на пополнение счета Alipay.
	
Клиент: %1$s
Email: %2$s
Логин: %3$s
Пароль: %4$s
Сумма пополнения: ¥%5$s
Итого будет зачислено на Alipay: ¥%6$s
Итого списать со счета: $%7$s';
	const TAOBAO_PAYMENT_NOTIFICATION = 'Добавлена новая заявка на оплату заказа Taobao.com.
	
Клиент: %1$s
Email: %2$s
Платежи: %3$s
Итого: ¥%4$s ($%5$s)
Комиссия: $%6$s';

	const SUBJECT_NEW_COMMENT = 'Countrypost.ru - Новый комментарий';
	const SUBJECT_NEW_ORDER = 'Countrypost.ru - Новый заказ';
	const SUBJECT_NEW_PACKAGE = 'Countrypost.ru - Новая посылка';
	const SUBJECT_NEW_ORDER_STATUS = 'Countrypost.ru - Новый статус заказа';
	const SUBJECT_NEW_PACKAGE_STATUS = 'Countrypost.ru - Новый статус посылки';
	const SUBJECT_ORDER_DELETED_STATUS = 'Countrypost.ru - Заказ удален';
	const SUBJECT_PACKAGE_DELETED_STATUS = 'Countrypost.ru - Посылка удалена';
	const SUBJECT_NEW_ORDER2IN = 'Countrypost.ru - Новая заявка на пополнение счета';
	const SUBJECT_NEW_ORDER2OUT = 'Countrypost.ru - Новая заявка на вывод';
	const SUBJECT_ORDER_COMPLETE = 'Countrypost.ru - Ваш заказ доставлен';
	const SUBJECT_TAOBAO_REGISTRATION = 'Countrypost.ru - Новая заявка на регистрацию на Taobao.com';
	const SUBJECT_ALIPAY_REFILL = 'Countrypost.ru - Новая заявка на пополнение счета Alipay';
	const SUBJECT_TAOBAO_PAYMENT = 'Countrypost.ru - Новая заявка на оплату заказа Taobao.com';
	
	const HEADERS = 'From: "Countrypost.ru" <info@countrypost.ru>; Content-type: text/plain; charset=UTF-8';
	const ADMIN_EMAIL = 'at3@yandex.ru';
	const WEBMASTER_EMAIL = 'tuataramusic@gmail.com';
	const NOTIFICATIONS_ON = TRUE;
	const DEBUG_ON = FALSE;
	
	public static function sendAdminNotification(
		$subject, 
		$template, 
		$manager_id,
		$id,
		$user_id,
		$url,
		$Managers,
		$Users,
		$status = null)
	{
		if (!self::NOTIFICATIONS_ON) return;
		
		try
		{
			$manager = $manager_id ? $Managers->getManagerData($manager_id) : FALSE;
			$user = $user_id ? $Users->getById($user_id) : FALSE;
			
			$body = sprintf($template, 
				'Администратор',
				$user ? $user->user_login : ($manager ? $manager->user_login : ''),
				$id, 
				$url,
				$status);

			//print_r($body);

			mail(self::ADMIN_EMAIL, 
				"=?utf8?B?" . base64_encode($subject) . "?=",
				$body, 
				self::HEADERS);
				
			if (self::DEBUG_ON)
			{
				mail(self::WEBMASTER_EMAIL, 
				"=?utf8?B?" . base64_encode($subject) . "?=",
				$body, 
				self::HEADERS);
			}
		}
		catch (Exception $ex) {}
	}

	public static function sendManagerNotification(
		$subject, 
		$template, 
		$manager_id,
		$id,
		$user_id,
		$url,
		$Managers,
		$Users,
		$status = null)
	{
		if ( ! self::NOTIFICATIONS_ON) return;
		
		try
		{
			$manager = $manager_id ? $Managers->getManagerData($manager_id) : FALSE;
			
			if ($manager)
			{
				$user = $user_id ? $Users->getById($user_id) : FALSE;
				
				$body = sprintf($template, 
					$manager->manager_name.' '.$manager->manager_otc,
					$user ? $user->user_login : ($manager ? $manager->user_login : ''),
					$id, 
					$url,
					$status);
					
				//print_r($body);

				mail($manager->user_email, 
					"=?utf8?B?" . base64_encode($subject) . "?=",
					$body, 
					self::HEADERS);
					
				if (self::DEBUG_ON)
				{
					mail(self::WEBMASTER_EMAIL, 
					"=?utf8?B?" . base64_encode($subject) . "?=",
					$body, 
					self::HEADERS);
				}
			}
		}
		catch (Exception $ex) {}
	}

	public static function sendClientNotification(
		$subject, 
		$template, 
		$id,
		$user_id,
		$url,
		$Clients,
		$status = null)
	{
		if ( ! self::NOTIFICATIONS_ON) return;
		
		try
		{
			$client = $user_id ? $Clients->getClientById($user_id) : FALSE;
			
			if ($client->notifications_on)
			{
				$body = sprintf($template, 
					$client->client_name.' '.$client->client_otc,
					null,
					$id, 
					$url,
					$status);
					
				//print_r($body);

				mail($client->user_email, 
					"=?utf8?B?" . base64_encode($subject) . "?=",
					$body, 
					self::HEADERS);
					
				if (self::DEBUG_ON)
				{
					mail(self::WEBMASTER_EMAIL, 
					"=?utf8?B?" . base64_encode($subject) . "?=",
					$body, 
					self::HEADERS);
				}
			}
		}
		catch (Exception $ex) {}
	}
	
	public static function sendTaobaoRegisterNotification(
		$subject, 
		$template,
		$user_id,
		$user_email,
		$taobao_login,
		$taobao_password)
	{
		if ( ! self::NOTIFICATIONS_ON) return;
		
		try
		{
			$body = sprintf($template, 
				$user_id,
				$user_email,
				$taobao_login,
				$taobao_password);

			//print_r($body);

			mail(self::ADMIN_EMAIL, 
				"=?utf8?B?" . base64_encode($subject) . "?=",
				$body, 
				self::HEADERS);
				
			if (self::DEBUG_ON)
			{
				mail(self::WEBMASTER_EMAIL, 
				"=?utf8?B?" . base64_encode($subject) . "?=",
				$body, 
				self::HEADERS);
			}
		}
		catch (Exception $ex) {}
	}
	
	public static function sendAlipayRefillNotification(
		$subject, 
		$template,
		$user_id,
		$user_email, 
		$alipay_login,
		$alipay_password,
		$alipay_amount,
		$alipay_total,
		$payment_amount)
	{
		if ( ! self::NOTIFICATIONS_ON) return;
		
		try
		{
			$body = sprintf($template, 
				$user_id,
				$user_email, 
				$alipay_login,
				$alipay_password,
				$alipay_amount,
				$alipay_total,
				$payment_amount);

			//print_r($body);

			mail(self::ADMIN_EMAIL, 
				"=?utf8?B?" . base64_encode($subject) . "?=",
				$body, 
				self::HEADERS);
				
			if (self::DEBUG_ON)
			{
				mail(self::WEBMASTER_EMAIL, 
				"=?utf8?B?" . base64_encode($subject) . "?=",
				$body, 
				self::HEADERS);
			}
		}
		catch (Exception $ex) {}
	}
	
	public static function sendTaobaoPaymentNotification(
		$subject, 
		$template,
		$user_id,
		$user_email, 
		$payments,
		$payments_total,
		$payments_total_usd,
		$payments_tax)
	{
		if ( ! self::NOTIFICATIONS_ON) return;
		
		try
		{
			$payments_description = '';
			
			foreach ($payments as $index => $payment)
			{
				$payments_description .= sprintf('%1$s. %2$s - ¥%3$s
', 
					$index + 1, 
					html_entity_decode($payment[0]), 
					$payment[1]);
			}
			
			$body = sprintf($template, 
				$user_id,
				$user_email, 
				$payments_description,
				$payments_total,
				$payments_total_usd,
				$payments_tax);
				
			//print_r($body);

			mail(self::ADMIN_EMAIL, 
				"=?utf8?B?" . base64_encode($subject) . "?=",
				$body, 
				self::HEADERS);
				
			if (self::DEBUG_ON)
			{
				mail(self::WEBMASTER_EMAIL, 
				"=?utf8?B?" . base64_encode($subject) . "?=",
				$body, 
				self::HEADERS);
			}
		}
		catch (Exception $ex) {}
	}
}