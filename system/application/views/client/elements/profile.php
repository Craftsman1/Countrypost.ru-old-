<div class="profile table client_tab" style="height: 828px;">
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<div class="dealer_profile_left">
		<form action="/client/saveProfile" id="onlineItemForm" method="POST">
			<img src="<?= IMG_PATH ?>avatar_big.png" width="200px" height="200px">
			<br>
			<br>
			<a href="javascript:void();">изменить фото</a>
		</form>
	</div>
	<div class='profile_box admin-inside'>
		<form id="profileForm" action="/client/saveProfile">           
			<br style="clear:both;" />
			<div>
				<span class="label">Фамилия*:</span>
				<input style="width:180px;" class="textbox" maxlength="255" type='text' id='client_surname' name="client_surname" value="<?= $client->statistics->client_surname ?>" />
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Имя*:</span>
				<input style="width:180px;" class="textbox" maxlength="255" type='text' id='client_name' name="client_name" value="<?= $client->statistics->client_name ?>" />
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Отчество*:</span>
				<input style="width:180px;" class="textbox" maxlength="255" type='text' id='client_otc' name="client_otc" value="<?= $client->statistics->client_otc ?>" />
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Логин*:</span>
				<input style="width:180px;" class="textbox" maxlength="32" type='text' id='login' name="login" value="<?= $client->statistics->login ?>" />
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Пароль*:</span>
				<input style="width:180px;" class="textbox" maxlength="32" type='password' id='password' name="password" />
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Email*:</span>
				<input style="width:180px;" class="textbox" maxlength="128" type='text' id='email' name="email" value="<?= $client->statistics->email ?>" />
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Сайт или тема на форуме:</span>
				<input style="width:180px;" class="textbox" maxlength="4096" type='text' id='website' name="website" value="<?= $client->website ?>" />
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Skype:</span>
				<input style="width:180px;" class="textbox" maxlength="255" type='text' id='skype' name="skype" value="<?= $client->skype ?>"/>
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Страна*:</span>
				<select id="country" name="country" class="textbox">
					<option value="0">выберите страну...</option>
					<? foreach ($countries as $country) : ?>
					<option value="<?= $country->country_id ?>"  title="/static/images/flags/<?= $country->country_name_en ?>.png" <? if ($client->client_country == $country->country_id) : ?>selected<? endif; ?>><?= $country->country_name ?></option>
					<? endforeach; ?>
				</select>
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Город*:</span>
				<input style="width:180px;" class="textbox" maxlength="255" type='text' id='city' name="city" value="<?= $client->client_town ?>"/>
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Индекс*:</span>
				<input style="width:180px;" class="textbox" maxlength="255" type='text' id='client_index' name="client_index" value="<?= $client->client_index ?>"/>
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Адрес*:</span>
				<input style="width:180px;" class="textbox" maxlength="255" type='text' id='client_address' name="client_address" value="<?= $client->client_address ?>"/>
			</div>            
			<br style="clear:both;" />
			<div>
				<span class="label">О себе:</span>
			</div>
			<br style="clear:both;" />
			<div style="padding-left:10px;">
				<textarea maxlength="65535" id='about' name="about"><?= $client->about_me ?></textarea>
			</div>
			<br style="clear:both;" />
			<div class="submit" style="margin-left: 8px;">
				<div>
					<input type="submit" value="Сохранить">
				</div>
			</div>
			<img class="float" id="profileProgress" style="display:none;margin:0px;margin-top:4px;" src="/static/images/lightbox-ico-loading.gif"/>
		</form>
	</div>
</div>
<script>
	$(function() {
		$("#country").msDropDown({mainCSS:'idd'});
		
		$('#profileForm').ajaxForm({
			target: '/client/saveProfile',
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$("#profileProgress").show();
			},
			success: function(response)
			{
				$("#profileProgress").hide();
				success('top', 'Персональные данные успешно сохранены!');
			},
			error: function(response)
			{
				$("#profileProgress").hide();
				error('top', 'Заполните необходимые поля и сохраните еще раз.');
			}
		});
	});

	<?= editor('about', 150, 380, 'PackageComment') ?>
</script>