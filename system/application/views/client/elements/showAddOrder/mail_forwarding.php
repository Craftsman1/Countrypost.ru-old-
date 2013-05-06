<div>
	<ol style="padding: 10px 0px; font-size: 14px; line-height: 26px;">
		<li>Выберите посредника, на адрес которого Вы будете самостоятельно заказывать товары.</li>
		<li>Добавьте ниже все товары, заказанные на адрес посредника (для каждого товара укажите номер посылки -
			Tracking номер).</li>
	</ol>
</div>
<br>
<div class='table' style="position:relative;">
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<form class='admin-inside'
		  action="<?= $selfurl ?>checkout/<?= $order->order_id ?>"
		  id="orderForm"
		  method="POST">
		<div class='new_order_box'>
			<div style="height: 50px">
				<span class="label">Посредник*:</span>
				<select id="dealer_id"
						name="dealer_id"
						class="textbox country"
						onchange="updateDealer();">
					<option value="0">выберите посредника...</option>
					<? foreach ($dealers as $dealer) : ?>
						<option
							value="<?= $dealer->manager_user ?>"
							title="<?= IMG_PATH ?>/flags/<?= $dealer->country_name_en ?>.png"
							<? if ($order->order_manager == $dealer->manager_user) : ?>selected<? endif; ?>>
							<?= $dealer->manager_name ?> (<?= $dealer->user_login ?>)
						</option>
					<? endforeach; ?>
				</select>
			</div>
			<div>
				<span class="label">В какую страну доставить*:</span>
				<select id="country_to"
						name="country_to"
						class="textbox country"
						onchange="updateCountryTo();">
					<option value="0">выберите страну...</option>
					<? foreach ($countries as $country) : ?>
						<option
							value="<?= $country->country_id ?>"
							title="<?= IMG_PATH ?>/flags/<?= $country->country_name_en ?>.png"
							<? if ($order->order_country_to == $country->country_id) : ?>selected<? endif; ?>>
							<?= $country->country_name ?>
						</option>
					<? endforeach; ?>
				</select>
			</div>
			<div style="height: 30px">
				<span class="label">Cпособ доставки:</span>
				<input class="textbox"
					   id="preferred_delivery"
					   id="name"
					   maxlength="255"
					   type='text'
					   value="<?= $order->preferred_delivery ?>"
					   onchange="updateDelivery();">
			</div>
		</div>
	</form>
</div>
