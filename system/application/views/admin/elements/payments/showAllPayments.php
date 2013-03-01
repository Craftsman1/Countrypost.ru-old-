<div class='table centered_td centered_th'>
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<? if (isset($Orders2In) AND $Orders2In): ?>
	<table>
		<col width='auto' />
		<col width='auto' />
		<col width='auto' />
		<col width='auto' />
		<col width='auto' />
		<col width='auto' />
		<col width='1px' />
		<tr>
			<th>№ заявки / № заказа</th>
			<th>Клиент /<br>Посредник</th>
			<th>Сумма оплаты</th>
			<th>Сумма перевода</th>
			<th>Способ оплаты</th>
			<th>Статус</th>
			<th></th>
		</tr>
		<? foreach ($Orders2In as $o2i) : ?>
		<tr>
			<td>
				<b>№ <?= $o2i->order2in_id ?></b>
				<br>
				<a href="/admin/order/<?= $o2i->order_id ?>">№<?= $o2i->order_id ?></a>
				<br>
				<?= $order_types[$o2i->order_type] ?>
				<br>
				<?= date("d.m.Y H:i", strtotime($o2i->order2in_createtime)) ?>
				<br>
				<img id="payment_progress<?= $o2i->order2in_id ?>"
					 class="float"
					 style="display:none;"
					 src="/static/images/lightbox-ico-loading.gif"/>
			</td>
			<td>
				<a href="/<?= $o2i->client->statistics->login ?>"><?= $o2i->client->statistics->fullname ?></a>
				<br>
				(<?= $o2i->client->statistics->login ?>)
				<br>
				<a href="/<?= $o2i->manager->statistics->login ?>"><?= $o2i->manager->statistics->fullname ?></a>
				<br>
				(<?= $o2i->manager->statistics->login ?>)
			</td>
			<td>
				<input type="text"
					   id="payment_amount<?= $o2i->order2in_id ?>"
					   name="payment_amount<?= $o2i->order2in_id ?>"
					   class="int"
					   value="<?= $o2i->order2in_amount ?>"
					   style="width:60px"
					   maxlength="11"
					   onchange="update_payment_amount('<?= $o2i->order_id ?>',
							   '<?= $o2i->order2in_id ?>');">
				<br>
				<?= $o2i->order_currency ?>
				<? if ($o2i->excess_amount) : ?>
				(+<?= $o2i->excess_amount ?>
				<?= $o2i->order_currency ?>)
				<? endif; ?>
			</td>
			<td>
				<? if ($o2i->is_countrypost) : ?>
				<input type="text"
					   id="payment_amount_local<?= $o2i->order2in_id ?>"
					   name="payment_amount_local<?= $o2i->order2in_id ?>"
					   class="int"
					   value="<?= $o2i->order2in_amount_local ?>"
					   style="width:60px"
					   maxlength="11"
					   onchange="update_payment_amount_local('<?= $o2i->order_id ?>',
							   '<?= $o2i->order2in_id ?>');">
				<br>
				<?= $o2i->order2in_currency ?>
				<? else : ?>
				перевод
				<br>
				напрямую
				<br>
				посреднику
				<? endif; ?>
			</td>
			<td>
				<? View::show('/main/elements/payments/payment_description',
				array('payment' => $o2i)); ?>
			</td>
			<td>
				<select name="payment_status<?= $o2i->order2in_id ?>"
						id="payment_status<?= $o2i->order2in_id ?>"
						class="order_status"
						onchange="update_payment_status('<?= $o2i->order_id ?>',
								'<?= $o2i->order2in_id ?>');">
				<? foreach ($Orders2InStatuses as $status => $status_name) : ?>
					<option value="<?= $status ?>" <? if ($o2i->order2in_status == $status) :
						?>selected="selected"<? endif; ?>><?= $status_name ?></option>
					<? endforeach; ?>
				</select>
			</td>
			<td>
				<a href="/admin/payment/<?= $o2i->order2in_id ?>">Посмотреть</a>
				<? if ($o2i->order2in_2admincomment) : ?>
				<br>
				Добавлен
				<br>
				новый
				<br>
				комментарий
				<? endif; ?>
				<br>
				<br>
				<a href="/admin/deletePayment/<?= $o2i->order2in_id ?>"><img
					title="Удалить"
					border="0"
					src="/static/images/delete.png"></a>
			</td>
		</tr>
		<? endforeach; ?>
	</table>
	<? else : ?>
	<div align="center">Заявки отсутствуют</div>
	<? endif; ?>
</div>
<? if (isset($pager)) echo $pager ?>
<script>
function status_handler(tab_status)
{
	payment_status_handler('<?= $selfurl ?>');
}

function refreshOrderTotals(order, success_message, error_message)
{
	if (order['is_error'])
	{
		error('top', error_message);
	}
	else
	{
		success('top', success_message);
	}
}
</script>