<form id='registration' name='registration' class='registration' action='<?=BASEURL?>user/registration' method="POST">
	<h2 style="left:50px;position:relative;">регистрация</h2>
	<p style="left:-10px;position:relative;">Все поля заполняются только латинскими буквами</p>
	<em id=errortext style="color:red !important"><?=$result->m?></em>
	<br />
	<div id=login class='field <?=$result->d && $result->d->user_login && $result->e != -17  ? 'done' :'';?>'>
		<span>Логин:</span>
		<div class='text-field'><div><input type='text' name="login" value="<?=$result->d ? $result->d->user_login :'';?>" /></div></div>
	</div>
	<div class='field <?=$result->d && $result->d->user_password ? 'done' :'';?>' id='password_div'>
		<span>Пароль:</span>
		<div class='text-field'><div><input type='password' id="password" name="password" value="<?=$result->d ? $result->d->user_password :'';?>" /></div></div>
	</div>
	<div class='field <?=$result->d && $result->d->repassword ? 'done' :'';?>' id='repassword_div'>
		<span>Повторите пароль:</span>
		<div class='text-field'><div><input type='password' id="repassword" name="repassword" value="<?=$result->d ? $result->d->repassword :'';?>" /></div></div>
	</div>
	<div id=email class='field <?=$result->d && $result->d->user_email && $result->e != -13 ? 'done' :'';?>' >
		<span>E-mail:</span>
		<div class='text-field'><div><input type='text' id=email name="email" value="<?=$result->d ? $result->d->user_email :'';?>" /></div></div>
	</div>
	<div class='field <?=$result->d && $client->client_country ? 'done' :'error';?>' id='country'>
		<span>Страна:</span>
		<select class="select" name="country">
			<option value="0">не выбрано&hellip;</option>
			<?if ($Countries):foreach($Countries as $country):?>
				<option value="<?=$country->country_id?>" <?=isset($client)&&$client->client_country==$country->country_id?'selected':''?>><?=$country->country_name?></option>
			<?endforeach;endif;?>
		</select>
	</div>
	<div class='hr'></div>
	<div class='captcha'>
		<img src='<?=BASEURL.'user/showCaptchaImage/'.rand(0,255)?>' />
	</div>
	<div id=captcha class='field'>
		<span>Введите текст на картинке:</span>
		<div class='text-field'><div><input type='text' id='captchacode' name='captchacode' value='' /></div></div>
	</div>
	<div class='field <?= (isset($result->terms_accepted) AND $result->terms_accepted) ? 'done' : 'error' ?>' id='terms'>
		<span></span>
		<div>
			<input class="checkbox" type="checkbox" name="terms_accepted" <?= isset($result->terms_accepted) && $result->terms_accepted ? 'checked' : '' ?>> 
			<span style="text-align:left;width: 70px;margin: 5px 0px 0 0;">Я согласен с</span>
			<span style="text-align:left;width: 70px;margin: 5px 0px 0 0;"><a style="text-indent:0px;" href='/terms' target='_blank'>Правилами</a></span>
		</div>
	</div>

	<div class='submit'><div><input type='submit' value='Регистрация' /></div>
	</div>
	
</form>
<script type="text/javascript">
	function onfieldchange() {
	$.ajax({
		  type: 'POST',
		  url: '/user/checkRegFields',
		  data: $("#registration").serialize(),
		  success: function (data) { 
		  	var d = $.parseJSON(data);
		  	if( d.code==-18 && $('input#captchacode').val()=='') 
			{
			  	d.text = 'Текст на кантринке не введен.';
		  	}
		  	if( d.code==-18 || d.code==-5) $('div#captcha').toggleClass('done',false).toggleClass('error',true);
		  	
		  	if( d.code==-11 ) d.text = '';
		  	if (d.code==-17 || d.code==-1) $('div#login').toggleClass('done',false).toggleClass('error',true);
		  	if( d.code==-16 || d.code==-2) $('div#email').toggleClass('done',false).toggleClass('error',true);
		  	if( d.code==-33 ) $('div#terms').toggleClass('done',false).toggleClass('error',true);
			else $('div#terms').toggleClass('done',true).toggleClass('error',false);
		  	if( d.code==-3 ) $('div#password_div').toggleClass('done',false).toggleClass('error',true);
		  	if( d.code==-4  || d.code==-15) $('div#repassword_div').toggleClass('done',false).toggleClass('error',true);
		  	if( d.code==-25 ) $('div#country').toggleClass('done',false).toggleClass('error',true);
		  	else $('div#country').toggleClass('done',true).toggleClass('error',false);
		  	
		  	$('#errortext').empty().append(d.text); 
		  }
		});
	}

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

	$(document).ready(function() {
		$('input[type=text],input[type=password]').change(function() {
			var regnum = /[0-1]/;
			var reglatin1 = /[а-я]/;
			var reglatin2 = /[А-Я]/;
			var regemail = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
			
			if ($(this).val() == '' || 
				reglatin1.test($(this).val()) ||
				reglatin2.test($(this).val()) ||
				($(this).attr('name') == 'email' && !regemail.test($(this).val())))
			{
				$(this).parent().parent().parent().removeClass('done').addClass('error');
			}
			else
			{
				if (($(this).attr('name') != 'phone_country' && 
					$(this).attr('name') != 'phone_city' && 
					$(this).attr('name') != 'phone_value') ||
					($('#phone_country').val() && $('#phone_city').val() && $('#phone_value').val()))
				{
					$(this).parent().parent().parent().removeClass('error').addClass('done');
				}
				else
				{
					$(this).parent().parent().parent().removeClass('done').addClass('error');
				}
			}
		});

		$('input[type=password]').change(function() {
			var firstPass = $('#password');
			var secondPass = $('#repassword');
			if (firstPass.val() && secondPass.val())
			{
				if (firstPass.val() != secondPass.val())
				{
					secondPass.parent().parent().parent().removeClass('done').addClass('error');
				}
				else
				{
					secondPass.parent().parent().parent().removeClass('error').addClass('done');
				}
			}
		});
		
		$('input[type=text],input[type=checkbox],input[type=password]').change();

		$('input[type=text],input[type=checkbox],input[type=password],select').change(function() {
			onfieldchange();
		});
	});
</script>