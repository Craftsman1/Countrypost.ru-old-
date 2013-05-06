<? $handler = $order->is_creating ?
	'addProduct' :
	'addProductToPrivilegedOrder';

$target = $selfurl . $handler . '/' . $order->order_id;
?>
<div class="service_order_form">
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
                <div style="height: 60px">
                    <span class="label">Подробное описание что<br/> нужно сделать*:</span>
                    <textarea class="textbox"
							  maxlength="255"
							  id='ocomment'
							  name="ocomment" style="resize: vertical!important;"></textarea>
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
                <div style="height: 30px;">
					<? View::show("client/elements/showAddProduct/screenshot_box"); ?>
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