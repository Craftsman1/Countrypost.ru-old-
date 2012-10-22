<div class='content'>
	<h2>Посылка №<?=$package->package_id?></h2>
	<form class='partner-inside-1' action='#'>
		
		<div class='table'>
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<table>
				<tr>
					<th>Партнер №</th>
					<th>Адрес партнера</th>
					<th>Общая цена заказа <br />с учетом местной доставки</th>
					<th>Статус</th>
				</tr>
				<tr>
					<td><?=$package->package_manager?></td>
					<td><?=$package->Managers->manager_name?> (<?=$package->Managers->manager_addres?>)</td>
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
	<br />
	<form  id="commentForm"  class='comments' action='<?=$selfurl?>addPackageComment/<?=$package->package_id?>' method='POST'>
		<?if (!$comments):?>
			<div class='comment'>
				Пока нет комментариев<br/>
			</div>
		<?else:?>
			<? foreach ($comments as $comment):?>
				<div class='comment'>
					<div class='question'>
						<span class="name">№<?= $comment->pcomment_id ?>
						<?if ($comment->pcomment_user == $package->package_client):?>
							Вы:
						<?elseif ($comment->pcomment_user == $package->package_manager):?>
							Менеджер:
						<?else:?>
							Администрация:
						<?endif;?>
							<br /><?=formatCommentDate($comment->pcomment_time)?>
						</span>
						<p><?=html_entity_decode($comment->pcomment_comment)?></p>
						
					<?if (false && $comment->pcomment_user == $package->package_client):?>
						<a href="<?=$selfurl?>delPackageComment/<?=$package->package_id.'/'.$comment->pcomment_id?>" >Удалить</a>
						<p onclick="$('#editComment_<?=$comment->pcomment_id?>').show();"   style="text-decoration:underline; cursor:pointer; color:#BF0090;" >Редактировать</p>
							<div class='add-comment' id="editComment_<?=$comment->pcomment_id?>" style="display:none;">
								<div class='textarea'><textarea name='ecomment_<?=$comment->pcomment_id?>'><?=$comment->pcomment_comment?></textarea></div>
								<div><a href="javascript:editComment(<?=$package->package_id?>,<?=$comment->pcomment_id?>)" >Сохранить</a></div>
							</div>
					<?endif;?>
						
					</div>
				</div>
			<? endforeach; ?>
		<?endif;?>
	
		<h3>Оставьте комментарий:</h3>
		<div class='add-comment'>
			<div><textarea id='comment' name='comment'></textarea></div>
			<div class='submit comment-submit'><div><input type='submit' name="add" value="Добавить" /></div></div>
			<script type='text/javascript' src='/system/plugins/fckeditor/fckeditor.js'></script>
		</div>
	</form>
</div>


<script type="text/javascript">
	function editComment($pid, $cid){
		var $f = document.getElementById('commentForm');
		$f.action = '<?=$selfurl?>addPackageComment/'+$pid+'/'+$cid;
		$f.comment.value = $f['ecomment_'+$cid].value;
		$f.submit();
	}

	<? echo editor('comment', 212, 650, 'PackageComment') ?>
</script>