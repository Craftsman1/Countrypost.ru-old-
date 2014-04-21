<form action="<?=PP_TEST ? PP_TEST_URL : PP_URL?>" name='postform' method="post">
    <input type="hidden" name="business" value="<?=PP_TEST ? PP_TEST_ACCOUNT : PP_ACCOUNT?>">
    <input type="hidden" name="cmd" value="_xclick">
    <input type="hidden" name="item_number" value="<?=$user->user_id?>">
    <input type="hidden" name="item_name" value="Refill balance for client <?=$user->user_id?> on $<?=$amount_usd?>"> 
    <input type="hidden" name="amount" value="<?=$amount_usd?>">  
    <input type="hidden" name="custom" value="<?=$amount*$tax*0.01 + $extra?>">  
    <input type="hidden" name="currency_code" value="USD"> 
    <input type='hidden' name='notify_url' value='<?=PP_NOTIFY_URL?>'>
    <input type='hidden' name='invoice' value='<?=$number?>'>
    <input type='hidden' name='image_url' value='<?=$config['base_url']?>static/images/logo.png'>
    <input type='hidden' name='return' value='<?=PP_RETURN_URL?>'>
    <input type='hidden' name='callback_url' value='<?=PP_CALLBACK_URL?>'>
    <input type='hidden' name='cancel_return' value='<?=$config['base_url']?>client'>
    <!--input type="image" name="submit" border="0" src="https://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif" alt="PayPal - The safer, easier way to pay online">
    <img alt="" border="0" width="1" height="1" src="https://www.paypal.com/en_US/i/scr/pixel.gif" --> 
</form>