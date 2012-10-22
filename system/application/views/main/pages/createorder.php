<div class='content'>
	<h2 id='page_title'>Выберите вид заказа:</h2>
	<div class="order_type_selector">
		<div class="online_order order">
			<div>
				<b>Online заказ</b>
			</div>
			<div>Заказ на покупку и доставку из любого интернет-магазина, торговой площадки, аукциона и т.д.
			</div>
		</div>
		<div class="offline_order order">
			<div>
				<b>Offline заказ</b>
			</div>
			<div>Заказ на покупку и доставку из любого offline магазина/поставщика у которого нет сайта или online продаж. 
				<br />Заявки на поиск товара/поставщика также добавляйте сюда.
			</div>
		</div>
		<div class="service_order order">
			<div>
				<b>Услуга</b>
			</div>
			<div>Если Вам нужна какая-то помощь или услуга, не связанная с покупкой и доставкой, в любой стране.
			</div>
		</div>
		<div class="delivery_order order">
			<div>
				<b>Доставка</b>
			</div>
			<div>Если Вам нужна только доставка без выкупа и поиска товара.
			</div>
		</div>
	</div>
	<? View::show('main/elements/orders/online'); ?>
</div>
<script>
	$(function() {
		$('div.order1').click(function() {
			$order_type = $(this);
			$('div.order_type_selector').hide();
			$('h2#page_title').html($order_type.find('b').html());
			
			if ($order_type.is('.offline')) 
			{
				show_offline_order();
			}			
			else if ($order_type.is('.service')) 
			{
				show_service_order();
			}			
			else if ($order_type.is('.delivery')) 
			{
				show_delivery_order();
			}
		});
	});
	
	function createOrder()
	{
		$.ajax({
			url: '<?= $selfurl ?>addEmptyOrder/online',
			type: 'POST',
			dataType: 'html',
			success: function(data) {
				$('input.order_id').val(data);
			},
		});
	}
	
	function setCountryFrom(id)
	{
		var prevCurrency = selectedCurrency;
		
		for (var index in currencies)
		{
			var currency = currencies[index];
		
			if (id == currency['country_id'])
			{
				selectedCurrency = currency['country_currency'];
				$('input.countryFrom').val(id);
				$('.currency').html(selectedCurrency);
				updateTotals();
				break;
			}
		}
	}
	
	function setCountryTo(id)
	{
		for (var index in currencies)
		{
			var currency = currencies[index];
		
			if (id == currency['country_id'])
			{
				$('input.countryTo').val(id);
				countryTo = currency['country_name'];
				updateTotals();
				break;
			}
		}
	}
	
	function checkout()
	{
		$('form#orderForm').submit();
	}
	
	var currencies = <?= json_encode($countries); ?>;
	var selectedCurrency = '';
	//var countryFrom = '';
	var countryTo = '';
	var cityTo = '';
</script>