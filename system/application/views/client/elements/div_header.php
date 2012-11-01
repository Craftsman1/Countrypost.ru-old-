<? if (isset($user) AND $user):
	// Проверяем раздел происхождения запроса в рамках типа аккаунта пользователя
	if (!empty($segment) AND !empty($allowed_segments) AND !in_array($segment, $allowed_segments)) :
	?>
        <div style='display:none;'>
            <script>
                $(function() {
                    window.location = '/';
                });
            </script>
        </div>
	<? else : ?>
        <div class='top-block'>
            <div class='block-user'>
                <div class='left-block'>
                <h2>Авторизация</h2>
                <p>Здравствуйте, <span class='big-text'><a href='<?=$user ? BASEURL.$user->user_group : BASEURL.'user/showRegistration';?>'><?=$user->user_login;?></a></span></p>
                <p><a href='/user/showProfile'>�?зменить личные данные</a></p>
                <div class='submit'><div><input type='submit' value='Выйти' onclick="javascript:window.location='<?=BASEURL?>user/logout';" /></div></div>
                </div>
                <? if ( ! empty($partners)) : ?>
                <div class='right-block right-block-client'>
                    <h3>
                        Ваш адрес в: 
                    </h3>
                    <? foreach ($partners as $partner) : ?>
                    <p>
                        <strong style="text-transform:uppercase;" class='floatleft'>
                            <b><?= $partner->country_address2 ?>:&nbsp;&nbsp;</b>
                        </strong> 
                        <div style="width:335px;height:11px;" class='ellipsis floatleft'>
                        <?= $partner->manager_addres ?>
                        (<?= $user->user_id ?>) 
                        </div> 
                        <a href='<?= $selfurl ?>showAddresses/<?= $partner->manager_user ?>' class='floatright'>Подробнее</a>
                    </p>
                    <br />
                    <? endforeach; ?>
                    <p>
                        <a href='<?= $selfurl ?>showAddresses'>Другие адреса</a>
                    </p>
                </div>
                <? endif; ?>
                <div class='center-block'>
                    <h3>ВАШ НОМЕР НА САЙТЕ: <?= $user->user_id ?></h3>
                    <p>Общий баланс: <span class='big-text'>$<?=$user->user_coints;?></span></p>
                    <p><a href='/<?=$user->user_group?>/showAddBalance'>Пополнить</a></p>
                    <p>(<a href='/syspay/showPays/' class='anthracite-color'>Как пополнить?</a>)</p>
                    <p><a href='/<?=$user->user_group?>/showPaymentHistory'>Статистика платежей</a></p>
                    <p><a href='/<?=$user->user_group?>/showOutMoney'>Заявка на вывод денег</a></p>
                </div>
            </div>
        </div>
    <? endif; ?>
<? else : ?>
<!--	<div class='top-block'>
		<form class='block-user autorization-inner' action='<?=BASEURL?>user/login' method="POST">
			<h2>Авторизация</h2>
			<div class='text-field'><div><input name="login" type='text' value='Логин' onfocus='javascript: if (this.value == "Логин") this.value = "";' onblur='javascript: if (this.value == "") this.value = "Логин";' /></div></div>
			<div class='text-field'><div><div class='password'><input name="password" type='password' value='Пароль' onfocus='javascript: if (this.value == "Пароль") this.value = "";' onblur='javascript: if (this.value == "") this.value = "Пароль";' /></div></div></div>
			<div class='submit'><div><input type='submit' value='Войти' /></div></div>
			<a href='<?=BASEURL?>user/showRegistration' class='registration'>Регистрация</a>
			<a href='<?=BASEURL?>user/showPasswordRecovery' class='remember-password'>Напомнить</a>
		</form>
	</div>
-->
<? endif; ?>