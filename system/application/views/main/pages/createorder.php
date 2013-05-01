<div class='content smallheader'>
	<? Breadcrumb::showCrumbs(); ?>
	<? if ( ! $order_type) : ?>
    <h2 id='page_title'>Выберите вид заказа:</h2>
	<? View::show('main/elements/orders/order_type_selector'); ?>
	<? else : ?>
    <h2 id='page_title'><?= $order_types[$order->order_type] ?></h2>
    <? View::show("main/elements/orders/$order_type"); ?>
	<h3>Товары в заказе:</h3>
	<? View::show('client/ajax/showOrderDetails'); ?>
	<? View::show('main/elements/orders/scripts'); ?>
	<? endif; ?>
</div>
<script>
    $(function() {
        $('div.online_order').bind('click', function() {
            document.location = '/main/createorder/online';
        });

        $('div.offline_order').bind('click', function() {
            document.location = '/main/createorder/offline';
        });

        $('div.service_order').bind('click', function() {
            document.location = '/main/createorder/service';
        });

        $('div.delivery_order').bind('click', function() {
            document.location = '/main/createorder/delivery';
        });

        $('div.mail_forwarding_order').bind('click', function() {
            document.location = '/main/createorder/mail_forwarding';
        });

		$("select.country").msDropDown({mainCSS:'idd'});


    });

    var orderData = <?= ($order AND ($json = json_encode(array($order)))) ? $json : 'null' ?>;
    var currencies = <?= json_encode($countries); ?>;
    var selectedCurrency = '<?= isset($order_currency) ? $order_currency : '' ?>';
    var countryFrom = '';
    var countryTo = '';
    var cityTo = '';
    var user = '<?= empty($this->user) ? '' : $this->user->user_group ?>';
</script>