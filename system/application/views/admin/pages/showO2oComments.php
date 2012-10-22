<div class='content'>
	<h2>Заявка №<?=$o2o->order2out_id?></h2>
	<br />
	<div class='table' style="width:300px;">
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		
		<table>
			<tr>
				<td>Сумма:</td>
				<td>$<?=$o2o->order2out_ammount?></td>
			</tr>
			<tr>
				<td>Статус:</td>
				<td><?=$o2o->order2out_status == 'processing' ? 'В обработке' : 'Выплачено'?></td>
			</tr>
		</table>
	</div>
	<h3>Комментарии</h3>
	<form class='comments' action='<?=$selfurl?>addO2oComment/<?=$o2o->order2out_id?>' method='POST'>
		<?if (!$comments):?>
			<div class='comment'>
				Пока нет комментариев<br/>
			</div>
		<?else:?>
			<? foreach ($comments as $comment):?>
				<div class='comment'>
					<div class='question'>
						<span class="name">№<?= $comment->o2comment_id ?>
							<?if ($comment->o2comment_user == $user->user_id):?>
								Вы:
							<?elseif (isset($o2o->is_client_o2o)) : ?>
								Клиент:
							<?elseif (isset($o2o->is_manager_o2o)) : ?>
								Менеджер:
							<?endif;?>
							<br /><?=formatCommentDate($comment->o2comment_time)?>
						</span>
						<div id="comment_<?= $comment->o2comment_id ?>">
							<p><?=html_entity_decode($comment->o2comment_comment)?></p>
						</div>						
					</div>
				</div>
			<? endforeach; ?>
		<?endif;?>
	
		<?if ($user):?>
		<div class='add-comment'>
			<div class='textarea'><textarea name='comment'></textarea></div>
			<div class='submit'><div><input type='submit' name="add" value="Добавить" /></div></div>
		</div>
		<?endif;?>
	</form>
</div>