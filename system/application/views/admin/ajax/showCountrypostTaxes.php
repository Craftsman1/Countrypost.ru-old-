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
            <th><input type="checkbox" id="check_all"/></th>
			<th>№ / Дата</th>
			<th>Посредник</th>
			<th>Назначение платежа</th>
			<th>Комментарий</th>
			<th>Комиссия Countrypost</th>
			<th>Сумма USD</th>
			<th>Статус</th>
		</tr>
		<? foreach ($taxes as $tax) : ?>
		<tr>
            <td>
                <input type="checkbox" class="check_tax" id="check<?= $tax->tax_id ?>"/>
            </td>
			<td>
				<b><?= $tax->tax_id ?></b>
				<?= date('d-m-Y H:i', strtotime($tax->usd_conversion_date)) ?>
                <img class="float" id="tax_progress<?= $tax->tax_id ?>"
                     style="display:none;vertical-align: middle;margin-left: 5px;"
                     src="/static/images/lightbox-ico-loading.gif"/>
			</td>
			<td>
				<a href='<?= $this->config->item('base_url') . $tax->dealer_login ?>'><?= $tax->dealer_login ?>
					(№ <?= $tax->manager_id ?>)</a>
			</td>
			<td>
				Оплата <? switch($tax->order_type)
				{
					case 'online' : echo 'Online заказа'; break;
					case 'offline' : echo 'Offline заказа'; break;
					case 'service' : echo 'услуги'; break;
					case 'delivery' : echo 'доставки'; break;
					case 'mail_forwarding' : echo 'заказа Mail Forwarding'; break;
				}
				?>
			</td>
			<td>
				<a href="<?= $this->config->item('base_url') . $this->user->user_group . '/order/' . $tax->order_id ?>">№ <?=
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
				<?//= $statuses[$tax->status] ?>
				<select id="tax_status<?= $tax->tax_id ?>"
						name="tax_status<?= $tax->tax_id ?>"
						onchange="update_tax_status('<?= $tax->tax_id ?>');">
					<? foreach ($statuses as $status => $status_name) : ?>
						<option value="<?= $status ?>" <? if ($tax->status == $status) :
						?>selected<? endif; ?>><?= $status_name ?></option>
					<? endforeach; ?>
				</select>

			</td>
		</tr>
		<?endforeach;?>
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
			<td colspan='5' align="right">
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
			<td colspan="8" align='center'>Комиссии не найдены.</td>
		</tr>
	<?endif;?>
	</table>
</div>
<? if (isset($pager)) echo $pager; ?>
<script>
    $('div#pagerForm input#check_all').change(function() {
        if ($(this).is(':checked'))
        {
            $('div#pagerForm input.check_tax').attr('checked', 'checked');
        }
        else
        {
            $('div#pagerForm input.check_tax').removeAttr('checked');
        }
    });

    $('div#pagerForm select#total_status').change(function() {
        if ($(this).val() &&
            $('div#pagerForm input.check_tax:checked').length > 0)
        {
            $('div#pagerForm input.check_tax:checked')
                .parents('tr')
                .find('option[value=' + $(this).val() + ']')
                .each(function() {
                    $(this).attr('selected', true);
                    eval($(this).parent().attr('onchange'));
                });
        }
    });
</script>