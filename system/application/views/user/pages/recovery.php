<?
if (!isset($result)){
	$result=new stdClass();
	$result->e='';
	$result->m='';
	$result->d='';
}?>
<form name='registration' class='registration' method="post" action='<?=BASEURL?>user/passwordRecovery'>
	<input type='hidden' name='country' value='' id='country' />
	<h2>восстановление пароля</h2>
	<p>Пароль будет выслан на указаный Вами адрес электронной почты</p>
	<div class='field done'>
		<span>E-mail:</span>
		<div class='text-field'><div><input type='text' name="email" value="<?=$result->d ? $result->d->user_email :'';?>" /></div></div>
	</div>
	<?if ($result->m):?>
		<p><?=$result->m?></p>
	<?endif;?>
	<div class='submit'><div><input type='submit' value='Восстановить' /></div></div>
</form>