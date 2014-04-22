<div class='table' id="package_item_preview_block" style="z-index:1000;display:none;">
	<center>
		<h3 style="margin-top:0;margin-bottom:20px;">Описание товара</h3>
	</center>
	<form class='admin-inside' id="odetailForm">
		<table>
			<col width="128px" />
			<col width="auto" />
			<tr>
				<td>Ссылка на товар:</td>
				<td>
					<a id="pdetail_link" href="javascript:return void(0);"></a>
				</td>
			</tr>
			<tr>
				<td>Название товара:</td>
				<td id="pdetail_product_name"></td>
			</tr>
			<tr>
				<td>Цвет:</td>
				<td id="pdetail_product_color"></td>
			</tr>
			<tr>
				<td>Размер:</td>
				<td id="pdetail_product_size"></td>
			</tr>				
			<tr>
				<td>Количество:</td>
				<td id="pdetail_product_amount"></td>
			</tr>
			<tr>
				<td>Скриншот товара:</td>
				<td id="itemscreenshot"></td>
			</tr>
			<tr class='last-row'>
				<td colspan='9'>
					<div class='float'>	
						<div class='submit'><div><input type='button' value='Закрыть' onclick="$('#lay').fadeOut('slow');$('#package_item_preview_block').fadeOut('slow');"/></div></div>
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
		pdetail = eval("pdetail"+id);
		$('#package_item_preview_block h3').html('Описание товара №' + id);
		$('#package_item_preview_block #pdetail_link').html(pdetail.pdetail_img).attr('href', pdetail.pdetail_img);
		$('#package_item_preview_block #pdetail_product_name').html(pdetail.pdetail_product_name);
		$('#package_item_preview_block #pdetail_product_color').html(pdetail.pdetail_product_color);
		$('#package_item_preview_block #pdetail_product_size').html(pdetail.pdetail_product_size);
		$('#package_item_preview_block #pdetail_product_amount').html(pdetail.pdetail_product_amount);
		$('#package_item_preview_block #itemscreenshot').html($('#pdetail_link' + id).html()).click(function(){
				$('#lay').fadeOut("slow");
				$('#package_item_preview_block').fadeOut("slow");
			});
		package_item_preview();
		return false;
	}

	function center(item) {
		item.css("position","absolute");
		item.css("top", (($(window).height() - item.outerHeight()) / 2) + $(window).scrollTop() + "px");
		item.css("left", ((960 - item.width()) / 2) + "px");
		return item;
	}

	var package_item_preview_click = 0;
	function package_item_preview(){
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		$('#lay').fadeIn("slow");
		center($('#package_item_preview_block').fadeIn("slow"));
		
		if (!package_item_preview_click){
			package_item_preview_click = 1;
			$('#lay').click(function(){
				$('#lay').fadeOut("slow");
				$('#package_item_preview_block').fadeOut("slow");
			})
		}
	}
</script>