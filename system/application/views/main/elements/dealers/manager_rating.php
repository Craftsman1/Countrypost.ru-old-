<? $is_own_rating = isset($this->user->user_id) AND	($this->user->user_id == $rating->manager_id OR	$this->user->user_id == $rating->client_id);?><div class='table bid' id='manager_rating<?=$rating->rating_id?>' style="margin-bottom: 30px;" >	<div class='angle angle-lt'></div>	<div class='angle angle-rt'></div>	<div class='angle angle-lb'></div>	<div class='angle angle-rb'></div>	<? View::show('main/elements/ratings/ratinginfo', array(		'rating' => $rating)); ?>	<br class="expander" style="clear:both;display:none;line-height:0;">	<div class='table admin-inside'		 style="margin-top:0px;padding-top:10px;padding-bottom:0;"		 id="comments<?= $rating->rating_id ?>">        <table>			<tr class="comment" <? if (empty($rating->comments)) : ?>style="display:none;"<? endif; ?>>				<th>					<b>Комментарии</b>				</th>			</tr>			<? if (isset($rating->comments)) : ?>                <? $i=0;?>                <? foreach ($rating->comments as $comment) : ?>                    <? $i++; if ($i <= 2) { $style =""; } else {$style = "display:none";} ?>                    <? if ($i>=3) $class_add="hide"; else $class_add="";?>                    <tr class="comment <?=$class_add?>" style="<?=$style?>">					<td>                        <? if($i>1) : ?>                            <? if ( $comment->user_id == $this->user->user_id || $rating->manager_id == $this->user->user_id ) : ?>                            <span id="comm_<?=$comment->comment_id?>" style="width: 95%; position: absolute; left: 95%;"">                                <img class="delCommentRating" src="static/images/delete.png" >                            </span>                            <? endif;?>                        <? endif;?>						<? View::show('main/elements/ratings/comment', array(							'comment' => $comment)); ?>					</td>				</tr>			    <? endforeach; ?>            <?endif; ?>			<? if (isset($this->user->user_id)) : ?>            <tr class='comment add-comment insert-comment<?= $rating->rating_id ?> hide'				style="display:none;">				<td>					<? View::show('main/elements/ratings/newComment', array(						'rating' => $rating,						'is_own_rating' => $is_own_rating)); ?>				</td>			</tr>            <? endif;?>			<tr class='last-row'>				<td>                    <?                    $flag_expand = false;                    if ($this->user->user_id == $rating->manager_id) {                        $flag_expand = true;                    }else{ $flag_expand = false;}                    if ( $this->user->user_id != -1 || $i > 2) {                        $flag_expand = true;                    }else{ $flag_expand = false;}                    if ( $this->user->user_id == $rating->client_id || $i > 2){                        $flag_expand = true;                    }else{ $flag_expand = false;}                    if ($this->user->user_id == $rating->manager_id) {                        $flag_expand = true;                    }                    if ($flag_expand){                        View::show('main/elements/dealers/expand_button',array('rating' => $rating));                    }                    ?>                        <? if ($this->user->user_id == $rating->client_id) : ?>                        <!--<div class='submit'>                            <div style="">                                <input type='button'                                       value='Редактировать отзыв'                                       onclick="editRating('<?= $rating->rating_id ?>');">                            </div>                        </div>!-->                        <div class='submit'>                            <div style="">                                <input type='button'                                       value='Удалить отзыв'                                       onclick="delRating('<?= $rating->rating_id ?>');">                            </div>                        </div>                        <? endif;?>						<div class='floatleft'>							<img class="float comment_progress<?= $rating->rating_id ?>"								 style="display:none;margin:0px;margin-top:5px;"								 src="/static/images/lightbox-ico-loading.gif"/>						</div>					</div>				</td>			</tr>		</table>	</div></div><script>    function collapseCommentsRating(rating_id)    {        var $comments = $('div#comments' + rating_id);        $comments            .find('div.expand_comments')            .show('slow');        $comments            .find('div.collapse_comments')            .hide('slow');        $("tr.comment.hide").hide('slow');    }</script>