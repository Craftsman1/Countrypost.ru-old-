<span class='floatleft bid_buttons'  style="padding-top: 2px; width:50;">
    <div class='submit expand_comments'>
        <div style="">
            <input type='button'
                   value='Развернуть переписку'
                   onclick="expandComments('<?= $rating->rating_id ?>');">
        </div>
    </div>
    <div class='submit collapse_comments' style='display:none;'>
        <div style="">
            <input type='button'
                   value='Свернуть переписку'
                   onclick="collapseCommentsRating('<?= $rating->rating_id ?>');">
        </div>
	</div>
</span>