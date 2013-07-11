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
        <a id="btnMore" style="cursor: pointer;">Показать еще 5 новостей</a>
    <? endif ?>
</div>
<script>
    $(function() {

        var start = 5;
        var all_message = <?=$blogs_allcount?>;
        $('#btnMore').click(function(){
            var count = 5;
            if ( (start+count) >= all_message ) $("#btnMore").hide();
            $.ajax({
                type: "POST",
                url: "/profile/getMoreBlogAjax/<?=$manager_user?>/"+start+"/"+count,
                success: function(result){
                    start = start + count;
                    result = JSON.parse(result);
                    for (var i = 0; i < result.length; i++) {
                        var date = (result[i].created).replace(/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)/,'$3.$2.$1 $4:$5');
                        var news_snippet = '<div id="table_'+result[i].blog_id+'" style="margin-top: 20px;" class="table"><div class="angle angle-lt"></div><div class="angle angle-rt"></div><div class="angle angle-lb"></div><div class="angle angle-rb"></div><div><span class="label">' +
                            date +
                            '</span> <span class="label"><b>' +
                            '</b>'+

                            '</span></div><div>' +
                            htmlUnescape(result[i].message)+
                            '</div></div>';
                        $('#insert_more_message').before(news_snippet);
                    }
                }
            });
        })

        function htmlUnescape(value){
            return String(value)
                .replace(/&quot;/g, '"')
                .replace(/&#39;/g, "'")
                .replace(/&lt;/g, '<')
                .replace(/&gt;/g, '>')
                .replace(/&amp;/g, '&');
        }

    })
</script>