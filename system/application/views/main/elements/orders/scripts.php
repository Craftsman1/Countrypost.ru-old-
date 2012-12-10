
<script type="text/javascript">
(function ($) {
    $.cpField = function () {
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

        var validations = [];

        var fieldValidate = function () {
            fObj.value = fObj.element.val();
            var fld = (fObj.useDd) ? $('#' + fObj.element.attr('id') + '_msdd') : fObj.element,
                    hasErrors = false;

            $.each(validations, function (k, v) {
                //expression и message
                var expression = v.expression.replace(/VAL/g, '"' + fObj.value + '"');

                var fn = 'function check() { ' + expression + ' } check();';

                if (eval(fn)) {
                    removeFieldError(fld);
                }
                else {
                    addFieldError(fld, v.message);
                    hasErrors = true;
                }
            });

            return (hasErrors) ? false : true;
        }

        var addFieldError = function (field, message) {
            $("#profileProgress").hide();
            if (!field.hasClass('ErrorField')) {
                errorMsg = $('<span class="ValidationErrors">' + message + '</span>');
                field.addClass('ErrorField').after(errorMsg);
            }
        }

        var removeFieldError = function (field) {
            var nextElement = field.next();
            if (field.hasClass('ErrorField')) {
                field.removeClass('ErrorField');
            }
            if (nextElement.hasClass('ValidationErrors')) {
                nextElement.remove();
            }
        }

        this.init = function (args) {
            if (typeof(args.onChange) == "function") {
                fObj.onChange = args.onChange;
            }

            if (args.useDd) {
                fObj.useDd = args.useDd
            }

            if (args.needCheck) {
                fObj.needCheck = args.needCheck;
            }

            if (args.maxValue) {
                fObj.maxValue = args.maxValue;
            }

            if (args.minValue) {
                fObj.minValue = args.minValue;
            }

            if (args.object) {
                fObj.element = args.object;
                fObj.value = args.object.val();
                fObj.id = fObj.element.attr('id');
                fObj.name = fObj.element.attr('name');

                if (fObj.element[0].tagName == 'SELECT' && fObj.useDd) {
                    fObj.element.bind('change', fieldValidate);
                }
                else {
                    fObj.element.bind('change, blur', fieldValidate);
                }

                if (typeof(fObj.onChange) == 'function') {
                    fObj.element.bind('change', fObj.onChange);
                }

                if (fObj.useDd) {
                    fObj.element.attr('onchange', "").msDropDown({mainCSS:'idd'});
                }

                if (fObj.needCheck == 'number') {
                    fObj.element.keypress(function (event) {
                        validate_number(event);
                    })
                }

                if (fObj.needCheck == 'float') {
                    fObj.element.keypress(function (event) {
                        validate_float(event);
                    })
                }

                if (fObj.maxValue !== null) {
                    fObj.element.bind('blur', function (event) {
                        if (isNaN(fObj.element.val())) {
                            fObj.element.val(0);
                        }

                        if (parseInt(fObj.element.val(), 10) > fObj.maxValue) {
                            fObj.element.val(fObj.maxValue);
                        }
                        else if (fObj.element.val() == '') {
                            fObj.element.val(0);
                        }
                    })
                }

                if (fObj.minValue !== null) {
                    fObj.element.bind('blur', function (event) {
                        if (isNaN(fObj.element.val())) {
                            fObj.element.val(0);
                        }

                        if (parseInt(fObj.element.val(), 10) < fObj.minValue || isNaN(fObj.element.val()) || fObj.element.val() == '') {
                            fObj.element.val(fObj.minValue);
                        }
                    })
                }
            }
            else {
                alert("Необходимо указать элемент поля заказа");
            }
        }

        this.validate = function (args) {
            if (args.expression && args.message)
                validations.push(args);
            else
                alert("Необходимо указать (expression и message) детали валидации");
        }

        this.check = function () {
            return fieldValidate();
        }

        this.val = function () {
            return fObj.element.val();
        }
    }

    $.cpCart = function () {
        var cObj = this;

        var cObjCurrency = '';

        var cartItem = function (args) {
            var iObj = this;

            iObj.id = 0;
            iObj.name = '';
            iObj.price = 0;
            iObj.delivery = 0;
            iObj.weight = 0;
            iObj.amount = 0;
            iObj.currency = cObjCurrency;

            if (args) {
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

        cObj.add = function (args) {
            if (args) {
                var item = new cartItem(args);

                cObj.items.push(item);
            }
        }

        cObj.addCustom = function (args) {
            if (args) {
                var item = new cartItem(args);

                $.each(args, function (k, v) {
                    if (typeof(v) == 'string') v = '"' + v + '"';
                    eval('item.' + k + '=' + v + ';')
                });

                cObj.items.push(item);
            }
        }

        cObj.createCustomItem = function (args) {
            if (args) {
                var item = new cartItem(args);

                $.each(args, function (k, v) {
                    if (typeof(v) == 'string') v = '"' + v + '"';
                    eval('item.' + k + '=' + v + ';')
                });

                return item;
            }
        }

        cObj.getById = function (id) {
            var out = null;
            $.each(cObj.items,
                    function (k, v) {
                        if (v.id == id) out = v;
                    }
            )
            return out;
        }

        cObj.update = function (args) {
            if (args && args.id) {

                for (var i = 0, n = cObj.items.length; i < n; i++) {
                    if (cObj.items[i].id == args.id) {
                        $.each(args, function (k, v) {
                            if (k != 'id') {
                                if (typeof(v) == 'string') v = '"' + v + '"';
                                eval('cObj.items[i].' + k + '=' + v + ';');
                            }
                        });
                    }
                }
            }
        }

        cObj.delete = function (id) {
            for (var i = 0, n = cObj.items.length; i < n; i++) {
                cObj.items.splice(i, 1);
                break;
            }
        }

        cObj.calcTotals = function () {
            var result = {price:0, delivery:0, weight:0, amount:0, currency:cObjCurrency};
            $.each(cObj.items, function (k, v) {
                result.price = parseFloat(result.price, 10) + parseFloat(v.price, 10);
                result.delivery = parseFloat(result.delivery, 10) + parseFloat(v.delivery, 10);
                result.weight = parseInt(result.weight, 10) + parseInt(v.weight, 10);
                result.amount = parseInt(result.amount, 10) + parseInt(v.amount, 10);
            });
            return result;
        }

        cObj.updateCurrency = function (currency) {
            cObjCurrency = currency;

            $.each(cObj.items, function (k, v) {
                v.currency = currency;
            });
        }
    }

    $.cpOrder = function (blankOrderData) {
        var oObj = this;

        var blankOrderData = (blankOrderData) ? blankOrderData : null;

        var fieldByName = function (fields, name) {
            var f = null;
            $.each(fields, function (k, v) {
                if (v.name == name) {
                    f = v;
                }
            });
            return f;
        } // End fieldByName

        var checkOrder = function () {
            $.each(oObj.fields, function (k, v) {
                if (!v.check()) {
                    errorFields.push(v);
                }
            });
            return (errorFields.length > 0) ? false : true;
        } // End checkOrder

        var scrollFirstError = function () {
            if (errorFields.length > 0) {
                var offset = errorFields[0].element.offset();

                $(window).scrollTop(offset.top);

                errorFields = [];
            }
        } // End scrollFirstError

        var updateProductForm = function ()
        {
            var country_from = $('#country_from_'+oObj.options.type+'').val(),
                    country_to = $('#country_to_'+oObj.options.type+'').val(),
                    city = $('#city_to_'+oObj.options.type+'').val();
            var countryFrom = $('#'+oObj.options.type+'ItemForm input.countryFrom'),
                    countryTo = $('#'+oObj.options.type+'ItemForm input.countryTo'),
                    cityTo = $('#'+oObj.options.type+'ItemForm input.cityTo');

            countryFrom.val(country_from);
            countryTo.val(country_to);
            cityTo.val(city);
        } // End updateProductForm

        var checkItem = function (iObj, errorFields)
        {
            $.each(iObj.fields, function (k, v) {
                if (!v.check()) {
                    errorFields.push(v);
                }
            });
            return (errorFields.length > 0) ? false : true;
        } // End checkItem

        var formFieldsClear = function (iObj)
        {
            $.each(iObj.fields, function (k, v) {
                if (v.element.attr('type') == 'checkbox') {
                    v.element.removeAttr('checked');
                }
                else {
                    if (v.minValue) {
                        v.element.val(v.minValue);
                    }
                    else {
                        v.element.val('');
                    }
                }
            })

            $('.screenshot_link_box,.screenshot_uploader_box').hide('slow');
            $('.screenshot_switch').show('slow');
        } // End formFieldsClear

        var errorFields = [];

        // Заказы
        var initOnline = function ()
        {
            oObj.options.type = 'online';
            oObj.options.title = 'Добавление нового Online заказа';
            oObj.options.cart = new $.cpCart();

            var itemOnline = function ()
                    {
                        var iObj = this;

                        var addItemProgress = function () {
                            var parent = $('#odetail');
                            var progress = $("<div class='progress'> " +
                                    "<img class='float product_progress_bar' src='/static/images/lightbox-ico-loading.gif'/> " +
                                    "</div>");
                            progress.css({
                                background:'none',
                                width:'32px',
                                height:'32px',
                                'margin-top':'6px',
                                float:'left'
                            });

                            parent.append(progress);
                        } // End addItemProgress

                        var removeItemProgress = function () {
                            $('div.progress').remove();
                        } // End removeItemProgress

                        var bindAddItem = function () {
                            // Добавляем обработчик к кнопке добавления товара к заказу
                            $('#addItemOnline').bind('click', iObj.add);
                        } // End bindAddItem

                        var unbindAddItem = function () {
                            // Убираем обработчик у кнопки добавления товара к заказу
                            $('#addItemOnline').unbind('click');
                        } // End unbindAddItem


                        iObj.fields = [];

                        iObj.init = function () {
                            // Ссылка на товар
                            olink = new $.cpField();
                            olink.init({
                                object:$('#olink')
                            });
                            olink.validate({
                                expression:'if (VAL == "") { return false; } else { var msg = urlValidate(VAL); if (msg!==true) { v.message = msg; return false; } else { return true; } }',
                                message:'Необходимо указать ссылку на товар'
                            });
                            iObj.fields.push(olink);

                            // Наименование товара
                            oname = new $.cpField();
                            oname.init({
                                object:$('#oname')
                            });
                            oname.validate({
                                expression:'if (VAL == "") { return false; } else { return true; }',
                                message:'Необходимо указать название товара'
                            });
                            iObj.fields.push(oname);

                            // Цена товара
                            oprice = new $.cpField();
                            oprice.init({
                                object:$('#oprice'),
                                needCheck:'float',
                                onChange:function () {
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
                                object:$('#odeliveryprice'),
                                needCheck:'float',
                                onChange:function () {
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
                                object:$('#oweight'),
                                needCheck:'number',
                                /*maxValue : 99999,*/
                                onChange:function () {
                                    /*if (!isNaN($(this).val()))
                                    {
                                        $(this).val(parseInt($(this).val(), 10));
                                    }*/
                                    if (!isNaN($(this).val()) && parseInt($(this).val(), 10) > 99999) {
                                        $(this).val(99999);
                                    }
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
                                object:$('#ocolor')
                            });
                            iObj.fields.push(ocolor);

                            // Размер
                            osize = new $.cpField();
                            osize.init({
                                object:$('#osize')
                            });
                            iObj.fields.push(osize);

                            // Количество
                            oamount = new $.cpField();
                            oamount.init({
                                object:$('#oamount'),
                                needCheck:'number'/*,
                            minValue : 1,
                            maxValue : 9999*/
                            });
                            iObj.fields.push(oamount);

                            // Скриншот
                            oimg = new $.cpField();
                            oimg.init({
                                object:$('#oimg')
                            });
                            iObj.fields.push(oimg);

                            ofile = new $.cpField();
                            ofile.init({
                                object:$('#ofile')
                            });
                            iObj.fields.push(ofile);

                            // Нужно ли фото товара
                            foto_requested = new $.cpField();
                            foto_requested.init({
                                object:$('#foto_requested')
                            });
                            iObj.fields.push(foto_requested);

                            // Комментарий к товару
                            ocomment = new $.cpField();
                            ocomment.init({
                                object:$('#ocomment')
                            });
                            iObj.fields.push(ocomment);
                        }

                        iObj.add = function () {
                            unbindAddItem();

                            // Рисуем новый товар
                            var item = oObj.options.cart.createCustomItem({
                                id:'',
                                name:fieldByName(iObj.fields, 'oname').val(),
                                price:fieldByName(iObj.fields, 'oprice').val(),
                                delivery:fieldByName(iObj.fields, 'odeliveryprice').val(),
                                weight:fieldByName(iObj.fields, 'oweight').val(),
                                amount:fieldByName(iObj.fields, 'oamount').val(),
                                currency:selectedCurrency,
                                olink:fieldByName(iObj.fields, 'olink').val(),
                                ocolor:fieldByName(iObj.fields, 'ocolor').val(),
                                osize:fieldByName(iObj.fields, 'osize').val(),
                                oimg:fieldByName(iObj.fields, 'userfileimg').val(),
                                foto_requested:fieldByName(iObj.fields, 'foto_requested').val(),
                                ocomment:fieldByName(iObj.fields, 'ocomment').val()
                            });
                            var row = iObj.drawRow(item);

                            // Отправляем на сервер данные
                            $('#'+oObj.options.type+'ItemForm').ajaxForm({
                                target:$('#onlineOrderForm').attr('action'),
                                type:'POST',
                                dataType:'json',
                                iframe:true,
                                beforeSubmit:function (formData, jqForm, options) {
                                    if (!checkOrder() || !checkItem(iObj, errorFields)) {
                                        scrollFirstError();
                                        bindAddItem();
                                        $('.snippet').remove();
                                        // Если товаров больше нет сворачиваем таблицу товаров
                                        var prows = $('.' + oObj.options.type + '_order_form #new_products tr');
                                        if (prows.length < 4) {
                                            $('.' + oObj.options.type + '_order_form #new_products').parent().hide('slow');
                                            $('.checkOutOrderBlock').hide('slow');
                                        }

                                        return false;
                                    }

                                    return true;
                                },
                                success:function (response) {
                                    if (response) {
                                        // Ответ не является числовым значением
                                        if (isNaN(response.odetail_id) || isNaN(response.order_id)) {
                                            error('top', response);
                                        }
                                        // Все в порядке, добавляем товар
                                        else {
                                            // проставляем всюду Id заказа
                                            $('input.order_id').val(response.order_id);
                                            oObj.options.order_id = response.order_id;

                                            var screenshot_code = '';
                                            if (response.odetail_img && response.odetail_img.match(/http:\/\//g)) {
                                                screenshot_code = '<a href="' + response.odetail_img + '" target="BLANK">Просмотреть</a>'
                                            }
                                            else if (parseInt(response.odetail_img, 10) != 0) {
                                                screenshot_code = "<a href='javascript:void(0)' onclick='setRel(" + response.odetail_img + ");'>Просмотреть</a> " +
                                                        "<a rel='lightbox_" + response.odetail_img + "' href='/client/showScreen/" + response.odetail_img + "' style='display:none;'>Посмотреть</a>";
                                            }
                                            else {
                                                screenshot_code = '';
                                            }

                                            // Подставляем изображение или ссылку на него
                                            $('.snippet .oimg').html(screenshot_code);
                                            $('.snippet').removeClass('snippet').attr('detail-id', response.odetail_id);

                                            // Добавляем товар в корзину
                                            item.id = response.odetail_id;
                                            item.oimg = response.odetail_img;
                                            oObj.options.cart.addCustom(item);

                                            // перерисовываем позицию товара
                                            row.remove();
                                            row = iObj.drawRow(item);
                                            row.find('#progress' + item.id).hide();

                                            // пересчитываем заказ
                                            oObj.updateTotals();

                                            success('top', 'Товар №' + response.odetail_id + ' успешно добавлен в корзину.');

                                            // чистим форму
                                            if (true) //debug only
                                            {
                                                formFieldsClear(iObj);
                                            }//debug only

                                            // Отображаем список товаров
                                            $('.' + oObj.options.type + '_order_form #new_products').parent().show();
                                        }
                                    }
                                    // Ответ не был получен
                                    else {
                                        row.find('#progress' + item.id).hide();
                                        error('top', 'Товар не добавлен. Заполните все поля и попробуйте еще раз.');
                                    }
                                },
                                error:function (response) {
                                    row.find('#progress' + item.id).hide();

                                    if (response.status == 0) {
                                        error('top', 'Товар не добавлен. Отсутствует подключение к интернету.');
                                    }
                                    else {
                                        error('top', 'Товар не добавлен. Заполните все поля и попробуйте еще раз.');
                                    }
                                }, // End error
                                complete:function () {
                                    bindAddItem();
                                }
                            });

                            addItemProgress();

                            $('#'+oObj.options.type+'ItemForm').submit();
                        }

                        iObj.deleteItem = function () {
                            var orderId = oObj.options.order_id;
                            var itemId = $(this).attr('odetail-id');

                            if (confirm("Вы уверены, что хотите удалить товар №" + itemId + "?")) {
                                var order = this;
                                $.post('<?= $selfurl ?>deleteNewProduct/' + orderId + '/' + itemId, {}, function () {
                                }, 'json')
                                        .success(function (responce) {
                                            // проверка на ошибку на сервере
                                            if (responce.e == -1) {
                                                error('top', responce.m);
                                            }
                                            else {
                                                oObj.options.cart.delete(itemId);
                                                // Удаляем строку товара
                                                $('tr#product' + itemId + '').remove();
                                                // Если товаров больше нет сворачиваем таблицу товаров
                                                var prows = $('.' + oObj.options.type + '_order_form #new_products tr');
                                                if (prows.length < 4) {
                                                    $('.' + oObj.options.type + '_order_form #new_products').parent().hide('slow');
                                                    $('.checkOutOrderBlock').hide('slow');
                                                }
                                                oObj.updateTotals();

                                                success('top', responce.m);
                                            }
                                        })
                                        .error(function (responce) {
                                            error('top', 'Товар не удален. Ошибка подключения.');
                                        });
                            }
                            return false;
                        }

                        iObj.editItem = function () {
                            var itemId = $(this).attr('odetail-id');
                            $('tr#product' + itemId + ' .plaintext').hide();
                            $('tr#product' + itemId + ' .producteditor').show();
                            $('tr#product' + itemId + ' .edit, tr#product' + itemId + ' .delete').hide();
                            $('tr#product' + itemId + ' .save, tr#product' + itemId + ' .cancel').show();

                            var odetail = oObj.options.cart.getById(itemId);

                            $tr = $('tr#product' + itemId);

                            $tr.find('textarea.link').val(odetail['olink']);
                            $tr.find('textarea.name').val(odetail['name']);
                            $tr.find('textarea.amount').val(odetail['amount']);
                            $tr.find('textarea.size').val(odetail['osize']);
                            $tr.find('textarea.color').val(odetail['ocolor']);
                            $tr.find('textarea.ocomment').val(odetail['ocomment']);
                            $tr.find('textarea.image').val(odetail['oimg']);
                            $tr.find('input.img_file').val(odetail['ouserfile']);

                            return false;
                        }

                        iObj.cancelItem = function () {
                            var itemId = $(this).attr('odetail-id');
                            $('tr#product' + itemId + ' .plaintext').show();
                            $('tr#product' + itemId + ' .producteditor').hide();
                            $('tr#product' + itemId + ' .edit, tr#product' + itemId + ' .delete').show();
                            $('tr#product' + itemId + ' .save, tr#product' + itemId + ' .cancel').hide();
                            return false;
                        }

                        iObj.saveItem = function () {
                            var itemId = $(this).attr('odetail-id'),
                                    cart = oObj.options.cart,
                                    odetail = cart.getById(itemId);

                            $tr = $('tr#product' + itemId);

                            odetail['olink'] = $tr.find('textarea.link').val();
                            odetail['name'] = $tr.find('textarea.name').val();
                            odetail['amount'] = $tr.find('textarea.amount').val();
                            odetail['osize'] = $tr.find('textarea.size').val();
                            odetail['ocolor'] = $tr.find('textarea.color').val();
                            odetail['ocomment'] = $tr.find('textarea.ocomment').val();
                            odetail['img_selector'] = $tr.find('input.img_selector:checked').val();

                            if (odetail['img_selector'] == 'link') {
                                odetail['img'] = $tr.find('textarea.image').val();
                                odetail['img_file'] = '';
                            }
                            else if (odetail['img_selector'] == 'file') {
                                odetail['img'] = '';
                                odetail['userfile'] = $tr.find('input.img_file').val();
                            }

                            var form = $tr.find('form#odetail' + oObj.options.type + itemId);
                            form.ajaxForm({
                                dataType:'json',
                                iframe:true,
                                beforeSubmit:function () {
                                    $('img#progress' + itemId).show();
                                },
                                error:function () {
                                    error('top', 'Описание товара №' + itemId + ' не сохранено.');
                                },
                                success:function (data) {
                                    cart.update(odetail);
                                    row = $('#product' + itemId);
                                    row.after(iObj.getRow(odetail));
                                    row.remove();
                                    $('img#progress' + itemId).hide();

                                    oObj.updateTotals();
                                    success('top', data.message);
                                }
                            });
                            form.submit();

                            oObj.updateTotals();
                            return false;
                        }

                        iObj.updateItemPrice = function () {
                            var val = $(this).val();
                            var odetail_id = $(this).attr('odetail-id');
                            if (isNaN(val) || parseInt(val, 10) == 0) {
                                error('top', 'Стоимость не изменена. Укажите стоимость товара.');
                                return;
                            }

                            $('img#progress' + odetail_id).show();

                            $.post('/client/update_new_odetail_price/' + $(this).attr('order-id') + '/' + odetail_id + '/' + parseInt(val, 10), {},
                                    function (responce) {
                                        $('img#progress' + odetail_id).hide();
                                        if (responce.is_error) {
                                            error('top', "Стоимость не изменена. " + responce.message);
                                        }
                                        else {
                                            success("top", "Стоимость товара №" + odetail_id + " успешно изменена");
                                            var item = oObj.options.cart.getById(odetail_id);
                                            item.price = val;
                                            oObj.options.cart.update(item);
                                            oObj.updateTotals();
                                        }
                                    },
                                    'json')
                                    .error(function (responce) {
                                        $('img#progress' + odetail_id).hide();
                                        error('top', "Стоимость не изменена. " + responce);
                                    });
                        }

                        iObj.updateItemDeliveryPrice = function () {
                            var val = $(this).val();
                            var odetail_id = $(this).attr('odetail-id');
                            if (isNaN(val) || parseInt(val, 10) == 0) {
                                error('top', 'Стоимость не изменена. Укажите стоимость доставки.');
                                return;
                            }

                            $('img#progress' + odetail_id).show();

                            $.post('/client/update_new_odetail_pricedelivery/' + $(this).attr('order-id') + '/' + odetail_id + '/' + parseInt(val, 10), {},
                                    function (responce) {
                                        $('img#progress' + odetail_id).hide();
                                        if (responce.is_error) {
                                            error('top', "Стоимость не изменена. " + responce.message);
                                        }
                                        else {
                                            success("top", "Стоимость доставки №" + odetail_id + " успешно изменена");
                                            var item = oObj.options.cart.getById(odetail_id);
                                            item.delivery = val;
                                            oObj.options.cart.update(item);
                                            oObj.updateTotals();
                                        }
                                    },
                                    'json')
                                    .error(function (responce) {
                                        $('img#progress' + odetail_id).hide();
                                        error('top', "Стоимость не изменена. " + responce);
                                    });
                        }

                        iObj.updateItemWeight = function () {
                            var val = $(this).val();
                            var odetail_id = $(this).attr('odetail-id');
                            if (isNaN(val) || parseInt(val, 10) == 0) {
                                error('top', 'Вес не изменен. Укажите вес товара.');
                                return;
                            }

                            $('img#progress' + odetail_id).show();

                            $.post('/client/update_new_odetail_weight/' + $(this).attr('order-id') + '/' + odetail_id + '/' + parseInt(val, 10), {},
                                    function (responce) {
                                        $('img#progress' + odetail_id).hide();
                                        if (responce.is_error) {
                                            error('top', "Вес не изменен. " + responce.message);
                                        }
                                        else {
                                            success("top", "Вес товара №" + odetail_id + " успешно изменен");
                                            var item = oObj.options.cart.getById(odetail_id);
                                            item.weight = val;
                                            oObj.options.cart.update(item);
                                            oObj.updateTotals();
                                        }
                                    },
                                    'json')
                                    .error(function (responce) {
                                        $('img#progress' + odetail_id).hide();
                                        error('top', "Вес не изменен. " + responce);
                                    });
                        }

                        iObj.getRow = function (item) {
                            if (item.oimg == 0) {
                                oimg = '';
                            }
                            else if (item.oimg === null) {
                                oimg = "<a href='javascript:void(0)' onclick='setRel(" + item.id + ");'>" +
                                        "<img width='55px' height='55px' src='/client/showScreen/" + item.id + "'></a>" +
                                        "<a rel='lightbox_" + item.id + "' href='/client/showScreen/" + item.id + "' style='display:none;'>Посмотреть</a>";
                            }
                            else if (item.oimg) {
                                var img_src = item.oimg;
                                if (!img_src.match(/http:\/\//g)) {
                                    img_src = 'http://' + img_src;
                                }
                                oimg = '<a href="' + img_src + '" target="BLANK">Просмотреть</a>'
                            }

                            // Рисуем новый товар
                            var snippet = $('' +
                                    '<tr id="product' + item.id + '" class="snippet" detail-id="' + item.id + '">' +
                                    '   <td id="odetail_id' + item.id + '">' +
                                    '      <input type="checkbox" name="odetail_id" value="' + item.id + '"/>' +
                                    '      <br>' +
                                    '      ' + item.id + '<br>' +
                                    '      <img src="/static/images/lightbox-ico-loading.gif" style="" class="float" id="progress' + item.id + '">' +
                                    '   </td>' +
                                    '   <form method="POST" id="odetail' + item.id + '" enctype="multipart/form-data" action="/client/updateNewProduct/' + oObj.options.order_id + '/' + item.id + '">' +
                                    '   <td style="text-align: left; vertical-align: bottom;">' +
                                    '      <span class="plaintext">' +
                                    '          <a href="' + item.link + '" target="_blank">' + item.name + '</a>' +
                                    '          <br>' +
                                    '          <b>Количество</b>: ' + item.amount +
                                    '          <b>Размер</b>:' + item.osize +
                                    '          <b>Цвет</b>: <br>' + item.ocolor +
                                    '          <b>Комментарий</b>:' + item.ocomment +
                                    '      </span>' +
                                    '      <span style="display: none;" class="producteditor">' +
                                    '      <br>' +
                                    '         <b>Ссылка</b>:' +
                                    '         <textarea name="link" class="link"></textarea>' +
                                    '         <br>' +
                                    '         <b>Наименование</b>:' +
                                    '         <textarea name="name" class="name"></textarea>' +
                                    '         <br>' +
                                    '         <b>Количество</b>:' +
                                    '         <textarea name="amount" class="amount int"></textarea>' +
                                    '         <br>' +
                                    '         <b>Размер</b>:' +
                                    '         <textarea name="size" class="size"></textarea>' +
                                    '         <br>' +
                                    '         <b>Цвет</b>:' +
                                    '         <textarea name="color" class="color"></textarea>' +
                                    '         <br>' +
                                    '         <b>Комментарий</b>:' +
                                    '         <textarea name="comment" class="ocomment"></textarea>' +
                                    '         <br>' +
                                    '      </span>' +
                                    '   </td>' +
                                    '   <td>' +
                                    '      <span class="plaintext">' +
                                    '         ' + oimg + ' ' +
                                    '      </span>' +
                                    '      <span style="display: none;" class="producteditor">' +
                                    '         <input type="radio" value="link" class="img_selector" name="img_selector">' +
                                    '         <textarea name="img" class="image"></textarea>' +
                                    '         <br>' +
                                    '         <input type="radio" value="file" class="img_selector" name="img_selector">' +
                                    '         <input type="file" name="userfile" class="img_file">' +
                                    '      </span>' +
                                    '   </td>' +
                                    '   </form>' +
                                    '   <td>' +
                                    '      <input type="text" order-id="' + oObj.options.order_id + '" odetail-id="' + item.id + '" maxlength="11" style="width:60px" value="' + item.price + '" class="odetail_price int" name="odetail_price' + item.id + '" id="odetail_price' + item.id + '">' +
                                    '   </td>' +
                                    '   <td>' +
                                    '      <input type="text" order-id="' + oObj.options.order_id + '" odetail-id="' + item.id + '" maxlength="11" style="width:60px" value="' + item.delivery + '" class="odetail_pricedelivery int" name="odetail_pricedelivery' + item.id + '" id="odetail_pricedelivery' + item.id + '">' +
                                    '   </td>' +
                                    '   <td>' +
                                    '      <input type="text" order-id="' + oObj.options.order_id + '" odetail-id="' + item.id + '" maxlength="11" style="width:60px" value="' + item.weight + '" class="odetail_weight int" name="odetail_weight' + item.id + '" id="odetail_weight' + item.id + '">' +
                                    '   </td>' +
                                    '   <td>' +
                                    '      <a class="edit" odetail-id="' + item.id + '" href="#">' +
                                    '         <img border="0" title="Редактировать" src="/static/images/comment-edit.png"/></a>' +
                                    '      <br>' +
                                    '      <a class="delete" odetail-id="' + item.id + '" href="#">' +
                                    '         <img border="0" title="Удалить" src="/static/images/delete.png"/></a>' +
                                    '      <br>' +
                                    '      <a style="display: none;" class="cancel" odetail-id="' + item.id + '" href="#">' +
                                    '         <img border="0" title="Отменить" src="/static/images/comment-delete.png"/></a>' +
                                    '      <br>' +
                                    '      <a style="display: none;" class="save" odetail-id="' + item.id + '" href="#">' +
                                    '         <img border="0" title="Сохранить" src="/static/images/done-filed.png"/></a>' +
                                    '   </td>' +
                                    '</tr>');

                            // Прикручиваем обработчики к кнопкам
                            snippet.find('a.delete').bind('click', iObj.deleteItem);
                            snippet.find('a.edit').bind('click', iObj.editItem);
                            snippet.find('a.cancel').bind('click', iObj.cancelItem);
                            snippet.find('a.save').bind('click', iObj.saveItem);

                            snippet.find('input.odetail_price').bind('change', iObj.updateItemPrice);
                            snippet.find('input.odetail_pricedelivery').bind('change', iObj.updateItemDeliveryPrice);
                            snippet.find('input.odetail_weight').bind('change', iObj.updateItemWeight);

                            return snippet;
                        }

                        iObj.drawRow = function (item) {
                            var snippet = iObj.getRow(item);
                            $('.' + oObj.options.type + '_order_form #new_products tr:first').after(snippet);
                            return snippet;
                        }

                        if (blankOrderData)
                        {
                            var orderData = null;

                            for (var i = 0, n = blankOrderData.length; i < n; i++)
                            {
                                if (blankOrderData[i].order_type == oObj.options.type)
                                {
                                    orderData = blankOrderData[i];
                                }
                            }

                            if (orderData)
                            {
                                oObj.options.order_id = orderData.order_id;

                                $.each(orderData.details, function (k, v)
                                {
                                    if (selectedCurrency == '') {
                                        selectedCurrency = orderData.order_currency;
                                    }

                                    oObj.options.cart.addCustom({
                                        id:v.odetail_id,
                                        name:v.odetail_product_name,
                                        price:v.odetail_price,
                                        delivery:v.odetail_pricedelivery,
                                        weight:v.odetail_weight,
                                        amount:v.odetail_product_amount,
                                        currency:selectedCurrency,
                                        olink:v.odetail_link,
                                        ocolor:v.odetail_product_color,
                                        osize:v.odetail_product_size,
                                        oimg:v.odetail_img,
                                        foto_requested:v.odetail_foto_requested,
                                        ocomment:v.odetail_comment
                                    });
                                });

                                // прикручиваем обработчики кнопок деталям заказа
                                $('.' + oObj.options.type + '_order_form #new_products a.edit').bind('click', iObj.editItem);
                                $('.' + oObj.options.type + '_order_form #new_products a.delete').bind('click', iObj.deleteItem);
                                $('.' + oObj.options.type + '_order_form #new_products a.save').bind('click', iObj.saveItem);
                                $('.' + oObj.options.type + '_order_form #new_products a.cancel').bind('click', iObj.cancelItem);
                                $('.' + oObj.options.type + '_order_form #new_products input.odetail_price').bind('change', iObj.updateItemPrice);
                                $('.' + oObj.options.type + '_order_form #new_products input.odetail_pricedelivery').bind('change', iObj.updateItemDeliveryPrice);
                                $('.' + oObj.options.type + '_order_form #new_products input.odetail_weight').bind('change', iObj.updateItemWeight);

                                oObj.updateTotals();
                            }

                            // Отображаем список товаров
                            $('#detailsForm').show();
                        }
                    }
            // начинаем инициализацию

            // Страна поступления, поле "Заказать из"
            country_from = new $.cpField();
            country_from.init({
                object:$('#country_from_online'),
                useDd:true,
                onChange:function () {
                    var id = $(this).val();
                    var prevCurrency = selectedCurrency;

                    for (var index in currencies) {
                        var currency = currencies[index];

                        if (id == currency['country_id']) {
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
                object:$('#country_to_online'),
                useDd:true,
                onChange:function () {
                    var id = $(this).val();
                    for (var index in currencies) {
                        var currency = currencies[index];

                        if (id == currency['country_id']) {
                            $('.' + oObj.options.type + '_order_form input.countryTo').val(id);
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
                object:$('#city_to_online'),
                onChange:function () {
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
                object:$('#requested_delivery_online')
            });
            oObj.fields.push(requested_delivery);

            var item = new itemOnline();
            item.init();

            $('#addItemOnline').unbind('click').bind('click', item.add);

            $('#'+oObj.options.type+'checkoutOrder').bind('click', saveOrder);

            // Отображаем форму
            $('div.order_type_selector').hide();
            $('h2#page_title').html(oObj.options.title);
            $("div.online_order_form").show('slow');

        }
        // End initOnline

        var initOffline = function ()
        {
            oObj.options.type = 'offline';
            oObj.options.title = 'Добавление нового Offline заказа';
            oObj.options.cart = new $.cpCart();

            var itemOffline = function ()
            {
                var iObj = this;

                var addItemProgress = function ()
                {
                    var parent = $('#odetail');
                    var progress = $("<div class='progress'> " +
                            "<img class='float product_progress_bar' src='/static/images/lightbox-ico-loading.gif'/> " +
                            "</div>");
                    progress.css({
                        background:'none',
                        width:'32px',
                        height:'32px',
                        'margin-top':'6px',
                        float:'left'
                    });

                    parent.append(progress);
                } // End addItemProgress

                var removeItemProgress = function ()
                {
                    $('div.progress').remove();
                } // End removeItemProgress

                var bindAddItem = function ()
                {
                    // Добавляем обработчик к кнопке добавления товара к заказу
                    $('#addItemOffline').bind('click', iObj.add);
                } // End bindAddItem

                var unbindAddItem = function ()
                {
                    // Убираем обработчик у кнопки добавления товара к заказу
                    $('#addItemOffline').unbind('click');
                } // End unbindAddItem


                iObj.fields = [];

                iObj.init = function ()
                {
                    // Наименование товара
                    oname = new $.cpField();
                    oname.init({
                        object:$('#'+oObj.options.type+'ItemForm #oname')
                    });
                    oname.validate({
                        expression:'if (VAL == "") { return false; } else { return true; }',
                        message:'Необходимо указать название товара'
                    });
                    iObj.fields.push(oname);

                    // Название магазина
                    oshop = new $.cpField();
                    oshop.init({
                        object:$('#'+oObj.options.type+'ItemForm #oshop')
                    });
                    iObj.fields.push(oshop);

                    // Цена товара
                    oprice = new $.cpField();
                    oprice.init({
                        object:$('#'+oObj.options.type+'ItemForm #oprice'),
                        needCheck:'float',
                        onChange:function () {
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
                        object:$('#'+oObj.options.type+'ItemForm #odeliveryprice'),
                        needCheck:'float',
                        onChange:function () {
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
                        object:$('#'+oObj.options.type+'ItemForm #oweight'),
                        needCheck:'number',
                        onChange:function () {
                            if (!isNaN($(this).val()) && parseInt($(this).val(), 10) > 99999) {
                                $(this).val(99999);
                            }
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
                        object:$('#'+oObj.options.type+'ItemForm #ocolor')
                    });
                    iObj.fields.push(ocolor);

                    // Размер
                    osize = new $.cpField();
                    osize.init({
                        object:$('#'+oObj.options.type+'ItemForm #osize')
                    });
                    iObj.fields.push(osize);

                    // Количество
                    oamount = new $.cpField();
                    oamount.init({
                        object:$('#'+oObj.options.type+'ItemForm #oamount'),
                        needCheck:'number'
                    });
                    iObj.fields.push(oamount);

                    // Скриншот
                    oimg = new $.cpField();
                    oimg.init({
                        object:$('#'+oObj.options.type+'ItemForm #oimg')
                    });
                    iObj.fields.push(oimg);

                    ofile = new $.cpField();
                    ofile.init({
                        object:$('#'+oObj.options.type+'ItemForm #ofile')
                    });
                    iObj.fields.push(ofile);

                    // Нужно ли фото товара
                    foto_requested = new $.cpField();
                    foto_requested.init({
                        object:$('#'+oObj.options.type+'ItemForm #foto_requested')
                    });
                    iObj.fields.push(foto_requested);

                    // Комментарий к товару
                    ocomment = new $.cpField();
                    ocomment.init({
                        object:$('#'+oObj.options.type+'ItemForm #ocomment')
                    });
                    iObj.fields.push(ocomment);
                }

                iObj.add = function () {
                    unbindAddItem();

                    // Рисуем новый товар
                    var item = oObj.options.cart.createCustomItem({
                        id:'',
                        name:fieldByName(iObj.fields, 'oname').val(),
                        price:fieldByName(iObj.fields, 'oprice').val(),
                        delivery:fieldByName(iObj.fields, 'odeliveryprice').val(),
                        weight:fieldByName(iObj.fields, 'oweight').val(),
                        amount:fieldByName(iObj.fields, 'oamount').val(),
                        currency:selectedCurrency,
                        oshop:fieldByName(iObj.fields, 'oshop').val(),
                        ocolor:fieldByName(iObj.fields, 'ocolor').val(),
                        osize:fieldByName(iObj.fields, 'osize').val(),
                        oimg:fieldByName(iObj.fields, 'userfileimg').val(),
                        foto_requested:fieldByName(iObj.fields, 'foto_requested').val(),
                        ocomment:fieldByName(iObj.fields, 'ocomment').val()
                    });
                    var row = iObj.drawRow(item);

                    // Отправляем на сервер данные
                    $('#'+oObj.options.type+'ItemForm').ajaxForm({
                        target:$('#onlineOrderForm').attr('action'),
                        type:'POST',
                        dataType:'json',
                        iframe:true,
                        beforeSubmit:function (formData, jqForm, options) {
                            if (!checkOrder() || !checkItem(iObj, errorFields)) {
                                scrollFirstError();
                                bindAddItem();
                                $('.snippet').remove();
                                // Если товаров больше нет сворачиваем таблицу товаров
                                var prows = $('.' + oObj.options.type + '_order_form #new_products tr');
                                if (prows.length < 4) {
                                    $('.' + oObj.options.type + '_order_form #new_products').parent().hide('slow');
                                    $('.' + oObj.options.type + '_order_form .checkOutOrderBlock').hide('slow');
                                }

                                return false;
                            }

                            return true;
                        },
                        success:function (response) {
                            if (response) {
                                // Ответ не является числовым значением
                                if (isNaN(response.odetail_id) || isNaN(response.order_id)) {
                                    error('top', response);
                                }
                                // Все в порядке, добавляем товар
                                else {
                                    // проставляем всюду Id заказа
                                    $('input.order_id').val(response.order_id);
                                    oObj.options.order_id = response.order_id;

                                    var screenshot_code = '';
                                    if (response.odetail_img && response.odetail_img.match(/http:\/\//g)) {
                                        screenshot_code = '<a href="' + response.odetail_img + '" target="BLANK">Просмотреть</a>'
                                    }
                                    else if (parseInt(response.odetail_img, 10) != 0) {
                                        screenshot_code = "<a href='javascript:void(0)' onclick='setRel(" + response.odetail_img + ");'>Просмотреть</a> " +
                                                "<a rel='lightbox_" + response.odetail_img + "' href='/client/showScreen/" + response.odetail_img + "' style='display:none;'>Посмотреть</a>";
                                    }
                                    else {
                                        screenshot_code = '';
                                    }

                                    // Подставляем изображение или ссылку на него
                                    $('.snippet .oimg').html(screenshot_code);
                                    $('.snippet').removeClass('snippet').attr('detail-id', response.odetail_id);

                                    // Добавляем товар в корзину
                                    item.id = response.odetail_id;
                                    item.oimg = response.odetail_img;
                                    oObj.options.cart.addCustom(item);

                                    // перерисовываем позицию товара
                                    row.remove();
                                    row = iObj.drawRow(item);
                                    row.find('#progress' + item.id).hide();

                                    // пересчитываем заказ
                                    oObj.updateTotals();

                                    success('top', 'Товар №' + response.odetail_id + ' успешно добавлен в корзину.');

                                    // чистим форму
                                    if (true) //debug only
                                    {
                                        formFieldsClear(iObj);
                                    }//debug only

                                    // Отображаем список товаров
                                    $('.' + oObj.options.type + '_order_form #new_products').parent().show();
                                    $('.' + oObj.options.type + '_order_form .checkOutOrderBlock').show('slow');
                                }
                            }
                            // Ответ не был получен
                            else {
                                row.find('#progress' + item.id).hide();
                                error('top', 'Товар не добавлен. Заполните все поля и попробуйте еще раз.');
                            }
                        },
                        error:function (response) {
                            row.find('#progress' + item.id).hide();

                            if (response.status == 0) {
                                error('top', 'Товар не добавлен. Отсутствует подключение к интернету.');
                            }
                            else {
                                error('top', 'Товар не добавлен. Заполните все поля и попробуйте еще раз.');
                            }
                        }, // End error
                        complete:function () {
                            bindAddItem();
                        }
                    });

                    addItemProgress();

                    $('#'+oObj.options.type+'ItemForm').submit();
                }

                iObj.deleteItem = function () {
                    var orderId = oObj.options.order_id;
                    var itemId = $(this).attr('odetail-id');

                    if (confirm("Вы уверены, что хотите удалить товар №" + itemId + "?")) {
                        var order = this;
                        $.post('<?= $selfurl ?>deleteNewProduct/' + orderId + '/' + itemId, {}, function () {
                        }, 'json')
                                .success(function (responce) {
                                    // проверка на ошибку на сервере
                                    if (responce.e == -1) {
                                        error('top', responce.m);
                                    }
                                    else {
                                        oObj.options.cart.delete(itemId);
                                        // Удаляем строку товара
                                        $('tr#product' + itemId + '').remove();
                                        // Если товаров больше нет сворачиваем таблицу товаров
                                        var prows = $('.' + oObj.options.type + '_order_form #new_products tr');
                                        if (prows.length < 4) {
                                            $('.' + oObj.options.type + '_order_form #new_products').parent().hide('slow');
                                            $('.checkOutOrderBlock').hide('slow');
                                        }
                                        oObj.updateTotals();

                                        success('top', responce.m);
                                    }
                                })
                                .error(function (responce) {
                                    error('top', 'Товар не удален. Ошибка подключения.');
                                });
                    }
                    return false;
                }

                iObj.editItem = function () {
                    var itemId = $(this).attr('odetail-id');
                    $('tr#product' + itemId + ' .plaintext').hide();
                    $('tr#product' + itemId + ' .producteditor').show();
                    $('tr#product' + itemId + ' .edit, tr#product' + itemId + ' .delete').hide();
                    $('tr#product' + itemId + ' .save, tr#product' + itemId + ' .cancel').show();

                    var odetail = oObj.options.cart.getById(itemId);

                    $tr = $('tr#product' + itemId);

                    $tr.find('textarea.shop').val(odetail['oshop']);
                    $tr.find('textarea.name').val(odetail['name']);
                    $tr.find('textarea.amount').val(odetail['amount']);
                    $tr.find('textarea.size').val(odetail['osize']);
                    $tr.find('textarea.color').val(odetail['ocolor']);
                    $tr.find('textarea.ocomment').val(odetail['ocomment']);
                    $tr.find('textarea.image').val(odetail['oimg']);
                    $tr.find('input.img_file').val(odetail['ouserfile']);

                    return false;
                }

                iObj.cancelItem = function () {
                    var itemId = $(this).attr('odetail-id');
                    $('tr#product' + itemId + ' .plaintext').show();
                    $('tr#product' + itemId + ' .producteditor').hide();
                    $('tr#product' + itemId + ' .edit, tr#product' + itemId + ' .delete').show();
                    $('tr#product' + itemId + ' .save, tr#product' + itemId + ' .cancel').hide();
                    return false;
                }

                iObj.saveItem = function () {
                    var itemId = $(this).attr('odetail-id'),
                            cart = oObj.options.cart,
                            odetail = cart.getById(itemId);

                    $tr = $('tr#product' + itemId);

                    odetail['oshop'] = $tr.find('textarea.shop').val();
                    odetail['name'] = $tr.find('textarea.name').val();
                    odetail['amount'] = $tr.find('textarea.amount').val();
                    odetail['osize'] = $tr.find('textarea.size').val();
                    odetail['ocolor'] = $tr.find('textarea.color').val();
                    odetail['ocomment'] = $tr.find('textarea.ocomment').val();
                    odetail['img_selector'] = $tr.find('input.img_selector:checked').val();

                    if (odetail['img_selector'] == 'link') {
                        odetail['img'] = $tr.find('textarea.image').val();
                        odetail['img_file'] = '';
                    }
                    else if (odetail['img_selector'] == 'file') {
                        odetail['img'] = '';
                        odetail['userfile'] = $tr.find('input.img_file').val();
                    }

                    var form = $tr.find('form#odetail' + oObj.options.type + itemId);
                    form.ajaxForm({
                        dataType:'json',
                        iframe:true,
                        beforeSubmit:function () {
                            $('img#progress' + itemId).show();
                        },
                        error:function () {
                            error('top', 'Описание товара №' + itemId + ' не сохранено.');
                        },
                        success:function (data) {
                            cart.update(odetail);
                            row = $('#product' + itemId);
                            row.after(iObj.getRow(odetail));
                            row.remove();
                            $('img#progress' + itemId).hide();

                            oObj.updateTotals();
                            success('top', data.message);
                        }
                    });
                    form.submit();

                    oObj.updateTotals();
                    return false;
                }

                iObj.updateItemPrice = function () {
                    var val = $(this).val();
                    var odetail_id = $(this).attr('odetail-id');
                    if (isNaN(val) || parseInt(val, 10) == 0) {
                        error('top', 'Стоимость не изменена. Укажите стоимость товара.');
                        return;
                    }

                    $('img#progress' + odetail_id).show();

                    $.post('/client/update_new_odetail_price/' + $(this).attr('order-id') + '/' + odetail_id + '/' + parseInt(val, 10), {},
                            function (responce) {
                                $('img#progress' + odetail_id).hide();
                                if (responce.is_error) {
                                    error('top', "Стоимость не изменена. " + responce.message);
                                }
                                else {
                                    success("top", "Стоимость товара №" + odetail_id + " успешно изменена");
                                    var item = oObj.options.cart.getById(odetail_id);
                                    item.price = val;
                                    oObj.options.cart.update(item);
                                    oObj.updateTotals();
                                }
                            },
                            'json')
                            .error(function (responce) {
                                $('img#progress' + odetail_id).hide();
                                error('top', "Стоимость не изменена. " + responce);
                            });
                }

                iObj.updateItemDeliveryPrice = function () {
                    var val = $(this).val();
                    var odetail_id = $(this).attr('odetail-id');
                    if (isNaN(val) || parseInt(val, 10) == 0) {
                        error('top', 'Стоимость не изменена. Укажите стоимость доставки.');
                        return;
                    }

                    $('img#progress' + odetail_id).show();

                    $.post('/client/update_new_odetail_pricedelivery/' + $(this).attr('order-id') + '/' + odetail_id + '/' + parseInt(val, 10), {},
                            function (responce) {
                                $('img#progress' + odetail_id).hide();
                                if (responce.is_error) {
                                    error('top', "Стоимость не изменена. " + responce.message);
                                }
                                else {
                                    success("top", "Стоимость доставки №" + odetail_id + " успешно изменена");
                                    var item = oObj.options.cart.getById(odetail_id);
                                    item.delivery = val;
                                    oObj.options.cart.update(item);
                                    oObj.updateTotals();
                                }
                            },
                            'json')
                            .error(function (responce) {
                                $('img#progress' + odetail_id).hide();
                                error('top', "Стоимость не изменена. " + responce);
                            });
                }

                iObj.updateItemWeight = function () {
                    var val = $(this).val();
                    var odetail_id = $(this).attr('odetail-id');
                    if (isNaN(val) || parseInt(val, 10) == 0) {
                        error('top', 'Вес не изменен. Укажите вес товара.');
                        return;
                    }

                    $('img#progress' + odetail_id).show();

                    $.post('/client/update_new_odetail_weight/' + $(this).attr('order-id') + '/' + odetail_id + '/' + parseInt(val, 10), {},
                            function (responce) {
                                $('img#progress' + odetail_id).hide();
                                if (responce.is_error) {
                                    error('top', "Вес не изменен. " + responce.message);
                                }
                                else {
                                    success("top", "Вес товара №" + odetail_id + " успешно изменен");
                                    var item = oObj.options.cart.getById(odetail_id);
                                    item.weight = val;
                                    oObj.options.cart.update(item);
                                    oObj.updateTotals();
                                }
                            },
                            'json')
                            .error(function (responce) {
                                $('img#progress' + odetail_id).hide();
                                error('top', "Вес не изменен. " + responce);
                            });
                }

                iObj.getRow = function (item)
                {
                    if (item.oimg == 0) {
                        oimg = '';
                    }
                    else if (item.oimg === null) {
                        oimg = "<a href='javascript:void(0)' onclick='setRel(" + item.id + ");'>" +
                                "<img width='55px' height='55px' src='/client/showScreen/" + item.id + "'></a>" +
                                "<a rel='lightbox_" + item.id + "' href='/client/showScreen/" + item.id + "' style='display:none;'>Посмотреть</a>";
                    }
                    else if (item.oimg) {
                        var img_src = item.oimg;
                        if (!img_src.match(/http:\/\//g)) {
                            img_src = 'http://' + img_src;
                        }
                        oimg = '<a href="' + img_src + '" target="BLANK">Просмотреть</a>'
                    }

                    // Рисуем новый товар
                    var snippet = $('' +
                            '<tr id="product' + item.id + '" class="snippet" detail-id="' + item.id + '">' +
                            '   <td id="odetail_id' + item.id + '">' +
                            '      <input type="checkbox" name="odetail_id" value="' + item.id + '"/>' +
                            '      <br>' +
                            '      ' + item.id + '<br>' +
                            '      <img src="/static/images/lightbox-ico-loading.gif" style="" class="float" id="progress' + item.id + '">' +
                            '   </td>' +
                            '   <form method="POST" id="odetail' + item.id + '" enctype="multipart/form-data" action="/client/updateNewProduct/' + oObj.options.order_id + '/' + item.id + '">' +
                            '   <td style="text-align: left; vertical-align: bottom;">' +
                            '      <span class="plaintext">' +
                            '          <b>' + item.name + '</b><br>' +
                            '          <b>Магазин</b>: ' + item.oshop + '' +
                            '          <br>' +
                            '          <b>Количество</b>: ' + item.amount +
                            '          <b>Размер</b>:' + item.osize +
                            '          <b>Цвет</b>: <br>' + item.ocolor +
                            '          <b>Комментарий</b>:' + item.ocomment +
                            '      </span>' +
                            '      <span style="display: none;" class="producteditor">' +
                            '      <br>' +
                            '         <b>Наименование</b>:' +
                            '         <textarea name="name" class="name"></textarea>' +
                            '         <br>' +
                            '         <b>Магазин</b>:' +
                            '         <textarea name="shop" class="shop"></textarea>' +
                            '         <br>' +
                            '         <b>Количество</b>:' +
                            '         <textarea name="amount" class="amount int"></textarea>' +
                            '         <br>' +
                            '         <b>Размер</b>:' +
                            '         <textarea name="size" class="size"></textarea>' +
                            '         <br>' +
                            '         <b>Цвет</b>:' +
                            '         <textarea name="color" class="color"></textarea>' +
                            '         <br>' +
                            '         <b>Комментарий</b>:' +
                            '         <textarea name="comment" class="ocomment"></textarea>' +
                            '         <br>' +
                            '      </span>' +
                            '   </td>' +
                            '   <td>' +
                            '      <span class="plaintext">' +
                            '         ' + oimg + ' ' +
                            '      </span>' +
                            '      <span style="display: none;" class="producteditor">' +
                            '         <input type="radio" value="link" class="img_selector" name="img_selector">' +
                            '         <textarea name="img" class="image"></textarea>' +
                            '         <br>' +
                            '         <input type="radio" value="file" class="img_selector" name="img_selector">' +
                            '         <input type="file" name="userfile" class="img_file">' +
                            '      </span>' +
                            '   </td>' +
                            '   </form>' +
                            '   <td>' +
                            '      <input type="text" order-id="' + oObj.options.order_id + '" odetail-id="' + item.id + '" maxlength="11" style="width:60px" value="' + item.price + '" class="odetail_price int" name="odetail_price' + item.id + '" id="odetail_price' + item.id + '">' +
                            '   </td>' +
                            '   <td>' +
                            '      <input type="text" order-id="' + oObj.options.order_id + '" odetail-id="' + item.id + '" maxlength="11" style="width:60px" value="' + item.delivery + '" class="odetail_pricedelivery int" name="odetail_pricedelivery' + item.id + '" id="odetail_pricedelivery' + item.id + '">' +
                            '   </td>' +
                            '   <td>' +
                            '      <input type="text" order-id="' + oObj.options.order_id + '" odetail-id="' + item.id + '" maxlength="11" style="width:60px" value="' + item.weight + '" class="odetail_weight int" name="odetail_weight' + item.id + '" id="odetail_weight' + item.id + '">' +
                            '   </td>' +
                            '   <td>' +
                            '      <a class="edit" odetail-id="' + item.id + '" href="#">' +
                            '         <img border="0" title="Редактировать" src="/static/images/comment-edit.png"/></a>' +
                            '      <br>' +
                            '      <a class="delete" odetail-id="' + item.id + '" href="#">' +
                            '         <img border="0" title="Удалить" src="/static/images/delete.png"/></a>' +
                            '      <br>' +
                            '      <a style="display: none;" class="cancel" odetail-id="' + item.id + '" href="#">' +
                            '         <img border="0" title="Отменить" src="/static/images/comment-delete.png"/></a>' +
                            '      <br>' +
                            '      <a style="display: none;" class="save" odetail-id="' + item.id + '" href="#">' +
                            '         <img border="0" title="Сохранить" src="/static/images/done-filed.png"/></a>' +
                            '   </td>' +
                            '</tr>');

                    // Прикручиваем обработчики к кнопкам
                    snippet.find('a.delete').bind('click', iObj.deleteItem);
                    snippet.find('a.edit').bind('click', iObj.editItem);
                    snippet.find('a.cancel').bind('click', iObj.cancelItem);
                    snippet.find('a.save').bind('click', iObj.saveItem);

                    snippet.find('input.odetail_price').bind('change', iObj.updateItemPrice);
                    snippet.find('input.odetail_pricedelivery').bind('change', iObj.updateItemDeliveryPrice);
                    snippet.find('input.odetail_weight').bind('change', iObj.updateItemWeight);

                    return snippet;
                }

                iObj.drawRow = function (item) {
                    var snippet = iObj.getRow(item);
                    $('.' + oObj.options.type + '_order_form #new_products tr:first').after(snippet);
                    return snippet;
                }

                if (blankOrderData)
                {
                    var orderData = null;

                    for (var i = 0, n = blankOrderData.length; i < n; i++)
                    {
                        if (blankOrderData[i].order_type == oObj.options.type)
                        {
                            orderData = blankOrderData[i];
                        }
                    }

                    if (orderData)
                    {
                        oObj.options.order_id = orderData.order_id;

                        $.each(orderData.details, function (k, v)
                        {
                            if (selectedCurrency == '') {
                                selectedCurrency = orderData.order_currency;
                            }

                            oObj.options.cart.addCustom({
                                id:v.odetail_id,
                                name:v.odetail_product_name,
                                price:v.odetail_price,
                                delivery:v.odetail_pricedelivery,
                                weight:v.odetail_weight,
                                amount:v.odetail_product_amount,
                                currency:selectedCurrency,
                                oshop:v.odetail_shop,
                                ocolor:v.odetail_product_color,
                                osize:v.odetail_product_size,
                                oimg:v.odetail_img,
                                foto_requested:v.odetail_foto_requested,
                                ocomment:v.odetail_comment
                            });
                        });

                        // прикручиваем обработчики кнопок деталям заказа
                        $('.' + oObj.options.type + '_order_form #new_products a.edit').bind('click', iObj.editItem);
                        $('.' + oObj.options.type + '_order_form #new_products a.delete').bind('click', iObj.deleteItem);
                        $('.' + oObj.options.type + '_order_form #new_products a.save').bind('click', iObj.saveItem);
                        $('.' + oObj.options.type + '_order_form #new_products a.cancel').bind('click', iObj.cancelItem);
                        $('.' + oObj.options.type + '_order_form #new_products input.odetail_price').bind('change', iObj.updateItemPrice);
                        $('.' + oObj.options.type + '_order_form #new_products input.odetail_pricedelivery').bind('change', iObj.updateItemDeliveryPrice);
                        $('.' + oObj.options.type + '_order_form #new_products input.odetail_weight').bind('change', iObj.updateItemWeight);

                        oObj.updateTotals();
                    }

                    // Отображаем список товаров
                    $('#detailsForm').show();
                }
            }
            // начинаем инициализацию

            // Страна поступления, поле "Заказать из"
            country_from = new $.cpField();
            country_from.init({
                object:$('#country_from_offline'),
                useDd:true,
                onChange:function () {
                    var id = $(this).val();
                    var prevCurrency = selectedCurrency;

                    for (var index in currencies) {
                        var currency = currencies[index];

                        if (id == currency['country_id']) {
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
                object:$('#country_to_offline'),
                useDd:true,
                onChange:function () {
                    var id = $(this).val();
                    for (var index in currencies) {
                        var currency = currencies[index];

                        if (id == currency['country_id']) {
                            $('.' + oObj.options.type + '_order_form input.countryTo').val(id);
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
                object:$('#city_to_offline'),
                onChange:function () {
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
                object:$('#requested_delivery_offline')
            });
            oObj.fields.push(requested_delivery);

            var item = new itemOffline();
            item.init();

            $('#addItemOffline').unbind('click').bind('click', item.add);

            $('#'+oObj.options.type+'checkoutOrder').bind('click', saveOrder);

            // Отображаем форму
            $('div.order_type_selector').hide();
            $('h2#page_title').html(oObj.options.title);
            $("div."+oObj.options.type+"_order_form").show('slow');

        }
        // End initOffline

        var initService = function ()
        {
            oObj.options.type = 'service';
            oObj.options.title = 'Добавление новой услуги';
            oObj.options.cart = new $.cpCart();

            var itemService = function ()
            {
                var iObj = this;

                var addItemProgress = function ()
                {
                    var parent = $('#odetail');
                    var progress = $("<div class='progress'> " +
                            "<img class='float product_progress_bar' src='/static/images/lightbox-ico-loading.gif'/> " +
                            "</div>");
                    progress.css({
                        background:'none',
                        width:'32px',
                        height:'32px',
                        'margin-top':'6px',
                        float:'left'
                    });

                    parent.append(progress);
                } // End addItemProgress

                var removeItemProgress = function ()
                {
                    $('div.progress').remove();
                } // End removeItemProgress

                var bindAddItem = function ()
                {
                    // Добавляем обработчик к кнопке добавления товара к заказу
                    $('#addItemOffline').bind('click', iObj.add);
                } // End bindAddItem

                var unbindAddItem = function ()
                {
                    // Убираем обработчик у кнопки добавления товара к заказу
                    $('#addItemOffline').unbind('click');
                } // End unbindAddItem


                iObj.fields = [];

                iObj.init = function ()
                {
                    // Наименование товара
                    oname = new $.cpField();
                    oname.init({
                        object:$('#'+oObj.options.type+'ItemForm #oname')
                    });
                    oname.validate({
                        expression:'if (VAL == "") { return false; } else { return true; }',
                        message:'Необходимо указать название услуги'
                    });
                    iObj.fields.push(oname);

                    // Подробное описание что нужно сделать
                    ocomment = new $.cpField();
                    ocomment.init({
                        object:$('#'+oObj.options.type+'ItemForm #ocomment')
                    });
                    ocomment.validate({
                        expression:'if (VAL == "") { return false; } else { return true; }',
                        message:'Необходимо указать описание услуги'
                    });
                    iObj.fields.push(ocomment);

                    // Цена товара
                    oprice = new $.cpField();
                    oprice.init({
                        object:$('#'+oObj.options.type+'ItemForm #oprice'),
                        needCheck:'float',
                        onChange:function () {
                            $(this).val(parseFloat($(this).val(), 10));
                        }
                    });
                    iObj.fields.push(oprice);

                    // Местная доставка
                    odeliveryprice = new $.cpField();
                    odeliveryprice.init({
                        object:$('#'+oObj.options.type+'ItemForm #odeliveryprice'),
                        needCheck:'float',
                        onChange:function () {
                            $(this).val(parseFloat($(this).val(), 10));
                        }
                    });
                    iObj.fields.push(odeliveryprice);

                    // Примерный вес
                    oweight = new $.cpField();
                    oweight.init({
                        object:$('#'+oObj.options.type+'ItemForm #oweight'),
                        needCheck:'number',
                        onChange:function () {
                            if (!isNaN($(this).val()) && parseInt($(this).val(), 10) > 99999) {
                                $(this).val(99999);
                            }
                        }
                    });
                    oweight.validate({
                        expression:'if (VAL == "" || VAL == 0) { return false; } else { return true; }',
                        message:'Необходимо указать примерный вес товара'
                    });
                    iObj.fields.push(oweight);

                    // Скриншот
                    oimg = new $.cpField();
                    oimg.init({
                        object:$('#'+oObj.options.type+'ItemForm #oimg')
                    });
                    iObj.fields.push(oimg);

                    ofile = new $.cpField();
                    ofile.init({
                        object:$('#'+oObj.options.type+'ItemForm #ofile')
                    });
                    iObj.fields.push(ofile);
                }

                iObj.add = function () {
                    unbindAddItem();

                    // Рисуем новый товар
                    var item = oObj.options.cart.createCustomItem({
                        id:'',
                        name:fieldByName(iObj.fields, 'oname').val(),
                        price:fieldByName(iObj.fields, 'oprice').val(),
                        delivery:fieldByName(iObj.fields, 'odeliveryprice').val(),
                        weight:fieldByName(iObj.fields, 'oweight').val(),
                        currency:selectedCurrency,
                        oimg:fieldByName(iObj.fields, 'userfileimg').val(),
                        ocomment:fieldByName(iObj.fields, 'ocomment').val()
                    });
                    var row = iObj.drawRow(item);

                    // Отправляем на сервер данные
                    $('#'+oObj.options.type+'ItemForm').ajaxForm({
                        type:'POST',
                        dataType:'json',
                        iframe:true,
                        beforeSubmit:function (formData, jqForm, options) {
                            if (!checkOrder() || !checkItem(iObj, errorFields)) {
                                scrollFirstError();
                                bindAddItem();
                                $('.snippet').remove();
                                // Если товаров больше нет сворачиваем таблицу товаров
                                var prows = $('.' + oObj.options.type + '_order_form #new_products tr');
                                if (prows.length < 4) {
                                    $('.' + oObj.options.type + '_order_form #new_products').parent().hide('slow');
                                    $('.' + oObj.options.type + '_order_form .checkOutOrderBlock').hide('slow');
                                }

                                return false;
                            }

                            return true;
                        },
                        success:function (response) {
                            if (response) {
                                // Ответ не является числовым значением
                                if (isNaN(response.odetail_id) || isNaN(response.order_id)) {
                                    error('top', response);
                                }
                                // Все в порядке, добавляем товар
                                else {
                                    // проставляем всюду Id заказа
                                    $('input.order_id').val(response.order_id);
                                    oObj.options.order_id = response.order_id;

                                    var screenshot_code = '';
                                    if (response.odetail_img && response.odetail_img.match(/http:\/\//g)) {
                                        screenshot_code = '<a href="' + response.odetail_img + '" target="BLANK">Просмотреть</a>'
                                    }
                                    else if (parseInt(response.odetail_img, 10) != 0) {
                                        screenshot_code = "<a href='javascript:void(0)' onclick='setRel(" + response.odetail_img + ");'>Просмотреть</a> " +
                                                "<a rel='lightbox_" + response.odetail_img + "' href='/client/showScreen/" + response.odetail_img + "' style='display:none;'>Посмотреть</a>";
                                    }
                                    else {
                                        screenshot_code = '';
                                    }

                                    // Подставляем изображение или ссылку на него
                                    $('.snippet .oimg').html(screenshot_code);
                                    $('.snippet').removeClass('snippet').attr('detail-id', response.odetail_id);

                                    // Добавляем товар в корзину
                                    item.id = response.odetail_id;
                                    item.oimg = response.odetail_img;
                                    oObj.options.cart.addCustom(item);

                                    // перерисовываем позицию товара
                                    row.remove();
                                    row = iObj.drawRow(item);
                                    row.find('#progress' + item.id).hide();

                                    // пересчитываем заказ
                                    oObj.updateTotals();

                                    success('top', 'Услуга №' + response.odetail_id + ' успешно добавлена в корзину.');

                                    // чистим форму
                                    if (true) //debug only
                                    {
                                        formFieldsClear(iObj);
                                    }//debug only

                                    // Отображаем список товаров
                                    $('.' + oObj.options.type + '_order_form #new_products').parent().show();
                                    $('.' + oObj.options.type + '_order_form .checkOutOrderBlock').show('slow');
                                }
                            }
                            // Ответ не был получен
                            else {
                                row.find('#progress' + item.id).hide();
                                error('top', 'Услуга не добавлена. Заполните все поля и попробуйте еще раз.');
                            }
                        },
                        error:function (response) {
                            row.find('#progress' + item.id).hide();

                            if (response.status == 0) {
                                error('top', 'Услуга не добавлена. Попробуйте еще раз.');
                            }
                            else {
                                error('top', 'Услуга не добавлена. Заполните все поля и попробуйте еще раз.');
                            }
                        }, // End error
                        complete:function () {
                            bindAddItem();
                        }
                    });

                    addItemProgress();

                    $('#'+oObj.options.type+'ItemForm').submit();
                }

                iObj.deleteItem = function () {
                    var orderId = oObj.options.order_id;
                    var itemId = $(this).attr('odetail-id');

                    if (confirm("Вы уверены, что хотите удалить услугу №" + itemId + "?")) {
                        var order = this;
                        $.post('<?= $selfurl ?>deleteNewProduct/' + orderId + '/' + itemId, {}, function () {
                        }, 'json')
                                .success(function (responce) {
                                    // проверка на ошибку на сервере
                                    if (responce.e == -1) {
                                        error('top', responce.m);
                                    }
                                    else {
                                        oObj.options.cart.delete(itemId);
                                        // Удаляем строку товара
                                        $('tr#product' + itemId + '').remove();
                                        // Если товаров больше нет сворачиваем таблицу товаров
                                        var prows = $('.' + oObj.options.type + '_order_form #new_products tr');
                                        if (prows.length < 4) {
                                            $('.' + oObj.options.type + '_order_form #new_products').parent().hide('slow');
                                            $('.checkOutOrderBlock').hide('slow');
                                        }
                                        oObj.updateTotals();

                                        success('top', responce.m);
                                    }
                                })
                                .error(function (responce) {
                                    error('top', 'Услуга не удалена. Ошибка подключения.');
                                });
                    }
                    return false;
                }

                iObj.editItem = function () {
                    var itemId = $(this).attr('odetail-id');
                    $('tr#product' + itemId + ' .plaintext').hide();
                    $('tr#product' + itemId + ' .producteditor').show();
                    $('tr#product' + itemId + ' .edit, tr#product' + itemId + ' .delete').hide();
                    $('tr#product' + itemId + ' .save, tr#product' + itemId + ' .cancel').show();

                    var odetail = oObj.options.cart.getById(itemId);

                    $tr = $('tr#product' + itemId);

                    $tr.find('textarea.name').val(odetail['name']);
                    $tr.find('textarea.ocomment').val(odetail['ocomment']);
                    $tr.find('textarea.image').val(odetail['oimg']);
                    $tr.find('input.img_file').val(odetail['ouserfile']);

                    return false;
                }

                iObj.cancelItem = function () {
                    var itemId = $(this).attr('odetail-id');
                    $('tr#product' + itemId + ' .plaintext').show();
                    $('tr#product' + itemId + ' .producteditor').hide();
                    $('tr#product' + itemId + ' .edit, tr#product' + itemId + ' .delete').show();
                    $('tr#product' + itemId + ' .save, tr#product' + itemId + ' .cancel').hide();
                    return false;
                }

                iObj.saveItem = function () {
                    var itemId = $(this).attr('odetail-id'),
                            cart = oObj.options.cart,
                            odetail = cart.getById(itemId);

                    $tr = $('tr#product' + itemId);

                    odetail['name'] = $tr.find('textarea.name').val();
                    odetail['ocomment'] = $tr.find('textarea.ocomment').val();
                    odetail['img_selector'] = $tr.find('input.img_selector:checked').val();

                    if (odetail['img_selector'] == 'link') {
                        odetail['img'] = $tr.find('textarea.image').val();
                        odetail['img_file'] = '';
                    }
                    else if (odetail['img_selector'] == 'file') {
                        odetail['img'] = '';
                        odetail['userfile'] = $tr.find('input.img_file').val();
                    }

                    var form = $tr.find('form#odetail' + oObj.options.type + itemId);
                    form.ajaxForm({
                        dataType:'json',
                        iframe:true,
                        beforeSubmit:function () {
                            $('img#progress' + itemId).show();
                        },
                        error:function () {
                            error('top', 'Описание услуги №' + itemId + ' не сохранено.');
                        },
                        success:function (data) {
                            cart.update(odetail);
                            row = $('#product' + itemId);
                            row.after(iObj.getRow(odetail));
                            row.remove();
                            $('img#progress' + itemId).hide();

                            oObj.updateTotals();
                            success('top', data.message);
                        }
                    });
                    form.submit();

                    oObj.updateTotals();
                    return false;
                }

                iObj.updateItemPrice = function () {
                    var val = $(this).val();
                    var odetail_id = $(this).attr('odetail-id');
                    if (isNaN(val) || parseInt(val, 10) == 0) {
                        error('top', 'Стоимость не изменена. Укажите стоимость товара.');
                        return;
                    }

                    $('img#progress' + odetail_id).show();

                    $.post('/client/update_new_odetail_price/' + $(this).attr('order-id') + '/' + odetail_id + '/' + parseInt(val, 10), {},
                            function (responce) {
                                $('img#progress' + odetail_id).hide();
                                if (responce.is_error) {
                                    error('top', "Стоимость не изменена. " + responce.message);
                                }
                                else {
                                    success("top", "Стоимость товара №" + odetail_id + " успешно изменена");
                                    var item = oObj.options.cart.getById(odetail_id);
                                    item.price = val;
                                    oObj.options.cart.update(item);
                                    oObj.updateTotals();
                                }
                            },
                            'json')
                            .error(function (responce) {
                                $('img#progress' + odetail_id).hide();
                                error('top', "Стоимость не изменена. " + responce);
                            });
                }

                iObj.updateItemDeliveryPrice = function () {
                    var val = $(this).val();
                    var odetail_id = $(this).attr('odetail-id');
                    if (isNaN(val) || parseInt(val, 10) == 0) {
                        error('top', 'Стоимость не изменена. Укажите стоимость доставки.');
                        return;
                    }

                    $('img#progress' + odetail_id).show();

                    $.post('/client/update_new_odetail_pricedelivery/' + $(this).attr('order-id') + '/' + odetail_id + '/' + parseInt(val, 10), {},
                            function (responce) {
                                $('img#progress' + odetail_id).hide();
                                if (responce.is_error) {
                                    error('top', "Стоимость не изменена. " + responce.message);
                                }
                                else {
                                    success("top", "Стоимость доставки №" + odetail_id + " успешно изменена");
                                    var item = oObj.options.cart.getById(odetail_id);
                                    item.delivery = val;
                                    oObj.options.cart.update(item);
                                    oObj.updateTotals();
                                }
                            },
                            'json')
                            .error(function (responce) {
                                $('img#progress' + odetail_id).hide();
                                error('top', "Стоимость не изменена. " + responce);
                            });
                }

                iObj.updateItemWeight = function () {
                    var val = $(this).val();
                    var odetail_id = $(this).attr('odetail-id');
                    if (isNaN(val) || parseInt(val, 10) == 0) {
                        error('top', 'Вес не изменен. Укажите вес товара.');
                        return;
                    }

                    $('img#progress' + odetail_id).show();

                    $.post('/client/update_new_odetail_weight/' + $(this).attr('order-id') + '/' + odetail_id + '/' + parseInt(val, 10), {},
                            function (responce) {
                                $('img#progress' + odetail_id).hide();
                                if (responce.is_error) {
                                    error('top', "Вес не изменен. " + responce.message);
                                }
                                else {
                                    success("top", "Вес товара №" + odetail_id + " успешно изменен");
                                    var item = oObj.options.cart.getById(odetail_id);
                                    item.weight = val;
                                    oObj.options.cart.update(item);
                                    oObj.updateTotals();
                                }
                            },
                            'json')
                            .error(function (responce) {
                                $('img#progress' + odetail_id).hide();
                                error('top', "Вес не изменен. " + responce);
                            });
                }

                iObj.getRow = function (item)
                {
                    if (item.oimg == 0) {
                        oimg = '';
                    }
                    else if (item.oimg === null) {
                        oimg = "<a href='javascript:void(0)' onclick='setRel(" + item.id + ");'>" +
                                "<img width='55px' height='55px' src='/client/showScreen/" + item.id + "'></a>" +
                                "<a rel='lightbox_" + item.id + "' href='/client/showScreen/" + item.id + "' style='display:none;'>Посмотреть</a>";
                    }
                    else if (item.oimg) {
                        var img_src = item.oimg;
                        if (!img_src.match(/http:\/\//g)) {
                            img_src = 'http://' + img_src;
                        }
                        oimg = '<a href="' + img_src + '" target="BLANK">Просмотреть</a>'
                    }

                    // Рисуем новый товар
                    var snippet = $('' +
                            '<tr id="product' + item.id + '" class="snippet" detail-id="' + item.id + '">' +
                            '   <td id="odetail_id' + item.id + '">' +
                            '      <input type="checkbox" name="odetail_id" value="' + item.id + '"/>' +
                            '      <br>' +
                            '      ' + item.id + '<br>' +
                            '      <img src="/static/images/lightbox-ico-loading.gif" style="" class="float" id="progress' + item.id + '">' +
                            '   </td>' +
                            '   <form method="POST" id="odetail' + item.id + '" enctype="multipart/form-data" action="/client/updateNewProduct/' + oObj.options.order_id + '/' + item.id + '">' +
                            '   <td style="text-align: left; vertical-align: bottom;">' +
                            '      <span class="plaintext">' +
                            '          <b>' + item.name + '</b>' +
                            '          <br>' +
                            '          <b>Описание услуги</b>:' + item.ocomment +
                            '      </span>' +
                            '      <span style="display: none;" class="producteditor">' +
                            '      <br>' +
                            '         <b>Наименование</b>:' +
                            '         <textarea name="name" class="name"></textarea>' +
                            '         <br>' +
                            '         <b>Описание услуги</b>:' +
                            '         <textarea name="comment" class="ocomment"></textarea>' +
                            '         <br>' +
                            '      </span>' +
                            '   </td>' +
                            '   <td>' +
                            '      <span class="plaintext">' +
                            '         ' + oimg + ' ' +
                            '      </span>' +
                            '      <span style="display: none;" class="producteditor">' +
                            '         <input type="radio" value="link" class="img_selector" name="img_selector">' +
                            '         <textarea name="img" class="image"></textarea>' +
                            '         <br>' +
                            '         <input type="radio" value="file" class="img_selector" name="img_selector">' +
                            '         <input type="file" name="userfile" class="img_file">' +
                            '      </span>' +
                            '   </td>' +
                            '   </form>' +
                            '   <td>' +
                            '      <input type="text" order-id="' + oObj.options.order_id + '" odetail-id="' + item.id + '" maxlength="11" style="width:60px" value="' + item.price + '" class="odetail_price int" name="odetail_price' + item.id + '" id="odetail_price' + item.id + '">' +
                            '   </td>' +
                            '   <td>' +
                            '      <input type="text" order-id="' + oObj.options.order_id + '" odetail-id="' + item.id + '" maxlength="11" style="width:60px" value="' + item.delivery + '" class="odetail_pricedelivery int" name="odetail_pricedelivery' + item.id + '" id="odetail_pricedelivery' + item.id + '">' +
                            '   </td>' +
                            '   <td>' +
                            '      <input type="text" order-id="' + oObj.options.order_id + '" odetail-id="' + item.id + '" maxlength="11" style="width:60px" value="' + item.weight + '" class="odetail_weight int" name="odetail_weight' + item.id + '" id="odetail_weight' + item.id + '">' +
                            '   </td>' +
                            '   <td>' +
                            '      <a class="edit" odetail-id="' + item.id + '" href="#">' +
                            '         <img border="0" title="Редактировать" src="/static/images/comment-edit.png"/></a>' +
                            '      <br>' +
                            '      <a class="delete" odetail-id="' + item.id + '" href="#">' +
                            '         <img border="0" title="Удалить" src="/static/images/delete.png"/></a>' +
                            '      <br>' +
                            '      <a style="display: none;" class="cancel" odetail-id="' + item.id + '" href="#">' +
                            '         <img border="0" title="Отменить" src="/static/images/comment-delete.png"/></a>' +
                            '      <br>' +
                            '      <a style="display: none;" class="save" odetail-id="' + item.id + '" href="#">' +
                            '         <img border="0" title="Сохранить" src="/static/images/done-filed.png"/></a>' +
                            '   </td>' +
                            '</tr>');

                    // Прикручиваем обработчики к кнопкам
                    snippet.find('a.delete').bind('click', iObj.deleteItem);
                    snippet.find('a.edit').bind('click', iObj.editItem);
                    snippet.find('a.cancel').bind('click', iObj.cancelItem);
                    snippet.find('a.save').bind('click', iObj.saveItem);

                    snippet.find('input.odetail_price').bind('change', iObj.updateItemPrice);
                    snippet.find('input.odetail_pricedelivery').bind('change', iObj.updateItemDeliveryPrice);
                    snippet.find('input.odetail_weight').bind('change', iObj.updateItemWeight);

                    return snippet;
                }

                iObj.drawRow = function (item) {
                    var snippet = iObj.getRow(item);
                    $('.' + oObj.options.type + '_order_form #new_products tr:first').after(snippet);
                    return snippet;
                }

                if (blankOrderData)
                {
                    var orderData = null;

                    for (var i = 0, n = blankOrderData.length; i < n; i++)
                    {
                        if (blankOrderData[i].order_type == oObj.options.type)
                        {
                            orderData = blankOrderData[i];
                        }
                    }

                    if (orderData)
                    {
                        oObj.options.order_id = orderData.order_id;

                        $.each(orderData.details, function (k, v)
                        {
                            if (selectedCurrency == '') {
                                selectedCurrency = orderData.order_currency;
                            }

                            oObj.options.cart.addCustom({
                                id:v.odetail_id,
                                name:v.odetail_product_name,
                                price:v.odetail_price,
                                delivery:v.odetail_pricedelivery,
                                weight:v.odetail_weight,
                                amount:v.odetail_product_amount,
                                currency:selectedCurrency,
                                oshop:v.odetail_shop,
                                ocolor:v.odetail_product_color,
                                osize:v.odetail_product_size,
                                oimg:v.odetail_img,
                                foto_requested:v.odetail_foto_requested,
                                ocomment:v.odetail_comment
                            });
                        });

                        // прикручиваем обработчики кнопок деталям заказа
                        $('.' + oObj.options.type + '_order_form #new_products a.edit').bind('click', iObj.editItem);
                        $('.' + oObj.options.type + '_order_form #new_products a.delete').bind('click', iObj.deleteItem);
                        $('.' + oObj.options.type + '_order_form #new_products a.save').bind('click', iObj.saveItem);
                        $('.' + oObj.options.type + '_order_form #new_products a.cancel').bind('click', iObj.cancelItem);
                        $('.' + oObj.options.type + '_order_form #new_products input.odetail_price').bind('change', iObj.updateItemPrice);
                        $('.' + oObj.options.type + '_order_form #new_products input.odetail_pricedelivery').bind('change', iObj.updateItemDeliveryPrice);
                        $('.' + oObj.options.type + '_order_form #new_products input.odetail_weight').bind('change', iObj.updateItemWeight);

                        oObj.updateTotals();
                    }

                    // Отображаем список товаров
                    $('#detailsForm').show();
                }
            }
            // начинаем инициализацию

            // Страна поступления, поле "Заказать из"
            country_from = new $.cpField();
            country_from.init({
                object:$('#country_from_service'),
                useDd:true,
                onChange:function () {
                    var id = $(this).val();
                    var prevCurrency = selectedCurrency;

                    for (var index in currencies) {
                        var currency = currencies[index];

                        if (id == currency['country_id']) {
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
                object:$('#country_to_service'),
                useDd:true,
                onChange:function () {
                    var id = $(this).val();
                    for (var index in currencies) {
                        var currency = currencies[index];

                        if (id == currency['country_id']) {
                            $('.' + oObj.options.type + '_order_form input.countryTo').val(id);
                            countryTo = currency['country_name'];
                            oObj.updateTotals();
                            break;
                        }
                    }
                    updateProductForm();
                }
            });
            country_to.validate({
                expression:'if (VAL == 0 && $("#delivery_need_y:checked").val()) { return false; } else { return true; }',
                message:'Необходимо выбрать страну доставки'
            });
            oObj.fields.push(country_to);

            // Город доставки, поле "Город доставки"
            city_to = new $.cpField();
            city_to.init({
                object:$('#city_to_service'),
                onChange:function () {
                    updateProductForm();
                }
            });
            city_to.validate({
                expression:'if (VAL == "" && $("#delivery_need_y:checked").val()) { return false; } else { return true; }',
                message:'Необходимо выбрать город доставки'
            });
            oObj.fields.push(city_to);

            // Cпособ доставки, поле "Cпособ доставки"
            requested_delivery = new $.cpField();
            requested_delivery.init({
                object:$('#requested_delivery_service')
            });
            oObj.fields.push(requested_delivery);

            var item = new itemService();
            item.init();

            $('#addItemService').unbind('click').bind('click', item.add);

            $('#'+oObj.options.type+'checkoutOrder').bind('click', saveOrder);

            // Отображаем форму
            $('div.order_type_selector').hide();
            $('h2#page_title').html(oObj.options.title);
            $("div."+oObj.options.type+"_order_form").show('slow');

        }
        // End initService

        var initDelivery = function ()
        {
            oObj.options.type = 'delivery';
            oObj.options.title = 'Добавление нового заказа на доставку';
            oObj.options.cart = new $.cpCart();

            var itemDelivery = function ()
            {
                var iObj = this;

                var addItemProgress = function ()
                {
                    var parent = $('#odetail');
                    var progress = $("<div class='progress'> " +
                            "<img class='float product_progress_bar' src='/static/images/lightbox-ico-loading.gif'/> " +
                            "</div>");
                    progress.css({
                        background:'none',
                        width:'32px',
                        height:'32px',
                        'margin-top':'6px',
                        float:'left'
                    });

                    parent.append(progress);
                } // End addItemProgress

                var removeItemProgress = function ()
                {
                    $('div.progress').remove();
                } // End removeItemProgress

                var bindAddItem = function ()
                {
                    // Добавляем обработчик к кнопке добавления товара к заказу
                    $('#addItemOffline').bind('click', iObj.add);
                } // End bindAddItem

                var unbindAddItem = function ()
                {
                    // Убираем обработчик у кнопки добавления товара к заказу
                    $('#addItemOffline').unbind('click');
                } // End unbindAddItem


                iObj.fields = [];

                iObj.init = function ()
                {
                    // Наименование товара
                    oname = new $.cpField();
                    oname.init({
                        object:$('#'+oObj.options.type+'ItemForm #oname')
                    });
                    oname.validate({
                        expression:'if (VAL == "") { return false; } else { return true; }',
                        message:'Необходимо указать название услуги'
                    });
                    iObj.fields.push(oname);

                    // Ссылка
                    olink = new $.cpField();
                    olink.init({
                        object:$('#'+oObj.options.type+'ItemForm #olink')
                    });
                    iObj.fields.push(olink);

                    // Количество
                    oamount = new $.cpField();
                    oamount.init({
                        object:$('#'+oObj.options.type+'ItemForm #oamount')
                    });
                    oamount.validate({
                        expression:'if (VAL == "") { return false; } else { return true; }',
                        message:'Необходимо указать количество'
                    });
                    iObj.fields.push(oamount);

                    // Примерный вес
                    oweight = new $.cpField();
                    oweight.init({
                        object:$('#'+oObj.options.type+'ItemForm #oweight'),
                        needCheck:'number',
                        onChange:function () {
                            if (!isNaN($(this).val()) && parseInt($(this).val(), 10) > 99999) {
                                $(this).val(99999);
                            }
                        }
                    });
                    oweight.validate({
                        expression:'if (VAL == "" || VAL == 0) { return false; } else { return true; }',
                        message:'Необходимо указать примерный вес товара'
                    });
                    iObj.fields.push(oweight);


                    // Объём
                    ovolume = new $.cpField();
                    ovolume.init({
                        object:$('#'+oObj.options.type+'ItemForm #ovolume')
                    });
                    iObj.fields.push(ovolume);


                    // Тнвэд
                    otnved = new $.cpField();
                    otnved.init({
                        object:$('#'+oObj.options.type+'ItemForm #otnved')
                    });
                    iObj.fields.push(otnved);


                    // Страховка
                    insurance_need = new $.cpField();
                    insurance_need.init({
                        object:$('#'+oObj.options.type+'ItemForm input[name="insurance_need"]:checked')
                    });
                    iObj.fields.push(insurance_need);

                    // Коментарий к товару
                    ocomment = new $.cpField();
                    ocomment.init({
                        object:$('#'+oObj.options.type+'ItemForm #ocomment')
                    });
                    iObj.fields.push(ocomment);

                    // Цена товара
                    oprice = new $.cpField();
                    oprice.init({
                        object:$('#'+oObj.options.type+'ItemForm #oprice'),
                        needCheck:'float',
                        onChange:function () {
                            $(this).val(parseFloat($(this).val(), 10));
                        }
                    });
                    iObj.fields.push(oprice);

                    // Местная доставка
                    odeliveryprice = new $.cpField();
                    odeliveryprice.init({
                        object:$('#'+oObj.options.type+'ItemForm #odeliveryprice'),
                        needCheck:'float',
                        onChange:function () {
                            $(this).val(parseFloat($(this).val(), 10));
                        }
                    });
                    iObj.fields.push(odeliveryprice);
                }

                iObj.add = function () {
                    unbindAddItem();

                    // Рисуем новый товар
                    var item = oObj.options.cart.createCustomItem({
                        id:'',
                        name:fieldByName(iObj.fields, 'oname').val(),
                        olink:fieldByName(iObj.fields, 'olink').val(),
                        ovolume:fieldByName(iObj.fields, 'ovolume').val(),
                        otnved:fieldByName(iObj.fields, 'otnved').val(),
                        insurance_need:fieldByName(iObj.fields, 'insurance_need').val(),
                        price:fieldByName(iObj.fields, 'oprice').val(),
                        delivery:fieldByName(iObj.fields, 'odeliveryprice').val(),
                        oamount:fieldByName(iObj.fields, 'oamount').val(),
                        weight:fieldByName(iObj.fields, 'oweight').val(),
                        ocomment:fieldByName(iObj.fields, 'ocomment').val(),
                        currency:selectedCurrency
                    });
                    var row = iObj.drawRow(item);

                    // Отправляем на сервер данные
                    $('#'+oObj.options.type+'ItemForm').ajaxForm({
                        type:'POST',
                        dataType:'json',
                        iframe:true,
                        beforeSubmit:function (formData, jqForm, options) {
                            if (!checkOrder() || !checkItem(iObj, errorFields)) {
                                scrollFirstError();
                                bindAddItem();
                                $('.snippet').remove();
                                // Если товаров больше нет сворачиваем таблицу товаров
                                var prows = $('.' + oObj.options.type + '_order_form #new_products tr');
                                if (prows.length < 4) {
                                    $('.' + oObj.options.type + '_order_form #new_products').parent().hide('slow');
                                    $('.' + oObj.options.type + '_order_form .checkOutOrderBlock').hide('slow');
                                }

                                return false;
                            }

                            return true;
                        },
                        success:function (response) {
                            if (response) {
                                // Ответ не является числовым значением
                                if (isNaN(response.odetail_id) || isNaN(response.order_id)) {
                                    error('top', response);
                                }
                                // Все в порядке, добавляем товар
                                else {
                                    // проставляем всюду Id заказа
                                    $('input.order_id').val(response.order_id);
                                    oObj.options.order_id = response.order_id;

                                    var screenshot_code = '';
                                    if (response.odetail_img && response.odetail_img.match(/http:\/\//g)) {
                                        screenshot_code = '<a href="' + response.odetail_img + '" target="BLANK">Просмотреть</a>'
                                    }
                                    else if (parseInt(response.odetail_img, 10) != 0) {
                                        screenshot_code = "<a href='javascript:void(0)' onclick='setRel(" + response.odetail_img + ");'>Просмотреть</a> " +
                                                "<a rel='lightbox_" + response.odetail_img + "' href='/client/showScreen/" + response.odetail_img + "' style='display:none;'>Посмотреть</a>";
                                    }
                                    else {
                                        screenshot_code = '';
                                    }

                                    // Подставляем изображение или ссылку на него
                                    $('.snippet .oimg').html(screenshot_code);
                                    $('.snippet').removeClass('snippet').attr('detail-id', response.odetail_id);

                                    // Добавляем товар в корзину
                                    item.id = response.odetail_id;
                                    item.oimg = response.odetail_img;
                                    oObj.options.cart.addCustom(item);

                                    // перерисовываем позицию товара
                                    row.remove();
                                    row = iObj.drawRow(item);
                                    row.find('#progress' + item.id).hide();

                                    // пересчитываем заказ
                                    oObj.updateTotals();

                                    success('top', 'Товар №' + response.odetail_id + ' успешно добавлен в корзину.');

                                    // чистим форму
                                    if (true) //debug only
                                    {
                                        formFieldsClear(iObj);
                                    }//debug only

                                    // Отображаем список товаров
                                    $('.' + oObj.options.type + '_order_form #new_products').parent().show();
                                    $('.' + oObj.options.type + '_order_form .checkOutOrderBlock').show('slow');
                                }
                            }
                            // Ответ не был получен
                            else {
                                row.find('#progress' + item.id).hide();
                                error('top', 'Товар не добавлен. Заполните все поля и попробуйте еще раз.');
                            }
                        },
                        error:function (response) {
                            row.find('#progress' + item.id).hide();

                            if (response.status == 0) {
                                error('top', 'Товар не добавлен. Попробуйте еще раз.');
                            }
                            else {
                                error('top', 'Товар не добавлен. Заполните все поля и попробуйте еще раз.');
                            }
                        }, // End error
                        complete:function () {
                            bindAddItem();
                        }
                    });

                    addItemProgress();

                    $('#'+oObj.options.type+'ItemForm').submit();
                }

                iObj.deleteItem = function () {
                    var orderId = oObj.options.order_id;
                    var itemId = $(this).attr('odetail-id');

                    if (confirm("Вы уверены, что хотите удалить услугу №" + itemId + "?")) {
                        var order = this;
                        $.post('<?= $selfurl ?>deleteNewProduct/' + orderId + '/' + itemId, {}, function () {
                        }, 'json')
                                .success(function (responce) {
                                    // проверка на ошибку на сервере
                                    if (responce.e == -1) {
                                        error('top', responce.m);
                                    }
                                    else {
                                        oObj.options.cart.delete(itemId);
                                        // Удаляем строку товара
                                        $('tr#product' + itemId + '').remove();
                                        // Если товаров больше нет сворачиваем таблицу товаров
                                        var prows = $('.' + oObj.options.type + '_order_form #new_products tr');
                                        if (prows.length < 4) {
                                            $('.' + oObj.options.type + '_order_form #new_products').parent().hide('slow');
                                            $('.checkOutOrderBlock').hide('slow');
                                        }
                                        oObj.updateTotals();

                                        success('top', responce.m);
                                    }
                                })
                                .error(function (responce) {
                                    error('top', 'Услуга не удалена. Ошибка подключения.');
                                });
                    }
                    return false;
                }

                iObj.editItem = function () {
                    var itemId = $(this).attr('odetail-id');
                    $('tr#product' + itemId + ' .plaintext').hide();
                    $('tr#product' + itemId + ' .producteditor').show();
                    $('tr#product' + itemId + ' .edit, tr#product' + itemId + ' .delete').hide();
                    $('tr#product' + itemId + ' .save, tr#product' + itemId + ' .cancel').show();

                    var odetail = oObj.options.cart.getById(itemId);

                    $tr = $('tr#product' + itemId);

                    $tr.find('textarea.name').val(odetail['name']);
                    $tr.find('textarea.link').val(odetail['olink']);
                    $tr.find('textarea.ocomment').val(odetail['ocomment']);
                    $tr.find('textarea.volume').val(parseFloat(odetail['ovolume']));
                    $tr.find('textarea.tnved').val(odetail['otnved']);
                    $tr.find('textarea.amount').val(parseInt(odetail['amount'], 10));
                    if (odetail['insurance_need'])
                    {
                        $tr.find('input#insurance_y').attr('checked', 'checked');
                    }
                    else
                    {
                        $tr.find('input#insurance_n').attr('checked', 'checked');
                    }

                    return false;
                }

                iObj.cancelItem = function () {
                    var itemId = $(this).attr('odetail-id');
                    $('tr#product' + itemId + ' .plaintext').show();
                    $('tr#product' + itemId + ' .producteditor').hide();
                    $('tr#product' + itemId + ' .edit, tr#product' + itemId + ' .delete').show();
                    $('tr#product' + itemId + ' .save, tr#product' + itemId + ' .cancel').hide();
                    return false;
                }

                iObj.saveItem = function () {
                    var itemId = $(this).attr('odetail-id'),
                            cart = oObj.options.cart,
                            odetail = cart.getById(itemId);

                    $tr = $('tr#product' + itemId);

                    odetail['name'] = $tr.find('textarea.name').val();
                    odetail['ocomment'] = $tr.find('textarea.ocomment').val();
                    odetail['img_selector'] = $tr.find('input.img_selector:checked').val();

                    if (odetail['img_selector'] == 'link') {
                        odetail['img'] = $tr.find('textarea.image').val();
                        odetail['img_file'] = '';
                    }
                    else if (odetail['img_selector'] == 'file') {
                        odetail['img'] = '';
                        odetail['userfile'] = $tr.find('input.img_file').val();
                    }

                    var form = $tr.find('form#odetail' + oObj.options.type + itemId);
                    form.ajaxForm({
                        dataType:'json',
                        iframe:true,
                        beforeSubmit:function () {
                            $('img#progress' + itemId).show();
                        },
                        error:function () {
                            error('top', 'Описание услуги №' + itemId + ' не сохранено.');
                        },
                        success:function (data) {
                            cart.update(odetail);
                            row = $('#product' + itemId);
                            row.after(iObj.getRow(odetail));
                            row.remove();
                            $('img#progress' + itemId).hide();

                            oObj.updateTotals();
                            success('top', data.message);
                        }
                    });
                    form.submit();

                    oObj.updateTotals();
                    return false;
                }

                iObj.updateItemPrice = function () {
                    var val = $(this).val();
                    var odetail_id = $(this).attr('odetail-id');
                    if (isNaN(val) || parseInt(val, 10) == 0) {
                        error('top', 'Стоимость не изменена. Укажите стоимость товара.');
                        return;
                    }

                    $('img#progress' + odetail_id).show();

                    $.post('/client/update_new_odetail_price/' + $(this).attr('order-id') + '/' + odetail_id + '/' + parseInt(val, 10), {},
                            function (responce) {
                                $('img#progress' + odetail_id).hide();
                                if (responce.is_error) {
                                    error('top', "Стоимость не изменена. " + responce.message);
                                }
                                else {
                                    success("top", "Стоимость товара №" + odetail_id + " успешно изменена");
                                    var item = oObj.options.cart.getById(odetail_id);
                                    item.price = val;
                                    oObj.options.cart.update(item);
                                    oObj.updateTotals();
                                }
                            },
                            'json')
                            .error(function (responce) {
                                $('img#progress' + odetail_id).hide();
                                error('top', "Стоимость не изменена. " + responce);
                            });
                }

                iObj.updateItemDeliveryPrice = function () {
                    var val = $(this).val();
                    var odetail_id = $(this).attr('odetail-id');
                    if (isNaN(val) || parseInt(val, 10) == 0) {
                        error('top', 'Стоимость не изменена. Укажите стоимость доставки.');
                        return;
                    }

                    $('img#progress' + odetail_id).show();

                    $.post('/client/update_new_odetail_pricedelivery/' + $(this).attr('order-id') + '/' + odetail_id + '/' + parseInt(val, 10), {},
                            function (responce) {
                                $('img#progress' + odetail_id).hide();
                                if (responce.is_error) {
                                    error('top', "Стоимость не изменена. " + responce.message);
                                }
                                else {
                                    success("top", "Стоимость доставки №" + odetail_id + " успешно изменена");
                                    var item = oObj.options.cart.getById(odetail_id);
                                    item.delivery = val;
                                    oObj.options.cart.update(item);
                                    oObj.updateTotals();
                                }
                            },
                            'json')
                            .error(function (responce) {
                                $('img#progress' + odetail_id).hide();
                                error('top', "Стоимость не изменена. " + responce);
                            });
                }

                iObj.updateItemWeight = function () {
                    var val = $(this).val();
                    var odetail_id = $(this).attr('odetail-id');
                    if (isNaN(val) || parseInt(val, 10) == 0) {
                        error('top', 'Вес не изменен. Укажите вес товара.');
                        return;
                    }

                    $('img#progress' + odetail_id).show();

                    $.post('/client/update_new_odetail_weight/' + $(this).attr('order-id') + '/' + odetail_id + '/' + parseInt(val, 10), {},
                            function (responce) {
                                $('img#progress' + odetail_id).hide();
                                if (responce.is_error) {
                                    error('top', "Вес не изменен. " + responce.message);
                                }
                                else {
                                    success("top", "Вес товара №" + odetail_id + " успешно изменен");
                                    var item = oObj.options.cart.getById(odetail_id);
                                    item.weight = val;
                                    oObj.options.cart.update(item);
                                    oObj.updateTotals();
                                }
                            },
                            'json')
                            .error(function (responce) {
                                $('img#progress' + odetail_id).hide();
                                error('top', "Вес не изменен. " + responce);
                            });
                }

                iObj.getRow = function (item)
                {
                    if (!item.olink) {
                        link = '';
                    }
                    else if (item.olink) {
                        var link = item.olink;
                        if (!link.match(/http:\/\//g)) {
                            link = 'http://' + src;
                        }
                    }

                    // Рисуем новый товар
                    var snippet = $('' +
                            '<tr id="product' + item.id + '" class="snippet" detail-id="' + item.id + '">' +
                            '   <td id="odetail_id' + item.id + '">' +
                            '      <input type="checkbox" name="odetail_id" value="' + item.id + '"/>' +
                            '      <br>' +
                            '      ' + item.id + '<br>' +
                            '      <img src="/static/images/lightbox-ico-loading.gif" style="" class="float" id="progress' + item.id + '">' +
                            '   </td>' +
                            '   <form method="POST" id="odetail' + item.id + '" enctype="multipart/form-data" action="/client/updateNewProduct/' + oObj.options.order_id + '/' + item.id + '">' +
                            '   <td style="text-align: left; vertical-align: bottom;">' +
                            '      <span class="plaintext">' +
                            '           <b>' + ((link) ? '<a href="' + link+ '" target="BLANK">' : '') + item.name + ((link) ? '</a>' : '')+'</b>' +
                            '           '+((item.insurance) ? ' (требуется страховка) ' : '') +
                            '           <br>' +
                            '           <b>Количество</b>: ' + item.amount+ ' ' +
                            '           <b>Объём</b>: ' + item.ovolume + ' ' +
                            '           <b>ТН ВЭД</b>: ' + item.otnved + ' ' +
                            '           <br>' +
                            '           <b>Комментарий</b>: ' + item.ocomment + ' ' +
                            '      </span>' +
                            '      <span style="display: none;" class="producteditor">' +
                            '           <br>' +
                            '           <b>Наименование</b>:' +
                            '           <textarea class="name" name="name"></textarea>' +
                            '           <br>' +
                            '           <b>Ссылка на товар</b>:' +
                            '           <textarea class="link" name="link"></textarea>' +
                            '           <br>' +
                            '           <b>Количество</b>:' +
                            '           <textarea class="amount int" name="amount"></textarea>' +
                            '           <br>' +
                            '           <b>Объём</b>:' +
                            '           <textarea class="volume" name="volume"></textarea>' +
                            '           <br>' +
                            '           <b>ТН ВЭД</b>:' +
                            '           <textarea class="tnved" name="tnved"></textarea>' +
                            '           <br>' +
                            '           <b>Требуется страховка?</b>' +
                            '           <div style="float:right">' +
                            '           <label><input type="radio" name="insurance" id="insurance_y" value="1"/> Да</label>' +
                            '           <label><input type="radio" name="insurance" id="insurance_n" value="0"/> Нет</label>' +
                            '           </div>' +
                            '           <br>' +
                            '           <b>Комментарий</b>:' +
                            '           <textarea class="ocomment" name="comment"></textarea>' +
                            '           <br>' +
                            '      </span>' +
                            '   </td>' +
                            '   <td>' +
                            '      <span class="plaintext">' +
                            '         ' + oimg + ' ' +
                            '      </span>' +
                            '      <span style="display: none;" class="producteditor">' +
                            '         <input type="radio" value="link" class="img_selector" name="img_selector">' +
                            '         <textarea name="img" class="image"></textarea>' +
                            '         <br>' +
                            '         <input type="radio" value="file" class="img_selector" name="img_selector">' +
                            '         <input type="file" name="userfile" class="img_file">' +
                            '      </span>' +
                            '   </td>' +
                            '   </form>' +
                            '   <td>' +
                            '      <input type="text" order-id="' + oObj.options.order_id + '" odetail-id="' + item.id + '" maxlength="11" style="width:60px" value="' + item.price + '" class="odetail_price int" name="odetail_price' + item.id + '" id="odetail_price' + item.id + '">' +
                            '   </td>' +
                            '   <td>' +
                            '      <input type="text" order-id="' + oObj.options.order_id + '" odetail-id="' + item.id + '" maxlength="11" style="width:60px" value="' + item.delivery + '" class="odetail_pricedelivery int" name="odetail_pricedelivery' + item.id + '" id="odetail_pricedelivery' + item.id + '">' +
                            '   </td>' +
                            '   <td>' +
                            '      <input type="text" order-id="' + oObj.options.order_id + '" odetail-id="' + item.id + '" maxlength="11" style="width:60px" value="' + item.weight + '" class="odetail_weight int" name="odetail_weight' + item.id + '" id="odetail_weight' + item.id + '">' +
                            '   </td>' +
                            '   <td>' +
                            '      <a class="edit" odetail-id="' + item.id + '" href="#">' +
                            '         <img border="0" title="Редактировать" src="/static/images/comment-edit.png"/></a>' +
                            '      <br>' +
                            '      <a class="delete" odetail-id="' + item.id + '" href="#">' +
                            '         <img border="0" title="Удалить" src="/static/images/delete.png"/></a>' +
                            '      <br>' +
                            '      <a style="display: none;" class="cancel" odetail-id="' + item.id + '" href="#">' +
                            '         <img border="0" title="Отменить" src="/static/images/comment-delete.png"/></a>' +
                            '      <br>' +
                            '      <a style="display: none;" class="save" odetail-id="' + item.id + '" href="#">' +
                            '         <img border="0" title="Сохранить" src="/static/images/done-filed.png"/></a>' +
                            '   </td>' +
                            '</tr>');

                    // Прикручиваем обработчики к кнопкам
                    snippet.find('a.delete').bind('click', iObj.deleteItem);
                    snippet.find('a.edit').bind('click', iObj.editItem);
                    snippet.find('a.cancel').bind('click', iObj.cancelItem);
                    snippet.find('a.save').bind('click', iObj.saveItem);

                    snippet.find('input.odetail_price').bind('change', iObj.updateItemPrice);
                    snippet.find('input.odetail_pricedelivery').bind('change', iObj.updateItemDeliveryPrice);
                    snippet.find('input.odetail_weight').bind('change', iObj.updateItemWeight);

                    return snippet;
                }

                iObj.drawRow = function (item) {
                    var snippet = iObj.getRow(item);
                    $('.' + oObj.options.type + '_order_form #new_products tr:first').after(snippet);
                    return snippet;
                }

                if (blankOrderData)
                {
                    var orderData = null;

                    for (var i = 0, n = blankOrderData.length; i < n; i++)
                    {
                        if (blankOrderData[i].order_type == oObj.options.type)
                        {
                            orderData = blankOrderData[i];
                        }
                    }

                    if (orderData)
                    {
                        oObj.options.order_id = orderData.order_id;

                        $.each(orderData.details, function (k, v)
                        {
                            if (selectedCurrency == '') {
                                selectedCurrency = orderData.order_currency;
                            }

                            oObj.options.cart.addCustom({
                                id:v.odetail_id,
                                name:v.odetail_product_name,
                                olink:v.odetail_link,
                                price:v.odetail_price,
                                delivery:v.odetail_pricedelivery,
                                weight:v.odetail_weight,
                                amount:v.odetail_product_amount,
                                currency:selectedCurrency,
                                ovolume:v.odetail_volume,
                                otnved:v.odetail_tnved,
                                insurance_need:v.odetail_insurance,
                                ocomment:v.odetail_comment
                            });
                        });

                        // прикручиваем обработчики кнопок деталям заказа
                        $('.' + oObj.options.type + '_order_form #new_products a.edit').bind('click', iObj.editItem);
                        $('.' + oObj.options.type + '_order_form #new_products a.delete').bind('click', iObj.deleteItem);
                        $('.' + oObj.options.type + '_order_form #new_products a.save').bind('click', iObj.saveItem);
                        $('.' + oObj.options.type + '_order_form #new_products a.cancel').bind('click', iObj.cancelItem);
                        $('.' + oObj.options.type + '_order_form #new_products input.odetail_price').bind('change', iObj.updateItemPrice);
                        $('.' + oObj.options.type + '_order_form #new_products input.odetail_pricedelivery').bind('change', iObj.updateItemDeliveryPrice);
                        $('.' + oObj.options.type + '_order_form #new_products input.odetail_weight').bind('change', iObj.updateItemWeight);

                        oObj.updateTotals();
                    }

                    // Отображаем список товаров
                    $('#detailsForm').show();
                }
            }
            // начинаем инициализацию

            // Страна поступления, поле "Заказать из"
            country_from = new $.cpField();
            country_from.init({
                object:$('#country_from_delivery'),
                useDd:true,
                onChange:function () {
                    var id = $(this).val();
                    var prevCurrency = selectedCurrency;

                    for (var index in currencies) {
                        var currency = currencies[index];

                        if (id == currency['country_id']) {
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
                object:$('#country_to_delivery'),
                useDd:true,
                onChange:function () {
                    var id = $(this).val();
                    for (var index in currencies) {
                        var currency = currencies[index];

                        if (id == currency['country_id']) {
                            $('.' + oObj.options.type + '_order_form input.countryTo').val(id);
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
                object:$('#city_to_delivery'),
                onChange:function () {
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
                object:$('#requested_delivery_delivery')
            });
            oObj.fields.push(requested_delivery);

            var item = new itemDelivery();
            item.init();

            $('#addItemDelivery').unbind('click').bind('click', item.add);

            $('#'+oObj.options.type+'checkoutOrder').bind('click', saveOrder);

            // Отображаем форму
            $('div.order_type_selector').hide();
            $('h2#page_title').html(oObj.options.title);
            $("div."+oObj.options.type+"_order_form").show('slow');

        }
        // End initDelivery

        var saveOrder = function () {
            $('#'+oObj.options.type+'OrderForm').ajaxForm({
                target:$('#'+oObj.options.type+'OrderForm').attr('action'),
                type:'POST',
                dataType:'html',
                iframe:true,
                beforeSubmit:function (formData, jqForm, options) {
                    if (!checkOrder()) {
                        scrollFirstError();
                        return false;
                    }
                    return true;
                },
                success:function (response) {
                    if (response) {
                        error('top', 'Заказ не добавлен. ' + response);
                    }
                    else {
                        var order_id = $('input.order_id').val();
                        success('top', 'Заказ №' + order_id + ' добавлен! Дождитесь предложений от посредников и выберите лучшее из них.');
                        window.location = '/client/order/' + order_id;
                    }
                },
                error:function (response) {
                    error('top', 'Заказ не добавлен. ' + response);
                }
            }).submit();
        }  // End saveOffline
        // Конец Заказы

        var showOrderProgress = function () {
            // Отображаем прогресс по выбору типа заказа
            if (obj = $('.' + oObj.options.type + '_order div:first b')) {
                var img = $("<div class='orderProgress'><img src='/static/images/lightbox-ico-loading.gif'/></div>");
                img.css({
                    float:'left',
                    left:'110px',
                    position:'absolute',
                    top:'-8px',
                    width:'32px'
                });
                obj.append(img);
            }
        } // End showOrderProgress

        var hideOrderProgress = function () {
            // Скрываем прогресс по выбору типа заказа
            if (obj = $('.' + oObj.options.type + '_order div:first b')) {
                obj.hide('slow').remove();
            }
        } // End hideOrderProgress

        oObj.options =
        {
            order_id:0,
            type:'',
            title:'',
            cart:null
        } // End options

        oObj.init = function (type) {
            switch (type) {
                case 'online' :
                    initOnline();
                    break;
                case 'offline' :
                    initOffline();
                    break;
                case 'service' :
                    initService();
                    break;
                case 'delivery' :
                    initDelivery();
                    break;
            }
        } // End init

        oObj.updateTotals = function () {
            oObj.options.cart.updateCurrency(selectedCurrency);

            if (countryTo == '') {
                var countryToId = $('#country_to_' + oObj.options.type + '').val();

                $.each(currencies,
                        function (k, v) {
                            if (v.country_id == countryToId) {
                                countryTo = v.country_name;
                            }
                        }
                )
            }

            var totals = oObj.options.cart.calcTotals(),
                    cityTo = fieldByName(oObj.fields, 'city_to');

            $('.price_total').text(totals.price + ' ' + totals.currency);
            $('.delivery_total').text(totals.delivery + ' ' + totals.currency);
            $('.weight_total').text(totals.weight + ' г');
            $('.order_totals').text((totals.price + totals.delivery) + ' ' + totals.currency);
            $('span.countryTo').text(countryTo);
            if (cityTo && cityTo.element.val()) {
                $('span.cityTo').text(' (' + cityTo.element.val() + ')');
            }
        }

        oObj.fields = [];

        oObj.items = [];
    }

})(jQuery)
</script>