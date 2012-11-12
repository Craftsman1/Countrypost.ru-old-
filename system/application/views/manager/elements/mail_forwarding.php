<div class="mail_forwarding dealer_tab" style="display:none;">
	<form action="/manager/saveAddress" id="addressForm" method="POST">
		<div class="table mail_forwarding_main admin-inside" style="height:290px;">
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<div>
				<span class="label">Адрес доставки*:</span>
			</div>
			<br style="clear:both;" />
			<div>
				<input class="textbox" maxlength="1024" type='text' id='address' name="address" value="<?=
				$manager->manager_address_local ?>"/>
				<span class="label hint">Пример: 116013, 中国, 辽宁省, 大连市, 中山区, 桃源街23, 305</span>
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Адрес доставки на английском*:</span>
			</div>
			<br style="clear:both;" />
			<div>
				<input class="textbox" maxlength="1024" type='text' id='address_en' name="address_en"
					   value="<?= $manager->manager_address ?>"/>
				<span class="label hint">Пример: 116013, China, Liaoning Province, Dalian, Zhongshan District, Taoyuan Street 23, 305</span>
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Телефон:</span>
			</div>
			<br style="clear:both;" />
			<div>
				<input class="textbox" maxlength="255" type='text' id='phone' name="phone"
					   value="<?= $manager->manager_phone ?>"/>
			</div>
		</div>
		<h3>Пример заполнения адреса</h3>
		<div class="table">
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<textarea maxlength="65535" id='address_description' name="address_description"><?= html_entity_decode
			($manager->manager_address_description) ?></textarea>
		</div>
		<br>
		<div class="submit floatleft">
			<div>
				<input type="submit" value="Сохранить">
			</div>
		</div>
		<img class="float" id="addressProgress" style="display:none;margin:0px;margin-top: -2px;margin-left: 8px;"
			 src="/static/images/lightbox-ico-loading.gif"/>
	</form>
</div>
<script type="text/javascript">
	$(function() {
		$('#addressForm').ajaxForm({
			target: '/manager/saveAddress',
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$("#addressProgress").show();
			},
			success: function(response)
			{
				$("#addressProgress").hide();
				success('top', 'Адреса доставки успешно сохранено!');
			},
			error: function(response)
			{
				$("#addressProgress").hide();
				error('top', 'Заполните все поля и сохраните еще раз.');
			}
		});
	});

	<?= editor('address_description', 200, 920, 'PackageComment') ?>
</script>