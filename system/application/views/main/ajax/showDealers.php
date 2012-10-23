<a name="pagerScroll"></a>
<form id="partnersForm" class='admin-inside' action='#'>
	<div class='table'>
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<table>
			<col width='auto' />
			<col width='auto' />
			<col width='auto' />
			<col width='200' />
			<col width='auto' />
			<col width='80' />
			<col width='auto' />
			<col width='auto' />
			<col width='auto' />
			<tr>
				<th>Рейтинг / №</th>
				<th>Страна</th>
				<th>Посредник</th>
				<th>Отзывы</th>
				<th>Сайт</th>
				<th>Выполненных заказов</th>
				<th></th>
			</tr>
			<?if ($managers): foreach ($managers as $manager):?>
				<tr>
					<td align='center'>
						<b><?=$manager->rating ?></b>
						<br>
						<b>№ <?=$manager->manager_user?></b>
					</td>
					<td>
						<img src="/static/images/flags/<?= $countries_en[$manager->manager_country] ?>.png" style="float:left;margin-right:10px;" />
						<b style="position:relative;top:5px;"><?=$countries[$manager->manager_country]?></b>
					</td>
					<td><?=$manager->statistics->fullname?></td>
					<td>
						<? View::show('main/elements/dealers/rating', array('manager' => $manager)); ?>
					</td>
					<td><?=$manager->website?></td>
					<td><?=$manager->statistics->completed_orders?></td>
					<td align="center">
						<a href='/dealers/profile/<?=$manager->manager_user?>'>посмотреть</a>
					</td>
				</tr>
				<?endforeach;?>	
			<?else:?>
				<tr>
					<td colspan=9>Партнеров нет!</td>
				</tr>
			<?endif;?>
			<tr class='last-row'>
				<td colspan='9'>
					<div class='float'>&nbsp;
					</div>
				</td>
				<td></td>
			</tr>
		</table>
	</div>
</form>
<?php if (isset($pager)) echo $pager ?>