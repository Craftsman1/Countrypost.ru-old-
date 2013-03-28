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
	<tr>
		<td><script type="text/javascript"><!--
			google_ad_client = "ca-pub-5893168729052796";
			/* Банер 1 */
			google_ad_slot = "3026683060";
			google_ad_width = 250;
			google_ad_height = 230;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><script type="text/javascript"><!--
			google_ad_client = "ca-pub-5893168729052796";
			/* Банер 2 */
			google_ad_slot = "2887082269";
			google_ad_width = 250;
			google_ad_height = 250;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script></td>
	</tr>
</table>
		</div>
		<? endif; ?>
	</div>
</div>