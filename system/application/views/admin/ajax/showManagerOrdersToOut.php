	<a name="pagerScroll"></a>
	<form id="pagerForm" class='admin-inside' action='<?=$selfurl?>saveOrders2out/showManagerOrdersToOut' method="POST">
		<ul class='tabs'>
			<li class='active'><div><a href='<?=$selfurl?>showManagerOrdersToOut'>Новые</a></div></li>
			<li><div><a href='<?=$selfurl?>showManagerPayedOrdersToOut'>Выплаченные</a></div></li>
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
				<col width='10' />
				
				<?if ($Orders):?>
				
					<tr>
						<th>Номер заявки</th>
						<th>Партнер</th>
						<th>Сумма</th>
						<th>Статус</th>
						<th>Комментарий</th>
						<th>Удалить</th>
					</tr>
					
					<?foreach ($Orders as $Order):?>
					<tr>
						<td><b>№ <?=$Order->order2out_id?></b><br/><?=$Order->order2out_time?></td>
						<td>Логин: <?=$Order->user_login?><br/>Номер: <?=$Order->order2out_user?></td>
						<td>
							<?= isset($Order->order2out_currency) && 
								empty($Order->order2out_ammount_local) ? 
									preg_replace("/;;/", ";", $Order->order2out_currency.';').$Order->order2out_ammount : // старые платежи в местной валюте
									''?>
							<?= isset($Order->order2out_currency) && 
								!empty($Order->order2out_ammount_local) ? 
									preg_replace("/;;/", ";", $Order->order2out_currency.';').$Order->order2out_ammount_local.'<br />' : // новые платежи в местной валюте
									''?>
							<?= !empty($Order->order2out_ammount) && 
								!isset($Order->order2out_ammount_local) && 
								empty($Order->order2out_currency) ? 
									'$'.$Order->order2out_ammount : // старые платежи в долларах
									'' ?>
							<?= !empty($Order->order2out_ammount) && 
								isset($Order->order2out_ammount_local) ?
									'$'.$Order->order2out_ammount : // новые платежи в долларах
									'' ?>
						</td>
						<td>
							<select name="status_<?=$Order->order2out_id?>">
								<?foreach ($statuses as $key=>$val):?>
								<option value='<?=$key?>' <?if ($key==$Order->order2out_status):?>selected="selected"<?endif;?>><?=$val?></option>
								<?endforeach;?>	
							</select>
						</td>
						<td><? if ($Order->comment_for_admin) : ?>
							Добавлен новый комментарий<br />
							<? endif; ?><a href="<?=$selfurl?>showO2oComments/<?=$Order->order2out_id?>">Посмотреть</a>
						</td>
						<td><?if ($Order->order2out_status == 'processing'):?><a class="delete" href='<?=$selfurl?>deleteOrder2out/<?=$Order->order2out_id?>'><img border="0" src="/static/images/delete.png" title="Удалить"></a><?endif;?></td>
					</tr>
					<?endforeach;?>	

					<tr class='last-row'>
						<td colspan='9'>
							<div class='float'>	
								<div class='submit'><div><input type='submit' value='Сохранить' /></div></div>
							</div>
						</td>
					</tr>
				<?else:?>
					<tr>
						<td  colspan='7'>Заявок нет</td>
					</tr>
				<?endif;?>
			</table>
		</div>
	</form>
	<?php if (isset($pager)) echo $pager ?>