<a name="pagerScroll"></a>
<form method="POST" action="/syspay/showGate" id="pagerForm">
	<ul class='tabs'>
		<li class='active'><div><a href='<?= $selfurl ?>showOpenOrders2In'>Новые</a></div></li>
		<li><div><a href='<?= $selfurl ?>showPayedOrders2In'>Выплаченные</a></div></li>
	</ul>
	<div class='table centered_td centered_th'>
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<? if(isset($Orders2In) && count($Orders2In)): ?>
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
			<? $o = $order; foreach ($Orders2In as $order) : ?>
			<tr>
				<td>
					<b>№ <?= $order->order2in_id ?></b>
					<br />
					<?= date("d.m.Y H:i", strtotime($order->order2in_createtime)) ?>
				</td>
				<td>
					<a href="/client/order/<?= $order->order_id ?>">№<?= $order->order_id ?></a>
					<br>
					<?= $order_types[$o->order_type] ?>
					<br>
					<a href="/<?= $order->statistics->login ?>"><?= $order->statistics->fullname ?></a>
					(<?= $order->statistics->login ?>)
				</td>
				<td>
					<?= $order->order2in_amount ?>
					<?= $o->order_currency ?>
				</td>
				<td>
					<? if ($order->is_countrypost == 0) : ?>
					перевод
					<br>
					напрямую
					<br>
					посреднику
					<? else : ?>
					<?= $order->order2in_amount_local ?>
					<?= $order->order2in_currency ?>
					<? endif; ?>
				</td>
				<td>
					<? View::show('/main/elements/payments/payment_description',
						array('payment' => $order)); ?>
				</td>
				<td>
					<?= $Orders2InStatuses[$order->order2in_status] ?>
				</td>
				<td>
					<a href="/client/payment/<?= $order->order2in_id ?>">Посмотреть</a>
					<? if ((isset($client) && $order->order2in_2clientcomment) OR ($this->user->user_group == 'admin' && $order->order2in_2admincomment)): ?>
					<br />Добавлен новый коментарий
					<? endif; ?>
					<? if ($order->order2in_status != 'payed') : ?>
					<br>
					<br>
					<a href="/client/deletePayment/<?= $order->order2in_id ?>"><img title="Удалить" border="0"
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
</form>