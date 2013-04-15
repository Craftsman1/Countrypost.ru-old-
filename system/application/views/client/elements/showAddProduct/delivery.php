<div class="delivery_order_form">
    <form class='admin-inside'
		  action="<?= $selfurl ?>addProductToPrivilegedOrder/<?= $order->order_id ?>"
		  id="deliveryItemForm"
		  method="POST">
        <input type='hidden' name="order_id" class="order_id" value="<?= ($order) ? (int) $order->order_id : 0 ?>" />
        <input type='hidden' name="order_type" class="order_type" value="delivery" />
        <input type='hidden' name="ocountry" class="countryFrom" value="<?= ($order) ? (int) $order->order_country_from : '' ?>" />
        <input type='hidden' name="ocountry_to" class="countryTo" value="<?= ($order) ? (int) $order->order_country_to : '' ?>" />
        <input type='hidden' name="city_to" class="cityTo" value="<?= ($order) ? (int) $order->order_city_to : '' ?>" />
        <input type='hidden' name="dealer_id" class="dealerId" value="<?= ($order) ? (int) $order->order_manager : '' ?>" />
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
                <div style="clear:both;" ></div>
                <div>
                    <span class="label">Ссылка на товар:</span>
                    <input style="width:180px;" class="textbox" maxlength="500" type='text' id='olink' name="olink" />
                </div>
                <div style="clear:both;" ></div>
                <div>
                    <span class="label">Количество*:</span>
                    <input style="width:180px;" class="textbox" maxlength="11" type='text' id='oamount' name="oamount" />
                </div>
                <div style="clear:both;" ></div>
                <div>
                    <span class="label">Примерный вес (кг)*:</span>
                    <input style="width:180px;" class="textbox" maxlength="255" type='text' id='oweight' name="oweight" />
                    <span style="float: left;margin: 2px 6px;">кг</span>
                    <div style="clear:both;" ></div>
                </div>
                <div style="clear:both;" ></div>
            </div>
        </div>
        <h3>Дополнительная информация по товару/грузу:</h3>
        <div class='add_detail_box' style="position:relative;">
            <div class='new_order_box'>
                <div style="clear:both;" ></div>
                <div>
                    <span class="label">Объём:</span>
                    <input style="width:180px;" class="textbox" maxlength="11" type='text' id='ovolume' name="ovolume" />
                    <span style="float: left;margin: 2px 6px;">м³</span>
                    <span style="float: left;margin: 2px 6px;">Пример: 5,5</span>
                </div>
                <div style="clear:both;" ></div>
                <div>
                    <span class="label">ТН ВЭД:</span>
                    <input style="width:180px;" class="textbox" maxlength="11" type='text' id='otnved' name="otnved" />
                </div>
                <div style="clear:both;" ></div>
                <div>
                    <span class="label">Требуется страховка?</span>
                   	<input type='checkbox' id='insurance' name="insurance" value="1" />
				</div>
                <div style="clear:both;" ></div>
                <div>
                    <span class="label">Стоимость:</span>
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
                    <span class="label">Комментарий к товару:</span>
                    <textarea style="width:180px;resize:vertical!important;"
							  class="textbox"
							  maxlength="255"
							  id='ocomment'
							  name="ocomment"></textarea>
                </div>
                <div style="clear:both;" ></div>
            </div>
        </div>
		<div style="height: 50px;" class="admin-inside">
			<div class="submit">
				<div>
					<input type="submit" value="Добавить товар" id="addItem" name="add">
				</div>
			</div>
		</div>
    </form>
</div>