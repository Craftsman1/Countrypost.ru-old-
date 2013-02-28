<form id="orderForm" action="<?= $selfurl ?>updateOrder/<?= $order->order_id ?>" method="POST">
	<? if (in_array($order->order_status, $editable_statuses)) : ?>
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
				<th nowrap style="vertical-align: middle;">
					<select id="order_status"
							name="order_status"
							class="order_status float-left"
							onchange="updateOrder();">
						<? foreach ($statuses[$order->order_type] as $status => $status_name) :
						if ($status == 'pending') continue; ?>
						<option value="<?= $status ?>" <? if ($order->order_status == $status) :
							?>selected<? endif; ?>><?= $status_name ?></option>
						<? endforeach; ?>
					</select>
					<? if (empty($order->sent_date)) : ?>
					<input id="close_order"
						   class="float-left"
						   style="display:none; vertical-align: middle; margin-top: 4px;"
						   type="checkbox"
						   onchange="closeOrder();">
					<label for="close_order"
						   class="float-left"
						   style="display:none; margin-top: 4px;">Закрыть заказ?</label>
					<? endif ?>
					<img class="float-left"
						 id="orderProgress"
						 style="display:none; margin:0 0 0 5px; vertical-align: middle;"
						 src="/static/images/lightbox-ico-loading.gif">

				</th>
			</tr>
			<tr>
				<td>
					Оплатить:
				</td>
				<td>
					<? View::show("/manager/elements/orders/payButton", array('show_caption' => TRUE)); ?>
				</td>
			</tr>
			<tr>
				<td>
					Клиент:
				</td>
				<td>
					<a href="/<?= $client->statistics->login ?>"><?= $client->statistics->fullname ?></a>
						(<?= $client->statistics->login ?>)
				</td>
			</tr>
			<tr <? if (empty($order->order_manager)) : ?>style="display: none;"<? endif; ?>>
				<td>
					Адрес доставки и телефон:
				</td>
				<td>
					<?= $order->order_address ?>
				</td>
			</tr>
			<tr>
				<td>
					Способ международной доставки:
				</td>
				<td>
					<?= empty($order->preferred_delivery) ?
					(empty($order->bid->delivery_name) ?
						'' :
						$order->bid->delivery_name) :
					$order->preferred_delivery; ?>
				</td>
			</tr>
			<tr>
				<td>
					Tracking №:
				</td>
				<td>
					<textarea name="tracking_no"
							  id="tracking_no"
							  style="width:188px;resize: vertical;"
							  onchange="updateOrder();"><?=
						$order->tracking_no ?></textarea>
				</td>
			</tr>
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