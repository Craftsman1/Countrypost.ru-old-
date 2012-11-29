<form id="orderForm" action="<?= $selfurl ?>updateOrder/<?= $order->order_id ?>" method="POST">
	<? if ($order->order_manager != $this->user->user_id OR empty($order->bid)) : ?>
	<div class='clientOrderInfo' style="display:none;"></div>
	<? else :
		$is_editable = 	($order->order_client == $this->user->user_id) &&
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
			<span>
				Tracking №:
			</span>
			<span>
				<?= $order->tracking_no ?>
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