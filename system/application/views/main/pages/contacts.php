<div class="content">
	<h2>Наши контакты</h2>
	<div class='table'>
					<div class='angle angle-lt'></div>
					<div class='angle angle-rt'></div>
					<div class='angle angle-lb'></div>
					<div class='angle angle-rb'></div>
	<b><p>По общим вопросам работы сайта и платежам:</p></b>
	<p>Тел. +7 (495) 956-88-50 доб. 293406</p>
	<p>Факс +7 (495) 956-82-62 доб. 293406</p>
	<p><a href="mailto:info@countrypost.ru">info@countrypost.ru</a></p>
	<p>Skype: <a href="skype:country_post">country_post</a></p>
	<b><p>По вопросам выполнения заказов и отправки посылкок просьба обращаться к ващему менеджеру. Узнать кто ваш менеджер можно <a href="/client/showAddresses">тут</a> (чтобы перейти по ссылке войдите в ваш аккаунт).</p></b> 
	<br />
	<br />
  	<b>Также Вы можете задать нам вопрос, воспользовавшись формой, указанной ниже:</b>
	<br /><br />
	<div class="contacts">
	<form name='contactsForm' class='registration' action='<?=$this->config->item('base_url')?>main/contactUs' method="POST">
		<? $result = Stack::last('contactResult'); 
			Stack::clear('contactResult'); 
			if (isset($result) && $result) : if ($result->e) : ?>
		<em style="color:red!important;margin-left:0;"><?=$result->m?></em>
		<br />
			<? else: ?>
		<em style="color:green!important;margin-left:0;"><?=$result->m?></em>
		<br />
		<? endif; endif; ?>
		<?if (!$user){?>
		Фамилия, Имя:
		<div class='field'>
			<div class='text-field'><div><input type='text' name="fio" maxlength="128" value="" /></div></div>
		</div>
		Email:
		<div class='field'>
			<div class='text-field'><div><input type='text' name="email" maxlength="128" value="" /></div></div>
		</div>
		Телефон:</span>
		<div class='field'>
			<div class='text-field'><div><input type='text' name="phone" maxlength="20" value="" /></div></div>
		</div>
		<?}?>
		<div class='add-comment'>
			<textarea class="textarea"  maxlength="1024" name="message"/></textarea>
		</div>
		<br />
		<div class='captcha'><img src='<?=$this->config->item('base_url').'user/showCaptchaImage/'.rand(0,255)?>' /></div>
		Введите текст на картинке:
		<div class='field'>
			<div class='text-field'><div><input type='text' name='captchacode' value='' /></div></div>
		</div>
		<br />
		<div class='submit'><div><input type='submit' value='Отправить' /></div></div>
	</form>
			</ul>
		</div>
	</div>
</div>