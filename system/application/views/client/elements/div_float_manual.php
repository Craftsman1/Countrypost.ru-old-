<div class='table' id="lay2_block" style="width:400px; position:fixed; z-index: 1000; display:none;">
	<center>
		<h3 style="margin-top:0;margin-bottom:20px;">
		<? if (empty($order->order_id)) : ?>
		Новый заказ
		<? else : ?>
		Добавить товар в заказ №<?= $order->order_id ?>
		<? endif; ?>
		</h3>
		<em id="new_order_message" style="display:none;" class="pink-color"></em>
	</center>
	<form class='admin-inside' id="orderForm" action="<?= $selfurl ?>addProductManualAjax/" enctype="multipart/form-data" method="POST">
		<table>
			<tr>
				<td id="itemcountry">Страна:&nbsp;<? if (empty($order->order_id)) : ?>*<? endif; ?></td>
				<td>
					<? if ( ! isset($odetails)) : ?>
						<select name="ocountry" style='width:100%;'>
							<option value=''>выберите страну...</option>
							<? foreach ($Countries as $Country) : ?>
							<option value="<?= $Country->country_id ?>">
								<?= $Country->country_name ?>
							</option>
							<? endforeach; ?>
						</select>
                        <input name="order_id" type="hidden" value="0" />
					<? else : ?>
						<input name="ocountry" type="hidden" readonly value="<?=$order->order_country;?>" />
						<input type="text" readonly value="<? foreach ($Countries as $Country){ if ($Country->country_id == $order->order_country){print $Country->country_name;} }?>" />
                        <input name="order_id" type="hidden" value="<?=$order->order_id;?>" />
					<? endif; ?>
				</td>
			</tr>
			<tr>
				<td id="itemlink">Ссылка на товар: *</td>
				<td><input type="text" name="olink" value="" size=40></td>
			</tr>
			<tr>
				<td>Название товара:</td>
				<td><input type="text" name="oname" value="" size=40></td>
			</tr>
			<tr>
				<td>Цвет:</td>
				<td><input type="text" name="ocolor" value="" size=40></td>
			</tr>
			<tr>
				<td>Размер:</td>
				<td><input type="text" name="osize" value="" size=40></td>
			</tr>				
			<tr>
				<td>Количество:</td>
				<td><input type="text" name="oamount" value="" size=40></td>
			</tr>
			<tr>
				<td id="itemscreenshot">Скриншот товара:<br />(макс. размер размер загружаемой картинки 3MB): *</td>
				<td>
					<input checked type="radio" value="1" id="img1" name="img"><label for="img1"><input id="userfileimg" type="text" name="userfileimg" maxlength="4096" value="" style="width:205px"></label><br>
					<input type="radio" value="2" id="img2" name="img"><label for="img2"><input id="userfile" type="file" name="userfile" value="" size="26"></label>
				</td>
			</tr>
			<tr class='last-row'>
				<td colspan='9'>
					<? if (empty($order->order_id)) : ?>
					<div>	
						<div class='submit'>
							<div>
								<input onclick="$('#lay2_block').fadeOut('slow');openImportPopup();" style='width:157px;' type='button' name="importExcel" value='Загрузить заказ из Exel' />
							</div>
						</div>
					</div>
					<? endif; ?>
					<div class='float'>	
						<div class='submit'>
							<div>
								<input type='submit' name="add" value='Добавить' />
							</div>
						</div>
					</div>
					<img class="float" id="progressbar" style="display:none;margin:5px;" src="/static/images/lightbox-ico-loading.gif"/>
				</td>
				<td>
				</td>
			</tr>
		</table>
	</form>
</div>
<script src="<?= JS_PATH ?>jquery.form.js"></script>
<script type="text/javascript">
	$(function() {
		$('#orderForm').ajaxForm({
			target: $('#orderForm').attr('action'),
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$('#progressbar').show();
			},
			success: function(response)
			{
				$('#progressbar').hide();
				
				if (response)
				{
					$('em#new_order_message').html(response + '<br /><br />').show();
					
					$('#itemscreenshot,#itemlink,#itemcountry').removeClass('red-color');
					
					if (response === 'Добавьте ссылку на товар.')
					{
						$('#itemlink').addClass('red-color');
					}
					else if (response === 'Выберите страну.')
					{
						$('#itemcountry').addClass('red-color');
					}
					else
					{
						$('#itemscreenshot').addClass('red-color');
					}
				}
				else
				{
					$('#itemscreenshot,#itemlink,#itemcountry').removeClass('red-color');
					
					$('em#new_order_message')
						.removeClass('pink-color')
						.addClass('green-color')
						.html('Товар успешно добавлен в корзину.<br /><br />')
						.show();
					window.location = '';
				}
			},
			error: function(response)
			{
				$('#progressbar').hide();
				$('em#new_order_message').html('Товар не добавлен. Попробуйте еще раз.<br /><br />').show();
			}
		});
	});

	var add_product_click = 0;
	function lay2(){
		var offsetLeft	= (window.innerWidth - $('#lay2_block').width()) / 2;
		var offsetTop	= (window.innerHeight - $('#lay2_block').height()) / 2;
		
		$('#lay2_block').css({
			'left' : offsetLeft,
			'top' : offsetTop
		});
			
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		<? if ( ! empty($updatePriceNotification)) : ?>
		if (confirm("При добавлении или удалении товара в уже оплаченный заказ, а также любом другом изменении оплаченного заказа, общая стоимость всего заказа пересчитывается по текущему курсу на главной странице. В связи с тем, что курсы валют меняются несколько раз в день, общая стоимость заказа может как увеличится, так и уменьшится. Просьба это учитывать при любых изменениях оплаченного заказа.\n\nЕсли Вы с этим не согласны, просьба отказаться от добавления изменений в уже оплаченный заказ."))
		{
			$('#lay').fadeIn("slow");
			$('#lay2_block').fadeIn("slow");
		}
		else
		{
			$('#lay').fadeOut("slow");
			$('#lay2_block').fadeOut("slow");
		}
		<? else : ?>
		$('#lay').fadeIn("slow");
		$('#lay2_block').fadeIn("slow");
		<? endif; ?>
		if (!add_product_click)
		{
			add_product_click = 1;
			$('#lay').click(function(){
				$('#lay').fadeOut("slow");
				$('#lay2_block').fadeOut("slow");
			});
		}
	}
</script>