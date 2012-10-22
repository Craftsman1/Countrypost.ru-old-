	<a name="pagerScroll"></a>
	<form id="packagesForm" class='admin-inside' action='<?=$selfurl?>updateNewPackagesStatus' method="post">
		<ul class='tabs'>
			<li><div><a href='<?=$selfurl?>showAddPackage'>Добавить посылку</a></div></li>
			<li class='active'><div><a href='<?=$selfurl?>showNewPackages'>Новые</a></div></li>
			<li><div><a href='<?=$selfurl?>showPayedPackages'>Оплаченные</a></div></li>
			<li><div><a href='<?=$selfurl?>showSentPackages'>Отправленные</a></div></li>
			<li><div><a href='<?=$selfurl?>showOpenOrders'>Заказы “Помощь в покупке”</a></div></li>
			<li><div><a href='<?=$selfurl?>showPayedOrders'>Оплаченные заказы</a></div></li>
			<li><div><a href='<?=$selfurl?>showSentOrders'>Закрытые заказы</a></div></li>
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
					<th>Номер клиента</th>
					<th>Номер посылки</th>
					<th>ФИО / адрес доставки</th>
					<th>Способ доставки</th>
					<th>Цена доставки</th>
					<th>Статус</th>
					<th>Декларация</th>
					<th>Удалить</th>
				</tr>
				<? if ($packages) : foreach($packages as $package) : ?>
				<tr>
					<td><b>№ <?=$package->package_client?></b></td>
					<td>
						<b>№ <?=$package->package_id?></b>
						<br />
						<input class="package_weight" type="text" id="package_weight<?=$package->package_id?>" name="package_weight<?=$package->package_id?>" value="<?=$package->package_weight?>">&nbsp;кг
						<br /><nobr<? if (intval($package->package_day) > 6) : ?> class="red-color"<? endif; ?>><?=$package->package_date?></nobr>
						<?=$package->package_day == 0 ? "" : 'Прошло:<br />'.$package->package_day.' '.humanForm((int)$package->package_day, "день", "дня", "дней")?> <?=$package->package_hour == 0 ? "" : $package->package_hour.' '.humanForm((int)$package->package_hour, "час", "часа", "часов")?>
					</td>
					<td>
						<?=nl2br($package->package_address)?>
						<br />
						<a href="<?=$selfurl?>editPackageAddress/<?=$package->package_id?>">Изменить</a>
						<br />
						<? if (empty($package->order_id)) : ?>
						<?php if(!empty($package->package_trackingno)):?><b>tracking: </b><?=$package->package_trackingno;?><br/><?php endif;?>
						<? else :?>
						<a href="<?= $selfurl.'showOrderDetails/'.$package->order_id ?>" ><b>Заказ <?= $package->order_id ?></b></a>
						<? endif; ?>
						<br/>
					</td>
					<td><?=$package->package_delivery_name ?></td>
					<td><? if (!$package->package_delivery_cost || $package->declaration_status == 'not_completed') : ?><? else : ?>
						$<?=$package->package_manager_cost?>
							<a href="javascript:void(0)" onclick="$('#pre_<?=$package->package_id?>').toggle()">Подробнее</a>
							<pre class="pre-href" id="pre_<?= $package->package_id ?>">
								<nobr>
									$<?= $package->package_delivery_cost ?><!-- стоимость доставки -->
									<img class="tooltip_package_delivery" src="/static/images/mini_help.gif" />
								</nobr>
								+
								<nobr>
									$<?= $package->package_manager_comission ?><!-- комиссия партнера -->
									<img class="tooltip_package_comission" src="/static/images/mini_help.gif" />
								</nobr>
								<? if ($manager->package_foto_tax AND $package->package_foto_cost) : ?>
								+
								<nobr>
									$<?= $package->package_foto_cost ?><!-- комиссия партнера за запросы фото от клиента -->
									<img class="tooltip_package_foto" src="/static/images/mini_help.gif" />
								</nobr>
								<? endif; ?>
								<? if ($manager->package_foto_system_tax AND $package->package_foto_cost_system) : ?>
								+
								<nobr>
									$<?= $package->package_foto_cost_system ?><!-- комиссия партнера за фото от партнера/админа -->
									<img class="tooltip_package_foto_system" src="/static/images/mini_help.gif" />
								</nobr>
								<? endif; ?>
								<? if ($package->package_special_cost_usd) : ?>
								+
								<nobr>
									$<?= $package->package_special_cost_usd ?><!-- комиссия за доп.услуги -->
									<img class="tooltip_package_special_cost" src="/static/images/mini_help.gif" />
								</nobr>
								<? endif; ?>
							</pre>
						<? endif; ?>
					</td>
					<td nowrap>
						<?= $package_statuses[$package->package_status] ?>
					</td>
					<td nowrap><? if ($package->declaration_status == 'completed') : ?>
						Заполнена <br />
						<a href="<?=$selfurl?>showDeclaration/<?=$package->package_id?>">Посмотреть</a><br />
						<input type="checkbox" id="help<?=$package->package_id?>" name="help<?=$package->package_id?>" <? if ($package->declaration_status == 'help') : ?>checked<? endif; ?>>
					<? elseif ($package->declaration_status == 'help') : ?>
						Помощь<br />
						<a href="<?=$selfurl?>showDeclaration/<?=$package->package_id?>">Заполнить самостоятельно</a><br />
						 <input type="checkbox" id="help<?=$package->package_id?>" name="help<?=$package->package_id?>" <? if ($package->declaration_status == 'help') : ?>checked<? endif; ?>>
					<? else : ?>
						Не заполнена
					<? endif; ?></td>
					<td align="center">
						<a href="<?=$selfurl?>showPackageDetails/<?=$package->package_id?>">Подробнее</a>
						<? if ($package->comment_for_manager) : ?>
						<br />
						Добавлен новый комментарий
						<? endif; ?>
						<hr />
						<a class='delete' href="javascript:deleteItem('<?=$package->package_id?>');"><img title="Удалить" border="0" src="/static/images/delete.png"></a>
					</td>
				</tr>
				<?endforeach; endif;?>
				<tr class='last-row'>
					<td colspan='10'>
						<div class='float'>	
							<div class='submit'><div><input type='button' onclick="updateWeight();" value='Сохранить' /></div></div>
						</div>
						<div class='float' style="margin-top:11px;margin-right:10px;">
							<label for="declaration_status">Выбрать статус декларации:</label>
							<select id="declaration_status" name="declaration_status" onchange="javascript:updateStatus();">
								<option value="-1">выбрать...</option>
								<option value="not_completed">Не заполнена</option>
							</select>
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
				</tr>
			</table>
		</div>
	</form>
	<? if (isset($pager)) echo $pager ?>
<script>
	$(function() {
		put_package_hints();
	});
</script>