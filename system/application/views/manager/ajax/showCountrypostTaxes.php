<div id="pagerForm" class='admin-inside table'>
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<table>
		<? if ($taxes) : ?>
			<tr class='last-row'>
				<td colspan='3'>
					<div style="margin:0 0 10px 0;">
						<label>Комиссий на странице:</label>
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
				<th>Заказ</th>
				<th>Сумма оплаты</th>
				<th>Сумма USD</th>
				<th>Статус</th>
			</tr>
			<? foreach ($taxes as $tax) : ?>
			<tr>
				<td>
					<b><?= $tax->tax_id ?></b>
					<?= date('d-m-Y H:i', strtotime($tax->usd_conversion_date)) ?>
				</td>
				<td>
					<a href="<?= BASEURL . $this->user->user_group . '/order/' . $tax->order_id ?>">№ <?=
						$tax->order_id ?>
				</td>
				<td>
					<?= $tax->amount ?>
					<?= $tax->currency ?>
				</td>
				<td>
					<? if ( ! empty($tax->amount_usd)) : ?>
					<?= $tax->amount_usd ?> USD
					(<?= $tax->usd_conversion_rate ?>)
					<? endif; ?>
				</td>
				<td>
					<?= $statuses[$tax->status] ?>
				</td>
			</tr>
			<?endforeach;?>
			<tr class='last-row'>
				<td colspan='3'>
					<div style="margin-top:11px;margin-right:10px;">
						<label>Комиссий на странице:</label>
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
<? if (isset($pager)) echo $pager; ?>