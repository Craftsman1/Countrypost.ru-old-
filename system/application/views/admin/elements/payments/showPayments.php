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
			<th>№ заявки</th>
			<th>№ заказа / клиент</th>
			<th>Сумма оплаты</th>
			<th>Способ оплаты</th>
			<th>Статус</th>
			<th></th>
		</tr>
		<? foreach ($Orders2In as $o2i) :
			$is_editable = ($o2i->order2in_status != 'payed') ;
		?>
		<tr>
			<td>
				<b>№ <?= $o2i->order2in_id ?></b>
				<br />
				<?= date("d.m.Y H:i", strtotime($o2i->order2in_createtime)) ?>
				<? if ($is_editable) : ?>
				<br>
				<img id="payment_progress<?= $o2i->order2in_id ?>"
					 class="float"
					 style="display:none;"
					 src="/static/images/lightbox-ico-loading.gif"/>
				<? endif; ?>
			</td>
			<td>
				<a href="/admin/order/<?= $o2i->order_id ?>">№<?= $o2i->order_id ?></a>
				<br>
				<?= $order_types[$order->order_type] ?>
				<br>
				<a href="/<?= $o2i->statistics->login ?>"><?= $o2i->statistics->fullname ?></a>
				(<?= $o2i->statistics->login ?>)
			</td>
			<td>
				<? if ($is_editable) : ?>
				<input type="text"
					   id="payment_amount<?= $o2i->order2in_id ?>"
					   name="payment_amount<?= $o2i->order2in_id ?>"
					   class="int"
					   value="<?= $o2i->order2in_amount ?>"
					   style="width:60px"
					   maxlength="11"
					   onchange="update_payment_amount('<?= $order->order_id ?>',
							   '<?= $o2i->order2in_id ?>');">
				<? else : ?>
				<?= $o2i->order2in_amount ?>
				<?= $order->order_currency ?>
				<? endif; ?>
			</td>
			<td>
				<? View::show('/main/elements/payments/payment_description',
				array('payment' => $o2i)); ?>
			</td>
			<td>
				<? if ($is_editable) : ?>
				<select name="payment_status<?= $o2i->order2in_id ?>"
						id="payment_status<?= $o2i->order2in_id ?>"
						class="order_status"
						onchange="update_payment_status('<?= $order->order_id ?>',
								'<?= $o2i->order2in_id ?>');">
				<? foreach ($Orders2InStatuses as $status => $status_name) : ?>
					<option value="<?= $status ?>" <? if ($o2i->order2in_status == $status) :
						?>selected="selected"<? endif; ?>><?= $status_name ?></option>
					<? endforeach; ?>
				</select>
				<? else : ?>
				<?= $Orders2InStatuses[$o2i->order2in_status] ?>
				<? endif; ?>
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
				<? if ($o2i->order2in_status != 'payed') : ?>
				<br>
				<br>
				<a href="/admin/deletePayment/<?= $o2i->order2in_id ?>"><img
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
<script>
function status_handler(tab_status)
{
	payment_status_handler('<?= $selfurl ?>');
}
</script>