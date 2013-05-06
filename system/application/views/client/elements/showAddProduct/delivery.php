<? $handler = $order->is_creating ?
	'addProduct' :
	'addProductToPrivilegedOrder';

$target = $selfurl . $handler . '/' . $order->order_id;
?>
<div class="delivery_order_form">
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
					<span class="label">Наименование товара*:</span>
					<input class="textbox"
						   maxlength="255"
						   type='text'
						   id='oname'
						   name="oname" />
                </div>
                <div>
					<span class="label">Примерный вес (кг)*:
					</span>
					<input class="textbox"
						   maxlength="255"
						   type='text'
						   id='oweight'
						   name="oweight" />
					<div style="clear:both;" ></div>
                </div>
				<div style="height: 30px;">
					<span class="label">Требуется страховка?</span>
					<input type='checkbox'
						   id='insurance'
						   name="insurance"
						   value="1" />
					<label for="insurance" style="font: 13px sans-serif;">да</label>
				</div>
            </div>
        </div>
        <h3>Дополнительная информация о товаре/грузe:</h3>
        <div class='add_detail_box' style="position:relative;">
            <div class='new_order_box'>
				<div>
					<span class="label">Ссылка на товар:</span>
					<input class="textbox"
						   maxlength="4096"
						   type='text'
						   id='olink'
						   name="olink" />
				</div>
                <div>
                    <span class="label">Объём:</span>
                    <input class="textbox"
						   maxlength="11"
						   type='text'
						   id='ovolume'
						   name="ovolume" />
					<span class="label">м³&nbsp;&nbsp;<i>Пример: 5.5</i></span>
                </div>
                <div>
                    <span class="label">ТН ВЭД:</span>
                    <input class="textbox"
						   maxlength="11"
						   type='text'
						   id='otnved'
						   name="otnved" />
                </div>
				<div>
					<span class="label">Количество*:</span>
					<input class="textbox"
						   maxlength="11"
						   type='text'
						   id='oamount'
						   name="oamount"
						   value="1" />
				</div>
                <div>
                    <span class="label">Стоимость:</span>
					<input class="textbox"
						   maxlength="11"
						   type='text'
						   id='oprice'
						   name="oprice" />
					<span class="label currency"><?= $order_currency ?></span>
                </div>
                <div>
                    <span class="label">Местная доставка:</span>
					<input class="textbox"
						   maxlength="11"
						   type='text'
						   id='odeliveryprice'
						   name="odeliveryprice" />
					<span class="label currency"><?= $order_currency ?></span>
                </div>
                <div>
					<span class="label">Комментарий к товару:</span>
					<textarea style="resize:vertical!important;"
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
		</div>
    </form>
</div>