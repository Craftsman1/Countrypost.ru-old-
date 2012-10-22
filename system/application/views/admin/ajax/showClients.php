<a name="pagerScroll"></a>
<form class='admin-inside' id="clientsForm" action="<?=$selfurl?>moveClients" method="POST">
	<ul class='tabs'>
		<li><div><a href='<?=$selfurl?>showNewPackages'>Новые</a></div></li>
		<li><div><a href='<?=$selfurl?>showPayedPackages'>Оплаченные</a></div></li>
		<li><div><a href='<?=$selfurl?>showSentPackages'>Отправленные</a></div></li>
		<li><div><a href='<?=$selfurl?>showOpenOrders'>Заказы “Помощь в покупке”</a></div></li>
		<li><div><a href='<?=$selfurl?>showPayedOrders'>Оплаченные заказы</a></div></li>
		<li><div><a href='<?=$selfurl?>showSentOrders'>Закрытые заказы</a></div></li>
		<li  class='active'><div><a href='<?=$selfurl?>showClients'>Клиенты</a></div></li>
		<li><div><a href='<?=$selfurl?>showPartners'>Партнеры</a></div></li>
	</ul>
	
	<div class='table'>
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
			<tr>
				<th>Номер клиента</th>
				<th>Логин</th>
				<th>Ф.И.О.</th>
				<th>Адрес доставки</th>
				<th>Кол-во сделанных заказов</th>
				<th>Партнер / Страна</th>
				<th>Баланс</th>
				<th>Изменить / удалить</th>
			</tr>
			<?if ($clients): foreach ($clients as $client):?>
			<tr>
				<td>
					<b>
						№ <?= $client->client_user ?>
					</b>
				</td>
				<td>
					<?= $client->user_login ?> / <?= $country_list[$client->client_country] ?>
				</td>
				<td>
					<?= $client->client_surname ?>
					<?= $client->client_name ?>
					<?= $client->client_otc ?>
				</td>
				<td>
					<?= $client->client_address ?>
				</td>
				<td nowrap>
					Пересылка: 
					<?= intval($client->package_count) ?>
					<br />
					(новых: 
					<a href='<?= $selfurl ?>showNewPackages/' target='_blank' onclick='showFilteredItems("Package", "New", "<?= $client->client_user ?>");'><?= intval($client->package_count) - intval($client->package_payed_count) - intval($client->package_sent_count) ?></a>,
					оплаченных: 
					<a href='<?= $selfurl ?>showPayedPackages/' target='_blank' onclick='showFilteredItems("Package", "Payed", "<?= $client->client_user ?>");'><?= intval($client->package_payed_count) ?></a>,
					отправленных: 
					<a href='<?= $selfurl ?>showSentPackages/' target='_blank' onclick='showFilteredItems("Package", "Sent", "<?= $client->client_user ?>");'><?= intval($client->package_sent_count) ?></a>)
					<br />
					<br />
					Помощь в заказе: 
					<?= intval($client->order_count) ?>
					<br />
					(новых: 
					<a href='<?= $selfurl ?>showOpenOrders/' target='_blank' onclick='showFilteredItems("Order", "Open", "<?= $client->client_user ?>");'><?= intval($client->order_count) - intval($client->order_payed_count) - intval($client->order_sent_count) ?></a>,
					оплаченных: 
					<a href='<?= $selfurl ?>showPayedOrders/' target='_blank' onclick='showFilteredItems("Order", "Payed", "<?= $client->client_user ?>");'><?= intval($client->order_payed_count) ?></a>,
					отправленных: 
					<a href='<?= $selfurl ?>showSentOrders/' target='_blank' onclick='showFilteredItems("Order", "Sent", "<?= $client->client_user ?>");'><?= intval($client->order_sent_count) ?></a>)
				</td>
				<td nowrap>
					<?if ($client->managers) : foreach ($client->managers as $manager) : ?>
						<?= $manager->user_login ?> / <?= $country_list[$manager->manager_country] ?>
						<br />
					<? endforeach; endif; ?>	
					<input type="checkbox" name="move<?=$client->client_user?>"/>
				</td>
				<td>
					<a href='<?=$selfurl?>editClientBalance/<?=$client->client_user?>' title='Изменить'>$<?= $client->user_coints ?></a>
				</td>
				<td align="center">
					<a href='<?=$selfurl?>editClient/<?=$client->client_user?>'>Изменить</a><br/>
					<hr />
					<a href='<?=$selfurl?>deleteClient/<?=$client->client_user?>'><img title="Удалить" border="0" src="/static/images/delete.png"></a>
					<br/>
				</td>
			</tr>
			<?endforeach;endif;?>
			<tr class='last-row'>
				<td colspan='9'>
					<div class='float'>	
						<div class='submit'>
							Переместить к:
							<select name="newPartnerId" id="newPartnerId">
								<option value="-1">выбрать...</option>
								<?if ($managers && $countries) : foreach($managers as $manager) : ?>
									<option value="<?=$manager->manager_user?>"><?=$manager->user_login?> (<?=$country_list[$manager->manager_country]?>)</option>
								<?endforeach; endif;?>
							</select>
					</div></div>
				</td>
				<td></td>
			</tr>
		</table>
	</div>
</form>
<? if (isset($pager)) echo $pager ?>
<script type="text/javascript">
	$(document).ready(function() {
		$('#newPartnerId').change(function() 
		{
			var selectedPartner = $('#newPartnerId option:selected');
			
			if (selectedPartner.val() == '-1')
			{
				return false;
			}
			
			if ($('#clientsForm input:checkbox:checked').size() == 0)
			{
				alert('Выберите клиентов для перемещения.');
				return false;
			}
			
			if (confirm('Вы уверены, что хотите переместить выбранных клиентов к новому партнеру?'))
			{
				document.getElementById('clientsForm').submit();
			}
		});
	});
</script>