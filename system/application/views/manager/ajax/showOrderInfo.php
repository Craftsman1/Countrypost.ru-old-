<? if (in_array($order->order_status, $editable_statuses)) : ?>
<form id="orderForm" action="<?= $selfurl ?>updateOrder/<?= $order->order_id ?>" method="POST">
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
					<select id="order_status" name="order_status" class="order_status">
						<? foreach ($statuses[$order->order_type] as $status => $status_name) :
						if ($status == 'pending') continue;
						?>
						<option value="<?= $status ?>" <? if ($order->order_status == $status) :
							?>selected<? endif; ?>><?= $status_name ?></option>
						<? endforeach; ?>
					</select>
				</th>
			</tr>
			<tr>
				<td>
					Оплатить:
				</td>
				<td>
					<? //if (in_array($order->order_status, $payable_statuses)) : ?>
					<?= $order->order_cost ?>
					<?= $order->order_currency ?>
					<? //endif; ?>
				</td>
			</tr>
			<tr>
				<td>
					Клиент:
				</td>
				<td>
					<a href="/<?= $client->statistics->login ?>"><?= $client->statistics->fullname ?> (<?= $client->statistics->login ?>)</a>
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
					$order->preferred_delivery ?>
				</td>
			</tr>
			<tr>
				<td>
					Tracking №:
				</td>
				<td>
					<textarea name="tracking_no"
							  id="tracking_no"
							  style="width:188px;resize: vertical;"><?=
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
	<div style="height:50px;">
		<div id="save_order" class="admin-inside float-left">
			<div class="submit">
				<div>
					<input type="button" value="Сохранить">
				</div>
			</div>
		</div>
		<? if (empty($order->sent_date)) : ?>
		<div id="close_order" class="admin-inside float-left" style="display:none;">
			<div class="submit">
				<div>
					<input type="button" value="Закрыть заказ">
				</div>
			</div>
		</div>
		<? endif ?>
		<img class="float" id="orderProgress" style="display:none;margin:0px;margin-top:5px;"
			 src="/static/images/lightbox-ico-loading.gif"/>
	</div>
</form>
<script>
	var is_closing_order = false;
	var noty_message = 'сохранен';

	$(function() {
		$("#close_order").click(function() {
			is_closing_order = true;
			noty_message = 'отправлен';
			$('#orderForm').attr('action', '<?= $selfurl ?>closeOrder/<?= $order->order_id ?>');
			$('#orderForm').submit();
		});

		$("#save_order").click(function() {
			is_closing_order = false;
			noty_message = 'сохранен';
			$('#orderForm').attr('action', '<?= $selfurl ?>updateOrder/<?= $order->order_id ?>');
			$('#orderForm').submit();
		});

		$("textarea#tracking_no").change(function() {
			$('div#close_order').show();
		});

		$('#orderForm').ajaxForm({
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$("#orderProgress").show();
			},
			success: function(response)
			{
				$("#orderProgress").hide();
				success('top', 'Заказ №<?= $order->order_id ?> успешно ' + noty_message + '!');

				if (is_closing_order)
				{
					$('select.order_status').val('completed');
				}
			},
			error: function(response)
			{
				$("#orderProgress").hide();
				error('top', 'Заказ №<?= $order->order_id ?> не ' + noty_message + '. Попробуйте еще раз.');
			}
		});
	});
</script>
<? endif; ?>