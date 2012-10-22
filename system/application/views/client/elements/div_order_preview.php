<div class='table' id="order_preview_block"  style="max-width: 550px;position:fixed; z-index: 1000; display:none;">
	<center>
		<h3 style="margin-top:0;margin-bottom:20px;">Описание заказа</h3>
	</center>
	<form class='admin-inside' id="orderPreviewForm">
		<table>
			<tr>
				<td>Номер заказа:</td>
				<td id="preview_order_id"></td>
			</tr>
			<tr>
				<td>Название магазина:</td>
				<td id="preview_order_shop_name"></td>
			</tr>
			<tr>
				<td>Страна:</td>
				<td id="preview_order_country"></td>
			</tr>
			<tr>
				<td>Дата:</td>
				<td id="preview_order_date"></td>
			</tr>
			<tr>
				<td>Общая стоимость с местной доставкой:</td>
				<td id="preview_order_products_cost"></td>
			</tr>
			<tr>
				<td>Статус:</td>
				<td id="preview_order_status"></td>
			</tr>
			<tr class='last-row'>
				<td colspan='9'>
					<div class='float'>	
						<div class='submit'><div><input type='button' value='Закрыть' onclick="$('#lay').fadeOut('slow');$('#order_preview_block').fadeOut('slow');"/></div></div>
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
	function showOrderDetails(id){
		$('#order_preview_block h3').html('Описание заказа №' + id);
		order = eval("order" + id);
		$('#orderPreviewForm #preview_order_id').html(order.order_id);
		$('#orderPreviewForm #preview_order_shop_name').html(order.order_shop_name);
		$('#orderPreviewForm #preview_order_country').html(order.order_country);
		$('#orderPreviewForm #preview_order_date').html(order.order_date);
		$('#orderPreviewForm #preview_order_products_cost').html('$' + order.order_products_cost);
		$('#orderPreviewForm #preview_order_status').html(order_statuses[order.order_status]);
		
		order_preview_lay();
		return false;
	}

	var order_preview_click = 0;
	function order_preview_lay(){
		var offsetLeft	= (window.innerWidth - $('#order_preview_block').width()) / 2;
		var offsetTop	= (window.innerHeight - $('#order_preview_block').height()) / 2;
		
		$('#order_preview_block').css({
			'left' : offsetLeft,
			'top' : offsetTop
		});
		
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
					
		$('#lay').fadeIn("slow");
		$('#order_preview_block').fadeIn("slow");
		
		if (!order_preview_click){
			order_preview_click = 1;
			$('#lay').click(function(){
				$('#lay').fadeOut("slow");
				$('#order_preview_block').fadeOut("slow");
			})
		}
	}
	
	var order_statuses = <?= json_encode($statuses) ?>;
</script>