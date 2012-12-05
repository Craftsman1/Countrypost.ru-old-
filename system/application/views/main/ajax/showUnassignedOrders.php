<? $order_link = BASEURL . 'main/order/'; ?>
<div class='table centered_th centered_td' style="margin:0;" id="unassignedOrders">
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<table>
		<tr>
			<th>Номер заказа</th>
			<th>Заказать из</th>
			<th>Доставить в</th>
			<th>Примерная<br>стоимость</th>
			<th>Примерный<br>вес</th>
			<th>Предложений<br>от посредников</th>
		</tr>
		<? if ($orders) : foreach($orders as $order) : ?>
		<tr>
			<td>
				<a href="<?=$order_link?><?=$order->order_id?>"><b><?=$order->order_id?></b></a>
				<br />
				<? if ($order->order_type == 'online' OR $order->order_type == 'offline') : ?>
				<?= $order->order_type ?> заказ
				<? elseif ($order->order_type == 'service') : ?>
				Услуга
				<? elseif ($order->order_type == 'delivery') : ?>
				Доставка
				<? endif;?>
				<br />
				<? if ($order->package_day == 0) : ?>
				<?=$order->package_day == 0 ? "" : $order->package_day.' '.humanForm((int)$order->package_day, "день", "дня", "дней")?> <?=$order->package_hour == 0 ? "" : $order->package_hour.' '.humanForm((int)$order->package_hour, "час", "часа", "часов")?> назад
				<? else : ?>
				<?=$order->order_date?>
				<? endif; ?>
			</td>
			<td style="text-align:left;">
				<img src="/static/images/flags/<?= $order->order_country_from_en ?>.png" style="float:left;margin-right:10px;" />
				<b style="position:relative;top:6px;"><?=$order->order_country_from ?></b>
			</td>
			<td style="text-align:left;">
				<img src="/static/images/flags/<?= $order->order_country_to_en ?>.png" style="float:left;margin-right:10px;" />
				<b style="position:relative;top:6px;"><?= $order->order_country_to ?></b>
			</td>
			<td>
				<?= $order->order_products_cost + $order->order_delivery_cost ?> <?= $order->currency ?>
			</td>
			<td>
				<?= round($order->order_weight/1000, 3) ?>кг<br />
			</td>
			<td>
				<? if (empty($order->request_count)) : ?>
				нет предложений
				<? else : ?>
				<a href="<?=$order_link?><?=$order->order_id?>"><?=$order->request_count?></a>
				<? endif; ?>
				
				<? if (empty($this->user)) : ?>
				<br />
				<a href="<?=$order_link?><?=$order->order_id?>">Добавить предложение</a>
				<? elseif ($this->user->user_group == 'manager') : ?>
				<br />
				<? if (empty($order->request_sent)) : ?>
				<a href="<?=$order_link?><?=$order->order_id?>">Добавить предложение</a>
				<? else : ?>
				Вы уже добавили<br>предложение
				<? endif; ?>
				<? endif; ?>
			</td>
		</tr>
		<? endforeach; ?>
		<? else : ?>
		<tr>
			<td colspan='8'>
				Заказы не найдены.
			</td>
		</tr>
		<? endif; ?>
	</table>
</div>
<? if (isset($pager)) echo $pager ?>
<script>
	$(function() {
		$('b#orders_count').html('<?= $orders_count ?>');
	});
</script>