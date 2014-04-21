<div class='table centered_td centered_th'>
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<? if (isset($Orders2In) AND $Orders2In) : ?>
	<table>
		<col width='auto' />
		<col width='auto' />
		<col width='auto' />
		<col width='auto' />
		<col width='auto' />
		<col width='auto' />
		<col width='1px' />
		<tr>
			<th>№ заявки</th>
			<th>№ заказа / посредник</th>
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
				<br />
				<?= date("d.m.Y H:i", strtotime($o2i->order2in_createtime)) ?>
			</td>
			<td>
				<a href="/client/order/<?= $o2i->order_id ?>">№<?= $o2i->order_id ?></a>
				<br>
				<?= $order_types[$order->order_type] ?>
				<br>
				<a href="/<?= $o2i->statistics->login ?>"><?= $o2i->statistics->fullname ?></a>
				(<?= $o2i->statistics->login ?>)
			</td>
			<td>
				<?= $o2i->order2in_amount ?>
				<?= $order->order_currency ?>
				<? if ($o2i->excess_amount) : ?>
				(+<?= $o2i->excess_amount ?>
				<?= $order->order_currency ?>)
				<? endif; ?>
			</td>
			<td>
				<? if ($o2i->is_countrypost == 0) : ?>
				перевод
				<br>
				напрямую
				<br>
				посреднику
				<? else : ?>
				<?= $o2i->order2in_amount_local ?>
				<?= $o2i->order2in_currency ?>
				<? endif; ?>
			</td>
			<td>
				<? View::show('/main/elements/payments/payment_description',
				array('payment' => $o2i)); ?>
			</td>
			<td>
				<?= $Orders2InStatuses[$o2i->order2in_status] ?>
			</td>
			<td>
				<a href="/client/payment/<?= $o2i->order2in_id ?>">Посмотреть</a>
				<? if ($o2i->order2in_2clientcomment) : ?>
				<br>
				Добавлен
				<br>
				новый
				<br>
				комментарий
				<? endif; ?>
				<? if ($o2i->order2in_status != 'payed') : ?>
				<br>
				<br>
				<a href="/client/deletePayment/<?= $o2i->order2in_id ?>"><img
					title="Удалить"
					border="0"
					src="/static/images/delete.png"></a>
				<? endif; ?>
			</td>
		</tr>
		<? endforeach; ?>
	</table>
	<? else : ?>
	<div align="center">Заявки отсутствуют</div>
	<? endif; ?>
</div>
<? if (isset($pager)) echo $pager ?>