<? View::show($viewpath.'elements/div_float_upload_pdetail') ?>
<script type="text/javascript" src="/static/js/easyTooltip.js"></script>
<script type="text/javascript" src="/static/js/jquery-ui.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$("img.tooltip").easyTooltip();
		$("img.tooltip_rbk").easyTooltip({
			tooltipId: "tooltip_id",
			content: '\
				<div class="box">\
					<p>Если в течении 7 дней этот товар не был доставлен и не появился в разделе "Посылки ожидающие отправки" поставьте тут галочку и нажмите сохранить.</p>\
				</div>\
			'
		});
	});
	
	function setstatusundelivered(o)
	{
		if(o.checked)
		{
			$.get("/client/setStatusUndelivered/" + o.value, { pdetail_id: o.value}, function(data){
			});
		}
	}

</script>
<div class='content'>
	<h2>Посылка №<?=$package->package_id?><?=($package->order_id>0?" (<a href=/{$this->user->user_group}/showOrderDetails/{$package->order_id}>заказ №{$package->order_id}</a>)":""); ?></h2>
	<div class='back'>
		<a class='back' href='<?= $selfurl ?><?= $back_handler ?>'><span>Назад к списку</span></a>
	</div><br />	
		
	<form class='admin-inside' id='packageForm' action="<?= $selfurl ?>updatePackageDetails" method="POST">
		<input type=hidden name=package_id value="<?=$package->package_id?>" />
		<div class='table'>
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<table>
				<? if (empty($package->order_id)) : ?>
				<tr>
					<th colspan='6'>
						Номер отслеживания (Tracking №):
						<input type='text' name='trackingno' value="<?= $package->package_trackingno ?>" />
					</th>
				</tr>
				<? endif; ?>
				<tr>
					<th>Страна / Вес</th>
					<th>Объединена с посылками</th>
					<th>Декларация</th>
					<th>Цена</th>
					<th>Дополнительные услуги или платежи</th>
					<th>Статус</th>
				</tr>
				<tr>
					<td><?="{$package_country->country_name}<br /><b>{$package->package_weight} кг</b><br/>";?></td>
					<td><?=$package->package_join_ids?@ereg_replace("([0-9]+)","<a href=/admin/showPackageDetails/\\1>\\1</a>",'{'.str_replace('+', ' + ', $package->package_join_ids).'}'):''?></td>
					<td><? if ($package->declaration_status == 'not_completed') : ?>
										Не заполнена
									<? elseif ($package->declaration_status == 'completed') : ?>
										Заполнена
									<? else : ?>
										Помощь партнера
									<? endif; ?>
									<br />
									<? if ($package->declaration_status == 'not_completed') : ?>
										<a href="<?= $selfurl ?>showDeclaration/<?=$package->package_id?>">Заполнить</a>
									<? else : ?>
										<a href="<?= $selfurl ?>showDeclaration/<?=$package->package_id?>">Посмотреть</a>
									<? endif; ?>
					</td>
					<td>
						<? if ( ! $package->package_delivery_cost OR
							$package->declaration_status == 'not_completed') : ?>
							Неизвестно
						<? else : ?>
						$<?= $package->package_cost ?>
						<br />
						<a href="javascript:void(0)" onclick="$('#pre_<?=$package->package_id?>').toggle();">Подробнее</a>
						<pre class="pre-href" id="pre_<?= $package->package_id ?>">
							<nobr>
								$<?= $package->package_delivery_cost ?><!-- стоимость доставки -->
								<img class="" src="/static/images/mini_help.gif" />
							</nobr>
							+
							<nobr>
								$<?= $package->package_comission ?><!-- комиссия за пересылку -->
								<img class="" src="/static/images/mini_help.gif" />
							</nobr>
							<? if ($package->package_join_cost) : ?>
							+
							<nobr>
								$<?= $package->package_join_cost ?><!-- комиссия за объединение посылок -->
								<img class="" src="/static/images/mini_help.gif" />
							</nobr>
							<? endif; ?>
							<? if ($package->package_declaration_cost) : ?>
							+
							<nobr>
								$<?= $package->package_declaration_cost ?><!-- комиссия за декларацию -->
								<img class="" src="/static/images/mini_help.gif" />
							</nobr>
							<? endif; ?>
							<? if ($package->package_insurance_comission) : ?>
							+
							<nobr>
								$<?= $package->package_insurance_comission ?><!-- комиссия за страховку -->
								<img class="" src="/static/images/mini_help.gif" />
							</nobr>
							<? endif; ?>
							<? if ($package->package_foto_cost) : ?>
							+
							<nobr>
								$<?= $package->package_foto_cost ?><!-- комиссия за запросы фото от клиента -->
								<img class="" src="/static/images/mini_help.gif" />
							</nobr>
							<? endif; ?>
							<? if ($package->package_foto_cost_system) : ?>
							+
							<nobr>
								$<?= $package->package_foto_cost_system ?><!-- комиссия за фото от партнера/админа -->
								<img class="" src="/static/images/mini_help.gif" />
							</nobr>
							<? endif; ?>
							<? if ($package->package_special_cost_usd) : ?>
							+
							<nobr>
								$<?= $package->package_special_cost_usd ?><!-- комиссия за доп.услуги -->
								<img class="" src="/static/images/mini_help.gif" />
							</nobr>
							<? endif; ?>
						</pre>
						<? endif; ?>
					</td>
					<td width="250px">
						<textarea name='special_comment' style='resize:both;width:250px;' <? if ($this->user->user_group == 'client') : ?>disabled<? endif; ?>><?= $package->package_special_comment ?></textarea>
						<br/>
						<span>
							<b>Итого: </b>
							<input type='text' name='special_cost' value='<?= $package->package_special_cost ?>' />
							($<?= $package->package_special_cost_usd ?>)
						</span>
					</td>
					<td class="package_status"> 
						<?= $package->package_status == 'deleted' ? 'Удалена' : $package_statuses[$package->package_status] ?>
					</td>
				</tr>
				<tr class='last-row'>
					<td colspan='6'>
						<div class='submit float'>
							<div>
								<input type='button' value='Сохранить' onclick="return confirmUpdateCost();" />
							</div>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</form>
	<h3>Товары в посылке:</h3>
    <? View::show($viewpath.'elements/div_float_manual_p', array(
		'updatePriceNotification' => false,
		'package_id'=>$package->package_id)); ?>	
	<? View::show($viewpath.'elements/div_package_item_preview'); ?>	
    <div style="height: 50px;" class="admin-inside">
		<div class="submit">
			<div>
				<input type="button" value="Добавить товар" name="add" onclick="lay2()" />
			</div>
		</div>
		<div class="submit">
			<div>
				<a href="<?= $selfurl ?>exportPackage/<?= $package->package_id ?>" style="text-decoration:none;">
					<input type="button" value="Экспорт">
				</a>
			</div>
		</div>
	</div>
	<form class='admin-inside' id='detailsForm' action='<?= $selfurl ?>updateProductAjaxP' enctype="multipart/form-data" method="POST">
		<input name="package_id" type="hidden" value="<?=$package->package_id?>" />
		<input id="pdetail_id" name="pdetail_id" type="hidden" value="" />
		<div class='table'>
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<table>
				<tr>
					<th nowrap>
						№ <input type='checkbox' id='select_all' />
					</th>
					<th>Наименование</th>
					<th>Цвет / Размер / Кол-во</th>
					<th>Скриншот</th>
					<th>Ссылка на товар</th>
					<? if ( ! $package->order_id) : ?>
					<th>Статус</th>
					<? endif; ?>
                    <th>Доп. опции</th>
                    <th>Фото товара</th>
					<th>Изменить / Удалить</th>
				</tr>
				<? $package_products_cost = 0; 
					$package_delivery_cost = 0; 
					$pdetail_joint_id = 0;
					$pdetail_joint_count = 0;
					$pdetail_foto_count = 0;
					
				if ($pdetails) : 
					foreach($pdetails as $pdetail) : 
						$package_products_cost += $pdetail->pdetail_price;
						
						if (stripos($pdetail->pdetail_link, 'http://') !== 0)
						{
							$pdetail->pdetail_link = 'http://'.$pdetail->pdetail_link;
						}
						
						if (isset($pdetail->pdetail_img) AND 
							stripos($pdetail->pdetail_img, 'http://') !== 0)
						{
							$pdetail->pdetail_img = 'http://'.$pdetail->pdetail_img;
						}
						
						if ($pdetail->pdetail_foto_request AND
							empty($pdetail->pdetail_joint_id))
						{
							$pdetail_foto_count++;
						}
				?>
				<tr id='product<?=$pdetail->pdetail_id?>'>
					<script>
						var pdetail<?=$pdetail->pdetail_id?> = {"pdetail_id":"<?=$pdetail->pdetail_id?>","pdetail_client":"<?=$pdetail->pdetail_client?>","pdetail_manager":"<?=$pdetail->pdetail_manager?>","pdetail_package":"<?=$pdetail->pdetail_package?>","pdetail_link":"<?=$pdetail->pdetail_link?>","pdetail_shop_name":"<?=$pdetail->pdetail_shop_name?>","pdetail_product_name":"<?=$pdetail->pdetail_product_name?>","pdetail_product_color":"<?=$pdetail->pdetail_product_color?>","pdetail_product_size":"<?=$pdetail->pdetail_product_size?>","pdetail_product_amount":"<?=$pdetail->pdetail_product_amount?>","pdetail_img":"<?=$pdetail->pdetail_img?>"};
	                </script>
					<td id='pdetail_id<?=$pdetail->pdetail_id?>'>
						<input type='checkbox' class='move_checkbox join_checkbox' name='pdetail_id<?=$pdetail->pdetail_id?>' value='1' />
						<br />
						<? if ($package->order_id AND $pdetail->odetail_id) : ?>
						<?= $pdetail->odetail_id ?>
						(<?= $pdetail->pdetail_id ?>)
						<? else : ?>
						<?= $pdetail->pdetail_id ?>
						<? endif; ?>
					</td>
					<td id='pdetail_product_name<?=$pdetail->pdetail_id?>'><?=shortenText($pdetail->pdetail_product_name, $pdetail->pdetail_id)?></td>
					<td id='pdetail_product_color<?=$pdetail->pdetail_id?>'><?=shortenText($pdetail->pdetail_product_color.' / '.$pdetail->pdetail_product_size.' / '.$pdetail->pdetail_product_amount, $pdetail->pdetail_id)?></td>
					<td id='pdetail_img<?=$pdetail->pdetail_id?>'>
						<? if (isset($pdetail->pdetail_img)) : ?>
						<a href="#" onclick="window.open('<?=$pdetail->pdetail_img?>');return false;"><?=(strlen($pdetail->pdetail_img)>17?substr($pdetail->pdetail_img,0,17).'...':$pdetail->pdetail_img)?></a>
						<? else : ?>
						<a href="javascript:void(0)" onclick="setRel('<?=$pdetail->pdetail_id?>_screenshot');">
	                        Просмотреть скриншот <a rel="lightbox_<?=$pdetail->pdetail_id?>_screenshot" href="/admin/showPdetailScreenshot/<?=$pdetail->pdetail_id?>" style="display:none;">Посмотреть</a>
						</a>
						<? endif; ?>
					</td>
					<td id='pdetail_link<?=$pdetail->pdetail_id?>'>
						<a href="#" onclick="window.open('<?=$pdetail->pdetail_link?>');return false;"><?=(strlen($pdetail->pdetail_link)>17?substr($pdetail->pdetail_link,0,17).'...':$pdetail->pdetail_link)?>
						</a>
					</td>
					<? if ( ! $package->order_id) : ?>
					<td id='pdetail_status<?=$pdetail->pdetail_id?>'>
						<select class="select" name="pdetail_status<?=$pdetail->pdetail_id?>">
						<?
						foreach ($pdetails_statuses as $key => $val)
						{
								?><option value="<?= $key ?>" <? if ($pdetail->pdetail_status == $key) : ?>selected <? endif; ?>><?= $val ?></option><?
						}
						?>
						</select>
					</td>
					<? endif; ?>
                    <td id='pdetail_special'>Убрать:<br/>
                    	<nobr>
							<input type='checkbox' name='pdetail_special_boxes<?=$pdetail->pdetail_id?>' <?=$pdetail->pdetail_special_boxes?"checked":"";?> value="1" /> 
							коробки
						</nobr>
						<br/>
                    	<nobr>
							<input type='checkbox' name='pdetail_special_invoices<?=$pdetail->pdetail_id?>' <?=$pdetail->pdetail_special_invoices?"checked":"";?> value="1" /> 
							инвойсы
						</nobr>
                    </td>
					<? if ( ! empty($pdetail->pdetail_joint_id)) :
							if ($pdetail->pdetail_joint_id != $pdetail_joint_id) : 
							$pdetail_joint_id = $pdetail->pdetail_joint_id; 
					?>
					<td align='center' rowspan="<?= $pdetail->joint_count ?>">
						<? if ($pdetail->joint_foto_request) : $pdetail_foto_count++; ?>
						<nobr>
							заказано фото
						</nobr>
						<br />
						<? endif; ?>
						<? if (isset($jointFotos[$pdetail->pdetail_joint_id])) : ?>
						<a href="javascript:void(0)" onclick="setRel('<?=$pdetail->pdetail_joint_id?>_joint_foto')" >Посмотреть (<?= count($jointFotos[$pdetail->pdetail_joint_id]) ?> фото)<? foreach ($jointFotos[$pdetail->pdetail_joint_id] as $jointFoto) : ?><a rel="lightbox_<?= $pdetail->pdetail_joint_id ?>_joint_foto" href="/admin/showPdetailJointFoto/<?= $pdetail->pdetail_package ?>/<?= $pdetail->pdetail_joint_id ?>/<?= $jointFoto ?>" style="display:none">Посмотреть</a><? endforeach; ?></a>
						<? endif; ?>
						<a href="#" onclick="return uploadPdetailJointFoto(<?= $pdetail->pdetail_joint_id ?>);" >Добавить</a>
						<? if ($pdetail->joint_count > 1) : ?>
						<br />
						<br />
						<nobr>
							<a href="/admin/deletePdetailJoint/<?= $package->package_id ?>/<?= $pdetail->pdetail_joint_id ?>">отменить<br />объединение</a>
						</nobr>
						<? endif; ?>
					</td>
					<? endif; else : ?>					
					<td align='center'>
						<? if ($pdetail->pdetail_foto_request) : ?>
						<nobr>
							заказано фото
						</nobr>
						<br />
						<? endif; ?>
						<? if (isset($packFotos[$pdetail->pdetail_id])): ?>
						<a href="javascript:void(0)" onclick="setRel('<?=$pdetail->pdetail_id?>_foto')" >Посмотреть (<?= count($packFotos[$pdetail->pdetail_id]) ?> фото)<? foreach ($packFotos[$pdetail->pdetail_id] as $packFoto) : ?><a rel="lightbox_<?= $pdetail->pdetail_id ?>_foto" href="/admin/showPdetailFoto/<?= $pdetail->pdetail_package ?>/<?= $pdetail->pdetail_id ?>/<?=$packFoto?>" style="display:none">Посмотреть</a><? endforeach; ?></a>
						<br />
						<? endif; ?>
						<a href="#" onclick="return uploadPdetailFoto(<?= $pdetail->pdetail_id ?>);" >Добавить</a>
					</td>
					<? endif; ?>
					<td align="center" id="pdetail_action<?=$pdetail->pdetail_id?>">
						<a href="javascript:editItem(<?=$pdetail->pdetail_id?>)" id="pdetail_edit<?=$pdetail->pdetail_id?>"><img border="0" src="/static/images/comment-edit.png" title="Редактировать"></a>
						<br />
						<a href="javascript:deleteItem(<?=$pdetail->pdetail_id?>)"><img border="0" src="/static/images/delete.png" title="Удалить"></a>
					</td>
				</tr>
				<? endforeach; ?>
				<tr>
					<td colspan="<?= $package->order_id ? '6' : '7' ?>">&nbsp;</td>
					<td align="center">
						<? if ($pdetail_foto_count) : ?>
						<nobr>
							Заказано: <?= $pdetail_foto_count ?> фото
						</nobr>
						<br />
						<b>
							$<?= $package->package_foto_cost ?>
						</b>
						<? else : ?>
						<nobr>
							Фото не заказаны
						</nobr>
						<? endif; ?>
					</td>
					<td>&nbsp;</td>
				</tr>
				<? endif; ?>
				<? if ($pdetails) : ?>
                <tr class='last-row'>
					<td colspan='7'>
						<div class="float-left" style="margin-top:11px;margin-right:10px;">
							<label>
								Перенести товары в посылку:
							</label>
						</div>
						<div class="float-left" style="margin-top:11px;margin-right:10px;">
							<input id="package_to" class='float-left' name="package_to" type="text" style="width:60px;" onkeypress="javascript:validate_number(event);" >
						</div>
						<div class='float-left'>	
							<div class='submit'><div><input type='button' value='Перенести' onclick='moveItems();'/></div></div>
						</div>
						<? if ($pdetails) : ?>
						<div class='floatleft'>	
							<div class='submit'><div><input type='button' value='Объединить фото' style='width:124px;' id='join_button' /></div></div>
						</div>
						<img class="tooltip_package_foto_join" src="/static/images/mini_help.gif" />
						<? endif; ?>
					</td>
					<td colspan='2'>					
						<div class='float'>	
							<div class='submit'><div><input type='button' value='Сохранить' onclick='updateStatuses();'/></div></div>
						</div>
					</td>
				</tr>
				<? endif; ?>
			</table>
		</div>
	</form>
	<a name="comments"></a>
	<h3>Комментарии к посылке</h3>
	<br />
	<form id="commentForm" class='comments' action='<?= $selfurl ?>addPackageComment/<?=$package->package_id?>' method='POST'>
		<? if ( ! $comments) : ?>
			<div class='comment'>
				Пока нет комментариев<br/>
			</div>
		<? else : ?>
			<? foreach ($comments as $comment) : ?>
				<div class='comment'>
					<div class='question'>
						<span class="name">№<?= $comment->pcomment_id ?>
						<?if ($comment->pcomment_user == $package->package_client):?>
							Клиент:
						<?elseif ($comment->pcomment_user == $package->package_manager):?>
							Менеджер:
						<?else:?>
							Вы:
						<?endif;?>
							<br /><?=formatCommentDate($comment->pcomment_time)?>
						</span>
						<div id="comment_<?= $comment->pcomment_id?>">
							<?=html_entity_decode($comment->pcomment_comment) ?>
						</div>
						
							<a href="javascript:updateItem(<?= $comment->pcomment_id?>, '№<?= $comment->pcomment_id ?> <? if ($comment->pcomment_user == $package->package_manager) : ?>Менеджер<? elseif ($comment->pcomment_user == $package->package_client) : ?>Клиент<? else : ?>Вы<? endif; ?>:', <?= $comment->pcomment_id?>);">Изменить</a>
							<a href="javascript:deleteComment(<?= $comment->pcomment_id?>);"><img border="0" src="/static/images/delete.png" title="Удалить"></a>
							<br /><br />
					</div>
				</div>
			<? endforeach; ?>
		<? endif; ?>
		<a name="edit_comment_area" />
		<script type='text/javascript' src='/system/plugins/fckeditor/fckeditor.js'></script>
		<h3 class="update" style="display:none;">Редактирование комментария</h3>
		<div class='comment update' style="border:0;">			
			<div class='question update' style="display:none;">
				<span id="comment_user" class="name">
				</span>
			</div>
		</div>
		<br class="update" style="display:none;"/>
		<div class='add-comment update' style="display:none;">
			<input type='hidden' id='comment_id' name='comment_id' />
			<div><textarea id='comment_update' name='comment_update'></textarea></div>
			<div class='submit comment-submit'><div><input type='submit' id="update" name="update" value="Сохранить" onclick="editComment();"/></div></div>
			<br />
		</div>
		<div class="back update" style="display:none;">
			<a href="#" onclick="cancel();" class="back"><span>Отмена</span></a>
		</div>
	
		<h3 class='save'>Оставьте комментарий:</h3>
		<div class='add-comment save'>
			<div><textarea id='comment' name='comment'></textarea></div>
			<div class='submit comment-submit'><div><input type='submit' name="add" value="Добавить" /></div></div>
		</div>
	</form>
