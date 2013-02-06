<form action="/manager/savePricelist" id="pricelistForm" method="POST">
	<div class="pricelist pricelist_main table dealer_tab" style="display:none;">
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<div>
			<span>
				<b>ЗАКАЗ:</b>
			</span>
		</div>
		<div>
			<span>
				Комиссия за обычный заказ:
			</span>
			<span>
				<input class="textbox" maxlength="6" type='text' id='order_tax' name="order_tax" value="<?=
				$manager->order_tax ?>" /> %
			</span>
		</div>
		<div>
			<span>
				Комиссия за заказ Mail Forwarding:
			</span>
			<span>
				<input class="textbox" maxlength="11" type='text' id='mf_tax' name="mf_tax" value="<?=
				$manager->order_mail_forwarding_tax ?>" /> <?= $manager->statistics->currency ?>
			</span>
		</div>
		<div>
			<span>
				Минимальная комиссия за заказ:
			</span>
			<span>
				<input class="textbox" maxlength="4" type='text' id='min_order_tax' name="min_order_tax" value="<?=
				$manager->min_order_tax ?>" /> <?= $manager->statistics->currency ?>
			</span>
		</div>
		<br>
		<div>
			<span>
				<b>ДОПОЛНИТЕЛЬНЫЕ УСЛУГИ:</b>
			</span>
		</div>
		<div>
			<span>
				Объединение посылок (консолидация):
			</span>
			<span>
				<input class="textbox" maxlength="11" type='text' id='join_tax' name="join_tax" value="<?=
				$manager->join_tax ?>" /> <?= $manager->statistics->currency ?>
			</span>
		</div>
		<div>
			<span>
				Комиссия за фото:
			</span>
			<span>
				<input class="textbox" maxlength="11" type='text' id='foto_tax' name="foto_tax" value="<?=
				$manager->foto_tax ?>" /> <?= $manager->statistics->currency ?>
			</span>
		</div>
		<div>
			<span>
				Оформление страховки:
			</span>
			<span>
				<input class="textbox" maxlength="3" type='text' id='insurance_tax' name="insurance_tax" value="<?=
				$manager->insurance_tax ?>" /> %
			</span>
		</div>
	</div>
	<div class="pricelist dealer_tab" style="display:none;">
		<h3>Подробное описание</h3>
		<div class="table">
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<textarea maxlength="65535" id='pricelist_message' name="pricelist_message"><?= empty
			($manager->pricelist_description) ?	''	: html_entity_decode($manager->pricelist_description) ?></textarea>
		</div>
		<br>
		<div class="submit floatleft">
			<div>
				<input type="submit" value="Сохранить">
			</div>
		</div>
		<img class="float" id="pricelistProgress" style="display:none;margin:0px;margin-top: -2px;margin-left: 8px;"
			 src="/static/images/lightbox-ico-loading.gif"/>
	</div>
</form>
<script>
	$(function() {
		$('#pricelistForm').ajaxForm({
			target: '/manager/savePricelist',
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$("#pricelistProgress").show();
			},
			success: function(response)
			{
				$("#pricelistProgress").hide();
				success('top', 'Тарифы успешно сохранены!');
			},
			error: function(response)
			{
				$("#pricelistProgress").hide();
				error('top', 'Заполните все поля и сохраните еще раз.');
			}
		});
	});

	<?= editor('pricelist_message', 200, 920, 'PackageComment') ?>
</script>