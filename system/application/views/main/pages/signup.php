<div class='content signup'>
	<? Breadcrumb::showCrumbs(); ?>
	<h2>Регистрация</h2>
	<div class='table centered_td admin-inside floatleft box'>
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<table>
			<tr class="last-row">
				<td>
					<h3>Я покупатель</h3>
					<p>
						Если Вы хотите добавить заказ на online/offline покупку в магазине,
						торговой площадке, аукционе, заказать услугу, доставить посылку/груз
						и найти исполнителя/посредника/транспортную компанию из любой страны.
					</p>
						<div class="signup_button">
							<div class="submit">
								<div>
									<input type="button"
										   value="ЗАРЕГИСТРИРОВАТЬСЯ"
											onclick="window.location='<?= BASEURL ?>signup/client'">
								</div>
							</div>
						</div>
					<h3>
						Бесплатно
					</h3>
				</td>
				<td></td>
			</tr>
		</table>
	</div>
	<div class='table centered_td admin-inside box' style="float:right;">
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<table>
			<tr class="last-row">
				<td>
					<h3>Я посредник</h3>
					<p>
						Зарегистрируйтесь и получайте каждый день новые заказы
						из Вашей страны
						бесплатно.
						Также Вы сможете использовать автоматическую online систему обработки заказов.
						Не теряйте время на Exсel формах.
					</p>
					<div class="signup_button">
						<div class="submit">
							<div>
								<input type="button"
									   value="ЗАРЕГИСТРИРОВАТЬСЯ"
									   onclick="window.location='<?= BASEURL ?>signup/manager'">
							</div>
						</div>
					</div>
					<h3>
						Бесплатно
					</h3>
				</td>
				<td></td>
			</tr>
		</table>
	</div>
	<br style="clear: both;">
</div>