<div id="balance" class='table admin-inside'>
	<table>
		<col width='auto' />
		<col align='right' />
		<? if ($balance) : $country = ''; ?>
		<? foreach ($balance as $manager) : ?>
		<? if ($manager->country_name != $country) : $country = $manager->country_name; ?>
		<tr>
			<th colspan="2">
				<img src="/static/images/flags/<?= $manager->country_name_en ?>.png"
					 style="margin-right:7px;vertical-align: bottom;margin-top: -3px;margin-bottom: -3px;"
					 title="<?= $manager->country_name ?>">
				<a href="<?= $this->config->item('base_url') . $manager->user_login ?>"><?= $manager->manager_name ?></a>
				(<?= $manager->user_login ?>)
			</th>
		</tr>
		<? endif; ?>
		<tr>
			<td style="text-align: justify;border-right: 0;">
				<a href="<?= $this->config->item('base_url'). 'client/order/' . $manager->order_id ?>">Заказ №<?= $manager->order_id ?></a>
			</td>
			<td style="text-align: right;border-left: 0;">
				<?= $manager->balance ?>
				<?= $manager->country_currency ?>
			</td>
		</tr>
		<? endforeach; ?>
		<? else : ?>
		<tr class="last-row">
			<td colspan="8">Остатки в Ваших заказах не найдены.</td>
		</tr>
		<? endif; ?>
	</table>
</div>
<? if (isset($pager)) echo $pager; ?>