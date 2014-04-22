<?
$comment=base64_encode('');
$crc  = md5(
	RK_LOGIN.
	":$amount:$number:".
	RK_PASS1.
	":ShpAmount=".
	$amount_usd.
	":ShpComment=:ShpTax=".
	$User_tax.
	":ShpUser=".
	$user->user_id);

if (TESTMODE==1) {
	$psform="<form action='http://test.robokassa.ru/Index.aspx' method=POST name='postform'>";}
else {
	$psform="<form action='http://merchant.roboxchange.com/Index.aspx' method=POST name='postform'>";
}
$psform	=	$psform.
		      "MrchLogin:<input type=text name=MrchLogin value=".RK_LOGIN.">".
		      "OutSum:<input type=text name=OutSum value=$amount>".
		      "InvId:<input type=text name=InvId value=$number>".
		      "Desc:<input type=text name=Desc value='Пополнение счета клиента №$user->user_id на $amount руб. (".'$'."$amount_usd)'>".
		      "SignatureValue:<input type=text name=SignatureValue value=$crc>".
		      "IncCurrLabel:<input type=text name=IncCurrLabel value=W1R>".
		      "Culture:<input type=text name=Culture value=ru>".
		      "ShpUser:<input type=text name=ShpUser value='".$user->user_id."'>".
		      "ShpComment:<input type=text name=ShpComment value=''>".
		      "ShpAmount:<input type=text name=ShpAmount value='$amount_usd'>".
		      "ShpTax:<input type=text name=ShpTax value='$User_tax'>".
		      "</form>";
echo $psform;
?>