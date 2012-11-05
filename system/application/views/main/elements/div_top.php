<div class='header'>
	<h1 class='logo'>
		<a href='<?= BASEURL ?>'>CountryPost - Лучший сервис покупок за рубежом</a>
	</h1>    
	<?php 
        $clients_count = $user_count[0]->user_count;
        $managers_count = $user_count[1]->user_count;
    ?>
	<ul class='menu'>
		<li><a href='<?= BASEURL ?>dealers'>Посредники (<?=$managers_count?>)</a></li>
		<li><a href='<?= BASEURL ?>clients'>Клиенты (<?=$clients_count?>)</a></li>
		<li><a href='<?= BASEURL ?>main/showFAQ'>FAQ</a></li>
		<li><a href='<?= BASEURL ?>'>Магазин</a></li>
		<li><a href='<?= BASEURL ?>'>Рассылка</a></li>
	</ul>
</div>