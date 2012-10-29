<br style="clear:both;" />
<div class='main-block'>
	<div class='adittional-block'>
		<? View::show($viewpath.'ajax/showUnassignedOrders', array(
			'handler' => 'filterUnassignedOrders',
		)); ?>
	</div>
	<style>
		#unassignedOrders td,#unassignedOrders th
		{
			text-align:center;
			vertical-align:middle;
			text-wrap: nowrap;
		}
	</style>
	<div class='main-content'>
		<? if (isset($showBannerBox)) : ?>
		<div class="banner-block">
			<img src="/static/images/headlines2.png" title="banner 3">
			<img src="/static/images/headlines.png" title="banner 3">
			<img src="/static/images/headlines.png" title="banner 3">
			<img src="/static/images/headlines.png" title="banner 3">
		</div>
		<? endif; ?>
	</div>
</div>