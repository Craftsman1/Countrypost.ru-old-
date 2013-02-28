<? $payableAmount =
	($order->order_cost > $order->order_cost_payed) ?
	$order->order_cost - $order->order_cost_payed :
	0;

if ((in_array($order->order_status, $payable_statuses) AND
	$payableAmount) OR
	$show_caption) : ?>
<?= $payableAmount ?> <?= $order->order_currency ?>
<? endif; ?>
<? if ($order->order_cost_payed) : ?>
 (оплачено <?= $order->order_cost_payed ?> <?= $order->order_currency ?>)
<? endif; ?>
