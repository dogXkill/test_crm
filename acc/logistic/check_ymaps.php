
<script>
var mas_adress=new Array();
</script>
<?php
function check_adress(){
	
}
$id=$_GET['courier_id'];
$date=$_GET['date'];
$auth = false;
require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");
if(isset($_GET["date"], $_GET["courier_id"]) and check_date($_GET["date"]) and isint($_GET["courier_id"]))
{
	$sql = "
SELECT
  COALESCE(CONCAT(u.surname, ' ', u.name, ' ', u.father, ' (', u.mobile, ')'), 'Удален') AS `user`,
  t.cash_payment,
  t.prdm_sum_acc,
  t.opl_voditel,
  t.address,
  t.address_real,
  t.text,
  t.contact_phone,
  t.contact_name,
  t.comment,
  t.first_point,
  (792 - t.map_x - 8) AS map_x,
  (784 - t.map_y - 7) AS map_y,
  ROUND((792 - t.map_x) / 4 - 8) AS map_x1,
  ROUND((784 - t.map_y) / 4 - 7) AS map_y1,
  m.name AS metro,
  t.date_last,
  t.id
FROM
  courier_tasks AS t
  LEFT JOIN users AS u ON t.user_id = u.uid
  LEFT JOIN metro AS m ON t.metro_id = m.id
WHERE
  t.date = '" . format_date($_GET["date"]) . "' AND
  t.courier_id = " . $_GET["courier_id"] . " AND
  t.done = 0
ORDER BY
  t.first_point DESC,
  t.date,
  `user`,
  u.uid";
  if($result = mysql_query($sql))
  {
    $num = 1;
	while($row = mysql_fetch_assoc($result))
    {
		//print_r($row);
		$row["address"] = $row["address"];
      $row["address_real"] = $row["address_real"];
      $row["metro"] = $row["metro"];
	  if (isset($row["address"])){
		  $mas_adress['adress'][]=$row['address'];
		  $adress=$row['address'];
		  
		  ?>
		  
		  <script>
		  //console.log("<?= trim($adress);?>");
		  mas_adress.push("<?= trim($adress);?>");
		  </script>
		  <?php
	  }
	}
  }
}else{
	exit();
}
//$mas_adress=json_encode($mas_adress);
//print_r($mas_adress);
?>
<div id="map"></div>
<script>
	 
	
		
		
	

function check_adress(adress){
	var coord_yandex=null;
     myGeocoder = ymaps.geocode(adress);
		myGeocoder.then(
			function (res) {
				//alert('Координаты объекта :' + res.geoObjects.get(0).geometry.getCoordinates());
				
				coord_yandex=res.geoObjects.get(0).geometry.getCoordinates();
				//console.log(coord_yandex);
				
				//console.log(coord_yandex);
				//coords_yandex.push(coord_yandex[0]+","+coord_yandex[1]);
				//$("#list_maps").html($("#list_maps").html()+"||"+coord_yandex[0]+"|"+coord_yandex[1]);
			//
			}
		 )
		 return coord_yandex;
		}
		
function init () {
	//var mas_adress=<?= json_encode($mas_adress['adress']) ?>;
	//console.log(mas_adress);
	//coord_yandex=res.geoObjects.get(0).geometry.getCoordinates();
    var myMap = new ymaps.Map("map", {
            center: [55.7472819,37.3709697],
            zoom: 10
        }, {
            searchControlProvider: 'yandex#search'
        });

   
	for (var i=0;i<mas_adress.length;i=i+1){
		 var flag_coord=0;
		//var temp_coord=check_adress(mas_adress[i]);
		let i_i=i;
		//
		//ajax проверка на наличие координат
		
		 $.ajax({
            url: "/acc/backend/load_save_maps.php",
            type: "GET",
			async :false,
            data: {text:mas_adress[i]},
            cache: false,
            success: function(html){
				//console.log(html);
				flag_coord=html;
				
			}
			});
			
		//
		console.log(mas_adress[i]+"|coord:"+flag_coord);
		if (flag_coord==0){
			
		 myGeocoder = ymaps.geocode(mas_adress[i]);
		myGeocoder.then(
			function (res) {
				//alert('Координаты объекта :' + res.geoObjects.get(0).geometry.getCoordinates());
				//console.log(res.geoObjects.get(0));
				coord_yandex=res.geoObjects.get(0).geometry.getCoordinates();
				//console.log(coord_yandex);
				adres=res.geoObjects.get(0).properties.get('text');
				console.log("z:"+res.metaData.geocoder.request);
				kor_address=res.metaData.geocoder.request;
				//kor_address=res.
				//console.log(adres+":"+coord_yandex);
				//console.log(coord_yandex);
				//ajax запрос на запись координат
				console.log("adress(mas):"+myGeocoder.adress_key);
				$.ajax({
					url: "/acc/backend/load_save_maps.php",
					type: "GET",
					async :false,
					data: {text:adres,coord_yandex:coord_yandex,kor_address:kor_address},
					cache: false,
					success: function(html){
						
					}
					});
				//
				console.log(coord_yandex);
				var temp_k=i_i+1;
				myPlacemark = new ymaps.Placemark([coord_yandex[0],coord_yandex[1]], {
					// Чтобы балун и хинт открывались на метке, необходимо задать ей определенные свойства.
					balloonContentHeader: "<a href='#"+temp_k+"'>"+adres+"</a>",
					iconContent: temp_k
				});
				myMap.geoObjects.add(myPlacemark);
				//coords_yandex.push(coord_yandex[0]+","+coord_yandex[1]);
				//$("#list_maps").html($("#list_maps").html()+"||"+coord_yandex[0]+"|"+coord_yandex[1]);
			//
			}
		 )
		}else{
			console.log("OK"+flag_coord+"|"+mas_adress[i]+"|"+i_i);
			var tmp_cord=flag_coord.split(",");
			console.log(Number(tmp_cord[1]));
			var temp_k=i_i+1;
			myPlacemark = new ymaps.Placemark([Number(tmp_cord[0]),Number(tmp_cord[1])], {
					// Чтобы балун и хинт открывались на метке, необходимо задать ей определенные свойства.
					balloonContentHeader: "<a href='#"+temp_k+"'>"+mas_adress[i]+"</a>",
					iconContent: temp_k
				});
			myMap.geoObjects.add(myPlacemark);
		}
		//
		
	}
    

    
}
ymaps.ready(init);

		</script>
