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
					<?= $order->order_cost ?> <?= $order->currency ?>
					<a href="javascript:void(0)" onclick="$('#pre_<?=$order->order_id?>').toggle()">Подробнее</a>
					<pre class="pre-href" id="pre_<?=$order->order_id?>">
						<?= $order->order_products_cost ?> <?= $order->currency ?>
						<? if ($order->order_products_cost) : ?>
						+
						* <?= $order->order_delivery_cost ?> <?= $order->currency ?>
						<? endif; if ($order->order_comission) : ?>
						+
						** <?= $order->order_comission ?> <?= $order->currency ?>
						<? endif; ?>
					</pre>
				</td>
				<td>
					<? foreach ($statuses[$order->order_type] as $status => $status_name)
					{
						if ($order->order_status == $status)
						{
							echo $status_name;
							break;
						}
					} ?>
					<? if ($order->order_status == 'payed' && $order->order_cost > $order->order_cost_payed) : ?>
					<br>
					<?= $order->order_cost_payed ?> <?= $order->currency ?>
					<div class='float'>	
						<div class='submit' style="margin-right:0;">
							<div>
								<input type='button' onclick="repayItem('<?=$order->order_id?>');" value='Доплатить <?=$order->order_cost - $order->order_cost_payed?> <?= $order->currency ?>' />
							</div>
						</div>
					</div>
					<? endif; ?>
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