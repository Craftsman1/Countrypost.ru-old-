<? if ($order->order_cost_payed) : ?>
<br>
<?= $order->order_cost_payed ?> <?= $order->order_currency ?>
<? if ($order->order_cost < $order->order_cost_payed) : ?>
&nbsp;(Остаток <?= $order->order_cost_payed - $order->order_cost ?> <?= $order->order_currency ?>)
<? endif; ?>
<? endif; ?>
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
<br>
<div style="display: inline-block;">
	<div class="submit payButton">
		<div>
			<input type="button"
				   onclick="window.location = '/client/payOrder/<?= $order->order_id ?>';"
				   value="<?= $button_name ?> <?= $payableAmount ?> <?= $order->order_currency ?>">
		</div>
	</div>
</div>
<? endif; ?>
