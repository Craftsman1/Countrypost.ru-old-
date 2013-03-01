<form id="ordersForm" class='admin-inside' action='#'>
	<? View::show($viewpath.'elements/orders/tabs', array('selected_submenu' => 'payed_orders')); ?>
	<div class='table centered_th centered_td'>
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<table>
			<tr>
				<th>Номер заказа</th>
				<th>Заказать из</th>
				<th>Доставить в</th>
				<th>Общая стоимость</th>
				<th>Статус</th>
				<th></th>
			</tr>
			<? if ($orders) : foreach($orders as $order) : ?>
			<tr>
				<td>
					<a href="<?= "{$selfurl}order/{$order->order_id}" ?>"><b><?=$order->order_id?></b></a>
					<br />
					<?= $order_types[$order->order_type] ?>
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
					<? if (empty($order->order_country_to_en)) : ?>
					<b>не требуется</b>
					<? else : ?>
					<img src="/static/images/flags/<?= $order->order_country_to_en ?>.png" style="float:left;margin-right:10px;" />
					<b style="position:relative;top:6px;">
						<?= $order->order_country_to ?>
					</b>
					<? endif; ?>
				</td>
				<td>
					<? View::show('client/elements/orders/price_description', array(
						'order' => $order
				)); ?>
				</td>
				<td>
					<?= $statuses[$order->order_type][$order->order_status] ?>
					<? $order->order_currency = $order->currency; ?>
					<? View::show("/client/elements/orders/payButtonAjax", array('order' => $order)); ?>
				</td>
				<td>
					<a href="<?= "{$selfurl}order/{$order->order_id}" ?>"><?= $order->comment_for_client ? "1234 комментариев" : "Посмотреть" ?></a>
				</td>
			</tr>
			<? endforeach; else : ?>
			<tr>
				<td colspan="6">
					Заказы не найдены.
				</td>
			</tr>
			<? endif;?>
		</table>
	</div>
	<?= $pager ?>
</form>