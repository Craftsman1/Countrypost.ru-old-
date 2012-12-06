<div class='content smallheader'>
	<? Breadcrumb::showCrumbs(); ?>
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
        $.cpField = function()
        {
            var fObj = this;

            this.useDd = false;
            this.needCheck = '';
            this.minValue = null;
            this.maxValue = null;
            this.element = '';
            this.value = '';
            this.id = '';
            this.name = '';
            this.onChange = null;

            var validations = [],
                    fieldValidate = function()
                    {
                        fObj.value = fObj.element.val();
                        var fld = (fObj.useDd) ? $('#'+fObj.element.attr('id')+'_msdd') : fObj.element,
                                hasErrors = false;

                        $.each(validations, function(k, v) {
                            //expression и message
                            var expression = v.expression.replace(/VAL/g, '"'+fObj.value+'"');

                            var fn = 'function check() { '+expression+' } check();';

                            if(eval(fn))
                            {
                                removeFieldError(fld);
                            }
                            else
                            {
                                addFieldError(fld, v.message);
                                hasErrors = true;
                            }
                        });

                        return (hasErrors) ? false : true;
                    },
                    addFieldError = function(field, message)
                    {
                        $("#profileProgress").hide();
                        if (!field.hasClass('ErrorField'))
                        {
                            errorMsg = $('<span class="ValidationErrors">'+message+'</span>');
                            field.addClass('ErrorField').after(errorMsg);
                        }
                    },
                    removeFieldError = function(field)
                    {
                        var nextElement = field.next();
                        if (field.hasClass('ErrorField'))
                        {
                            field.removeClass('ErrorField');
                        }
                        if(nextElement.hasClass('ValidationErrors'))
                        {
                            nextElement.remove();
                        }
                    }

            this.init = function(args)
            {
                if (typeof(args.onChange) == "function")
                {
                    fObj.onChange = args.onChange;
                }

                if (args.useDd)
                {
                    fObj.useDd = args.useDd
                }

                if (args.needCheck)
                {
                    fObj.needCheck = args.needCheck;
                }

                if (args.maxValue)
                {
                    fObj.maxValue = args.maxValue;
                }

                if (args.minValue)
                {
                    fObj.minValue = args.minValue;
                }

                if (args.object)
                {
                    fObj.element = args.object;
                    fObj.value = args.object.val();
                    fObj.id = fObj.element.attr('id');
                    fObj.name = fObj.element.attr('name');

                    if (fObj.element[0].tagName == 'SELECT' && fObj.useDd)
                    {
                        fObj.element.bind('change', fieldValidate);
                    }
                    else
                    {
                        fObj.element.bind('change, blur', fieldValidate);
                    }

                    if (typeof(fObj.onChange) == 'function')
                    {
                        fObj.element.bind('change', fObj.onChange);
                    }

                    if (fObj.useDd)
                    {
                        fObj.element.attr('onchange' ,"").msDropDown({mainCSS:'idd'});
                    }

                    if (fObj.needCheck == 'number')
                    {
                        fObj.element.keypress(function(event){validate_number(event);})
                    }

                    if (fObj.needCheck == 'float')
                    {
                        fObj.element.keypress(function(event){validate_float(event);})
                    }

                    if (fObj.maxValue !== null)
                    {
                        fObj.element.keypress(function(event){
                            if (parseInt(fObj.element.val(), 10) > fObj.maxValue)
                            {
                                fObj.element.val(fObj.maxValue);
                            }
                            else if (fObj.element.val() == '')
                            {
                                fObj.element.val(0);
                            }
                        })
                    }

                    if (fObj.minValue !== null)
                    {
                        fObj.element.keypress(function(event){
                            if (parseInt(fObj.element.val(), 10) < fObj.minValue || isNaN(fObj.element.val()) || fObj.element.val() == '')
                            {
                                fObj.element.val(fObj.minValue);
                            }
                        })
                    }
                }
                else
                {
                    alert("Необходимо указать элемент поля заказа");
                }
            }

            this.validate = function(args)
            {
                if (args.expression && args.message)
                    validations.push(args);
                else
                    alert("Необходимо указать (expression и message) детали валидации");
            }

            this.check = function()
            {
                return fieldValidate();
            }

            this.val = function ()
            {
                return fObj.element.val();
            }
        }

        $.cpCart = function ()
        {
            var cObj = this;

            var cObjCurrency = '';

            var cartItem = function (args)
            {
                var iObj = this;

                iObj.id = 0;
                iObj.name = '';
                iObj.price = 0;
                iObj.delivery = 0;
                iObj.weight = 0;
                iObj.amount = 0;
                iObj.currency = cObjCurrency;

                if (args)
                {
                    if (args.id) iObj.id = args.id;
                    if (args.name) iObj.name = args.name;
                    if (args.price) iObj.price = args.price;
                    if (args.delivery) iObj.delivery = args.delivery;
                    if (args.weight) iObj.weight = args.weight;
                    if (args.amount) iObj.amount = args.amount;
                    if (args.currency) iObj.currency = args.currency;
                }
            }

            cObj.items = [];

            cObj.add = function(args)
            {
                if (args)
                {
                    var item = new cartItem(args);

                    cObj.items.push(item);
                }
            }

            cObj.addCustom = function(args)
            {
                if (args)
                {
                    var item = new cartItem(args);

                    $.each(args, function(k, v)
                    {
                        if (typeof(v) == 'string') v = '"'+v+'"';
                        eval('item.'+k+'='+v+';')
                    });

                    cObj.items.push(item);
                }
            }

            cObj.update = function(args)
            {
                if (args && args.id)
                {

                    for (var i = 0, n = cObj.items.length; i < n; i++)
                    {
                        if (cObj.items[i].id == args.id)
                        {
                            $.each(args, function(k, v)
                            {
                                if (k != 'id')
                                {
                                    if (typeof(v) == 'string') v = '"'+v+'"';
                                    eval('cObj.items[i].'+k+'='+v+';');
                                }
                            });
                        }
                    }
                }
            }

            cObj.delete = function(id)
            {
                for (var i = 0, n = cObj.items.length; i < n; i++)
                {
                    cObj.items.splice(i, 1);
                    break;
                }
            }

            cObj.calcTotals = function()
            {
                var result = {price : 0, delivery : 0, weight : 0, amount : 0, currency : cObjCurrency};
                $.each(cObj.items, function (k, v)
                {
                    result.price = parseFloat(result.price, 10) + parseFloat(v.price, 10);
                    result.delivery = parseFloat(result.delivery, 10) + parseFloat(v.delivery, 10);
                    result.weight = parseInt(result.weight, 10) + parseInt(v.weight, 10);
                    result.amount = parseInt(result.amount, 10) + parseInt(v.amount, 10);
                });
                return result;
            }

            cObj.updateCurrency = function(currency)
            {
                cObjCurrency = currency;

                $.each(cObj.items, function(k, v)
                {
                    v.currency = currency;
                });
            }
        }

        /*
        var ct = new $.cpCart();
        ct.add({id : 666, name : 'XXX', price : 100, delivery : 10, weight : 15, amount : 1});
        ct.add({id : 667, name : 'XXY', price : 200, delivery : 24, weight : 4, amount : 1});
        var totals = ct.calcTotals();
        ct.update({id : 666, name : 'XXZ', price : 300, delivery : 12, weight : 22, amount : 1});
        var totals = ct.calcTotals();
        ct.delete(666);
        */

        $.cpOrder = function(blankOrderData)
        {
            var oObj = this,
                    blankOrderData = (blankOrderData) ? blankOrderData : null;
            fieldByName = function(fields, name)
            {
                var f = null;
                $.each(fields, function(k, v) {
                    if (v.name == name)
                    {
                        f = v;
                    }
                });
                return f;
            }, // End fieldByName
                    checkOrder = function()
                    {
                        $.each(oObj.fields, function(k, v) {
                            if (!v.check())
                            {
                                errorFields.push(v);
                            }
                        });
                        return (errorFields.length > 0) ? false : true;
                    }, // End checkOrder
                    scrollFirstError = function()
                    {
                        if (errorFields.length > 0)
                        {
                            var offset = errorFields[0].element.offset();

                            $(window).scrollTop(offset.top);

                            errorFields = [];
                        }
                    }, // End scrollFirstError
                    errorFields = [],

                // Заказы
                    initOnline = function()
                    {
                        oObj.options.type = 'online';
                        oObj.options.title = 'Добавление нового Online заказа';
                        oObj.options.cart = new $.cpCart();

                        var itemOnline = function()
                        {
                            var iObj = this,
                                    addItemProgress = function()
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
                                    removeItemProgress = function()
                                    {
                                        $('div.progress').remove();
                                    }, // End removeItemProgress
                                    bindAddItem = function()
                                    {
                                        // Добавляем обработчик к кнопке добавления товара к заказу
                                        $('#addItemOnline').bind('click', iObj.add);
                                    }, // End bindAddItem
                                    unbindAddItem = function()
                                    {
                                        // Убираем обработчик у кнопки добавления товара к заказу
                                        $('#addItemOnline').unbind('click');
                                    }, // End unbindAddItem
                                    checkItem = function()
                                    {
                                        $.each(iObj.fields, function(k, v)
                                        {
                                            if (!v.check())
                                            {
                                                errorFields.push(v);
                                            }
                                        });
                                        return (errorFields.length > 0) ? false : true;
                                    }, // End checkItem
                                    formFieldsClear = function()
                                    {
                                        $.each(iObj.fields, function(k, v)
                                        {
                                            if (v.element.attr('type') == 'checkbox')
                                            {
                                                v.element.removeAttr('checked');
                                            }
                                            else
                                            {
                                                if (v.minValue)
                                                {
                                                    v.element.val(v.minValue);
                                                }
                                                else
                                                {
                                                    v.element.val('');
                                                }
                                            }
                                        })

                                        $('.screenshot_link_box,.screenshot_uploader_box').hide('slow');
                                        $('.screenshot_switch').show('slow');
                                    } // End formFieldsClear


                            iObj.fields = [];

                            iObj.init = function()
                            {
                                // Ссылка на товар
                                olink = new $.cpField();
                                olink.init({
                                    object : $('#olink')
                                });
                                olink.validate({
                                    expression:'if (VAL == "") { return false; } else { var msg = urlValidate(VAL); if (msg!==true) { v.message = msg; return false; } else { return true; } }',
                                    message:'Необходимо указать ссылку на товар'
                                });
                                iObj.fields.push(olink);

                                // Наименование товара
                                oname = new $.cpField();
                                oname.init({
                                    object : $('#oname')
                                });
                                oname.validate({
                                    expression:'if (VAL == "") { return false; } else { return true; }',
                                    message:'Необходимо указать название товара'
                                });
                                iObj.fields.push(oname);

                                // Цена товара
                                oprice = new $.cpField();
                                oprice.init({
                                    object : $('#oprice'),
                                    needCheck : 'float',
                                    onChange : function ()
                                    {
                                        $(this).val(parseFloat($(this).val(), 10));
                                    }
                                });
                                oprice.validate({
                                    expression:'if (VAL == "" || VAL == 0) { return false; } else { return true; }',
                                    message:'Необходимо указать цену товара'
                                });
                                iObj.fields.push(oprice);

                                // Местная доставка
                                odeliveryprice = new $.cpField();
                                odeliveryprice.init({
                                    object : $('#odeliveryprice'),
                                    needCheck : 'float',
                                    onChange : function ()
                                    {
                                        $(this).val(parseFloat($(this).val(), 10));
                                    }
                                });
                                odeliveryprice.validate({
                                    expression:'if (VAL == "" || VAL == 0) { return false; } else { return true; }',
                                    message:'Необходимо указать местную доставку товара'
                                });
                                iObj.fields.push(odeliveryprice);

                                // Примерный вес
                                oweight = new $.cpField();
                                oweight.init({
                                    object : $('#oweight'),
                                    needCheck : 'number',
                                    maxValue : 99999,
                                    onChange : function ()
                                    {
                                        $(this).val(parseInt($(this).val(), 10));
                                    }
                                });
                                oweight.validate({
                                    expression:'if (VAL == "" || VAL == 0) { return false; } else { return true; }',
                                    message:'Необходимо указать примерный вес товара'
                                });
                                iObj.fields.push(oweight);

                                // Цвет
                                ocolor = new $.cpField();
                                ocolor.init({
                                    object : $('#ocolor')
                                });
                                iObj.fields.push(ocolor);

                                // Размер
                                osize = new $.cpField();
                                osize.init({
                                    object : $('#osize')
                                });
                                iObj.fields.push(osize);

                                // Количество
                                oamount = new $.cpField();
                                oamount.init({
                                    object : $('#oamount'),
                                    needCheck : 'number',
                                    minValue : 1,
                                    maxValue : 9999
                                });
                                iObj.fields.push(oamount);

                                // Скриншот
                                oimg = new $.cpField();
                                oimg.init({
                                    object : $('#oimg')
                                });
                                iObj.fields.push(oimg);

                                ofile = new $.cpField();
                                ofile.init({
                                    object : $('#ofile')
                                });
                                iObj.fields.push(ofile);

                                // Нужно ли фото товара
                                foto_requested = new $.cpField();
                                foto_requested.init({
                                    object : $('#foto_requested')
                                });
                                iObj.fields.push(foto_requested);

                                // Комментарий к товару
                                ocomment = new $.cpField();
                                ocomment.init({
                                    object : $('#ocomment')
                                });
                                iObj.fields.push(ocomment);
                            }

                            iObj.add = function ()
                            {
                                unbindAddItem();

                                var olink = fieldByName(iObj.fields, 'olink').val(),
                                    oname = fieldByName(iObj.fields, 'oname').val(),
                                    foto_requested = fieldByName(iObj.fields, 'foto_requested').val(),
                                    oamount = fieldByName(iObj.fields, 'oamount').val(),
                                    osize = fieldByName(iObj.fields, 'osize').val(),
                                    ocolor = fieldByName(iObj.fields, 'ocolor').val(),
                                    ofile = fieldByName(iObj.fields, 'userfile').val(),
                                    oimg = fieldByName(iObj.fields, 'userfileimg').val(),
                                    oprice = fieldByName(iObj.fields, 'oprice').val(),
                                    odeliveryprice = fieldByName(iObj.fields, 'odeliveryprice').val(),
                                    oweight = fieldByName(iObj.fields, 'oweight').val(),
                                    ocomment = fieldByName(iObj.fields, 'ocomment').val();

                                // Рисуем новый товар
                                var snippet = $("" +
                                        "<tr class='snippet'>" +
                                        "<td id='odetail' class=''><input type='checkbox' value='1' /><br /><span class='itemId'></span></td>" +
                                        "<td class='oname'><a target='_blank' href='" + olink + "'>" + oname + "</a>" +
                                        (foto_requested == 1 ? " (требуется фото товара)" : "") +
                                        "<br/><b>Количество</b>: " + oamount +
                                        ((osize) ? " <b>Размер</b>: " + osize : "") +
                                        ((ocolor != "") ? " <b>Цвет</b>: " + ocolor : "") +
                                        ((ocomment != "") ? "<br/><b>Комментарий</b>: " + ocomment + "</td>" : "") +
                                        "<td class='oimg" + (ofile ? " userfile" : "") + "'>" + oimg + "</td>" +
                                        "<td class='oprice'>" + oprice + " <span class='label currency'>" + getSelectedCurrency() + "</span></td>" +
                                        "<td class='odeliveryprice'>" + odeliveryprice + " <span class='label currency'>" + getSelectedCurrency() + "</span></td>" +
                                        "<td class='oweight'>" + oweight + " г</td>" +
                                        "<td class='oedit'><a class='edit_icon' style='cursor: pointer;'><img border='0' src='/static/images/comment-edit.png' title='Изменить'></a><br /><a class='delete_icon'><img border='0' src='/static/images/delete.png' style='cursor: pointer;' title='Удалить'></a></td>" +
                                        "</tr>");
                                $('#new_products tr:first').after(snippet);

                                // Отправляем на сервер данные
                                $('#onlineItemForm').ajaxForm({
                                    target: $('#onlineOrderForm').attr('action'),
                                    type: 'POST',
                                    dataType: 'json',
                                    iframe: true,
                                    beforeSubmit: function(formData, jqForm, options)
                                    {
                                        if (!checkOrder() || !checkItem())
                                        {
                                            scrollFirstError();
                                            bindAddItem();
                                            $('.snippet').remove();
                                            // Если товаров больше нет сворачиваем таблицу товаров
                                            var prows = $('.'+oObj.options.type+'_order_form #new_products tr');
                                            if (prows.length < 4)
                                            {
                                                $('.'+oObj.options.type+'_order_form #detailsForm').hide('slow');
                                            }

                                            return false;
                                        }

                                        return true;
                                    },
                                    success: function(response)
                                    {
                                        removeItemProgress();

                                        if (response)
                                        {
                                            // Ответ не является числовым значением
                                            if ( isNaN(response.odetail_id) || isNaN(response.order_id) )
                                            {
                                                error('top', response);
                                            }
                                            // Все в порядке, добавляем товар
                                            else
                                            {
                                                // проставляем всюду Id заказа
                                                $('input.order_id').val(response.order_id);

                                                var screenshot_code = '';
                                                if(response.odetail_img && response.odetail_img.match(/http:\/\//g))
                                                {
                                                    screenshot_code = '<a href="'+response.odetail_img+'" target="BLANK">Просмотреть</a>'
                                                }
                                                else if (parseInt(response.odetail_img, 10) != 0)
                                                {
                                                    screenshot_code = "<a href='javascript:void(0)' onclick='setRel(" + response.odetail_img + ");'>Просмотреть</a> " +
                                                            "<a rel='lightbox_" + response.odetail_img + "' href='/client/showScreen/" + response.odetail_img + "' style='display:none;'>Посмотреть</a>";
                                                }
                                                else
                                                {
                                                    screenshot_code = '';
                                                }

                                                // Прикручиваем обработчики к кнопкам
                                                snippet.find('a.delete_icon')
                                                        .click(function() {
                                                            iObj.delete(response.odetail_id);
                                                        });
                                                snippet.find('a.edit_icon')
                                                        .click(function() {
                                                            iObj.edit(response.odetail_id);
                                                        });

                                                // Подставляем изображение или ссылку на него
                                                snippet.find('.userfile:last')
                                                        .html(screenshot_code)
                                                        .removeClass('userfile');

                                                snippet.removeClass('snippet').attr('item-id',response.odetail_id);

                                                oObj.options.cart.add({
                                                    id : response.odetail_id,
                                                    price : oprice,
                                                    delivery : odeliveryprice,
                                                    weight : oweight,
                                                    amount : oamount
                                                });

                                                // пересчитываем заказ
                                                oObj.updateTotals();

                                                $('#odetail')
                                                        .attr({'id' : '#odetail'+response.odetail_id})
                                                        .find('.itemId')
                                                        .text(response.odetail_id);

                                                success('top', 'Товар №' + response.odetail_id + ' успешно добавлен в корзину.');

                                                // чистим форму
                                                if (true) //debug only
                                                {
                                                    formFieldsClear();
                                                }//debug only

                                                // Отображаем список товаров
                                                $('#detailsForm').show();
                                            }
                                        }
                                        // Ответ не был получен
                                        else
                                        {
                                            error('top', 'Товар не добавлен. Заполните все поля и попробуйте еще раз.');
                                        }
                                    },
                                    error: function(response)
                                    {
                                        removeItemProgress();

                                        if (response.status == 0)
                                        {
                                            error('top', 'Товар не добавлен. Отсутствует подключение к интернету.');
                                        }
                                        else
                                        {
                                            error('top', 'Товар не добавлен. Заполните все поля и попробуйте еще раз.');
                                        }
                                    }, // End error
                                    complete : function()
                                    {
                                        bindAddItem();
                                    }
                                });

                                addItemProgress();

                                $('#onlineItemForm').submit();
                            }

                            iObj.delete = function (itemId)
                            {
                                if (confirm("Вы уверены, что хотите удалить товар №" + itemId + "?"))
                                {
                                    var order = this;
                                    $.post('<?= $selfurl ?>deleteProduct/' + itemId)
                                            .success(function()
                                            {
                                                oObj.options.cart.delete(itemId);
                                                // Удаляем строку товара
                                                $('#detailsForm tr[item-id="'+itemId+'"]').remove();
                                                // Если товаров больше нет сворачиваем таблицу товаров
                                                var prows = $('.'+oObj.options.type+'_order_form #new_products tr');
                                                if (prows.length < 4)
                                                {
                                                    $('.'+oObj.options.type+'_order_form #detailsForm').hide('slow');
                                                }
                                                oObj.updateTotals();
                                                success('top', 'Товар успешно удален.');
                                            })
                                            .error(function(args) {
                                                error('top', 'Товар не удален. Проверьте подключение к интернету и повторите попытку.');
                                            });
                                }
                            }

                            iObj.edit = function (itemId)
                            {
                                success('top', "В разработке.");
                            }

                            iObj.drawRow = function (item)
                            {
                                if(item.oimg && item.oimg.match(/http:\/\//g))
                                {
                                    item.oimg = '<a href="'+item.oimg+'" target="BLANK">Просмотреть</a>'
                                }
                                else if (item.oimg != null && parseInt(item.oimg, 10) > 0)
                                {
                                    item.oimg = "<a href='javascript:void(0)' onclick='setRel(" + item.oimg + ");'>Просмотреть</a> " +
                                            "<a rel='lightbox_" + item.oimg + "' href='/client/showScreen/" + item.oimg + "' style='display:none;'>Посмотреть</a>";
                                }
                                else
                                {
                                    item.oimg = '';
                                }

                                // Рисуем новый товар
                                var snippet = $("" +
                                        "<tr class='snippet' item-id='"+item.id+"'>" +
                                        "<td id='odetail"+item.id+"' class=''><input type='checkbox' value='1' /><br /><span class='itemId'>"+item.id+"</span></td>" +
                                        "<td class='oname'><a target='_blank' href='" + item.olink + "'>" + item.name + "</a>" +
                                        (item.foto_requested == 1 ? " (требуется фото товара)" : "") +
                                        "<br/><b>Количество</b>: " + item.amount +
                                        ((item.osize) ? " <b>Размер</b>: " + item.osize : "") +
                                        ((item.ocolor != "") ? " <b>Цвет</b>: " + item.ocolor : "") +
                                        ((item.ocomment != "") ? "<br/><b>Комментарий</b>: " + item.ocomment + "</td>" : "") +
                                        "<td class='oimg'>" + item.oimg + "</td>" +
                                        "<td class='oprice'>" + item.price + " <span class='label currency'>" + getSelectedCurrency() + "</span></td>" +
                                        "<td class='odeliveryprice'>" + item.delivery + " <span class='label currency'>" + getSelectedCurrency() + "</span></td>" +
                                        "<td class='oweight'>" + item.weight + " г</td>" +
                                        "<td class='oedit'><a class='edit_icon' style='cursor: pointer;'><img border='0' src='/static/images/comment-edit.png' title='Изменить'></a><br /><a class='delete_icon'><img border='0' src='/static/images/delete.png' style='cursor: pointer;' title='Удалить'></a></td>" +
                                        "</tr>");
                                $('#new_products tr:first').after(snippet);

                                // Прикручиваем обработчики к кнопкам
                                snippet.find('a.delete_icon')
                                        .click(function() {
                                            iObj.delete(item.id);
                                        });
                                snippet.find('a.edit_icon')
                                        .click(function() {
                                            iObj.edit(item.id);
                                        });
                            }

                            if (blankOrderData)
                            {
                                $.each(blankOrderData.details, function(k, v)
                                {
                                    if (selectedCurrency == '')
                                    {
                                        for (var index in currencies)
                                        {
                                            var currency = currencies[index];

                                            if (v.odetail_country == currency['country_id'])
                                            {
                                                selectedCurrency = currency['country_currency'];
                                                break;
                                            }
                                        }
                                    }

                                    oObj.options.cart.addCustom({
                                        id :v.odetail_id,
                                        name : v.odetail_product_name,
                                        price : v.odetail_price,
                                        delivery : v.odetail_pricedelivery,
                                        weight : v.odetail_weight,
                                        amount : v.odetail_product_amount,
                                        currency : selectedCurrency,
                                        olink : v.odetail_link,
                                        ocolor : v.odetail_product_color,
                                        osize : v.odetail_product_size,
                                        oimg : v.odetail_img,
                                        foto_requested : v.odetail_foto_requested,
                                        ocomment : v.odetail_comment
                                    });
                                });

                                $.each(oObj.options.cart.items, function (k, item)
                                {
                                    iObj.drawRow(item);
                                });

                                oObj.updateTotals();

                                // Отображаем список товаров
                                $('#detailsForm').show();
                            }
                        },

                        updateProductForm = function()
                        {
                            var country_from = $('#country_from_online').val(),
                                country_to = $('#country_to_online').val(),
                                city = $('#city_to').val();
                            var countryFrom = $('#onlineItemForm input.countryFrom'),
                                countryTo = $('#onlineItemForm input.countryTo'),
                                cityTo = $('#onlineItemForm input.cityTo');

                            countryFrom.val(country_from);
                            countryTo.val(country_to);
                            cityTo.val(city);
                        }
                        // начинаем инициализацию

                        // Страна поступления, поле "Заказать из"
                        country_from = new $.cpField();
                        country_from.init({
                            object : $('#country_from_online'),
                            useDd : true,
                            onChange : function ()
                            {
                                var id = $(this).val();
                                var prevCurrency = selectedCurrency;

                                for (var index in currencies)
                                {
                                    var currency = currencies[index];

                                    if (id == currency['country_id'])
                                    {
                                        selectedCurrency = currency['country_currency'];
                                        $('input.countryFrom').val(id);
                                        $('.currency').html(selectedCurrency);
                                        $('.order_currency').val(selectedCurrency);
                                        oObj.options.cart.updateCurrency(selectedCurrency);
                                        oObj.updateTotals();
                                        break;
                                    }
                                }
                                updateProductForm();
                            }
                        });
                        country_from.validate({
                            expression:'if (VAL == 0) { return false; } else { return true; }',
                            message:'Необходимо выбрать страну поступления'
                        });
                        oObj.fields.push(country_from);

                        // Страна доставки, поле "В какую страну доставить"
                        country_to = new $.cpField();
                        country_to.init({
                            object : $('#country_to_online'),
                            useDd : true,
                            onChange : function ()
                            {
                                var id = $(this).val();
                                for (var index in currencies)
                                {
                                    var currency = currencies[index];

                                    if (id == currency['country_id'])
                                    {
                                        $('input.countryTo').val(id);
                                        countryTo = currency['country_name'];
                                        oObj.updateTotals();
                                        break;
                                    }
                                }
                                updateProductForm();
                            }
                        });
                        country_to.validate({
                            expression:'if (VAL == 0) { return false; } else { return true; }',
                            message:'Необходимо выбрать страну доставки'
                        });
                        oObj.fields.push(country_to);

                        // Город доставки, поле "Город доставки"
                        city_to = new $.cpField();
                        city_to.init({
                            object : $('#city_to'),
                            onChange : function ()
                            {
                                updateProductForm();
                            }
                        });
                        city_to.validate({
                            expression:'if (VAL == "") { return false; } else { return true; }',
                            message:'Необходимо выбрать город доставки'
                        });
                        oObj.fields.push(city_to);

                        // Cпособ доставки, поле "Cпособ доставки"
                        requested_delivery = new $.cpField();
                        requested_delivery.init({
                            object : $('#requested_delivery')
                        });
                        oObj.fields.push(requested_delivery);

                        var item = new itemOnline();
                        item.init();

                        $('#addItemOnline').unbind('click').bind('click', item.add);

                        $('#checkoutOrder').bind('click', saveOnline);

                        // Отображаем форму
                        $('div.order_type_selector').hide();
                        $('h2#page_title').html(oObj.options.title);
                        $("div.online_order_form").show('slow');

                    }, // End initOnline

                    saveOnline = function()
                    {
                        $('#onlineOrderForm').ajaxForm({
                            target: $('#orderForm').attr('action'),
                            type: 'POST',
                            dataType: 'html',
                            iframe: true,
                            beforeSubmit: function(formData, jqForm, options)
                            {
                                if (!checkOrder())
                                {
                                    scrollFirstError();
                                    return false;
                                }
                                return true;
                            },
                            success: function(response)
                            {
                                if (response)
                                {
                                    error('top', 'Заказ не добавлен. '+response);
                                }
                                else
                                {
                                    var order_id = $('input.order_id').val();
                                    success('top', 'Заказ №' + order_id + ' добавлен! Дождитесь предложений от посредников и выберите лучшее из них.');
                                    window.location = '/client/order/'+order_id;
                                }
                            },
                            error: function(response)
                            {
                                error('top', 'Заказ не добавлен. '+response);
                            }
                        }).submit();
                    },  // End saveOnline
                // Конец Заказы

                    showOrderProgress = function()
                    {
                        // Отображаем прогресс по выбору типа заказа
                        if(obj = $('.'+oObj.options.type+'_order div:first b'))
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
                    hideOrderProgress = function()
                    {
                        // Скрываем прогресс по выбору типа заказа
                        if(obj = $('.'+oObj.options.type+'_order div:first b'))
                        {
                            obj.hide('slow').remove();
                        }
                    }, // End hideOrderProgress
                    createEmptyOrder = function(callback)
                    {
                        $.post('http://cps/main/addEmptyOrder/online', {}, function(responce)
                        {
                            if (parseInt(responce, 10) != 0)
                            {
                                oObj.options.order_id = parseInt(responce, 10);
                                $('input.order_id').val(oObj.options.order_id);
                            }
                            else
                            {
                                error("Невозможно создать новый заказ. Перезагрузите страницу и попробуйте еще раз.");
                                hideOrderProgress();
                            }
                            setTimeout(function() { callback(); }, 100);

                        });
                    } // End getEmptyOrder

            oObj.options =
            {
                order_id : 0,
                type : '',
                title : '',
                cart : null
            } // End options

            oObj.init = function (type)
            {
                switch (type)
                {
                    case 'online' :
                        initOnline();
                        break;
                }
            } // End init

            oObj.updateTotals = function ()
            {
                oObj.options.cart.updateCurrency(selectedCurrency);

                if (countryTo == '')
                {
                    var countryToId = $('#country_to_online').val();

                    $.each (currencies,
                        function (k, v)
                        {
                            if (v.country_id == countryToId)
                            {
                                countryTo = v.country_name;
                            }
                        }
                    )
                }

                var totals = oObj.options.cart.calcTotals(),
                    cityTo = fieldByName(oObj.fields, 'city_to');

                $('.price_total').text(totals.price+' '+totals.currency);
                $('.delivery_total').text(totals.delivery+' '+totals.currency);
                $('.weight_total').text(totals.weight+' г');
                $('.order_totals').text((totals.price+totals.delivery)+' '+totals.currency);
                $('span.countryTo').text(countryTo);
                if (cityTo && cityTo.element.val())
                {
                    $('span.cityTo').text(' ('+cityTo.element.val()+')');
                }
            }

            oObj.fields = [];

            oObj.items = [];

        }

      
    })(jQuery)
  </script>

	<? View::show('main/elements/orders/online'); ?>
	<? View::show('main/elements/orders/offline'); ?>
</div>
<script>

    /*function setCountryFrom(id)
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
    }*/

    // скриншот
    function showScreenshotLink()
    {
        $('.screenshot_link_box').show('slow');
        if ($('.screenshot_link_box').val() == '')
        {
            $('.screenshot_link_box').val('ссылка на скриншот')
        }
        $('.screenshot_switch').hide('slow');
    }

    function showScreenshotUploader()
    {
        $('.screenshot_uploader_box').show('slow');
        if ($('.screenshot_link_box').val() == 'ссылка на скриншот')
        {
            $('.screenshot_link_box').val('')
        }
        $('.screenshot_switch').hide('slow');
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


    var orderData = <?= ($json = json_encode($order_empty_data)) ? $json : '{}' ?>;
    var currencies = <?= json_encode($countries); ?>;
    var selectedCurrency = '<?= $order_currency ?>';
    //var countryFrom = '';
    var countryTo = '';
    var cityTo = '';
</script>