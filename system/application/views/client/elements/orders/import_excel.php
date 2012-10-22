<div class='table' id='import_excel_block' style="width:400px; position:fixed; z-index: 1000; display:none;">
	<center>
		<h3 style="margin-top:0;margin-bottom:20px;">
			<? if (isset($Country)) : ?>
			Загрузить товары из Exсel
			<? else : ?>
			Загрузить заказ из Exсel
			<? endif; ?>
		</h3>
		<em id="importmessage" style="display:none;" class="red-color"></em>
	</center>
	<p>
		Если у Вас большое количество товаров в заказе, Вы можете <a href="<?= BASEURL ?>Zakaz-Countrypost.ru.xlsx">скачать</a> нашу форму в Exсel, заполнить ее и загрузить из нее товары на сайт.
	</p>
	<br />
	<form class='admin-inside' id="importOrderForm" action="<?= $selfurl ?>importOrder" enctype="multipart/form-data" method="POST">
		<table>
			<tr>
				<td id="importcountry">Страна&nbsp;производитель:&nbsp;<? if (empty($Country)) : ?>*<? endif; ?></td>
				<td>
					<? if (isset($Country)) : ?>
					<input name="importcountry" type="hidden" value="<?= $Country->country_id ?>" />
					<input type="text" readonly value="<?= $Country->country_name ?>" style='width:100%;' />
                    <? elseif (isset($order->order_country)) : ?>
					<input name="importcountry" type="hidden" value="<?=$order->order_country?>" />
					<input type="text" readonly value="<? foreach ($Countries as $Country){ if ($Country->country_id == $order->order_country){print $Country->country_name;} }?>" style='width:100%;' />
					<? else : ?>
					<select name="importcountry" style='width:100%;'>
						<option value=''>выберите страну...</option>
						<? foreach ($Countries as $Country) : ?>
						<option value="<?= $Country->country_id ?>">
							<?= $Country->country_name ?>
						</option>
						<? endforeach; ?>
					</select>
                    <? endif; ?>
				</td>
			</tr>
			<tr>
				<td id="importfile">Excel файл:&nbsp;*</td>
				<td>
					<input type="file" id="file_browser" name="importfile" />
				</td>
			</tr>
			<tr class='last-row'>
				<td colspan='9'>
					<a class="float-left" href="<?= BASEURL ?>Zakaz-Countrypost.ru.xls">
						<br />
						Скачать форму
					</a>
					<div class='float'>	
						<div class='submit'>
							<div>
								<input type="submit" value="Загрузить" id="send_import_button" />
							</div>
						</div>
					</div>
					<img class="float" id="importProgress" style="display:none;margin:5px;" src="/static/images/lightbox-ico-loading.gif"/>
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
		$('#importOrderForm').ajaxForm({
			target: $('#importOrderForm').attr('action'),
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$('#importProgress').show();
			},
			success: function(response)
			{
				$('#importProgress').hide();
				
				if (response)
				{
					$('em#importmessage').html(response + '<br /><br />').show();
					
					$('#importfile,#importcountry').removeClass('red-color');
					
					if (response === 'Выберите страну.')
					{
						$('#importcountry').addClass('red-color');
					}
					else if (response === 'Выберите файл.')
					{
						$('#importfile').addClass('red-color');
					}
					else if (response === 'Товары не найдены.')
					{
						$('em#importmessage').html('Товары не найдены. Добавьте товары в форму и отправьте ее еще раз.<br /><br />').show();
					}
					else
					{
						$('em#importmessage').html('Товары не добавлены. Попробуйте еще раз.<br /><br />').show();
					}
				}
				else
				{
					$('#importfile,#importcountry').removeClass('red-color');
					
					$('em#importmessage')
						.removeClass('red-color')
						.addClass('green-color')
						.html('Товары успешно добавлены в корзину.<br /><br />')
						.show();
					window.location = '';
				}
			},
			error: function(response)
			{
				$('#importProgress').hide();
				$('em#importmessage').html('Товары не добавлены. Попробуйте еще раз.<br /><br />').show();
			}
		});
	});

	var import_excel_click = 0;
	
	function openImportPopup()
	{
		var offsetLeft	= (window.innerWidth - $('#import_excel_block').width()) / 2;
		var offsetTop	= (window.innerHeight - $('#import_excel_block').height()) / 2;
		
		$('#import_excel_block').css({
			'left' : offsetLeft,
			'top' : offsetTop
		});
		
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		$('#lay').fadeIn("slow");
		$('#import_excel_block').fadeIn("slow");
		
		if (!import_excel_click)
		{
			import_excel_click = 1;
			$('#lay').click(function(){
				$('#lay').fadeOut("slow");
				$('#import_excel_block').fadeOut("slow");
			})
		}
	}
</script>