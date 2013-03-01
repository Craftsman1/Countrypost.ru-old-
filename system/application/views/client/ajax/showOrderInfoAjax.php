<form id="orderForm" action="<?= $selfurl ?>updateOrder/<?= $order->order_id ?>" method="POST">
	<? if ($order->order_client != $this->user->user_id) : ?>
	<div class='clientOrderInfo' style="display:none;"></div>
	<? else :
		$is_editable = ($order->order_client == $this->user->user_id) &&
			in_array($order->order_status, $editable_statuses);
	?>
	<div class="pricelist pricelist_main table clientOrderInfo">
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<table>
			<tr>
				<th nowrap>
					Статус:
				</th>
				<th nowrap>
					<b>
						<?= $statuses[$order->order_type][$order->order_status] ?>
					</b>
					<img class="float"
						 id="orderProgress"
						 style="display:none; margin:0 0 0 5px; vertical-align: middle;"
						 src="/static/images/lightbox-ico-loading.gif">
				</th>
			</tr>
			<tr>
				<td>
					<? if ($order->order_status == 'pending') : ?>
					Оплатить:
					<? else : ?>
					Оплачено:
					<? endif; ?>
				</td>
				<td>
					<? View::show("/client/elements/orders/payButton"); ?>
				</td>
			</tr>
			<tr id="address_box">
				<td>
					Адрес доставки и телефон:
				</td>
				<td>
					<? if ($is_editable) : ?>
					<? if (empty($addresses)) : ?>
						<textarea name="address_text"
								  id="address_text"
								  style="width:188px;resize:vertical;"
								  onchange="updateOrder();"><?= $order->order_address ?></textarea>
						<? else : ?>
						<select id="address"
								name="address"
								style="width: 610px!important;clear: both;"
								onchange="updateOrder();">
							<option value="0" >выберите адрес...</option>
							<? foreach ($addresses as $address) :
							if ($address->is_generated)
							{
								$full_address = implode(', ', array(
									$address->address_address,
									$address->country_name,
									$address->address_recipient
								));
							}
							else
							{
								$full_address = implode(', ', array(
									$address->address_zip,
									$address->address_address,
									$address->address_town,
									$address->country_name,
									'тел.' . $address->address_phone,
									$address->address_recipient
								));
							}
							?>
							<option
									value="<?= $address->address_id ?>"
									title="/static/images/flags/<?= $address->country_name_en ?>.png"
								<? if ($address->address_id == $order->address_id) : ?>
									selected="true"
								<? endif ?>
									><?= $full_address ?></option>
							<? endforeach; ?>
						</select>
						<br>
						<a class="floatleft" href="/profile">редактировать адреса</a>
						<? endif; ?>
					<? else : ?>
					<?= $order->order_address ?>
					<? endif; ?>
				</td>
			</tr>
			<tr>
				<td>
					Способ международной доставки:
				</td>
				<td>
					<? $delivery_name = empty($order->preferred_delivery) ?
					(empty($order->bid->delivery_name) ?
						'' :
						$order->bid->delivery_name) :
					$order->preferred_delivery;

					if ($is_editable) : ?>
					<textarea name="delivery"
							  id="delivery"
							  style="width:188px;resize: vertical;"
							  onchange="updateOrder();"
							><?= $delivery_name ?></textarea>
					<? else : ?>
					<?= $delivery_name ?>
					<? endif; ?>
				</td>
			</tr>
			<? if ( ! empty($order->tracking_no)) : ?>
			<tr>
				<td>
					Tracking №:
				</td>
				<td>
					<?= $order->tracking_no ?>
				</td>
			</tr>
			<? endif ?>
			<tr>
				<td>
					Добавлен:
				</td>
				<td>
					<?= date('d.m.Y H:i', strtotime($order->order_date)) ?>
				</td>
			</tr>
			<? if (isset($order->payed_date)) : ?>
			<tr>
				<td>
					Оплачен:
				</td>
				<td>
					<?= date('d.m.Y H:i', strtotime($order->payed_date)) ?>
				</td>
			</tr>
			<? endif ?>
			<? if (isset($order->sent_date)) : ?>
			<tr>
				<td>
					Отправлен:
				</td>
				<td>
					<?= date('d.m.Y H:i', strtotime($order->sent_date)) ?>
				</td>
			</tr>
			<? endif ?>
		</table>
	</div>
	<a name="pagerScroll"></a>
	<? if (FALSE AND $is_editable) : ?>
	<div style="height:50px;">
		<div class="admin-inside float-left">
			<div class="submit">
				<div>
					<input type="submit" value="Сохранить">
				</div>
			</div>
		</div>
	</div>
	<? endif ?>
	<? endif; ?>
</form>
<? if ( ! empty($open_orders2in) OR ! empty($payed_orders2in)) : ?>
<h3>Заявки на оплату</h3>
<? View::show("/client/ajax/showOpenOrders2In"); ?>
<? endif; ?>
<script>
	$(function() {
		prepareOrderFormHandlers();
	});
</script>
