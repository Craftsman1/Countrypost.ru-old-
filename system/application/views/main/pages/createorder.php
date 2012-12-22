<div class='content smallheader'>
<? Breadcrumb::showCrumbs(); ?>

<? if (!$order_type) : ?>
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
                <br/>Заявки на поиск товара/поставщика также добавляйте сюда.
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
        <div class="mail_forwarding_order order">
            <div>
                <b>MailForwarding</b>
            </div>
            <div>Самостоятельный заказ товар на адрес посредника (перед заказом согласуйте с посредником).
            </div>
        </div>
    </div>
<? else : ?>
    <h2 id='page_title'></h2>
    <? View::show('main/elements/orders/scripts'); ?>
    <?
    switch ($order_type) :
        case 'online' :
            View::show('main/elements/orders/online');
            break;
        case 'offline' :
            View::show('main/elements/orders/offline');
            break;
        case 'service' :
            View::show('main/elements/orders/service');
            break;
        case 'delivery' :
            View::show('main/elements/orders/delivery');
            break;
        case 'mailforwarding' :
            View::show('main/elements/orders/mailforwarding');
            break;
    endswitch;
    ?>
<? endif; ?>

</div>
<script>
    (function($){
        $('div.online_order').bind('click', function() {
            document.location = '<?= $this->viewpath ?>createorder/online';
        });
        $('div.offline_order').bind('click', function() {
            document.location = '<?= $this->viewpath ?>createorder/offline';
        });
        $('div.service_order').bind('click', function() {
            document.location = '<?= $this->viewpath ?>createorder/service';
        });
        $('div.delivery_order').bind('click', function() {
            document.location = '<?= $this->viewpath ?>createorder/delivery';
        });
        $('div.mail_forwarding_order').bind('click', function() {
            document.location = '<?= $this->viewpath ?>createorder/mailforwarding';
        });


        $('.submit input[type="submit"]').bind('click', function () {
            var data_items = $('#new_products input[name="odetail_id"]:checked'),
                post_data = {},
                order_id = parseInt($('input.order_id').val(), 10);


            if (order_id && !isNaN(order_id))
            {
                $('#joint_progress').show();

                $.each(data_items, function(k, v) {
                    post_data['join'+ $(v).val()] = $(v).val();
                });

                $.post( "/main/joinNewProducts/" + order_id,
                        post_data,
                        function (responce)
                        {
                            if (!responce.is_error)
                            {
                                success('top', responce.message);
                            }
                            else
                            {
                                error('top', responce.message);
                            }

                            $('#joint_progress').hide();
                        },
                        'json'
                );
            }

        });

    })(jQuery)


    // скриншот
    function showScreenshotLink() {
        $('.screenshot_link_box').show('slow');
        if ($('.screenshot_link_box').val() == '') {
            $('.screenshot_link_box').val('ссылка на скриншот')
        }
        $('.screenshot_switch').hide('slow');
    }

    function showScreenshotUploader() {
        $('.screenshot_uploader_box').show('slow');
        if ($('.screenshot_link_box').val() == 'ссылка на скриншот') {
            $('.screenshot_link_box').val('')
        }
        $('.screenshot_switch').hide('slow');
    }

    var urlValidate = function (str) {
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
        if (prefRes !== null) {
            var allowed = Array('https', 'http', 'ftp');
            for (var i = 0, s = allowed.length; i < s; i++) {
                if (allowed[i] === prefRes[1]) {
                    hasPrefix = true;
                    prefix = prefRes[1];
                    str = str.replace(prefRgx, '');
                    break;
                }
            }
            if (!hasPrefix) {
                str = str.replace(prefRgx, '');
                hasErrors = true;
                message = "Сетевой протокол не может быть: <b>'" + prefRes[1] + "'</b>. Укажите ссылку с одним из следующих протоколов: (" + allowed.join(', ') + ")";
            }
        }

        // Check WWW
        var wwwRgx = new RegExp(/^([w]{1,}\.)/),
                wwwRes = wwwRgx.exec(str);
        if (wwwRes !== null) {
            wwwRes_ = wwwRes[1];
            if (wwwRes_.length == 4) {
                hasWww = true;
                str = str.replace(wwwRgx, '');
            }
            else {
                hasErrors = true;
                message = "Префикс не может быть: <b>'" + wwwRes_ + "'</b>.";
                if (wwwRes_.length > 4) {
                    message += " Если адрес домена введен верно - укажите перед ним префикс <b>'www.'</b>.";
                }
                else {
                    message += " Укажите префикс из трёх символов <b>'www.'</b>.";
                }
            }
        }

        // Check Domain
        var domainRgx = new RegExp(/^([^\/]*)/),
                domainRes = domainRgx.exec(str);
        if (fileRes !== null) {
            // TODO: Проверка находится ли домен в списке разрешенных\запрещенных
            var segments = domainRes[1].split('.');
            if (segments.length > 1) {
                domain = domainRes[1];
                str = str.replace(domainRgx, '');
            }
            else {
                hasErrors = true;
                message = "Доменное имя не может быть: <b>'" + domainRes[1] + "'</b>. Укажите ссылку с доменным именем, состоящим из, не менее 2-х сегментов.";
            }
        }

        // Check File
        var fileRgx = new RegExp(/\/?([^\?]*)/),
                fileRes = fileRgx.exec(str);
        if (fileRes !== null) {
            hasFile = true;
            file = fileRes[1];
            str = str.replace(fileRgx, '');
        }

        // Check Query
        var queryRgx = new RegExp(/\?(.*)$/),
                queryRes = queryRgx.exec(str);
        if (queryRes !== null) {
            hasQuery = true;
            query = queryRes[1];
            str = str.replace(queryRgx, '');
        }

        if (hasErrors) {
            return message;
        }

        return true;
    }


    var orderData = <?= ($json = json_encode($orders)) ? $json : '{}' ?>;
    var currencies = <?= json_encode($countries); ?>;
    var selectedCurrency = '<?= $order_currency ?>';
    //var countryFrom = '';
    var countryTo = '';
    var cityTo = '';
    var user = '<?= (!empty($this->user)) ? $this->user->user_group : '' ?>';
</script>