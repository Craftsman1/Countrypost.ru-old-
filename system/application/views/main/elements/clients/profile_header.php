<div class='clientinfo admin-inside'>
<<<<<<< HEAD
	<img src="/main/avatar_big/<?= $client->client_user ?>" width="90px" height="90px">
=======
	<img src="/main/avatar_medium/<?= $client->client_user ?>" width="90px" height="90px">
>>>>>>> parent of 6c2ba62... Задачи: 16+37+35+33+30+31
	<h2 style='margin-bottom: 10px;text-transform: none;'><?= $client->statistics->fullname ?> (<?= $client->statistics->login ?>)</h2>
    <br/>
	<ul class='tabs'>
        <li class='active profile'><div><a class='profile' href='/<?= $client->statistics->login ?>/profile'>Профиль</a></div></li>
        <? if( ! empty($this->user) AND
			($this->user->user_group == 'manager' OR
			$this->user->user_id == $client->client_user)) : ?>
            <li class='delivery_address'><div><a class='delivery_address' href='/<?= $client->statistics->login ?>/profile'>Адреса доставки</a></div></li>
        <? endif; ?>
    </ul>
</div>
<script>
	$(function() {
		$('ul.tabs a')
			.click(function(e) {
				e.preventDefault();
				$('ul.tabs li').removeClass('active');
				$('div.client_tab').hide();
				
				$('div.' + $(e.target).attr('class')).show();
				
				$(this).parent().parent().addClass('active');
			});
	});
</script>