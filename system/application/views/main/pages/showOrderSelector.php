<div class='content smallheader'>
	<? Breadcrumb::showCrumbs(); ?>
    <h2 id='page_title'>Выберите вид заказа:</h2>
	<? View::show('main/elements/orders/order_type_selector'); ?>
</div>
<script>
    $(function() {
		$('div.online_order').bind('click', function() {
            window.location = '/main/createorder/online';
        });

        $('div.offline_order').bind('click', function() {
            window.location = '/main/createorder/offline';
        });

        $('div.service_order').bind('click', function() {
            window.location = '/main/createorder/service';
        });

        $('div.delivery_order').bind('click', function() {
            window.location = '/main/createorder/delivery';
        });

        $('div.mail_forwarding_order').bind('click', function() {
            window.location = '/main/createorder/mail_forwarding';
        });
	});
</script>