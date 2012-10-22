<div class='content'>
	<h2>Адреса, на которые вы можете делать заказы</h2>
	<p>Ваши виртуальные адреса, на которые Вы можете заказывать в любое время. Мы получим посылку и отправим ее Вам.</p>
	<? if ($partners) : ?>
	<div class='table' id="Addresses">
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<select id="country" class="select">
			<option value="">выберите страну...</option>
			<? foreach($partners as $address) : 
				if (isset($partner_id) && $address->manager_user == $partner_id):?>
			<option value="<?=$address->manager_user?>" selected><?=$address->country_name?></option>
			<? else : ?>
			<option value="<?=$address->manager_user?>"><?=$address->country_name?></option>
			<? endif; ?>
			<? endforeach; ?>
		</select>
		<br />
		<table>
			<? foreach($partners as $manager) : ?>
			<tr style="display:none;" class="tr<?= $manager->manager_user ?>">
				<td>Адрес:</td>
				<td><?=$manager->manager_addres?> (<?= $client->client_user ?>)
					<? if (!empty($manager->manager_address_local)) : ?>
					<br />
					<?= $manager->manager_address_local ?>
					(<?= $client->client_user ?>)
					<? endif; ?>					
				</td>
			</tr>
			<tr class="tr<?= $manager->manager_user ?>">
				<td>Получатель:</td>
				<td>
					<?= sprintf($manager->manager_address_description, $client->client_user) ?>
				</td>
			</tr>
			<tr style="display:none;" class="tr<?= $manager->manager_user ?>">
				<td>Телефон:</td>
				<td><?= $manager->manager_phone ?></td>
			</tr>
			<? if ( ! empty($manager->manager_description)) : ?>
			<tr class="tr_html_<?= $manager->manager_user ?>">
				<td colspan="2">
					<?= html_entity_decode($manager->manager_description) ?>
				</td>
			</tr>
			<? endif; ?>
			<? endforeach; ?>			
		</table>
	</div>
	<? endif; ?>	
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#country').change(function() {
			var selectedId = $('#country').val();
			$('#Addresses tr').hide();
			
			if (selectedId == '') 
			{
				return;
			}
			
			$('.tr' + selectedId + ',.tr_html_' + selectedId).show();
		});
		
		$('#country').change();
	});
</script>