<div class="profile table dealer_tab" style="height: 700px;">
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<div class="dealer_profile_left">
		<form action="<?= $selfurl ?>addProductManualAjax" id="onlineItemForm" method="POST">
			<img src="<?= IMG_PATH ?>avatar_big.png" width="200px" height="200px">
			<br>
			<br>
			<a href="javascript:void(0);">изменить фото</a>
		</form>
	</div>
	<div class='profile_box admin-inside'>
		<form id="profileForm" action="/manager/saveProfile">
			<!--div class="cashback_box" style=" width: 337px; ">
				<span class="cashback_span" style=" float: left; width: 160px; margin-top: 4px;">
					Статус: 100% CASHBACK
				</span>
				<span class="label" style=" float: left; margin-top: -15px; width: 150px; margin-left: 7px;">
					<div class="submit floatright">
						<div>
							<input type="button" value="Заказать">
						</div>
					</div>
				</span>
				<br style="clear:both;" />
				<span class="label" style=" margin-left: 0; width: 170px; margin-bottom: 0; ">Лимит на заказы*:</span>
				<input style="width:155px;" class="textbox" maxlength="11" type='text' id='limit' name="limit" value="<?= $manager->cashback_limit ?>" />
			</div>
			<br style="clear:both;" /-->
			<div>
                <span class="label">Логин:</span>
                <span class="label" style="margin-left: 0;"><b><?= $manager->statistics->login ?></b></span>
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Пароль*:</span>
				<input style="width:180px;" class="textbox" maxlength="32" type='password' id='password' name="password" />
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Email*:</span>
				<input style="width:180px;" class="textbox" maxlength="128" type='text' id='email' name="email" value="<?= $manager->statistics->email ?>" />
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Имя или название компании*:</span>
				<input style="width:180px;" class="textbox" maxlength="255" type='text' id='fio' name="fio" value="<?= $manager->statistics->fullname ?>" />
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Сайт или тема на форуме:</span>
				<input style="width:180px;" class="textbox" maxlength="4096" type='text' id='website' name="website" value="<?= $manager->website ?>" />
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Skype:</span>
				<input style="width:180px;" class="textbox" maxlength="255" type='text' id='skype' name="skype" value="<?= $manager->skype ?>"/>
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Страна*:</span>
				<select id="country" name="country" class="textbox" onchange="$.fn.validateProfileCountry($(this))">
					<option value="0">выберите страну...</option>
					<? foreach ($Countries as $country) : ?>
					<option value="<?= $country->country_id ?>"  title="/static/images/flags/<?= $country->country_name_en ?>.png" <? if ($manager->manager_country == $country->country_id) : ?>selected<? endif; ?>><?= $country->country_name ?></option>
					<? endforeach; ?>
				</select>
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Город*:</span>
				<input style="width:180px;" class="textbox" maxlength="255" type='text' id='city' name="city" value="<?= $manager->city ?>"/>
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Принимать Mail Forwarding:</span>
				<input class="checkbox" type='checkbox' id='mf' name="mf" <? if ($manager->is_mail_forwarding) : ?>checked<? endif;?> />
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Принимать платежи от клиентов через Countrypost.ru:</span>
				<input class="checkbox" type='checkbox' id='payments' name="payments" <? if ($manager->is_internal_payments) : ?>checked<? endif; ?> />
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">О себе:</span>
			</div>
			<br style="clear:both;" />
			<div style="padding-left:10px;">
				<textarea maxlength="65535" id='about' name="about"><?= $manager->about_me ?></textarea>
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

        var validateProfileForm = function() {
            field = null,
                    errorCount = 0;

            field = $('#fio');
            if(field.val() == '')
            {
                $.fn.addProfileFieldError(field, 'Введите ваше имя или название компании');
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
                $.fn.addProfileFieldError($('#country_msdd'), 'Необходимо указать страну');
                errorCount++;
            }
            else
            {
                $.fn.removeProfileFieldError($('#country_msdd'));
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

            return (errorCount > 0) ? false : true;

        }

        $.fn.validateProfileCountry = function(field)
        {
            if(field.val() == 0 || field.val() == '')
            {
                $.fn.addProfileFieldError($('#country_msdd'), 'Необходимо указать страну');
                return false;
            }
            else
            {
                $.fn.removeProfileFieldError($('#country_msdd'));
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
        $('#fio').validate({
            expression: "if (VAL != '') return true; else return false;",
            message: "Введите ваше имя или название компании"
        });
        $('#password').validate({
            expression: "if (!(VAL != '' && VAL.length < 6)) return true; else return false;",
            message: "Пароль должен состоять из не менее 6-ти символов"
        });
        $('#email').validate({
            expression: "if (VAL.match(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/)) return true; else return false;",
            message: "Введите правильный email"
        });

		$('#profileForm').ajaxForm({
			target: '/manager/saveProfile',
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$("#profileProgress").show();

                var valid = validateProfileForm();

                if (!valid)
                    $("#profileProgress").hide();

                return valid;
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