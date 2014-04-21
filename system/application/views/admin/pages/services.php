<div class='content'>
	<? View::show($viewpath.'elements/div_submenu'); ?>
	<h3>Изменение цен за услуги</h3>
	<br />
	<form class="admin-inside" id="servicesForm" action="<?=$selfurl?>saveServicesPrice" method="POST">
		<input type='hidden' id='country_id' name='country_id' value='' />
		<div>
			<div class='table' style="width:430px;float:left;">
				<div class='angle angle-lt'></div>
				<div class='angle angle-rt'></div>
				<div class='angle angle-lb'></div>
				<div class='angle angle-rb'></div>
				<center>
					Страна:
					<select id="country">
						<? foreach ($taxes as $tax) : ?>
						<option value="<?= $tax->country_id ?>">
							<?= $tax->country_name ?>
						</option>
						<?	 endforeach; ?>
					</select>
				</center>
				<br />
				<br />
				<table id="taxes">
					<col width="50%">
					<col width="50%">
					<? foreach ($taxes as $tax) : ?>
					<tr style="display:none;" class="tr<?= $tax->country_id ?>">
						<td><span>Цена за пересылку (посылка привязана к заказу):</span></td>
						<td><input type="text" name="package<?= $tax->country_id ?>" value="<?= $tax->package ?>">$</td>
					</tr>	
					<tr style="display:none;" class="tr<?= $tax->country_id ?>">
						<td><span>Цена за пересылку (самостоятельная посылка):</span></td>
						<td><input type="text" name="package_disconnected<?= $tax->country_id ?>" value="<?= $tax->package_disconnected ?>">$</td>
					</tr>	
					<tr style="display:none;" class="tr<?= $tax->country_id ?>">
						<td>Цена за помощь в заказе:</td>
						<td><input type="text" name="order<?= $tax->country_id ?>" value="<?= $tax->order ?>">%</td>
					</tr>		
					<tr style="display:none;" class="tr<?= $tax->country_id ?>">
						<td>Цена за заполнение декларации:</td>
						<td><input type="text" name="package_declaration<?= $tax->country_id ?>" value="<?= $tax->package_declaration ?>">$</td>
					</tr>
					<tr style="display:none;" class="tr<?= $tax->country_id ?>">
						<td>Цена за объединение посылок:</td>
						<td><input type="text" name="package_joint<?= $tax->country_id ?>" value="<?= $tax->package_joint ?>">$</td>
					</tr>
					<tr style="display:none;" class="tr<?= $tax->country_id ?>">
						<td>Цена за страховку:</td>
						<td><input type="text" name="package_insurance<?= $tax->country_id ?>" value="<?= $tax->package_insurance ?>">%</td>
					</tr>
					<tr style="display:none;" class="tr<?= $tax->country_id ?>">
						<td>Минимальная комиссия заказа:</td>
						<td><input type="text" name="min_order<?= $tax->country_id ?>" value="<?= $tax->min_order ?>">$</td>
					</tr>
					<tr style="display:none;" class="tr<?= $tax->country_id ?>">
						<td>Максимальная сумма страховки:</td>
						<td><input type="text" name="max_package_insurance<?= $tax->country_id ?>" value="<?= $tax->max_package_insurance ?>">$</td>
					</tr>
					<tr style="display:none;" class="tr<?= $tax->country_id ?>">
						<td>Фото в посылках (клиент):</td>
						<td><input type="text" name="package_foto<?= $tax->country_id ?>" value="<?= $tax->package_foto ?>">$</td>
					</tr>
					<tr style="display:none;" class="tr<?= $tax->country_id ?>">
						<td>Фото в посылках<br />(админ и партнеры):</td>
						<td><input type="text" name="package_foto_system<?= $tax->country_id ?>" value="<?= $tax->package_foto_system ?>">$</td>
					</tr>
					<? endforeach; ?>
					<tr class='last-row'>
						<td colspan='2'>
							<br />
							<div class='float'>	
								<div class='submit'>
									<div>
										<input type='submit' value='Сохранить' />
									</div>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div class='table' style="width:430px;float:right;">
				<div class='angle angle-lt'></div>
				<div class='angle angle-rt'></div>
				<div class='angle angle-lb'></div>
				<div class='angle angle-rb'></div>
				<table>
					<tr>
						<td>Минимальный курс доллара:</td>
						<td><input type="text" name="min_USD_rate" value="<?=$config['min_USD_rate']->config_value?>">$</td>
					</tr>
					<tr>
						<td>Минимальный курс евро:</td>
						<td><input type="text" name="min_EUR_rate" value="<?=$config['min_EUR_rate']->config_value?>">&euro;</td>
					</tr>
					<tr>
						<td>Минимальный курс тенге:</td>
						<td><input type="text" name="min_KZT_rate" value="<?=$config['min_KZT_rate']->config_value?>">&#x20B8;</td>
					</tr>
					<tr>
						<td>Минимальный курс гривны:</td>
						<td><input type="text" name="min_UAH_rate" value="<?=$config['min_UAH_rate']->config_value?>">&#8372;</td>
					</tr>
					<tr>
						<td>Минимальный курс юаня:</td>
						<td><input type="text" name="min_CNY_rate" value="<?=$config['min_CNY_rate']->config_value?>">&yen;</td>
					</tr>
					<tr>
						<td>Минимальный курс воны:</td>
						<td><input type="text" name="min_KRW_rate" value="<?=$config['min_KRW_rate']->config_value?>">&#x20A9;</td>
					</tr>
					<tr>
						<td>Минимальный курс лиры:</td>
						<td><input type="text" name="min_TRY_rate" value="<?=$config['min_TRY_rate']->config_value?>">₤</td>
					</tr>
					<tr>
						<td>Минимальный курс йены:</td>
						<td><input type="text" name="min_JPY_rate" value="<?=$config['min_JPY_rate']->config_value?>">&yen;</td>
					</tr>
					<tr class='last-row'>
						<td colspan='2'>
							<br />
							<div class='float'>	
								<div class='submit'><div><input type='submit' value='Сохранить' /></div></div>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<br style="clear:both;" />
		</div>
		<h3>Наши тарифы</h3>
		<div style="margin-top:20px;">
			<script type='text/javascript' src='/system/plugins/fckeditor/fckeditor.js'></script>
			<textarea id='country_pricelist' name='country_pricelist'>
				<?= isset($manager->manager_description) ? $manager->manager_description : '' ?>
			</textarea>
		</div>
		<div class='submit' style="margin:0;">
			<div>
				<input type='submit' value='Сохранить' />
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	var country_pricelist = <?= json_encode($country_pricelist) ?>;
	
	$(function() {
		$('select#country').change(function() {
			var selectedId = $('select#country').val();
			$('table#taxes tr').hide();
			$('table#taxes tr.last-row,table#taxes tr.tr' + selectedId).show();
		
			$('input#country_id').val(selectedId);
			var editor = FCKeditorAPI.GetInstance('country_pricelist') ;

			for (var i = 0; i < country_pricelist.length; i++)
			{
				if ((country_pricelist[i]).country_id == selectedId)
				{
					var decoded = $("<div/>").html((country_pricelist[i]).description).text();
					editor.SetHTML(decoded);
					break;
				}
				else
				{
					editor.SetHTML('');
				}				
			}			
		});
		
		$('select#country').change();
		$('#servicesForm input').keypress(function(event){validate_number(event);});
	});

	function validate_number(evt) {
		var theEvent = evt || window.event;
		var key = theEvent.keyCode || theEvent.which;
		key = String.fromCharCode( key );
		var regex = /[0-9]|\./;
		if ( ! regex.test(key))
		{
			theEvent.returnValue = false;
			theEvent.preventDefault();
		}
	}
	
	<?= editor('country_pricelist', 212, 920, 'Basic') ?>
</script>