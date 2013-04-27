<br style="clear:both;" />
<div class='main-block'>
	<div class='adittional-block'>
		<? View::show($viewpath.'ajax/showUnassignedOrders', array(
			'handler' => 'filterUnassignedOrders',
		)); ?>
	</div>
	<div class='main-content'>
		<? if (isset($showBannerBox)) : ?>
		<div class="banner-block">
			<table border="1" width="100%" style="border-width: 0px">
	<tr>
		<td><script type="text/javascript" src="//vk.com/js/api/openapi.js?83"></script>
			<!-- VK Widget -->
			<div id="vk_groups"></div>
			<script type="text/javascript">
			VK.Widgets.Group("vk_groups", {mode: 1, width: "250", height: "290"}, 27870280);
			</script>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	
</table>
		</div>
		<? endif; ?>
	</div>
</div>