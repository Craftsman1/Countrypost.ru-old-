<form id="orderForm" action="<?= $selfurl ?>updateOrder/<?= $order->order_id ?>" method="POST">
	<? if ($order->order_manager != $this->user->user_id OR empty($order->bid)) : ?>
	<div class='clientOrderInfo' style="display:none;"></div>
	<? else :
		$is_editable = 	($order->order_manager == $this->user->user_id) &&
			in_array($order->order_status, $editable_statuses);
	?>
	<div class="pricelist pricelist_main table clientOrderInfo">
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<div>
			<span>
				Статус:
			</span>
			<span style="display: inline-block;">
				<select id="order_status" name="order_status" class="order_status">
					<? foreach ($statuses[$order->order_type] as $status => $status_name) :
					if ($status == 'pending') continue;
					?>
					<option value="<?= $status ?>" <? if ($order->order_status == $status) :
						?>selected<? endif; ?>><?= $status_name ?></option>
					<? endforeach; ?>
				</select>
			</span>
		</div>
		<div>
			<span>
				Оплатить:
			</span>
			<span>
				<? if (in_array($order->order_status, $payable_statuses)) : ?>
				<?= $order->order_cost - $order->order_cost_payed ?>
				<?= $order->order_currency ?>
				<? endif; ?>
			</span>
		</div>
		<div>
			<span>
				Клиент:
			</span>
			<span>
				<a href="/main/profile/<?=$order->order_client?>"><?= $client->statistics->fullname ?> (<?= $client->statistics->login ?>)</a>
			</span>
		</div>
		<div id="address_box" <? if (empty($order->order_manager)) : ?>style="display: none;"<? endif; ?>>
			<span style="vertical-align: top;">
				Адрес доставки и телефон:
			</span>
			<span style="display: inline-block;">
				<?= $order->order_address ?>
			</span>
		</div>
		<div>
			<span>
				Способ международной доставки:
			</span>
			<span>
				<?= empty($order->preferred_delivery) ?
						(empty($order->bid->delivery_name) ?
							'' :
							$order->bid->delivery_name) :
						$order->preferred_delivery ?>
			</span>
		</div>
		<div>
			<span style="vertical-align: top;">
				Tracking №:
			</span>
			<span>
				<? if ($is_editable) : ?>
				<textarea name="tracking_no"
						  id="tracking_no"
						  style="width:188px;resize: vertical;"><?=
					$order->tracking_no ?></textarea>
				<? else : ?>
				<?= $order->tracking_no ?>
				<? endif; ?>
			</span>
		</div>
		<div>
			<span>
				Добавлен:
			</span>
			<span>
				<?= date('d.m.Y H:i', strtotime($order->order_date)) ?>
			</span>
		</div>
		<? if (isset($order->payed_date)) : ?>
		<div>
			<span>
				Оплачен:
			</span>
			<span>
				<?= date('d.m.Y H:i', strtotime($order->payed_date)) ?>
			</span>
		</div>
		<? endif ?>
		<? if (isset($order->sent_date)) : ?>
		<div>
			<span>
				Отправлен:
			</span>
			<span>
				<?= date('d.m.Y H:i', strtotime($order->sent_date)) ?>
			</span>
		</div>
		<? endif ?>
	</div>
	<? if ($is_editable) : ?>
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
	<? endif ?>
</form>
<script>
	var is_closing_order = false;
	var noty_message = 'сохранен';

	$(function() {
		//$("#order_status").msDropDown({mainCSS:'idd_order'});

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