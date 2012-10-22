<?
$declaration_disabled = 
	$package->package_status == 'payed' OR
	$package->package_status == 'sent' OR
	$package->package_status == 'not_delivered';
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
		<table>
			<? if ( ! $declaration_disabled): ?>
			<tr>
				<td colspan="5" align="right">
					<a href="javascript:addDeclaration();" >добавить</a>
				</td>
			</tr>
			<? endif;?>
			<?$index = 0; if (!$declarations): $index++;?>
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
					<? if ( ! $declaration_disabled): ?>
					<a class='delete' href="javascript:deleteNewItem('1');"><img title="Удалить" border="0" src="/static/images/delete.png"></a>
					<? endif; ?>
				</td>
			</tr>
			<?else : foreach ($declarations as $declaration): $index++; ?>
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
					<span>Общая сумма: <strong class='big-text' id="total">0</strong></span>
					<span class='pink-color'>Лимит для России 1000$</span>
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
		addValidation();
		
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