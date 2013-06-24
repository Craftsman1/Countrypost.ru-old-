<div id="balance" class='table admin-inside'>
	<table>
		<col width='auto' />
		<col align='right' />
		<? if ($balance) : $country = ''; ?>
		<? foreach ($balance as $manager) : ?>
		<? if ($manager->country_name != $country) : $country = $manager->country_name; ?>
		<tr>
			<th colspan="2">
				<b><?= $manager->country_name ?></b>
			</th>
		</tr>
		<? endif; ?>
		<tr>
			<td style="text-align: justify;border-right: 0;">
				<a href="<?= $this->config->item('base_url') . $manager->user_login ?>"><?= $manager->manager_name ?></a>
				(<?= $manager->user_login ?>)
			</td>
			<td style="text-align: right;border-left: 0;">
				<?= $manager->balance ?>
				<?= $manager->country_currency ?>
			</td>
		</tr>
		<? endforeach; ?>
		<? else : ?>
		<tr class="last-row">
			<td colspan="8">Балансы не найдены.</td>
		</tr>
		<? endif; ?>
	</table>
</div>
<? if (isset($pager)) echo $pager; ?>