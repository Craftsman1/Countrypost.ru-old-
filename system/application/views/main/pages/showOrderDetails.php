<div class='content smallheader'>
	<? Breadcrumb::showCrumbs(); ?>
	<? View::show('main/elements/orders/header'); ?>
	<? View::show('main/ajax/showOrderDetails'); ?>
	<? if (empty($this->user->user_group) OR $this->user->user_group == 'manager') : ?>
	<? View::show('main/elements/orders/newBidForm'); ?>
	<? endif; ?>
	<? View::show('main/elements/orders/bids'); ?>
</div>