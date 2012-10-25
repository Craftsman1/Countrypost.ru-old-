<form id="partnersForm" class='admin-inside' action='#'>
	<a name="pagerScroll"></a>
	<div class="search_results">
		<span class="total" style="float: none;">
			Найдено посредников: <b id="managers_count"><?= $this->paging_count ?></b>
		</span>
		<span class="total" style="margin:0 0 0px 0;">
			<label>посредников на странице:</label>
			<select class="per_page" name="per_page" onchange="javascript:updatePerPage(this, 'dealers');">
				<option value="10" <?= $per_page == 10 ? 'selected' : ''?>>10</option>
				<option value="50" <?= $per_page == 50 ? 'selected' : ''?>>50</option>
				<option value="100" <?= $per_page == 100 ? 'selected' : ''?>>100</option>
				<option value="200" <?= $per_page == 200 ? 'selected' : ''?>>200</option>
				<option value="350" <?= $per_page == 350 ? 'selected' : ''?>>350</option>
				<option value="500" <?= $per_page == 500 ? 'selected' : ''?>>500</option>
			</select>
		</span>
	</div>
	<br>
	<br>
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
						<b class="cashback">100% CASHBACK</b>
						<b class="mf">MF</b>
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
	<div class="search_results">
		<span class="total" style="float: none;">
			Найдено посредников: <b id="managers_count"><?= $this->paging_count ?></b>
		</span>
		<span class="total" style="margin:0;">
			<label>посредников на странице:</label>
			<select class="per_page" name="per_page" onchange="javascript:updatePerPage(this, 'dealers');">
				<option value="10" <?= $per_page == 10 ? 'selected' : ''?>>10</option>
				<option value="50" <?= $per_page == 50 ? 'selected' : ''?>>50</option>
				<option value="100" <?= $per_page == 100 ? 'selected' : ''?>>100</option>
				<option value="200" <?= $per_page == 200 ? 'selected' : ''?>>200</option>
				<option value="350" <?= $per_page == 350 ? 'selected' : ''?>>350</option>
				<option value="500" <?= $per_page == 500 ? 'selected' : ''?>>500</option>
			</select>
		</span>
	</div>
	<?php if (isset($pager)) echo $pager ?>
</form>