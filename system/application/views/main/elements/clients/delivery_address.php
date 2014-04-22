<? if(!empty($this->user) AND ($this->user->user_group == 'manager' OR $this->user->user_id == $client->client_user)) : ?>
<div class="delivery_address client_tab" style="display:none;">

    <div class="table" style="min-height: 53px;">
        <div class='angle angle-lt'></div>
        <div class='angle angle-rt'></div>
        <div class='angle angle-lb'></div>
        <div class='angle angle-rb'></div>

        <table id="deliveryAddressTable">
            <tr>
                <th width="20px">&nbsp;</th>
                <th><b>Получатель</b></th>
                <th><b>Адрес</b></th>
                <th><b>Телефон</b></th>
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