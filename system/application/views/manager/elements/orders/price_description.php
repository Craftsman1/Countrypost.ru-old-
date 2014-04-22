<?= $order->order_cost ?> <?= $order->currency ?>
<br>
<a href="javascript:void(0)" onclick="$('#pre_<?= $order->order_id ?>').toggle()">Подробнее</a>
<pre class="pre-href" id="pre_<?= $order->order_id ?>">
	<?= $order->order_products_cost + $order->order_delivery_cost ?> <?= $order->currency ?>
	+
	<?= $order->manager_tax ?> <?= $order->currency ?>
	+
	<?= $order->foto_tax ?> <?= $order->currency ?>
	+
	<?= $order->delivery_cost ?> <?= $order->currency ?>
	+
	<?= $order->extra_tax ?> <?= $order->currency ?>
</pre>