<? $is_offer_accepted = (empty($this->user->user_group) OR $this->user->user_group == 'manager'); ?>
<div class='table centered_td centered_th'>
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<table class="products">
		<colgroup>
			<col style="width: 60px;">
			<col>
			<col>
			<col style="width: 85px;">
			<col style="width: 85px;">
			<col style="width: 85px;">
		</colgroup>
		<tr>
			<th nowrap style="width:1px;">№	</th>
			<th>Товар</th>
			<th>Скриншот</th>
			<? if ($order->order_type == 'mail_forwarding') : ?>
			<th>Tracking №</th>
			<? else : ?>
			<th>
				Стоимость
			</th>
			<th>
				Местная<br>доставка
			</th>
			<th>
				Вес<br>товара
			</th>
			<? endif; ?>
		</tr>
		<?
		$odetail_joint_id = 0;
		$odetail_joint_count = 0;

		if ( ! empty($odetails)) : foreach($odetails as $odetail) :
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
		<tr id='product<?= $odetail->odetail_id ?>'>
			<td>
				<?= $odetail->odetail_id ?>
			</td>
			<td style="text-align: left; vertical-align: bottom;">
				<span class="plaintext">
					<a target="_blank" href="<?= $odetail->odetail_link ?>"><?= $odetail->odetail_product_name ?></a>
					<? if ($odetail->odetail_foto_requested) : ?>(требуется фото товара)<? endif; ?>
					<br>
					<b>Количество</b>: <?= $odetail->odetail_product_amount ?>
					<b>Размер</b>: <?= $odetail->odetail_product_size ?>
					<b>Цвет</b>: <?= $odetail->odetail_product_color ?>
					<br>
					<b>Комментарий</b>: <?= $odetail->odetail_comment ?>
				</span>
			</td>
			<? if ($order->order_type == 'mail_forwarding') : ?>
			<td>
				<?= $odetail->odetail_tracking_no ?>
			</td>
			<? else : ?>
			<td>
				<span class="plaintext">
					<? if (isset($odetail->odetail_img)) : ?>
					<a target="_blank" href="<?= $odetail->odetail_img ?>"><?=
						(strlen($odetail->odetail_img) > 17 ?
							substr($odetail->odetail_img, 0, 17) . '...' :
							$odetail->odetail_img) ?></a>
					<? else : ?>
					<a href="javascript:void(0)" onclick="setRel(<?= $odetail->odetail_id ?>);">
						<img src='/manager/showScreen/<?= $odetail->odetail_id ?>' width="55px" height="55px">
						<a rel="lightbox_<?= $odetail->odetail_id ?>" href="/manager/showScreen/<?=
							$odetail->odetail_id ?>" style="display:none;">Посмотреть</a>
					</a>
					<? endif; ?>
				</span>
			</td>
			<td>
				<?= $odetail->odetail_price ?> <?= $order->order_currency ?>
			</td>
			<? if ( ! $odetail->odetail_joint_id) : ?>
			<td>
				<?= $odetail->odetail_pricedelivery ?> <?= $order->order_currency ?>
			</td>
			<? elseif ($odetail_joint_id != $odetail->odetail_joint_id) :
			$odetail_joint_id = $odetail->odetail_joint_id; ?>
			<td rowspan="<?= $joints[$odetail->odetail_joint_id]->count ?>">
				<?= $joints[$odetail->odetail_joint_id]->cost ?> <?= $order->order_currency ?>
			</td>
			<? endif; ?>
			<td>
				<?= $odetail->odetail_weight ?> г
			</td>
			<? endif; ?>
		</tr>
		<? endforeach; endif; ?>
		<tr>
			<td colspan="3">&nbsp;</td>
			<td class="price_total product_total">
				<?= $order->order_products_cost ?> <?= $order->order_currency ?>
			</td>
			<td class="delivery_total product_total">
				<?= $order->order_delivery_cost ?> <?= $order->order_currency ?>
			</td>
			<td class="weight_total">
				<?= $order->order_weight ?> г
			</td>
		</tr>
		<? if ( ! empty($order->preferred_delivery)) : ?>
		<tr class='last-row' style="display: none;">
			<td style="text-align: right;" colspan='6'>
				<b>
					Способ доставки: <b class="order_totals"><?= $order->preferred_delivery ?></b>
				</b>
			</td>
		</tr>
			<? endif; ?>
	</table>
</div>
<? if ($is_offer_accepted) : ?>
<div style="height: 50px;" id="newBidButton">
	<div class="admin-inside float-left">
		<div class='submit'>
			<div>
				<input type='button' class="bid_button" value='Добавить предложение' onclick="showNewBidForm('<?= $order->order_id ?>');" />
			</div>
		</div>
	</div>
</div>
<? endif; ?>
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