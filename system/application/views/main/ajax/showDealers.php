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
			<col width='auto' />
			<col width='auto' />
			<col width='auto' />
			<col width='auto' />
			<tr>
				<th>Рейтинг / №</th>
				<th>Страна</th>
				<th>Посредник</th>
				<th>Отзывы</th>
				<th>Сайт</th>
				<th>Выполненных&nbsp;заказов</th>
				<th>Профиль</th>
			</tr>
			<style>
				#partnersForm td,#partnersForm th
				{
					text-align:center;
					vertical-align:middle;
					text-wrap: nowrap;
				}
			</style>
			<?if ($managers): foreach ($managers as $manager):?>
				<tr>
					<td>
						<b style=""><?=$manager->rating ?></b>
						<br>
						<b style="color:#D7D7D7;">№ <?=$manager->manager_user?></b>
					</td>
					<td>
						<img src="/static/images/flags/big/<?= $countries_en[$manager->manager_country] ?>.png" style="float:left;margin-right:10px;" />
						<b style="position:relative;top:17px;"><?=$countries[$manager->manager_country]?></b>
					</td>
					<td style="text-align:left;">
						<?=$manager->statistics->fullname?>
						<br>
						<b style="color: orange;">100% CASHBACK</b>
						<b style="color: #BF0090;">MF</b>
					</td>
					<td>
						<? View::show('main/elements/dealers/rating', array('manager' => $manager)); ?>
					</td>
					<td>
						<a target="_blank" href="<?= empty($manager->website) ? BASEURL."/dealers/profile/{$manager->manager_user}" : $manager->website ?>"><?= empty($manager->website) ? BASEURL."/dealers/profile/{$manager->manager_user}" : $manager->website ?></a>
					</td>
					<td><?=$manager->statistics->completed_orders?></td>
					<td>
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