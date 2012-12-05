<?
$is_own_order = ($this->user->user_id == $order->order_client);
$is_editable = $is_own_order && ($order->order_status == 'pending'); ?>
<div class='table centered_td centered_th'>
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<table id="new_products">
		<colgroup>
			<col style="width: 60px;">
			<col>
			<col>
			<col style="width: 85px;">
			<col style="width: 85px;">
			<col style="width: 85px;">
			<? if ( ! $is_editable AND $is_own_order) : ?>
			<col style="width: 169px;">
			<? endif; ?>
			<col style="width: 44px">
		</colgroup>
		<tr>
			<th nowrap>
				№ <input type='checkbox' id='select_all'>
			</th>
			<th>Товар</th>
			<th>Скриншот</th>
			<? if ($order->order_type != 'mail_forwarding') : ?>
			<th>
				Стоимость
			</th>
			<th>
				Местная<br>доставка
			</th>
			<th>
				Вес<br>товара
			</th>
			<? else : ?>
			<th>Tracking №</th>
			<? endif; ?>
			<? if ($order->order_status != 'pending') : ?>
			<th>Статус</th>
			<? endif; ?>
			<? if ($is_editable) : ?>
			<th style="width:1px;"></th>
			<? endif; ?>
		</tr>
		<?
		$odetail_joint_id = 0;
		$odetail_joint_count = 0;

		if ( ! empty($odetails)) : foreach($odetails as $odetail) :
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
		<tr id='product<?= $odetail->odetail_id ?>'>
			<td id='odetail_id<?= $odetail->odetail_id ?>'>
				<?= $odetail->odetail_id ?>
				<? if ($is_editable) : ?>
				<br>
				<img id="progress<?= $odetail->odetail_id ?>"
					 class="float"
					 style="display:none;"
					 src="/static/images/lightbox-ico-loading.gif"/>
				<? endif; ?>
			</td>
			<? if ($is_editable) : ?>
			<form action='<?= $selfurl ?>updateProduct/<?= $order->order_id ?>/<?= $odetail->odetail_id ?>'
				  enctype="multipart/form-data"
				  method="POST">
			<? endif; ?>
			<td style="text-align: left; vertical-align: bottom;">
				<span class="plaintext">
					<a target="_blank" href="<?= $odetail->odetail_link ?>"><?= $odetail->odetail_product_name ?></a>
					<? if ($odetail->odetail_foto_requested) : ?>(требуется фото товара)<? endif; ?>
					<br>
					<b>Количество</b>: <?= $odetail->odetail_product_amount ?>
					<b>Размер</b>: <?= $odetail->odetail_product_size ?>
					<b>Цвет</b>: <?= $odetail->odetail_product_color ?>
					<br>
					<b>Комментарий</b>: <?= $odetail->odetail_comment ?>
				</span>
				<? if ($is_editable) : ?>
				<script>
					var odetail<?= $odetail->odetail_id ?> = {
						"link":"<?= $odetail->odetail_link ?>",
						"name":"<?= $odetail->odetail_product_name ?>",
						"color":"<?= $odetail->odetail_product_color ?>",
						"size":"<?= $odetail->odetail_product_size ?>",
						"amount":"<?= $odetail->odetail_product_amount ?>",
						"comment":"<?= $odetail->odetail_comment ?>",
						"img":"<?= $odetail->odetail_img ?>",
						"img_file":"",
						"img_selector":"<?= isset($odetail->odetail_img) ? 'link' : 'file' ?>",
						"foto_requested":"<?= $odetail->odetail_foto_requested ?>",
						"is_editing":0
					};

					$(function() {
						$('tr#product<?= $odetail->odetail_id ?> form').ajaxForm({
							dataType: 'json',
							iframe: true,
							beforeSubmit: function()
							{
								$('img#progress<?= $odetail->odetail_id ?>').show();
							},
							error: function()
							{
								error('top', 'Описание товара №<?= $odetail->odetail_id ?> не сохранено.');
							},
							success: function(data) {
								$('img#progress<?= $odetail->odetail_id ?>').hide();

								submitItem(<?= $odetail->odetail_id ?>, data);
							}
						});
					});
				</script>
				<span class="producteditor" style="display: none;">
					<br>
					<b>Ссылка</b>:
					<textarea class="link" name="link"></textarea>
					<br>
					<b>Наименование</b>:
					<textarea class="name" name="name"></textarea>
					<br>
					<b>Количество</b>:
					<textarea class="amount int" name="amount"></textarea>
					<br>
					<b>Размер</b>:
					<textarea class="size" name="size"></textarea>
					<br>
					<b>Цвет</b>:
					<textarea class="color" name="color"></textarea>
					<br>
					<b>Комментарий</b>:
					<textarea class="ocomment" name="comment"></textarea>
					<br>
				</span>
				<? endif; ?>
			</td>
			<td>
				<span class="plaintext">
					<? if (isset($odetail->odetail_img)) : ?>
					<a target="_blank" href="<?= $odetail->odetail_img ?>"><?=
						(strlen($odetail->odetail_img) > 17 ?
							substr($odetail->odetail_img, 0, 17) . '...' :
							$odetail->odetail_img) ?></a>
					<? else : ?>
					<a href="javascript:void(0)" onclick="setRel(<?= $odetail->odetail_id ?>);">
						<img src='/client/showScreen/<?= $odetail->odetail_id ?>' width="55px" height="55px">
						<a rel="lightbox_<?= $odetail->odetail_id ?>" href="/client/showScreen/<?=
							$odetail->odetail_id ?>" style="display:none;">Посмотреть</a>
					</a>
					<? endif; ?>
				</span>
				<? if ($is_editable) : ?>
				<span class="producteditor" style="display: none;">
					<input type="radio" name="img_selector" class="img_selector" value="link">
					<textarea class="image" name="img"></textarea>
					<br>
					<input type="radio" name="img_selector" class="img_selector" value="file">
					<input type="file" class="img_file" name="userfile">
				</span>
				<? endif; ?>
			</td>
			<? if ($is_editable) : ?>
			</form>
			<? endif; ?>
			<td>
				<? if ($is_editable) : ?>
				<input type="text"
					   id="odetail_price<?= $odetail->odetail_id ?>"
					   name="odetail_price<?= $odetail->odetail_id ?>"
					   class="int"
					   value="<?= $odetail->odetail_price ?>"
					   style="width:60px"
					   maxlength="11"
					   onchange="update_odetail_price('<?= $order->order_id ?>',
							   '<?= $odetail->odetail_id ?>');">
				<? else : ?>
				<?= $odetail->odetail_price ?> <?= $order->order_currency ?>
				<? endif; ?>
			</td>
			<td>
				<? if ($is_editable) : ?>
				<input type="text"
					   id="odetail_pricedelivery<?= $odetail->odetail_id ?>"
					   name="odetail_price<?= $odetail->odetail_id ?>"
					   class="int"
					   value="<?= $odetail->odetail_pricedelivery ?>"
					   style="width:60px"
					   maxlength="11"
					   onchange="update_odetail_pricedelivery('<?= $order->order_id ?>',
							   '<?= $odetail->odetail_id ?>');">
				<? else : ?>
				<?= $odetail->odetail_pricedelivery ?> <?= $order->order_currency ?>
				<? endif; ?>
			</td>
			<? //if (!$odetail->odetail_joint_id) :
			//$order_delivery_cost += $odetail->odetail_pricedelivery;
			?>
			<td>
				<? if ($is_editable) : ?>
				<input type="text"
					   id="odetail_weight<?= $odetail->odetail_id ?>"
					   name="odetail_weight<?= $odetail->odetail_id ?>"
					   class="int"
					   value="<?= $odetail->odetail_weight ?>"
					   style="width:60px"
					   maxlength="11"
					   onchange="update_odetail_weight('<?= $order->order_id ?>', '<?= $odetail->odetail_id ?>')
							   ;">
				<? else : ?>
				<?= $odetail->odetail_weight ?> г
				<? endif; ?>
			</td>
			<?// elseif ($odetail_joint_id != $odetail->odetail_joint_id) :
			//	$odetail_joint_id = $odetail->odetail_joint_id;
			//	$odetail_joint_count = $odetail->odetail_joint_count;
			//	$order_delivery_cost += $odetail->odetail_joint_cost;
			?>
			<!--td rowspan="<?= $odetail_joint_count ?>">
				<?//=$odetail->odetail_joint_cost ?>
			</td-->
			<? //endif; ?>
			<? if ( ! $is_editable AND $is_own_order) : ?>
			<td>
				<?= $odetail_statuses[$order->order_type][$odetail->odetail_status] ?>
			</td>
			<? endif; ?>
			<? if ($is_editable) : ?>
			<td>
				<a href="javascript:editItem(<?= $odetail->odetail_id ?>)"
				   class="edit">
					<img border="0" src="/static/images/comment-edit.png" title="Редактировать"></a>
				<br>
				<a href="javascript:cancelItem(<?= $odetail->odetail_id ?>)"
				   class="cancel"
				   style="display: none;">
					<img border="0" src="/static/images/comment-delete.png" title="Отменить"></a>
				<br>
				<a href="javascript:saveItem(<?= $odetail->odetail_id ?>)"
				   class="save"
				   style="display: none;">
					<img border="0" src="/static/images/done-filed.png" title="Сохранить"></a>
			</td>
			<? endif; ?>
		</tr>
			<? endforeach; endif; ?>
		<tr>
			<td colspan="3">&nbsp;</td>
			<td class="price_total product_total">
				<b class="total_product_cost"><?= $order->order_products_cost ?></b>&nbsp;<?=
				$order->order_currency ?>
			</td>
			<td class="delivery_total product_total">
				<b class="total_delivery_cost"><?= $order->order_delivery_cost ?></b>&nbsp;<?= $order->order_currency ?>
			</td>
			<td class="weight_total">
				<b class="total_weight"><?= $order->order_weight ?></b> г
			</td>
			<? if ($is_own_order) : ?>
			<td>&nbsp;</td>
			<? endif; ?>
		</tr>
	</table>
</div>
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