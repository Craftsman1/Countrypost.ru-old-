			<a name="pagerScroll"></a>
			<form class='admin-inside' id="packagesForm" action="<?=$selfurl?>updateNewPackagesStatus" method="POST">
				<ul class='tabs'>
					<li class='active'><div><a href='<?=$selfurl?>showNewPackages'>Новые</a></div></li>
					<li><div><a href='<?=$selfurl?>showPayedPackages'>Оплаченные</a></div></li>
					<li><div><a href='<?=$selfurl?>showSentPackages'>Отправленные</a></div></li>
					<li><div><a href='<?=$selfurl?>showOpenOrders'>Заказы “Помощь в покупке”</a></div></li>
					<li><div><a href='<?=$selfurl?>showPayedOrders'>Оплаченные заказы</a></div></li>
					<li><div><a href='<?=$selfurl?>showSentOrders'>Закрытые заказы</a></div></li>
					<li><div><a href='<?=$selfurl?>showClients'>Клиенты</a></div></li>
					<li><div><a href='<?=$selfurl?>showPartners'>Партнеры</a></div></li>
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
							<th>Номер посылки</th>
							<th>Партнер, страна</th>
							<th>ФИО / Адрес доставки</th>
							<th>Способ доставки</th>
							<th>Цена доставки</th>
							<th>Декларация / Страховка</th>
							<th>Статус</th>
						</tr>
						<?if ($packages) : foreach($packages as $package) : ?>
						<tr>
							<td>
								<b>№ <?=$package->package_id?></b>
								<br />
								<nobr<? if (intval($package->package_day) > 6) : ?> class="red-color"<? endif; ?>>
									<?=$package->package_date?>
								</nobr>
								<br />
								<input class="package_weight" type="text" id="package_weight<?=$package->package_id?>" name="package_weight<?=$package->package_id?>" value="<?=$package->package_weight?>">&nbsp;кг<br />
								Прошло:<br />
								<?=$package->package_day == 0 ? "" : $package->package_day.' '.humanForm((int)$package->package_day, "день", "дня", "дней")?> <?=$package->package_hour == 0 ? "" : $package->package_hour.' '.humanForm((int)$package->package_hour, "час", "часа", "часов")?>
							</td>
							<td><?=$package->package_manager_login?>, <?=$package->package_manager_country?></td>
							<td>
								<b>№ <?=$package->package_client?></b>&nbsp;<?=$package->package_address.($package->package_address!="Адрес не заполнен!"?"":" (<a href=/admin/editClient/{$package->package_client}>заполнить&hellip;</a>)")?> <a href='<?=$selfurl?>editPackageAddress/<?=$package->package_id?>'>Подробнее</a>
								<br /><br />
								<? if ($package->package_status == 'not_delivered') : ?>
								<b>Жду посылку!</b>
								<? else :?>
									<? if (empty($package->order_id)) : ?>
									<b>самостоятельный заказ</b>
									<? else :?>
									<a href="<?= $selfurl.'showOrderDetails/'.$package->order_id ?>" ><b>Заказ <?= $package->order_id ?></b></a>
									<? endif; ?>
								<? endif; ?>
							</td>
							<td><?=$package->package_delivery_name ?></td>
							<td><? if (!$package->package_delivery_cost || $package->declaration_status == 'not_completed') : ?><? else : ?>
								<?=$package->package_cost?>$
								<a href="javascript:void(0)" onclick="$('#pre_<?=$package->package_id?>').toggle()">Подробнее</a>
								<pre class="pre-href" id="pre_<?= $package->package_id ?>">
									<nobr>
										$<?= $package->package_delivery_cost ?><!-- стоимость доставки -->
										<img class="tooltip_package_delivery" src="/static/images/mini_help.gif" />
									</nobr>
									+
									<nobr>
										$<?= $package->package_comission ?><!-- комиссия за пересылку -->
										<img class="tooltip_package_comission" src="/static/images/mini_help.gif" />
									</nobr>
									<? if ($package->package_join_cost) : ?>
									+
									<nobr>
										$<?= $package->package_join_cost ?><!-- комиссия за объединение посылок -->
										<img class="tooltip_package_join" src="/static/images/mini_help.gif" />
									</nobr>
									<? endif; ?>
									<? if ($package->package_declaration_cost) : ?>
									+
									<nobr>
										$<?= $package->package_declaration_cost ?><!-- комиссия за декларацию -->
										<img class="tooltip_package_declaration" src="/static/images/mini_help.gif" />
									</nobr>
									<? endif; ?>
									<? if ($package->package_insurance_comission) : ?>
									+
									<nobr>
										$<?= $package->package_insurance_comission ?><!-- комиссия за страховку -->
										<img class="tooltip_package_insurance" src="/static/images/mini_help.gif" />
									</nobr>
									<? endif; ?>
									<? if ($package->package_foto_cost) : ?>
									+
									<nobr>
										$<?= $package->package_foto_cost ?><!-- комиссия за запросы фото от клиента -->
										<img class="tooltip_package_foto" src="/static/images/mini_help.gif" />
									</nobr>
									<? endif; ?>
									<? if ($package->package_foto_cost_system) : ?>
									+
									<nobr>
										$<?= $package->package_foto_cost_system ?><!-- комиссия за фото от партнера/админа -->
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
							<td><? if ($package->declaration_status == 'not_completed') : ?>
										Не заполнена
									<? elseif ($package->declaration_status == 'completed') : ?>
										Заполнена
									<? else : ?>
										Помощь партнера
									<? endif; ?>
									<br />
									<input type="checkbox" id="help<?=$package->package_id?>" name="help<?=$package->package_id?>">
									<? if ($package->declaration_status == 'not_completed') : ?>
										<a href="<?=$selfurl?>showDeclaration/<?=$package->package_id?>">Заполнить</a>
									<? else : ?>
										<a href="<?=$selfurl?>showDeclaration/<?=$package->package_id?>">Посмотреть</a>
									<? endif; ?>
							</td>
							<td align="center">
								<select name="package_status<?=$package->package_id?>">
									<? foreach ($package_statuses as $key => $value) : ?>
									<option value="<?= $key ?>"<?= ($key == $package->package_status) ? ' selected' : '' ?>>
										<?= $value ?>
									</option>
									<? endforeach; ?>
								</select>
								<br />
								<a href="<?=$selfurl?>showPackageDetails/<?=$package->package_id?>">Подробнее</a>
								<? if ($package->comment_for_client OR $package->comment_for_manager) : ?>
								<br />
								Добавлен новый комментарий
								<? endif; ?>
								<hr />
								<a href="javascript:deleteItem('<?=$package->package_id?>');" class='delete'><img title="Удалить" border="0" src="/static/images/delete.png"></a>
							</td>
						</tr>
						<?endforeach; endif;?>
						<tr class='last-row'>
							<td colspan='9'>
								<div class='float'>	
									<div class='submit'><div><input type='submit' value='Сохранить' /></div></div>
								</div>
								<div class='float' style="margin-top:11px;margin-right:10px;">	
									<label for="declaration_status">Выбрать статус декларации:</label>
									<select id="declaration_status" name="declaration_status" onchange="javascript:updateStatus();">
										<option value="-1">выбрать...</option>
										<option value="completed">Заполнена</option>
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