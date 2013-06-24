{unwrap}
Уважаемый(ая) <?=$client_name?>.
{/unwrap}
{unwrap}
Сообщаем Вам, что в заказе №<?=$order_id?> в Вашем предложении посредник добавил новый комментарий. Чтобы его просмотреть перейдите по этой ссылке:
{/unwrap}
{unwrap}
<?=$this->config->item('base_url')?>client/order/<?=$order_id?>
{/unwrap}
{unwrap}
C уважением,
Countrypost.ru - лучший сервис покупок за рубежом
{/unwrap}