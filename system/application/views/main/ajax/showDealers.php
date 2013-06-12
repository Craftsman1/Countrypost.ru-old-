<div id="partnersFormContainer">
    <form id="partnersForm" class='admin-inside' action='#'>
        <div class="search_results">
            <span class="total" style="float: none;">
                Найдено посредников: <b id="managers_count"><?= $this->paging_count ?></b>
            </span>
            <span class="total" style="margin:0 0 0px 0;">
                <label>посредников на странице:</label>
				<? View::show('main/elements/per_page', array(
					'handler' =>  'dealers'
				)); ?>
            </span>
        </div>
        <br>
        <br>
        <div class='table centered_td centered_th'>
            <div class='angle angle-lt'></div>
            <div class='angle angle-rt'></div>
            <div class='angle angle-lb'></div>
            <div class='angle angle-rb'></div>
            <table>
                <col width='auto' />
                <col width='auto' />
                <col width='auto' />
                <col width='auto' />
                <col width='auto' />
                <col width='auto' />
                <col width='auto' />
                <tr>
                    <th>Рейтинг / №</th>
                    <th>Страна</th>	
                    <th>Посредник</th>
                    <th>Отзывы</th>
                    <th>Сайт</th>
                    <th>Выполненных&nbsp;заказов</th>
                    <th>Профиль</th>
                </tr>
                <? if ($managers): foreach ($managers as $manager) : ?>
                    <tr>
                        <td>
                            <b style=""><?= $manager->rating ?></b>
<<<<<<< HEAD
                        </td>
                        <td style="text-align:left;">
                            <span>
                                <a href="<?=BASEURL.$manager->statistics->login;?>"><img style="width:48px; height:48px;" src="/main/avatar_big/<?= $manager->manager_user; ?>" /></a>
                            </span>
                            <span style="display: inline-block; position: relative; margin-left: 7px;">
                                <a href="<?=BASEURL.$manager->statistics->login;?>"><?= $manager->statistics->fullname ?></a>
                                (<?=$manager->user_login;?>)
                                <div style="margin-top: 17px;">
                                <? if ($manager->is_cashback OR $manager->is_mail_forwarding) : ?>
                                    <? if ($manager->is_cashback) : ?>
                                        <div>
                                        <b class="cashback">100% CASHBACK</b>
                                        </div>
                                        <? endif; ?>
                                        <? if ($manager->is_mail_forwarding) : ?>
                                        <div>
                                        <b class="mf">Принимает заказы MailForwarding</b>
                                        </div>
                                    <? endif; ?>
                                <? endif; ?>
                                </div>
                            </span>
=======
                            <br>
                            <b style="color:#D7D7D7;">№ <?=$manager->manager_user?></b>
>>>>>>> parent of 6c2ba62... Задачи: 16+37+35+33+30+31
                        </td>
                        <td>
                            <img src="/static/images/flags/big/<?= $countries_en[$manager->manager_country] ?>.png" style="float:left;margin-right:10px;" />
                            <!--b style="position:relative;top:17px;"><?=$countries[$manager->manager_country]?></b-->
							<?= shortenCountryName($countries[$manager->manager_country], 'position:relative;top:17px;') ?>
						</td>
                        <td style="text-align:left;">
                            <?= $manager->statistics->fullname ?>
                            <? if ($manager->is_cashback OR $manager->is_mail_forwarding) : ?>
							<br>
							<? if ($manager->is_cashback) : ?>
							<b class="cashback">100% CASHBACK</b>
							<? endif; ?>
							<? if ($manager->is_mail_forwarding) : ?>
							<b class="mf">MF</b>
							<? endif; ?>
							<? endif; ?>
                        </td>
                        <td>
							<? View::show('main/elements/ratings/reviews', array(
								'positive' =>  $manager->statistics->positive_reviews,
								'neutral' =>  $manager->statistics->neutral_reviews,
								'negative' =>  $manager->statistics->negative_reviews));
							?>
                        </td>
                        <td>
                            <a target="_blank" href="<?= empty($manager->website) ? BASEURL.$manager->statistics->login : $manager->website ?>"><?= empty($manager->website) ? BASEURL.$manager->statistics->login : $manager->website ?></a>
                        </td>
                        <td>
							<?= $manager->statistics->completed_orders ?>
						</td>
                        <td>
                            <a href='<?= BASEURL.$manager->statistics->login ?>'>посмотреть</a>
                        </td>
                    </tr>
                    <? endforeach; ?>
                	<? else : ?>
                    <tr>
                        <td colspan='9'>Посредники не найдены.</td>
                    </tr>
                <?endif;?>
                <tr class='last-row'>
                    <td colspan='9'>
                        <div class='float'>&nbsp;
                        </div>
                    </td>
                    <td></td>
                </tr>
            </table>
        </div>
        <div class="search_results">
            <span class="total" style="float: none;">
                Найдено посредников: <b id="managers_count"><?= $this->paging_count ?></b>
            </span>
            <span class="total" style="margin:0;">
                <label>посредников на странице:</label>
				<? View::show('main/elements/per_page', array(
					'handler' =>  'dealers'
				)); ?>
            </span>
        </div>
        <?= $pager ?>
    </form>
</div>