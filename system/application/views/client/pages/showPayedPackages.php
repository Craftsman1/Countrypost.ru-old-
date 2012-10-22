<a name="pagerScroll"></a>
<div class='content'>
	<h2 id='page_title'>Оплаченные посылки</h2>
	<? View::show($viewpath.'elements/packages/errors'); ?>
	<? View::show($viewpath.'elements/packages/add_package'); ?>
	<div class="admin-inside" style="height:50px" id='add_package_button'>
		<div class="submit">
			<div>
				<input type="button" onclick="add_package();" name="add" value="Жду посылку">
			</div>
		</div>
	</div>
	<? View::show($viewpath.'ajax/showPayedPackages', array(
		'packages' => $packages,
		'pager' => $pager)); ?>
</div>
<? View::show('elements/hints'); ?>
<? View::show($viewpath.'elements/packages/scripts'); ?>