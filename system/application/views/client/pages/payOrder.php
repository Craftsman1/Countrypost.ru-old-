<div class='content syspay'>
	<? Breadcrumb::showCrumbs(); ?>
	<h2>Оплата заказа №<?= $order->order_id ?> (напрямую посреднику)</h2>
	<? View::show('/client/elements/payments/manager_payment_box'); ?>
	<? if (isset($is_countrypost_payments_allowed) AND $is_countrypost_payments_allowed) : ?>
	<br>
	<h2>Оплата заказа №<?= $order->order_id ?> (через Countrypost.ru)</h2>
	<? View::show('/client/elements/payments/countrypost_payment_box'); ?>
	<? endif; ?>
</div>