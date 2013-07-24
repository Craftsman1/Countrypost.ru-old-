<div class='floatleft bid_buttons'
     style="width:100%;">
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