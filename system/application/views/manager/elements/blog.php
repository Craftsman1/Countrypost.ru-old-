<div class="blog dealer_tab" style="display:none;">
	<form action="/manager/saveBlog" id="blogForm" method="POST">
		<div class="table" style="height: 303px;">
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<div class="blog_box admin-inside">
				<input type="hidden" name="blog_id" />
				<br style="clear:both;" />
				<div id="textarea_point">
					<span class="label">Текст новости*:</span>
				</div>
				<br style="clear:both;" />
				<div>
					<textarea maxlength="65535" id='message' name="message"></textarea>
				</div>
			</div>
		</div>
		<br style="clear:both;" />
		<div class="submit floatleft">
			<div>
				<input id="btnSubmit" type="submit" value="Добавить новость">
			</div>
		</div>
		<img class="float" id="blogProgress" style="display:none;margin:0px;margin-top:4px;" src="/static/images/lightbox-ico-loading.gif"/>
	    <input hidden="hidden" value="0" name="message_edit" id="message_edit">
    </form>
	<br style="clear:both;" />
	<h3 id="news_header">Все новости</h3>
	<? if ($blogs) : foreach ($blogs as $blog) : ?>
	<div class="table" id="table_<?=$blog->blog_id?>" style="margin-top: 20px;">
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<div>
			<span class="label">
				<?= isset($blog->created) ? date('d.m.Y H:i', strtotime($blog->created)) : '' ?>
                <span style="float: right;" id="message_<?=$blog->blog_id?>">
                    <img class="edit_news" style="cursor:pointer; display: block;" src="static/images/comment-edit.png">
                    <img class="delete_news" style="cursor:pointer;" src="static/images/delete.png">
                </span>
			</span>
		</div>
		<div>
			<?= html_entity_decode($blog->message) ?>
		</div>
	</div>
	<? endforeach; endif; ?>
    <div id="insert_more_message" style="text-align: center; margin-top: 20px;">
        <input id="btnMore" type="button" value="Ещё...">
    </div>
</div>

<script>
	$(function() {
		$('#blogForm').ajaxForm({
			target: '/manager/saveBlog',
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
                $("#blogProgress").show();
			},
			success: function(response)
			{
                $("#blogProgress").hide();
				success('top', 'Новость успешно сохранена!');

				var oEditor = FCKeditorAPI.GetInstance('message');
				var message = oEditor.GetHTML(true);
				
				if( $("#message_edit").attr('value') == 0 )
                {

                    var news_snippet = '<div id="table_'+response+'" style="margin-top: 20px;" class="table"><div class="angle angle-lt"></div><div class="angle angle-rt"></div><div class="angle angle-lb"></div><div class="angle angle-rb"></div><div><span class="label">' +
                    getNowDate() +
                    '</span> <span class="label"><b>' +
                    '</b>'+

                    '<span style="float: right;" id="message_'+response+'">'+
                    '<img class="edit_news" style="cursor:pointer; display: block;" src="static/images/comment-edit.png">'+
                    '<img class="delete_news" style="cursor:pointer;" src="static/images/delete.png">'+
                    '</span>'+

                    '</span></div><div>' +
                    message +
                    '</div></div>';

                    $('#news_header').after(news_snippet);
                }else{

                    var id_message = $("#message_edit").attr('value');
                    $("#message_"+id_message).parent().parent().next().html(message);
                    scrollToDiv ( $("#table_"+id_message),40);

                    var color = $("#table_"+id_message).css('color');

                    $("#table_"+id_message).animate({
                        color:"red"
                    }, 1000);
                    $("#table_"+id_message).animate({
                        color:color
                    }, 1000);
                    $("#message_edit").attr('value',0);

                    $("#btnSubmit").attr('value','Добавить новость');

                }

				oEditor.SetHTML('');
			},
			error: function(response)
			{
				$("#blogProgress").hide();
				error('top', 'Заполните все поля и сохраните еще раз.');
			}
		});

        $(".edit_news").live('click',function(){
            var id_message = $(this).parent().attr('id');
            var re = /message_/; id_message = id_message.replace(re,'');
            $("#message_edit").attr('value',id_message);

            $("#btnSubmit").attr('value','Сохранить изменения');

            var mes = $(this).parent().parent().parent().next().html();
            var oEditor = FCKeditorAPI.GetInstance('message');
            var message = oEditor.SetHTML(mes);

            scrollToDiv ( $("#textarea_point"),40);

        })

        $(".delete_news").live('click',function(){
            var id_message = $(this).parent().attr('id');
            var re = /message_/; id_message = id_message.replace(re,'');
            if ( confirm('Вы уверены что хотите удалить сообщение?') )
            {
                $.ajax({
                    type: "POST",
                    url: "/manager/delBlog",
                    data: "blog_id="+id_message,
                    success: function(msg){
                        $("#table_"+id_message).remove();
                        success('top', 'Новость успешно удалена!');
                    }
                });
            }
        })

        var start = 5;
        $('#btnMore').click(function(){
            var count = 5;
            $.ajax({
                type: "POST",
                url: "/manager/getMoreBlogAjax/"+start+"/"+count,
                success: function(result){
                    start = start + count;
                    result = JSON.parse(result);
                    for (var i = 0; i < result.length; i++) {
                        var date = (result[i].created).replace(/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)/,'$3.$2.$1 $4:$5');
                        var news_snippet = '<div id="table_'+result[i].blog_id+'" style="margin-top: 20px;" class="table"><div class="angle angle-lt"></div><div class="angle angle-rt"></div><div class="angle angle-lb"></div><div class="angle angle-rb"></div><div><span class="label">' +
                            date +
                            '</span> <span class="label"><b>' +
                            '</b>'+

                            '<span style="float: right;" id="message_'+result[i].blog_id+'">'+
                            '<img class="edit_news" style="cursor:pointer; display: block;" src="static/images/comment-edit.png">'+
                            '<img class="delete_news" style="cursor:pointer;" src="static/images/delete.png">'+
                            '</span>'+

                            '</span></div><div>' +
                            htmlUnescape(result[i].message)+
                            '</div></div>';
                        $('#insert_more_message').before(news_snippet);
                    }
                }
            });
        })

        function scrollToDiv(element,navheight){
            var offset = element.offset();
            var offsetTop = offset.top;
            var totalScroll = offsetTop-navheight;

            $('body,html').animate({
                scrollTop: totalScroll
            }, 500);
        }

	});

    function htmlUnescape(value){
        return String(value)
            .replace(/&quot;/g, '"')
            .replace(/&#39;/g, "'")
            .replace(/&lt;/g, '<')
            .replace(/&gt;/g, '>')
            .replace(/&amp;/g, '&');
    }


	<?= editor('message', 200, 920, 'PackageComment') ?>
</script>