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
					<th>Номер / Дата</th>
					<th>Отправитель</th>
					<th>Получатель</th>
					<th>Способ оплаты</th>
					<th>Назначение платежа</th>
					<th>Комментарий</th>
					<th>Сумма оплаты</th>
					<th>Сумма USD</th>
					<th>Статус</th>
				</tr>
				<?foreach ($Payments as $Payment):?>
				<tr <?= ($Payment->payment_purpose == 'отмена дополнительного платежа' ||
						$Payment->payment_purpose == 'отмена дополнительного платежа в местной валюте') ?
						'style="background-color:red;"' : '' ?>>
					<td><b><?=$Payment->payment_id?></b> <?=date('d-m-Y H:i', strtotime($Payment->payment_time))?></td>
					<td>
					<? if (isset($Payment->user_from)) : ?>
						[<?=$Payment->payment_from?>] <?=$Payment->user_from?>
					<? else :?>
						<?=$Payment->payment_from?>
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
						<? if (isset($Payment->payment_service_id)) : foreach ($services as $service) : 
						if ($service->payment_service_id == $Payment->payment_service_id) : 
							echo $service->payment_service_name;
						break; endif; endforeach; endif; ?>
					</td>
					<td><?=$Payment->payment_purpose?></td>
					<td><?=$Payment->payment_comment?></td>
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
					</td>
					<td>
						<? if ( ! empty($Payment->amount_usd)) : ?>
						<?= $Payment->amount_usd ?>
						(<?= $Payment->usd_conversion_rate ?>)
						<? endif; ?>
					</td>
					<td>
						Выплачено
					</td>
				</tr>
				<?endforeach;?>	
				<tr class='last-row'>
					<td colspan='9'>
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
				</tr>
			<?else:?>
				<tr>
					<td colspan="8">Платежи не найдены</td>
				</tr>
			<?endif;?>
		</table>
	</div>
	<?php if (isset($pager)) echo $pager ?>