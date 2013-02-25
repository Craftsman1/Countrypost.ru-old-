<form id="ordersForm" class='admin-inside' action="<?=$selfurl?>closeOrders" method="POST">
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
	<? View::show($viewpath.'elements/orders/tabs', array('selected_submenu' => 'sent_orders')); ?>
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
				<th>Полная стоимость</th>
				<th>Статус</th>
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
					<a target="_blank" href="<?= BASEURL . $order->client_login ?>"><?= $order->client_login ?></a>
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
					<? View::show('manager/elements/orders/price_description', array(
						'order' => $order
					)); ?>
				</td>
				<td>
					<select name="order_status<?= $order->order_id ?>" class="order_status">
						<? foreach ($statuses[$order->order_type] as $status => $status_name) :
							if ($status == 'pending') continue;
						?>
						<option value="<?= $status ?>" <? if ($order->order_status == $status) :
							?>selected="selected"<? endif; ?>><?= $status_name ?></option>
						<? endforeach; ?>
					</select>
					<img class="float status_progress" style="display:none;margin-left: 5px;;"
						 src="/static/images/lightbox-ico-loading.gif"/>
					<? if ($order->order_cost < $order->order_cost_payed) : ?>
					<br />
					<?= $order->order_cost_payed - $order->order_cost ?> <?= $order->currency ?>
					<? endif; ?>
					<? if ($order->order_cost > $order->order_cost_payed) : ?><br /><?= $order->order_cost_payed - $order->order_system_comission_payed ?> <?= $order->currency ?>
					<br />Доплатить <?= $order->order_manager_cost - $order->order_cost_payed + $order->order_system_comission_payed ?> <?= $order->currency ?>
					<? endif; ?>
					<br />
				</td>
				<td>
					<a href="<?= $selfurl ?>order/<?= $order->order_id ?>"><?= $order->comment_for_manager ?
						"добавлен новый комментарий" : "Посмотреть" ?></a>
				</td>
			</tr>
			<? endforeach; else : ?>
			<tr>
				<td colspan="6">
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
<script type="text/javascript">
	$(function() {
		status_handler('Sent');
	});
</script>