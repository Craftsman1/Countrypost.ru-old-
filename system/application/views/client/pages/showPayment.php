<div class='content'>
	<?  Breadcrumb::showCrumbs(); ?>
	<h2>Заявка на оплату №<?= $o2i->order2in_id ?> (напрямую посреднику)</h2>
	<br />
	<div class='table'>
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>		
		<table>
			<tr>
				<td>
					<b>Статус:</b>
				</td>
				<td>
					<b>
						<?= $o2i->order2in_status == 'processing' ? 'Обрабатывается' : ($o2i->order2in_status == 'not_delivered' ? 'Не получено' : ($o2i->order2in_status == 'not_confirmed' ? 'Нет скриншота' :'Выплачено')) ?>
					</b>
				</td>
			</tr>
			<tr>
				<td>
					Оплата заказа
				</td>
				<td>
					<a href="/client/order/<?= $o2i->order_id ?>">№<?= $o2i->order_id ?></a>
				</td>
			</tr>
			<tr>
				<td>
					Сумма оплаты
				</td>
				<td>
					<?= $o2i->order2in_amount ?>
				</td>
			</tr>
			<tr>
				<td>
					Способ оплаты
				</td>
				<td>
					<?= $o2i->payment_service_name ?>
				</td>
			</tr>
			<tr>
				<td>
					Получатель
				</td>
				<td>
					<a href="/dealers/<?= $o2i->order2in_to ?>"><?= $o2i->order2in_to ?></a>
				</td>
			</tr>
			<tr>
				<td>
					Дата добавления
				</td>
				<td>
					<?= date("d.m.Y H:i", strtotime($o2i->order2in_createtime)) ?>
				</td>
			</tr>
		</table>
	</div>
	<h3>Комментарии</h3>
	<form class='comments' action='<?= $selfurl ?>addO2iComment/<?= $o2i->order2in_id ?>' method='POST'>
		<? if ( ! $comments): ?>
			<div class='comment'>
				Пока нет комментариев<br/>
			</div>
		<? else: ?>
			<?  foreach ($comments as $comment): ?>
				<div class='comment'>
					<div class='question'>
						<span class="name">№<?= $comment->o2icomment_id ?>
							<? if ($comment->o2icomment_user == $o2i->order2in_user): ?>
								Вы:
							<? else: ?>
								Администратор:
							<? endif; ?>
							<br /><?= formatCommentDate($comment->o2icomment_time) ?>
						</span>
						<p><?= $comment->o2icomment_text ?></p>
					</div>
				</div>
			<?  endforeach; ?>
		<? endif; ?>
		<? if ($user): ?>
		<div class='add-comment'>
			<div class='textarea'><textarea name='comment'></textarea></div>
			<div class='submit'><div><input type='submit' name="add" value="Добавить" /></div></div>
		</div>
		<? endif; ?>
	</form>
</div>