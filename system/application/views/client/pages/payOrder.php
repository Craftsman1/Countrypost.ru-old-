<div class='content syspay'>
	<? Breadcrumb::showCrumbs(); ?>
	<h2 class="choose_payment_type"><span class="choose_payment_title">Оплата заказа №<?= $order->order_id ?> (напрямую посреднику)</span> 
		<span class="help">?</span>
		<div class="help_text">Тест первой подсказки</div>
	</h2>
	<div class="choose_payment_container" <? echo (isset($is_countrypost_payments_allowed) AND $is_countrypost_payments_allowed)?'style="display: none;"':''; ?>>
	<? View::show('/client/elements/payments/manager_payment_box'); ?>
	</div>
	<? if (isset($is_countrypost_payments_allowed) AND $is_countrypost_payments_allowed) : ?>
	<br>
	<h2 class="choose_payment_type"><span class="choose_payment_title">Оплата заказа №<?= $order->order_id ?> (через Countrypost.ru)</span>
		<span class="help">?</span>
		<div class="help_text">Тест второй подсказки</div>
	</h2>
	<div class="choose_payment_container">
	<? View::show('/client/elements/payments/countrypost_payment_box'); ?>
	</div>
	<? endif; ?>
</div>