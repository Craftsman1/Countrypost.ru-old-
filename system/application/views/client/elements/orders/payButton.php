<div class="float-left payButton" style="vertical-align: bottom;height: 23px;padding-top: 9px;margin-right: 5px!important;">
	<?= $order->order_cost_payed ?> <?= $order->order_currency ?>
</div>
<? $payableAmount =
	($order->order_cost > $order->order_cost_payed) ?
	$order->order_cost - $order->order_cost_payed :
	0;

$button_name =
	($order->order_cost_payed > 0 AND
		$order->order_cost > $order->order_cost_payed) ?
	'Доплатить' :
	'Оплатить';

if (in_array($order->order_status, $payable_statuses) AND
	$payableAmount) : ?>
<div class="admin-inside submit float-left payButton"
	 id='save_order_button'>
		<div>
			<input type="button" onclick="window.location = '/client/payOrder/<?= $order->order_id ?>';"
				   value="<?= $button_name ?> <?= $payableAmount ?> <?=
					   $order->order_currency ?>">
		</div>
</div>
<? endif; ?>
