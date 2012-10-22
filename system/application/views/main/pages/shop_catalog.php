		<div class='content'>
			<h2>Каталог магазинов</h2>
			<?if (isset($is_added)):?>
			<em class="order_result">Магазин успешно добавлен.</em>
			<br />
			<? elseif (Stack::last('shop_deleted')):
				$result = Stack::shift('shop_deleted'); ?>
			<em class="order_result" <?= isset($result->e) && $result->e == 1 ? 'style="color:green;"' : 'style="color:green;"' ?>><?=$result->m?></em>
			<br />
			<?endif;?>
					<?if ($is_authorized):?>
						<div style='float:left;'>	
							<div class='submit'><div><input type='submit' style="width:150px;" value='Добавить новый магазин' onclick="window.location = '<?=BASEURL?>main/showAddShop'" /></div></div>
						</div>
						<br /><br /><br />
						<?endif;?>
				
			<div class='table'>
					<div class='angle angle-lt'></div>
					<div class='angle angle-rt'></div>
					<div class='angle angle-lb'></div>
					<div class='angle angle-rb'></div>
					<style>
						.shop_catalog h3
						{
							margin-top:0;
						}
						.shop_catalog td
						{
							border:0;
							width:33%;
						}
					</style>
			<? $all = count($Categories); if ($all):?>
				<table class="shop_catalog">
					<tr>
					<? $i = 0; foreach ($Categories as $Category):?>
					<? $i++; ?>
						<td>
							<h4 style="font-size:1.4em;">
								<a href='<?=BASEURL?>main/showCategory/<?=$Category->scategory_id?>'><?=$Category->scategory_name?></a>
								(<?=$Category->count?>)
							</h4>
							<?=$Category->scategory_details?>
							<br /><br />
						</td>
					<?if ($i != $all && $i%3==0):?>
					</tr>
					<tr>
					<?elseif ($i == $all):?>
					</tr>
					<?endif;?>
					<? endforeach; ?>
				</table>
			</div>
			<?endif;?>
		</div>