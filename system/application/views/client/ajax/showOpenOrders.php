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
			<col width='auto' />
			<tr class='last-row'>
				<td colspan='9'>
					<div style="margin:0 0 10px 0;height:22px;">
						<div class='floatleft'>
							<? View::show($viewpath.'elements/orders/status_links', array('selected_submenu' => 'new_orders')); ?>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<th>№ заказа</th>
				<th>Страна / Дата</th>
				<th>Общая стоимость с местной доставкой</th>
				<th>Статус</th>
				<th>Посмотреть / Удалить</th>
			</tr>
			<? if ($orders) : foreach($orders as $order) : ?>
			<script>
				var order<?=$order->order_id?> = {"order_id":"<?=$order->order_id?>","order_shop_name":"<?=$order->order_shop_name?>","order_country":"<?= $order->order_manager_country ?>","order_date":"<?= $order->order_date ?>","order_products_cost":"<?= $order->order_products_cost ?>","order_status":"<?= $order->order_status ?>"};
			</script>
			<tr>
				<td>
					<b>№ <?=$order->order_id?></b>
				</td>
				<td>
					<?= $order->order_manager_country ?>
					<br />
					<?= $order->order_date ?><!-- <?=Func::round2half($order->order_weight)?>кг <?=Func::round2half($order->order_weight) != $order->order_weight ? '('.$order->order_weight.'кг)' : '';?>--></td>
				<td>
					$<?= $order->order_cost ?>
					<a href="javascript:void(0)" onclick="$('#pre_<?=$order->order_id?>').toggle()">Подробнее</a>
					<pre class="pre-href" id="pre_<?=$order->order_id?>">
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
				<td>
					<?	  if ($order->order_status == 'proccessing') : ?>Обрабатывается
					<?elseif ($order->order_status == 'not_available') : ?>Нет в наличии<br /><i>Удалите из заказа товары, которых нет в наличии</i>
					<?elseif ($order->order_status == 'not_available_color'):?>Нет данного цвета
					<?elseif ($order->order_status == 'not_available_size'):?>Нет данного размера
					<?elseif ($order->order_status == 'not_available_count'):?>Нет указанного кол-ва
					<?elseif ($order->order_status == 'not_payed') : ?>Не оплачен
					<?elseif ($order->order_status == 'not_delivered') : ?><b>Не доставлен</b>
					<?elseif ($order->order_status == 'payed') : ?>Оплачен<? endif; ?>
					<? if ($order->order_status == 'not_payed') : ?>
					<div class='float'>	
						<div class='submit' style="margin-right:0;">
							<div>
								<input type='button' onclick="payItem('<?=$order->order_id?>');" value='Оплатить' />
							</div>
						</div>
					</div>
					<? endif; ?>
					<? if ($order->order_status == 'payed' && $order->order_cost > $order->order_cost_payed) : ?><br />$<?=$order->order_cost_payed?>
					<div class='float'>	
						<div class='submit' style="margin-right:0;">
							<div>
								<input type='button' onclick="repayItem('<?=$order->order_id?>');" value='Доплатить $<?=$order->order_cost - $order->order_cost_payed?>' />
							</div>
						</div>
					</div>
					<? endif; ?>
				</td>
				<td align="center">
					<a href="<?=$selfurl?>showOrderDetails/<?=$order->order_id?>">Посмотреть</a>
					<br />
					<? if ($order->comment_for_client) : ?>
					Добавлен новый комментарий<br />
					<? endif; ?><a href="<?=$selfurl?>showOrderDetails/<?=$order->order_id?>#comments">Комментарии</a>

					<? if ($order->order_status != 'payed') : ?>
					<hr>
					<a href="javascript:deleteItem('<?=$order->order_id?>');"><img border="0" src="/static/images/delete.png" title="Удалить"></a>
					<? endif; ?>
				</td>
			</tr>
			<?endforeach; endif;?>
			<tr class='last-row'>
				<td colspan='8'>
					<div id="tableComments" style="text-align:left;float:left;">
						<br />
						* стоимость местной доставки<br />
						** комиссия за помощь в покупке<br />
					</div>
					<div class='float'>	
						<div class='submit'><div></div></div>
					</div>
				</td>
			</tr>
		</table>
	</div>
</form>
<?= $pager ?>
<script>
	$(function() {
		$('#page_title').html('Новые заказы');
	});
</script>