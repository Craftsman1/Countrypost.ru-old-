<form class='admin-inside' id='detailsForm' action='<?=$selfurl?>updateProductAjax' enctype="multipart/form-data" method="POST" style="display:none;">
	<input name="order_id" type="hidden" value=""/>
	<input id="odetail_id" name="odetail_id" type="hidden" value=""/>
	<h3>Ваш заказ:</h3>
	<div class='table'>
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<table id="new_products">
			<tr>
				<th nowrap style="width:1px;">
					№ <input type='checkbox' id='select_all' />
				</th>
				<th>Товар</th>
				<th>Скриншот</th>
				<th>Стоимость</th>
				<th>Местная доставка</th>
				<th>Примерный вес</th>
				<th style="width:1px;"></th>
			</tr>
			<? $order_products_cost = 0; 
				$order_delivery_cost = 0;
				$order_product_weight = 0;
				$odetail_joint_id = 0;
				$odetail_joint_count = 0;
				
			if ( ! empty($odetails)) : foreach($odetails as $odetail) : 
				$order_products_cost += $odetail->odetail_price;
				
				if (stripos($odetail->odetail_link, 'http://') !== 0)
				{
					$odetail->odetail_link = 'http://'.$odetail->odetail_link;
				}
				
				if (isset($odetail->odetail_img) && 
					stripos($odetail->odetail_img, 'http://') !== 0)
				{
					$odetail->odetail_img = 'http://'.$odetail->odetail_img;
				}
			?>
			<tr id='product<?=$odetail->odetail_id?>'>
				<script>
					var odetail<?=$odetail->odetail_id?> = {"odetail_id":"<?=$odetail->odetail_id?>","odetail_client":"<?=$odetail->odetail_client?>","odetail_manager":"<?=$odetail->odetail_manager?>","odetail_order":"<?=$odetail->odetail_order?>","odetail_link":"<?=$odetail->odetail_link?>","odetail_shop_name":"<?=$odetail->odetail_shop_name?>","odetail_product_name":"<?=$odetail->odetail_product_name?>","odetail_product_color":"<?=$odetail->odetail_product_color?>","odetail_product_size":"<?=$odetail->odetail_product_size?>","odetail_product_amount":"<?=$odetail->odetail_product_amount?>","odetail_img":"<?=$odetail->odetail_img?>"};
				</script>
				<td id='odetail_id<?=$odetail->odetail_id?>'><?=$odetail->odetail_id?></td>
				<td id='odetail_product_name<?=$odetail->odetail_id?>'><?=shortenText($odetail->odetail_product_name, $odetail->odetail_id)?></td>
				<td id='odetail_product_color<?=$odetail->odetail_id?>'><?=shortenText($odetail->odetail_product_color.' / '.$odetail->odetail_product_size.' / '.$odetail->odetail_product_amount, $odetail->odetail_id)?></td>
				<td id='odetail_img<?=$odetail->odetail_id?>'>
					<? if (isset($odetail->odetail_img)) : ?>
					<a href="#" onclick="window.open('<?=$odetail->odetail_img?>');return false;"><?=(strlen($odetail->odetail_img)>17?substr($odetail->odetail_img,0,17).'...':$odetail->odetail_img)?></a>
					<? else : ?>
					<a href="javascript:void(0)" onclick="setRel(<?=$odetail->odetail_id?>);">
						Просмотреть скриншот <a rel="lightbox_<?=$odetail->odetail_id?>" href="/client/showScreen/<?=$odetail->odetail_id?>" style="display:none;">Посмотреть</a>
					</a>
					<? endif; ?>
				</td>
				<td id='odetail_link<?=$odetail->odetail_id?>'><a href="#" onclick="window.open('<?=$odetail->odetail_link?>');return false;"><?=(strlen($odetail->odetail_link)>17?substr($odetail->odetail_link,0,17).'...':$odetail->odetail_link)?></a></td>
				<td id='odetail_status<?=$odetail->odetail_id?>'><?=$odetail->odetail_status_desc?>
					<? if (($order->order_status == 'sended' || $order->order_status == 'not_delivered') &&
							($odetail->odetail_status == 'available' || $odetail->odetail_status == 'sent')) : ?>
					<br />
					<input type="checkbox" value="<?=$odetail->odetail_id?>" name="odetail_status<?=$odetail->odetail_id?>"/>Не доставлен <img class="tooltip tooltip_rbk" src="/static/images/mini_help.gif">
					<? endif; ?>
				</td>
				<td id="odetail_price<?=$odetail->odetail_id?>"><?=$odetail->odetail_price?></td>
				<? if (!$odetail->odetail_joint_id) : 
					$order_delivery_cost += $odetail->odetail_pricedelivery;
				?>
				<td id="odetail_pricedelivery<?=$odetail->odetail_id?>">
					<?=$odetail->odetail_pricedelivery?>
				</td>
				<? elseif ($odetail_joint_id != $odetail->odetail_joint_id) :
						$odetail_joint_id = $odetail->odetail_joint_id;
						$odetail_joint_count = $odetail->odetail_joint_count;
						$order_delivery_cost += $odetail->odetail_joint_cost;
				?>
				<td rowspan="<?=$odetail_joint_count?>">
					<?=$odetail->odetail_joint_cost?>
				</td>
				<? endif; ?>
				<td align="center" id="odetail_action<?=$odetail->odetail_id?>">
					<a href="javascript:editItem(<?=$odetail->odetail_id?>)" id="odetail_edit<?=$odetail->odetail_id?>"><img border="0" src="/static/images/comment-edit.png" title="Редактировать"></a>
					<br />
					<a href="javascript:deleteItem(<?=$odetail->odetail_id?>)"><img border="0" src="/static/images/delete.png" title="Удалить"></a>
				</td>
			</tr>
			<? endforeach; endif; ?>
			<tr>
				<td colspan="3">&nbsp;</td>
				<td class="price_total product_total"><?= $order_products_cost ?></td>
				<td class="delivery_total product_total"><?= $order_delivery_cost ?></td>
				<td class="weight_total"><?= $order_product_weight ?></td>
				<td align="center">&nbsp;</td>
			</tr>
			<tr class='last-row'>
				<td colspan='4'>
					<div class='floatleft'>	
						<div class='submit'><div><input type='submit' value='Объединить доставку' /></div></div>
					</div>
					<img class="tooltip_join" src="/static/images/mini_help.gif" />
				</td>
				<td style="text-align: right;" colspan='3'>
					<br />
					<b>
						Итого: <b class="order_totals"></b>
						<br />
						Доставка в <span class='countryTo' style="float:none; display:inline; margin:0;"></span><span class='cityTo' style="float:none; display:inline; margin:0;"></span>: <b class="weight_total"></b>
					</b>
				</td>
			</tr>
		</table>			
	</div>
	<div style="height: 50px;" class="admin-inside">
		<div class="submit">
			<div>
				<input type="button" value="Готово" onclick="checkout();">
			</div>
		</div>
	</div>
