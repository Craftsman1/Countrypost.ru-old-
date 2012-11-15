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
                    <select id="country" name="country" class="textbox">
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
                    <input style="width:180px;" class="textbox" maxlength="255" id="index" name="index" value="" type="text">
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
    <h3 id="news_header">Мои адреса</h3>

    <div class="table deliveryAddressTableContainer" <? if (!$addresses) :  ?>style="display: none"<? endif; ?>>
        <div class='angle angle-lt'></div>
        <div class='angle angle-rt'></div>
        <div class='angle angle-lb'></div>
        <div class='angle angle-rb'></div>
        <div>

            <table id="deliveryAddressTable">
                <tr>
                    <td width="20px">&nbsp;</td>
                    <td><b>Получатель</b></td>
                    <td><b>Адрес</b></td>
                    <td><b>Телефон</b></td>
                    <td width="50px">&nbsp;</td>
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
    $(function() {

        var deleteAddress = function()
        {
            var iconContainer = $(this).parent(),
            row = $(this).parent().parent(),
            tkey = row.find('td.address_id').text();

            if (tkey != '' && !isNaN(tkey))
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
                            $('.deliveryAddressTableContainer').hide();
                        }
                    }
                    removeAddressItemProgress(iconContainer);
                });
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
        }

        $('#addressForm').ajaxForm({
            target: '/client/saveAddress',
            clearForm: true,
            type: 'POST',
            dataType: 'html',
            iframe: true,
            beforeSubmit: function(formData, jqForm, options)
            {
                $("#blogProgress").show();
            },
            success: function(response)
            {
                $("#blogProgress").hide();
                success('top', 'Новость успешно сохранена!');
                var response_ = $.parseJSON(response);
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
                $('.deliveryAddressTableContainer').show();
                $('.delete_icon').unbind('click').bind('click', deleteAddress);
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



<!--
<div class="delivery_address client_tab" style="min-height: 300px;display:none;">
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>

    	<form id="deliveryAddressForm" method="post" action="/client/saveAdress">
            <div class="table  admin-inside">
                <br style="clear:both;">
                <div>
                    <span class="label">Получатель *:</span>
                    <input style="width:180px;" class="textbox" maxlength="32" id="recipient" name="recipient" value="" type="text">
                </div>
                <br style="clear:both;">
                <div>
                    <span class="label">Страна *:</span>
                    <select style="width:180px;" class="textbox" maxlength="32" id="country" name="country">
                        <option>choose...</option>
                    </select>
                </div>
                <br style="clear:both;">
                <div>
                    <span class="label">Город *:</span>
                    <input style="width:180px;" class="textbox" maxlength="128" id="city" name="city" value="" type="text">
                </div>
                <br style="clear:both;">
                <div>
                    <span class="label">Индекс *:</span>
                    <input style="width:180px;" class="textbox" maxlength="255" id="index" name="index" value="" type="text">
                </div>
                <br style="clear:both;">
                <div>
                    <span class="label">Адрес *:</span>
                    <textarea style="width:180px; height: 60px;" class="textbox" maxlength="255" id="address" name="address"></textarea>
                </div>
                <br style="clear:both;">
                <div>
                    <span class="label">Телефон *:</span>
                    <input style="width:180px;" class="textbox" maxlength="255" id="phone" name="phone" value="" type="text">
                </div>
                <br style="clear:both;">
                <div class="submit" style="margin-left: 188px;">
                    <div>
                        <input value="Добавить" type="submit">
                    </div>
                </div>
                <img ilo-full-src="http://cps/static/images/lightbox-ico-loading.gif" class="float" id="addressProgress" style="display:none;margin:0px;margin-top:4px;" src="/static/images/lightbox-ico-loading.gif">
            </div>
        </form>

        <br style="clear:both;">

        <h3>Мои адреса</h3>
        <div class="table">
            <div class="angle angle-lt"></div>
            <div class="angle angle-rt"></div>
            <div class="angle angle-lb"></div>
            <div class="angle angle-rb"></div>
            <div>
                <table id="deliveryAddressTable">
                    <thead>
                    <tr>
                        <td width="20px">&nbsp;</td>
                        <td><b>Получатель</b></td>
                        <td><b>Адрес</b></td>
                        <td><b>Телефон</b></td>
                        <td width="50px">&nbsp;</td>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>

</div>


<script type="text/javascript">
$(function($) {

    $('#deliveryAddressForm').ajaxForm({
        target: '/manager/saveAddress',
        type: 'POST',
        dataType: 'html',
        iframe: true,
        beforeSubmit: function(formData, jqForm, options)
        {
            $("#addressProgress").show();
        },
        success: function(response)
        {
            $("#addressProgress").hide();
            success('top', 'Адрес успешно сохранен!');

            var news_snippet = '<div class="table"><div class="angle angle-lt"></div><div class="angle angle-rt"></div><div class="angle angle-lb"></div><div class="angle angle-rb"></div><div><table>' +
                    getNowDate() +
                    '</table> <span class="label"><b>' +
                    $('.blog_box input#title').val() +
                    '</b></span></div><div>' +
                    message +
                    '</div></div><br><br>';

            $('#news_header').after(news_snippet);

            $('.blog_box input#title').val('');
            oEditor.SetHTML('');
        },
        error: function(response)
        {
            $("#addressProgress").hide();
            error('top', 'Заполните все поля и сохраните еще раз.');
        }
    });

})(jQuery)
</script>
-->