<?
$order = null;
for ($i = 0, $n = count($orders); $i<$n; $i++) :
    if ($orders[$i]->order_type == 'delivery') :
        $order = $orders[$i];
        break;
    endif;
endfor;
?>
<div class="delivery_order_form" style='display:none;'>
    <div class='table' style="position:relative;">
        <div class='angle angle-lt'></div>
        <div class='angle angle-rt'></div>
        <div class='angle angle-lb'></div>
        <div class='angle angle-rb'></div>
        <form class='admin-inside' action="<?= $selfurl ?>checkout" id="deliveryOrderForm" method="POST">
            <input type='hidden' name="order_id" class="order_id" value="<?= ($order) ? (int) $order->order_id : 0 ?>" />
            <input type='hidden' name="order_type" class="order_type" value="delivery" />
            <input type='hidden' name="order_currency" class="order_currency" value="<?= $order_currency ?>" />
            <div class='new_order_box'>
                <div>
                    <span class="label">Заказать из*:</span>
                    <!--onchange="setCountryFrom(this.value)"-->
                    <select id="country_from_delivery" name="country_from" class="textbox" >
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
                    <span class="label">В какую страну доставить*:</span>
                    <select id="country_to_delivery" name="country_to" class="textbox" >
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
                <div>
                    <span class="label">Город доставки*:</span>
                    <input style="width:180px;" class="textbox" maxlength="255" type='text' id='city_to_delivery' name="city_to" value="<?= ($order) ? $order->order_city_to : '' ?>" />
                </div>
                <br style="clear:both;" />
                <div>
                    <span class="label">Cпособ доставки:</span>
                    <input style="width:180px;" class="textbox" maxlength="255" type='text' id='requested_delivery_delivery' name="requested_delivery" />
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
    <h3>Добавить товар/груз:</h3>
    <div class="h2_link">
        <img src="/static/images/mini_help.gif" style="float:right;margin-left: 7px;" />
        <a href="javascript: void(0);" class="excel_switcher" style="">Массовая загрузка товаров</a>
    </div>
    <form class='admin-inside' action="<?= $selfurl ?>addProductManualAjax" id="deliveryItemForm" method="POST">
        <input type='hidden' name="order_id" class="order_id" value="<?= ($order) ? (int) $order->order_id : 0 ?>" />
        <input type='hidden' name="order_type" class="order_type" value="delivery" />
        <input type='hidden' name="ocountry" class="countryFrom" value="<?= ($order) ? (int) $order->order_country_from : '' ?>" />
        <input type='hidden' name="ocountry_to" class="countryTo" value="<?= ($order) ? (int) $order->order_country_to : '' ?>" />
        <input type='hidden' name="city_to" class="cityTo" value="<?= ($order) ? (int) $order->order_city_to : '' ?>" />
        <input type='hidden' name="userfileimg" value="" />
        <div class='table add_detail_box' style="position:relative;">
            <div class='angle angle-lt'></div>
            <div class='angle angle-rt'></div>
            <div class='angle angle-lb'></div>
            <div class='angle angle-rb'></div>
            <div class='new_order_box'>
                <div>
                    <span class="label">Наименование товара*:</span>
                    <input style="width:180px;" class="textbox" maxlength="255" type='text' id='oname' name="oname" />
                </div>
                <br style="clear:both;" />
                <div>
                    <span class="label">Ссылка на товар:</span>
                    <input style="width:180px;" class="textbox" maxlength="500" type='text' id='olink' name="olink" />
                </div>
                <br style="clear:both;" />
                <div>
                    <span class="label">Количество*:</span>
                    <input style="width:180px;" class="textbox" maxlength="11" type='text' id='oamount' name="oamount" />
                </div>
                <br style="clear:both;" />
                <div>
                    <span class="label">Примерный вес (кг)*:</span>
                    <input style="width:180px;" class="textbox" maxlength="255" type='text' id='oweight' name="oweight" />
                    <span style="float: left;margin: 2px 6px;">кг</span>
                    <br style="clear:both;" />
                </div>
                <br style="clear:both;" />
            </div>
        </div>
        <h3>Дополнительная информация по товару/грузу:</h3>
        <div class='add_detail_box' style="position:relative;">
            <div class='new_order_box'>
                <br style="clear:both;" />
                <div>
                    <span class="label">Объём:</span>
                    <input style="width:180px;" class="textbox" maxlength="11" type='text' id='ovolume' name="ovolume" />
                    <span style="float: left;margin: 2px 6px;">м³</span>
                    <span style="float: left;margin: 2px 6px;">Пример: 5,5</span>
                </div>
                <br style="clear:both;" />
                <div>
                    <span class="label">ТН ВЭД:</span>
                    <input style="width:180px;" class="textbox" maxlength="11" type='text' id='otnved' name="otnved" />
                </div>
                <br style="clear:both;" />
                <div>
                    <span class="label">Требуется страховка?</span>
                    <label><input type="radio" name="insurance_need" id="insurance_need_y" value="1"/> Да</label>
                    <label><input type="radio" name="insurance_need" id="insurance_need_n" value="0" checked="true"/> Нет</label>
                </div>
                <br style="clear:both;" />
                <div>
                    <span class="label">Стоимость:</span>
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
                <input type="button" value="Добавить товар" id="addItemDelivery" name="add" onclick="/*addItem();*/">
            </div>
        </div>
    </div>

	<? View::show('main/ajax/showNewOrderDetails', array('order_type' => 'delivery', 'order' => $order)); ?>
</div>
<script type="text/javascript">
	$(function()
    {
        $('div.delivery_order').click(function() {
            var order = new $.cpOrder(orderData);
            order.init("delivery");
        });

        // нужна доставка?
        $('#delivery_need_y').bind('click', function() {
            $('#country_to_delivery_msdd, #city_to_delivery, #requested_delivery_delivery').parent().show('slow');
        });
        $('#delivery_need_n').bind('click', function() {
            $('#country_to_delivery_msdd, #city_to_delivery, #requested_delivery_delivery').parent().hide('slow');
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