</form>
<script>
	function deleteItem(item) {
		if (confirm("Вы уверены, что хотите удалить товар №" + item + "?"))
		{
			$('td#odetail' + item).parent().hide('slow').remove();
			updateTotals();
			$.post('<?= $selfurl ?>deleteProduct/' + item);
			
			if ($('#detailsForm tr').length < 4)
			{
				$('#detailsForm').hide('slow');
			}
			
			success('top', 'Товар успешно удален.');
		}
	}

	function editItem(id) {
		if (!$('#odetail_product_name' + id + ' textarea').length)
		{
			var odetail = eval('odetail' + id);
		
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
		if ($('#odetail_product_name' + id + ' textarea').length)
		{
			var odetail = eval('odetail' + id);
			
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
		if ($('#odetail_product_name' + id + ' textarea').length)
		{
			$('#odetail_product_name' + id).parent().find('input,textarea').attr('readonly', true);
			$('#odetail_action' + id).html('<img border="0" src="/static/images/lightbox-ico-loading.gif" title="Товар сохраняется..."><br><a href="javascript:cancelItem(' + id + ')" id="odetail_cancel' + id + '"><img border="0" src="/static/images/comment-delete.png" title="Отменить"></a>');
			$('#odetail_id').val(id);
			$('#detailsForm').submit();						
		}
	}
	
	function setRel(id){
		$("a[rel*='lightbox_"+id+"']").lightBox();
		var aa = $("a[rel*='lightbox_"+id+"']");
		$(aa[0]).click();
	}
	
	function addItem()
	{
		// читаем введенные данные
		var odetail = {
			"oname" : $('input#oname').val(),
			"olink" : $('input#olink').val(),
			"ocolor" : $('input#ocolor').val(),
			"osize" : $('input#osize').val(),
			"oamount" : $('input#oamount').val(),
			"oprice" : $('input#oprice').val(),
			"odeliveryprice" : $('input#odeliveryprice').val(),
			"oweight" : $('input#oweight').val(),
			"oimg" : ($('input#ofile').val() || $('input#oimg').val() == 'ссылка на скриншот' ? '' : $('input#oimg').val()),
			"ofile" : $('input#ofile').val(),
			"ocomment" : $('textarea#ocomment').val(),
			"foto_requested" : ($('input[name="foto_requested"]:checked')).length == 1
		};		
		
		// валидация
		var isValid = true;
		
		if (odetail['oname'] == '' ||
			odetail['olink'] == '' ||
			isNaN(parseInt(odetail['oprice'])) ||
			isNaN(parseInt(odetail['odeliveryprice'])) ||
			isNaN(parseInt(odetail['oweight'])))
		{
			isValid = false;
		}
		
		if ( ! isValid)
		{
			error('top', 'Товар не добавлен. Заполните все поля и попробуйте еще раз.');
			return;
		}
		
		// TODO: Дописать валидацию
		
		// шлем запрос
		$('#onlineOrderForm').submit();

		// рисуем новый товар
		var snippet = "<tr>" +
			"<td class=''><input type='checkbox' value='1' /><br /><img class='float product_progress_bar' src='/static/images/lightbox-ico-loading.gif'/></td>" +
			"<td class='oname'><a target='_blank' href='" + odetail['olink'] + "'>" + odetail['oname'] + "</a>" +
			(odetail['foto_requested'] == 1 ? " (требуется фото товара)" : "") +
			"<br/><b>Количество</b>: " + odetail['oamount'] + 
			" <b>Размер</b>: " + odetail['osize'] + 
			" <b>Цвет</b>: " + odetail['ocolor'] + 
			"<br/><b>Комментарий</b>: " + odetail['ocomment'] + "</td>" +
			"<td class='oimg" + (odetail['ofile'] ? " userfile" : "") + "'>" + odetail['oimg'] + "</td>" +
			"<td class='oprice'>" + odetail['oprice'] + " <span class='label currency'>" + getSelectedCurrency() + "</span></td>" +
			"<td class='odeliveryprice'>" + odetail['odeliveryprice'] + " <span class='label currency'>" + getSelectedCurrency() + "</span></td>" +
			"<td class='oweight'>" + odetail['oweight'] + " г</td>" +
			"<td class='oedit'><a href='javascript:editItem()'><img border='0' src='/static/images/comment-edit.png' title='Изменить'></a><br /><a class='delete_icon'><img border='0' src='/static/images/delete.png' style='cursor: pointer;' title='Удалить'></a></td>" +
			"</tr>";
		
		$('#new_products tr:first').after(snippet);
		
		// пересчитываем заказ
		updateTotals();
		$('#detailsForm').show();
	}
	
	$(function() {
		$('#onlineOrderForm').ajaxForm({
			target: $('#onlineOrderForm').attr('action'),
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
			},
			success: function(response)
			{
				$progress = $('img.product_progress_bar:last');
				$progress.hide();
				
				if (response)
				{
					if (isNaN(response))
					{
						error('top', response);
					}
					else
					{
						var $screenshot_code = "<a href='javascript:void(0)' onclick='setRel(" + response + ");'>Просмотреть <a rel='lightbox_" + response + "' href='/client/showScreen/" + response + "' style='display:none;'>Посмотреть</a></a>";						
						
						$progress
							.after(response)
							.parent()
							.attr('id', 'odetail' + response)
							.parent()
							.find('a.delete_icon')
							.click(function() {
								deleteItem(response);
							})
							.parent()
							.parent()
							.find('.userfile:last')
							.html($screenshot_code)
							.removeClass('userfile')
							;
						
						$progress.remove();
						
						success('top', 'Товар №' + response + ' успешно добавлен в корзину.');
		
						// чистим форму
						if (true) //debug only
						{
							$('input#oname').val('');
							$('input#olink').val('');
							$('input#ocolor').val('');
							$('input#osize').val('');
							$('input#oamount').val('1');
							$('input#oprice').val('');
							$('input#odeliveryprice').val('');
							$('input#oweight').val('');
							$('input#oimg').val('ссылка на скриншот');
							$('input#ofile').val('');
							$('textarea#ocomment').val('');
							$('input[name="foto_requested"]').removeAttr('checked');
						}//debug only
					}
				}
				else
				{
					error('top', 'Товар не добавлен. Заполните все поля и попробуйте еще раз.');
				}
			},
			error: function(response)
			{
				$progress = $('img.product_progress_bar:last');
				$progress.hide();

				error('top', 'Товар не добавлен. Заполните все поля и попробуйте еще раз.');
			}
		});
	});
		
	function updateTotals()
	{
		updateTotalGeneric('oprice', 'price_total', getSelectedCurrency());
		updateTotalGeneric('odeliveryprice', 'delivery_total', getSelectedCurrency());
		updateTotalGeneric('product_total', 'order_totals', getSelectedCurrency());
		updateTotalGeneric('oweight', 'weight_total', 'г');
		
		$('span.countryTo').html(countryTo);
		cityTo = $.trim($("input#city_to").val());
		
		if (cityTo)
		{
			$('span.cityTo').html(" (город: " + cityTo + ")");
		}
		else
		{
			$('span.cityTo').html("");
		}
	}
	
	function updateTotalGeneric(column, result, measure)
	{
		var total = 0;
		
		$('.' + column).each(function() {
			total += parseFloat($(this).html());
		});
		
		$('.' + result).html(total + ' ' + measure);
	}
	
	function getSelectedCurrency()
	{
		return selectedCurrency;
	}
</script>