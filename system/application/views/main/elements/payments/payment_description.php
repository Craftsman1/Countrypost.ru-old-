<? foreach ($services as $service) :	if ($service->payment_service_id == $payment->order2in_payment_service) : ?>	<b style="text-decoration: underline; font-weight: normal;"><?= $service->payment_service_name ?></b>	<br />	<? break; endif; endforeach; ?><? if ($payment->order2in_payment_service == 'bm' OR	$payment->order2in_payment_service == 'cc' OR	$payment->order2in_payment_service == 'so' OR	$payment->order2in_payment_service == 'op' OR	$payment->order2in_payment_service == 'sv' OR	$payment->order2in_payment_service == 'alf' OR	$payment->order2in_payment_service == 'wur' OR	$payment->order2in_payment_service == 'wud' OR	$payment->order2in_payment_service == 'con' OR	$payment->order2in_payment_service == 'cod' OR	$payment->order2in_payment_service == 'unr' OR	$payment->order2in_payment_service == 'und' OR	$payment->order2in_payment_service == 'gcr' OR	$payment->order2in_payment_service == 'gcd' OR	$payment->order2in_payment_service == 'vm' OR	$payment->order2in_payment_service == 'anr' OR	$payment->order2in_payment_service == 'and' OR	$payment->order2in_payment_service == 'cus') : ?><b>Номер карты:</b><?= $payment->order2in_details ?><br /><? elseif ($payment->order2in_payment_service == 'rbk' OR	$payment->order2in_payment_service == 'qw') : ?><b>Номер кошелька:</b><?= $payment->order2in_details ?><br /><? elseif ($payment->order2in_payment_service == 'mb') : ?><b>Email отправителя:</b><?= $payment->order2in_details ?><br /><? else : ?><b style="text-decoration: underline; font-weight: normal;">	<?= $payment->payment_service_name ?></b><br /><? if ( ! empty($payment->order2in_details)) : ?><b>Комментарий:</b><?= $payment->order2in_details ?><br /><? endif; ?><? View::show('main/elements/payments/payment_screenshot'); ?><? endif; ?><? if ($payment->order2in_payment_service == 'bm' OR	$payment->order2in_payment_service == 'cc' OR	$payment->order2in_payment_service == 'so' OR	$payment->order2in_payment_service == 'op' OR	$payment->order2in_payment_service == 'sv' OR	$payment->order2in_payment_service == 'alf' OR	$payment->order2in_payment_service == 'wur' OR	$payment->order2in_payment_service == 'con' OR	$payment->order2in_payment_service == 'cod' OR	$payment->order2in_payment_service == 'unr' OR	$payment->order2in_payment_service == 'und' OR	$payment->order2in_payment_service == 'gcr' OR	$payment->order2in_payment_service == 'gcd' OR	$payment->order2in_payment_service == 'vm' OR	$payment->order2in_payment_service == 'anr' OR	$payment->order2in_payment_service == 'and' OR	$payment->order2in_payment_service == 'cus') : ?>	<? View::show('main/elements/payments/payment_screenshot'); ?><? endif; ?>