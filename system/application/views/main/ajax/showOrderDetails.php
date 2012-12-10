<?
$is_offer_accepted = ! empty($order->order_manager);
$is_own_order = $is_offer_accepted AND ($order->order_manager == $this->user->user_id);
?>
<form class='admin-inside' id='detailsForm' action='<?=$selfurl?>updateProductAjax' enctype="multipart/form-data" method="POST">
	<div class='table'>
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<table id="new_products">
			<tr>
				<th nowrap style="width:1px;">
					№ <input type='checkbox' id='select_all' />
				</th>
				<th>Товар</th>
				<th>Скриншот</th>
				<th>Стоимость</th>
				<th>Местная доставка</th>
				<th>Примерный вес</th>
				<? if ($is_own_order) : ?>
				<th style="width:1px;"></th>
				<? endif; ?>
			</tr>
			<? $order_products_cost = 0; 
				$order_delivery_cost = 0;
				$order_product_weight = 0;
				$odetail_joint_id = 0;
				$odetail_joint_count = 0;
				
			if ( ! empty($odetails)) : foreach($odetails as $odetail) : 
				$order_products_cost += $odetail->odetail_price;
				$order_product_weight += $odetail->odetail_weight;
				
				if (stripos($odetail->odetail_link, 'http://') !== 0)
				{
					$odetail->odetail_link = 'http://'.$odetail->odetail_link;
				}
				
				if (isset($odetail->odetail_img) && 
					stripos($odetail->odetail_img, 'http://') !== 0)
				{
					$odetail->odetail_img = 'http://'.$odetail->odetail_img;
				}
			?>
			<tr id='product<?=$odetail->odetail_id?>'>
				<script>
					var odetail<?=$odetail->odetail_id?> = {"odetail_id":"<?=$odetail->odetail_id?>","odetail_client":"<?=$odetail->odetail_client?>","odetail_manager":"<?=$odetail->odetail_manager?>","odetail_order":"<?=$odetail->odetail_order?>","odetail_link":"<?=$odetail->odetail_link?>","odetail_product_name":"<?=$odetail->odetail_product_name?>","odetail_product_color":"<?=$odetail->odetail_product_color?>","odetail_product_size":"<?=$odetail->odetail_product_size?>","odetail_product_amount":"<?=$odetail->odetail_product_amount?>","odetail_img":"<?=$odetail->odetail_img?>"};
				</script>
				<td id='odetail_id<?=$odetail->odetail_id?>'><?=$odetail->odetail_id?></td>
				<td id='odetail_product_name<?=$odetail->odetail_id?>'>
					<a target="_blank" href="<?=$odetail->odetail_link?>"><?=$odetail->odetail_product_name?></a> 
					<? if ($odetail->odetail_foto_requested) : ?>(требуется фото товара)<? endif; ?>
					<br>
					<b>Количество</b>: <?=$odetail->odetail_product_amount?>
					<b>Размер</b>: <?=$odetail->odetail_product_size?>
					<b>Цвет</b>: <?=$odetail->odetail_product_color?>
					<br>
					<b>Комментарий</b>: <?=$odetail->odetail_comment?>
				</td>
				<td id='odetail_img<?=$odetail->odetail_id?>'>
					<? if (isset($odetail->odetail_img)) : ?>
					<a href="#" onclick="window.open('<?=$odetail->odetail_img?>');return false;"><?=(strlen($odetail->odetail_img)>17?substr($odetail->odetail_img,0,17).'...':$odetail->odetail_img)?></a>
					<? else : ?>
					<a href="javascript:void(0)" onclick="setRel(<?=$odetail->odetail_id?>);">
						Просмотреть скриншот <a rel="lightbox_<?=$odetail->odetail_id?>" href="/client/showScreen/<?=$odetail->odetail_id?>" style="display:none;">Посмотреть</a>
					</a>
					<? endif; ?>
				</td>
				<td id="odetail_price<?=$odetail->odetail_id?>"><?=$odetail->odetail_price?> <?=$order->order_currency?></td>
				<td id="odetail_pricedelivery<?=$odetail->odetail_id?>"><?=$odetail->odetail_pricedelivery?> <?=$order->order_currency?></td>
				<? if (!$odetail->odetail_joint_id) : 
					$order_delivery_cost += $odetail->odetail_pricedelivery;
				?>
				<td id="odetail_weight<?=$odetail->odetail_id?>">
					<?=$odetail->odetail_weight?>г
				</td>
				<? elseif ($odetail_joint_id != $odetail->odetail_joint_id) :
						$odetail_joint_id = $odetail->odetail_joint_id;
						$odetail_joint_count = $odetail->odetail_joint_count;
						$order_delivery_cost += $odetail->odetail_joint_cost;
				?>
				<td rowspan="<?=$odetail_joint_count?>">
					<?=$odetail->odetail_joint_cost?>
				</td>
				<? endif; ?>
				<? if ($is_own_order) : ?>
				<td align="center" id="odetail_action<?=$odetail->odetail_id?>">
					<a href="javascript:editItem(<?=$odetail->odetail_id?>)" id="odetail_edit<?=$odetail->odetail_id?>"><img border="0" src="/static/images/comment-edit.png" title="Редактировать"></a>
					<br />
					<a href="javascript:deleteItem(<?=$odetail->odetail_id?>)"><img border="0" src="/static/images/delete.png" title="Удалить"></a>
				</td>
				<? endif; ?>
			</tr>
			<? endforeach; endif; ?>
			<tr>
				<td colspan="3">&nbsp;</td>
				<td class="price_total product_total"><?= $order_products_cost ?> <?=$order->order_currency?></td>
				<td class="delivery_total product_total"><?= $order_delivery_cost ?>  <?=$order->order_currency?></td>
				<td class="weight_total"><?= $order_product_weight ?>г</td>
				<? if ($is_own_order) : ?>
				<td align="center">&nbsp;</td>
				<? endif; ?>
			</tr>
			<tr class='last-row'>
				<td colspan='4'>
					<div class='floatleft'>	
						<div class='submit'><div><input type='button' class="bid_button" value='Добавить предложение!' onclick="showNewBidForm('<?=$order->order_id?>');" /></div></div>
					</div>
				</td>
				<td style="text-align: right;" colspan='3'>
					<br />
					<b>
						Доставка в <span class='countryTo' style="float:none; display:inline; margin:0;"><?=$order->order_country_to?></span> <span class='cityTo' style="float:none; display:inline; margin:0;">(город: <?=$order->order_city_to?>)</span>: <b class="weight_total"><?= $order_product_weight ?> г</b>
						<? if ( ! empty($order->preferred_delivery)) : ?>
						<br />
						Способ доставки: <b class="order_totals"><?=$order->preferred_delivery?></b>
						<? endif; ?>
					</b>
				</td>
			</tr>
		</table>			
	</div>
</form>
<script>
	function updateTotals()
	{
		updateTotalGeneric('oprice', 'price_total', getSelectedCurrency());
		updateTotalGeneric('odeliveryprice', 'delivery_total', getSelectedCurrency());
		updateTotalGeneric('product_total', 'order_totals', getSelectedCurrency());
		updateTotalGeneric('oweight', 'weight_total', 'г');
		
		$('span.countryTo').html(countryTo);
		cityTo = $.trim($("input#city_to").val());
		
		if (cityTo)
		{
			$('span.cityTo').html(" (город: " + cityTo + ")");
		}
		else
		{
			$('span.cityTo').html("");
		}
	}
	
	function updateTotalGeneric(column, result, measure)
	{
		var total = 0;
		
		$('.' + column).each(function() {
			total += parseFloat($(this).html());
		});
		
		$('.' + result).html(total + ' ' + measure);
	}
	
	function getSelectedCurrency()
	{
		return selectedCurrency;
	}
</script>