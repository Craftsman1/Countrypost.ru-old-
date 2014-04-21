<?= $order->order_cost_payed ?> <?= $order->order_currency ?>
<? if ($order->order_cost > $order->order_cost_payed) : ?>
&nbsp;(Доплатить <?= $order->order_cost - $order->order_cost_payed ?> <?= $order->order_currency ?>)
<? elseif ($order->order_cost < $order->order_cost_payed) : ?>
&nbsp;(Остаток <?= $order->order_cost_payed - $order->order_cost ?> <?= $order->order_currency ?>)
<? endif; ?>