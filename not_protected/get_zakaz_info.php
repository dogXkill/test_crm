<?php

require_once("../acc/includes/db.inc.php");

$uniq_id = mysql_real_escape_string($_GET["uniq_id"]);

//получаем общие данные о заказе и заказчике
$q = "SELECT q.uid AS zakaz_id, c.name AS name, c.email AS email, c.cont_tel AS cont_tel, q.prdm_sum_acc AS prdm_sum_acc, q.prdm_opl AS prdm_opl, q.prdm_dolg AS prdm_dolg, (SELECT COUNT(*) FROM payment_predm pp WHERE pp.query_id = q.uid) as payments_count";
$q .= " FROM queries AS q, clients AS c WHERE q.uniq_id = '$uniq_id' AND q.client_id = c.uid";
$data = mysql_query($q);

if (mysql_num_rows($data) > 0) {
    $data = mysql_fetch_assoc($data);
    // предоплата
    $prdm_opl = $data['prdm_opl'];
    $payments_count = $data['payments_count'];
    $zakaz_id = intval($data['zakaz_id']);
    // сумма заказа по товарам
    $orderSum = 0;
    $goods = '';

    if (!empty($zakaz_id)) {
        $obj_q = "SELECT name, price, num FROM `obj_accounts` WHERE query_id = '{$zakaz_id}'";
        $zakaz_data = mysql_query($obj_q);

        if ($zakaz_data) {
            while ($z_data = mysql_fetch_assoc($zakaz_data)) {
                $name = mb_convert_encoding($z_data['name'], "utf-8", "windows-1251");
                $price = $z_data['price'] * 100;
                $quantity = $z_data['num'];
                $amount = $z_data['price'] * $z_data['num'] * 100;
                $goods .= $name . "**" . $price . "**" . $quantity . "**" . $amount . "%%";

                $orderSum += $z_data['price'] * $z_data['num'];
            }
        }
    }

    // задолженность по заказу всего (с учетом предоплаты)
    $debt = $orderSum - $prdm_opl;

    $ar = mb_convert_encoding($data['name'], "utf-8", "windows-1251")."#".$data['email']."#".$data['cont_tel']."#".$debt."#".$data['zakaz_id']."#".$prdm_opl."#".$payments_count."&&";

    echo $ar.$goods;
} else {
    echo '';
}

?>