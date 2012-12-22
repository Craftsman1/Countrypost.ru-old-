<?
    $order = null;
    for ($i = 0, $n = count($orders); $i<$n; $i++) :
        if ($orders[$i]->order_type == 'online') :
            $order = $orders[$i];
            break;
        endif;
    endfor;
?>
<div class="online_order_form">
	<div class='table' style="position:relative;">
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<form class='admin-inside' action="<?= $selfurl ?>checkout" id="onlineOrderForm" method="POST">
            <input type='hidden' name="order_id" class="order_id" value="<?= ($order) ? (int) $order->order_id : 0 ?>" />
            <input type='hidden' name="order_type" class="order_type" value="online" />
            <input type='hidden' name="order_currency" class="order_currency" value="<?= $order_currency ?>" />
			<div class='new_order_box'>
				<div>
					<span class="label">Заказать из*:</span>
                    <!--onchange="setCountryFrom(this.value)"-->
					<select id="country_from_online" name="country_from" class="textbox" >
						<option value="0">выберите страну...</option>
						<? foreach ($countries as $country) : ?>
						<option 
							value="<?= $country->country_id ?>"
							title="/static/images/flags/<?= $country->country_name_en ?>.png" 
							<? if (isset($filter->country_from) AND $filter->country_from == $country->country_id OR ($order AND $order->order_country_from == $country->country_id)) : ?>selected<? endif; ?>><?= $country->country_name ?></option>
						<? endforeach; ?>
					</select>
				</div>
				<div style="clear:both;" ></div>
				<div>
					<span class="label">В какую страну доставить*:</span>
                    <!--onchange="setCountryTo(this.value)"-->
					<select id="country_to_online" name="country_to" class="textbox" >
						<option value="0">выберите страну...</option>
						<? foreach ($countries as $country) : ?>
						<option
                                value="<?= $country->country_id ?>"
                                title="/static/images/flags/<?= $country->country_name_en ?>.png"
                                <? if (isset($filter->country_to) AND $filter->country_to == $country->country_id OR ($order AND $order->order_country_to == $country->country_id)) : ?>selected<? endif; ?>><?= $country->country_name ?></option>
						<? endforeach; ?>
					</select>
				</div>
				<div style="clear:both;" ></div>
				<div>
					<span class="label">Город доставки*:</span>
					<input style="width:180px;" class="textbox" maxlength="255" type='text' id='city_to_online' name="city_to" value="<?= ($order) ? $order->order_city_to : '' ?>" />
				</div>
				<div style="clear:both;" ></div>
				<div>
					<span class="label">Cпособ доставки:</span>
					<input style="width:180px;" class="textbox" maxlength="255" type='text' id='requested_delivery_online' name="requested_delivery" />
				</div>
				<div style="clear:both;" ></div>
				<div>
					<span class="label dealer_number_switch" style='<?= (!$order OR empty($order->order_manager)) ? '' : 'display:none;' ?>'>
						<a href="javascript: void(0);" onclick="">Выбрать посредника</a>
					</span>
					<span class="label dealer_number_box" style='<?= (!$order OR empty($order->order_manager)) ? 'display:none;' : '' ?>'>Номер посредника:</span>
                    <input class="textbox dealer_number_box" maxlength="255" type='text' id='dealer_id_ac_online' style='<?= (!$order OR empty($order->order_manager)) ? 'display:none;' : '' ?>width:180px;' value="<?= ($order AND !empty($order->order_manager)) ? $order->order_manager : '' ?>" >
                    <input type='hidden' id='dealer_id_online' name="dealer_id" value="<?= ($order AND !empty($order->order_manager)) ? $order->order_manager : '' ?>">
					<span class="label dealer_number_box" style='<?= (!$order OR empty($order->order_manager)) ? 'display:none;' : '' ?>'>
						<img border="0" src="/static/images/delete.png" title="Удалить">
                        <img src="/static/images/lightbox-ico-loading.gif" style="position: absolute; margin-top: -8px; margin-left: 10px; display: none;" class="float progress_ac" id="progress_ac">
					</span>
				</div>
				<div style="clear:both;" ></div>
			</div>
		</form>
	</div>
	<h3>Добавить товар:</h3>
	<div class="h2_link">
		<img src="/static/images/mini_help.gif" style="float:right;margin-left: 7px;" />
		<a href="javascript: void(0);" class="excel_switcher" style="">Массовая загрузка товаров</a>
	</div>		
	<form class='admin-inside' action="<?= $selfurl ?>addProductManualAjax" id="onlineItemForm" method="POST">
		<input type='hidden' name="order_id" class="order_id" value="<?= ($order) ? (int) $order->order_id : 0 ?>" />
        <input type='hidden' name="order_type" class="order_type" value="online" />
		<input type='hidden' name="ocountry" class="countryFrom" value="<?= ($order) ? (int) $order->order_country_from : '' ?>" />
        <input type='hidden' name="ocountry_to" class="countryTo" value="<?= ($order) ? (int) $order->order_country_to : '' ?>" />
        <input type='hidden' name="city_to" class="cityTo" value="<?= ($order) ? (int) $order->order_city_to : '' ?>" />
        <input type='hidden' name="dealer_id" class="dealerId" value="<?= ($order) ? (int) $order->order_manager : '' ?>" />

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
				<div style="clear:both;" ></div>
				<div>
					<span class="label">Наименование товара*:</span>
					<input style="width:180px;" class="textbox" maxlength="255" type='text' id='oname' name="oname" />
				</div>
				<div style="clear:both;" ></div>
				<div>
					<span class="label">Цена товара*:</span>
					<input style="width:180px;" class="textbox" maxlength="11" type='text' id='oprice' name="oprice" />
					<span class="label currency"><?= $order_currency ?></span>
				</div>
				<div style="clear:both;" ></div>
				<div>
					<span class="label">Местная доставка:</span>
					<input style="width:180px;" class="textbox" maxlength="11" type='text' id='odeliveryprice' name="odeliveryprice" />
					<span class="label currency"><?= $order_currency ?></span>
				</div>
				<div style="clear:both;" ></div>
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
				<div style="clear:both;" ></div>
				</div>
			</div>
		</div>
		<h3>Дополнительная информация по товару:</h3>
		<div class='add_detail_box' style="position:relative;">
			<div class='new_order_box'>
				<div style="clear:both;" ></div>
				<div>
					<span class="label">Цвет:</span>
					<input style="width:180px;" class="textbox" maxlength="255" type='text' id='ocolor' name="ocolor" />
				</div>
				<div style="clear:both;" ></div>
				<div>
					<span class="label">Размер:</span>
					<input style="width:180px;" class="textbox" maxlength="255" type='text' id='osize' name="osize" />
					<span class="label">
						<input class="border:auto;" type='button' value="подобрать размер" />
					</span>
				</div>
				<div style="clear:both;" ></div>
				<div>
					<span class="label">Количество:</span>
					<input style="width:180px;" class="textbox" maxlength="255" type='text' id='oamount' name="oamount" value="1" />
				</div>
				<div style="clear:both;" ></div>
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
				<div style="clear:both;" ></div>
				<div>
					<span class="label">Нужно ли фото товара?</span>
					<input type='checkbox' id='foto_requested' name="foto_requested" />
				</div>
				<div style="clear:both;" ></div>
				<div>
					<span class="label">Комментарий к товару:</span>
					<textarea style="width:180px;resize:auto!important;" class="textbox" maxlength="255" id='ocomment' name="ocomment"></textarea>
				</div>
				<div style="clear:both;" ></div>
			</div>
		</div>
	</form>
	<div style="height: 50px;" class="admin-inside">
		<div class="submit">
			<div>
				<input type="button" value="Добавить товар" id="addItemOnline" name="add" onclick="/*addItem();*/">
			</div>
		</div>
	</div>

    <? View::show('main/ajax/showNewOrderDetails', array('order_type' => 'online', 'order' => $order)); ?>

</div>
<script type="text/javascript">
	$(function() {
		$(window).load(function() {
            var order = new $.cpOrder(orderData);
            order.init("online");
		});

		// номер посредника
		$('.dealer_number_switch a').click(function() {
			$('.dealer_number_switch').hide('slow');
			$('.dealer_number_box').show('slow');
		});
		
		$('.dealer_number_box img').click(function() {
			$('.dealer_number_switch').show('slow');
            $('.dealer_number_box').hide('slow');
            $('.dealer_number_box').val('');
            $('#dealer_id').val('');
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