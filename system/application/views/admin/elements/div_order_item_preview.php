<div class='table' id="lay4_block" style="z-index:1000;display:none;">
	<center>
		<h3 style="margin-top:0;margin-bottom:20px;">Описание товара</h3>
	</center>
	<form class='admin-inside' id="odetailForm">
		<table>
			<col width="128px" />
			<col width="auto" />
			<tr>
				<td>Название магазина:</td>
				<td id="odetail_shop_name"></td>
			</tr>
			<tr>
				<td>Ссылка на товар:</td>
				<td>
					<a id="odetail_link" href="javascript:return void(0);"></a>
				</td>
			</tr>
			<tr>
				<td>Название товара:</td>
				<td id="odetail_product_name"></td>
			</tr>
			<tr>
				<td>Страна&nbsp;производитель:</td>
				<td id="order_country">
				</td>
			</tr>
			<tr>
				<td>Цвет:</td>
				<td id="odetail_product_color"></td>
			</tr>
			<tr>
				<td>Размер:</td>
				<td id="odetail_product_size"></td>
			</tr>				
			<tr>
				<td>Количество:</td>
				<td id="odetail_product_amount"></td>
			</tr>
			<tr>
				<td>Скриншот товара:</td>
				<td id="itemscreenshot"></td>
			</tr>
			<tr class='last-row'>
				<td colspan='9'>
					<div class='float'>	
						<div class='submit'><div><input type='button' value='Закрыть' onclick="$('#lay').fadeOut('slow');$('#lay4_block').fadeOut('slow');"/></div></div>
					</div>
				</td>
				<td>
				</td>
			</tr>
		</table>
	</form>
</div>
<script src="<?=JS_PATH?>jquery.form.js"></script>
<script type="text/javascript">
	function showOdetail(id){
		odetail = eval("odetail"+id);
		$('#lay4_block h3').html('Описание товара №' + id);
		$('#odetailForm #odetail_shop_name').html(odetail.odetail_shop_name);
		$('#odetailForm #odetail_link').html(odetail.odetail_link).attr('href', odetail.odetail_link);
		$('#odetailForm #odetail_product_name').html(odetail.odetail_product_name);
		$('#odetailForm #odetail_product_color').html(odetail.odetail_product_color);
		$('#odetailForm #order_country').html(order_country);
		$('#odetailForm #odetail_product_size').html(odetail.odetail_product_size);
		$('#odetailForm #odetail_product_amount').html(odetail.odetail_product_amount);
		$('#odetailForm #itemscreenshot').html($('#odetail_link' + id).html()).click(function(){
				$('#lay').fadeOut("slow");
				$('#lay4_block').fadeOut("slow");
			});
		lay4();
		return false;
	}

	function center(item) {
		item.css("position","absolute");
		item.css("top", (($(window).height() - item.outerHeight()) / 2) + $(window).scrollTop() + "px");
		item.css("left", ((960 - item.width()) / 2) + "px");
		return item;
	}

	var fmclick4 = 0;
	function lay4(){
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		$('#lay').fadeIn("slow");
		center($('#lay4_block').fadeIn("slow"));
		
		if (!fmclick4){
			fmclick4 = 1;
			$('#lay').click(function(){
				$('#lay').fadeOut("slow");
				$('#lay4_block').fadeOut("slow");
			})
		}
	}
</script>