<!DOCTYPE HTML>

<html>

<head>
  <title>Работа с базой и аналитика</title>
</head>
<script type="text/javascript" src="../acc/includes/js/jquery-1.11.3.min.js"></script>
<script>
function show_general_stat(){
month_num_from = $("#month_num_from").val()
year_num_from = $("#year_num_from").val()
month_num_to = $("#month_num_to").val()
year_num_to = $("#year_num_to").val()
window.open('../acc/backend/general_stat.php?month_num_from='+month_num_from+'&year_num_from='+year_num_from+'&month_num_to='+month_num_to+'&year_num_to='+year_num_to,"MyWin", "");
}

</script>

<?php
$tek_year = date("Y");
$tek_month = date("m"); ?>
<body>
<h2>Работа с базой и аналитика</h2>

<a href="getcl.php">Выгрузить клиентов (телефоны, количество заказов, последний заказ)</a><br>
<a href="del_dupl_cl1.php">Поиск дубликатов клиентов</a><br>
<a href="abc_cl.php">ABC отчет по клиентам</a><br>
<a href="new_old.php">Соотношение новых и старых клиентов</a><br>
<a href="abcxyz_cl.php">ABCXYZ отчет по клиентам</a><br>
<a href="goods_groups.php">Разбить товары на группы</a><br>
<a href="goods_abc.php">ABC отчет по товарным группам</a><br>
<a href="goods_abc.php">ABCXYZ отчет по товарным группам</a><br>
<a href="region_stat.php">Статистика по регионам</a><br>
<a href="set_uniq_id.php">Проставить uniq_id по заказам</a><br>
<br><br>
<a href="update_rpr.php">Проставить с/с в базе</a><br>
<a href="export.php">Выгрузить на сайт таблицу plan_arts</a>
<br><br>
<a href="http://crm.upak.me/fix/showDoubles.php" target="_blank">Поиск дублей</a><br>
<a href="http://crm.upak.me/fix/phoneNumbers.php" target="_blank">Формат номеров телефонов</a><br>
<a href="http://crm.upak.me/fix/mergeClients.php?by=temp_phone&process=1" target="_blank">Мерж клиентов по номеру телефона</a><br>
<a href="http://crm.upak.me/fix/mergeClients.php?by=email&process=1" target="_blank">Мерж клиентов по емейлу</a><br>
<a href="http://crm.upak.me/fix/mergeClients.php?by=inn&process=1" target="_blank">Мерж клиентов по ИНН</a><br>

 <br><br>

 общая статитстика с
