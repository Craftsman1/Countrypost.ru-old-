<div class="online_order_form">
	<? View::show('client/elements/showAddOrder/online'); ?>
	<h3>Добавить товар:</h3>
    <? View::show('client/elements/showAddProduct/online'); ?>
    <? if ( ! empty($orders)) : ?>
	<? View::show('client/ajax/showOrderDetails'); ?>
	<? endif; ?>
</div>