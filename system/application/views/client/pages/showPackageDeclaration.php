<?
$declaration_disabled = 
	$package->declaration_status == 'help' OR
	$package->package_status == 'payed' OR
	$package->package_status == 'sent' OR
	$package->package_status == 'not_delivered';

$insurance_disabled = ($package->package_status == 'sent');
	/*$package->package_insurance OR
	$package->package_status == 'payed' OR
	$package->package_status == 'sent' OR
	$package->package_status == 'not_delivered';*/
?>
<div class='content'>
	<h2>
		Декларация  
		<a href="<?= $selfurl ?>showPackageDetails/<?= $package->package_id ?>" >посылки №<?= $package->package_id ?></a>
	</h2>
	<div class='back'>
		<a class='back' href='<?= $selfurl ?><?= $back_handler ?>'><span>Назад к списку</span></a>
	</div>
	<form class='card' id="declarationForm" action='<?=$selfurl?>saveDeclaration/<?=$package->package_id?>' method='POST'>
		<input type="hidden" name="insurance_comission" id="insurance_comission" value="3" />
		<label class='filling-declaration'>
			<input id="help" type="checkbox" <? if ($package->declaration_status == 'help') : ?>checked="checked" disabled="disabled"<? elseif ($declaration_disabled) : ?>disabled="disabled"<? endif; ?>/>
			<span>
				Запросить помощь в заполнении декларации. После выбора отменить будет нельзя.<em>Если вы не знаете, что в посылке, поставьте тут галочку. Мы заполним декларацию за вас. Стоимость $<?=$config['price_for_declaration']->config_value?></em>
			</span>
			<br />
		</label>
		<label style="margin-top:0;" class='filling-declaration' for="">
			<input id="insurance" type="checkbox" <? if ($package->package_insurance) : ?>checked="checked"<? endif; ?> <? if ($insurance_disabled) : ?>disabled <? endif; ?>/>
			<span>
				Добавить страховку (<?=$config['price_for_insurance']->config_value?>% от стоимости страховки)
				<em id="insurance_input" class='total-price' <? if (!$package->package_insurance) : ?>style="display:none;"<? endif; ?>>
					Введите сумму страховки (макс. $<?=$config['max_insurance']->config_value?>): <input type="text" class="green" id="insurance_amount" maxlength="4" name="insurance_amount" style="display:inline;float:none;width:30px;" value="<?=($package->package_insurance) ? $package->package_insurance_cost : '100'?>" <? if ($insurance_disabled) : ?>readonly<? endif; ?>><font id="total_insurance">= $<?=($package->package_insurance) ? $package->package_insurance_comission : '3'?> (3% от введенной суммы)</font>
					<? if ( ! $insurance_disabled) : ?>
					<div class='submit saveInsurance' ><div><input type='button' value='Сохранить' onclick="addInsurance();"/></div></div>
					<? endif; ?>
					<br /><br />
				</em>
				<em>
					Посылка страхуется только <b>от факта недоставки (потери) по истечении 3 месяцев с момента отправления</b>. Страховка не распространяется на: пропажу вещей, порчу, конфискацию посылки таможней (или части вещей), <font style="color:grey;">отправление на несуществующий адрес.</font><br />Максимальная сумма страховки <span style="display:inline;">$<?=$config['max_insurance']->config_value?></span>
				</em>
			</span>
		</label>
		<table>
			<tr>
				<td colspan="5" align="right">
					<? if ( ! $declaration_disabled) : ?>
					<a href="javascript:addDeclaration();" >добавить</a>
					<? endif;?>
				</td>
			</tr>
			
			<? $total_cost = 0;
			$index = 0; 
			if ( ! $declarations): 
				$index++; ?>
			<tr id="new_item1">
				<th>Вещь №1</th>
				<td>
					<div class='text-field name-field'><div><input name="new_item1" type='text' value='' /></div></div>
				</td>
				<td>
					<span>Количество:</span>
					<div class='text-field number-field'><div><input class="count" name="new_amount1" type='text' value='' /></div></div>
				</td>
				<td>
					<span>Стоимость:</span>
					<div class='text-field price-field'><div><input class="price" name="new_cost1" type='text' value='' /></div></div>
					<span>$</span>
				</td>
				<td>
					<? if (!$declaration_disabled): ?>
					<a class='delete' href="javascript:deleteNewItem('1');"><img title="Удалить" border="0" src="/static/images/delete.png"></a>
					<? endif; ?>
				</td>
			</tr>
			<? else : 
				foreach ($declarations as $declaration): 
					$index++; 
					$total_cost += $declaration->declaration_amount * $declaration->declaration_cost;
			?>
			<tr id="item<?=$declaration->declaration_id?>">
				<th>Вещь №<?=$index?></th>
				<td>
					<div class='text-field name-field'><div><input type='text' name="declaration_item<?=$declaration->declaration_id?>" value="<?=$declaration->declaration_item?>" id="item_name<?=$declaration->declaration_id?>"/></div></div>
				</td>
				<td>
					<span>Количество:</span>
					<div class='text-field number-field'><div><input class="count" type='text' name="declaration_amount<?=$declaration->declaration_id?>" value="<?=$declaration->declaration_amount?>" /></div></div>
				</td>
				<td>
					<span>Стоимость:</span>
					<div class='text-field price-field'><div><input class="price" type='text'  name="declaration_cost<?=$declaration->declaration_id?>" value="<?=$declaration->declaration_cost?>" /></div></div>
					<span>$</span>
				</td>
				<td>
					<? if ( ! $declaration_disabled): ?>
					<a class='delete' href="javascript:deleteItem('<?=$declaration->declaration_id?>');"><img title="Удалить" border="0" src="/static/images/delete.png"></a>
					<? endif; ?>
				</td>
			</tr>
			<? endforeach; endif;?>
			<tr>
				<td class='total-price' colspan='5'>
					<span>Общая сумма: <strong class='big-text' id="total"><?= $total_cost ?></strong></span>
					<span class='pink-color'>Лимит для России $1000</span>
					<? if ( ! $declaration_disabled): ?>
					<div style="margin-top:0;" class='submit'><div><input type='submit' value='Сохранить' /></div></div>
					<? endif; ?>
				</td>
			</tr>
		</table>
		<input type="hidden" id="declaration_count" value="<?=$index?>" />
	</form>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#insurance_amount').bind('keypress keydown mouseup keyup blur', function(){
			calculateInsuranceComission();
		});
		
		$('#help').change(function() {
			if ($(this).attr('checked') &&
				confirm('Запросить помощь партнера в заполнении декларации выбранной посылки?'))
			{
				window.location = '<?=$selfurl?>addDeclarationHelp/<?=$package->package_id?>';	
			}
		});

		$('#insurance').change(function() {
			if ($(this).attr('checked'))
			{
				$('#insurance_input').slideDown();
			}
			else
			{
				$('#insurance_input').slideUp();

				if (<?=$package->package_insurance?> &&
					confirm('Вы уверены, что хотите удалить страховку?'))
				{
					window.location = '<?=$selfurl?>removeInsurance/<?=$package->package_id?>';	
				}
			}
		});

		$('#insurance_input').keypress(function(event){validate_number(event);});
		
		<? if ($declaration_disabled): ?>
		$('#declarationForm tr input').attr('readonly', 'readonly');
		<? else : ?>
		addValidation();
		<? endif; ?>
	});
	
	function deleteNewItem(id)
	{
		deleteGeneric('#new_item' + id, true);
	}

	function deleteItem(id)
	{
		deleteGeneric('#item' + id);
	}	
	
	function deleteGeneric(search_string, is_remove)
	{
		var declaration_count = $('#declaration_count').val();
		if (declaration_count < 2)
		{
			alert('В декларации должен присутствовать хотя бы один товар.');
			return false;
		}
		
		if (is_remove)
		{
			$(search_string).hide().remove();
		}
		else
		{
			$(search_string).hide().find('input:text').val('');
		}
		
		$('#declarationForm tr:visible').each(function(i)
		{
			$(this).find('th').html('Вещь №' + i);
		});
		updateTotal();
		$('#declaration_count').val(declaration_count - 1);
		return true;
	}
	
	function addInsurance()
	{
		if ($('#insurance').attr('checked') &&
			confirm('Добавить страховку?'))
		{
			document.getElementById('declarationForm').action = '<?=$selfurl?>addInsurance/<?=$package->package_id?>';
			document.getElementById('declarationForm').submit();
		}
	}
	
	function calculateInsuranceComission()
	{
		var ourPercent = <?=$config['price_for_insurance']->config_value?>;
		
		var main_input = '#insurance_amount';
		var val = $(main_input).val();
		if (val.indexOf('.') > -1) $(main_input).val(parseInt(val));
		
		val = parseInt(val);
		val = (isNaN(val) ? 0 : val);
		val = Math.ceil(ourPercent*val/100);
		$('#total_insurance').html('= $' + val + ' (' + ourPercent + '% от введенной суммы)');
		$('#insurance_comission').val(val);
	}
	
	function addValidation()
	{
		var inputs = $('.count, .price');
		
		inputs.keypress(function(event){validate_number(event);});
		inputs.change(function(){updateTotal();});
		updateTotal();
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

	function addDeclaration(){
		var declaration_count = $('#declaration_count').val();
		
		if (declaration_count < 4)
		{
			var tag = $('#declarationForm tr:last');
			declaration_count++;
			var declaration_html = '<tr id="new_item' + declaration_count + '"><th>Вещь №' + declaration_count + '</th><td><div class="text-field name-field"><div><input type="text" name="new_item' + declaration_count + '" value="" /></div></div></td>	<td><span>Количество:</span><div class="text-field number-field"><div><input class="count" type="text" name="new_amount' + declaration_count + '" value="" /></div></div></td>		<td><span>Стоимость:</span><div class="text-field price-field"><div><input class="price" type="text" name="new_cost' + declaration_count + '" value="" /></div></div><span>$</span></td><td><a class="delete" href="javascript:deleteNewItem(' + declaration_count + ');"><img title="Удалить" border="0" src="/static/images/delete.png"></a></td></tr>';
			tag.before(declaration_html);
			$('#declaration_count').val(declaration_count);
			addValidation();
		}
	}

	function updateTotal(){
		var amounts = $('.count');
		var costs = $('.price');
		var total = 0;
		
		for (var i = 0; i < amounts.length; i++)
		{
			amount = parseInt(amounts[i].value);
			cost = parseFloat(costs[i].value);
			if (isNaN(amount)) amount = 0;
			if (isNaN(cost)) cost = 0;
			total += amount * cost;
		}

		$('#total').text(parseInt(total)+' $');
	}
</script>