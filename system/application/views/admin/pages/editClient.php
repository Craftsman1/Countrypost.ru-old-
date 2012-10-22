<div class='content'>
	<?View::show($viewpath.'elements/div_submenu');?>	
	<br />
	<div class="back">
		<a href="javascript:history.back();" class="back"><span>Назад</span></a>
	</div>
	
	<form name='registration' class='registration' action="<?=$selfurl?>updateClient/<?=isset($client_user) ? $client_user->user_id :'';?>" method="POST">
	
		<h2>Клиент: <?=isset($client_user) ? $client_user->user_login :'';?></h2>
		<p>Все поля заполняются только латинскими буквами</p>
		
		
		<? if ($result->e <0):?>
			<em style="color:red !important"><?=$result->m?></em>
			<br />
		<?endif;?>
		<div class='field <?=isset($client_user) && $client_user->user_login ? 'done' :'';?>'>
			<span>Логин:</span>
			<div class='text-field'><div><input type="text" name="login" value="<?=isset($client_user) ? $client_user->user_login :'';?>"></div></div>
		</div>
		<div class='field <?=isset($client_user) && $client_user->user_email ? 'done' :'';?>' >
			<span>E-mail:</span>
			<div class='text-field'><div><input type="text" name="email" value="<?=isset($client_user) ? $client_user->user_email :'';?>"></div></div>
		</div>
		<div class='hr'></div>
		<div class='field <?=isset($client) && $client->client_name ?'done' :'';?>'>
			<span>Имя:</span>
			<div class='text-field'><div><input type="text" name="name" value="<?=isset($client) ? $client->client_name :'';?>"></div></div>
		</div>
		<div class='field <?=isset($client) && $client->client_surname ?'done' :'';?>'>
			<span>Фамилия:</span>
			<div class='text-field'><div><input type="text" name="surname" value="<?=isset($client) ? $client->client_surname :'';?>"></div></div>
		</div>
		<div class='field <?=isset($client) && $client->client_otc ?'done' :'';?>'>
			<span>Отчество:</span>
			<div class='text-field'><div><input type="text" name="otc" value="<?=isset($client) ? $client->client_otc :'';?>"></div></div>
		</div>
		<div class='field done' id='country'>
			<span>Страна:</span>
			<select name="country" class="select">
				<option>выберите...</option>
				<?if (count($countries)>0): foreach ($countries as $country):?>
					<option value="<?=$country->country_id;?>" <?= (isset($client) && $client->client_country==$country->country_id) ? 'selected' :'';?>><?=$country->country_name?></option>
				<?endforeach; endif;?>							
			</select>
		</div>
		<div class='field <?=isset($client) && $client->client_town ?'done' :'';?>'>
			<span>Город:</span>
			<div class='text-field'><div><input type="text" name="town" value="<?=isset($client) ? $client->client_town :'';?>"></div></div>
		</div>
		<div class='field <?=isset($client) && $client->client_address ?'done' :'';?>'>
			<span>Адрес:</span>
			<div class='text-field'><div><input type='text' name="address" value="<?=isset($client) ? $client->client_address :'';?>" /></div></div>
		</div>
		<div class='field <?=isset($client) && $client->client_index ?'done' :'';?>'>
			<span>Индекс:</span>
			<div class='text-field'><div><input type="text" name="index" value="<?=isset($client) ? $client->client_index :'';?>"></div></div>
		</div>
		<div class='field <?=isset($client) && $client->client_phone ?'done' :'';?>'>
			<span>Телефон:</span>
			<div class='text-field'><div><input type='text' name="phone" value="<?=isset($client) ? $client->client_phone :'';?>" /></div></div>
		</div>
		<div class='hr'></div>
		<div class='submit'><div><input type='submit' value='Сохранить' /></div></div>
	</form>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#filterForm select').change(function() {
			document.getElementById('filterForm').submit();	
		});
	});
</script>