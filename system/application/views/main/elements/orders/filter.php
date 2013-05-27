<div class='step-by-step'>
	<p style="text-align: justify;">
		<b>Countrypost</b> – поможет Вам легко и быстро найти посредника для покупки и доставки любого товара из online или offline магазинов Китая, США, Германии, Италии или любой другой страны мира. 
		<a href="/main/createorder">Добавьте</a> заказ прямо сейчас и Вы получите<b> БЕСПЛАТНО </b>предложения от посредников с итоговой стоимостью за выполнение заказа или услуги.
	</p>
	<form class='admin-inside' action="<?= $selfurl ?>filterUnassignedOrders" id="filterForm" method="POST">
		<div class='table' style="position:relative;background:#fff;">
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>	
			<div class='filter-box'>
				<div>
					<span class="label">Номер заказа:</span>
					<input style="width:180px;" class="textbox" maxlength="6" type='text' id='order_number' name="order_number" value="<?= empty($filter->order_id) ? '' : $filter->order_id ?>"/>
				</div>
				<br />
				<br />
				<div>
					<span class="label">Заказ из:</span>
					<select id="country_from" name="country_from" class="select" style="width:190px;">
						<option value="0">все страны</option>
						<? foreach ($countries as $country) : ?>
						<option value="<?= $country->country_id ?>"  title="/static/images/flags/<?= $country->country_name_en ?>.png" <? if (isset($filter->country_from) AND $filter->country_from == $country->country_id) : ?>selected<? endif; ?>><?= $country->country_name ?></option>
						<? endforeach; ?>
					</select>
					<span class="label">&nbsp;&nbsp;&nbsp;Доставить в:</span>
					<select id="country_to" name="country_to" class="select" style="width:190px;">
						<option value="0">все страны</option>
						<? foreach ($countries as $country) : ?>
						<option value="<?= $country->country_id ?>"  title="/static/images/flags/<?= $country->country_name_en ?>.png" <? if (isset($filter->country_to) AND $filter->country_to == $country->country_id) : ?>selected<? endif; ?>><?= $country->country_name ?></option>
						<? endforeach; ?>
					</select>
				</div>
				<br />
				<br />
				<div>
					<span class="label">Вид заказа:</span>
					<select class="select" style="width:190px;" id="order_type" name="order_type">
						<option value="">все заказы</option>
						<option value="online" <? if (isset($filter->order_type) AND $filter->order_type == "online") : ?>selected<? endif; ?>>Online заказ</option>
						<option value="offline" <? if (isset($filter->order_type) AND $filter->order_type == "offline") : ?>selected<? endif; ?>>Offline заказ</option>
						<option value="service" <? if (isset($filter->order_type) AND $filter->order_type == "service") : ?>selected<? endif; ?>>Услуга</option>
						<option value="delivery" <? if (isset($filter->order_type) AND $filter->order_type == "delivery") : ?>selected<? endif; ?>>Доставка</option>
					</select>
				</div>
				<br style="clear:both;" />
				<br />
				<div>
					<span class="label"></span>
					<div style="">
						<input type='submit' id="filterSubmit" value='Найти' style="width:91px;height: 27px;font: 13px sans-serif;vertical-align: top;margin-bottom:4px;margin-right:10px;"/>
						<img class="float" id="importProgress" style="display:none;margin:0px;margin-top:-5px;" src="/static/images/lightbox-ico-loading.gif"/>
					</div>
				</div>
			</div>
		</div>
	</form>
	<a name="pagerScroll" class="pagerScroll"></a>
	<div class="add_order_box">
        <? if (!$this->user OR $this->user->user_group != 'manager') : ?>
            <div class="admin-inside" id='add_package_button'>
                <div class="submit">
                    <div>
                        <input style="font:13px sans-serif;" type="button" onclick="window.location = '/main/createorder';" value="Добавить заказ">
                    </div>
                </div>
            </div>
            <span>
                Бесплатно
            </span>
        <? else : ?>
            <div class="admin-inside" id='add_package_button'>
            </div>
        <? endif; ?>
		<span class="total">
			Найдено заказов: <b id="orders_count"><?= $orders_count ?></b>
		</span>
	</div>
</div>
<script type="text/javascript">
	$(function() {
		$("#country_from").msDropDown({mainCSS:'idd'});
		$("#country_to").msDropDown({mainCSS:'idd'});
		$("#order_type").msDropDown({mainCSS:'idd'});
		$("#filterForm").show();

		$('#filterForm').ajaxForm({
			target: '<?= $selfurl ?>filterUnassignedOrders',
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$("#importProgress").show();
			},
			success: function(response)
			{
				$('#filterForm').append($(response));
				$("#importProgress").hide();
				
				$("div#unassignedOrders,a.pagerScroll,div.pages").remove();					
				$("div.adittional-block").html(response);
			},
			error: function(response)
			{
				$("#importProgress").hide();
			}
		});
	});
</script>