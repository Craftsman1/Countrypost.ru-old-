	<form id="pagerForm" class='admin-inside' action='#'>
		
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
				<tr>
					<th><a href='#' onclick="goto_page('/main/showCategory/<?=$category->scategory_id?>/id/0/ajax');return false;">№</a></th>
					<th><a href='#' onclick="goto_page('/main/showCategory/<?=$category->scategory_id?>/name/0/ajax');return false;">Магазин</a></th>
					<th><a href='#' onclick="goto_page('/main/showCategory/<?=$category->scategory_id?>/country/0/ajax');return false;">Страна</a></th>
					<th>Описание</th>
					<th><a href='#' onclick="goto_page('/main/showCategory/<?=$category->scategory_id?>/comments/0/ajax');return false;">Отзывы</a></th>
					<? if ($is_admin) : ?>
					<th>Посмотреть / Удалить</th>
					<? endif; ?>
				</tr>
				<?if ($shops):?>
					<?foreach ($shops as $shop):?>
					<tr>
						<td><?=$shop->shop_id?></td>
						<td><a href='<?=$shop->shop_name?>' target='_blank'><?=$shop->shop_name?></a>
						<td><?=$countries[$shop->shop_country]?></td>
						<td><?=$shop->shop_desc?></td>
						<td><a href='<?=BASEURL?>main/showShop/<?=$shop->shop_id?>'>
							<? if ($shop->count) : ?>Посмотреть (<?=$shop->count?>)
							<? elseif ($is_authorized) : ?>Добавить
							<? endif; ?>
							</a>
							<? if (!$shop->count && !$is_authorized) : ?>Отзывов нет<? endif; ?>							
						</td>
						<? if ($is_admin) : ?>
						<td align="center">
							<a href="<?=$selfurl?>showEditShop/<?=$shop->shop_id?>">Посмотреть</a><br/>
							<hr />
							<a href="javascript:deleteItem('<?=$shop->shop_id?>');"><img title="Удалить" border="0" src="/static/images/delete.png"></a>
							<br/>
						</td>
						<? endif; ?>
					</tr>
					<?endforeach;?>	
				<?else:?>
					<tr>
						<td colspan="5">Магазинов в этой категории пока нет.</td>
					</tr>
				<?endif;?>
			</table>
		</div>
	</form>
	<?php if (isset($pager)) echo $pager ?>