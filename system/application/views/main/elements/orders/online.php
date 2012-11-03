<div class="online_order_form" style='display:none;'>
	<div class='table' style="position:relative;">
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>	
		<form class='admin-inside' action="<?= $selfurl ?>checkout" id="onlineOrderForm" method="POST">
			<input type='hidden' name="order_id" class="order_id" />
			<div class='new_order_box'>
				<div>
					<span class="label">Заказать из*:</span>
					<select id="country_from_online" name="country_from" class="textbox" onchange="setCountryFrom(this.value)">
						<option value="0">выберите страну...</option>
						<? foreach ($countries as $country) : ?>
						<option 
							value="<?= $country->country_id ?>"
							title="/static/images/flags/<?= $country->country_name_en ?>.png" 
							<? if (isset($filter->country_from) AND $filter->country_from == $country->country_id) : ?>selected<? endif; ?>><?= $country->country_name ?></option>
						<? endforeach; ?>
					</select>
				</div>
				<br style="clear:both;" />
				<div>
					<span class="label">В какую страну доставить*:</span>
					<select id="country_to_online" name="country_to" class="textbox" onchange="setCountryTo(this.value)">
						<option value="0">выберите страну...</option>
						<? foreach ($countries as $country) : ?>
						<option value="<?= $country->country_id ?>"  title="/static/images/flags/<?= $country->country_name_en ?>.png" <? if (isset($filter->country_to) AND $filter->country_to == $country->country_id) : ?>selected<? endif; ?>><?= $country->country_name ?></option>
						<? endforeach; ?>
					</select>
				</div>
				<br style="clear:both;" />
				<div>
					<span class="label">Город доставки*:</span>
					<input style="width:180px;" class="textbox" maxlength="255" type='text' id='city_to' name="city_to" />
				</div>
				<br style="clear:both;" />
				<div>
					<span class="label">Cпособ доставки:</span>
					<input style="width:180px;" class="textbox" maxlength="255" type='text' id='requested_delivery' name="requested_delivery" />
				</div>
				<br style="clear:both;" />
				<div>
					<span class="label dealer_number_switch">
						<a href="javascript: void(0);" onclick="">Выбрать посредника</a>
					</span>
					<span class="label dealer_number_box" style='display:none;'>Номер посредника:</span>
					<input class="textbox dealer_number_box" maxlength="6" type='text' id='dealer_id' name="dealer_id" style='display:none;width:180px;' >
					<span class="label dealer_number_box" style='display:none;'>
						<img border="0" src="/static/images/delete.png" title="Удалить">
					</span>
				</div>
				<br style="clear:both;" />
			</div>
		</form>
	</div>
	<h3>Добавить товар:</h3>
	<div class="h2_link">
		<img src="/static/images/mini_help.gif" style="float:right;margin-left: 7px;" />
		<a href="javascript: void(0);" class="excel_switcher" style="">Массовая загрузка товаров</a>
	</div>		
	<form class='admin-inside' action="<?= $selfurl ?>addProductManualAjax" id="onlineItemForm" method="POST">
		<input type='hidden' name="order_id" class="order_id" />
		<input type='hidden' name="ocountry" class="countryFrom" />
		<input type='hidden' name="ocountry_to" class="countryTo" />
		<input type='hidden' name="userfileimg" value="12345" />
		<div class='table add_detail_box' style="position:relative;">
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>	
			<div class='new_order_box'>
				<div>
					<span class="label">Ссылка на товар*:</span>
					<input style="width:180px;" class="textbox" maxlength="4096" type='text' id='olink' name="olink" />
				</div>
				<br style="clear:both;" />
				<div>
					<span class="label">Наименование товара*:</span>
					<input style="width:180px;" class="textbox" maxlength="255" type='text' id='oname' name="oname" />
				</div>
				<br style="clear:both;" />
				<div>
					<span class="label">Цена товара*:</span>
					<input style="width:180px;" class="textbox" maxlength="11" type='text' id='oprice' name="oprice" />
					<span class="label currency"></span>
				</div>
				<br style="clear:both;" />
				<div>
					<span class="label">Местная доставка*:</span>
					<input style="width:180px;" class="textbox" maxlength="11" type='text' id='odeliveryprice' name="odeliveryprice" />
					<span class="label currency"></span>
				</div>
				<br style="clear:both;" />
				<div>
					<span class="label">Примерный вес (г)*:
						<br />
						<i>1кг - 1000грамм
						</i>
					</span>
					<input style="width:180px;" class="textbox" maxlength="255" type='text' id='oweight' name="oweight" />
					<span class="label">
						<input class="border:auto;" type='button' value="примерный вес товаров" />
					</span>
				<br style="clear:both;" />
				</div>
			</div>
		</div>
		<h3>Дополнительная информация по товару:</h3>
		<div class='add_detail_box' style="position:relative;">
			<div class='new_order_box'>
				<br style="clear:both;" />
				<div>
					<span class="label">Цвет:</span>
					<input style="width:180px;" class="textbox" maxlength="255" type='text' id='ocolor' name="ocolor" />
				</div>
				<br style="clear:both;" />
				<div>
					<span class="label">Размер:</span>
					<input style="width:180px;" class="textbox" maxlength="255" type='text' id='osize' name="osize" />
					<span class="label">
						<input class="border:auto;" type='button' value="подобрать размер" />
					</span>
				</div>
				<br style="clear:both;" />
				<div>
					<span class="label">Количество:</span>
					<input style="width:180px;" class="textbox" maxlength="255" type='text' id='oamount' name="oamount" value="1" />
				</div>
				<br style="clear:both;" />
				<div>
					<span class="label">
						Скриншот (max. 3 Mb):
					</span>
					<span class="label screenshot_switch" style="font-size:11px;margin:0;width:300px;">
						<a href="javascript: showScreenshotLink();">Добавить ссылку</a>&nbsp;или&nbsp;<a href="javascript: showScreenshotUploader();" class="screenshot_switch">Загрузить файл</a>
					</span>
					<input class="textbox screenshot_link_box" type='text' id='oimg' name="oimg" style='display:none;width:180px;' value="ссылка на скриншот" onfocus="javascript: if (this.value == 'ссылка на скриншот') this.value = '';" onblur="javascript: if (this.value == '') this.value = 'ссылка на скриншот';">
					<input class="textbox screenshot_uploader_box" type='file' id='ofile' name="userfile" style='display:none;width:180px;'>
					<span class="label screenshot_link_box screenshot_uploader_box" style='display:none;'>
						<img border="0" src="/static/images/delete.png" title="Удалить">
					</span>
				</div>
				<br style="clear:both;" />
				<div>
					<span class="label">Нужно ли фото товара?</span>
					<input type='checkbox' id='' name="foto_requested" />
				</div>
				<br style="clear:both;" />
				<div>
					<span class="label">Комментарий к товару:</span>
					<textarea style="width:180px;resize:auto!important;" class="textbox" maxlength="255" id='ocomment' name="ocomment"></textarea>
				</div>
				<br style="clear:both;" />
			</div>
		</div>
	</form>
	<div style="height: 50px;" class="admin-inside">
		<div class="submit">
			<div>
				<input type="button" value="Добавить товар" name="add" onclick="/*addItem();*/">
			</div>
		</div>
	</div>
	<? View::show('main/ajax/showNewOrderDetails'); ?>
