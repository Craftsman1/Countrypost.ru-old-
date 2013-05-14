<div class='table' style="position:relative;">
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<form class='admin-inside'
		  action="<?= $selfurl ?>checkout/<?= $order->order_id ?>"
		  id="orderForm"
		  method="POST">
		<div class='new_order_box' style="height: auto!important;">
			<div>
				<span class="label">Заказать из*:</span>
				<select id="country_from"
						name="country_from"
						class="textbox country"
						onchange="updateCountryFrom();">
					<option value="0">выберите страну...</option>
					<? foreach ($countries as $country) : ?>
						<option
							value="<?= $country->country_id ?>"
							title="<?= IMG_PATH ?>/flags/<?= $country->country_name_en ?>.png"
							<? if ($order->order_country_from == $country->country_id) : ?>selected<? endif; ?>>
							<?= $country->country_name ?>
						</option>
					<? endforeach; ?>
				</select>
			</div>
			<div>
				<span class="label">Нужна ли доставка?</span>
				<input type='checkbox'
					   id='delivery_requested'
					   name="delivery_requested"
					   style="margin-left: 0;"
					   <? if ($order->order_country_to) : ?>checked<? endif; ?> />
				<label for="delivery_requested" style="font: 13px sans-serif;">да</label>
			</div>
			<div class="delivery_section"
				 <? if (empty($order->order_country_to)) : ?>style="display: none;"<? endif; ?>>
				<span class="label">В какую страну доставить:</span>
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
			<div class="delivery_section"
				 <? if (empty($order->order_country_to)) : ?>style="display: none;"<? endif; ?>>
				<span class="label">Город доставки:</span>
				<input class="textbox"
					   id="city_to"
					   name="city_to"
					   maxlength="255"
					   type='text'
					   value="<?= $order->order_city_to ?>"
					   onchange="updateCityTo();">
			</div>
			<div class="delivery_section"
				 style="height: 30px;<? if (empty($order->order_country_to)) : ?>display: none;<? endif; ?>">
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