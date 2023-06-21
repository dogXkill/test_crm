<?php
//собираем данные и пишем в txt 200 строк последних
require_once("/home/crmu660633/test.upak.me/docs/acc/includes/db.inc.php");
//queries
$pol_quer="qu.uid uid_qu,qu.client_id,qu.date_query,qu.prdm_sum_acc,qu.deleted";
//clients
$pol_clients="cl.uid uid_c, cl.short, cl.name, cl.inn, cl.kpp, cl.okpo, cl.postal_address, cl.legal_address, cl.bank, cl.bik, cl.korr_acc, cl.rs_acc, cl.dogov_num";
//obj_accounts
$pol_obj="art_num, query_id, nn, name, num, price, deleted";
$limit=200;
$ysl="'qu.deleted'<>1";
$sql="SELECT {$pol_quer},{$pol_clients} FROM queries qu LEFT JOIN clients cl ON cl.uid=qu.client_id WHERE {$ysl}  GROUP BY qu.uid DESC LIMIT {$limit}";

/*структура
------
uid_qu|client_id|date_query|prdm_sum_acc|deleted|uid_c|short
------
end структура*/
$result = mysql_query($sql);
//обработка результата и отправка уведомлений
$data="";//переменная для данных
while($r = mysql_fetch_array($result)) {
	$data.="{$r['uid_qu']}|{$r['client_id']}|{$r['date_query']}|{$r['prdm_sum_acc']}|{$r['deleted']}|{$r['uid_c']}|{$r['short']}|{$r['name']}|{$r['inn']}|{$r['kpp']}|{$r['okpo']}|{$r['postal_address']}|{$r['legal_address']}|{$r['bank']}|{$r['bik']}|{$r['korr_acc']}|{$r['rs_acc']}|{$r['dogov_num']}\n";
	$data1.="{$r['uid_qu']}|{$r['client_id']}|{$r['date_query']}|{$r['prdm_sum_acc']}|{$r['deleted']}|{$r['uid_c']}|{$r['short']}|{$r['name']}|{$r['inn']}|{$r['kpp']}|{$r['okpo']}|{$r['postal_address']}|{$r['legal_address']}|{$r['bank']}|{$r['bik']}|{$r['korr_acc']}|{$r['rs_acc']}|{$r['dogov_num']}</br>";
	
	$query_id=$r['uid_qu'];
	$sql1="SELECT {$pol_obj} FROM obj_accounts qu WHERE `query_id`={$query_id}";
	//echo $sql1."</br>";
	$result1 = mysql_query($sql1);
	while($r1 = mysql_fetch_array($result1)) {
		$data.="{$r1['art_num']}|{$r1['query_id']}|{$r1['nn']}|{$r1['name']}|{$r1['num']}|{$r1['price']}|{$r1['deleted']}\n";
		$data1.="{$r1['art_num']}|{$r1['query_id']}|{$r1['nn']}|{$r1['name']}|{$r1['num']}|{$r1['price']}|{$r1['deleted']}</br>";
	}
}
$filename = __DIR__ . '/file.txt';
 
file_put_contents($filename, $data);
echo $data1;
//echo file_get_contents($filename);
?>