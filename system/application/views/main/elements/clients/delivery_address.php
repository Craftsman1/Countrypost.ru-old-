<? if(!empty($this->user) AND ($this->user->user_group == 'manager' OR $this->user->user_id == $client->client_user)) : ?>
<div class="delivery_address client_tab" style="display:none;">

    <div class="table" style="min-height: 53px;">
        <div class='angle angle-lt'></div>
        <div class='angle angle-rt'></div>
        <div class='angle angle-lb'></div>
        <div class='angle angle-rb'></div>

        <table id="deliveryAddressTable">
            <tr>
                <td width="20px">&nbsp;</td>
                <td><b>Получатель</b></td>
                <td><b>Адрес</b></td>
                <td><b>Телефон</b></td>
            </tr>
            <? if ($addresses) :  ?>
            <? foreach ($addresses as $address) : ?>
                <tr id="addressRow<?=$address->address_id?>">
                    <td class="address_id"><?=$address->address_id?></td>
                    <td><?=$address->address_recipient?></td>
                    <td><?=$address->address_zip.', '.$address->address_address.', '.$address->address_town.', '.$address->country_name?></td>
                    <td><?=$address->address_phone?></td>
                </tr>
                <? endforeach; ?>
            <? endif; ?>
        </table>


    </div>

</div>
<? endif; ?>