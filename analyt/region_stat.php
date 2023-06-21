<html>

<head>
  <title>Распределение клиентов по городам / областям</title>
<? $str = $_SERVER['QUERY_STRING'];
parse_str($str);
 ?>

</head>

<body>
<a href="index.php">В главное меню</a>

<form action="" method=get>

<select name="type" id="type">
<option value="by_regions" <?if($type=="by_regions"){echo " selected";}?>>по регионам</option>
<option value="by_cities" <?if($type=="by_cities"){echo " selected";}?>>по 174 крупнейшим городам</option>

</select>

Получить данные начиная с
<select id=year_from name=year_from>
<option value="2010" <?if($year_from=="2010"){echo " selected";}?>>2010</option>
<option value="2011" <?if($year_from=="2011"){echo " selected";}?>>2011</option>
<option value="2012" <?if($year_from=="2012"){echo " selected";}?>>2012</option>
<option value="2013" <?if($year_from=="2013"){echo " selected";}?>>2013</option>
<option value="2014" <?if($year_from=="2014"){echo " selected";}?>>2014</option>
<option value="2015" <?if($year_from=="2015"){echo " selected";}?>>2015</option>
<option value="2016" <?if($year_from=="2016"){echo " selected";}?>>2016</option>
<option value="2017" <?if($year_from=="2017"){echo " selected";}?>>2017</option>
<option value="2018" <?if($year_from=="2018"){echo " selected";}?>>2018</option>
<option value="2019" <?if($year_from=="2019"){echo " selected";}?>>2019</option>
<option value="2020" <?if($year_from=="2020"){echo " selected";}?>>2020</option>
<option value="2021" <?if($year_from=="2021"){echo " selected";}?>>2021</option>
<option value="2022" <?if($year_from=="2022"){echo " selected";}?>>2022</option>




</select>


<select name="typ_ord" id="typ_ord">
<option value="0" <?if($typ_ord=="0"){echo " selected";}?>>все заказы</option>
<option value="1" <?if($typ_ord=="1"){echo " selected";}?>>под заказ</option>
<option value="2" <?if($typ_ord=="2"){echo " selected";}?>>магазин</option>
<option value="3" <?if($typ_ord=="3"){echo " selected";}?>>магазин с лого</option>
</select>

сортировка:
<select name="sort_by" id="sort_by">
<option value="num_ord" <?if($sort_by=="num_ord"){echo " selected";}?>>количество заказов</option>
<option value="viruchka" <?if($sort_by=="viruchka"){echo " selected";}?>>выручка</option>
<option value="sredn_ord" <?if($sort_by=="sredn_ord"){echo " selected";}?>>средний заказ</option>
</select>

<input type="hidden" name="act" value="do" /><input type=submit value=">>>">

 </form>


