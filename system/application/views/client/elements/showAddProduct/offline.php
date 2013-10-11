<? $handler = $order->is_creating ?
	'addProduct' :
	'addProductToPrivilegedOrder';

$target = $selfurl . $handler . '/' . $order->order_id;
?>
<div class="offline_order_form">
	<form class='admin-inside orderForm'
		  action="<?= $target ?>"
		  method="POST"
		  enctype="multipart/form-data">
		<? View::show('client/elements/showAddProduct/hiddens'); ?>
        <div class='table add_detail_box' style="position:relative;">
            <div class='angle angle-lt'></div>
            <div class='angle angle-rt'></div>
            <div class='angle angle-lb'></div>
            <div class='angle angle-rb'></div>
            <div class='new_order_box'>
                <div>
                    <span class="label">Наименование товара<span class="red-color">*</span>:</span>
                    <input class="textbox"
						   maxlength="255"
						   type='text'
						   id='oname'
						   name="oname" />
                </div>
                <div>
                    <span class="label">Название магазина<span class="red-color">*</span>:</span>
                    <input class="textbox"
						   maxlength="255"
						   type='text'
						   id='oshop'
						   name="oshop" />
                </div>
                <div>
					<span class="label">Цвет<span class="red-color">*</span>:</span>
					<input class="textbox"
						   maxlength="255"
						   type='text'
						   id='ocolor'
						   name="ocolor" />
				</div>
				<div>
					<span class="label">Размер<span class="red-color">*</span>:</span>
					<input class="textbox"
						   maxlength="255"
						   type='text'
						   id='osize'
						   name="osize" />
				</div>
				<div>
					<span class="label">Стоимость<span class="red-color">*</span>:</span>
					<input class="textbox"
						   maxlength="11"
						   type='text'
						   id='oprice'
						   name="oprice" />
					<span class="label currency"><?= $order_currency ?></span>
                </div>
				<div style="height: 30px;">
					<span class="label">Местная доставка:</span>
					<input class="textbox"
						   maxlength="11"
						   type='text'
						   id='odeliveryprice'
						   name="odeliveryprice" />
					<span class="label currency"><?= $order_currency ?></span>
				</div>
            </div>
        </div>
        <h3>Дополнительная информация о товаре:</h3>
        <div class='add_detail_box' style="margin-top: 20px;position:relative;">
            <div class='new_order_box'>
				<div>
					<span class="label">Примерный вес (кг):
					</span>
					<input style="width:180px;" class="textbox" maxlength="255" type='text' id='oweight' name="oweight" />
					<div style="clear:both;" ></div>
				</div>
				<div>
					<span class="label">Количество:</span>
					<input class="textbox"
						   maxlength="255"
						   type='text'
						   id='oamount'
						   name="oamount"
						   value="1" />
				</div>
				<div>
					<? View::show("client/elements/showAddProduct/screenshot_box"); ?>
                </div>
                <div>
					<span class="label">Нужно ли фото товара?</span>
					<input type='checkbox'
						   id='foto_requested'
						   name="foto_requested"
						   value="1" />
					<label for="foto_requested" style="font: 13px sans-serif;">да</label>
                </div>
                <div>
                    <span class="label">Требуется поиск товара?</span>
                    <input type='checkbox'
						   id='search_requested'
						   name="search_requested"
						   value="1" />
					<label for="search_requested" style="font: 13px sans-serif;">да</label>
                </div>
                <div style="height: 60px;">
					<span class="label">Комментарий к товару:</span>
					<textarea style="resize:vertical!important; display: block;"
							  class="textbox"
							  maxlength="255"
							  id='ocomment'
							  name="ocomment"></textarea>
                </div>
            </div>
        </div>
		<br>
		<div style="height: 50px;" class="admin-inside">
			<div class="submit">
				<div>
					<input type="submit" value="Добавить товар">
				</div>
			</div>
			<img src="/static/images/lightbox-ico-loading.gif"
				 style="margin-top: 3px; margin-left: 5px; display: none;"
				 class="float progress">
		</div>
	</form>
</div>