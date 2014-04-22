<form id="filterForm" action="<?= $selfurl ?><?= $handler ?>" method="POST">
	<div class='sorting'>

		<span class='first-title'>Отфильтровать по стране:</span>
		<select name="client_country" class='select first-input'>
			<option value="">выбрать...</option>
			<?if ($countries) : foreach($countries as $country) : ?>
				<option value="<?=$country->country_id?>" <? if ($country->country_id == $filter->client_country) : ?>selected="selected"<? endif; ?>><?=$country->country_name?></option>
			<?endforeach; endif;?>
		</select>

		<span class='first-title'>партнеру:</span>
		<select name="manager_user" class='select first-input'>
			<option value="">выбрать...</option>
			<?if ($managers) : foreach($managers as $manager) : ?>
				<option value="<?=$manager->manager_user?>" <? if ($manager->manager_user == $filter->manager_user) : ?>selected="selected"<? endif; ?>><?=$manager->user_login?></option>
			<?endforeach; endif;?>
		</select>
		<span>за:</span>
		<select name="period" class='select'>
			<option value="">все</option>
			<option value="day" <? if ('day' == $filter->period) : ?>selected="selected"<? endif; ?>>день</option>
			<option value="week" <? if ('week' == $filter->period) : ?>selected="selected"<? endif; ?>>неделю</option>
			<option value="month" <? if ('month' == $filter->period) : ?>selected="selected"<? endif; ?>>месяц</option>
		</select>
	</div>
	<div class='sorting'>
		<span class='first-title'>Поиск:</span>
		<div class='text-field first-input'><div><input type="text" maxlength="11" name="search_client" value="<?=$filter->search_client?>"/></div></div>
		<span>по:</span>

		<select name="id_type" class='select'>
			<option value="">выбрать...</option>
			<option value="login" <? if ('login' == $filter->id_type) : ?>selected="selected"<? endif; ?>>Логину</option>
			<option value="client_number" <? if ('client_number' == $filter->id_type) : ?>selected="selected"<? endif; ?>>Номеру</option>
		</select>	
	</div>
</form>