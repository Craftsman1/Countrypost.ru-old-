<div class="profile table client_tab" style="height: 620px;">
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<div class="dealer_profile_absolute_right">
		<form action="/client/saveProfilePhoto" enctype='multipart/form-data'  id="profilePhotoForm" method="POST">
			<img src="/main/avatar_big/<?= $client->client_user ?>" id="img_place" width="200px" height="200px">
			<br>
			<br>
			<input class="textbox screenshot_uploader_box" type='file' id='pr_file' name="userfile" style='display:none;width:180px;'>
			<a id="select_file" href="javascript:void();">изменить фото</a>
		</form>
	</div>
	<div class='profile_box admin-inside'>
		<form id="profileForm" action="/client/saveProfile">
			<div>
				<span class="label">Логин:</span>
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
                <select id="client_country" class="textbox" name="client_country" onchange="$.fn.validateProfileCountry($(this))">
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
                <span class="label">ФИО*:</span>
                <input style="width:180px;" class="textbox" maxlength="128" type='text' id='client_name' name="client_name" value="<?= $client->client_name ?>" />
            </div>
			<br style="clear:both;" />
			<div>
				<span class="label">Skype:</span>
				<input style="width:180px;" class="textbox" maxlength="255" type='text' id='skype' name="skype" value="<?= $client->skype ?>"/>
			</div>
			<br style="clear:both;" />
			<div>
			<span class="label">Телефон:</span>
				<input style="width:180px;" class="textbox" maxlength="255" type='text' id='phone' name="phone" value="<?= $client->client_phone ?>"/>
			</div>
			<br style="clear:both;" />
			<div>
                <span class="label">Получать уведомления на email</span>
                <input type="checkbox" class="checkbox" name="notifications_on" id="notifications_on" value="1"  <?= ($client->notifications_on) ? 'checked' : '' ?> />
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
	var profile_country = '';
	$(function() {
        $("#client_country").msDropDown({mainCSS:'idd'});

		var validateProfileForm = function() {
			field = null,
			errorCount = 0;

			field = $('#login');
			if(field.val() == '')
			{
				$.fn.addProfileFieldError(field, 'Введите ваш логин');
				errorCount++;
			}
			else
			{
				$.fn.removeProfileFieldError(field);
			}

            field = $('#password');
            if(field.val() != '' && field.val().length < 6)
            {
                $.fn.addProfileFieldError(field, 'Пароль должен состоять из не менее 6-ти символов');
                errorCount++;
            }
            else
            {
                $.fn.removeProfileFieldError(field);
            }

            field = $('#client_country');
            if(field.val() == 0 || field.val() == '')
            {
                $.fn.addProfileFieldError($('#client_country_msdd'), 'Необходимо указать страну');
                errorCount++;
            }
            else
            {
                $.fn.removeProfileFieldError($('#client_country_msdd'));
            }

			field = $('#email');
			if(!field.val().match(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/))
			{
				$.fn.addProfileFieldError(field, 'Введите правильный email.');
				errorCount++;
			}
			else
			{
				$.fn.removeProfileFieldError(field);
			}

            field = $('#client_name');
            if(!field.val().match(/^\S+/))
            {
                $.fn.addProfileFieldError(field, 'ФИО не должно быть пустым');
                errorCount++;
            }
            else
            {
                $.fn.removeProfileFieldError(field);
            }

			return (errorCount > 0) ? false : true;

        }

        $.fn.validateProfileCountry = function(field)
        {
            if(field.val() == 0 || field.val() == '')
            {
                $.fn.addProfileFieldError($('#client_country_msdd'), 'Необходимо указать страну');
                return false;
            }
            else
            {
                $.fn.removeProfileFieldError($('#client_country_msdd'));
                return true;
            }
        }

        $.fn.addProfileFieldError = function(field, message) {
            $("#profileProgress").hide();
            if (!field.hasClass('ErrorField'))
            {
                errorMsg = $('<span class="ValidationErrors">'+message+'</span>');
                field.addClass('ErrorField').after(errorMsg);
            }
        }

        $.fn.removeProfileFieldError = function(field) {
            var nextElement = field.next();
            if (field.hasClass('ErrorField'))
            {
                field.removeClass('ErrorField');
            }
            if(nextElement.hasClass('ValidationErrors'))
            {
                nextElement.remove();
            }
        }

        // Валидация при заполнении
        $('#login').validate({
            expression: "if (VAL != '') return true; else return false;",
            message: "Введите ваш логин"
        });
        $('#password').validate({
            expression: "if (!(VAL != '' && VAL.length < 6)) return true; else return false;",
            message: "Пароль должен состоять из не менее 6-ти символов"
        });
        $('#email').validate({
            expression: "if (VAL.match(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/)) return true; else return false;",
            message: "Введите правильный email"
        });

		$('a#select_file').click(function(e) {
			$('#pr_file').click();
		});

		$('#pr_file').change(function(e) {
			$('#profilePhotoForm').submit();
		});

		$('#profilePhotoForm').ajaxForm({
			target: '/client/saveProfilePhoto',
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$("#profileProgress").show();
				// Валидация перед отправкой
                var valid = validateProfileForm();

                if (!valid)
                    $("#profileProgress").hide();

				return validateProfileForm();
			},
			success: function(response)
			{
				$("#profileProgress").hide();
				success('top', 'Аватар успешно сохранен!');
				$("#img_place").attr('src',response);
			},
			error: function(response)
			{
				$("#profileProgress").hide();
			}
		});

		$('#profileForm').ajaxForm({
			target: '/client/saveProfile',
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$("#profileProgress").show();
				// Валидация перед отправкой
                var valid = validateProfileForm();

                if (!valid)
                    $("#profileProgress").hide();

				return validateProfileForm();
			},
			success: function(response)
			{
				if(response && (''+response).length>0)
				{
					$("#profileProgress").hide();
					error('top', response);
				}
				else
				{
					$("#profileProgress").hide();
					success('top', 'Персональные данные успешно сохранены!');
				}
			},
			error: function(response)
			{
				$("#profileProgress").hide();
				error('top', 'Заполните необходимые поля и сохраните еще раз.');
			}
		});
	});

    <?= editor('about', 170, 900, 'PackageComment') ?>
</script>