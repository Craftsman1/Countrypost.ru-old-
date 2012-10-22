<a name="pagerScroll"></a>
<script>
	$(document).ready(function(){
		$("img.tooltip").easyTooltip();
		$("img.tooltip_join").easyTooltip({
			tooltipId: "tooltip_id",
			content: '<div class="box"><div>Объединение в одну посылку</div><p>За каждое нажатие кнопки <i>Объединить</i><br /> с вашего счета снимается 3$,<br />поэтому выбирайте все посылки сразу,<br />которые хотите объединить</p></div>'
		});
	});
</script>
	<form class='admin-inside' id="pagerForm" action="<?=$selfurl?>joinPackages" method="POST">
		<?View::show($viewpath.'elements/div_float_preview_package');?>
		
		<ul class='tabs'>
			<li class='active'><div><a href='<?=$selfurl?>showOpenPackages'>Ожидающие отправки</a></div></li>
			<li><div><a href='<?=$selfurl?>showSentPackages'>Отправленные</a></div></li>
			<li><div><a href='<?=$selfurl?>showOpenOrders'>Заказы “Помощь в покупке”</a></div></li>
			<li><div><a href="<?=$selfurl?>showSentOrders">Закрытые заказы</a></div></li>
		</ul>
		
		<div class='table'>
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<table>
				<col width='200' />
				<col width='auto' />
				<col width='300' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<tr class='last-row'>
					<td colspan='9'>
						<div style="margin:0 0 10px 0;">
							<label>Посылок на странице:</label>
							<select class="per_page" name="per_page" onchange="javascript:updatePerPage(this);">
								<option value="10" <?= $per_page == 10 ? 'selected' : ''?>>10</option>
								<option value="50" <?= $per_page == 50 ? 'selected' : ''?>>50</option>
								<option value="100" <?= $per_page == 100 ? 'selected' : ''?>>100</option>
								<option value="200" <?= $per_page == 200 ? 'selected' : ''?>>200</option>
								<option value="350" <?= $per_page == 350 ? 'selected' : ''?>>350</option>
								<option value="500" <?= $per_page == 500 ? 'selected' : ''?>>500</option>
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<th>№ посылки / Страна</th>
					<th>Выберите способ<br />доставки</th>
					<th>ФИО / Адрес доставки</th>
					<th>Декларация</th>					
					<th>Цена доставки</th>
					<th>Фото</th>
					<th>Объединить <img class="tooltip tooltip_join" src="/static/images/mini_help.gif" /> ***</th>
					<th>Статус</th>
					<th>Подробнее / Удалить</th>
				</tr>

				<?if ($packages) : foreach($packages as $package) : ?>
				<tr>
					<td><b>№ <?=$package->package_id?></b>
						<?=$package->package_join_ids?@ereg_replace("([0-9]+)","<a href=/client/showPackageDetails/\\1>\\1</a>",'{'.str_replace('+', ' + ', $package->package_join_ids).'}'):''?>
						<br/><b><?=Func::round2half($package->package_weight)?> кг</b> <?=Func::round2half($package->package_weight) != $package->package_weight ? '('.$package->package_weight.' кг)' : '';?>
						<br/><?=$package->package_manager_country?>
						<br/><?=$package->package_date?>						
					</td>
					<td><? if ($package->package_status == 'payed') : echo($package->package_delivery_name); else : ?>
						<select id="delivery<?=$package->package_id?>" onchange="javascript:updateDelivery('<?=$package->package_id?>');" >
							<option value="0">выбрать...</option>
							<? if ($package->delivery_list) : foreach($package->delivery_list as $delivery) : ?>
							<option value="<?=$delivery->delivery_id?>" <? if ($package->package_delivery == $delivery->delivery_id) : ?>selected="selected"<? endif; ?>><?=$delivery->delivery_name?></option>
							<? endforeach; endif;?>
						</select>
						<? endif; ?></td>
					<td><?=nl2br($package->package_address.($package->package_address!="Адрес не заполнен!"?"":" (<a href=/user/showProfile>заполнить&hellip;</a>)"))?>
						<? if ($package->package_status != 'sent' && $package->package_status != 'payed') : ?>
						<br />
						<a href="<?=$selfurl?>editPackageAddress/<?=$package->package_id?>">Изменить</a>
						<? endif; ?>
						<br /><br />
						<? if (empty($package->order_id)) : ?>
						<b>самостоятельный заказ</b>
						<? else :?>
						<a href="<?= $selfurl.'showOrderDetails/'.$package->order_id ?>" ><b>Заказ <?= $package->order_id ?></b></a>
						<? endif; ?>
					</td>
					<td><? if ($package->declaration_status == 'not_completed') : ?>
						Не заполнена
						<a href="<?=$selfurl?>showDeclaration/<?=$package->package_id?>">Заполнить</a>
					<? elseif ($package->declaration_status == 'completed' ||
								($package->declaration_status == 'help') && $package->package_declaration_cost) : ?>
						Заполнена<br />
						<a href="<?=$selfurl?>showDeclaration/<?=$package->package_id?>">Посмотреть</a>
					<? else : ?>
						Помощь в заполнении<br />
						<a href="<?=$selfurl?>showDeclaration/<?=$package->package_id?>">Посмотреть</a>
					<? endif; ?></td>
					
					
					<td><? if (!$package->package_delivery_cost) : ?>Выберите способ доставки
						<? elseif ($package->declaration_status == 'not_completed') : ?>Заполните декларацию<? else : ?>
						<?=$package->package_cost?>$
							<a href="javascript:void(0)" onclick="$('#pre_<?=$package->package_id?>').toggle()">Подробнее</a>
							<pre class="pre-href" id="pre_<?=$package->package_id?>">
								<?= $package->package_delivery_cost ?>$
								+
								*<?= $package->package_comission - 
									$package->package_declaration_cost - 
									$package->package_join_cost - 
									$package->package_insurance_comission?>$
								<? if ($package->package_declaration_cost) : ?>
								+
								**<?= $package->package_declaration_cost ?>$
								<? endif; ?>
								<? if ($package->package_join_cost) : ?>
								+
								***<?= $package->package_join_cost ?>$
								<? endif;?>
								<? if ($package->package_insurance) : ?>
								+
								****<?= $package->package_insurance_comission ?>$
								<? endif;?>
								</pre>
							<? endif; ?>
					</td>
					<td>
						<? if (isset($packFotos[$package->package_id])): ?>
							<a href="javascript:void(0)" onclick="setRel(<?=$package->package_id?>)">
								Посмотреть <?=count($packFotos[$package->package_id]);?> фото
								<?foreach ($packFotos[$package->package_id] as $packFoto):?>
									<a rel="lightbox_<?=$package->package_id?>" href="/client/showPackageFoto/<?=$package->package_id?>/<?=$packFoto?>" style="display:none;">Посмотреть</a>
								<?endforeach;?>
							</a>
						<? endif; ?>
					</td>
					<td>
						<? if ($package->package_status != 'sent' && $package->package_status != 'payed') : ?>
							<input type="checkbox" id="join<?=$package->package_id?>" name="join<?=$package->package_id?>" />
						<? endif; ?>
					</td>
					<td><? if ($package->package_status == 'processing') : ?>Обрабатывается
						<? elseif ($package->package_status == 'not_delivered') : ?>Ждем прибытия
						<? elseif ($package->package_status == 'not_available') : ?>Нет в наличии
						<? elseif ($package->package_status == 'not_available_color') : ?>Нет данного цвета
						<? elseif ($package->package_status == 'not_available_size') : ?>Нет данного размера
						<? elseif ($package->package_status == 'not_available_count') : ?>Нет указанного кол-ва
						<? elseif ($package->package_status == 'not_payed') : ?>Не оплачен
						<? elseif ($package->package_status == 'payed') : ?>Оплачен<? endif; ?>
						<? if ($package->package_status == 'not_payed' && 
								$package->declaration_status != 'not_completed' &&
								$package->package_delivery_cost) : ?>
						<div class='float'>	
							<div class='submit' style="margin-right:0;">
								<div>
									<input type='button' onclick="payItem('<?=$package->package_id?>');" value='Оплатить' />
								</div>
							</div>
						</div>
						<? endif; ?>
					
						<? if ($package->package_status == 'payed' && $package->package_cost > $package->package_cost_payed) : ?><br />$<?=$package->package_cost_payed?>
						<div class='float'>	
							<div class='submit' style="margin-right:0;">
								<div>
									<input type='button' onclick="repayItem('<?=$package->package_id?>');" value='Доплатить $<?=$package->package_cost - $package->package_cost_payed?>' />
								</div>
							</div>
						</div>
						<? endif; ?>
					</td>
					<td>
						<a href="<?=$selfurl?>showPackageDetails/<?=$package->package_id?>">Подробнее</a>
						<? if ($package->comment_for_client) : ?>
						Добавлен новый комментарий<br />
						<? endif; ?>
						<a href="<?=$selfurl?>deletePackage/<?=$package->package_id?>">Удалить</a><br />
								
					</td>
				</tr>
				<?endforeach; endif;?>
				<tr class='last-row'>
					<td colspan='10'>
						<br />
						<div id="tableComments" style="text-align:left;float:left;">
							* оплата услуг за пересылку<br />
							** помощь в заполнении декларации<br />
							*** за каждое нажатие кнопки Объединить с вашего счета снимается 3$,<br />
							поэтому выбирайте все посылки сразу, которые хотите объединить<br />
							**** стоимость страховки
						</div>
						<div class='float'>	
							<div class='submit'><div><input type='submit' value='Объединить' /></div></div>
						</div>
						<div class='float' style="margin-top:11px;margin-right:10px;">
							<label>Посылок на странице:</label>
							<select class="per_page" name="per_page" onchange="javascript:updatePerPage(this);">
								<option value="10" <?= $per_page == 10 ? 'selected' : ''?>>10</option>
								<option value="50" <?= $per_page == 50 ? 'selected' : ''?>>50</option>
								<option value="100" <?= $per_page == 100 ? 'selected' : ''?>>100</option>
								<option value="200" <?= $per_page == 200 ? 'selected' : ''?>>200</option>
								<option value="350" <?= $per_page == 350 ? 'selected' : ''?>>350</option>
								<option value="500" <?= $per_page == 500 ? 'selected' : ''?>>500</option>
							</select>
						</div>
					</td>
					<td>
					</td>
				</tr>
			</table>
		</div>
	</form>

	<?php if (isset($pager)) echo $pager ?>