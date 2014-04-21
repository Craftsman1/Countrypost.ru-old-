<form action="<?=PP_TEST ? PP_TEST_URL : PP_URL?>" name='postform' method="post"> 

    <input type="hidden" name="business" value="<?=PP_TEST ? PP_TEST_ACCOUNT : PP_ACCOUNT?>"> 

    <input type="hidden" name="cmd" value="_xclick"> 

    <input type="hidden" name="item_number" value="<?=$user->user_id?>"> 
    <input type="hidden" name="item_name" value="Pay order: <?= $order_id ?> Amount: <?= $amount_usd?> Client: <?=$user->user_id?>"> 
    <input type="hidden" name="amount" value="<?=$amount_usd?>">  
    <input type="hidden" name="custom" value="<?=$amount*$tax*0.01 + $extra?>">  
    <input type="hidden" name="currency_code" value="USD"> 
    <input type='hidden' name='notify_url' value='<?=PP_NOTIFY_URL?>'>
    <input type='hidden' name='invoice' value='<?=$number?>'>
    <input type='hidden' name='image_url' value='<?=PP_IMAGE_URL?>'>
    <input type='hidden' name='return' value='<?=PP_RETURN_URL?>'>
    <input type='hidden' name='callback_url' value='<?=PP_CALLBACK_URL?>'>
    <input type='hidden' name='cancel_return' value='<?=PP_CANCEL_URL?>'>
</form>