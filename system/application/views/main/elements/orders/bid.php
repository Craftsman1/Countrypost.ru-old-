<? $is_own_bid = isset($this->user->user_id);if ($is_own_bid){	$is_own_bid = ($bid->manager_id == $this->user->user_id OR 		$order->order_manager == $this->user->user_id OR 		$order->order_client == $this->user->user_id);}	$is_bid_hidden = ($order->order_manager AND	$order->order_manager != $bid->manager_id);?><? if ($bid->new_comments > 0) $newClass = "new"; else $newClass = ""; ?><style>    .table.bid.new, .table.admin-inside.new {        background: #f0ffec;    }    .table.bid .new {        background-image:url(/static/images/angle_new.png);    }</style><div class='table bid <?=$newClass?>' id='bid<?=$bid->bid_id?>' <? if ($is_bid_hidden) : ?>style="display:none;"<? endif; ?>>	<div class='angle angle-lt <?=$newClass?>'></div>	<div class='angle angle-rt <?=$newClass?>'></div>	<div class='angle angle-lb <?=$newClass?>'></div>	<div class='angle angle-rb <?=$newClass?>'></div>	<? View::show('main/elements/orders/managerinfo', array('bid' => $bid)); ?>	<? if ($is_own_bid AND $this->user->user_group == 'manager') : ?>	<? View::show('main/elements/orders/editBidForm', array(		'bid' => $bid)); ?>	<? endif; ?>    <br class="expander" style="clear:both;display:none;line-height:0;" />    <? if ($is_own_bid) : ?>	<div class='table admin-inside <?=$newClass?>'		 style="margin-top:0px;padding-top:0px;padding-bottom:0;"		 id="comments<?=$bid->bid_id?>">        <? if (count($bid->comments) >= 1) : ?>            <table>                <tr class="comment">                    <th>                        <b>Комментарии</b>                    </th>                </tr>                <? if (isset($bid->comments)) : foreach ($bid->comments as $comment) : ?>                <tr class="comment">                    <td>                        <? View::show('main/elements/orders/comment', array(                            'comment' => $comment)); ?>                    </td>                </tr>                <? endforeach; endif; ?>                <tr class='comment add-comment'                    style="display:none;">                    <td>                        <? View::show('main/elements/orders/newComment', array('bid' => $bid, 'is_own_bid' => $is_own_bid)); ?>                    </td>                </tr>                <tr class='last-row'>                    <td>                        <div class='floatleft bid_buttons'                             style="width:100%;">                            <? if (isset($this->user) AND                                $this->user->user_group == 'client') : ?>                            <form class='chooseBidForm' action="<?= $selfurl ?>chooseBid/<?=$bid->bid_id?>" id="chooseBidForm<?=$bid->bid_id?>" method="POST">                                <div class='submit choose_bid'<? if ($order->order_manager) : ?> style="display:none;"<? endif; ?>>                                    <div>                                        <input type='button' class="" value='Выбрать исполнителем' onclick="chooseBid('<?=$bid->bid_id?>');" />                                    </div>                                </div>                                <script>                                    $(function() {                                        $('#chooseBidForm<?=$bid->bid_id?>').ajaxForm({                                            target: $('#chooseBidForm<?=$bid->bid_id?>').attr('action'),                                            type: 'POST',                                            dataType: 'html',                                            iframe: true,                                            beforeSubmit: function(formData, jqForm, options)                                            {                                                beforeSubmitChoose('<?=$bid->bid_id?>');                                            },                                            success: function(response)                                            {                                                successSubmitChoose(response, '<?=$bid->bid_id?>');                                            },                                            error: function(response)                                            {                                                errorSubmitChoose(response, '<?=$bid->bid_id?>');                                            }                                        });                                    });                                </script>                            </form>                            <? endif; ?>                            <div class='submit expand_comments'>                                <div>                                    <input type='button' value='Развернуть переписку' onclick="expandComments('<?=$bid->bid_id?>');" />                                </div>                            </div>                            <div class='submit collapse_comments' style='display:none;'><div><input type='button' value='Свернуть переписку' onclick="collapseComments('<?=$bid->bid_id?>');" /></div></div>                            <? if (isset($this->user->user_group) AND                                $this->user->user_group == 'manager' AND                                $this->user->user_id == $bid->manager_id AND                                empty($order->order_cost_payed)) : ?>                            <div class='submit delete_bid'>                                <div>                                    <input                                        type='button'                                        value='Удалить предложение'                                        onclick="deleteBid('<?= $order->order_id ?>', '<?= $bid->bid_id ?>');" />                                </div>                            </div>                            <? endif; ?>                            <? if (isset($this->user->user_group) AND                                $this->user->user_group == 'client' AND                                empty($order->order_cost_payed)) : ?>                            <div class='submit unchoose_bid' <? if ($order->order_manager != $bid->manager_id) :                                ?>style="display: none;"<? endif; ?>>                                <div>                                    <input                                            type='button'                                            value='Отказаться'                                            onclick="unchooseBid(<?= $bid->bid_id ?>);" />                                </div>                            </div>                            <? endif; ?>                            <? if ($bid->new_comments > 0) : ?>                                <div id="new_comments" style='float: left; padding: 0px 0px 0px 15px;'>                                    <?                                    if ($bid->new_comments == 1)                                        $msg = "новое сообщение";                                    else                                        $msg = "новых сообщений";                                    ?>                                    <div style='float: right;'><img style="position: relative; top: 3px; left: -5px;" src = "/static/images/mail.png" > <?=$bid->new_comments?> <?=$msg?></div>                                </div>                            <? endif; ?>                            <div class='floatleft'>                                <img class="float comment_progress" style="display:none;margin:0px;margin-top:5px;" src="/static/images/lightbox-ico-loading.gif"/>                            </div>                        </div>                    </td>                </tr>            </table>        <? endif; ?>	</div>	<? endif; ?></div><br><br>