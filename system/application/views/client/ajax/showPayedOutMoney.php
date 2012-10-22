	<a name="pagerScroll"></a>
	<form id="pagerForm" class='admin-inside' action="<?=$selfurl?>order2out" method='POST'>
		<ul class='tabs'>
			<li><div><a href='<?=$selfurl?>showOutMoney'>Новые заявки</a></div></li>
			<li class='active'><div><a href='<?=$selfurl?>showPayedOutMoney'>Выполненные заявки</a></div></li>
		</ul>
		<div class='table'>
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<table>
				<tr>
					<th>№ заявки</th>
					<th>Способ вывода</th>
					<th>Сумма ($)</th>
					<th>Сумма (руб.)</th>
					<th>Статус</th>
					<th>Комментарий</th>
				</tr>
				<?foreach ($Orders as $Order):?>
				<tr>
					<td>#<?=$Order->order2out_id?>&nbsp;&nbsp;(<?=date('H:i d-m-Y',strtotime($Order->order2out_time))?>)</td>
					<td><? foreach ($services as $service) : 
						if ($service->payment_service_id == $Order->order2out_payment_service) : 
							echo $service->payment_service_name;
						break; endif; endforeach; ?>
					</td>
					<td>$<?=$Order->order2out_ammount?></td>
					<td><?=$Order->order2out_ammount_rur?> руб.</td>
					<td><?=$statuses[$Order->order2out_status]?></td>
					<td><? if ($Order->comment_for_client) : ?>
							Добавлен новый комментарий<br />
						<? endif; ?><a href="<?=$selfurl?>showO2oComments/<?=$Order->order2out_id?>">Посмотреть</a>
					</td>
				</tr>
				<?endforeach;?>
			</table>
		</div>
	</form>
	<?php if (isset($pager)) echo $pager ?>