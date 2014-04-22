<? if (isset($orders2InFoto[$payment->order2in_id])) : ?>
<b>Скриншот:</b>
<a href="javascript:void(0)" onclick="setRel(<?= $payment->order2in_id ?>)">
	Посмотреть&nbsp;(<?= count($orders2InFoto[$payment->order2in_id]); ?>)<?
	foreach ($orders2InFoto[$payment->order2in_id] as $o2iFoto) :
		?><a rel="lightbox_<?= $payment->order2in_id ?>"
			 href="<?= $selfurl ?>showPaymentFoto/<?= $payment->order2in_id ?>/<?= $o2iFoto ?>"
			 style="display:none;">Посмотреть</a><? endforeach; ?></a>
<br />
<? endif; ?>

<? if ((isset($this->user->user_id) AND
	$this->user->user_id == $payment->order2in_user) OR
	(isset($this->user->user_group) AND
		$this->user->user_group == 'admin')) : ?>
<a href="javascript:uploadBillFoto(<?= $payment->order2in_id ?>);">Добавить</a>
<div style="line-height:21px;display:none;padding-top:10px;" id="scans_<?= $payment->order2in_id ?>">
	<? if (isset($orders2InFoto[$payment->order2in_id])): ?>
	&nbsp;<? foreach ($orders2InFoto[$payment->order2in_id] as $o2iFoto):
		?><a href="<?= $selfurl ?>deletePaymentFoto/<?= $payment->order2in_id ?>/<?= $o2iFoto ?>"
			 style="margin-right:8px;"><? $file = parse_url($o2iFoto); echo $file['path']; ?><img src="/static/images/delete.png"/></a>&nbsp;<? endforeach; ?>
	<? endif; ?>
</div>
<? endif; ?>