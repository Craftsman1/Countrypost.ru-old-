<div class="delivery_address client_tab" style="display:none;">
    <form action="/client/saveAddress" id="addressForm" method="POST">
        <div class="table" style="height: 303px;">
            <div class='angle angle-lt'></div>
            <div class='angle angle-rt'></div>
            <div class='angle angle-lb'></div>
            <div class='angle angle-rb'></div>
            <div class="blog_box admin-inside">
                <div>
                    <span class="label" style="float:left">Получатель *:</span>
                    <input style="width:180px;" class="textbox" maxlength="32" id="recipient" name="recipient" value="" type="text">
                </div>
                <br style="clear:both;">
                <div>
                    <span class="label" style="float:left">Страна *:</span>
                    <select id="country" name="country" class="textbox" onchange="$.fn.validateAddressCountry($(this))">
                        <option value="0">выберите страну...</option>
                        <? foreach ($Countries as $cntry) : ?>
                        <option value="<?= $cntry->country_id ?>"  title="/static/images/flags/<?= $cntry->country_name_en ?>.png" <? if ($client->client_country == $cntry->country_id) : ?>selected<? endif; ?>><?= $cntry->country_name ?></option>
                        <? endforeach; ?>
                    </select>
                </div>
                <br style="clear:both;">
                <div>
                    <span class="label" style="float:left">Город *:</span>
                    <input style="width:180px;" class="textbox" maxlength="128" id="city" name="city" value="" type="text">
                </div>
                <br style="clear:both;">
                <div>
                    <span class="label" style="float:left">Индекс *:</span>
                    <input style="width:180px;" class="textbox" maxlength="6" id="index" name="index" value="" type="text">
                </div>
                <br style="clear:both;">
                <div>
                    <span class="label" style="float:left">Адрес *:</span>
                    <textarea style="width:180px; height: 60px;" class="textbox" maxlength="255" id="address" name="address"></textarea>
                </div>
                <br style="clear:both;">
                <div>
                    <span class="label" style="float:left">Телефон *:</span>
                    <input style="width:180px;" class="textbox" maxlength="255" id="phone" name="phone" value="" type="text">
                </div>
                <br style="clear:both;">
            </div>
        </div>
        <br style="clear:both;" />
        <div class="submit floatleft">
            <div>
                <input type="submit" value="Добавить">
            </div>
        </div>
        <img class="float" id="blogProgress" style="display:none;margin:0px;margin-top:4px;" src="/static/images/lightbox-ico-loading.gif"/>
    </form>
    <br style="clear:both;" />
    <h3 id="delivery_addressess_header" <? if (!$addresses) :  ?>style="display:none;"<? endif; ?>>Мои адреса</h3>

    <div class="table deliveryAddressTableContainer" <? if (!$addresses) :  ?>style="display: none"<? endif; ?>>
        <div class='angle angle-lt'></div>
        <div class='angle angle-rt'></div>
        <div class='angle angle-lb'></div>
        <div class='angle angle-rb'></div>
        <div>

            <table id="deliveryAddressTable">
                <tr>
                    <th width="20px">&nbsp;</th>
                    <th><b>Получатель</b></th>
                    <th><b>Адрес</b></th>
                    <th><b>Телефон</b></th>
                    <th width="50px">&nbsp;</th>
                </tr>
                <? if ($addresses) :  ?>
                <? foreach ($addresses as $address) : ?>
                <tr id="addressRow<?=$address->address_id?>">
                    <td class="address_id"><?=$address->address_id?></td>
                    <td><?=$address->address_recipient?></td>
                    <td><?=$address->address_zip.', '.$address->address_address.', '.$address->address_town.', '.$address->country_name?></td>
                    <td><?=$address->address_phone?></td>
                    <td align="center">
                        <a class="edit_icon" style="cursor: pointer;"><img src="/static/images/comment-edit.png" title="Изменить" border="0"></a>
                        <a class="delete_icon"><img src="/static/images/delete.png" style="cursor: pointer;" title="Удалить" border="0"></a>
                    </td>
                </tr>
                <? endforeach; ?>
                <? endif; ?>
            </table>
        </div>
    </div>
    <br>
    <br>
