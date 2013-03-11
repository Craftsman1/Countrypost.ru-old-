<? if ( ! isset($result)){
	$result = new stdClass();
	$result->e = '';
	$result->m = '';
	$result->d = '';
} ?>
<div class='content'>
	<? Breadcrumb::showCrumbs(); ?>
	<form name='registration' class='registration' method="post" action='<?= BASEURL ?>user/passwordRecovery'>
		<input type='hidden' name='country' value='' id='country' />
		<h2 style="left:-19px;position:relative;">восстановление пароля</h2>
		<br>
		<p style="left:-46px;position:relative;">Пароль будет выслан на указаный Вами адрес электронной почты.</p>
		<div class='field'>
			<span>E-mail:</span>
			<div class='text-field'>
				<div>
					<input type='text' name="email" value="<?= $result->d ? $result->d->user_email : '' ?>">
				</div>
			</div>
		</div>
		<? if ($result->m) : ?>
		<p style="margin-left: 317px;">
			<?= $result->m ?>
		</p>
		<? endif; ?>
		<div class='submit'>
			<div>
				<input type='submit' value='Восстановить' />
			</div>
		</div>
	</form>
</div>