</div>
<script type="text/javascript">
	$(function() {
		$('div.online_order').click(function() {
			$.fn.getOrder({
				orderType : "online",
				forms : {
					order : "#onlineOrderForm",
					item : "#onlineItemForm",
				},
				fields : {
					country_from : {
						selector : "#country_from_online",
						required : true
					},
					country_to : {
						selector : "#country_to_online",
						required : true
					}
				},
				show : function() 
				{
					// Отображаем форму
					$('div.order_type_selector').hide();
					$('h2#page_title').html('Добавление нового Online заказа');
					$("div.online_order_form").show('slow');
				}
			});
		});
		
		// номер посредника
		$('.dealer_number_switch a').click(function() {
			$('.dealer_number_switch').hide('slow');
			$('.dealer_number_box').show('slow');
		});
		
		$('.dealer_number_box img').click(function() {
			$('.dealer_number_switch').show('slow');
			$('.dealer_number_box').hide('slow');
		});

		// ссылка на скриншот
		$('.screenshot_link_box img').click(function() {
			$('.screenshot_link_box,.screenshot_uploader_box').hide('slow');
			$('.screenshot_switch').show('slow');
		});
		
		$('.excel_switcher').click(function() {
			$('.excel_box').show('slow');
			$('.add_detail_box').hide('slow');
		});
		
		//$('input#osize,input#oprice,input#odeliveryprice').keypress(function(event){validate_float(event);});
		//$('input#oamount,input#oweight').keypress(function(event){validate_number(event);});
	});		

	$(function() {
		$('#onlineOrderForm').ajaxForm({
			target: $('#orderForm').attr('action'),
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{			
			},
			success: function(response)
			{
				// $progress = $('img.product_progress_bar:last');
				// $progress.hide();
				
				if (response)
				{
					error('top', 'Заказ не добавлен. '+response);
				}
				else
				{
					success('top', 'Заказ №' + $('input.order_id').val() + ' добавлен! Дождитесь предложений от посредников и выберите лучшее из них.');
					window.location = '/';
				}
			},
			error: function(response)
			{
				// $('img.product_progress_bar').hide();
				// $('em.product_error').html('Товар не добавлен. Попробуйте еще раз.<br /><br />').show();
			}
		});
	});
			
	// скриншот
	function showScreenshotLink()
	{
		$('.screenshot_link_box').show('slow');
		$('.screenshot_switch').hide('slow');
	}

	function showScreenshotUploader()
	{
		$('.screenshot_uploader_box').show('slow');
		$('.screenshot_switch').hide('slow');
	}
