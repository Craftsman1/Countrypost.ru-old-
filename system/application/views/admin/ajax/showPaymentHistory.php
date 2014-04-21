<div id="pagerForm" class='table admin-inside centered_th'>
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<table>
		<col width='auto' />
		<col width='auto' />
		<col width='auto' />
		<col width='auto' />
		<col width='auto' />
		<col width='auto' />
		<col width='auto' />
		<col width='auto' />
		<col width='auto' />
		<? if ($Payments) : ?>
			<tr class='last-row'>
				<td colspan='9'>
					<div style="margin:0 0 10px 0;">
						<label>Платежей на странице:</label>
						<select class="per_page" name="per_page" onchange="javascript:updatePerPage(this);">
							<option value="10" <?= $per_page == 10 ? 'selected' : ''?>>10</option>
							<option value="50" <?= $per_page == 50 ? 'selected' : ''?>>50</option>
							<option value="100" <?= $per_page == 100 ? 'selected' : ''?>>100</option>
							<option value="200" <?= $per_page == 200 ? 'selected' : ''?>>200</option>
							<option value="350" <?= $per_page == 350 ? 'selected' : ''?>>350</option>
							<option value="500" <?= $per_page == 500 ? 'selected' : ''?>>500</option>
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<th>
					<input type="checkbox" id="check_all">
				</th>
				<th>Номер / Дата</th>
				<th>Отправитель</th>
				<th>Получатель</th>
				<th>Назначение платежа</th>
				<th>Комментарий</th>
				<th>Сумма оплаты</th>
				<th>Сумма USD</th>
				<th>Статус</th>
			</tr>
			<? foreach ($Payments as $Payment) : ?>
			<tr <?= ($Payment->payment_purpose == 'отмена дополнительного платежа' ||
					$Payment->payment_purpose == 'отмена дополнительного платежа в местной валюте') ?
					'style="background-color:red;"' : '' ?>>
				<td>
					<input type="checkbox"
						   class="check_payment"
						   id="check<?= $Payment->payment_id ?>">
				</td>
				<td>
					<b><?= $Payment->payment_id ?></b>
					<?= date('d-m-Y H:i', strtotime($Payment->payment_time)) ?>
					<img class="float" id="payment_progress<?= $Payment->payment_id ?>"
						 style="display:none;vertical-align: middle;margin-left: 5px;"
						 src="/static/images/lightbox-ico-loading.gif"/>
				</td>
				<td>
					<? if (isset($Payment->user_from)) : ?>
					[<?= $Payment->payment_from ?>] <?=$Payment->user_from?>
					<? else :?>
					<?= $Payment->payment_from ?>
					<? endif; ?>
				</td>
				<td>
					<? if (isset($Payment->user_to)) : ?>
					[<?=$Payment->payment_to?>] <?=$Payment->user_to?>
					<? else :?>
					<?=$Payment->payment_to?>
					<? endif; ?>
				</td>
				<td>
					<?= $Payment->payment_purpose ?>
				</td>
				<td>
					<?=$Payment->payment_comment?>
				</td>
				<td>
					<? if ($Payment->payment_type == 'order')
					{
						if ($Payment->payment_from == 1 || $Payment->payment_to == 1)
						{
							echo $Payment->payment_amount_tax;
						}
						else if ($Payment->payment_purpose == 'оплата заказа' ||
							$Payment->payment_purpose == 'оплата заказа в местной валюте')
						{
							echo $Payment->payment_amount_to;
						}
						else if ($Payment->payment_purpose == 'доплата заказа в местной валюте')
						{
							echo ($Payment->payment_amount_to + $Payment->payment_amount_from);
						}
						else
						{
							echo $Payment->payment_amount_from;
						}
					}
					else if ($Payment->payment_type == 'extra_payment')
					{
						echo ($Payment->payment_amount_to ? $Payment->payment_amount_to : $Payment->payment_amount_from);
					}
					else
					{
						echo $Payment->payment_amount_from;
					} ?>
					<?= $Payment->payment_currency ?>
					<? if ($Payment->excess_amount) : ?>
					(+<?= $Payment->excess_amount ?>
					<?= $Payment->payment_currency ?>)
					<? endif; ?>
				</td>
				<td>
					<? if ( ! empty($Payment->amount_usd)) : ?>
					<?= $Payment->amount_usd ?> USD
					(<?= $Payment->usd_conversion_rate ?>)
					<? endif; ?>
				</td>
				<td>
					<? if ($Payment->status == 'sent_by_client') : ?>
					<a href="<?= $selfurl; ?>payment/<?= $Payment->order2in_id ?>"><?= $statuses[$Payment->status] ?>
					<? else : ?>
					<select id="payment_status<?= $Payment->payment_id ?>"
							name="payment_status<?= $Payment->payment_id ?>"
							onchange="update_payment_status('<?= $Payment->payment_id ?>');">
						<? foreach ($countrypost_statuses as $status => $status_name) : ?>
						<option value="<?= $status ?>" <? if ($Payment->status == $status) :
							?>selected<? endif; ?>><?= $status_name ?></option>
						<? endforeach; ?>
					</select>
					<? endif; ?>
				</td>
			</tr>
			<? endforeach; ?>
			<tr class='last-row'>
				<td colspan='6'>
					<div style="margin-top:11px;margin-right:10px; float: left;">
						<select id='total_status'>
							<option value='' selected>выберите статус...</option>
							<option value='sent_by_client'>Переведено клиентом</option>
							<option value='not_payed'>К выплате</option>
							<option value='payed'>Выплачено</option>
						</select>
					</div>
					<div style="margin-top:11px;margin-right:10px;">
						<label>Платежей на странице:</label>
						<select class="per_page" name="per_page" onchange="javascript:updatePerPage(this);">
							<option value="10" <?= $per_page == 10 ? 'selected' : ''?>>10</option>
							<option value="50" <?= $per_page == 50 ? 'selected' : ''?>>50</option>
							<option value="100" <?= $per_page == 100 ? 'selected' : ''?>>100</option>
							<option value="200" <?= $per_page == 200 ? 'selected' : ''?>>200</option>
							<option value="350" <?= $per_page == 350 ? 'selected' : ''?>>350</option>
							<option value="500" <?= $per_page == 500 ? 'selected' : ''?>>500</option>
						</select>
					</div>
				</td>
				<td colspan='5' align="right">
					<div style="margin-top:16px; font-size: 17px; font-family: Arial; font-weight: bold;">
						Итого к выплате:
						<b class="total_usd"><?= $total_usd ?></b> USD
						<? if (isset($total_local)) : ?>
						(<?= $total_local ?>)
						<? endif; ?>
					</div>
				</td>
			</tr>
			<? else : ?>
			<tr>
				<td colspan="8">Платежи не найдены</td>
			</tr>
			<? endif; ?>
	</table>
</div>
<? if (isset($pager)) echo $pager ?>
<script>
	$('div#pagerForm input#check_all').change(function() {
		if ($(this).is(':checked'))
		{
			$('div#pagerForm input.check_payment').attr('checked', 'checked');
		}
		else
		{
			$('div#pagerForm input.check_payment').removeAttr('checked');
		}
	});

	$('div#pagerForm select#total_status').change(function() {
		if ($(this).val() &&
			$('div#pagerForm input.check_payment:checked').length > 0)
		{
			$('div#pagerForm input.check_payment:checked')
				.parents('tr')
				.find('option[value=' + $(this).val() + ']')
					.each(function() {
						$(this).attr('selected', true);
						eval($(this).parent().attr('onchange'));
					});
		}
	});
</script>