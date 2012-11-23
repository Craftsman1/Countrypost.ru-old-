<div class='content smallheader'>
	<? Breadcrumb::showCrumbs(); ?>
	<h2><?=$order->order_type?> Заказ №<?=$order->order_id?> <?=$order->order_country_from?> - <?=$order->order_country_to?> (<?=$order->order_city_to?>)</h2>
	<? View::show('client/ajax/showOrderInfo'); ?>
	<h3 class='clientOrderInfo' <? if (empty($order->order_manager)) : ?>style="display:none;"<? endif; ?>>Товары в заказе</h3>
	<? View::show('manager/ajax/showOrderDetails'); ?>
	<? View::show('main/elements/orders/bids'); ?>
</div>
<script type="text/javascript">
/*
	$(document).ready(function() {
		$('#orderForm input:text').keypress(function(event){validate_number(event);});

		$('#detailsForm').ajaxForm({
			target: '<?=$selfurl?>updateProductAjax/',
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
			},
			success: function(response)
			{
				$('#detailsForm').append($(response));
			},
			error: function(response)
			{
			}
		});
	});	
	
	function editItem(id) {
		if (!$('#odetail_shop_name' + id + ' textarea').length)
		{
			var odetail = eval('odetail' + id);
		
			$('#odetail_shop_name' + id)
				.html('<textarea name="shop' + id + '" style="width:50px;resize:auto;">' + odetail['odetail_shop_name'] + '</textarea>');

			$('#odetail_product_name' + id)
				.html('<textarea name="oname' + id + '" style="width:75px;resize:auto;">' + odetail['odetail_product_name'] + '</textarea>');

			$('#odetail_product_color' + id)
				.html('')
				.append('<textarea name="ocolor' + id + '" style="width:52px;height:14px;resize:auto;">' + odetail['odetail_product_color'] + '</textarea><br />')
				.append('<textarea name="osize' + id + '" style="width:52px;height:14px;resize:auto;">' + odetail['odetail_product_size'] + '</textarea><br />')
				.append('<textarea name="oamount' + id + '" style="width:52px;height:14px;resize:auto;">' + odetail['odetail_product_amount'] + '</textarea>');

			$('#odetail_link' + id)
				.children().hide().parent()
				.append('<textarea name="olink' + id + '" style="width:75px;resize:auto;">' + odetail['odetail_link'] + '</textarea>');

			$('#odetail_img' + id)
				.children().hide().parent()
				.append('<div style="width:120px;"><input type="radio" value="1" id="img' + id + '" name="img' + id + '" ><label for="img' + id + '"><textarea id="userfileimg' + id + '" type="text" name="userfileimg' + id + '" maxlength="4096" style="top:0;height:14px;width:92px;resize:auto;">' + odetail['odetail_img'] + '</textarea></label><br><input type="radio" value="2" id="img2' + id + '" name="img' + id + '" ><label for="img2' + id + '"><input id="userfile' + id + '" type="file" name="userfile" style="width:95px;"></label></div>');
				
			$('#odetail_edit' + id).remove();
			$('#odetail_action' + id + ' br').remove();
				
			$('#odetail_action' + id)
				.prepend('<a href="javascript:cancelItem(' + id + ')" id="odetail_cancel' + id + '"><img border="0" src="/static/images/comment-delete.png" title="Отменить"></a><br /><a href="javascript:saveItem(' + id + ')" id="odetail_save' + id + '"><img border="0" src="/static/images/done-filed.png" title="Сохранить"></a><br />');
		}
		else
		{
			cancelItem(id);
		}
	}

	function cancelItem(id) {
		if ($('#odetail_shop_name' + id + ' textarea').length)
		{
			var odetail = eval('odetail' + id);
			
			$('#odetail_shop_name' + id).html(odetail['odetail_shop_name']);
			$('#odetail_product_name' + id).html(odetail['odetail_product_name']);
			$('#odetail_product_color' + id).html(odetail['odetail_product_color'] + ' / ' + odetail['odetail_product_size'] + ' / ' + odetail['odetail_product_amount']);

			$('#odetail_link' + id + ',#odetail_img' + id).find('label,textarea,input,br').remove();
			$('#odetail_link' + id + ',#odetail_img' + id).children().show();
			$('#odetail_img' + id + ' a[rel]').hide();
						
			$('#odetail_action' + id)			
				.html('<a href="javascript:editItem(' + id + ')" id="odetail_edit' + id + '"><img border="0" src="/static/images/comment-edit.png" title="Изменить"></a><br /><a href="javascript:deleteItem(' + id + ')"><img border="0" src="/static/images/delete.png" title="Удалить"></a>');
		}
	}

	function saveItem(id) {
		if ($('#odetail_shop_name' + id + ' textarea').length)
		{
			$('#odetail_shop_name' + id).parent().find('textarea').attr('readonly', true);
			$('#odetail_action' + id).html('<img border="0" src="/static/images/lightbox-ico-loading.gif" title="Товар сохраняется..."><br><a href="javascript:cancelItem(' + id + ')" id="odetail_cancel' + id + '"><img border="0" src="/static/images/comment-delete.png" title="Отменить"></a>');
			$('#odetail_id').val(id);
			$('#detailsForm').submit();						
		}
	}
*/
	function setRel(id){
		$("a[rel*='lightbox_"+id+"']").lightBox();
		var aa = $("a[rel*='lightbox_"+id+"']");
		$(aa[0]).click();
	}
