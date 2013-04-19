<div class='clientinfo admin-inside'>
	<img src="<? if(!$client->avatar){echo IMG_PATH ?>avatar_big.png<?}else echo $client->avatar?>" width="90px" height="90px">
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