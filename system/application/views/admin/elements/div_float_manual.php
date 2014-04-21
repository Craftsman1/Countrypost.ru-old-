<div class='table' id="lay2_block" style="width:400px; position:absolute; z-index: 1000; display:none; top:390px; left:290px;">
	<center>
		<h3 style="margin-top:0;margin-bottom:20px;">Новый заказ</h3>
		<em style="display:none;" class="pink-color"></em>
	</center>
	<form class='admin-inside' id="newOrderForm" action="<?=$selfurl?>addProductManualAjax/" enctype="multipart/form-data" method="POST">
		<table>
			<tr>
				<td>Название магазина:</td>
				<td><input type="text" name="shop" value="" size=40></td>
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
				<td>Страна&nbsp;производитель:&nbsp;*</td>
				<td>
					<?
					if (!isset($odetails)):?>
						<select name="ocountry">
						<?foreach ($Countries as $Country):?>
							<option value="<?=$Country->country_id;?>"><?=$Country->country_name;?></option>
						<?endforeach;?>
						</select>
                        <input name="order_id" type="hidden" value="0" />
					<?else:?>
						<input name="ocountry" type="hidden" readonly value="<?=$order->order_country;?>" />
						<input type="text" readonly value="<? foreach ($Countries as $Country){ if ($Country->country_id == $order->order_country){print $Country->country_name;} }?>" />
                        <input name="order_id" type="hidden" value="<?=$order->order_id;?>" />
					<?endif;?>
				</td>
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
					<div class='float'>	
						<div class='submit'><div><input type='submit' name="add" value='Добавить' /></div></div>
					</div>
					<img class="float" id="progressbar" style="display:none;margin:5px;" src="/static/images/lightbox-ico-loading.gif"/>
					<a target='_blank' href='/main/showFAQ#faq1'><br/>Как сделать скриншот?</a>
				</td>
				<td>
				</td>
			</tr>
		</table>
	</form>
</div>
<script src="<?=JS_PATH?>jquery.form.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('#newOrderForm').ajaxForm({
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
					$('em:last').html(response + '<br /><br />').show();
					
					$('#itemscreenshot,#itemlink').removeClass('red-color');
					
					if (response === 'Добавьте ссылку на товар.')
					{
						$('#itemlink').addClass('red-color');
					}
					else
					{
						$('#itemscreenshot').addClass('red-color');
					}
				}
				else
				{
					$('#itemscreenshot,#itemlink').removeClass('red-color');
					
					$('em:last')
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
				$('em:last').html('Товар не добавлен. Попробуйте еще раз.<br /><br />').show();
			}
		});
	});

	var fmclick = 0;
	function lay2(){
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		$('#lay').fadeIn("slow");
		$('#lay2_block').fadeIn("slow");
		
		if (!fmclick){
			fmclick = 1;
			$('#lay').click(function(){
				$('#lay').fadeOut("slow");
				$('#lay2_block').fadeOut("slow");
			})
		}
	}
</script>