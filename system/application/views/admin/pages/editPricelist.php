<style>
	.card .text-field {
		margin-left: 0px;
	}
	
	.card td {
		padding: 10px 12px 10px 0px;
		border-bottom: 0;
	}
	
	.card td:last-child {
		padding: 10px 0px 10px 0px;
	}
	
	.card table {
		margin: 0px;
	}
</style>
<div class='content'>
	<? View::show($viewpath.'elements/div_submenu'); ?>
	<h3>Изменить тарифы на доставку</h3>
	<div class="h2_link">
		<a href="<?=$selfurl?>showAddDelivery">Добавить способ доставки</a>
	</div>
	<br />
	<form class='admin-inside' id="filterForm" action="<?=$selfurl?>filterEditPricelist" method="POST">
		<div class='sorting'>
			<span class='first-title'>Доставка из:</span>
			<select name="pricelist_country_from" class="select">
				<option value="">выбрать...</option>
				<? if ($countries) : foreach($countries as $country) : ?>
				<option value="<?=$country->country_id?>" <? if ($country->country_id == $filter->pricelist_country_from) : ?>selected="selected"<? endif; ?>><?=$country->country_name?></option>
				<?endforeach; endif;?>
			</select>
			<span class='first-title'>&nbsp;доставка в</span>
			<select name="pricelist_country_to" class="select">
				<option value="">выбрать...</option>
				<?if ($countries) : foreach($countries as $country) : ?>
				<option value="<?=$country->country_id?>" <? if ($country->country_id == $filter->pricelist_country_to) : ?>selected="selected"<? endif; ?>><?=$country->country_name?></option>
				<?endforeach; endif;?>
			</select>
			<span class='first-title'>&nbsp;способ доставки:</span>
			<select name="pricelist_delivery" class="select">
				<option value="">выбрать...</option>
				<?if ($deliveries) : foreach($deliveries as $deliv) : ?>
				<option value="<?=$deliv->delivery_id?>" <? if ($deliv->delivery_id == $filter->pricelist_delivery) : ?>selected="selected"<? endif; ?>><?=$deliv->delivery_name?></option>
				<?endforeach; endif;?>
			</select>
		</div>
	</form>
	<form class='card' id="pricelistForm" action='<?=$selfurl?>savePricelist/<?=$filter->pricelist_country_from?>/<?=$filter->pricelist_country_to?>/<?=$filter->pricelist_delivery?>' enctype="multipart/form-data" method='POST' style='min-height:311px;'>
        	<table class='floatright'>
            <tr>
				<td>
					Описание тарифа
					<br />
					<br />
				</td>
			</tr>
			<tr>
				<td>
					<textarea id='description' name='description'>
						<?= ! empty($pricelist_description) ? $pricelist_description->pricelist_description : '' ?>
					</textarea>
					<br />
					<div class='submit floatright admin-inside' style="margin-top:13px;">
						<div><input type='submit' value='Сохранить' style="width:91px;" /></div>
					</div>
					<div class='floatleft'>
						<input id="userfile" type="file" name="userfile" value="" style="margin-left:0;margin-top:16px;">
					</div>
				</td>
			</tr>
		</table>
		<table class="pricelist">
            <tr>
	            <td>Вес (кг)<br /><a href="javascript:addPrice(1);" >добавить строку сверху</a><br /></td><td>Цена (<? if (isset($currency) && $currency) : echo $currency->currency_symbol; endif;?>)</td><td>Цена ($)</td><td></td>
            </tr>
			<? $index = 0; if (!isset($pricelist) || !$pricelist): $index++;?>
			<tr>
				<td>
					<div class='text-field name-field'><div><input name="new_weight1" id="new_weight1" type="text"/></div></div>
				</td>
				<td>
					<div class='text-field number-field'><div><input name="new_price_local1" type="text"/></div></div>
				</td>
				<td>
					<div class='text-field number-field'><div><input name="new_price1" type="text"/></div></div>
				</td>
				<td>
					<div class='text-field price-field'><div><input type="button" value="Удалить" onclick="javascript:removePrice('new_weight1');"/></div></div>
				</td>
			</tr>
			<? else : foreach ($pricelist as $price): $index++; ?>
			<tr>
				<td>
					<div class='text-field name-field'><div>
						<input name="pricelist_weight<?=$price->pricelist_id?>" id="pricelist_weight<?=$price->pricelist_id?>" type="text" value="<?=$price->pricelist_weight?>" />
					</div></div>						
				</td>
				<td>
					<div class='text-field number-field'><div><input name="pricelist_price_local<?=$price->pricelist_id?>" type="text" value="<?=$price->pricelist_price_local?>" /></div></div>
				</td>
				<td>
					<div class='text-field number-field'><div><input name="pricelist_price<?=$price->pricelist_id?>" type="text" value="<?=$price->pricelist_price?>" /></div></div>
				</td>
				<td>
					<div class='text-field price-field'><div><input type="button" value="Удалить" onclick="javascript:removePrice('pricelist_weight<?=$price->pricelist_id?>');"/></div></div>
				</td>
			</tr>
			<? endforeach; endif;?>
			<tr>
				<td class='total-price'>
	                <a href="javascript:addPrice(2);" >добавить строку снизу</a><br />
				</td>
				<td class='total-price' colspan='3'>
					<label style="float:right;" for="is_local_price">Конвертировать местную цену в $</label>
					<input style="float:right;margin-top:0px;" type="checkbox" id="is_local_price" name="is_local_price">
				</td>
			</tr>
		</table>
		<input type="hidden" id="price_count" value="<?=$index?>" />
	</form>
</div>
<script type='text/javascript' src='/system/plugins/fckeditor/fckeditor.js'></script>
<script type="text/javascript">
	$(document).ready(function() {
		addValidation();
		
		$('#filterForm select').change(function() {
			document.getElementById('filterForm').submit();	
		});
	});
	
	function addValidation()
	{
		$('#pricelistForm input:text').keypress(function(event){validate_number(event);});
	}
	
	function validate_number(evt) {
		var theEvent = evt || window.event;
		var key = theEvent.keyCode || theEvent.which;
		key = String.fromCharCode( key );
		var regex = /[0-9]|\./;
		if( !regex.test(key) ) {
			theEvent.returnValue = false;
			theEvent.preventDefault();
		}
	}
	
	function addPrice(t) {
		var price_count = $('#price_count').val();
		price_count++;
		
		var price_html = '<tr><td><div class="text-field name-field"><div><input name="new_weight' + price_count + '" id="new_weight' + price_count + '" type="text"/></div></div></td>	<td><div class="text-field number-field"><div><input name="new_price_local' + price_count + '" type="text"/></div></div></td><td><div class="text-field number-field"><div><input name="new_price' + price_count + '" type="text"/></div></div></td><td><div class="text-field price-field"><div><input type="button" value="Удалить" onclick="javascript:removePrice(' + "'" + 'new_weight' + price_count + "'" + ');"/></div></div></td></tr>';

		var tag;
		
		if (t == 2)
		{
			tag  = $('table.pricelist tr:nth(' + price_count + ')'); 
			tag.before(price_html);
		}
		else
		{
			tag  = $('table.pricelist tr:first'); 
			tag.after(price_html);
		}
		
		$('#price_count').val(price_count);
		
		addValidation();
	}
	
	function removePrice(id) 
	{
		$('#' + id).parent().parent().parent().parent().fadeOut('fast');
		$('#' + id).val('');
	}
	
	<?= editor('description', 204, 450, 'Basic') ?>
</script>