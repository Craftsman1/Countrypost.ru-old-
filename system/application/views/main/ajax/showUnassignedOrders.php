<? $order_link = $this->config->item('base_url') . 'main/order/'; ?>
<div class='table centered_th centered_td' style="margin:0;" id="unassignedOrders">
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<table>
		<tr>
			<th style="width: 120px;">Номер заказа</th>
			<th style="width: 115px;">Заказать из</th>
			<th style="width: 115px;">Доставить в</th>
			<th>Примерная<br>стоимость</th>
			<th>Примерный<br>вес</th>
			<th style="width: 100px;">Предложений<br>от посредников</th>
		</tr>
		<? if ($orders) : foreach($orders as $order) : ?>
		<tr>
			<td>
				<a href="<?=$order_link?><?=$order->order_id?>"><b><?=$order->order_id?></b></a>
				<br />
				<?= $order_types[$order->order_type] ?>
				<br />
				<? if ($order->package_day == 0) : ?>
				<?= $order->package_day == 0 ? "" : $order->package_day.' '.humanForm((int)$order->package_day,
					"день", "дня", "дней")?> <?=$order->package_hour == 0 ? "" : $order->package_hour.' '.humanForm((int)$order->package_hour, "час", "часа", "часов")?> назад
				<? else : ?>
				<?= $order->order_date ?>
				<? endif; ?>
			</td>
			<td style="text-align:left;">
				<img src="/static/images/flags/<?= $order->order_country_from_en ?>.png" style="float:left;margin-right:10px;" />
				<?= shortenCountryName($order->order_country_from, 'position:relative;top:6px;') ?>
			</td>
			<td style="text-align:left;">
				<? if (empty($order->order_country_to_en)) : ?>
				<b>не требуется</b>
				<? else : ?>
				<img src="/static/images/flags/<?= $order->order_country_to_en ?>.png" style="float:left;margin-right:10px;" />
				<?= shortenCountryName($order->order_country_to, 'position:relative;top:6px;') ?>
				<? endif; ?>
			</td>
			<td>
				<?= $order->order_products_cost + $order->order_delivery_cost ?> <?= $order->currency ?>
			</td>
			<td>
				<?= $order->order_weight ?>кг<br />
			</td>
			<td>
				<? // 1. счетчик
				if (empty($order->request_count)) : ?>
				нет предложений
				<? else : ?>
				<a href="<?= $order_link ?><?= $order->order_id ?>"><?= $order->request_count ?></a>
				<? endif; ?>
				<? // 2. ссылка
				if (empty($this->user) OR
					($this->user->user_group == 'manager' AND
					empty($order->request_sent))) : ?>
				<br />
				<a href="<?=$order_link?><?=$order->order_id?>">Добавить предложение</a>
				<? endif; ?>
				<? // 3. поздравления
				if ( ! empty($order->request_sent)) : ?>
				<br />
				Вы уже добавили<br>предложение
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