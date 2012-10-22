<? if (empty($order->order_manager)) : ?>
<div class='clientOrderInfo' style="display:none;"></div>
<? else : ?>
<div class='table clientOrderInfo'>
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<table>
		<tr>
			<th>Посредник</th>
			<th>Расходы по заказу</th>
			<th>Статус</th>
		</tr>
		<tr>
			<td>
				<? if ( ! empty($order->order_manager)) : ?>
				<a href='/'><?= $order->statistics->fullname ?></a>
				<br>
				<br>
				<a href='javascript:void();' onclick="unchooseBid();">Выбрать другого</a>
				<? endif; ?>
			</td>
			<td align='right'>
				Итого за заказ: <span class="order_total_cost"><?=$order->bid->total_cost?></span> <?=$order->order_currency?>
				<div class="order_totals">
					<div class="expand_order_info" style="display:block!important;">
						<a href="javascript:expandOrderTotals('<?= $order->bid->bid_id ?>');">подробнее</a>
					</div>
					<div>
						<b>Расходы по заказу:</b>
					</div>
					<div><?=$order->order_products_cost?> <?=$order->order_currency?> <img class="tooltip" src="/static/images/mini_help.gif" title="Общая стоимость товаров в заказе"></div>
					<div><?=$order->bid->manager_tax?> <?=$order->order_currency?> <img class="tooltip" src="/static/images/mini_help.gif" title="Комиссия посредника"></div>
					<? if ($order->bid->foto_tax) : ?>
					<div><?=$order->bid->order_tax?> <?=$order->order_currency?> <img class="tooltip" src="/static/images/mini_help.gif" title="Фото товаров"></div>
					<? endif; ?>
					<div><?=$order->bid->delivery_cost?> <?=$order->order_currency?> <img class="tooltip" src="/static/images/mini_help.gif" title="Стоимость доставки<?=empty($order->bid->delivery_cost) ? '' : ", {$order->bid->delivery_name}" ?>"></div>
					<div>
						<b>Итого: <?=$order->bid->total_cost?> <?=$order->order_currency?></b>
					</div>
					<div class="collapse_order_info">
						<a href="javascript:collapseOrderTotals('<?=$order->bid->bid_id?>');">свернуть</a>
					</div>
				</div>
				<hr />
				Доставка <b class="weight_total"><?= $order->order_product_weight ?>г</b> в <span class='countryTo' style="float:none; display:inline; margin:0;"><?=$order->order_country_to?></span> <span class='cityTo' style="float:none; display:inline; margin:0;">(город: <?=$order->order_city_to?>)</span> 
						<? if ( ! empty($order->bid->delivery_name)) : ?>
						-
						<?=$order->bid->delivery_name?></b>
						<? endif; ?>:
						<b><?= $order->bid->delivery_cost ?> <?=$order->order_currency?></b>
			</td>
			<td>
				<span class='order_status'><?=$order->order_status_desc?></span>
				<? if ($order->order_status == 'payed' || $order->order_status == 'sended') : ?>
				:<br />
				$<?=$order->order_cost_payed?>
				<? endif; ?>						
			</td>
		</tr>
	</table>
</div>
<? endif; ?>
<script>
	function expandOrderTotals()
	{
		$('.order_totals div').show('slow');
		$('.order_totals .expand_order_info').hide('slow');
	}

	function collapseOrderTotals()
	{
		$('.order_totals div').hide('slow');
		$('.order_totals div.expand_order_info').show();
	}
</script>