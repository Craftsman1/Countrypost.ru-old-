<script type="text/javascript" src="/static/js/easyTooltip.js"></script>
<script type="text/javascript">
	var package_price_hints = {
		"package_delivery"		:"стоимость доставки",
		"package_comission"		:"оплата услуг за пересылку",
		"package_join"			:"за каждое нажатие кнопки Объединить<br />с вашего счета снимается 3$,<br />поэтому выбирайте все посылки сразу,<br />которые хотите объединить",
		"package_declaration"	:"помощь в заполнении декларации",
		"package_insurance"		:"стоимость страховки",
		"remove_package_insurance"		:"удалить страховку",
		"package_foto"			:"комиссия за фото",
		"package_foto_system"	:"комиссия за фото",
		"package_special_cost"	:"комиссия за доп.услуги",
		"package_foto_join"		:"Вы можете заказать одно общее фото<br />для выбранных товаров вместо<br />нескольких отдельных",
	};
	
	function put_package_hints() 
	{
		$.each(package_price_hints, function(i, val) {
			$("img.tooltip_" + i).easyTooltip({
				tooltipId: "tooltip_id",
				content: '<div class="box"><div>' + val + '</div></div>'
			});
		});
	}
</script>