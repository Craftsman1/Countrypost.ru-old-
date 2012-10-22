<div class='content'>
	<h2>Каталог магазинов</h2>
	<div class="back">
		<a href="javascript:history.back();" class="back"><span>Назад</span></a>
	</div><br />
	<center>
		<h3><?=$shop->shop_name?></h3>
	</center>
	
	<form class='admin-inside' action='#'>
		
		<div class='table'>
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<table>
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<tr>
					<th>Страна</th>
					<th>Описание</th>
				</tr>
				<tr>
					<td><?=$country?></td>
					<td><?=$shop->shop_desc?></td>
				</tr>
			</table>
		</div>
	</form>
	
	<h3>Комментарии</h3>
	<form class='comments' action='<?=BASEURL?>main/showShop/<?=$shop->shop_id?>' method='POST'>
		<?if (!$comments):?>
			<div class='comment'>
				Пока нет комментариев<br/>
			</div>
		<?else:?>
			<? foreach ($comments as $comment):?>
				<div class='comment'>
					<div class='question'>
						<? if (isset($user) &&
								$user &&
								$user->user_group == 'admin') : ?>
						<a href="javascript:deleteItem('<?=$comment->scomment_id?>');"><img title="Удалить" border="0" src="/static/images/delete.png"></a>
						<? endif; ?>
						<span class="name">
							<? if (isset($user) &&
								$user &&
								$user->user_id == $comment->scomment_user) : ?>
							Вы:
							<? elseif ($susers[$comment->scomment_user]->user_group == 'admin') : ?>
							Countrypost.ru:
							<? else: ?>
							<?=$susers[$comment->scomment_user]->user_login?>:
							<? endif; ?>&nbsp;
						</span>
						<p><?=$comment->scomment_comment?></p>
						<? if (isset($user) &&
								$user &&
								$user->user_group == 'admin') : ?>
						<p>
							<a href="javascript:updateItem('<?=$comment->scomment_id?>', '<?=$susers[$comment->scomment_user]->user_login?>', '<?=$comment->scomment_comment?>');">Изменить</a>
						</p>
						<? endif; ?>
					</div>
				</div>
			<? endforeach; ?>
		<?endif;?>
		<?if ($user):?>
		<a name="edit_comment_area" />
		<h3 class="update" style="display:none;">Редактирование комментария</h3>
		<div class='comment' style="border:0;">			
			<div class='question update' style="display:none;">
				<span id="comment_user" class="name">
				</span>
			</div>
		</div>
		<br class="update" style="display:none;"/>
		<div class='add-comment'>
			<input type='hidden' id='comment_id' name='comment_id' />
			<div class='textarea update' style="display:none;"><textarea id='comment_update' name='comment_update'></textarea></div>
			<div class='submit update' style="display:none;"><div><input type='submit' id="update" name="update" value="Сохранить" /></div></div>
			<br />
			<div class='textarea save'><textarea name='comment'></textarea></div>
			<div class='submit save'><div><input type='submit' name="add" value="Добавить" /></div></div>
		</div>
		<br class="update" style="display:none;"/>
		<div class="back update" style="display:none;">
			<a href="#" onclick="cancel();" class="back"><span>Отмена</span></a>
		</div>
		<?endif;?>
	</form>
</div>
<? if (isset($user) && $user && $user->user_group == 'admin') : ?>
<script type="text/javascript">
function deleteItem(id){
	if (confirm("Вы уверены, что хотите удалить комментарий №" + id + "?")){
		window.location.href = '<?=$selfurl?>deleteSComment/' + id + '/<?=$comment->scomment_shop?>';
	}
}

function updateItem(comment_id, user_login, comment){
	$('.save').hide();
	$('#comment_user').html(user_login);
	$('#comment_id').val(comment_id);
	$('#comment_update').text(comment);
	$('.update').show().css('display:block;');
	window.location.href = '#edit_comment_area';
}

function cancel(){
	$('.save').show();
	$('.update').hide();

	$('#comment_user').html('');
	$('#comment_id').val('');
	$('#comment_update').text('');
}
</script>
<? endif; ?>