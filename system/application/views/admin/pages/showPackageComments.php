<div class='content'>
	<h2>Аккаунт Администратора</h2>
	<h3>Посылка №<?=$package->package_id?></h3>
	<div class='back'>
	</div><br />
	<?Breadcrumb::showCrumbs();?>
	<br />
	
	<form class='partner-inside-1' action='#'>
		
		<div class='table'>
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<table>
				<col width='auto' />
				<col width='auto' />
				<col width='250' />
				<col width='auto' />
				<tr>
					<th>Информация о партнере</th>
					<th>Информация о клиенте</th>
					<th>Ф.И.О., адрес доставки</th>
					<th>Общая цена заказа <br />с учетом местной доставки</th>
					<th>Статус</th>
				</tr>
				<tr>
					<td><?= '#'.$package->Managers->manager_user.' '.$package->Managers->manager_name.' '.$package->Managers->manager_surname?></td>
					<td><?= '#'.$package->Clients->client_user.' '.$package->Clients->client_name.' '.$package->Clients->client_surname?></td>
					<td><?=$package->package_address?></td>
					<td>
						Общая стомость заказанных товаров: <?=$package->package_cost?> $<br />
						Цена доставки: <?=$package->package_delivery_cost?> $<br />
						Общий вес посылки: <?=$package->package_weight?> кг
					</td>
					<td>
						<?if (	$package->package_status == 'not_delivered'):?>
							Ждем прибытия
						<?elseif ($package->package_status == 'payed'):?>
							Оплачено
						<?elseif ($package->package_status == 'not_payed'):?>
							Не оплачено
						<?elseif ($package->package_status == 'sended' || $package->package_status == 'sent'):?>
							Отправлена
						<?elseif ($package->package_status == 'proccessing'):?>
							Обрабатывается
						<?elseif ($package->package_status == 'deleted'):?>
							Удалена
						<?endif;?>
					</td>
				</tr>				
			</table>
		</div>
	</form>
	
	<h3>Комментарии к посылке</h3>
	<form id="commentForm" class='comments' action='<?=$selfurl?>addPackageComment/<?=$package->package_id?>' method='POST'>
		<?if (!$comments):?>
			<div class='comment'>
				Пока нет комментариев<br/>
			</div>
		<?else:?>
			<? foreach ($comments as $comment):?>
				<div class='comment'>
					<div class='question'>
						<span class="name">№<?= $comment->pcomment_id ?>
						<?if ($comment->pcomment_user == $package->package_manager):?>
							Менеджер:
						<?elseif ($comment->pcomment_user == $package->package_client):?>
							Клиент:
						<?else:?>
							Вы:
						<?endif;?>
							<br /><?=formatCommentDate($comment->pcomment_time)?>
						</span>
						<div id="comment_<?=$comment->pcomment_id?>">
							<?=html_entity_decode($comment->pcomment_comment)?>
						</div>
						
							<a href="javascript:updateItem(<?=$comment->pcomment_id?>, '№<?= $comment->pcomment_id ?> <?if ($comment->pcomment_user == $package->package_manager):?>Менеджер<?elseif ($comment->pcomment_user == $package->package_client):?>Клиент<?else:?>Вы<?endif;?>:', <?=$comment->pcomment_id?>);">Изменить</a>
							<a href="javascript:deleteItem(<?=$comment->pcomment_id?>);"><img border="0" src="/static/images/delete.png" title="Удалить"></a>
							<br /><br />
					</div>
				</div>
			<? endforeach; ?>
		<?endif;?>
	
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
		$f.action = '<?=$selfurl?>addPackageComment/<?=$package->package_id?>/'+$('#comment_id').val();
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
			window.location.href = '<?=$selfurl?>delPackageComment/<?=$package->package_id?>/'+id;
		}
	}

	<? echo editor('comment', 212, 650, 'PackageComment') ?>
	<? echo editor('comment_update', 212, 650, 'PackageComment') ?>
</script>