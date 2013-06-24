<div class='content'>
	<? Breadcrumb::showCrumbs(); ?>
	<form id='registration'
		  name='registration'
		  class='registration'
		  action='<?= $this->config->item('base_url') ?>signup/signupDealer'
		  method="POST">
		<h2 style="left:50px;position:relative;">регистрация</h2>
		<br>
		<p style="left:-33px;position:relative;">
			Зарегистрируйтесь и получайте каждый день новые заказы.
		</p>
		<em id='errortext' class='red-color'>
			<?= $result->m ?>&nbsp;
		</em>
		<br />
		<div id='login' class='field <?= (isset($result->e) && ($result->e == -17 OR $result->e == -1)) ?
			'error' :
			(empty($result->d->user_login) ?
				'' :
				'done') ?>'>
			<span>Логин* :</span>
			<div class='text-field'>
				<div>
					<input type='text'
						   name="login"
						   value="<?= empty($result->d->user_login) ? '' : $result->d->user_login ?>" />
				</div>
			</div>
		</div>
		<div class='field <?= $result->d && $result->d->user_password ? 'done' :'' ?>' id='password_div'>
			<span>Пароль* :</span>
			<div class='text-field'>
				<div>
					<input type='password'
						   id="password"
						   name="password"
						   value="<?= $result->d ?
						$result->d->user_password :'' ?>" />
				</div>
			</div>
		</div>
		<div id='email_div'
			 class='field <?= $result->d && $result->d->user_email && $result->e != -13 ? 'done' : '' ?>' >
			<span>E-mail* :</span>
			<div class='text-field'>
				<div>
					<input type='text'
						   id='email'
						   name="email"
						   value="<?= $result->d ? $result->d->user_email : '' ?>" />
				</div>
			</div>
		</div>
		<div id='fio_div'
			 class='field <?= ($result->d  AND
				 $manager AND
				 $manager->manager_name AND
				 $result->e != -100) ? 'done' : '' ?>' >
			<span>Имя или название компании* :</span>
			<div class='text-field'>
				<div>
					<input type='text'
						   id='fio'
						   name="fio"
						   value="<?= ($result->d AND isset($manager)) ? $manager->manager_name : '' ?>" />
				</div>
			</div>
		</div>
		<div class='field <?= $result->d && $manager->manager_country ? 'done' : '' ?>' id='country'>
			<span>Страна* :</span>
			<select class="select" name="country">
				<option value="0">выбрать&hellip;</option>
				<? if ($Countries) : foreach ($Countries as $country) : ?>
					<option value="<?= $country->country_id ?>" <?= (isset($manager)
						AND $manager->manager_country == $country->country_id) ?
						'selected' :
						'' ?>><?= $country->country_name ?></option>
				<? endforeach; endif; ?>
			</select>
		</div>
		<div id='city_div'
			 class='field <?= ($result->d  AND $manager AND $manager->city AND $result->e != -300) ? 'done' :
				 ''?>' >
			<span>Город* :</span>
			<div class='text-field'>
				<div>
					<input type='text'
						   id='city'
						   name="city"
						   maxlength="255"
						   value="<?= ($result->d AND isset($manager)) ? $manager->city : '' ?>" />
				</div>
			</div>
		</div>
		<!--div class='hr'></div>
		<div class='captcha'>
			<img src='<?= $this->config->item('base_url') . 'signup/showCaptchaImage/' . rand(0, 255) ?>'>
		</div>
		<div id='captcha' class='field'>
			<span>Введите текст на картинке* :</span>
			<div class='text-field'>
				<div>
					<input type='text' id='captchacode' name='captchacode' value=''>
				</div>
			</div>
		</div-->
		<div class='field <?= empty($result->terms_accepted) ? '' : 'done' ?>'
			 id='terms'>
			<span></span>
			<div>
				<input class="checkbox"
					   type="checkbox"
					   name="terms_accepted"
						<?= empty($result->terms_accepted) ?  '' : 'checked' ?>>
				<span style="text-align:left;width: 70px;margin: 5px 0px 0 0;">Я согласен с</span>
				<span style="text-align:left;width: 70px;margin: 5px 0px 0 0;">
					<a style="text-indent:0px;" href='/terms' target='_blank'>Правилами</a>
				</span>
			</div>
		</div>
		<div class='submit'>
			<div>
				<input type='submit' value='Регистрация' />
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	$(function()
	{
		$('input[type=text],input[type=checkbox],input[type=password],select').change(function() {
			onfieldchange($(this).attr('name'));
		});
	});

	function onfieldchange(element)
	{
		$.ajax({
			type: 'POST',
			url: '/signup/validateDealerAjax',
			data: $("#registration").serialize(),
			success: function(data) {
				var d = $.parseJSON(data);

				// капча
				/*if (d.code == -18 || d.code == -5)
				{
					$('div#captcha')
						.toggleClass('done', false)
						.toggleClass('error', true);
				}
				else if (element == 'captchacode')
				{
					$('div#captcha')
						.toggleClass('done', true)
						.toggleClass('error', false);
				}
*/
				// логин
				if (d.code == -17 || d.code == -1)
				{
					$('div#login')
						.toggleClass('done', false)
						.toggleClass('error', true);
				}
				else if (element == 'login')
				{
					$('div#login')
						.toggleClass('done', true)
						.toggleClass('error', false);
				}

				// страна
				if (d.code == -25)
				{
					$('div#country')
						.toggleClass('done', false)
						.toggleClass('error', true);
				}
				else if (element == 'country')
				{
					$('div#country')
						.toggleClass('done', true)
						.toggleClass('error', false);
				}

				// город
				if (d.code == -300)
				{
					$('div#city_div')
						.toggleClass('done', false)
						.toggleClass('error', true);
				}
				else if (element == 'city')
				{
					$('div#city_div')
						.toggleClass('done', true)
						.toggleClass('error', false);
				}

				// фио
				if (d.code == -100)
				{
					$('div#fio_div')
						.toggleClass('done', false)
						.toggleClass('error', true);
				}
				else if (element == 'fio')
				{
					$('div#fio_div')
						.toggleClass('done', true)
						.toggleClass('error', false);
				}

				// email
				if (d.code == -16 || d.code == -2)
				{
					$('div#email_div')
						.toggleClass('done', false)
						.toggleClass('error',true);
				}
				else if (element == 'email')
				{
					$('div#email_div')
						.toggleClass('done', true)
						.toggleClass('error', false);
				}

				// условия
				if (d.code == -33)
				{
					$('div#terms')
						.toggleClass('done', false)
						.toggleClass('error', true);
				}
				else if (element == 'terms_accepted')
				{
					$('div#terms')
						.toggleClass('done', true)
						.toggleClass('error', false);
				}

				// пароль
				if (d.code == -3 || d.code == -200)
				{
					$('div#password_div')
						.toggleClass('done', false)
						.toggleClass('error', true);
				}
				else if (element == 'password')
				{
					$('div#password_div')
							.toggleClass('done', true)
							.toggleClass('error', false);
				}

				// сообщение об ошибке
				$('#errortext').empty().append(d.text);
  			}
		});
	}
</script>