<div class='content syspay'>
	<? Breadcrumb::showCrumbs(); ?>
	<h2>Оплата заказа №<?= $order->order_id ?> (через Countrypost.ru)</h2>
	<? View::show('/syspay/elements/client_payment_box'); ?>
</div>