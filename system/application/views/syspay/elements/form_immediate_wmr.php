<form method="POST" action="https://merchant.webmoney.ru/lmi/payment.asp" name="postform">
	LMI_PAYMENT_AMOUNT:<input type="text" name="LMI_PAYMENT_AMOUNT" value="<?=$amount?>"><br />
	LMI_PAYMENT_DESC:<input type="text" name="LMI_PAYMENT_DESC" value="Order: <?= $order_id ?>. Payment: <?=
$amount ?> RUB. User: <?= $user->user_id ?>"><br />
	LMI_PAYMENT_NO:<input type="text" name="LMI_PAYMENT_NO" value="<?=$number?>"><br />
	LMI_PAYEE_PURSE:<input type="text" name="LMI_PAYEE_PURSE" value="<?=WM_PURSE?>"><br />
	LMI_SIM_MODE:<input type="text" name="LMI_SIM_MODE" value="<?= (TESTMODE == 1) ? 2 : 0 ?>"><br />
	LMI_RESULT_URL:<input type="text" name="LMI_RESULT_URL" value="<?= WM_RESULT_URL ?>"><br />
	LMI_SUCCESS_URL:<input type="text" name="LMI_SUCCESS_URL" value="<?= WM_SUCCESS_URL ?>"><br />
	LMI_SUCCESS_METHOD:<input type="text" name="LMI_SUCCESS_METHOD" value="2"><br />
	LMI_FAIL_URL:<input type="text" name="LMI_FAIL_URL" value="<?= WM_FAIL_URL ?>"><br />
	LMI_FAIL_METHOD:<input type="text" name="LMI_FAIL_METHOD" value="2"><br />
	User_id:<input type="text" name="User_id" value="<?= $user->user_id ?>"><br />
	User_comment:<input type="text" name="User_comment" value="<?= $amount_local ?>"><br />
	User_amount:<input type="text" name="User_amount" value="<?= $amount_usd ?>"><br />
	User_tax:<input type="text" name="User_tax" value="<?= $User_tax ?>"><br />
	User_order:<input type="text" name="order" value="<?= $order_id ?>">
</form>