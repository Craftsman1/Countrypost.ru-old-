<div class='content syspay'>
	<? Breadcrumb::showCrumbs(); ?>
	<h2 class="choose_payment_type"><span class="choose_payment_title">Оплата заказа №<?= $order->order_id ?> (напрямую посреднику)</span> 
		<img class="help1" style="top: 3px;position: relative;" src="/static/images/mini_help.gif">
	</h2>
	<div class="choose_payment_container" <? echo (isset($is_countrypost_payments_allowed) AND $is_countrypost_payments_allowed)?'style="display: none;"':''; ?>>
	<? View::show('/client/elements/payments/manager_payment_box'); ?>
	</div>
	<? if (isset($is_countrypost_payments_allowed) AND $is_countrypost_payments_allowed) : ?>
	<br>
	<h2 class="choose_payment_type"><span class="choose_payment_title">Оплата заказа №<?= $order->order_id ?> (через Countrypost.ru)</span>
		<img class="help2" style="top: 3px;position: relative;" src="/static/images/mini_help.gif">
	</h2>
	<div class="choose_payment_container">
	<? View::show('/client/elements/payments/countrypost_payment_box'); ?>
	</div>
	<? endif; ?>
</div>
<script type="text/javascript">
	$(function() {
		$('.choose_payment_title').click(function()
		{
			$('.choose_payment_container').toggle();
		});
	
		$("img.help1").easyTooltip({
			tooltipId: "tooltip_id",
			content: '<div class="box"><div>Оплата заказа напрямую посреднику. Деньги вы переводите самостоятельно. <br> Для зачисления денег в заказ добавьте заявку.</div></div>'
		});
		$("img.help2").easyTooltip({
			tooltipId: "tooltip_id",
			content: '<div class="box"><div>Оплата заказа через сервис countrypost.ru. Деньги посреднику переводим мы.</div></div>'
		});

	});
</script>