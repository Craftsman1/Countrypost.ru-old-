	<a name="pagerScroll"></a>
	<div id="pagerForm" class='table'>
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
			<?if ($Payments):?>
				<tr>
					<th>№ / Дата</th>
					<!--th>Отправитель</th-->
					<th>Получатель</th>
					<th>Назначение платежа</th>
					<!--th>Способ оплаты</th-->
					<th>Комментарий</th>
					<th>Сумма оплаты</th>
					<!--th>Статус</th-->
				</tr>
				<?foreach ($Payments as $Payment):?>
				<tr  <?= ($Payment->payment_purpose == 'отмена дополнительного платежа') ?
						'style="background-color:red;"' : '' ?>>
					<td><b><?=$Payment->payment_id?></b> <?=date('d-m-Y H:i', strtotime($Payment->payment_time))?></td>
					<!--td>
						<? if (isset($Payment->user_from)) : ?>
							<?=$Payment->payment_from == 1 ? 'Countrypost.ru' : $Payment->user_from ?>
						<? else :?>
							<?=$Payment->payment_from?>
						<? endif; ?>
					</td-->
					<td>
					<? if (isset($Payment->user_to)) : ?>
						<?=$Payment->payment_to == 1 ? 'Countrypost.ru' : $Payment->user_to ?>
					<? else :?>
						<?=$Payment->payment_to?>
					<? endif; ?>
					</td>
					<td><?=$Payment->payment_purpose?></td>
					<!--td>
						<? if (isset($Payment->payment_service_id)) : foreach ($services as $service) : 
						if ($service->payment_service_id == $Payment->payment_service_id) : 
							echo $service->payment_service_name;
						break; endif; endforeach; endif; ?>
					</td-->
					<td><?= $Payment->payment_comment ?></td>
					<td>
						<?= $Payment->payment_to == $user->user_id ? $Payment->payment_amount_to : $Payment->payment_amount_from ?>
						<?= isset($Payment->payment_currency) ? $Payment->payment_currency : '$' ?>
					</td>
					<!--td>
						Выплачено
					</td-->
				</tr>
				<?endforeach;?>	
			<?else:?>
				<tr>
					<td colspan="8">Платежи не найдены</td>
				</tr>
			<?endif;?>
		</table>
	</div>
	<?php if (isset($pager)) echo $pager ?>