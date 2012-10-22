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
					<th>Сумма ($)</th>
					<th>Статус</th>
					<th>Комментарий</th>
				</tr>
				<?foreach ($Orders as $Order):?>
				<tr>
					<td>#<?=$Order->order2out_id?>&nbsp;&nbsp;(<?=date('H:i d-m-Y',strtotime($Order->order2out_time))?>)</td>
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