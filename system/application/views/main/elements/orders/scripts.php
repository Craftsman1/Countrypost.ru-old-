<script>
	function updateCountryFrom()
	{
		var country_id = $('select#country_from').val();
		$('input.country_from').val(country_id);

		for (var index in currencies)
		{
			var currency = currencies[index];

			if (country_id == currency['country_id'])
			{
				$('.currency')
					.html(currency['country_currency']);

				$('.order_currency')
					.val(currency['country_currency']);
				break;
			}
		}
	}

	function updateCountryTo()
	{
		$('input.country_to').val($('select#country_to').val());
	}

	function updateCityTo()
	{
		$('input.city_to').val($('input#city_to').val());
	}

	function updateDelivery()
	{
		$('input.preferred_delivery').val($('input#preferred_delivery').val());
	}

	function checkout()
	{
		$('form#orderForm').submit();
	}


	// скриншот
	function showScreenshotLink()
	{
		$('.screenshot_link_box').show('slow');
		if ($('.screenshot_link_box').val() == '')
		{
			$('.screenshot_link_box').val('ссылка на скриншот')
		}

		$('.screenshot_switch').hide('slow');
	}

	function showScreenshotUploader()
	{
		$('.screenshot_uploader_box').show('slow');
		if ($('.screenshot_link_box').val() == 'ссылка на скриншот')
		{
			$('.screenshot_link_box').val('')
		}
		$('.screenshot_switch').hide('slow');
	}
</script>