</script>

<script type="text/javascript">
	/* <![CDATA[ */
	jQuery(function(){
			
			/*jQuery("#ValidNumber").validate({
					expression: "if (!isNaN(VAL) && VAL) return true; else return false;",
					message: "Please enter a valid number"
			});
			jQuery("#ValidInteger").validate({
					expression: "if (VAL.match(/^[0-9]*$/) && VAL) return true; else return false;",
					message: "Please enter a valid integer"
			});
			jQuery("#ValidDate").validate({
					expression: "if (!isValidDate(parseInt(VAL.split('-')[2]), parseInt(VAL.split('-')[0]), parseInt(VAL.split('-')[1]))) return false; else return true;",
					message: "Please enter a valid Date"
			});
			jQuery("#ValidEmail").validate({
					expression: "if (VAL.match(/^[^\\W][a-zA-Z0-9\\_\\-\\.]+([a-zA-Z0-9\\_\\-\\.]+)*\\@[a-zA-Z0-9_]+(\\.[a-zA-Z0-9_]+)*\\.[a-zA-Z]{2,4}$/)) return true; else return false;",
					message: "Please enter a valid Email ID"
			});
			jQuery("#ValidPassword").validate({
					expression: "if (VAL.length > 5 && VAL) return true; else return false;",
					message: "Please enter a valid Password"
			});
			jQuery("#ValidConfirmPassword").validate({
					expression: "if ((VAL == jQuery('#ValidPassword').val()) && VAL) return true; else return false;",
					message: "Confirm password field doesn't match the password field"
			});
			jQuery("#ValidSelection").validate({
					expression: "if (VAL != '0') return true; else return false;",
					message: "Please make a selection"
			});
			jQuery("#ValidMultiSelection").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Please make a selection"
			});
			jQuery("#ValidRadio").validate({
					expression: "if (isChecked(SelfID)) return true; else return false;",
					message: "Please select a radio button"
			});
			jQuery("#ValidCheckbox").validate({
					expression: "if (isChecked(SelfID)) return true; else return false;",
					message: "Please check atleast one checkbox"
			});*/
	});
	/* ]]> */
</script>