<? if (isset($this->user) AND $this->user):
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
                <p>Здравствуйте, <span class='big-text'><a href='<?=$this->user ? BASEURL.$this->user->user_group : BASEURL.'user/showRegistration';?>'><?=$this->user->user_login;?></a></span></p>
                <p><a href='/profile'>Изменить личные данные</a></p>
                <div class='submit'><div><input type='submit' value='Выйти' onclick="javascript:window.location='<?=BASEURL?>user/logout';" /></div></div>
                </div>
                <? if ( ! empty($partners)) : ?>
                <div class='right-block right-block-client'>
                    <!--h3>
                        Ваш адрес в: 
                    </h3>
                    <? foreach ($partners as $partner) : ?>
                    <p>
                        <strong style="text-transform:uppercase;" class='floatleft'>
                            <b><?= $partner->country_address2 ?>:&nbsp;&nbsp;</b>
                        </strong> 
                        <div style="width:335px;height:11px;" class='ellipsis floatleft'>
                        <?= $partner->manager_addres ?>
                        (<?= $this->user->user_id ?>)
                        </div> 
                        <a href='<?= $selfurl ?>showAddresses/<?= $partner->manager_user ?>' class='floatright'>Подробнее</a>
                    </p>
                    <br />
                    <? endforeach; ?>
                    <p>
                        <a href='<?= $selfurl ?>showAddresses'>Другие адреса</a>
                    </p-->
                </div>
                <? endif; ?>
                <div class='center-block'>
                    <h3>ВАШ НОМЕР НА САЙТЕ: <?= $this->user->user_id ?></h3>
					<br>
					<!--p>
						Баланс Countrypost.ru: <span class='big-text'><?=$this->user->user_coints;?> USD</span></p-->
                    <p>
						<a href='javascript:showBalanceWindow();'>Баланс по посредникам</a>
					</p>
                    <p>
						<a href='/<?= $this->user->user_group ?>/history'>Статистика платежей</a>
					</p>
                </div>
            </div>
        </div>
		<? View::show('/client/elements/payments/balance_block'); ?>
    <script>
        user = '<?= (!empty($this->user)) ? $this->user->user_group : '' ?>';
    </script>
    <? endif; ?>
<? else : ?>
<? endif; ?>