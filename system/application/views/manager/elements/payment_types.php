<div class="payments dealer_tab" style="display:none;">
	<form action="/manager/savePaymentTypes" id="paymentsForm" method="POST">
		<div class="table">
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<textarea maxlength="65535" id='payment_types' name="payment_types"><?= empty
				($manager->payments_description) ?	''	: html_entity_decode($manager->payments_description) ?></textarea>
		</div>
		<br>
		<div class="submit floatleft">
			<div>
				<input type="submit" value="Сохранить">
			</div>
		</div>
		<img class="float" id="paymentsProgress" style="display:none;margin:0px;margin-top: -2px;margin-left: 8px;"
			 src="/static/images/lightbox-ico-loading.gif"/>
	</form>
</div>
<script>
	$(function() {
		$('#paymentsForm').ajaxForm({
			target: '/manager/savePaymentTypes',
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$("#paymentsProgress").show();
			},
			success: function(response)
			{
				$("#paymentsProgress").hide();
				success('top', 'Способы оплаты успешно сохранены!');
			},
			error: function(response)
			{
				$("#paymentsProgress").hide();
				error('top', 'Заполните все поля и сохраните еще раз.');
			}
		});
	});

	<?= editor('payment_types', 200, 920, 'PackageComment') ?>
</script>