<form id="pagerForm" class='admin-inside' action='#'>
	<? View::show($viewpath.'elements/orders/tabs'); ?>
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
			<tr class='last-row'>
				<td colspan='9'>
					<div style="margin:0 0 10px 0;height:22px;">
						<div class='floatleft'>
							<? View::show($viewpath.'elements/orders/status_links', array('selected_submenu' => 'sent_orders')); ?>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<th>№ заказа</th>
				<th>Кто выполняет заказ</th>
				<th>Страна / Дата / Вес</th>
				<th>Общая<br />стоимость<br />с местной<br />доставкой</th>
				<th>Статус</th>
				<th>Посмотреть</th>
			</tr>
			<? if ($orders) : foreach($orders as $order) : ?>
			<script>
				var order<?= $order->order_id ?> = {"order_id":"<?= $order->order_id ?>","order_shop_name":"<?= $order->order_shop_name ?>","order_country":"<?= $order->order_manager_country ?>","order_date":"<?= $order->order_date ?>","order_products_cost":"<?= $order->order_products_cost ?>","order_status":"<?= $order->order_status ?>"};
			</script>
			<tr>
				<td>
					<b>№ <?= $order->order_id ?></b>
				</td>
				<td>
					<b>
						Менеджер:
					</b>
					<br />
					Имя: 
					<?= $order->manager_fio ?>
					<br />
					<? if ( ! empty($order->manager_skype)) : ?>
					Skype:
					<?= $order->manager_skype ?>
					<br />
					<? endif; ?>
					E-mail:
					<a href='mailto:<?= $order->manager_email ?>'><?= $order->manager_email ?></a>
				</td>
				<td>
					<?= $order->order_manager_country ?>
					<br />
					<?= $order->order_date ?> 
					<br />
					<?= Func::round2half($order->order_weight)?>кг 
					<?= Func::round2half($order->order_weight) != $order->order_weight ? '('.$order->order_weight.'кг)' : '' ?>
				</td>
				<td>
					$<?= $order->order_cost ?>
					<a href="javascript:void(0)" onclick="$('#pre_<?= $order->order_id ?>').toggle()">Подробнее</a>
					<pre class="pre-href" id="pre_<?= $order->order_id ?>">
						$<?= $order->order_products_cost ?>
						<? if ($order->order_products_cost) : ?>
						+
						* $<?= $order->order_delivery_cost ?>
						<? endif; if ($order->order_comission) : ?>
						+
						** $<?= $order->order_comission ?>
						<? endif; ?>
					</pre>
				</td>
				<td>Отправлен</td>
				<td align="center">
					<a href="<?= $selfurl ?>showOrderDetails/<?= $order->order_id ?>">Посмотреть</a>
					<br />
					<? if ($order->comment_for_client) : ?>
					Добавлен новый комментарий
					<br />
					<? endif; ?>
					<a href="<?= $selfurl ?>showOrderDetails/<?= $order->order_id ?>#comments">Комментарии</a>
				</td>
			</tr>
			<? endforeach; endif; ?>
			<tr class='last-row'>
				<td colspan='9'>
					<div id="tableComments" style="text-align:left;float:left;">
						<br />
						* стоимость местной доставки<br />
						** комиссия за помощь в покупке<br />
					</div>
					<div class='float'>	
						<div class='submit'><div></div></div>
					</div>
				</td>
				<td>
				</td>
			</tr>
		</table>
	</div>
</form>
<?= $pager ?>
<script>
	$(function() {
		$('#page_title').html('Отправленные заказы');
	});
</script>