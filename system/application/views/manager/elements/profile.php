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
			<a href="javascript:void();">изменить фото</a>
		</form>
	</div>
	<div class='profile_box'>
		<form class='admin-inside' action="<?= $selfurl ?>addProductManualAjax" id="onlineItemForm" method="POST">
			<div class="cashback_box" style=" width: 338px; ">
				<span class="cashback_span" style=" float: left; width: 160px; ">
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
				<input style="width:155px;" class="textbox" maxlength="4096" type='text' id='olink' name="olink" />
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Логин*:</span>
				<input style="width:180px;" class="textbox" maxlength="4096" type='text' id='olink' name="olink" value="<?= $manager->statistics->login ?>" />
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Пароль*:</span>
				<input style="width:180px;" class="textbox" maxlength="255" type='password' id='oname' name="oname" />
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Email*:</span>
				<input style="width:180px;" class="textbox" maxlength="11" type='text' id='oprice' name="oprice" value="<?= $manager->statistics->email ?>" />
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Имя или название компании*:</span>
				<input style="width:180px;" class="textbox" maxlength="11" type='text' id='odeliveryprice' name="odeliveryprice" value="<?= $manager->statistics->fullname ?>" />
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Сайт или тема на форуме:</span>
				<input style="width:180px;" class="textbox" maxlength="255" type='text' id='oweight' name="oweight" value="<?= $manager->website ?>" />
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Skype:</span>
				<input style="width:180px;" class="textbox" maxlength="255" type='text' id='oweight' name="oweight" value="<?= $manager->skype ?>"/>
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Страна*:</span>
				<input style="width:180px;" class="textbox" maxlength="255" type='text' id='oweight' name="oweight" />
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Город*:</span>
				<input style="width:180px;" class="textbox" maxlength="255" type='text' id='oweight' name="oweight" />
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Принимать Mail Forwarding*:</span>
				<input class="checkbox" maxlength="255" type='checkbox' id='oweight' name="oweight" value="<?= $manager->is_mail_forwarding ?>" />
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">Принимать платежи от клиентов через Countrypost.ru:</span>
				<input class="checkbox" maxlength="255" type='checkbox' id='oweight' name="oweight" value="<?= $manager->is_internal_payments ?>" />
			</div>
			<br style="clear:both;" />
			<div>
				<span class="label">О себе:</span>
			</div>
			<br style="clear:both;" />
			<div>
				<textarea maxlength="65535" id='about_me' name="about_me"><?= $manager->about_me ?></textarea>
			</div>
			<br style="clear:both;" />
		</form>
	</div>
</div>