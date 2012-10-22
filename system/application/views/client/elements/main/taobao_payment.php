<div class='table' id='taobao_payment_block' style="width:370px; position:fixed; z-index: 1000; display:none;">
	<center>
		<h3 style="margin-top:0;margin-bottom:20px;">Заявка на оплату заказа (счета) на Taobao.com</h3>
	</center>
	<p>
		Как создать счет для оплаты заказа на Taobao.com можно посмотреть <a href='#' class='taobao_register'>тут</a>.
		<br />
		<br />
		Для оплаты счета пополните баланс на сайте и заполните форму ниже, либо скачайте и заполните эту форму и отправьте ее на <a href='mailto:info@countrypost.ru'>info@countrypost.ru</a>.
		<br />
	</p>
	<br />
	<div class='extra_service_plus'>
		Добавить еще ссылку
	</div>
	<br />
	<form class='admin-inside' id='taobao_payment_form' action="/client/taobaoPayment/" enctype="multipart/form-data" method="POST">
		<input type='hidden' name='taobao_payment_count' id='taobao_payment_count' value='1'>
		<table>
			<tr>
				<td>
					Ссылка
				</td>
				<td>
					Сумма, &yen;<em class='amount_star'>*</em>
				</td>
			</tr>
			<tr class='payment'>
				<td>
					<b>1.</b><em class='link_star'>*</em>
					<input type="text" name="taobao_payment_link1" maxlength="4096" value="" />
				</td>
				<td>
					<input type="text" name="taobao_payment_amount1" id="taobao_payment_amount1" class='amount' maxlength="7" value="" />
				</td>
			</tr>
			<tr>
				<td>
					<b>
						Итого :
					</b>
				</td>
				<td>
					<b id='taobao_payment_total'></b>
				</td>
			</tr>
			<tr>
				<td nowrap>
					<b>
						Комиссия :
					</b>
				</td>
				<td>
					<b id='taobao_payment_tax'></b>
				</td>
			</tr>
			<tr class='last-row'>
				<td colspan='2'>
					<br />
					<em>
						* поля, обязательны для заполнения
					</em>
					<br />
					<em class='message' style='display:none;'>
					</em>
				</td>
			</tr>
			<tr class='last-row'>
				<td colspan='2'>
					<div class='float'>	
						<div class='submit'>
							<div>
								<input type='submit' name="add" value='Отправить' />
							</div>
						</div>
						<div class='submit'>
							<div>
								<input type='button' value='Отмена' onclick="$('#lay').fadeOut('slow');$('#taobao_payment_block').fadeOut('slow');"/>
							</div>
						</div>
					</div>
					<img class="float progressbar" style="display:none;margin:5px;" src="/static/images/lightbox-ico-loading.gif"/>
				</td>
			</tr>
		</table>
	</form>
