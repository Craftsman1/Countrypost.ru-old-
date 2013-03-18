window.bidFormInitialized = false;
window.manager_tax = <?= ceil(($order->order_products_cost + $order->order_delivery_cost) *
	$manager->order_tax *
	0.01) ?>;
window.manager_tax_percentage = <?= $manager->order_tax ?>;
window.manager_foto_tax = <?= ceil($order->requested_foto_count *
	$manager->foto_tax *
	0.01) ?>;
window.manager_foto_tax_percentage = <?= $manager->foto_tax ?>;
window.requested_foto_count = <?= $order->requested_foto_count ?>;
window.order_total_cost = <?= isset($order->order_total_cost) ? $order->order_total_cost : 0 ?>;
window.order_products_cost = <?= $order->order_products_cost + $order->order_delivery_cost ?>;
window.order_delivery_cost = 0;
window.order_delivery_name = '';
window.order_weight = <?= $order->order_weight ?>;
window.extra_tax = 0;
window.extra_tax_counter = 0;

$('.bidinfo_name')
	.attr('href', '<?= BASEURL . $user->user_login ?>')
	.html('<?= $manager->manager_name ?>')
	.after(' (<?= $user->user_login ?>)');

$('.bidinfo_rating')
	.find('span.review_count')
	.html('<a style="color:green;">+ <?= $user->positive_reviews ?></a> / <?= $user->neutral_reviews ?> / <a style="color:red;">- <?= $user->negative_reviews ?></a>');


$('.bidinfo_completed_orders')
	.html('Выполненных заказов: <?= $manager->statistics->completed_orders ?>');

$('.manager_url')
	.attr('href', '<?= $manager->website ?>')
	.html('<?= $manager->website ?>');

