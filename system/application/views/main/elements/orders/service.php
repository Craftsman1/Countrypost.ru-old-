<?
$order = null;
for ($i = 0, $n = count($orders); $i<$n; $i++) :
    if ($orders[$i]->order_type == 'service') :
        $order = $orders[$i];
        break;
    endif;
endfor;
?>
<div class="service_order_form" style='display:none;'>
    <div class='table' style="position:relative;">
        <div class='angle angle-lt'></div>
        <div class='angle angle-rt'></div>
        <div class='angle angle-lb'></div>
        <div class='angle angle-rb'></div>
        <form class='admin-inside' action="<?= $selfurl ?>checkout" id="serviceOrderForm" method="POST">
            <input type='hidden' name="order_id" class="order_id" value="<?= ($order) ? (int) $order->order_id : 0 ?>" />
            <input type='hidden' name="order_type" class="order_type" value="service" />
            <input type='hidden' name="order_currency" class="order_currency" value="<?= $order_currency ?>" />
            <div class='new_order_box'>
                <div>
                    <span class="label">Заказать из*:</span>
                    <!--onchange="setCountryFrom(this.value)"-->
                    <select id="country_from_service" name="country_from" class="textbox" >
                        <option value="0">выберите страну...</option>
                        <? foreach ($countries as $country) : ?>
                        <option
                                value="<?= $country->country_id ?>"
                                title="/static/images/flags/<?= $country->country_name_en ?>.png"
                            <? if (isset($filter->country_from) AND $filter->country_from == $country->country_id OR ($order AND $order->order_country_from == $country->country_id)) : ?>selected<? endif; ?>><?= $country->country_name ?></option>
                        <? endforeach; ?>
                    </select>
                </div>
                <br style="clear:both;" />
                <div>
                    <span class="label">Нужна ли доставка?</span>
                    <label><input type="radio" name="delivery_need" id="delivery_need_y" value="1"/> Да</label>
                    <label><input type="radio" name="delivery_need" id="delivery_need_n" value="0" checked="true"/> Нет</label>
                </div>
                <br style="clear:both;" />
                <div style="display:none">
                    <span class="label">В какую страну доставить*:</span>
                    <select id="country_to_service" name="country_to" class="textbox" >
                        <option value="0">выберите страну...</option>
                        <? foreach ($countries as $country) : ?>
                        <option
                                value="<?= $country->country_id ?>"
                                title="/static/images/flags/<?= $country->country_name_en ?>.png"
                            <? if (isset($filter->country_to) AND $filter->country_to == $country->country_id OR ($order AND $order->order_country_to == $country->country_id)) : ?>selected<? endif; ?>><?= $country->country_name ?></option>
                        <? endforeach; ?>
                    </select>
                </div>
                <br style="clear:both;" />
                <div style="display:none">
                    <span class="label">Город доставки*:</span>
                    <input style="width:180px;" class="textbox" maxlength="255" type='text' id='city_to_service' name="city_to" value="<?= ($order) ? $order->order_city_to : '' ?>" />
                </div>
                <br style="clear:both;" />
                <div style="display:none">
                    <span class="label">Cпособ доставки:</span>
                    <input style="width:180px;" class="textbox" maxlength="255" type='text' id='requested_delivery_service' name="requested_delivery" />
                </div>
                <br style="clear:both;" />
                <div>
                        <span class="label dealer_number_switch">
                            <a href="javascript: void(0);" onclick="">Выбрать посредника</a>
                        </span>
                    <span class="label dealer_number_box" style='display:none;'>Номер посредника:</span>
                    <input class="textbox dealer_number_box" maxlength="6" type='text' id='dealer_id_online' name="dealer_id" style='display:none;width:180px;' >
                        <span class="label dealer_number_box" style='display:none;'>
                            <img border="0" src="/static/images/delete.png" title="Удалить">
                        </span>
                </div>
                <br style="clear:both;" />
            </div>
        </form>
    </div>
    <h3>Добавить услугу:</h3>
    <div class="h2_link">
        <img src="/static/images/mini_help.gif" style="float:right;margin-left: 7px;" />
        <a href="javascript: void(0);" class="excel_switcher" style="">Массовая загрузка товаров</a>
    </div>
    <form class='admin-inside' action="<?= $selfurl ?>addProductManualAjax" id="serviceItemForm" method="POST">
        <input type='hidden' name="order_id" class="order_id" value="<?= ($order) ? (int) $order->order_id : 0 ?>" />
        <input type='hidden' name="order_type" class="order_type" value="service" />
        <input type='hidden' name="ocountry" class="countryFrom" value="<?= ($order) ? (int) $order->order_country_from : '' ?>" />
        <input type='hidden' name="ocountry_to" class="countryTo" value="<?= ($order) ? (int) $order->order_country_to : '' ?>" />
        <input type='hidden' name="city_to" class="cityTo" value="<?= ($order) ? (int) $order->order_city_to : '' ?>" />
        <input type='hidden' name="userfileimg" value="12345" />
        <div class='table add_detail_box' style="position:relative;">
            <div class='angle angle-lt'></div>
            <div class='angle angle-rt'></div>
            <div class='angle angle-lb'></div>
            <div class='angle angle-rb'></div>
            <div class='new_order_box'>
                <div>
                    <span class="label">Наименование услуги*:</span>
                    <input style="width:180px;" class="textbox" maxlength="255" type='text' id='oname' name="oname" />
                </div>
                <br style="clear:both;" />
                <div>
                    <span class="label">Подробное описание что нужно сделать*:</span>
                    <textarea style="width:180px;resize:auto!important;" class="textbox" maxlength="255" id='ocomment' name="ocomment"></textarea>
                </div>
                <br style="clear:both;" />
                <div>
                    <span class="label">Стоимость за выполнение:</span>
                    <input style="width:180px;" class="textbox" maxlength="11" type='text' id='oprice' name="oprice" />
                    <span class="label currency"><?= $order_currency ?></span>
                </div>
                <br style="clear:both;" />
                <div>
                    <span class="label">Местная доставка:</span>
                    <input style="width:180px;" class="textbox" maxlength="11" type='text' id='odeliveryprice' name="odeliveryprice" />
                    <span class="label currency"><?= $order_currency ?></span>
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
        <div class='add_detail_box' style="position:relative;">
            <div class='new_order_box'>
                <br style="clear:both;" />
                <div>
                    <span class="label">
                        Скриншот (max. 3 Mb):
                    </span>
                    <span class="label screenshot_switch" style="font-size:11px;margin:0;width:300px;">
                        <a href="javascript: showScreenshotLink();">Добавить ссылку</a>&nbsp;или&nbsp;<a href="javascript: showScreenshotUploader();" class="screenshot_switch">Загрузить файл</a>
                    </span>
                    <input class="textbox screenshot_link_box" type='text' id='oimg' name="userfileimg" style='display:none;width:180px;' value="" onfocus="javascript: if (this.value == 'ссылка на скриншот') this.value = '';" onblur="javascript: if (this.value == '') this.value = 'ссылка на скриншот';">
                    <input class="textbox screenshot_uploader_box" type='file' id='ofile' name="userfile" style='display:none;width:180px;'>
                    <span class="label screenshot_link_box screenshot_uploader_box" style='display:none;'>
                        <img border="0" src="/static/images/delete.png" title="Удалить">
                    </span>
                </div>
                <br style="clear:both;" />
            </div>
        </div>
    </form>
    <div style="height: 50px;" class="admin-inside">
        <div class="submit">
            <div>
                <input type="button" value="Добавить товар" id="addItemService" name="add" onclick="/*addItem();*/">
            </div>
        </div>
    </div>

	<? View::show('main/ajax/showNewOrderDetails', array('order_type' => 'service', 'order' => $order)); ?>
</div>
<script type="text/javascript">
	$(function()
    {
        $('div.service_order').click(function() {
            var order = new $.cpOrder(orderData);
            order.init("service");
        });

        // нужна доставка?
        $('#delivery_need_y').bind('click', function() {
            $('#country_to_service_msdd, #city_to_service, #requested_delivery_service').parent().show('slow');
        });
        $('#delivery_need_n').bind('click', function() {
            $('#country_to_service_msdd, #city_to_service, #requested_delivery_service').parent().hide('slow');
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
	});
</script>