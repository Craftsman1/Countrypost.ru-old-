	<form class='admin-inside'>
		<div class='table'>
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<table>
				<tr>
					<th>Номер заказа</th>
					<th>Страна</th>
					<th>Номер клиента</th>
					<th>Общая стоимость с местной доставкой</th>
					<th>Статус</th>
					<th>Посмотреть / удалить</th>
				</tr>
				<?if ($orders) : foreach($orders as $order) : ?>
				<tr>
					<td nowrap>
						<b>№ <?=$order->order_id?></b><br /><?=$order->order_date?><br /><?=$order->order_weight?>кг<br />
						Прошло:<br /><?=$order->package_day == 0 ? "" : $order->package_day.' '.humanForm((int)$order->package_day, "день", "дня", "дней")?> <?=$order->package_hour == 0 ? "" : $order->package_hour.' '.humanForm((int)$order->package_hour, "час", "часа", "часов")?>
					</td>
					<td><?=$order->order_manager_country?></td>
					<td><b>№ <?=$order->order_client?></b></td>
					<td>
						<?=$order->order_cost?>$
						<a href="javascript:void(0)" onclick="$('#pre_<?=$order->order_id?>').toggle()">Подробнее</a>
						<pre class="pre-href" id="pre_<?=$order->order_id?>">
							$<?= $order->order_products_cost ?>
							<? if ($order->order_delivery_cost) : ?>
							+
							* $<?= $order->order_delivery_cost ?>
							<? endif;
							 if ($order->order_comission) : ?>
							+
							** $<?= $order->order_comission ?>
							<? endif; ?>
						</pre>
					</td>
					<td>
					<? switch ($order->order_status) {
						case 'proccessing': ?>Обрабатывается<? break;
						case 'not_delivered': ?><b>Не доставлен</b><? break;
						case 'not_available': ?>Нет в наличии<? break;
						case 'not_available_color': ?>Нет данного цвета<? break;
						case 'not_available_size': ?>Нет данного размера<? break;
						case 'not_available_count': ?>Нет указанного кол-ва<? break;
						case 'not_payed': ?>Не оплачен<? break; } ?>
					</td>
					<td align="center">
						<a href="<?=$selfurl?>showOrderDetails/<?=$order->order_id?>">Посмотреть</a><br/>
						<hr />
						<a href="javascript:deleteItem('<?=$order->order_id?>');"><img title="Удалить" border="0" src="/static/images/delete.png"></a>
						<br/>
					</td>
				</tr>
				<?endforeach;?>
				<? else : ?>
				<tr>
					<td colspan='8'>
						Новых заказов в Вашей стране нет.
					</td>
				</tr>
				<? endif; ?>
			</table>
		</div>
	</form>