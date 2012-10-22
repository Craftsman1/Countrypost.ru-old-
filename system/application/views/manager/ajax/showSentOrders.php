	<a name="pagerScroll"></a>
	<form id="pagerForm" class='admin-inside' action="<?=$selfurl?>closeOrders" method="POST">
		<ul class='tabs'>
			<li><div><a href='<?=$selfurl?>showAddPackage'>Добавить посылку</a></div></li>
			<li><div><a href='<?=$selfurl?>showNewPackages'>Новые</a></div></li>
			<li><div><a href='<?=$selfurl?>showPayedPackages'>Оплаченные</a></div></li>
			<li><div><a href='<?=$selfurl?>showSentPackages'>Отправленные</a></div></li>
			<li><div><a href='<?=$selfurl?>showOpenOrders'>Заказы “Помощь в покупке”</a></div></li>
			<li><div><a href='<?=$selfurl?>showPayedOrders'>Оплаченные заказы</a></div></li>
			<li class='active'><div><a href='<?=$selfurl?>showSentOrders'>Закрытые заказы</a></div></li>
		</ul>
		<div class='table'>
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<table>
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<tr>
					<th>Номер заказа</th>
					<th>Номер клиента</th>
					<th>Номер посылки</th>
					<th>Цена доставки</th>
					<th>Статус</th>
					<th>Просмотр деталей заказа</th>
				</tr>
				<? if ($orders) : foreach($orders as $order) : ?>
				<tr>
					<td nowrap>
						<b>№ <?=$order->order_id?></b><br /><?=$order->order_date?><br />
						Прошло:<br /><?=$order->package_day == 0 ? "" : $order->package_day.' '.humanForm((int)$order->package_day, "день", "дня", "дней")?> <?=$order->package_hour == 0 ? "" : $order->package_hour.' '.humanForm((int)$order->package_hour, "час", "часа", "часов")?>
					</td>
					<td><b>№ <?=$order->order_client?></b></td>
					<td>
						<? if (empty($order->package_id)) : ?>
						<a href="#" onclick="return addPackage('<?=$order->order_id?>', '<?=$order->order_client?>');"><b>Добавить посылку</b></a>
						<? else :?>
						<a href="<?= $selfurl.'showPackageDetails/'.$order->package_id ?>" >№ <?= $order->package_id ?></a>
						<? endif; ?>
					</td>
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
						Отправлен
						<? if ($order->order_cost < $order->order_cost_payed) : ?>: $<?=$order->order_cost_payed - $order->order_system_comission_payed?>
						<br />
						<div class='float'>	
							<div class='submit' style="margin-right:0;">
								<div>
									<input type='button' onclick="refundItem('<?=$order->order_id?>');" value='Возместить $<?=$order->order_cost_payed - $order->order_cost - $order->order_system_comission_payed + $order->order_system_comission?>' />
								</div>
							</div>
						</div>
						<? endif; ?>
						<br />
						<? if ($order->comment_for_manager) : ?>
							Добавлен новый комментарий<br />
						<? endif; ?>
						<a href="<?=$selfurl?>showOrderDetails/<?=$order->order_id?>#comments">Комментарии</a>
					</td>
					<td><a href="<?=$selfurl?>showOrderDetails/<?=$order->order_id?>">Посмотреть</a></td>
				</tr>
				<?endforeach; endif;?>
				<tr class='last-row'>
					<td colspan='9'>
						<div id="tableComments" style="text-align:left;float:left;">
							<br />
							* стоимость местной доставки<br />
							** комиссия за помощь в покупке<br />
						</div>
					</td>
				</tr>
			</table>
		</div>
	</form>
	<?php if (isset($pager)) echo $pager ?>
<script type="text/javascript">
	function refundItem(id) {
		if (confirm("Возместить клиенту заказ №" + id + "?")){
			window.location.href = '<?=$selfurl?>refundOrder/' + id;
		}
	}
</script>