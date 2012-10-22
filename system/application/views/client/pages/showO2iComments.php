<div class='content'>
	<h2>Заявка №<?=$o2i->order2in_id?></h2>
	<br />
	<div class='table' style="width:300px;">
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		
		<table>
			<tr>
				<td>Сумма:</td>
				<td>$<?=$o2i->order2in_amount?></td>
			</tr>
			<tr>
				<td>Статус:</td>
				<td><?=$o2i->order2in_status == 'processing' ? 'Обрабатывается' : ($o2i->order2in_status == 'not_delivered' ? 'Не получено' : ($o2i->order2in_status == 'not_confirmed' ? 'Нет скриншота' :'Выплачено'))?></td>
			</tr>
		</table>
	</div>
	<h3>Комментарии</h3>
	<form class='comments' action='<?=$selfurl?>addO2iComment/<?=$o2i->order2in_id?>' method='POST'>
		<?if (!$comments):?>
			<div class='comment'>
				Пока нет комментариев<br/>
			</div>
		<?else:?>
			<? foreach ($comments as $comment):?>
				<div class='comment'>
					<div class='question'>
						<span class="name">№<?= $comment->o2icomment_id ?>
							<?if ($comment->o2icomment_user == $o2i->order2in_user):?>
								Вы:
							<?else:?>
								Администратор:
							<?endif;?>
							<br /><?=formatCommentDate($comment->o2icomment_time)?>
						</span>
						<p><?=$comment->o2icomment_text?></p>
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