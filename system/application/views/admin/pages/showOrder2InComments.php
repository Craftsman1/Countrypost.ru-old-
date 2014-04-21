<div class='content'>
	<h2>Комментарии к заявке №<?=$order->order2in_id?></h2>

	<br />
	<div class="back">
		<a href="javascript:history.back();" class="back"><span>Назад</span></a>
	</div><br />
	
	<form class='partner-inside-1' action='#'>
		
		<div class='table'>
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<table>
				<tr>
					<th>Дата</th>
					<th>Сумма перевода</th>
					<th>Комиссия</th>
					<th>Статус</th>
				</tr>
				<tr>
					<td><?=date('d-m-Y H:i', strtotime($order->order2in_createtime))?></td>
					<td><?=$order->order2in_amount?>$</td>
					<td><?=$order->order2in_tax?>$</td>
					<td><?=$statuses[$order->order2in_status]?></td>
				</tr>
				
			</table>
		</div>
	</form>
	
	<h3>Комментарии к заказу</h3>
	<form  id="commentForm"  class='comments' action='<?=$selfurl?>addOrder2InComment/<?=$order->order2in_id?>' method='POST'>
		<?if (!$comments):?>
			<div class='comment'>
				Пока нет комментариев<br/>
			</div>
		<?else:?>
			<? foreach ($comments as $comment):?>
				<div class='comment'>
					<div class='question'>
					<?if ($comment->o2icomment_user == $order->order2in_user):?>
						<span class="name">Клиент:</span>
					<?else:?>
						<span class="name">Администрация:</span>
					<?endif;?>
						<p><?=$comment->o2icomment_text?></p>
						[<?=date('H:i d.m.Y', strtotime($comment->o2icomment_time))?>]
					</div>
				</div>
			<? endforeach; ?>
		<?endif;?>
	
		<div class='add-comment'>
			<div class='textarea'><textarea name='comment'></textarea></div>
			<div class='submit'><div><input type='submit' name="add" value="Добавить" /></div></div>
		</div>
	</form>
</div>
