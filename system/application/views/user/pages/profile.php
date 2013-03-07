		<form name='registration' class='registration' action='<?=BASEURL?>user/showProfile' method="POST">
			<h2 style="left:35px;position:relative;">личные данные</h2>
			<p style="left:-10px;position:relative;">Все поля заполняются только латинскими буквами</p>
			
			<? if ($result->e):?>
				<em style="color:<?= $result->e<0 ? 'red' : 'green'; ?> !important"><?=$result->m?></em>
				<br />
			<?endif;?>
			<div class='field <?=$user && $user->user_login && $result->e != -17  ? 'done' :'';?>'>
				<span>Логин:</span>
				<div class='text-field'><div><input type='text' name="login" value="<?=$user ? $user->user_login :'';?>" /></div></div>
			</div>
			<div class='field <?=$user && $user->user_password ? 'done' :'';?>'>
				<span>Пароль:</span>
				<div class='text-field'><div><input type='password' name="password" value="" /></div></div>
			</div>
			<div class='field <?=$user && $user->user_password ? 'done' :'';?>'>
				<span>Повторите пароль:</span>
				<div class='text-field'><div><input type='password' name="repassword" value="" /></div></div>
			</div>
			<div class='field <?=$user && $user->user_email && $result->e != -13 ? 'done' :'';?>' >
				<span>E-mail:</span>
				<div class='text-field'><div><input type='text' name="email" value="<?=$user ? $user->user_email :'';?>" /></div></div>
			</div>
			<div class='hr'></div>
			<div class='field <?=isset($client) && $client->client_name ?'done' :'';?>'>
				<span>Имя:</span>
				<div class='text-field'><div><input type='text' name="name" value="<?=isset($client) ? $client->client_name :'';?>" /></div></div>
			</div>
			<div class='field <?=isset($client) && $client->client_surname ?'done' :'';?>'>
				<span>Фамилия:</span>
				<div class='text-field'><div><input type='text' name="surname" value="<?=isset($client) ? $client->client_surname :'';?>" /></div></div>
			</div>
			<div class='field <?=isset($client) && $client->client_otc ?'done' :'';?>'>
				<span>Отчество:</span>
				<div class='text-field'><div><input type='text' name="otc" value="<?=isset($client) ? $client->client_otc :'';?>" /></div></div>
			</div>
			<div class='field done' id='country'>
				<span>Страна:</span>
				<select class="select" name="country">
					<?if ($Countries):foreach($Countries as $country):?>
						<option value="<?=$country->country_id?>" <?=isset($client)&&$client->client_country==$country->country_id?'selected':''?>><?=$country->country_name?></option>
					<?endforeach;endif;?>
				</select>
			</div>
			<div class='field <?=isset($client) && $client->client_town ?'done' :'';?>'>
				<span>Город:</span>
				<div class='text-field'><div><input type='text' name="town" value="<?=isset($client) ? $client->client_town :'';?>" /></div></div>
			</div>
			<div class='field <?=isset($client) && $client->client_address ?'done' :'';?>'>
				<span>Адрес:</span>
				<div class='text-field'><div><input type='text' name="address" value="<?=isset($client) ? $client->client_address :'';?>" /></div></div>
			</div>
			<div class='field <?=isset($client) && $client->client_index ?'done' :'';?>'>
				<span>Индекс:</span>
				<div class='text-field'><div><input type='text' name="index" value="<?=isset($client) ? $client->client_index :'';?>" /></div></div>
			</div>
			<div class='field <?=isset($client) && $client->client_phone ?'done' :'';?>'>
				<span>Телефон: +</span>
				<div class='text-field'><div>
					<input maxlength='5' class='reg_country' type='text' name="phone_country" id="phone_country" value="<?=isset($client) ? $client->client_phone_country :'';?>" />
				</div></div>
				<span style="width:5px;margin-left:2px;margin-right:2px;">(</span>
				<div class='text-field'><div>
					<input maxlength='5' class='reg_city' type='text' name="phone_city" id="phone_city" value="<?=isset($client) ? $client->client_phone_city :'';?>" />
				</div></div>
				<span class='' style="width:5px;margin-left:2px;margin-right:2px;">)</span>
				<div class='text-field'><div>
					<input maxlength='11' class='reg_number' type='text' name="phone_value" id="phone_value" value="<?=isset($client) ? $client->client_phone_value :'';?>" />
				</div></div>
			</div>
			<div class='field'>
				<span>Отправлять уведомления на e-mail:</span>
				<div class='checkbox'><div><input type='checkbox' name="notifications_on" <?=!isset($client) || $client->notifications_on ? 'checked' : '';?> /></div></div>
			</div>
			<div class='submit'><div><input type='submit' value='Сохранить' /></div></div>
			<input type="hidden" name='action' value='change' />
		</form>
<script>
	function validate_number(evt) {
		var theEvent = evt || window.event;
		var key = theEvent.keyCode || theEvent.which;
		key = String.fromCharCode( key );
		var regex = /[0-9]|\./;

		if( !regex.test(key) ) {
			theEvent.returnValue = false;
			theEvent.preventDefault();
		}
	}

	$(function() {
		$('#phone_country,#phone_city,#phone_value').keypress(function(event){validate_number(event);});
	});
</script>