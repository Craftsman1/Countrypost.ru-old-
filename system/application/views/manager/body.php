	<!--[if IE 7 ]>    <body class="ie7 inner"> <![endif]-->
	<!--[if IE 8 ]>    <body class="ie8 inner"> <![endif]-->
	<!--[if IE 9 ]>    <body class="ie9 inner"> <![endif]-->
	<!--[if (gt IE 9)|!(IE)]><!--> <body class='inner'> <!--<![endif]-->
	<div id="lay" style="position:absolute; z-index: 999; background: #787878; width:100%; height:100%; display:none; opacity:0.3;"></div>
	<div class="main_content">
		<div class='layout'>
			<? View::show('main/elements/div_top'); ?>
			<? View::show($viewpath.'elements/div_header'); ?>
			<? View::show($viewpath.'elements/div_content'); ?>
		</div>
		<div class="footer_placeholder"></div>
	</div>
	<div class="footer_content">
		<? View::show('elements/div_bottom'); ?>
		<? View::show('elements/div_footer'); ?>
	</div>
	<script type="text/javascript">
    var reformalOptions = {
        project_id: 41409,
        project_host: "Countrypost.reformal.ru",
        tab_orientation: "left",
        tab_indent: "50%",
        tab_bg_color: "#5c567d",
        tab_border_color: "#FFFFFF",
        tab_image_url: "http://tab.reformal.ru/T9GC0LfRi9Cy0Ysg0Lgg0L%252FRgNC10LTQu9C%252B0LbQtdC90LjRjw==/FFFFFF/4bfb34d91c8d7fb481972ca3c84aec38/left/0/tab.png",
        tab_border_width: 2
    };
    
    (function() {
        var script = document.createElement('script');
        script.type = 'text/javascript'; script.async = true;
        script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'media.reformal.ru/widgets/v3/reformal.js';
        document.getElementsByTagName('head')[0].appendChild(script);
    })();
	</script><noscript><a href="http://reformal.ru"><img src="http://media.reformal.ru/reformal.png" /></a><a href="http://Countrypost.reformal.ru">Oтзывы и предложения для Countrypost.ru - сервис покупок за рубежом</a></noscript>
	
</body>