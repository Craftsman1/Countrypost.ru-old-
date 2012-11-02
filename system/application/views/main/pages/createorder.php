<div class='content'>
	<? Breadcrumb::showCrumbs(); ?>
	<br>
	<br>
	<h2 id='page_title'>Выберите вид заказа:</h2>
	<div class="order_type_selector">
		<div class="online_order order">
			<div>
				<b>Online заказ</b>
			</div>
			<div>Заказ на покупку и доставку из любого интернет-магазина, торговой площадки, аукциона и т.д.
			</div>
		</div>
		<div class="offline_order order">
			<div>
				<b>Offline заказ</b>
			</div>
			<div>Заказ на покупку и доставку из любого offline магазина/поставщика у которого нет сайта или online продаж. 
				<br />Заявки на поиск товара/поставщика также добавляйте сюда.
			</div>
		</div>
		<div class="service_order order">
			<div>
				<b>Услуга</b>
			</div>
			<div>Если Вам нужна какая-то помощь или услуга, не связанная с покупкой и доставкой, в любой стране.
			</div>
		</div>
		<div class="delivery_order order">
			<div>
				<b>Доставка</b>
			</div>
			<div>Если Вам нужна только доставка без выкупа и поиска товара.
			</div>
		</div>
	</div>
  
	<script type="text/javascript">
    (function($) {
      $.fn.getOrder = function(args)
      {
        var order = {
			params : $.extend({
				orderType : '',
				forms : {
					order : "#onlineOrderForm",
					item : "#onlineItemForm",
				},
				fields : {
					country_from : {
						selector : '#country_from',
						required : true
					},
					country_to : {
						selector : '#country_to',
						required : true
					}
				},
				show : function() {}
			},args),
			fields : 
			{
				order_id : 0,
				country_from : 0,
				country_to : 0,
				city_to : "",
				requested_delivery : ""
			},
			validationMessages : {
				"country_from" : "Необходимо выбрать страну поступления",
				"country_to" : "Необходимо выбрать страну доставки",
				"city_to" : "Необходимо указать город доставки",
				"requested_delivery" : "Необходимо указать способ доставки",
				"olink" : "Необходимо указать ссылку на товар",
				"oname" : "Необходимо указать название",
				"oprice" : "Необходимо указать цену",
				"odeliveryprice" : "Необходимо указать цену местной доставки",
				"oweight" : "Необходимо указать приблизительный вес"
			},
			init : function ()
			{
				this.showOrderProgress();
				this.create();            						
			},
			fill : function()
			{
				// Записываем значения полей формы заказа
				this.fields.country_from = isNaN(parseInt($('input.countryFrom').val(), 10)) ? 0 : parseInt($('input.countryFrom').val(), 10);
				this.fields.country_to = isNaN(parseInt($('input.countryTo').val(), 10)) ? 0 : parseInt($('input.countryTo').val(), 10);
				this.fields.city_to = $('input#city_to').val();
				this.fields.requested_delivery = $('input#requested_delivery').val();
				
				// Записываем значения полей формы товара
				this.itemFields =  
				{
					order_id : this.fields.order_id,
					ocountry : isNaN(parseInt($('input.countryFrom').val(), 10)) ? 0 : parseInt($('input.countryFrom').val(), 10),
					ocountry_to : isNaN(parseInt($('input.countryTo').val(), 10)) ? 0 : parseInt($('input.countryTo').val(), 10),
					userfileimg : "",
					olink : $('input#olink').val(),
					oname : $('input#oname').val(),
					oprice : isNaN(parseFloat($('input#oprice').val())) ? 0 : parseFloat($('input#oprice').val()),
					odeliveryprice : isNaN(parseFloat($('input#odeliveryprice').val())) ? 0 : parseFloat($('input#odeliveryprice').val()),
					oweight : isNaN(parseInt($('input#oweight').val(), 10)) ? 0 : parseInt($('input#oweight').val(), 10),
					ocolor : $('input#ocolor').val(),
					osize : $('input#osize').val(),
					oamount : isNaN(parseInt($('input#oamount').val(), 10)) ? 1 : parseInt($('input#oamount').val(), 10),
					oimg : ($('input#ofile').val() || $('input#oimg').val() == 'ссылка на скриншот' ? '' : $('input#oimg').val()),
					ofile : $('input#ofile').val(),
					foto_requested : ($('input[name="foto_requested"]:checked')).length == 1,
					ocomment : $('textarea#ocomment').val()
				}					
			}, // End fill
			
// --------------------- Order Part ---------------------

			create : function()
			{
				var o = this;
				$.ajax({
					url: '<?= $selfurl ?>addEmptyOrder/online',
					type: 'POST',
					dataType: 'html',
					success: function(data) {
						o.fields.order_id = data;
						$('input.order_id').val(o.fields.order_id);
						o.onAfterCreate();
						o.hideOrderProgress();
					},
					error: function() {
						o.hideOrderProgress();
						error('top', 'Невозможно создать заказ. Проверьте подключение к интернету или обратитесь к администратору.');
					}
				});
			}, // End create
			onAfterCreate : function()
			{		
				var urlregex = new RegExp('^(https?\:\/\/)?([w]{3}\.)?([^\/])*(\/)?');	
						
				// Создание дропбоксов для стран и валидация обязательных полей
				$(this.params.fields.country_from.selector).change(this.ddLifeValidate).msDropDown({mainCSS:'idd'});
				$(this.params.fields.country_to.selector).change(this.ddLifeValidate).msDropDown({mainCSS:'idd'});
				$('#city_to')
				  .validate({
					  expression: "if (VAL) return true; else return false;",
					  message: this.validationMessages["city_to"]
				  });
				/*$('#requested_delivery')
				  .validate({
					  expression: "if (VAL) return true; else return false;",
					  message: this.validationMessages["requested_delivery"]
				  });*/
				$('#olink')
				  .validate({
					  expression: "if (VAL) { var t = urlValidate(VAL); if (t === true) { return true; } else { options['message'] = t; return false; } } else return false;",
					  message: this.validationMessages["olink"]
				  })
				$('#oname')
				  .validate({
					  expression: "if (VAL) return true; else return false;",
					  message: this.validationMessages["oname"]
				  });
				$('#oprice')
				  .keypress(function(event){validate_float(event);})
				  .validate({
					  expression: "if (!isNaN(VAL) && VAL) return true; else return false;",
					  message: this.validationMessages["oprice"]
				  });
				$('#odeliveryprice')
				  .keypress(function(event){validate_float(event);})
				  .validate({
					  expression: "if (!isNaN(VAL) && VAL) return true; else return false;",
					  message: this.validationMessages["odeliveryprice"]
				  });
				$('#oweight')
				  .keypress(function(event){validate_number(event);})
				  .validate({
					  expression: "if (!isNaN(VAL) && VAL) return true; else return false;",
					  message: this.validationMessages["oweight"]
				  });
				$('#osize').keypress(function(event){validate_float(event);});
				$('#oamount').keypress(function(event){validate_number(event);});
				
				// Клик по кнопке "Добавить товар"
				this.bindAddItem();
							
				// Клик по кнопке "Готово"
				$('input[name="checkout"]').click(function() { window.order.checkout() });
							
				this.params.show();
			}, // End onAfterCreate
			showOrderProgress : function()
			{		
				// Отображаем прогресс по выбору типа заказа
				if(obj = $('.'+this.params.orderType+'_order div:first b'))
				{							
					var img = $("<div class='orderProgress'><img src='/static/images/lightbox-ico-loading.gif'/></div>");
					img.css({
							float: 'left',
							left: '110px',
							position: 'absolute',
							top: '-8px',
							width: '32px'
						});
					obj.append(img);
				}
			}, // End showOrderProgress
			hideOrderProgress : function()
			{
				// Скрываем прогресс по выбору типа заказа
				if(obj = $('.'+this.params.orderType+'_order div:first b'))
				{
					obj.hide('slow').remove();
				}
			}, // End hideOrderProgress
			checkout : function()
			{
				// Записываем значения полей формы
				this.fill();
			
				if (this.orderFieldsValidate())
				{
					$(this.params.forms.order).submit();
				}
			}, // End checkout
			orderFieldsValidate : function()
			{
				var fields = this.fields;
				var errorFields = Array();
				if (fields.order_id == 0)
				{
					error('top', 'Номер заказа не определен');
					return false;
				}
					
				if (fields.country_from == 0)
				{
					obj = $(this.params.fields.country_from.selector+'_msdd');
					if(!obj.next().hasClass('ValidationErrors')) 
					{
						obj.after('<span class="ValidationErrors">'+this.validationMessages["country_from"]+'</span>');
						obj.addClass('ErrorField');
					}
					errorFields[$(errorFields).size()] = obj[0];
				}
					
				if (fields.country_to == 0)
				{
					obj = $(this.params.fields.country_to.selector+'_msdd');
					if(!obj.next().hasClass('ValidationErrors')) 
					{
						obj.after('<span class="ValidationErrors">'+this.validationMessages["country_to"]+'</span>');
						obj.addClass('ErrorField');
					}
					errorFields[$(errorFields).size()] = obj[0];
				}
					
				if (fields.city_to == "")
				{
					obj = $('#city_to');
					if(!obj.next().hasClass('ValidationErrors')) 
					{
						obj.after('<span class="ValidationErrors">'+this.validationMessages["city_to"]+'</span>');
						obj.addClass('ErrorField');
					}
					errorFields[$(errorFields).size()] = obj[0];
				}					
					
				if ($(errorFields).size() > 0)
				{
					var firstErrorField = $(errorFields[0]);
					var offset = firstErrorField.offset();
					$(window).scrollTop(offset.top-30);
			
					error('top', 'Заказ не добавлен. Заполните все обязательные поля и попробуйте еще раз.');
					return false;
				}
				return true;
			}, // End orderFieldsValidate
					
// --------------------- Items Part ---------------------
	
			progress : null,
			itemFields : 
			{
				order_id : 0,
				ocountry : 0,
				ocountry_to : 0,
				userfileimg : "",
				olink : "",
				oname : "",
				oprice : 0,
				odeliveryprice : 0,
				oweight : 0,
				ocolor : "",
				osize : "",
				oamount : 1,
				oimg : "",
				ofile : "",
				foto_requested : 0,
				ocomment : ""
			},
			itemFieldsClear : function()
			{
				this.itemFields =  
				{
					order_id : 0,
					//ocountry : 0,
					userfileimg : "",
					olink : "",
					oname : "",
					oprice : 0,
					odeliveryprice : 0,
					oweight : 0,
					ocolor : "",
					osize : "",
					oamount : 1,
					oimg : "",
					ofile : "",
					foto_requested : 0,
					ocomment : ""
				}
			}, // End itemFieldsClear
			itemFormFieldsClear : function()
			{
				$('input#olink').val('');
				$('input#oname').val('');
				$('input#oprice').val('');
				$('input#odeliveryprice').val('');
				$('input#oweight').val('');
				$('input#ocolor').val('');
				$('input#osize').val('');
				$('input#oamount').val(1);
				$('input#oimg').val('ссылка на скриншот');
				$('input#ofile').val('');
				$('input[name="foto_requested"]').removeAttr('checked'),
				$('textarea#ocomment').val('');
				
				$('.screenshot_link_box,.screenshot_uploader_box').hide('slow');
				$('.screenshot_switch').show('slow');
			}, // End itemFormFieldsClear
			itemFieldsValidate : function()
			{
				var fields = this.itemFields;
				var errorFields = Array();
				if (fields.order_id == 0)
				{
					error('top', 'Номер заказа не определен');
					return false;
				}
				
				if (fields.ocountry == 0)
				{
					obj = $(this.params.fields.country_from.selector+'_msdd');
					if(!obj.next().hasClass('ValidationErrors')) 
					{
						obj.after('<span class="ValidationErrors">'+this.validationMessages["country_from"]+'</span>');
						obj.addClass('ErrorField');
					}
					errorFields[$(errorFields).size()] = obj[0];
				}
				
				if (fields.ocountry_to == 0)
				{
					obj = $(this.params.fields.country_to.selector+'_msdd');
					if(!obj.next().hasClass('ValidationErrors')) 
					{
						obj.after('<span class="ValidationErrors">'+this.validationMessages["country_to"]+'</span>');
						obj.addClass('ErrorField');
					}
					errorFields[$(errorFields).size()] = obj[0];
				}
				
				if (this.fields.city_to == '')
				{
					obj = $('input#city_to');
					if(!obj.next().hasClass('ValidationErrors')) 
					{
						obj.after('<span class="ValidationErrors">'+this.validationMessages["city_to"]+'</span>');
						obj.addClass('ErrorField');
					}
					errorFields[$(errorFields).size()] = obj[0];
				}
				
				if (fields.olink == '')
				{
					obj = $('input#olink');
					if(!obj.next().hasClass('ValidationErrors')) 
					{
						obj.after('<span class="ValidationErrors">'+this.validationMessages["olink"]+'</span>');
						obj.addClass('ErrorField');
					}
					errorFields[$(errorFields).size()] = obj[0];
				}
				
				if (fields.oname == '')
				{
					obj = $('input#oname');
					if(!obj.next().hasClass('ValidationErrors')) 
					{
						obj.after('<span class="ValidationErrors">'+this.validationMessages["oname"]+'</span>');
						obj.addClass('ErrorField');
					}
					errorFields[$(errorFields).size()] = obj[0];
				}
				
				if (fields.oprice == '')
				{
					obj = $('input#oprice');
					if(!obj.next().hasClass('ValidationErrors')) 
					{
						obj.after('<span class="ValidationErrors">'+this.validationMessages["oprice"]+'</span>');
						obj.addClass('ErrorField');
					}
					errorFields[$(errorFields).size()] = obj[0];
				}
				
				if (fields.odeliveryprice == '')
				{
					obj = $('input#odeliveryprice');
					if(!obj.next().hasClass('ValidationErrors')) 
					{
						obj.after('<span class="ValidationErrors">'+this.validationMessages["odeliveryprice"]+'</span>');
						obj.addClass('ErrorField');
					}
					errorFields[$(errorFields).size()] = obj[0];
				}
				
				if (fields.oweight == '')
				{
					obj = $('input#oweight');
					if(!obj.next().hasClass('ValidationErrors')) 
					{
						obj.after('<span class="ValidationErrors">'+this.validationMessages["oweight"]+'</span>');
						obj.addClass('ErrorField');
					}
					errorFields[$(errorFields).size()] = obj[0];
				}
				
				// Скролим страницу к первому неправильно заполненному полю
				if ($(errorFields).size() > 0) 
				{
					var firstErrorField = $(errorFields[0]);
					var offset = firstErrorField.offset();
					$(window).scrollTop(offset.top-30);
					
					error('top', 'Товар не добавлен. Заполните все обязательные поля и попробуйте еще раз.');
					return false;
				}					
				return true;
			}, // End itemFieldsValidate
			ddLifeValidate : function()
			{
				var o = window.order,
				value = parseInt(this.value, 10);
				if(value) 
				{
					var nextElement = $('#'+this.id+'_msdd').next();
					if(nextElement.hasClass('ValidationErrors'))
					{
						nextElement.remove();
						$('#'+this.id+'_msdd').removeClass('ErrorField');
					}
				}
				else
				{				
					key = "";				
					if (this.id.indexOf("_from") != -1) 
					{ 
						key = "country_from"	
					}
					else if(this.id.indexOf("_to") != -1)
					{
						key = "country_to"
					}
					$('#'+this.id+'_msdd').after('<span class="ValidationErrors">'+o.validationMessages[key]+'</span>');
					$('#'+this.id+'_msdd').addClass('ErrorField');
				}
			}, // End ddLifeValidate
			bindAddItem : function() {
				// Добавляем обработчик к кнопке добавления товара к заказу
				$('input[name="add"]').bind('click', this.addItem);
			}, // End bindAddItem
			unbindAddItem : function() {
				// Убираем обработчик у кнопки добавления товара к заказу
				$('input[name="add"]').unbind('click');
			}, // End unbindAddItem
			addItemProgress : function()
			{
				var parent = $('#odetail');
				var progress = $("<div class='progress'> " +
				"<img class='float product_progress_bar' src='/static/images/lightbox-ico-loading.gif'/> " +
				"</div>");
				progress.css({
					background : 'none',
					width : '32px',
					height : '32px',
					'margin-top' : '6px',
					float : 'left'					
				});
				
				parent.append(progress);
			}, // End addItemProgress
			removeItemProgress : function()
			{
				$('div.progress').remove();
			}, // End addItemProgress
			addItemRow : function(itemId)
			{		
				var screenshot_code = "<a href='javascript:void(0)' onclick='setRel(" + itemId + ");'>Просмотреть</a> <a rel='lightbox_" + itemId + "' href='/client/showScreen/" + itemId + "' style='display:none;'>Посмотреть</a>",
				snippet = $('.snippet');		

				// Прикручиваем обработчики к кнопкам
				snippet.find('a.delete_icon')
					.click(function() {
						window.order.deleteItem(itemId);
					});
				snippet.find('a.edit_icon')
					.click(function() {
						window.order.editItemRow(itemId);
					});
				
				// Подставляем изображение или ссылку на него
				snippet.find('.userfile:last')
					.html(screenshot_code)
					.removeClass('userfile');
				
				snippet.removeClass('snippet');
				// пересчитываем заказ
				updateTotals();
			}, // End addItemRow
			removeItemRow : function(itemId)
			{
				$('td#odetail' + itemId).parent().hide('slow').remove();
				// пересчитываем заказ
				updateTotals();
			}, // End removeItemRow				
			editItemRow : function (id) {
				error('top', 'Функция в разработке');
			}, // End editItemRow
			cancelItemRow : function (id) {
				error('top', 'Функция в разработке');
			}, // End cancelItem
			saveItemRow : function (id) {
				error('top', 'Функция в разработке');
			}, // End saveItem
			addItem : function(args)
			{
				var o = window.order;
				// Исключаем двойной клик по кнопке
				// Oтключить клик по кнопке добавления товара до завершения процесса 
				o.unbindAddItem();
				
				o.itemFieldsClear();
				// Записываем значения полей формы
				o.fill();
				
				// Если все заполнено правильно отправляем запрос
				if (o.itemFieldsValidate())
				{
					var iFields = o.itemFields,
					// Рисуем новый товар
					snippet = $("<tr class='snippet'>" +
						"<td id='odetail' class=''><input type='checkbox' value='1' /><br /><span class='itemId'></span></td>" +
						"<td class='oname'><a target='_blank' href='" + iFields.olink + "'>" + iFields.oname + "</a>" +
						(iFields.foto_requested == 1 ? " (требуется фото товара)" : "") +
						"<br/><b>Количество</b>: " + iFields.oamount + 
						((iFields.osize) ? " <b>Размер</b>: " + iFields.osize : "") + 
						((iFields.ocolor != "") ? " <b>Цвет</b>: " + iFields.ocolor : "") + 
						((iFields.ocomment != "") ? "<br/><b>Комментарий</b>: " + iFields.ocomment + "</td>" : "") +
						"<td class='oimg" + (iFields.ofile ? " userfile" : "") + "'>" + iFields.oimg + "</td>" +
						"<td class='oprice'>" + iFields.oprice + " <span class='label currency'>" + getSelectedCurrency() + "</span></td>" +
						"<td class='odeliveryprice'>" + iFields.odeliveryprice + " <span class='label currency'>" + getSelectedCurrency() + "</span></td>" +
						"<td class='oweight'>" + iFields.oweight + " г</td>" +
						"<td class='oedit'><a class='edit_icon' style='cursor: pointer;'><img border='0' src='/static/images/comment-edit.png' title='Изменить'></a><br /><a class='delete_icon'><img border='0' src='/static/images/delete.png' style='cursor: pointer;' title='Удалить'></a></td>" +
						"</tr>");					
					$('#new_products tr:first').after(snippet);
					
					o.addItemProgress();
					$(o.params.forms.item).submit();
				  
					// Отображаем список товаров
					$('#detailsForm').show();											
				}
				else
				{
					// Включаем клик по кнопке добавления товара 
					o.bindAddItem();
				}
			}, // End addItem
			deleteItem : function (itemId) 
			{
				if (confirm("Вы уверены, что хотите удалить товар №" + itemId + "?"))
				{						
					var order = this;
					$.post('<?= $selfurl ?>deleteProduct/' + itemId)
					.success(function() {
						// Удаляем строку товара
						order.removeItemRow(itemId);								
						// Если товаров больше нет сворачиваем таблицу товаров
						if ($('#detailsForm tr').length < 4)
						{
							$('#detailsForm').hide('slow');
						}						
						success('top', 'Товар успешно удален.');
					})
					error(function(args) {
						error('top', 'Товар не удален. Проверьте подключение к интернету и повторите попытку.');
					});
				}
			} // End deleteItem
        }
        order.init();
        window.order = order;
      }
      
    })(jQuery)
  </script>

	<? View::show('main/elements/orders/online'); ?>
	<? View::show('main/elements/orders/offline'); ?>
