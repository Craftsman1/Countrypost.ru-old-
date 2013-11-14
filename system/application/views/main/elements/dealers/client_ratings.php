<?
   if(!isset($this->user) || empty($this->user)) $this->user = new stdClass();
   if (!isset($this->user->user_id)) $this->user->user_id = -1;
   if (!isset($this->user->user_group)) $this->user->user_group = -1;

?>
<div class="client_ratings dealer_tab" style="display:none;">

    <? if ( $this->user->user_id == -1) : ?>
    <div class="table">
        <div class='angle angle-lt'></div>
        <div class='angle angle-rt'></div>
        <div class='angle angle-lb'></div>
        <div class='angle angle-rb'></div>
        <div style="height: 30px;">
            <div class="" style="margin-top: 10px;">
                 <b style="font-size: medium;">Чтобы добавить отзыв посреднику войдите в систему под своим логином и паролем.</b>
            </div>
        </div>
    </div>
    <br>
    <br>
    <? endif; ?>

    <? if ( $this->user->user_id != $manager->manager_user AND $this->user->user_id != -1
        AND $this->user->user_group != "manager") : ?>

    <form action="/client/saveRating" id="ratingForm" method="POST">
		<div class="table">
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<div class="rating_box admin-inside">
				<input type="hidden" name="manager_id" value='<?= $manager->manager_user ?>' />
				<div>
					<span class="label">
						<input type='radio' value='positive' name="rating_type" />
						<label for="positive" style="color: green;">Положительный</label>
					</span>
					<span class="label">
						<input type='radio' value='neutral' name="rating_type" checked />
						<label for="neutral">Нейтральный</label>
					</span>
					<span class="label">
						<input type='radio' value='negative' name="rating_type" />
						<label for="negative" style="color: red;">Отрицательный</label>
					</span>
				</div>
				<br style="clear:both;" />
				<div>
					<textarea maxlength="65535" 
							  id='rating_message' 
							  name="rating_message" 
							  style="width: 864px;
								height: 100px;
								margin-top: 6px;
								margin-bottom: 5px;
								resize: vertical;
								border: 1px solid rgb(215, 215, 215);"></textarea>
				</div>
			</div>
			<div class="rating_group table">
				<div>
					<? View::show('/main/elements/ratings/ratings_plugin', array(
					'rating' => 5,
					'description' => 'Консультация (ответы в скайпе, почте)',
					'rating_type' => 'communication_rating'
				)); ?>
				</div>
				<div>
					<? View::show('/main/elements/ratings/ratings_plugin', array(
					'rating' => 4,
					'description' => 'Выкуп товара',
					'rating_type' => 'buy_rating'
				)); ?>
				</div>
				<div>
					<? View::show('/main/elements/ratings/ratings_plugin', array(
					'rating' => 3,
					'description' => 'Консолидация и отправка посылок',
					'rating_type' => 'consolidation_rating'
				)); ?>
				</div>
				<div style="height:auto;">
					<? View::show('/main/elements/ratings/ratings_plugin', array(
					'rating' => 2,
					'description' => 'Упаковка посылок',
					'rating_type' => 'pack_rating'
				)); ?>
				</div>
			</div>
		</div>
		<br style="clear:both;" />
		<div class="submit floatleft">
			<div>
				<input type="submit" value="Добавить">
			</div>
		</div>
		<img class="float" id="ratingProgress" style="display:none;margin:0px;margin-top:4px;" src="/static/images/lightbox-ico-loading.gif"/>
	</form>
    <? elseif(!$manager_ratings OR count($manager_ratings) <= 1) : ?>
        <div class="table">
            <div class='angle angle-lt'></div>
            <div class='angle angle-rt'></div>
            <div class='angle angle-lb'></div>
            <div class='angle angle-rb'></div>
            <div>
                <b style="font-size: medium;">Добавлять отзывы посреднику могут только клиенты</b>
            </div>
        </div>
        <br>
        <br>
    <? endif;?>


    <? if( $this->user->user_id != $manager->manager_user AND $this->user->user_id != -1
        AND $this->user->user_group != "manager") : ?>
        <br />
	<? View::show('main/elements/ratings/rating_list', array(
		'ratings' => $manager_ratings
	)); ?>
    <? endif; ?>



    <? if( $this->user->user_id == $manager->manager_user || $this->user->user_id == -1 ||
        $this->user->user_group == "manager") : ?>
    <? if ($manager_ratings)
    {
        foreach ($manager_ratings as $rating)
        {
            View::show('main/elements/dealers/manager_rating', array(
                'rating' => $rating));
        }
    } ?>
    <? endif;?>


    <h3 id="ratings_header" <? if (empty($ratings)) : ?>style="display: none;"<? endif; ?>>Все отзывы</h3>

    <? if (isset($ratings)) : foreach ($ratings as $rating) : ?>
	<div class="table">
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<div>
			<span class="label">
				<?= isset($rating->created) ? date('d.m.Y H:i', strtotime($rating->created)) : '' ?>
			</span>
			<span class="label">
				<b><?= $rating->title ?></b>
			</span>
		</div>
		<div>
			<?= html_entity_decode($rating->message) ?>
		</div>
	</div>
	<br>
	<br>
	<? endforeach; endif; ?>
</div>
<script>
	$(function() {

        $('#ratingForm').ajaxForm({
			target: '/client/saveRating',
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
                //var oEditor = jQuery('#rating_message').val();
                var getText = jQuery('#rating_message').val();
				console.log(getText);
                var StripTag = getText.replace(/(<([^>]+)>)/ig,"");
                StripTag = StripTag.replace(/\&nbsp\;/ig,'');
                if( StripTag=="" || StripTag.length < 5) {
                    error('top', 'Заполните все поля и сохраните еще раз.');
                    return false;
                }
                $("#ratingProgress").show();
			},
			success: function(response)
			{

                $("#ratingProgress").hide();
				success('top', 'Отзыв успешно сохранен!');

                $("#insert_rating").after(response);

                /*var oEditor = FCKeditorAPI.GetInstance('rating_message');
				oEditor.SetHTML('');*/
				jQuery('#rating_message').val('')
				$('#ratingForm .ratings_plugin div').removeClass('on').removeClass('half');
				$('#ratingForm .ratings_plugin input').val('');
				$('#ratingForm .rating_box input[name=rating_type][value=neutral]').attr('checked', true);


			},
			error: function(response)
			{
				$("#ratingProgress").hide();
				error('top', 'Заполните все поля и сохраните еще раз.');
			}
		});

        /*if( $("#rating_message").is("#rating_message") ){
            <?= editor('rating_message', 200, 920, 'PackageComment') ?>;
        }*/

        /*$("#btnAddOtziv").click(function(){
            error('top', 'Вам необходимо войти систему под своим логином и паролем.');
        })*/

	});
</script>