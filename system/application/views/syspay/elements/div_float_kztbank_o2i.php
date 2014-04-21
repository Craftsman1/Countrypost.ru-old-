<div class='table' id="kzt_block" style="width:550px; position:fixed; z-index: 1000; display:none; top:200px;">
    <center>
        <h3 style="margin-top:0;margin-bottom:20px;">Заявка на пополнение счета</h3>
        <em style="display:none;" class="pink-color"></em>
    </center>
    <p>
        Пополнение счета переводом с карты на карту через <b class="kzt_service_name"></b>:
        <br />
        <br />
        Вам нужно перевести <b><b class="kzt_amount"></b> тенге</b> на карту <b class="kzt_account"></b>. После перевода сохраните квитанцию.
    </p>
    <br />
    <form class='admin-inside' action="/client/addOrder2In/<?= $order->order_id ?>" enctype="multipart/form-data" method="POST">
        <input type="hidden" name="payment_service" class="kzt_payment_service" value="" />
        <input type="hidden" name="total_kzt" class="kzt_amount" value="" />
        <input type="hidden" name="total_usd" class="kzt_amount_usd" value="" />
        <table>
            <tr>
                <td>Номер карты:</td>
                <td>
                    <input type="text" name="account" maxlength="20" value="" />
                    <i>Пример: 7790****2198</i>
                </td>
            </tr>
            <tr>
                <td>Фото квитанции:
                    <br />(максимальный размер 3Mb)
                </td>
                <td><input type="file" name="userfile" value="" /></td>
            </tr>
            <tr class='last-row'>
                <td colspan='2'>
                    <div class='float'>    
                        <div class='submit'>
                            <div>
                                <input type='submit' name="add" value='Добавить заявку' />
                            </div>
                        </div>
                        <div class='submit'>
                            <div>
                                <input type='button' value='Отмена' onclick="$('#lay').fadeOut('slow');$('#kzt_block').fadeOut('slow');"/>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </form>
</div>
<script type="text/javascript">
    var kzt_click = 0;

    function openKZTPopup(user_id, amount_usd, amount_kzt, service)
    {
        $('#kzt_user_id').html(user_id);
        $('.kzt_amount_usd').val(amount_usd);
        $('.kzt_amount').html(amount_kzt).val(amount_kzt);
		$('.kzt_payment_service').val(service);
		$('.kzt_service_name').html(getServiceName(service)).css('font-weight', 'normal');
		$('.kzt_account').html(getAccount(service)).css('font-weight', 'normal');
        
        var offsetLeft    = window.innerWidth / 2 - 280;
        
        $('#kzt_block').css({
            'left' : offsetLeft
        });
        
        $('#lay').css({
            'width': document.body.clientWidth,
            'height': document.body.clientHeight
        });
        
        $('#lay').fadeIn("slow");
        $('#kzt_block').fadeIn("slow");
        
        if (!kzt_click)
        {
            kzt_click = 1;
            $('#lay').click(function(){
                $('#lay').fadeOut("slow");
                $('#kzt_block').fadeOut("slow");
            })
        }
    }
	
	function getServiceName(service)
	{
		var name = '';
	
		switch (service) 
		{
	        case "bta": name = '<?= BTA_SERVICE_NAME ?>'; break;
            case "ccr": name = '<?= CCR_SERVICE_NAME ?>'; break;
            case "kkb": name = '<?= KKB_SERVICE_NAME ?>'; break;
            case "nb": name = '<?= NB_SERVICE_NAME ?>'; break;
            case "tb": name = '<?= TB_SERVICE_NAME ?>'; break;
            case "atf": name = '<?= ATF_SERVICE_NAME ?>'; break;
            case "ab": name = '<?= AB_SERVICE_NAME ?>'; break;
    	}
		
		return name;
	}
	
	function getAccount(service)
	{
		var name = '';
	
		switch (service) 
		{
	        case "bta": name = '<?= BTA_IN_ACCOUNT ?>'; break;
            case "ccr": name = '<?= CCR_IN_ACCOUNT ?>'; break;
            case "kkb": name = '<?= KKB_IN_ACCOUNT ?>'; break;
            case "nb": name = '<?= NB_IN_ACCOUNT ?>'; break;
            case "tb": name = '<?= TB_IN_ACCOUNT ?>'; break;
            case "atf": name = '<?= ATF_IN_ACCOUNT ?>'; break;
            case "ab": name = '<?= AB_IN_ACCOUNT ?>'; break;
    	}
		
		return name;
	}
</script>