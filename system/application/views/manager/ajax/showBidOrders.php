<form id="ordersForm" class='admin-inside' action="<?= $selfurl ?>closeOrders" method="POST">
	<div class="search_results">
            <span class="total" style="float: none;">
                Найдено заказов: <b id="orders_count"><?= $this->paging_count ?></b>
            </span>
	</div>
	<br>
	<br>
	<div class="search_results">
		<span class="total" style="float: none;">
			&nbsp;
		</span>
		<span class="total" style="margin:0;">
			<label>заказов на странице:</label>
			<? View::show('main/elements/per_page', array(
			'handler' => 'manager'
		)); ?>
		</span>
	</div>
	<? View::show($viewpath.'elements/orders/tabs', array('selected_submenu' => 'bid_orders')); ?>
	<div class='table centered_th centered_td'>
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<table>
			<tr>
				<th>Номер заказа</th>
				<th>Клиент</th>
				<th>Доставка в</th>
				<th>Примерная стоимость</th>
				<th></th>
			</tr>
			<? if ($orders) : foreach($orders as $order) : ?>
			<tr>
				<td>
					<a href="<?= $selfurl . 'order/' . $order->order_id ?>"><b><?= $order->order_id ?></b></a>
					<br />
					<?= $order_types[$order->order_type] ?>
					<br />
					<? if ($order->package_day == 0) : ?>
					<?= $order->package_day == 0 ? "" : $order->package_day.' '.humanForm((int)$order->package_day, "день", "дня", "дней") ?> <?= $order->package_hour == 0 ? "" : $order->package_hour.' '.humanForm((int)$order->package_hour, "час", "часа", "часов") ?> назад
					<? else : ?>
					<?= $order->order_date ?>
					<? endif; ?>
				</td>
				<td>
					<a target="_blank" href="<?= $this->config->item('base_url') . $order->client_login ?>"><?= $order->client_login ?></b>
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
					<?= $order->order_products_cost + $order->order_delivery_cost ?> <?= $order->currency ?>
				</td>
				<td>
					<a href="<?= $selfurl ?>order/<?= $order->order_id ?>">Посмотреть</a>
				</td>
			</tr>
			<? endforeach; else : ?>
			<tr>
				<td colspan="5">
					Заказы не найдены.
				</td>
			</tr>
			<? endif; ?>
		</table>
	</div>
	<div class="search_results">
            <span class="total" style="float: none;">
                Найдено заказов: <b id="orders_count"><?= $this->paging_count ?></b>
            </span>
            <span class="total" style="margin:0;">
                <label>заказов на странице:</label>
				<? View::show('main/elements/per_page', array(
					'handler' => 'manager'
				)); ?>
            </span>
	</div>
	<?= $pager ?>
</form>