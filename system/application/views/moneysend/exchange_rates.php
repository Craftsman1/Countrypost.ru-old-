<? if (isset($currencies) AND $currencies) : ?>
    <h2>курсы валют</h2>
    <div id="currency_rates">
        <form action="" method="POST">
                <select id="currency_select">
                    <? foreach ($currencies as $cur): ?>
                        <option value="<?= $cur['currency_name']; ?>" <?= ($cur_currency == $cur['currency_name'] ? 'selected="selected"' : ''); ?>><?= $cur['currency_name']; ?>
                        </option>
                    <? endforeach; ?>
                </select>
        </form>
    </div>
    <div><?php echo '1 ' . $cur_currency . ' = ' . number_format($rate_rur, 6) ?> RUB</div>
    <div><?php echo '1 ' . $cur_currency . ' = ' . number_format($rate_usd, 6) ?> USD</div>
    <div><?php echo '1 ' . $cur_currency . ' = ' . number_format($rate_uah, 6) ?> UAH</div>
    <div><?php echo '1 ' . $cur_currency . ' = ' . number_format($rate_kzt, 6) ?> CNY</div>
    <script type="text/javascript">
        $(function () {
            $('#currency_select').change(function () {
                jQuery.post('<?= base_url().'moneysend/ajax' ?>', {
                        "cur_name": $('#currency_select').val(),
                        'action':'exchange_rate'
                    },
                    function (data) {
                        if (data.status == 'success') {
                            $('#currency-insert').html(data.view);
                        }
                    }, 'json'
                );
            })
        })
    </script>
<? endif; ?>