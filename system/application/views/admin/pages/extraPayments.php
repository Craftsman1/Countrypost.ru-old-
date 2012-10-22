<script type="text/javascript" src="/static/js/jquery-ui.min.js"></script>
<div class='content'>
	<?View::show($viewpath.'elements/div_submenu');?>
	<h3>Дополнительные платежи</h3>
	<? if (isset($result) && isset($result->e)) : ?>
	<?if($result->e<0):?>
		<em class="order_result red-color"><?=$result->m?></em><br />
	<?elseif ($result->e>0):?>
		<em class="order_result green-color"><?=$result->m?></em><br />
	<?endif;?>		
	<?endif;?>
		<br />
	<form class='admin-sorting' id="paymentForm" action="<?=$selfurl?>addExtraPayment" method="POST">
		<div class='table' >
			<div class="angle angle-lt"></div>
			<div class="angle angle-rt"></div>
			<div class="angle angle-lb"></div>
			<div class="angle angle-rb"></div>
			<table>
				<tr>
					<td>
						<div class='extra_payment' id='from_box'>
							<span class='first-title'>Отправитель:</span>
							<br />
							<select id="from" name="from" class='select extra_payment'>
								<option value="-">-</option>
								<option value="client">Клиент</option>
								<option value="partner">Партнер</option>
								<option value="admin">Администратор</option>
							</select>
						</div>
						<div class='extra_payment' id='to_box'>
							<span class='first-title'>Получатель:</span>
							<br />
							<select id="to" name="to" class='select extra_payment'>
								<option value="-">-</option>
								<option value="client">Клиент</option>
								<option value="partner">Партнер</option>
								<option value="admin">Администратор</option>
							</select>
						</div>
						<div class='extra_payment'>
							<span class='first-title'>Способ оплаты:</span>
							<br />
							<input type='text' maxlength="4096" name="payment_type" value='оплата со счета' onfocus='if (this.value == "оплата со счета") this.value = "";' onblur='if (this.value == "") this.value = "оплата со счета"'/>
						</div>
						<div class='extra_payment'>
							<span class='first-title'>Назначение платежа:</span>
							<br />
							<input type='text' maxlength="4096" name="payment_purpose" />
						</div>
						<div id="comment_box" class='extra_payment'>
							<span class='first-title'>Комментарий:</span>
							<br />
							<input type='text' maxlength="4096" name="payment_comment" />
						</div>
						<div id="money_dollar" class='extra_payment_money'>
							<span class='first-title'>Сумма ($):</span>
							<br /><input type='text' maxlength="11" name="payment_amount" />
						</div>
						<div id="money_ru" class='extra_payment_money'>
							<span class='first-title'>Сумма (руб.):</span>
							<br />
							<input type='text' maxlength="11" name="payment_amount_ru" />
						</div>
						<div  id="money_comission" class='extra_payment_money'>
							<span class='first-title'>Комиссия:</span>
							<br />
							<input type='text' maxlength="4096" name="payment_comission" />
						</div>
					</td>
				</tr>
				<tr>
					<td style="border:0;">
						<div class="admin-inside">
							<div class="submit">
								<div>
									<input type="submit" value="Добавить" >
								</div>
							</div>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</form>
	<br />
	<?View::show($viewpath.'ajax/showExtraPayments', array(
		'payments' => $payments,
		'pager' => $pager));?>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#money_dollar input:text,#money_ru input:text').keypress(function(event){validate_number(event);});

		setupInputs('from');
		setupInputs('to');
		
	});
	
	function setupInputs(prefix)
	{
		$('#' + prefix).change(function() {
			var selected_sender = $(this).find('option:selected').val();
			
			if (selected_sender == 'partner')
			{
				showLocalBoxes();
				showPartnerBoxes(prefix);				
			}
			else
			{
				hidePartnerBoxes(prefix);
				hideLocalBoxes();
			}
			
			if (selected_sender == 'client')
			{
				showClientBox(prefix);				
			}
			else
			{
				hideClientBox(prefix);				
			}
		});
	}
	
	function showClientBox(prefix)
	{
		$('#' + prefix + '_box').append('<input type="text" id="' + prefix + '_client" name="payment_' + prefix + '" class="extra_payment ' + prefix + '_client" />');
		$('#' + prefix + '_client')
			.focus()
			.keypress(function(event){validate_number(event);})
			.autocomplete({
				source: function( request, response ) {
					$.ajax({
						url: '<?=$selfurl?>autocompleteClient/' + request.term,
						success: function(data) {
							response($.map(eval(data), function( item ) {
								return {
									label: item,
									value: item
								}
							}));
						}
					});
				},
				minLength: 1
			});
	}

	function hideClientBox(prefix)
	{
		$('#' + prefix + '_box .' + prefix + '_client').remove();
	}
	
	function hidePartnerBoxes(prefix)
	{
		$('#' + prefix + '_box .' + prefix + '_partner').remove();
	}
	
	function hideClientBoxes(prefix)
	{
		$('#' + prefix + '_box .' + prefix + '_client').remove();
	}
	
	function showPartnerBoxes(prefix)
	{
		$('#' + prefix + '_box').append('<select id="' + prefix + '_partner" name="payment_' + prefix + '" class="' + prefix + '_partner select extra_payment">' + showManagers() + '</select>');
		$('#' + prefix + '_partner').focus();
	}

	function showManagers()
	{
		return '<?if ($managers) : foreach($managers as $manager) : ?><option value="<?=$manager->manager_user?>"><?=$manager->user_login?></option><?endforeach; endif;?>';
	}
	
	function hideLocalBoxes()
	{
		if (!$('.to_partner,.from_partner').length)
		{
			$('#money_dollar .amount_local,#money_comission .comission_local,#comment_box .comment_local,#money_ru .comment_local').remove();
		}
	}
	
	function showLocalBoxes()
	{
		if (!$('.to_partner,.from_partner').length)
		{			
			$('#money_dollar').append('<br class="amount_local" /><input class="amount_local" type="text" maxlength="11" name="payment_amount_local" />');
			$('#money_comission').append('<br class="comission_local" /><input class="comission_local" type="text" maxlength="11" name="payment_comission_local" />');
			$('#comment_box').append('<br class="comment_local" /><span class="comment_local first-title">Сумма в<br />местной валюте:</span>');
			$('#money_ru').append('<br class="comment_local" /><span class="comment_local first-title">Комиссия в<br />местной валюте</span>');
			$('input.amount_local').keypress(function(event){validate_number(event);});
		}
	}
	
	function deleteItem(id){
		if (confirm("Вы уверены, что хотите удалить платеж №" + id + "?")){
			window.location.href = '<?=$selfurl?>deleteExtraPayment/' + id;
		}
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
</script>