/*
	function validate_number(evt) {
		var theEvent = evt || window.event;
		var key = theEvent.keyCode || theEvent.which;
		key = String.fromCharCode( key );
		var regex = /[0-9]|\./;
		if( !regex.test(key) ) {
			theEvent.returnValue = false;
			theEvent.preventDefault();
		}
	}
	
	function updateItem(comment_id, user_login){
		$('.save').hide();
		$('.update').show();
		
		$('#comment_user').html(user_login);
		$('#comment_id').val(comment_id);
		var oEditor = FCKeditorAPI.GetInstance('comment_update');
		oEditor.SetHTML($('#comment_'+comment_id).html());

		window.location.href = '#edit_comment_area';
	}

	function editComment()
	{
		var $f = document.getElementById('commentForm');
		$f.action = '<?=$selfurl?>addOrderComment/<?=$order->order_id?>/'+$('#comment_id').val();
		$f.submit();
	}
	
	function cancel(){
		$('.save').show();
		$('.update').hide();

		$('#comment_user').html('');
		$('#comment_id').val('');
		$('#comment_update').text('');
		history.back();
	}

	function deleteItem(id){
		if (confirm("Вы уверены, что хотите удалить комментарий №" + id + "?"))
		{
			window.location.href = '<?=$selfurl?>delOrderComment/<?=$order->order_id?>/'+id;
		}
	}

	function joinProducts()
	{
		var selectedProds = $('#detailsForm input[type="checkbox"]:checked');
		
		if (selectedProds.length < 2)
		{
			alert("Выберите хотя бы 2 товара для объединения.");
			return false;			
		}
		
		if (confirm("Вы уверены, что хотите объединить выбранные товары?"))
		{
			var queryString = $('#detailsForm').formSerialize(); 
			$.post('<?=$selfurl?>joinProducts/<?=$order->order_id?>', queryString,
				function()
				{
					self.location.reload();
				}
			);
		}
	}

	function updateStatuses()
	{
		var queryString = $('#detailsForm').formSerialize(); 
		$.post('<?=$selfurl?>updateOdetailStatuses/', queryString, function(result) {
			self.location.reload();
		});
	}

	function removeJoint(id)
	{
		if (confirm("Отменить объединение общей доставки?"))
		{
			window.location.href = '<?=$selfurl?>removeOdetailJoint/<?=$order->order_id?>/'+id;
		}
	}

	function confirmUpdateCost()
	{
		return confirm("После нажатия кнопки Сохранить весь заказ будет обновлен и пересчитан по текущему курсу на главной странице. После этого сумма заказа может как увеличиться, так и уменьшиться.\n\nОб этом обязательно нужно предупредить клиента, написав в комментариях, либо любым другим способом.\n\nБез необходимости не нажимать на эту кнопку в оплаченном заказе.");
	}	

	function sendConfirmation()
	{
		if (confirm("Отправить клиенту уведомление о доставке заказа №<?=$order->order_id?>?"))
		{
			window.location.href = '<?=$selfurl?>sendOrderConfirmation/<?=$order->order_id?>';
		}
	}
	*/
	function unchooseBid()
	{
		$.post("/client/unchooseBid/<?= $order->order_id ?>")
			.success(function() { 
				success('top', 'Вы успешно отказались от работы с прошлым посредником.');
				$('.clientOrderInfo').hide('slow');
				$('.chooseBidForm,.choose_bid').show('slow');
				$('h3.bids_header').html('Предложения от посредников');
			})
			.error(function() { 
				error('top', 'Попробуйте еще раз.');
			});
	}
</script>