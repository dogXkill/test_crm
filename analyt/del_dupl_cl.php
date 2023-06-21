<html>

<head>
  <title>Удаление дубликатов</title>
  <style type="text/css">
  <!--
  table, th, td { border: 1px solid black; border-spacing: 0px; font-size:15px;}
   th { text-align: center; }
   tr:hover { background-color: #E0E0FF; }

   label{cursor:pointer;}

  -->
  </style>
</head>

<body>

<?

require_once("acc/includes/db.inc.php");

 //date_format(q.date_query, '%d.%m.%Y')

print "<table><tr><th width=60>id</th><th>короткое название</th><th>полное название</th><th>адрес доставки</th><th>юрадрес</th><th>инн</th><th>контактное лицо</th><th>контактный телефон</th><th>емейл</th><th>гендир</th><th>дата посл заказа</th><th>id менеджера</th><th width=100>связь</th></tr>";

$q = "SELECT c.uid, short, name, legal_address, postal_address, deliv_address, inn, cont_pers, cont_tel, firm_tel, email, gen_dir, ogrn, date_format(q.date_query, '%d.%m.%Y') AS date_query, sphere, sphere_other, kak_uznali, q.user_id, q.date_query AS date_q FROM clients AS c, queries AS q WHERE date_query > '2008-01-01 00:00:00' AND c.uid = q.client_id ORDER BY date_q DESC LIMIT 0,1";
$get = mysql_query($q);



while($g =  mysql_fetch_assoc($get)){

$uid = $g[uid];
$short = $g[short];
$name = $g[name];
$legal_address = $g[legal_address];
$deliv_address = $g[deliv_address];
$inn = $g[inn];
$cont_pers = $g[cont_pers];
$cont_tel = $g[cont_tel];
$firm_tel = $g[firm_tel];
$email = $g[email];
$gen_dir = $g[gen_dir];
$date_query = $g[date_query];
$user_id = $g[user_id];

show_tbl($uid,$short,$name,$deliv_address,$legal_address,$inn,$cont_pers,$cont_tel,$email,$gen_dir,$date_query,$user_id,"");

$q2 = "SELECT c.uid, short, name, legal_address, postal_address, deliv_address, inn, cont_pers, cont_tel, firm_tel, email, gen_dir, ogrn, date_format(q.date_query, '%d.%m.%Y') AS date_query, sphere, sphere_other, kak_uznali, q.user_id, q.date_query AS date_q
FROM clients AS c, queries AS q
WHERE
((email = '$email' AND email <> '') OR (inn = '$inn' AND inn <> '') OR (ogrn = '$ogrn' AND ogrn <> '') OR (firm_tel = '$firm_tel' AND firm_tel <> '') OR (cont_tel = '$cont_tel' AND cont_tel <> ''))
AND date_query > '2008-01-01 00:00:00' AND c.uid = q.client_id AND c.uid <> '$uid' ORDER BY date_q DESC LIMIT 0,100";
$get2 = mysql_query($q2);
while($g2 =  mysql_fetch_assoc($get2)){
$uid1 = $g2[uid];
$short1 = $g2[short];
$name1 = $g2[name];
$legal_address1 = $g2[legal_address];
$deliv_address1 = $g2[deliv_address];
$inn1 = $g2[inn];
$cont_pers1 = $g2[cont_pers];
$cont_tel1 = $g2[cont_tel];
$firm_tel1 = $g2[firm_tel];
$email1 = $g2[email];
$gen_dir1 = $g2[gen_dir];
$date_query1 = $g2[date_query];
$user_id1 = $g2[user_id];

show_tbl($uid1,$short1,$name1,$deliv_address1,$legal_address1,$inn1,$cont_pers1,$cont_tel1,$email1,$gen_dir1,$date_query1,$user_id1,"&rArr;");

}
//queries.client_id

}

function show_tbl($uid,$short,$name,$deliv_address,$legal_address,$inn,$cont_pers,$cont_tel,$email,$gen_dir,$date_query,$user_id,$type){
print "<tr><td>$type $uid</td><td>$short</td><td>$name</td><td>$deliv_address</td><td>$legal_address</td><td>$inn</td><td>$cont_pers</td><td>$cont_tel</td><td>$email</td><td>$gen_dir</td><td>$date_query</td><td>$user_id</td><td>
<label for=\"chk_$uid\">связанные</label> <input type=checkbox id=\"chk_$uid\" value=\"$uid\"/><br>
<label for=\"rd_$uid\">основная</label>  <input type=radio name=\"osnovn\" id=\"rd_$uid\" value=\"$uid\"/>
</td></tr>";

}
print "</table>";
echo mysql_error();
?>
 </body>

</html>
