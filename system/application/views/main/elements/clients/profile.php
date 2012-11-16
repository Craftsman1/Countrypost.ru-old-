<div class="profile table client_tab" style="height: 131px;">
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<div class="personal table">
		<div>
			<span>
				Страна:
			</span>
			<span>
				<img src="/static/images/flags/<?= $countries_en[$client->client_country] ?>.png" />
				<b><?=$countries[$client->client_country]?></b>
			</span>
			</div>
			<div>
			<span>
				Зарегистрирован:
			</span>
			<span>
				<?= isset($client->created) ? date('d.m.Y H:i', strtotime($client->created)) : date('d.m.Y H:i')?>
			</span>
		</div>
		<div>
		<span>
			Отзывы:
		</span>
		<span>
			<? View::show('main/elements/clients/reviews', array(
				'positive' =>  $client->statistics->positive_reviews,
				'neutral' =>  $client->statistics->neutral_reviews,
				'negative' =>  $client->statistics->negative_reviews
			)); ?>
		</span>
		</div>
		<? if ( ! empty($client->statistics->skype)) : ?>
		<div>
            <span>
                Skype:
            </span>
            <span>
                <?= $client->statistics->skype ?>
            </span>
		</div>
		<? endif; ?>
		<? if (isset($this->user->user_group) AND ($this->user->user_group == 'manager' OR ($this->user->user_group == 'client' AND $this->user->user_id == $client->client_user))) : ?>
		<div>
            <span>
                Email:
            </span>
            <span>
                <a href="mailto:<?= $client->statistics->email ?>"><?= $client->statistics->email ?></a>
            </span>
		</div>
		<? endif; ?>
	</div>
</div>