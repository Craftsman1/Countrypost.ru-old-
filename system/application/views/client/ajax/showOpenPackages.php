	<form class='admin-inside' id="pagerForm" action="<?= $selfurl ?>joinPackages" method="POST">
		<? View::show($viewpath.'elements/div_float_preview_package'); ?>
		<? View::show($viewpath.'elements/packages/tabs'); ?>
		<div class='table'>
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<table>
				<col width='20' />
				<col width='200' />
				<col width='auto' />
				<col width='300' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<tr class='last-row'>
					<td colspan='9'>
						<div style="margin:0 0 10px 0;height:22px;">
							<div class='floatleft'>
							<? View::show($viewpath.'elements/packages/status_links', array('selected_submenu' => 'new_packages')); ?>
							</div>
							<div class='floatright'>
								<? View::show($viewpath.'elements/packages/per_page'); ?>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<th>
						<input type="checkbox" id="join_all" />
					</th>
					<th>
						№ посылки / Страна
					</th>
					<th>Выберите способ<br />доставки</th>
					<th>ФИО / Адрес доставки</th>
					<th>Декларация</th>					
					<th>Цена доставки</th>
					<th>Статус</th>
					<th>Подробнее / Удалить</th>
				</tr>
				<?if ($packages) : foreach($packages as $package) : ?>
				<tr>
					<td>
						<input type="checkbox" id="join<?= $package->package_id ?>" name="join<?= $package->package_id ?>" class="join_check" />
					</td>
					<td>
						<b>№ <?= $package->package_id ?></b>
						<br/><b><?= Func::round2half($package->package_weight) ?> кг</b> <?= Func::round2half($package->package_weight) != $package->package_weight ? '('.$package->package_weight.' кг)' : ''; ?>
						<br/><?= $package->package_manager_country ?>
						<br/><?= $package->package_date ?>						
					</td>
					<td><? if ($package->package_status == 'payed') : echo($package->package_delivery_name); else : ?>
						<select id="delivery<?= $package->package_id ?>" onchange="javascript:updateDelivery('<?= $package->package_id ?>');" >
							<option value="0">выбрать...</option>
							<? if ($package->delivery_list) : foreach($package->delivery_list as $delivery) : ?>
							<option value="<?= $delivery->delivery_id ?>" <? if ($package->package_delivery == $delivery->delivery_id) : ?>selected="selected"<? endif; ?>><?= $delivery->delivery_name ?></option>
							<? endforeach; endif; ?>
						</select>
						<? endif; ?></td>
					<td><?= nl2br($package->package_address.($package->package_address!="Адрес не заполнен!"?"":" (<a href=/user/showProfile>заполнить&hellip;</a>)")) ?>
						<? if ($package->package_status != 'sent' && $package->package_status != 'payed') : ?>
						<br />
						<a href="<?= $selfurl ?>editPackageAddress/<?= $package->package_id ?>">Изменить</a>
						<? endif; ?>
						<br /><br />
						<? if (empty($package->order_id)) : ?>
						<b>Жду посылку!</b>
						<? else : ?>
						<a href="<?= $selfurl.'showOrderDetails/'.$package->order_id ?>" ><b>Заказ <?= $package->order_id ?></b></a>
						<? endif; ?>
					</td>
					<td><? if ($package->declaration_status == 'not_completed') : ?>
						Не заполнена
						<a href="<?= $selfurl ?>showDeclaration/<?= $package->package_id ?>">Заполнить</a>
					<? elseif ($package->declaration_status == 'completed' ||
								($package->declaration_status == 'help') && $package->package_declaration_cost) : ?>
						Заполнена<br />
						<a href="<?= $selfurl ?>showDeclaration/<?= $package->package_id ?>">Посмотреть</a>
					<? else : ?>
						Помощь в заполнении<br />
						<a href="<?= $selfurl ?>showDeclaration/<?= $package->package_id ?>">Посмотреть</a>
					<? endif; ?></td>
					<td><? if (!$package->package_delivery_cost) : ?>Выберите способ доставки
						<? elseif ($package->declaration_status == 'not_completed') : ?>Заполните декларацию<? else : ?>
						$<?= $package->package_cost ?>
						<a href="javascript:void(0)" onclick="$('#pre_<?= $package->package_id ?>').toggle()">Подробнее</a>
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
								<a href="<?= $selfurl ?>/removeInsurance/<?= $package->package_id ?>"><img class="tooltip_remove_package_insurance" src="/static/images/delete.png" /></a>
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
					<td nowrap>
						<?= $package_statuses[$package->package_status] ?>
						<? if ($package->package_status == 'not_payed' && 
								$package->declaration_status != 'not_completed' &&
								$package->package_delivery_cost) : ?>
						<div class='float'>	
							<div class='submit' style="margin-right:0;">
								<div>
									<input type='button' onclick="payItem('<?= $package->package_id ?>');" value='Оплатить' />
								</div>
							</div>
						</div>
						<? endif; ?>
						<? if ($package->package_status == 'payed' && $package->package_cost > $package->package_cost_payed) : ?><br />$<?= $package->package_cost_payed ?>
						<div class='float'>	
							<div class='submit' style="margin-right:0;">
								<div>
									<input type='button' onclick="repayItem('<?= $package->package_id ?>');" value='Доплатить $<?= $package->package_cost - $package->package_cost_payed ?>' />
								</div>
							</div>
						</div>
						<? endif; ?>
					</td>
					<td>
						<center>
							<a href="<?= $selfurl ?>showPackageDetails/<?= $package->package_id ?>">Подробнее</a>
							<br />
							<? if ($package->comment_for_client) : ?>
								Добавлен новый комментарий<br />
							<? endif; ?>
							<a href="<?=$selfurl?>showPackageDetails/<?=$package->package_id?>#comments">Комментарии</a>
							<hr />
							<a href="javascript:deleteItem('<?= $package->package_id ?>');" class='delete'>
								<img title="Удалить" border="0" src="/static/images/delete.png">
							</a>
						</center>
					</td>
				</tr>
				<?endforeach; endif; ?>
				<tr class='last-row'>
					<td colspan='10'>
						<div class='floatleft'>	
							<div class='submit'><div><input type='submit' value='Объединить' /></div></div>
						</div>
						<img class="tooltip_join" src="/static/images/mini_help.gif" />
						<div class='float' style="margin-top:11px;">
							<? View::show($viewpath.'elements/packages/per_page'); ?>
						</div>
					</td>
					<td>
					</td>
				</tr>
			</table>
		</div>
	</form>
	<?= $pager ?>
<script>
	$(function() {
		put_package_hints();
		$('#page_title').html('Посылки на складе');
	
		$("img.tooltip_join").easyTooltip({
			tooltipId: "tooltip_id",
			content: '<div class="box"><div>Объединение в одну посылку</div><p>За каждое нажатие кнопки <i>Объединить</i><br /> с вашего счета снимается 3$,<br />поэтому выбирайте все посылки сразу,<br />которые хотите объединить</p></div>'
		});
		
		$('input#join_all').change(function() {
			if ($(this).attr('checked'))
			{
				$('input.join_check').attr('checked', true);
			}
			else
			{
				$('input.join_check').removeAttr('checked');
			}
		});
	});
</script>