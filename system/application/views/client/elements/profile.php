<div class="profile table client_tab" style="height: 530px;">
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
				<span class="label">Логин*:</span>
                <span class="label" style="margin-left: 0;"><b><?= $client->statistics->login ?></b></span>
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Пароль*:</span>
				<input style="width:180px;" class="textbox" maxlength="32" type='password' id='password' name="password" />
			</div>
            <br style="clear:both;" />
            <div>
                <span class="label">Страна*:</span>
                <select id="client_country" class="textbox" name="client_country">
                    <option value="0">Выбрать страну</option>
                    <? if (!empty($Countries)) : foreach ($Countries as $country) : ?>
                    <option title="/static/images/flags/<?= $country->country_name_en ?>.png" value="<?= $country->country_id ?>" <?= ($client->client_country == $country->country_id) ? ' selected="selected" ' : '' ?>><?= $country->country_name ?></option>
                    <? endforeach; endif; ?>
                </select>

            </div>
            <br style="clear:both;" />
            <div>
                <span class="label">Email*:</span>
                <input style="width:180px;" class="textbox" maxlength="128" type='text' id='email' name="email" value="<?= $client->statistics->email ?>" />
            </div>
			<br style="clear:both;" />
			<div>
				<span class="label">Skype:</span>
				<input style="width:180px;" class="textbox" maxlength="255" type='text' id='skype' name="skype" value="<?= $client->skype ?>"/>
			</div>
			<br style="clear:both;" />
			<div>
                <span class="label">Получать уведомления на email</span>
                <input type="checkbox" class="checkbox" name="notifications_on" id="notifications_on"  <?= ($client->notifications_on == 1) ? 'checked="checked"' : '' ?> />
			</div>
			<br style="clear:both;" />
            <div>
                <span class="label">О себе:</span>
            </div>
            <br style="clear:both;">
            <div style="padding-left:10px;">
                <textarea id="about" name="about_me" maxlength="65535"><?= $client->about_me ?></textarea>
            </div>
            <br style="clear:both;">
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
        $("#client_country").msDropDown({mainCSS:'idd'});
		
		var validateProfile = function() {
			var addError = function(field, message) {
				$("#profileProgress").hide();
				if (!field.hasClass('ErrorField')) 
				{
					errorMsg = $('<span class="ValidationErrors">'+message+'</span>');
					field.addClass('ErrorField').after(errorMsg);
				}
			},
			removeError = function(field) {
				var nextElement = field.next();
				if (field.hasClass('ErrorField'))
				{
					field.removeClass('ErrorField');
				}
				if(nextElement.hasClass('ValidationErrors'))
				{
					nextElement.remove();
				}
			},
			field = null,
			errorCount = 0;
			
			field = $('#login');
			if(field.val() == '')
			{
				addError(field, 'Введите ваш логин.');
				errorCount++;				
			}
			else
			{
				removeError(field);
			}
			
			field = $('#password');
			if(field.val() != '' && field.val().length < 6)
			{
				addError(field, 'Пароль должен состоять из не менее 6-ти символов.');
				errorCount++;				
			}
			else
			{
				removeError(field);
			}
			
			field = $('#email');
			if(!field.val().match(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/))
			{
				addError(field, 'Введите правильный email.');
				errorCount++;				
			}
			else
			{
				removeError(field);
			}
			
			return (errorCount > 0) ? false : true;
			
		}
		
		// Валидация при заполнении
		$('#login, #password, #email').bind('blur', validateProfile);
		
		$('#profileForm').ajaxForm({
			target: '/client/saveProfile',
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$("#profileProgress").show();
				// Валидация перед отправкой
				return validateProfile();
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