<?
 require_once("../acc/includes/db.inc.php");




 if($act == "do"){

 if($typ_ord == "0"){$typ_ord_vst = "";}else{$typ_ord_vst = " AND q.typ_ord = '$typ_ord' ";}


 if($type == "by_regions"){
$region_arr = array(
array("Амурская", "Благовещенск"),
array("Архангельская", "Архангельск"),
array("Астраханская", "Астрахань"),
array("Белгородская", "Белгород"),
array("Брянская", "Брянск"),
array("Владимирская", "Владимир"),
array("Волгоградская", "Волгоград"),
array("Вологодская", "Вологда"),
array("Воронежская", "Воронеж"),
array("Ивановская", "Иваново"),
array("Иркутская", "Иркутск"),
array("Калининградская", "Калининград"),
array("Калужская", "Калуга"),
array("Камчатская", "Петропавловск-Камчатский"),
array("Кемеровская", "Кемерово"),
array("Кировская", "Киров"),
array("Костромская", "Кострома"),
array("Курганская", "Курган"),
array("Курская", "Курск"),
array("Ленинградская", "Петербург"),
array("Липецкая", "Липецк"),
array("Магаданская", "Магадан"),
array("Московская", "Москва"),
array("Мурманская", "Мурманск"),
array("Нижегородская", "Нижний Новгород"),
array("Новгородская", "Новгород"),
array("Новосибирская", "Новосибирск"),
array("Омская", "Омск"),
array("Оренбургская", "Оренбург"),
array("Орловская", "Орёл"),
array("Пензенская", "Пенза"),
array("Пермская", "Пермь"),
array("Псковская", "Псков"),
array("Ростовская", "Ростов-на-Дону"),
array("Рязанская", "Рязань"),
array("Самарская", "Самара"),
array("Саратовская", "Саратов"),
array("Сахалинская", "Южно-Сахалинск"),
array("Свердловская", "Екатеринбург"),
array("Смоленская", "Смоленск"),
array("Тамбовская", "Тамбов"),
array("Тульская", "Тула"),
array("Тюменская", "Тюмень"),
array("Ульяновская", "Ульяновск"),
array("Челябинская", "Челябинск"),
array("Читинская", "Чита"),
array("Ярославская", "Ярославль"),
array("Адыгея", "Майкоп"),
array("Алтай", "Горно-Алтайск"),
array("Башкирия", "Уфа"),
array("Бурятия", "Улан-Уде"),
array("Дагестан", "Махачкала"),
array("Еврейская", "Биробиджан"),
array("Ингушетия", "Магас"),
array("Кабардино-Балкария", "Нальчик"),
array("Калмыкия", "Элиста"),
array("Карачаево-Черкессия", "Черкесск"),
array("Карелия", "Петрозавосдск"),
array("Коми", "Сыктывкар"),
array("Крым", "Симферополь"),
array("Марий эл", "Йошкар-Ола"),
array("Мордовия", "Саранск"),
array("Якутия", "Якутск"),
array("Северная Осетия", "Владикавказ"),
array("Татарстан", "Казань"),
array("Тыва", "Кызыл"),
array("Удмуртия", "Ижевск"),
array("Хакасия", "Абакан"),
array("Чечня", "Грозный"),
array("Чувашия", "Чебоксары"),
array("Алтайский", "Барнаул"),
array("Краснодарский", "Краснодар"),
array("Красноярский", "Красноярск"),
array("Приморский", "Владивосток"),
array("Ставропольский", "Ставрополь"),
array("Хабаровский", "Хабаровск"),
array("Ненецкий", "Нарьян-Мар"),
array("Ханты-Мансийский", "Ханты-Мансийск"),
array("Чукотский", "Анадырь"),
array("Ямало-Ненецкий", "Салехард")

);
}

if($type == "by_cities"){
$region_arr = array(
array("Москва", ""),
array("Санкт-Петербург", ""),
array("Новосибирск", ""),
array("Екатеринбург", ""),
array("Нижний Новгород", ""),
array("Казань", ""),
array("Челябинск", ""),
array("Омск", ""),
array("Самара", ""),
array("Ростов-на-Дону", ""),
array("Уфа", ""),
array("Красноярск", ""),
array("Воронеж", ""),
array("Пермь", ""),
array("Волгоград", ""),
array("Краснодар", ""),
array("Саратов", ""),
array("Тюмень", ""),
array("Тольятти", ""),
array("Ижевск", ""),
array("Барнаул", ""),
array("Ульяновск", ""),
array("Иркутск", ""),
array("Хабаровск", ""),
array("Ярославль", ""),
array("Владивосток", ""),
array("Махачкала", ""),
array("Томск", ""),
array("Оренбург", ""),
array("Кемерово", ""),
array("Новокузнецк", ""),
array("Рязань", ""),
array("Астрахань", ""),
array("Набережные Челны", ""),
array("Пенза", ""),
array("Киров", ""),
array("Липецк", ""),
array("Чебоксары", ""),
array("Балашиха", ""),
array("Калининград", ""),
array("Тула", ""),
array("Курск", ""),
array("Севастополь", ""),
array("Сочи", ""),
array("Ставрополь", ""),
array("Улан-Удэ", ""),
array("Тверь", ""),
array("Магнитогорск", ""),
array("Брянск", ""),
array("Иваново", ""),
array("Белгород", ""),
array("Сургут", ""),
array("Владимир", ""),
array("Чита", ""),
array("Нижний Тагил", ""),
array("Архангельск", ""),
array("Симферополь", ""),
array("Калуга", ""),
array("Смоленск", ""),
array("Волжский", ""),
array("Якутск", ""),
array("Саранск", ""),
array("Череповец", ""),
array("Курган", ""),
array("Вологда", ""),
array("Орёл", ""),
array("Грозный", ""),
array("Владикавказ", ""),
array("Подольск", ""),
array("Тамбов", ""),
array("Мурманск", ""),
array("Петрозаводск", ""),
array("Нижневартовск", ""),
array("Стерлитамак", ""),
array("Кострома", ""),
array("Новороссийск", ""),
array("Йошкар-Ола", ""),
array("Химки", ""),
array("Таганрог", ""),
array("Сыктывкар", ""),
array("Комсомольск-на-Амуре", ""),
array("Нижнекамск", ""),
array("Нальчик", ""),
array("Шахты", ""),
array("Дзержинск", ""),
array("Братск", ""),
array("Орск", ""),
array("Благовещенск", ""),
array("Энгельс", ""),
array("Ангарск", ""),
array("Великий Новгород", ""),
array("Королёв", ""),
array("Старый Оскол", ""),
array("Мытищи", ""),
array("Псков", ""),
array("Люберцы", ""),
array("Южно-Сахалинск", ""),
array("Бийск", ""),
array("Прокопьевск", ""),
array("Армавир", ""),
array("Балаково", ""),
array("Абакан", ""),
array("Рыбинск", ""),
array("Северодвинск", ""),
array("Петропавловск-Камчатский", ""),
array("Норильск", ""),
array("Уссурийск", ""),
array("Волгодонск", ""),
array("Красногорск", ""),
array("Сызрань", ""),
array("Новочеркасск", ""),
array("Каменск-Уральский", ""),
array("Златоуст", ""),
array("Электросталь", ""),
array("Альметьевск", ""),
array("Миасс", ""),
array("Керчь", ""),
array("Салават", ""),
array("Копейск", ""),
array("Находка", ""),
array("Пятигорск", ""),
array("Хасавюрт", ""),
array("Майкоп", ""),
array("Рубцовск", ""),
array("Березники", ""),
array("Коломна", ""),
array("Одинцово", ""),
array("Ковров", ""),
array("Домодедово", ""),
array("Кисловодск", ""),
array("Нефтекамск", ""),
array("Нефтеюганск", ""),
array("Батайск", ""),
array("Новочебоксарск", ""),
array("Дербент", ""),
array("Серпухов", ""),
array("Щёлково", ""),
array("Каспийск", ""),
array("Черкесск", ""),
array("Новомосковск", ""),
array("Первоуральск", ""),
array("Раменское", ""),
array("Назрань", ""),
array("Кызыл", ""),
array("Обнинск", ""),
array("Орехово-Зуево", ""),
array("Новый Уренгой", ""),
array("Невинномысск", ""),
array("Димитровград", ""),
array("Октябрьский", ""),
array("Долгопрудный", ""),
array("Камышин", ""),
array("Ессентуки", ""),
array("Муром", ""),
array("Жуковский", ""),
array("Евпатория", ""),
array("Новошахтинск", ""),
array("Реутов", ""),
array("Пушкино", ""),
array("Артём", ""),
array("Северск", ""),
array("Ноябрьск", ""),
array("Ачинск", ""),
array("Арзамас", ""),
array("Бердск", ""),
array("Элиста", ""),
array("Ногинск", ""),
array("Елец", ""),
array("Сергиев Посад", ""),
array("Новокуйбышевск", ""),
array("Железногорск", "")
 );

}


foreach($region_arr as $rgn => $massiv)
{

$obl = $massiv[0];
$city = $massiv[1];

if($type == "by_cities"){
$q = "SELECT COUNT(*), SUM(q.prdm_sum_acc) FROM clients AS c, queries AS q WHERE (legal_address LIKE '% $obl %' OR postal_address LIKE '% $obl %' OR deliv_address LIKE '% $obl %') AND c.uid = q.client_id AND q.date_query > '$year_from-01-01 00:00:00' $typ_ord_vst";

}
else{
 $q = "SELECT COUNT(*), SUM(q.prdm_sum_acc) FROM clients AS c, queries AS q WHERE (legal_address LIKE '%$obl%' OR postal_address LIKE '%$obl%' OR deliv_address LIKE '%$obl%' OR legal_address LIKE '%$city%' OR postal_address LIKE '%$city%' OR deliv_address LIKE '%$city%') AND c.uid = q.client_id AND q.date_query > '$year_from-01-01 00:00:00' $typ_ord_vst";

}

$reg = mysql_query($q);


$r = mysql_fetch_array($reg);

$num_ord = $r[0];
$viruchka = round($r[1]);
if($viruchka > 0 and $num_ord > 0){
$sredn_ord = round($viruchka/$num_ord);}else{$sredn_ord = "-";}

$regions_stat[$obl]["city"] .= $city;
$regions_stat[$obl]["num_ord"] .= $num_ord;
$regions_stat[$obl]["viruchka"] .= $viruchka;
$regions_stat[$obl]["sredn_ord"] .= $sredn_ord;



}




function cmp_function_desc($a, $b){

  $sort_by = $_GET["sort_by"];
  if ($a[$sort_by] == $b[$sort_by]) {
    return 0;
  }

  return ($a[$sort_by] > $b[$sort_by]) ? -1 : 1;
}

uasort($regions_stat, 'cmp_function_desc');

echo "<table border=1 cellpadding=10><tr><td><b>#</b></td><td><b>Область/край/республика</b></td><td><b>Город</b></td><td><b>Кол-во заказов</b></td><td><b>Выручка</b></td><td><b>Средний заказ</b></td></tr>";
$i = 0;  
foreach($regions_stat as $obl => $val)
{

$i = $i + 1;
//echo $regions_stat[$obl][num_ord];
$city = $regions_stat[$obl][city];
$num_ord = $regions_stat[$obl][num_ord];
$viruchka = $regions_stat[$obl][viruchka];
$sredn_ord = $regions_stat[$obl][sredn_ord];
echo "<tr><td>$i</td><td align=center>$obl</td><td align=center>$city</td><td align=center>$num_ord</td><td align=center>$viruchka</td><td align=center>$sredn_ord</td></tr>";

}
echo "</table>";

   /* echo "<pre>";
    print_r($regions_stat);
    echo "<pre>"; */

  }
?>
 </body>

</html>
