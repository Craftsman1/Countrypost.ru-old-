<div class="managerinfo">	<form class='admin-inside' action="<?= $selfurl ?>addBidComment/<?=$bid->bid_id?>" id="bidCommentForm<?=$bid->bid_id?>" method="POST">		<input type="hidden" class="bid_id" value="<?= $bid->bid_id ?>">		<img src="/main/avatar/<?= $this->user->user_id ?>" width="56px" height="56px">		<span class='label floatleft'			  style="padding-top: 4px;">			<img src="/static/images/comment-reply.png"				 style="margin-right:8px;margin-left:-4px;margin-top: -3px;">			<img src="/static/images/flags/<?= $this->session->userdata['user_country_name_en'] ?>.png"				 style="margin-right:10px;margin-top: -4px;padding-top: 0;top: -2px;">			<a href="<?= $this->config->item('base_url') . $this->user->user_login ?>"><?= $this->session->userdata['user_name'] ?></a>			(<?= $this->user->user_login ?>)			&nbsp;&nbsp;&nbsp;			<?= date('d.m.Y H:i') ?>		</span>		<br style="height: 1px;line-height: 1px;">		<div style="float: left;margin-top: 0;width: 835px;">			<span class='label'>			<textarea id='comment<?= $bid->bid_id ?>'					  name='comment'					style="margin-top: 6px;margin-bottom: 5px;width: 550px;height: 50px;resize: vertical;border: 1px					solid #D7D7D7;					"></textarea>			</span>			<div class='submit save_comment floatleft'>				<div style="margin-top: 0;">				<div style="margin-top: 0;">					<input type='button' class="" value='Добавить' onclick="saveComment('<?=$bid->bid_id?>');" />				</div>				</div>			</div>		</div>	</form></div><? if ($is_own_bid) : ?><script>	$(function() {		$('#bidCommentForm<?= $bid->bid_id ?>').ajaxForm({			dataType: 'html',			iframe: true,			beforeSubmit: function(formData, jqForm, options)			{				beforeSubmitComment(<?= $bid->bid_id ?>);			},			success: function(response)			{				successSubmitComment(response, <?= $bid->bid_id ?>);			},			error: function(response)			{				errorSubmitComment(response, <?= $bid->bid_id ?>);			}		});	});</script><? endif; ?>