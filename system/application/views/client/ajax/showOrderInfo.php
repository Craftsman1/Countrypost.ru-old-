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
		<div>
			<span>
				Статус:
			</span>
			<span>
				<b>
				<? foreach ($statuses[$order->order_type] as $status => $status_name)
				{
					if ($order->order_status == $status)
					{
						echo $status_name;
						break;
					}
				} ?>
				</b>
			</span>
		</div>
		<div>
			<span>
				Оплатить:
			</span>
			<span>
				<? if (in_array($order->order_status, $payable_statuses) AND
					$order->order_cost > $order->order_cost_payed) : ?>
				<div class="admin-inside" style="height:50px;" id='save_order_button'>
					<div class="submit">
						<div>
							<input type="button" onclick="window.location = '/client/saveorder';"
								   value="Оплатить <?= $order->order_cost - $order->order_cost_payed ?> <?=
									   $order->order_currency ?>">
						</div>
					</div>
				</div>
				<? endif; ?>
			</span>
		</div>
		<div id="address_box" <? if (empty($order->order_manager)) : ?>style="display: none;"<? endif; ?>>
			<span style="vertical-align: top;">
				Адрес доставки и телефон:
			</span>
			<span style="display: inline-block;">
				<? if ($is_editable) : ?>
					<? if (empty($addresses)) : ?>
					<textarea name="address_text"
							  id="address_text"
							  style="width:188px;resize:vertical;
					"><?= $order->order_address ?></textarea>
					<? else : ?>
					<select id="address" name="address" style="width: 610px!important;clear: both;">
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
					<a class="floatright" href="/profile">редактировать адреса</a>
					<? endif; ?>
				<? else : ?>
				<?= $order->order_address ?>
				<? endif; ?>
			</span>
		</div>
		<div>
			<span style="vertical-align: top;">
				Способ международной доставки:
			</span>
			<span>
				<? if ($is_editable) : ?>
				<textarea name="delivery"
						  id="delivery"
						  style="width:188px;resize: vertical;"><?=	empty($order->preferred_delivery) ?
						(empty($order->bid->delivery_name) ?
							'' :
							$order->bid->delivery_name) :
						$order->preferred_delivery ?></textarea>
				<? else : ?>
                <?= empty($order->bid) ? '' : $order->bid->delivery_name ?>
				<? endif; ?>
			</span>
		</div>
		<? if ( ! empty($order->tracking_no)) : ?>
		<div>
			<span>
				Tracking №:
			</span>
			<span>
				<?= $order->tracking_no ?>
			</span>
		</div>
		<? endif ?>
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
		<div class="admin-inside float-left">
			<div class="submit">
				<div>
					<input type="submit" value="Сохранить">
				</div>
			</div>
		</div>
		<img class="float" id="orderProgress" style="display:none;margin:0px;margin-top:5px;"
			 src="/static/images/lightbox-ico-loading.gif"/>
	</div>
	<? endif ?>
</form>
<script>
	$(function() {
		$("#address").msDropDown({mainCSS:'idd_order'});

		$('#orderForm').ajaxForm({
			target: "<?= $selfurl ?>updateOrder/<?= $order->order_id ?>",
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$("#orderProgress").show();
			},
			success: function(response)
			{
				$("#orderProgress").hide();
				success('top', 'Заказ №<?= $order->order_id ?> успешно сохранен!');
			},
			error: function(response)
			{
				$("#orderProgress").hide();
				error('top', 'Заказ №<?= $order->order_id ?> нео сохранен. Попробуйте еще раз.');
			}
		});
	});
</script>
<? endif; ?>