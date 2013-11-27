<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>Отправить деньги в китай и из китая</title>
    <link rel='stylesheet' type='text/css' media="screen" href='<?= CSS_PATH ?>money.css' />
    <script src="<?php echo JS_PATH;?>jquery-1.8.2.js"></script>
</head>
<body>
<div class="main_content">
    <div class="layout">
        <div class="header">
            <h1 class="logo flt"><a class="header-link" href="https://countrypost.ru/">CountryPost - Лучший сервис покупок за рубежом</a></h1>
            <a class="skype header-link flt" href="skype:country_post?call"><span>skype: country_post</span></a>
            <a class="mail header-link frt" href="mailto:info@countrypost.ru"><span>info@countrypost.ru</span></a>
        </div>
        <div class="main_block">

            <div class="title_landing">отправить деньги в китай и из китая</div>
            <div class="time_transfer">за один час</div>
            <div class="transfer_img"><img src="<?=IMG_PATH?>money/transfer_money.png" width="480" height="160" alt="transfer_money"></div>
            <div class="soc-icons"><img src="<?=IMG_PATH?>money/soc-icon.png" width="405" height="44" alt="soc_icons"></div>
            <div class="flt"><img src="<?=IMG_PATH?>money/transfer_to_China.png" width="445" height="110" alt="transfer_to_China"></div>
            <div class="frt"><img src="<?=IMG_PATH?>money/transfer_from_China.png" width="445" height="110" alt="transfer_from_China"></div>
            <table class="ratestable" width="100%">
                <tbody>
                <tr>
                    <td class="headtable" width="53%">Тарифы</td>
                    <td class="headtable" width="47%">Тарифы</td>
                </tr>
                <tr>
                    <td>Обычный (в течении 24 часов) - 1.5%</td>
                    <td>Обычный (в течении 24 часов) - 0.5%</td>
                </tr>
                <tr>
                    <td>Срочный (в течении 1 часа) - 2%</td>
                    <td>Срочный (в течении 1 часа) - 1%</td>
                </tr>
                <tr>
                    <td>Срочный  + пополнение alipay - 2%</td>
                    <td></td>
                </tr>
                </tbody>
            </table>
            <div class="clarification">* При размещении заказа на сервисе countrypost.ru перевод денег БЕСПЛАТНО</div>
            <div class="box-back-left flt">
                <h2>Заявка на перевод</h2>
                <form class="" action="" method="POST">
                    <div class="text-field">
                        <div>
                            <select>
                                <?php if(isset($money)) foreach($money as $m):?>
                                <option data-percent="<?php echo $m->percent;?>" value="<?php echo $m->id;?>"><?php echo $m->name;?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                    <div class="text-field">
                        <div>
                            <input class="amount" type="text" placeholder="Сумма" name="price">
                            <div class="flt total">Итого к оплате: <span>23000 руб</span></div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="text-field">
                        <div>
                            <input type="text" placeholder="Ваши контакты (email, skype, телефон)" name="contacts">
                        </div>
                    </div>
                    <div class="submit">
                        <div>
                            <button>Отправить заявку</button>

                        </div>
                    </div>
                    <form>
            </div>
            <div class="box-back-right frt" id="currency-insert">
                <?php echo $currency;?>
            </div>

            <div class="clear"></div>


            <div class="box-reviews">
                <h2>отзывы</h2>
                <div class="inbox-reviews"> </div>
            </div>
        </div>
    </div>
</div>
<div class="before_footer">
    <div class="layout">
        <h3>варианты оплаты</h3>
        <div class="other_variants"><a href="#"><span>Рассмотрим любые другие удобные вам варианты</span></a>
        </div>
        <div class="clear"></div>
        <div id="shop-container">
            <div class="row1">
                <a class="flt shop-link" href="#" target="_blank"><img src="<?=IMG_PATH?>money/shop_01.jpg"></a>
                <a class="flt shop-link" href="#" target="_blank"><img src="<?=IMG_PATH?>money/shop_02.jpg"></a>
                <a class="flt shop-link" href="#" target="_blank"><img src="<?=IMG_PATH?>money/shop_03.jpg"></a>
                <a class="flt shop-link" href="#" target="_blank"><img src="<?=IMG_PATH?>money/shop_04.jpg"></a>
                <a class="flt shop-link" href="#" target="_blank"><img src="<?=IMG_PATH?>money/shop_05.jpg"></a>
                <a class="flt shop-link" href="#" target="_blank"><img src="<?=IMG_PATH?>money/shop_06.jpg"></a>
                <a class="flt shop-link-last" href="#" target="_blank"><img src="<?=IMG_PATH?>money/shop_07.jpg"></a>
            </div>
            <div class="clear"></div>
            <div class="row2">
                <a class="flt shop-link" href="#" target="_blank"><img src="<?=IMG_PATH?>money/shop_08.jpg"></a>
                <a class="flt shop-link" href="#" target="_blank"><img src="<?=IMG_PATH?>money/shop_09.jpg"></a>
                <a class="flt shop-link" href="#" target="_blank"><img src="<?=IMG_PATH?>money/shop_10.jpg"></a>
                <a class="flt shop-link" href="#" target="_blank"><img src="<?=IMG_PATH?>money/shop_11.jpg"></a>
                <a class="flt shop-link" href="#" target="_blank"><img src="<?=IMG_PATH?>money/shop_12.jpg"></a>
                <a class="flt shop-link" href="#" target="_blank"><img src="<?=IMG_PATH?>money/shop_13.jpg"></a>
                <a class="flt shop-link-last" href="#" target="_blank"><img src="<?=IMG_PATH?>money/shop_14.jpg"></a>
            </div>
            <div class="clear"></div>
            <div class="row3">
                <a class="flt shop-link" href="#" target="_blank"><img src="<?=IMG_PATH?>money/shop_15.jpg"></a>
                <a class="flt shop-link" href="#" target="_blank"><img src="<?=IMG_PATH?>money/shop_16.jpg"></a>
                <a class="flt shop-link" href="#" target="_blank"><img src="<?=IMG_PATH?>money/shop_17.jpg"></a>
                <a class="flt shop-link" href="#" target="_blank"><img src="<?=IMG_PATH?>money/shop_18.jpg"></a>
                <a class="flt shop-link" href="#" target="_blank"><img src="<?=IMG_PATH?>money/shop_19.jpg"></a>
                <a class="flt shop-link" href="#" target="_blank"><img src="<?=IMG_PATH?>money/shop_20.jpg"></a>
                <a class="flt shop-link-last" href="#" target="_blank"><img src="<?=IMG_PATH?>money/shop_21.jpg"></a>
            </div>
            <div class="clear"></div>


        </div>
    </div>
</div>
<div class="footer">
    <div class="layout">
        <div class="biz flt">© 2013. Countrypost.ru</div>
        <a href="#" class="footer-link flt">GLOBAL POST Ltd., Ground Floor 2 Woodberry Grove, N12 0DR, London, England.</a>
        <a href="mailto:info@countrypost.ru" class="frt">e-mail: info@countrypost.ru</a>
        <a href="skype:country_post?call" class="frt">skype: country_post</a>

    </div>
</div>
</body>
</html>