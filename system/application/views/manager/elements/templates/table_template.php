<? $n = count($data)+1; ?>
<? $lines = floor($n / $cols); ?>
<? $k = 1; ?>

<?=$before_table_text?>
<table border="0" cellpadding="0" cellspacing="0" width="<?=$width.'px'?>" style="border-collapse: collapse; width: <?=$width.'pt'?>">

    <tr>
        <? for ($col = 1; $col <= $cols; $col++) : ?>
        <th style="text-align: center;">КГ</th>
        <th style="text-align: center;"><?=$currency_table_text?></th>
        <? endfor; ?>
    </tr>

    <? for($i = 1; $i <= $lines; $i ++) : ?>
        <tr height="21" style="height:15.75pt">
        <? for($ii = 0; $ii <= $cols-1; $ii++) : ?>
            <td style="text-align: center; height: 15.75pt; width: 48pt; font-size: 12.0pt; font-weight: 700; color: black; font-style: normal; text-decoration: none; font-family: Calibri, sans-serif; text-align: general; vertical-align: bottom; white-space: nowrap; border: 1px solid rgb(215,215,215); padding-left: 1px; padding-right: 1px; padding-top: 1px">
                <? if (isset($data[$k+($lines*$ii)])) {
                    $res = $data[$k+($lines*$ii)];
                    echo $res[0];
                    $showflag = true;
                }else{
                    echo "";
                    $showflag = false;
                }
                ?>
                </td>
            <td style="text-align: center; height: 15.75pt; width: 48pt; font-size: 12.0pt; font-weight: 700; color: black; font-style: normal; text-decoration: none; font-family: Calibri, sans-serif; text-align: general; vertical-align: bottom; white-space: nowrap; border: 1px solid rgb(215,215,215); padding-left: 1px; padding-right: 1px; padding-top: 1px">
                <?
                if ($showflag) {
                    echo $res[1];
                }else{
                    echo "";
                }
                ?>
            </td>
        <? endfor; ?>
    </tr>
    <?$k++;?>
    <? endfor; ?>

</table>
<?=$after_table_text?>
<br /><br />