<select id=month_num_from name=month_num_from>
<option value="01" <?if($tek_month=="01"){echo " selected";}?>>январь</option>
<option value="02" <?if($tek_month=="02"){echo " selected";}?>>февраль</option>
<option value="03" <?if($tek_month=="03"){echo " selected";}?>>март</option>
<option value="04" <?if($tek_month=="04"){echo " selected";}?>>апрель</option>
<option value="05" <?if($tek_month=="05"){echo " selected";}?>>май</option>
<option value="06" <?if($tek_month=="06"){echo " selected";}?>>июнь</option>
<option value="07" <?if($tek_month=="07"){echo " selected";}?>>июль</option>
<option value="08" <?if($tek_month=="08"){echo " selected";}?>>август</option>
<option value="09" <?if($tek_month=="09"){echo " selected";}?>>сентябрь</option>
<option value="10" <?if($tek_month=="10"){echo " selected";}?>>октябрь</option>
<option value="11" <?if($tek_month=="11"){echo " selected";}?>>ноябрь</option>
<option value="12" <?if($tek_month=="12"){echo " selected";}?>>декабрь</option>
</select>
<select id=year_num_from name=year_num_from>
<option value="2010" <?if($tek_year=="2010"){echo " selected";}?>>2010</option>
<option value="2011" <?if($tek_year=="2011"){echo " selected";}?>>2011</option>
<option value="2012" <?if($tek_year=="2012"){echo " selected";}?>>2012</option>
<option value="2013" <?if($tek_year=="2013"){echo " selected";}?>>2013</option>
<option value="2014" <?if($tek_year=="2014"){echo " selected";}?>>2014</option>
<option value="2015" <?if($tek_year=="2015"){echo " selected";}?>>2015</option>
<option value="2016" <?if($tek_year=="2016"){echo " selected";}?>>2016</option>
<option value="2017" <?if($tek_year=="2017"){echo " selected";}?>>2017</option>
<option value="2018" <?if($tek_year=="2018"){echo " selected";}?>>2018</option>
<option value="2019" <?if($tek_year=="2019"){echo " selected";}?>>2019</option>
<option value="2020" <?if($tek_year=="2020"){echo " selected";}?>>2020</option>
<option value="2021" <?if($tek_year=="2021"){echo " selected";}?>>2021</option>
<option value="2022" <?if($tek_year=="2022"){echo " selected";}?>>2022</option>
</select>
по
<select id=month_num_to name=month_num_to>
<option value="01" <?if($tek_month=="01"){echo " selected";}?>>январь</option>
<option value="02" <?if($tek_month=="02"){echo " selected";}?>>февраль</option>
<option value="03" <?if($tek_month=="03"){echo " selected";}?>>март</option>
<option value="04" <?if($tek_month=="04"){echo " selected";}?>>апрель</option>
<option value="05" <?if($tek_month=="05"){echo " selected";}?>>май</option>
<option value="06" <?if($tek_month=="06"){echo " selected";}?>>июнь</option>
<option value="07" <?if($tek_month=="07"){echo " selected";}?>>июль</option>
<option value="08" <?if($tek_month=="08"){echo " selected";}?>>август</option>
<option value="09" <?if($tek_month=="09"){echo " selected";}?>>сентябрь</option>
<option value="10" <?if($tek_month=="10"){echo " selected";}?>>октябрь</option>
<option value="11" <?if($tek_month=="11"){echo " selected";}?>>ноябрь</option>
<option value="12" <?if($tek_month=="12"){echo " selected";}?>>декабрь</option>
</select>
<select id=year_num_to name=year_num_to>
<option value="2010" <?if($tek_year=="2010"){echo " selected";}?>>2010</option>
<option value="2011" <?if($tek_year=="2011"){echo " selected";}?>>2011</option>
<option value="2012" <?if($tek_year=="2012"){echo " selected";}?>>2012</option>
<option value="2013" <?if($tek_year=="2013"){echo " selected";}?>>2013</option>
<option value="2014" <?if($tek_year=="2014"){echo " selected";}?>>2014</option>
<option value="2015" <?if($tek_year=="2015"){echo " selected";}?>>2015</option>
<option value="2016" <?if($tek_year=="2016"){echo " selected";}?>>2016</option>
<option value="2017" <?if($tek_year=="2017"){echo " selected";}?>>2017</option>
<option value="2018" <?if($tek_year=="2018"){echo " selected";}?>>2018</option>
<option value="2019" <?if($tek_year=="2019"){echo " selected";}?>>2019</option>
<option value="2020" <?if($tek_year=="2020"){echo " selected";}?>>2020</option>
<option value="2021" <?if($tek_year=="2021"){echo " selected";}?>>2021</option>
<option value="2022" <?if($tek_year=="2022"){echo " selected";}?>>2022</option>
</select> <input type=button value=">>" onclick="show_general_stat()">


<br><br>
<div style="width:600px;">Обновление таблицы plan_arts происходит через через планировщик заданий open server каждый день в 10.00, (файл /plan/synch.php?type=app_stat),
выгрузка обновленной таблицы на ftp сайта происходит каждый день в 10.15 на сайт также через планировщик заданий open server) (файл /analyt/export.php).
На сайте, выгруженные в директорию /docs/upd файлы  /upd/plan_arts_upd.sql и /upd/shop_goods_upd.sql  и содержащие чистый SQL,
обрабатываются файлом  <a href="http://www.paketoff.ru/content/controllers/AnyController.php" target="_blank">paketoff.ru/docs/content/controllers/AnyController.php</a>  запускаемым через Список задач в ЛК провайдера в 10.30 ежедневно.</div>
</body>

</html>