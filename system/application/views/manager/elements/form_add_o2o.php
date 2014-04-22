	<script>
	function deleteItem(id){
		if (confirm("Вы уверены, что хотите удалить заявку №" + id + "?")){
			window.location.href = '<?=$selfurl?>deleteOrder2out/' + id;
		}
	}
	
	var rate = <?=$rate?>;
	
	$(function() {
		$('#o2oform input:text').keypress(function(event) {
			validate_number(event);
		});
		
		$('#ammount_local').bind('keypress keydown mouseup keyup blur', function() {
			convertToUsd();
		});
		
		$('#ammount_usd').bind('keypress keydown mouseup keyup blur', function() {
			convertToLocal();
		});
		
		$('#o2oform').submit(function(e) {
			var account_count = $(this).find('input:checked').length;
			
			if (account_count == 2)
			{
				e.preventDefault();
				alert('Выберите хотя бы один счет для списания средств.');
			}
		});
		convertToLocal();
	});
	
	function convertToUsd() 
	{			
		var amount = $('#ammount_local').val();
		amount = parseFloat(amount);
		amount = (isNaN(amount) ? 0 : amount) / rate;

		$('#ammount_usd').val(Math.ceil(amount));
	}
	
	function convertToLocal() 
	{			
		var amount = $('#ammount_usd').val();
		amount = parseFloat(amount);
		amount = (isNaN(amount) ? 0 : amount) * rate;

		$('#ammount_local').val(Math.ceil(amount));
	}
	
	function validate_number(evt) 
	{
		var theEvent = evt || window.event;
		var key = theEvent.keyCode || theEvent.which;
		key = String.fromCharCode(key);
		var regex = /[0-9]/;
		if (!regex.test(key))
		{
			theEvent.returnValue = false;
			theEvent.preventDefault();
		}
	}
	</script>
	<h2>Вывод денег</h2>
	<p>Для вывода денег со счета введите сумму в местной валюте и сумму в $, равную сумме в местной валюте.</p>
	<? if (isset($result->type)) : if ($result->e < 0):?>
		<em style="color:red;"><?=$result->m?></em>
		<br />
		<br />
	<? elseif ($result->e > 0) : ?>
		<em style="color:green;"><?=$result->m?></em>
		<br />
		<br />
	<? endif; endif; ?>
	<form class='admin-inside' id="o2oform" action="<?=$selfurl?>order2out" method='POST' style="width:500px;">
		<div class='table'>
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<table>
				<tr>
					<td>Сумма вывода (<?=$this->session->userdata('manager_currency')?>):</td>
					<td><input class="input" maxlength="8" type='text' name='ammount_local' id='ammount_local'/></td>
					<td>
						<input class="input" type='checkbox' name='ignore_local' id='ignore_local' />
						<label for="ignore_local">Не списывать со счета</label>
					</td>
				</tr>
				<tr>
					<td>Сумма вывода ($):</td>
					<td><input class="input" maxlength="8" type='text' name='ammount' id='ammount_usd' value="100"/></td>
					<td>
						<input class="input" type='checkbox' name='ignore_usd' id='ignore_usd'/>
						<label for="ignore_usd">Не списывать со счета</label>
					</td>
				</tr>
				<tr class='last-row'>
					<td colspan='9'>
						<div class='float'>	
							<div class='submit'><div>
								<input type='submit' name='send' value='Отправить заявку' style="width:115px;"/>
							</div></div>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</form>