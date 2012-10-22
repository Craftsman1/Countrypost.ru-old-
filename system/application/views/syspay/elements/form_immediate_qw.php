<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></meta>
<title>Выставление счёта за покупку</title>
<script type="text/javascript">
var ie=document.all;var moz=(navigator.userAgent.indexOf("Mozilla")!=-1);var opera=window.opera;var brodilka="";if(ie&&!opera){brodilka="ie"}else{if(moz){brodilka="moz"}else{if(opera){brodilka="opera"}}}var inputMasks=new Array();function kdown(a,c){var e=a.getAttribute("id");var b=e.substring(0,e.length-1);var d=Number(e.substring(e.length-1));inputMasks[b].BlKPress(d,a,c)}function kup(a,b){if(Number(a.getAttribute("size"))==a.value.length){var f=a.getAttribute("id");var d=f.substring(0,f.length-1);var e=Number((f.substring(f.length-1)))+1;var c=document.getElementById(d+e);if(b!=8&&b!=9){if(c){c.focus()}}else{if(b==8){a.value=a.value.substring(0,a.value.length-1)}}}}function Mask(d){var c="(\\d{3})\\d{3}-\\d{2}-\\d{2}";var f=[];var g=[];var a=[];var e="";var b=function(k){var j=Number(k.substring(3,k.indexOf("}")));var i=d.getAttribute("id");var m=g.length;var l="";var h=function(n){return((n>=48)&&(n<=57))||((n>=96)&&(n<=105))||(n==27)||(n==8)||(n==9)||(n==13)||(n==45)||(n==46)||(n==144)||((n>=33)&&(n<=40))||((n>=16)&&(n<=18))||((n>=112)&&(n<=123))};this.makeInput=function(){return"<input type='text' size='"+j+"' maxlength='"+j+"' id='"+i+m+"' onKeyDown='kdown(this, event)' onKeyUp='kup(this, event.keyCode)' value='"+l+"'>"};this.key=function(n,q){if(opera){return}if(!h(q.keyCode)){switch(brodilka){case"ie":q.cancelBubble=true;q.returnValue=false;break;case"moz":q.preventDefault();q.stopPropagation();break;case"opera":break;default:}return}if(q.keyCode==8&&n.value==""){var s=n.getAttribute("id");var r=s.substring(0,s.length-1);var o=Number(s.substring(s.length-1))-1;var p=document.getElementById(r+o);if(p!=null){p.focus()}}};this.getText=function(){l=document.getElementById(i+m).value;return l};this.setText=function(n){l=n};this.getSize=function(){return j}};this.drawInputs=function(){var k="<span class='Field'>";var l=0;var h=0;for(var j=0;j<a.length;j++){if(a[j]=="p"){k+=f[l];l++}else{k+=g[h].makeInput();h++}}k+="</span>";document.getElementById("div_"+d.getAttribute("id")).innerHTML=k;d.style.display="none"};this.buildFromFields=function(){var i=c;while(i.indexOf("\\")!=-1){var h=i.indexOf("\\");var k="";if(i.substring(0,h)!=""){f[f.length]=i.substring(0,h);a[a.length]="p";i=i.substring(h)}var j=i.indexOf("}");g[g.length]=new b(i.substring(0,j+1),k);i=i.substring(j+1);a[a.length]="b"}if(i!=""){f[f.length]=i;a[a.length]="p"}this.drawInputs()};this.buildFromFields();this.BlKPress=function(j,h,i){g[j].key(h,i)};this.makeHInput=function(){var h=d.getAttribute("name");document.getElementById("div_"+d.getAttribute("id")).innerHTML="<input type='text' readonly='readonly' name='"+h+"' value='"+this.getValue()+"'>"};this.getFName=function(){return d.getAttribute("name")};this.getValue=function(){e="";var k=0;var h=0;for(var j=0;j<a.length;j++){if(a[j]!="p"){e+=g[h].getText();h++}}return e};this.check=function(){for(var h in g){if(g[h].getText().length==0){return false}}return true}};
</script>
</head>
<body>
<div style="margin:0 auto; padding:5px; width:500px; border:1px solid #ddd; background:#fff; border-radius: 7px; -webkit-border-radius: 7px; -moz-border-radius: 7px; font:normal 14px/14px Geneva,Verdana,Arial,Helvetica,Tahoma,sans-serif;">
	<form action="http://w.qiwi.ru/setInetBill_utf.do" method="get" accept-charset="UTF-8" onSubmit="return checkSubmit();">
		<input type="hidden" name="from" value="16801"/>
		<input type="hidden" name="lifetime" value="1.0"/>
		<input type="hidden" name="check_agt" value="false"/>
		<input type="hidden" name="txn_id" value="<?= $psform['number'] ?>" />
		<input type="hidden" name="amount_rub" value="<?= $psform['amount'] ?>" />
		<input type="hidden" name="com" value="Пополнение счета клиента №<?= $user->user_id ?> на <?= $psform['amount'] ?> руб. ($<?= $psform['amount_usd'] ?>)" />
		<p style="text-align:center; color:#006699; padding:20px 0px; background:url(https://ishop.qiwi.ru/img/button/logo_31x50.jpg) no-repeat 10px 50%;">Выставить счёт за покупку</p>
		<table style="border-collapse:collapse;">
			<tr style="background:#f1f5fa;">
				<td style="color:#a3b52d; width:35%; text-align:center; padding:10px 0px;">Мобильный телефон<br />(пример: 9057772233)</td>
				<td style="padding:10px">
					<input type="text" name="to" id="idto" style="width:130px; border: 1px inset #555;"></input>
					<span id="div_idto"></span>
					<script type="text/javascript">
						inputMasks["idto"] = new Mask(document.getElementById("idto"));
						function checkSubmit() {
							if (inputMasks["idto"].getValue().match(/^\d{10}$/)) {
								document.getElementById("idto").setAttribute("disabled", "disabled");
								inputMasks["idto"].makeHInput();
								return true;
							} else {
								alert("Введите номер телефона в федеральном формате без \"8\" и без \"+7\"");
								return false;
							}
						}
					</script>
    			</td>
			</tr>
			<tr>
				<td style="color:#a3b52d; padding:10px 0px; width:45%; text-align:center;">Сумма</td>
				<td style="padding:10px">
					<?= $psform['amount'] ?> руб. 
				</td>
			</tr>
			<tr style="background:#f1f5fa;">
				<td style="color:#a3b52d; padding:10px 0px; width:45%; text-align:center;">Комментарий</td>
				<td style="padding:10px">
					Пополнение счета клиента №<?= $user->user_id ?> на <?= $psform['amount'] ?> руб. ($<?= $psform['amount_usd'] ?>)
				</td>
			</tr>
		</table>
		<p style="text-align:center;"><input type="submit" value="Выставить счёт за покупку" style=" padding:10px 0;border:none; background:url(https://ishop.qiwi.ru/img/button/superBtBlue.jpg) no-repeat 0 50%; color:#fff; width:300px;"/></p>
	</form>
</div>
</body>
</html>