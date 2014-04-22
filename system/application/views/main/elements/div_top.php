<div class='header'>
	<h1 class='logo'>
		<a href='<?= $this->config->item('base_url') ?>'>CountryPost - Лучший сервис покупок за рубежом</a>
	</h1>    
	<?php 
        $clients_count = $user_count[0]->user_count;
        $managers_count = $user_count[1]->user_count;
    ?>
	<ul class='menu'>
		<li><a href='<?= $this->config->item('base_url') ?>dealers'>Посредники (<?=$managers_count?>)</a></li>
		<li><a href='<?= $this->config->item('base_url') ?>main/showFAQ'>FAQ</a></li>
		</ul>
</div>