</div>
<script>
    var validateCountry = '';
    $(function() {

        var deleteAddress = function()
        {
            var iconContainer = $(this).parent(),
            row = $(this).parent().parent(),
            tkey = row.find('td.address_id').text();

            if (tkey != '' && !isNaN(tkey))
            {
                if (confirm("Вы уверены, что хотите удалить адрес №" + tkey + "?"))
                {
                    addAddressItemProgress(iconContainer);
                    var ikey = parseInt(tkey, 10);
                    $.post('/client/removeAddress', {address_id : ikey}, function(data) {
                        if(data == 1)
                        {
                            row.remove();
                            rows = $('#deliveryAddressTable tr');
                            if (rows.size() < 2)
                            {
                                $('.deliveryAddressTableContainer, #delivery_addressess_header').hide();
                            }
                            success('top', 'Адрес успешно удален!');
                        }
                        removeAddressItemProgress(iconContainer);
                    });
                }
            }

        }

        var addAddressItemProgress = function(obj)
        {
            var progress_snipet = '<img class="float" id="addressItemProgress" style="margin:0px;margin-top:4px;" src="/static/images/lightbox-ico-loading.gif"/>';
            $(obj).find('.edit_icon, .delete_icon').hide();
            $(obj).append(progress_snipet);
        },
        removeAddressItemProgress = function(obj)
        {
            $(obj).find('#addressItemProgress').remove();
            $(obj).find('.edit_icon, .delete_icon').show();
        },

        validateAddressForm = function() {

            field = null,
            errorCount = 0;

            field = $('#recipient');
            if(field.val() == '')
            {
                $.fn.addProfileFieldError(field, 'Введите получателя');
                errorCount++;
            }
            else
            {
                $.fn.removeProfileFieldError(field);
            }

            field = $('#country');
            if(field.val() == 0)
            {
                $.fn.addProfileFieldError($('#country_msdd'), 'Необходимо указать страну');
                errorCount++;
            }
            else
            {
                $.fn.removeProfileFieldError($('#country_msdd'));
            }

            field = $('#city');
            if(field.val() == '')
            {
                $.fn.addProfileFieldError(field, 'Укажите город');
                errorCount++;
            }
            else
            {
                $.fn.removeProfileFieldError(field);
            }
            field = $('#index');
            if(field.val() == '' || field.val().length < 5 || !field.val().match(/^[0-9]*$/))
            {
                $.fn.addProfileFieldError(field, 'Введите правильный индекс');
                errorCount++;
            }
            else
            {
                $.fn.removeProfileFieldError(field);
            }
            field = $('#address');
            if(field.val() == '' || field.val().length < 5)
            {
                $.fn.addProfileFieldError(field, 'Введите адрес');
                errorCount++;
            }
            else
            {
                $.fn.removeProfileFieldError(field);
            }
            field = $('#phone');
            if(field.val() == '' || !field.val().match(/^[0-9()\+ -]*$/))
            {
                $.fn.addProfileFieldError(field, 'Введите правильный телефон');
                errorCount++;
            }
            else
            {
                $.fn.removeProfileFieldError(field);
            }
            return (errorCount > 0) ? false : true;
        }


        $.fn.validateAddressCountry = function(field)
        {
            if(field.val() == 0 || field.val() == '')
            {
                $.fn.addProfileFieldError($('#country_msdd'), 'Необходимо указать страну');
                return false;
            }
            else
            {
                $.fn.removeProfileFieldError($('#country_msdd'));
                return true;
            }
        }

        // Валидация при заполнении
        $('#recipient').validate({
            expression: "if (VAL != '') return true; else return false;",
            message: "Введите получателя"
        });;
        $('#city').validate({
            expression: "if (VAL != '') return true; else return false;",
            message: "Укажите город"
        });;
        $('#index')
                .keypress(function(event){validate_number(event);})
                .validate({
            expression: "if (!(VAL == '' || VAL.length < 5 || VAL.length > 6 || !VAL.match(/^[0-9]*$/))) return true; else return false;",
            message: "Введите правильный индекс"
        });;
        $('#address').validate({
            expression: "if (VAL != '') return true; else return false;",
            message: "Введите адрес"
        });;
        $('#phone').validate({
            expression: "if (!(VAL == '' || !VAL.match(/^[0-9()\+ -]*$/))) return true; else return false;",
            message: "Введите правильный телефон"
        });;

        $("#country").msDropDown({mainCSS:'idd'});

        $('#addressForm').ajaxForm({
            target: '/client/saveAddress',
            clearForm: false,
            type: 'POST',
            dataType: 'html',
            iframe: true,
            beforeSubmit: function(formData, jqForm, options)
            {
                $("#blogProgress").show();
                var valid = validateAddressForm();

                if (!valid) $("#blogProgress").hide();

                return valid;
            },
            success: function(response)
            {
                $("#blogProgress").hide();

                var response_ = $.parseJSON(response);

                if (response_ === null)
                {
                    error('top', 'Заполните все поля и сохраните еще раз.');
                    return;
                }

                var news_snippet = '<tr id="addressRow'+response_[0].address_id+'"><td class="address_id">' +
                        response_[0].address_id +
                        '</td><td>' +
                        response_[0].address_recipient +
                        '</td><td>' +
                        response_[0].address_zip +', '+response_[0].address_address +', '+response_[0].address_town +', '+response_[0].country_name +
                        '</td><td>' +
                        response_[0].address_phone +
                        '</td><td><a class="edit_icon" style="cursor: pointer;"><img src="/static/images/comment-edit.png" title="Изменить" border="0"></a><a class="delete_icon"><img src="/static/images/delete.png" style="cursor: pointer;" title="Удалить" border="0"></a></td></tr>';

                $('#deliveryAddressTable').find('tr:last').after(news_snippet);
                $('.deliveryAddressTableContainer, #delivery_addressess_header').show();
                $('.delete_icon').unbind('click').bind('click', deleteAddress);

                $('#recipient').val('');
                $('#city').val('');
                $('#index').val('');
                $('#address').val('');
                $('#phone').val('');

                success('top', 'Адрес успешно сохранен!');
            },
            error: function(response)
            {
                $("#blogProgress").hide();
                error('top', 'Заполните все поля и сохраните еще раз.');
            }
        });
        $('.delete_icon').unbind('click').bind('click', deleteAddress);
    });

</script>