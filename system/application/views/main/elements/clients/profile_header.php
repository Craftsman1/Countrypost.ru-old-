<div class='clientinfo admin-inside'>
	<img src='/static/images/avatar_big.png'>
	<h2 style='margin-bottom: 10px;'><?= $client->statistics->fullname ?> (№ <?= $client->client_user ?>)</h2><br/>
	<ul class='tabs'>
		<li class='active profile'><div><a class='profile' href='/<?= $client->statistics->login ?>/profile'>Профиль</a></div></li>
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