<div id="partnersFormContainer">
    <form id="partnersForm" class='admin-inside' action='#'>
        <a name="pagerScroll"></a>
        <div class="search_results">
            <span class="total" style="float: none;">
                Найдено клиентов: <b id='clients_count'><?= $this->paging_count ?></b>
            </span>
            <span class="total" style="margin:0 0 0px 0;">
                <label>клиентов на странице:</label>
				<? View::show('main/elements/per_page', array(
					'handler' =>  'clients'
				)); ?>
            </span>
        </div>
        <br>
        <br>
        <div class='table'>
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
                    <th>№</th>
                    <th style="width:300px;">Клиент</th>
                    <th>Страна</th>
                    <th>Отзывы</th>
                    <th>Всего&nbsp;заказов</th>
                </tr>
                <style>
                    #partnersForm td,#partnersForm th
                    {
                        text-align:center;
                        vertical-align:middle;
                        text-wrap: nowrap;
                    }
                </style>
                <? if ($clients): foreach ($clients as $client):?>
				<tr>
					<td>
						<b style="color:#D7D7D7;"><?=$client->client_user?></b>
					</td>
                    <td style="text-align:left;">
                        <span>
                            <a target="_blank" href="<?= empty($client->website) ? $this->config->item('base_url').$client->statistics->login : $client->website ?>"><img style="width:48px; height:48px;" src="/main/avatar_big/<?= $client->client_user ?>" /></a>
                        </span>
                        <span style="display: inline-block; position: relative; margin-top: 15px; margin-left: 7px;width: 200px;">
                        <a target="_blank" href="<?= empty($client->website) ? $this->config->item('base_url').$client->statistics->login : $client->website ?>"><?=$client->client_name?></a> (<?=$client->statistics->login?>)
                        </span>
                    </td>
					<td style="text-align: left; width: 200px;">
                        <img src="/static/images/flags/big/<?= $countries_en[$client->client_country] ?>.png" />
						<span style="display: inline-block; margin: 17px 0px 0px 5px;" class="overflow_hidden">
                        <?= //shortenCountryName($countries[$client->client_country], '')
						$countries[$client->client_country]
						?>
                        <? if ($client->client_town) : ?>
                            (<span title="<?=$client->client_town;?>"><?=$client->client_town;?></span>)
                        <? endif; ?>
                        </span>
					</td>
					<td>
						<div style="margin-left: 35px;">
                        <? View::show('main/elements/clients/reviews', array(
							'positive' =>  $client->statistics->positive_reviews,
							'neutral' =>  $client->statistics->neutral_reviews,
							'negative' =>  $client->statistics->negative_reviews
						)); ?>
                        </div>
					</td>
					<td><?=$client->statistics->order_count;?></td>
				</tr>
				<?endforeach;?>
                <?else:?>
				<tr>
					<td colspan=9>Клиенты не найдены.</td>
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
                Найдено клиентов: <b id="clients_count"><?= $this->paging_count ?></b>
            </span>
            <span class="total" style="margin:0;">
                <label>клиентов на странице:</label>
				<? View::show('main/elements/per_page', array(
					'handler' =>  'clients'
				)); ?>
            </span>
        </div>
        <? if (isset($pager)) echo $pager ?>
    </form>
</div>