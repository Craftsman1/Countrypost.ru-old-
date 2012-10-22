<? if ($result->e < 0) : ?>
	<em class="order_result" style="color:red;" id="ajax_message"><?= $result->m ?></em>
<? elseif ($result->e > 0) : ?>
	<em class="order_result" style="color:green;" id="ajax_message"><?= $result->m ?></em>
<? endif; ?>	