</div>
<script type="text/javascript">
	function confirmUpdateCost()
	{
		if(confirm("После нажатия кнопки Сохранить посылка будет обновлена и пересчитана!\n\nБез необходимости не нажимать на эту кнопку.")) {
			var queryString = $('#packageForm').formSerialize(); 
			$.post('<?= $selfurl ?>updatePackageDetails/', queryString, function(result) {
				self.location.reload();
			});
			
		}	
		return false;
	}

	function deleteItem(id) 
	{
		if (confirm("Вы уверены, что хотите удалить товар № " + id + " ?")){
			window.location.href = '<?= $selfurl ?>deleteProductP/' + id;
		}
	}	

	$(function() {
		$('#detailsForm').ajaxForm({
			target: '<?= $selfurl ?>updateProductAjaxP/',
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

		$('#package_to')
			.keypress(function(event){validate_number(event);})
			.autocomplete({
				source: function( request, response ) {
					$.ajax({
						url: '<?= $selfurl ?>autocompletePackage/' + request.term,
						success: function(data) {
							response($.map(eval(data), function( item ) {
								return {
									label: item,
									value: item
								}
							}));
						}
					});
				},
				minLength: 1
			});
			
		$('#select_all').change(function() {
			$('input.move_checkbox').attr('checked', $(this).attr('checked'));
		});

		put_package_hints();
		
		$('#join_button').click(function() {
			joinItems();
		});
	});

	function joinItems()
	{
		var selectedItems = $('#detailsForm input.join_checkbox:checked');
		
		if (selectedItems.length < 2)
		{
			alert("Выберите хотя бы 2 товара для объединения фото.");
			return false;			
		}
		
		if (confirm("Вы уверены, что хотите объединить фото выбранных товаров?"))
		{
			var queryString = $('#detailsForm').formSerialize(); 
			$.post('<?= $selfurl ?>joinPackageFotos', queryString, function(result) {
				self.location.reload();
			});
		}
	}

	function editItem(id) 
	{
		if (!$('#pdetail_product_name' + id + ' textarea').length)
		{
			var pdetail = eval('pdetail' + id);

			$('#pdetail_product_name' + id)
				.html('<textarea name="oname' + id + '" style="width:75px;resize:auto;">' + pdetail['pdetail_product_name'] + '</textarea>');

			$('#pdetail_product_color' + id)
				.html('')
				.append('<textarea name="ocolor' + id + '" style="width:52px;height:14px;resize:auto;">' + pdetail['pdetail_product_color'] + '</textarea><br />')
				.append('<textarea name="osize' + id + '" style="width:52px;height:14px;resize:auto;">' + pdetail['pdetail_product_size'] + '</textarea><br />')
				.append('<textarea name="oamount' + id + '" style="width:52px;height:14px;resize:auto;">' + pdetail['pdetail_product_amount'] + '</textarea>');

			$('#pdetail_link' + id)
				.children().hide().parent()
				.append('<textarea name="olink' + id + '" style="width:75px;resize:auto;">' + pdetail['pdetail_link'] + '</textarea>');

			$('#pdetail_img' + id)
				.children().hide().parent()
				.append('<div style="width:120px;"><input type="radio" value="1" id="img' + id + '" name="img' + id + '" ><label for="img' + id + '"><textarea id="userfileimg' + id + '" type="text" name="userfileimg' + id + '" maxlength="4096" style="top:0;height:14px;width:92px;resize:auto;">' + pdetail['pdetail_img'] + '</textarea></label><br><input type="radio" value="2" id="img2' + id + '" name="img' + id + '" ><label for="img2' + id + '"><input id="userfile' + id + '" type="file" name="userfile" style="width:95px;"></label></div>');
			$('input[name="pdetail_special_boxes'+id+'"]').enable();	
			$('input[name="pdetail_special_invoices'+id+'"]').enable();
			$('#pdetail_edit' + id).remove();
			$('#pdetail_action' + id + ' br').remove();
				
			$('#pdetail_action' + id)
				.prepend('<a href="javascript:cancelItem(' + id + ')" id="pdetail_cancel' + id + '"><img border="0" src="/static/images/comment-delete.png" title="Отменить"></a><br /><a href="javascript:saveItem(' + id + ')" id="pdetail_save' + id + '"><img border="0" src="/static/images/done-filed.png" title="Сохранить"></a><br />');
		}
		else
		{
			cancelItem(id);
		}
	}

	function cancelItem(id) 
	{
		if ($('#pdetail_product_name' + id + ' textarea').length)
		{
			var pdetail = eval('pdetail' + id);

			$('#pdetail_product_name' + id).html(pdetail['pdetail_product_name']);
			$('#pdetail_product_color' + id).html(pdetail['pdetail_product_color'] + ' / ' + pdetail['pdetail_product_size'] + ' / ' + pdetail['pdetail_product_amount']);

			$('#pdetail_link' + id + ',#pdetail_img' + id).find('label,textarea,input,br').remove();
			$('#pdetail_link' + id + ',#pdetail_img' + id).children().show();
			$('#pdetail_img' + id + ' a[rel]').hide();
						
			$('#pdetail_action' + id)			
				.html('<a href="javascript:editItem(' + id + ')" id="pdetail_edit' + id + '"><img border="0" src="/static/images/comment-edit.png" title="Изменить"></a><br /><a href="javascript:deleteItem(' + id + ')"><img border="0" src="/static/images/delete.png" title="Удалить"></a>');
		}
	}

	function saveItem(id) 
	{
		if ($('#pdetail_product_name' + id + ' textarea').length)
		{
			$('#pdetail_action' + id).html('<img border="0" src="/static/images/lightbox-ico-loading.gif" title="Товар сохраняется..."><br><a href="javascript:cancelItem(' + id + ')" id="pdetail_cancel' + id + '"><img border="0" src="/static/images/comment-delete.png" title="Отменить"></a>');
			$('#pdetail_id').val(id);
			$('#detailsForm').submit();						
		}
	}
	function updateStatuses()
	{
		$('input[name*="pdetail_special_"]').each(function () {
			if(this.checked==false) {
				this.value=0; 
				this.checked=true;
			}
		});
		var queryString = $('#detailsForm').formSerialize(); 
		$.post('<?= $selfurl ?>updatePdetailStatuses/', queryString, function(result) {
			self.location.reload();
		});
	}
	
	function moveItems()
	{
		var queryString = $('#detailsForm').formSerialize(); 
		$.post('<?= $selfurl ?>moveProducts/', queryString, function(result) {
			self.location.reload();
		});
	}
	
	function setRel(id)
	{
		$("a[rel*='lightbox_"+id+"']").lightBox();
		var aa = $("a[rel*='lightbox_"+id+"']");
		$(aa[0]).click();
	}
	
	function validate_number(evt) 
	{
		var theEvent = evt || window.event;
		var key = theEvent.keyCode || theEvent.which;
		key = String.fromCharCode( key );
		var regex = /[0-9]/;
		if( !regex.test(key) ) {
			theEvent.returnValue = false;
			theEvent.preventDefault();
		}
	}
	
	function updateItem(comment_id, user_login)
	{
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
		$f.action = '<?= $selfurl ?>addPackageComment/<?= $package->package_id ?>/'+$('#comment_id').val();
	}
	
	function cancel(){
		$('.save').show();
		$('.update').hide();

		$('#comment_user').html('');
		$('#comment_id').val('');
		$('#comment_update').text('');
		history.back();
	}

	function deleteComment(id)
	{
		if (confirm("Вы уверены, что хотите удалить комментарий №" + id + "?"))
		{
			window.location.href = '<?= $selfurl ?>delPackageComment/<?= $package->package_id ?>/'+id;
		}
	}
	
	var package_country = '<?=$package_country->country_name?>';
	<?= editor('comment', 212, 650, 'PackageComment') ?>
	<?= editor('comment_update', 212, 650, 'PackageComment') ?>
</script>
<? View::show('elements/hints'); ?>