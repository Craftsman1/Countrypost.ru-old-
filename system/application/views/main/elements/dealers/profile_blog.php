<h3>Новости и объявления</h3>
<? if ($blogs) : ?>
    <? foreach ($blogs as $blog) : ?>
    <div class="table" style="margin-top: 20px;">
        <div class='angle angle-lt'></div>
        <div class='angle angle-rt'></div>
        <div class='angle angle-lb'></div>
        <div class='angle angle-rb'></div>
        <div>
			<span class="label">
				<?= isset($blog->created) ? date('d.m.Y H:i', strtotime($blog->created)) : '' ?>
			</span>
        </div>
        <div>
            <?= html_entity_decode($blog->message) ?>
        </div>
    </div>
    <? endforeach; ?>
<? else: ?>
    <div class="table">
        <div class='angle angle-lt'></div>
        <div class='angle angle-rt'></div>
        <div class='angle angle-lb'></div>
        <div class='angle angle-rb'></div>
        <p>
            Нет новостей.
        </p>
    </div>
<? endif ?>
<div id="insert_more_message" style="text-align: center; margin-top: 20px;">
    <? if ( (count($blogs) < $blogs_allcount) && count($blogs) > 0 ) : ?>
        <a id="btnMore" style="cursor: pointer; font-size: medium;">Показать еще 5 новостей</a>
    <? endif ?>
</div>