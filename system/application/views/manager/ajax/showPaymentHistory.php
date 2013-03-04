	<div id="pagerForm" class='admin-inside table'>
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
					<th>№ / Дата</th>
					<th>Клиент</th>
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
						<?=$Payment->payment_from == 1 ? 'Countrypost.ru' : $Payment->user_from ?>
					<? else :?>
						<?=$Payment->payment_from?>
					<? endif; ?>
					</td>
					<td><?=$Payment->payment_purpose?></td>
					<td><?=$Payment->payment_comment?></td>
					<td>
						<?= $Payment->payment_to == $user->user_id ? $Payment->payment_amount_to : $Payment->payment_amount_from ?>
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
						<?= $statuses[$Payment->status] ?>
					</td>
				</tr>
				<?endforeach;?>	
				<tr class='last-row'>
					<td colspan='4'>
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
					<td colspan='4' align="right">
						<div style="margin-top:16px; font-size: 17px; font-family: Arial; font-weight: bold;">
							Итого к выплате:
							<b class="total_usd"><?= $total_usd ?></b> USD
							<? if (isset($total_local)) : ?>
							(<?= $total_local ?> <?= $total_currency ?>)
							<? endif; ?>
						</div>
					</td>
				</tr>

			<? else : ?>
				<tr>
					<td colspan="8">Платежи не найдены</td>
				</tr>
			<?endif;?>
		</table>
	</div>
	<?php if (isset($pager)) echo $pager ?>