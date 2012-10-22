	<form class='admin-inside'>
		<div class='table'>
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<table>
				<tr>
					<th>Номер заказа</th>
					<th>Название магазина</th>
					<th>Номер клиента</th>
					<th>Дата формирования заказа</th>
					<th>Общая стоимость с местной доставкой</th>
					<th>Статус</th>
					<th>Выполнить</th>
				</tr>
				<?if ($orders) : foreach($orders as $order) : ?>
				<tr>
					<td><b>№ <?=$order->order_id?></b></td>
					<td><? echo($order->order_shop_name); 
						if ($order->order_status == 'proccessing') : ?><br />NEW<? endif; ?></td>
					<td><b>№ <?=$order->order_client?></b></td>
					<td><?=$order->order_date?></td>
					<td>$<?=$order->order_manager_cost?>
						<a href="javascript:void(0)" onclick="$('#pre_<?=$order->order_id?>').toggle()">Подробнее</a>
						<pre class="pre-href" id="pre_<?=$order->order_id?>">
							$<?= $order->order_products_cost ?>
							<? if ($order->order_products_cost) : ?>
							+
							* $<?= $order->order_delivery_cost ?>
							<? endif; if ($order->order_manager_comission) : ?>
							+
							** $<?= $order->order_manager_comission ?>
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
					<td>
						<? if (empty($order->order_manager) AND 
							$acceptOrderAllowed === TRUE) : ?>
						<a href='<?=$selfurl?>acceptOrder/<?= $order->order_id ?>'>
							<div>	
								<div class='submit' style="margin-right:0;">
									<div>
										<input type='button' value='Выполнить' />
									</div>
								</div>
							</div>
						</a>
						<? elseif ( ! empty($order->order_manager)) : ?>
						<a href="<?=$selfurl?>showOrderDetails/<?= $order->order_id ?>">Посмотреть</a>
						<? else : ?>
						Просмотр недоступен
						<? endif; ?>
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