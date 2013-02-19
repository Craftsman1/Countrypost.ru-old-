<div class='content syspay'>
	<? Breadcrumb::showCrumbs(); ?>
	<h2>Оплата заказа №<?= $order->order_id ?> (напрямую посреднику)</h2>
	<? View::show('/client/elements/payments/manager_payment_box'); ?>
	<!--br>
	<h2>Оплата заказа №<?= $order->order_id ?> (через Countrypost.ru)</h2-->
	<? //View::show('/client/elements/payments/countrypost_payment_box'); ?>
</div>