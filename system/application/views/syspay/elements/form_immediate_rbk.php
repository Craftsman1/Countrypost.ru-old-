<?
$crc  = md5(
	RK_LOGIN.
	":$amount:$number:".
	RK_PASS1.
	":ShpAmount=".
	$amount_local.
	":ShpComment=$order_id:ShpTax=".
	$User_tax.
	":ShpUser=".
	$user->user_id);

if (TESTMODE==1)
{
	$psform="<form action='http://test.robokassa.ru/Index.aspx' method=POST name='postform'>";}
else
{
	$psform="<form action='http://merchant.roboxchange.com/Index.aspx' method=POST name='postform'>";
}
$psform	= $psform .
	"MrchLogin:<input type=text name=MrchLogin value=".RK_LOGIN.">".
	"OutSum:<input type=text name=OutSum value=$amount>".
	"InvId:<input type=text name=InvId value=$number>".
	"Desc:<input type=text name=Desc value='Order: $order_id. Payment: $amount RUB. User: {$user->user_id}'>".
	"SignatureValue:<input type=text name=SignatureValue value=$crc>".
	"IncCurrLabel:<input type=text name=IncCurrLabel value=RuPayR>".
	"Culture:<input type=text name=Culture value=ru>".
	"ShpAmount:<input type=text name=ShpAmount value='$amount_local'>".
	"ShpComment:<input type=text name=ShpComment value='$order_id'>".
	"ShpTax:<input type=text name=ShpTax value='$User_tax'>".
	"ShpUser:<input type=text name=ShpUser value='".$user->user_id."'>".
	"</form>";
echo $psform;
?>