</div>
<script type="text/javascript">
	var taobao_payment_click = 0;

	function openTaobaoPaymentPopup()
	{
		var offsetLeft	= (window.innerWidth - $('#taobao_payment_block').width()) / 2;
		var offsetTop	= (window.innerHeight - $('#taobao_payment_block').height()) / 2;
		
		$('#taobao_payment_block').css({
			'left' : offsetLeft,
			'top' : offsetTop
		});
		
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		// init popup
		$('#taobao_payment_form em.message')
			.addClass('green-color')
			.removeClass('red-color')
			.html('')
			.hide();
				
		$('#taobao_payment_form em').removeClass('red-color');
		$('#taobao_payment_form input:text').val('');
		$('#taobao_payment_form tr.extra_payment').remove();
		$('#taobao_payment_count').val('1');
		$('#taobao_payment_form td b#taobao_payment_total,#taobao_payment_form td b#taobao_payment_tax').html('');

		update_total();
		
		// show popup
		$('#lay').fadeIn("slow");
		$('#taobao_payment_block').fadeIn("slow");
		
		if (!taobao_payment_click)
		{
			taobao_payment_click = 1;
			$('#lay').click(function(){
				$('#lay').fadeOut("slow");
				$('#taobao_payment_block').fadeOut("slow");
			})
		}
	}

	function update_total() 
	{
		var rate = <?= $cny_rate ?>;
		var tax = <?= $taobao_payment_tax ?>;
		var payment_count = $('#taobao_payment_count').val();
		var total_amount = 0;
		
		for (var i = 1; i <= payment_count; i++)
		{
			var amount = parseInt($('#taobao_payment_amount' + i).val());
			amount = (isNaN(amount) ? 0 : amount);
			total_amount += amount;
		}

		var total_amount_usd = Math.ceil(total_amount / rate);
		var total_tax = Math.ceil(tax * total_amount_usd * 0.01);
		
		$('#taobao_payment_form td b#taobao_payment_total').html(
			total_amount ?
			'&yen;' +
			total_amount +
			' ($' + 
			total_amount_usd +
			')' :
			'');
			
		$('#taobao_payment_form td b#taobao_payment_tax').html('$' + total_tax);
	}

	
	$(function() {
		$('#taobao_payment_form').ajaxForm({
			target: $('#taobao_payment_form').attr('action'),
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$('#taobao_payment_form em')
					.removeClass('red-color');

				$('#taobao_payment_form em.message')
					.html('')
					.hide();

				$('#taobao_payment_form img.progressbar').show();
			},
			success: function(response)
			{
				$('#taobao_payment_form img.progressbar').hide();
				
				if (response)
				{
					$('#taobao_payment_form em.message')
						.html(response + '<br /><br />')
						.addClass('red-color')
						.show();
					
					if (response === 'Введите ссылку на заказ Taobao.com.')
					{
						$('#taobao_payment_form em.link_star').addClass('red-color');
					}
					else if (response === 'Введите сумму для оплаты заказа.')
					{
						$('#taobao_payment_form em.amount_star').addClass('red-color');
					}
				}
				else
				{
					$('#taobao_payment_form em.message')
						.addClass('green-color')
						.html('')
						.hide();

					$('#taobao_payment_block').fadeOut('slow');
					openSuccessPopup();
				}
			},
			error: function(response)
			{
				$('#taobao_payment_form img.progressbar').hide();
				
				$('#taobao_payment_form em.message')
					.html('Заявка на регистрацию аккаунта не добавлена. Попробуйте еще раз.<br /><br />')
					.addClass('red-color')
					.show();
			}
		});
		
		$('#taobao_payment_amount1')
			.keypress(function(event){validate_number(event);})
			.bind('change keyup click', update_total);
		
		$('div.extra_service_plus').click(function(e) {
			e.preventDefault();
			var $count = $('#taobao_payment_count');
			
			if ($count.val() == 5)
			{
				return;
			}
			
			$count.val(parseInt($count.val()) + 1);
			var new_id = $count.val();
			
			$('#taobao_payment_form tr.payment:last').after(
				"<tr class='payment extra_payment'><td><b>" + 
				new_id +
				".</b>&nbsp;&nbsp;<input type='text' id='taobao_payment_link" + 
				new_id +
				"' name='taobao_payment_link" +
				new_id +
				"' maxlength='4096' value='' /></td><td><input type='text' id='taobao_payment_amount" +
				new_id +
				"' name='taobao_payment_amount" +
				new_id +
				"' maxlength='7' value='' />&nbsp;<img id='remove_taobao_payment" +
				new_id +
				"' title='Удалить' border='0' src='/static/images/delete.png'></td></tr>");
		
			$('#taobao_payment_form tr.extra_payment img#remove_taobao_payment' + new_id)
				.bind('click', {id: new_id}, function(e) {
					remove_taobao_payment(e.data.id);
				});

			$('#taobao_payment_amount' + new_id)
				.keypress(function(event){validate_number(event);})
				.bind('change keyup click', update_total);
			
			update_total();
		});
		
		$('a.taobao_payment').click(function(e) {
			e.preventDefault();
			openTaobaoPaymentPopup();
		});
	});
	
	function remove_taobao_payment(payment_id)
	{
		var $count = $('#taobao_payment_count');
		$count.val(parseInt($count.val()) - 1);
		
		var i = parseInt(payment_id);
		$('#taobao_payment_form tr.payment:eq(' + (i - 1) + ')').remove();
	
		for (i; i <= 5; i++)
		{
			//alert('#taobao_payment_form tr.payment:eq(' + (i - 1) + ')');			
			$('#taobao_payment_form tr.payment:eq(' + (i - 1) + ')').each(function() {
				$(this)
					.find('b')
					.html(i + '.');

				$(this)
					.find('input:first')
					.attr('id', 'taobao_payment_link' + i)
					.attr('name', 'taobao_payment_link' + i);

				$(this)
					.find('input:last')
					.attr('id', 'taobao_payment_amount' + i)
					.attr('name', 'taobao_payment_amount' + i);

				$(this)
					.find('img')
					.attr('id', 'remove_taobao_payment' + i)
					.unbind('click')
					.bind('click', {id: i}, function(e) {
						//alert(e.data.id + ' updated');
						remove_taobao_payment(e.data.id);
					});
			});
		}
		
		update_total();
	}
</script>