</div>
<script>
	
	function setCountryFrom(id)
	{
		var prevCurrency = selectedCurrency;
		
		for (var index in currencies)
		{
			var currency = currencies[index];
		
			if (id == currency['country_id'])
			{
				selectedCurrency = currency['country_currency'];
				$('input.countryFrom').val(id);
				$('.currency').html(selectedCurrency);
				updateTotals();
				break;
			}
		}
	}
	
	function setCountryTo(id)
	{
		for (var index in currencies)
		{
			var currency = currencies[index];
		
			if (id == currency['country_id'])
			{
				$('input.countryTo').val(id);
				countryTo = currency['country_name'];
				updateTotals();
				break;
			}
		}
	}
	
	var urlValidate = function(str) {
		var hasErrors = false,
			hasPrefix = false, 
			prefix = '', 
			hasWww = false,
			domain = '',
			hasFile = false,
			file = '',
			hasQuery = false,
			query = '',
			message = '';
		
		// Check Prefix		
		var prefRgx = new RegExp(/^([A-Za-z]{3,5})?\:\/\//),
			prefRes = prefRgx.exec(str);
		if(prefRes !== null)
		{
			var allowed = Array('https','http','ftp');
			for (var i = 0, s = allowed.length;i<s;i++)
			{
				if (allowed[i] === prefRes[1])
				{
					hasPrefix = true;
					prefix = prefRes[1];
					str = str.replace(prefRgx,'');
					break;
				}
			}
			if (!hasPrefix)
			{
				str = str.replace(prefRgx,'');
				hasErrors = true;
				message = "Сетевой протокол не может быть: <b>'"+prefRes[1]+"'</b>. Укажите ссылку с одним из следующих протоколов: ("+allowed.join(', ')+")";
			}
		}
		
		// Check WWW		
		var wwwRgx = new RegExp(/^([w]{1,}\.)/),
			wwwRes = wwwRgx.exec(str);
		if(wwwRes !== null)
		{
			wwwRes_ = wwwRes[1];
			if(wwwRes_.length==4)
			{
				hasWww = true;
				str = str.replace(wwwRgx,'');
			}
			else
			{
				hasErrors = true;
				message = "Префикс не может быть: <b>'"+wwwRes_+"'</b>.";
				if(wwwRes_.length>4)
				{
					message += " Если адрес домена введен верно - укажите перед ним префикс <b>'www.'</b>.";
				}
				else
				{
					message += " Укажите префикс из трёх символов <b>'www.'</b>.";
				}
			}
		}
		
		// Check Domain		
		var domainRgx = new RegExp(/^([^\/]*)/),
			domainRes = domainRgx.exec(str);
		if(fileRes !== null)
		{
			// TODO: Проверка находится ли домен в списке разрешенных\запрещенных 
			var segments = domainRes[1].split('.');
			if(segments.length > 1)
			{
				domain = domainRes[1];
				str = str.replace(domainRgx,'');
			}
			else
			{
				hasErrors = true;
				message = "Доменное имя не может быть: <b>'"+domainRes[1]+"'</b>. Укажите ссылку с доменным именем, состоящим из, не менее 2-х сегментов.";
			}
		}
		
		// Check File		
		var fileRgx = new RegExp(/\/?([^\?]*)/),
			fileRes = fileRgx.exec(str);
		if(fileRes !== null)
		{
			hasFile = true;
			file = fileRes[1];
			str = str.replace(fileRgx,'');
		}
		
		// Check Query		
		var queryRgx = new RegExp(/\?(.*)$/),
			queryRes = queryRgx.exec(str);
		if(queryRes !== null)
		{
			hasQuery = true;
			query = queryRes[1];
			str = str.replace(queryRgx,'');
		}
		
		if(hasErrors)
		{
			return message;
		}
		//alert(((hasPrefix) ? prefix+'://' : '')+((hasWww) ? 'www.' : '')+domain+((hasFile) ? file : '')+((hasQuery) ? '?'+query : ''));
		return true;
	}	
	
	var currencies = <?= json_encode($countries); ?>;
	var selectedCurrency = '';
	//var countryFrom = '';
	var countryTo = '';
	var cityTo = '';
</script>