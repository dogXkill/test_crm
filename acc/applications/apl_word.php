<?
	require_once("../includes/db.inc.php");
	require_once("../includes/lib.php");





  // Класс для работы с форматами дат
class dat_fn
{
	var $basedat 		= '';				// формат базы DATE
	var $basetm 		= '';				// формат базы DATETIME
	var $tmstamp 		= 0;				// tamestamp
	var $stringdat	= '';				// формат 12.05.2009
	var $stringtm		= '';				// формат 12.05.2009 12:49




	//	$TP:
	//	0 - формат базы данных 0000-00-00 00:00:00
	//	1 - формат базы данных 0000-00-00
	//	2 - формат 01.02.2009 16:54
	//	3 - формат 01.02.2009

	function dat_fn($dat, $tp = 0) {
		if($tp ==0) {		// формат базы данных 0000-00-00 00:00:00

				if((!trim($dat)) || ($dat == '0000-00-00 00:00:00'))
					return 0;

				preg_match('/(\d{4})-(\d{1,2})-(\d{1,2})\s(\d{1,2}):(\d{1,2}):(\d{1,2})/', $dat, $t);

				// timestamp
				$this -> tmstamp 		= mktime($t[4],$t[5],$t[6],$t[2],$t[3],$t[1]);
				$this -> basedat 		= date("Y-m-d", $this -> tmstamp);
				$this -> basetm 		= $dat;
				$this -> stringdat	= date("d.m.Y", $this -> tmstamp);
				$this -> stringtm		= date("d.m.Y H:i", $this -> tmstamp);

		} elseif($tp == 1) {	// формат базы данных 0000-00-00

				if((!trim($dat)) || ($dat == '0000-00-00'))
					return 0;

				preg_match('/(\d{4})-(\d{1,2})-(\d{1,2})/', $dat, $t);

				// timestamp
				$this -> tmstamp 		= mktime(0,0,0,$t[2],$t[3],$t[1]);
				$this -> basedat 		= $dat;
				$this -> basetm 		= date("Y-m-d 00:00:00", $this -> tmstamp);
				$this -> stringdat	= date("d.m.Y", $this -> tmstamp);
				$this -> stringtm		= date("d.m.Y H:i", $this -> tmstamp);

		} elseif($tp == 2) {	// формат 01.02.2009 16:54

				if((!trim($dat)) || ($dat == '00.00.0000 00:00'))
					return 0;

				preg_match('/(\d{1,2})\.(\d{1,2})\.(\d{4})\s(\d{1,2}):(\d{1,2})/', $dat, $t);

				$this -> tmstamp 		= mktime($t[4],$t[5],0,$t[2],$t[1],$t[3]);
				$this -> basedat 		= date("Y-m-d", $this -> tmstamp);
				$this -> basetm 		= date("Y-m-d H:i:00", $this -> tmstamp);
				$this -> stringdat	= date("d.m.Y", $this -> tmstamp);
				$this -> stringtm		= date("d.m.Y H:i", $this -> tmstamp);

		} else {		// формат 01.02.2009

				if((!trim($dat)) || ($dat == '00.00.0000'))
					return 0;

				preg_match('/(\d{1,2})\.(\d{1,2})\.(\d{4})/', $dat, $t);

				$this -> tmstamp 		= mktime(0,0,0,$t[2],$t[1],$t[3]);
				$this -> basedat 		= date("Y-m-d", $this -> tmstamp);
				$this -> basetm 		= date("Y-m-d 00:00:00", $this -> tmstamp);
				$this -> stringdat	= date("d.m.Y", $this -> tmstamp);
				$this -> stringtm		= date("d.m.Y H:i", $this -> tmstamp);

		}

		return $this -> tmstamp;
	}
}




// удаление участка начиная с $a до $b
function repl($a,$b) {
  global $output;
  $output = preg_replace('/(#'.$a.'#)[\S\s]+(#'.$b.'#)/', '\1\2', $output);
}





	$fl_nm_dog = @$_GET['fl'];		// номер договора 1 - авто, 0 - из базы


	$query  = "SELECT a.*,b.surname,b.name FROM applications as a LEFT JOIN users as b ON(a.user_id=b.uid) WHERE a.uid=".$_GET['id'];
	$res = mysql_query($query);

	$r = mysql_fetch_array($res);

//$r['title']=iconv("cp1251", "utf-8", $r['title']);
//$a = array("первый", "второй", "третий");
function encod($arr){
  return iconv('cp1251', 'utf-8', $arr);
}
$r = (array_map("encod",$r));

//$r = explode( "^", iconv( 'UTF-8', 'Windows-1251', implode( "^", $r ) ) );


    $date = date( "F d, Y" );

    // open our template file
    $filename = "zayavkanaproizv.html";
    $fp = fopen ( $filename, "rb" );

    //read our template into a variable
    $output = fread( $fp, filesize( $filename ) );


    fclose ( $fp );
   $output = str_replace( "<<uid>>", $r['uid'], $output );
    // replace the place holders in the template with our data
   $output = str_replace( "<<num_dog>>", $r['num_ord'], $output );

   $num_ord = $r['num_ord'];

   $dat_ord = new dat_fn($r['dat_ord']);
   $dat_ord = $dat_ord -> stringdat;
   $output = str_replace( "<<dat_ord>>", $dat_ord, $output );

   $output = str_replace( "<<user>>", $r['name'].' '.$r['surname'], $output );    // менеджер
   $output = str_replace( "<<title>>", $r['title'], $output );                    // название заказа
   $output = str_replace( "<<tiraz>>", $r['tiraz'], $output );                    // Общий тираж

   if($r['limit_per']=='0') {
      $output = str_replace( "<<limit_per>>", 'изменение тиража не допускается', $output );            // Пределы перекат/недокат
   } else {
      $limit_per = (($r['limit_per_sign']==0)?'+':'-').$r['limit_per'];
      $output = str_replace( "<<limit_per>>", $limit_per, $output );

   }

   $output = str_replace( "<<paper_density>>", $r['paper_density'], $output );    // Бумага (плотность)
   $output = str_replace( "<<paper_name>>", $r['paper_name'], $output );          // Название бумаги
   $dat_dlv = new dat_fn($r['paper_dat_deliv'],1);
   $dat_dlv = trim($dat_dlv -> stringdat);

   if($dat_dlv) {
      $output = str_replace( "<<dat_dlv>>", $dat_dlv, $output );    // Планируемая дата поставки листов на производство
   } else {
      $output = preg_replace('/#51#([\S\s]+)#52#/', '', $output);
   }

   if(trim($r['paper_press'])) {
     $output = str_replace( "<<paper_press>>", $r['paper_press'], $output );    // Типография
   } else {
     $output = preg_replace('/#47#([\S\s]+)#48#/', '', $output);
   }

   if(trim($r['paper_suppl'])) {
     $output = str_replace( "<<paper_suppl>>", $r['paper_suppl'], $output );    // Типография
   } else {
     $output = preg_replace('/#49#([\S\s]+)#50#/', '', $output);
   }

   //$output = str_replace( "<<paper_suppl>>", $r['paper_suppl'], $output );    // Поставщик бумаги
   $output = str_replace( "<<paper_width>>", $r['paper_width'], $output );    // Ширина
   $output = str_replace( "<<paper_height>>", $r['paper_height'], $output );  // Высота
   $output = str_replace( "<<paper_side>>", $r['paper_side'], $output );      // Бок
   $output = str_replace( "<<paper_color_ext>>", $r['paper_color_ext'], $output );      // Цвет пакета снаружи
   $output = str_replace( "<<paper_color_inn>>", $r['paper_color_inn'], $output );      // Цвет пакета внутри

   // Ламинация
   $lami = '';
   if($r['lamination_tp']==0)
     $lami = 'Без ламинации';
   if($r['lamination_tp']==1)
     $lami = 'Матовая';
   if($r['lamination_tp']==2)
     $lami = 'Глянцевая';

   if($r['lamination_tp'] > 0) {
     if($r['lamination_ext']==1)
       $lami.=' / Снаружи';
     if($r['lamination_inn']==1)
       $lami.=' / Внутри';
   }
   $output = str_replace( "<<lami>>", $lami, $output );      // Ламинация

   // Тиснение
   $tisn = '';
   if($r['stamp']==0) {
     $output = preg_replace('/#4#([\S\s]+)#4#/', '', $output);
     $output = preg_replace('/#5#([\S\s]+)#5#/', '', $output);
     $tisn = 'Без тиснения';
   }
   if($r['stamp']==1)
     $tisn = 'С одной стороны';
   if($r['stamp']==2) {
     $tisn_rz = (($r['stamp_typ']==0)?'Одинаковое':'Разное');
     $output = str_replace("<<tysn_razn>>", $tisn_rz, $output);
     $tisn = 'С двух сторон';
   }
   $output = str_replace( "<<tysn_typ>>", $tisn, $output);

   if($r['stamp'] > 0) {
     $output = str_replace( "<<tisn_wd>>", $r['stamp_width'], $output);
     $output = str_replace( "<<tysn_hg>>", $r['stamp_height'], $output);
     $output = str_replace( "<<tysn_col>>", $r['stamp_color'], $output);
     $output = str_replace( "<<tysn_foil>>", $r['stamp_foil_name'], $output);
     $output = str_replace( "<<st_idnb>>", $r['stamp_indent_bott'], $output);
     $output = str_replace( "<<st_idnr>>", $r['stamp_indent_right'], $output);
   }



   $output = str_replace( "<<paper_num_list>>", ($r['paper_num_list']==1)?'Из одного':'Из двух', $output );

   if(!trim($r['paper_list_typ'])) {
     $output = preg_replace('/#2#([\S\s]+)#3#/', '', $output);   // листы на пакете одинаковые
   } else {
     $output = preg_replace('/#1#([\S\s]+)#2#/', '', $output);   // разные
     $output = str_replace( "<<paper_list_typ>>", $r['paper_list_typ'], $output );
   }

   // Материал ручек
   $hand_mat = '';
   if($r['hand_mater_tp'] !=4 ) {
     repl(911,912);
   } else {
     repl(912,6);
   }
   if($r['hand_mater_tp']==0) {
     $output = str_replace( "<<hand_mat_oth>>", $r['hand_mater_txt'], $output );
   } else {
     if($r['hand_mater_tp']==1)
       $hand_mat = 'п/п шнур';
     if($r['hand_mater_tp']==2)
       $hand_mat = 'бум. шпагат';
     if($r['hand_mater_tp']==3)
       $hand_mat = 'лента';
     $output = str_replace( "<<hand_mat_oth>>", $hand_mat, $output );
   }

   // Крепление ручек
   if($r['hand_mount_tp']==0) {     // другой
      repl(8,9);
      if(trim($r['hand_mount_txt']))
        $output = str_replace("<<hand_mount_oth>>", $r['hand_mount_txt'], $output);
      else
        repl(6,8);
   } elseif($r['hand_mount_tp']==2) {  // клипсы
      repl(7,8);
      if(trim($r['hand_mount_color']))
        $output = str_replace("<<hand_mn_col>>", $r['hand_mount_color'], $output);
      else
        repl(913,9);
   } else {
      repl(8,9);
      $hand_mount_tp = '';
      if($r['hand_mount_tp']==1)
        $hand_mount_tp = 'Узелок';
      if($r['hand_mount_tp']==3)
        $hand_mount_tp = 'Клей';
      if($r['hand_mount_tp']==4)
        $hand_mount_tp = 'Прорубные';
      if($r['hand_mount_tp']==5)
        $hand_mount_tp = 'Без ручек';
      if($r['hand_mount_tp']==6)
        $hand_mount_tp = 'Бумажные';
      $output = str_replace("<<hand_mount_oth>>", $hand_mount_tp, $output);
   }

   // Толщина ручек
   if($r['hand_thick'] != '' ) {
     $output = str_replace("<<hand_thick>>", $r['hand_thick'], $output);
   } else {
     $output = preg_replace('/#53#([\S\s]+)#54#/', '', $output);
     $output = preg_replace('/#55#([\S\s]+)#56#/', '', $output);
   }

   // Цвет ручек
   if(trim($r['hand_color'])) {
      $output = str_replace("<<hand_color>>", $r['hand_color'], $output);
   } else {
      repl(57,571); repl(572,573);
   }
   // Видимая длина ручек (без учета узелков)
   if($r['hand_length']!='') {
     $output = str_replace("<<hand_length>>", $r['hand_length'], $output);
   } else {
     repl(574,575);
   }

   if(($r['hand_mater_scotch'] == 0)&&($r['hand_mater_glue'] == 0)) {
     $output = preg_replace('/#10#([\S\s]+)#13#/', '', $output);
   } else {
     if($r['hand_mater_scotch'] == 1) {
       $output = str_replace("<<hand_sctch>>", $r['hand_mater_scotch_tx'], $output);
     } else {
       $output = preg_replace('/#11#([\S\s]+)#12#/', '', $output);
     }

     if($r['hand_mater_glue'] == 1) {
       $output = str_replace("<<hand_glue>>", $r['hand_mater_glue_tx'], $output);
     } else {
       $output = preg_replace('/#12#([\S\s]+)#13#/', '', $output);
     }
   }

   // Пикало
   if($r['pikalo_on']==0) {
      repl(15,16);
      //$output = preg_replace('/#15#([\S\s]+)#16#/', '', $output);
      if( ($r['pikalo_diam_hol'] != '') && ($r['hand_mater_tp'] != 2) ) {
        $output = str_replace("<<pikalo_diam>>", $r['pikalo_diam_hol'], $output);
      } else {
        repl(16,17);
      }
   } else {
      repl(14,15); repl(16,17);
      if(trim($r['pikalo_color']))
        $output = str_replace("<<pikalo_col>>", $r['pikalo_color'], $output);
      else
        repl(15,151);
      if($r['pikalo_diam_hol']!='')
        $output = str_replace("<<pikalo_diam>>", $r['pikalo_diam_hol'], $output);
      else
        repl(151,16);
   }

   // Укрепление пакета
   if( ($r['strengt_bot']==0) && ($r['strengt_side']==0) && (!trim($r['strengt_oth_tx']))) {
     $output = preg_replace('/(#181#)[\S\s]+(#21#)/', '\1\2', $output); // без укр
   } else {
     $output = preg_replace('/(#18#)[\S\s]+(#181#)/', '\1\2', $output);
     if($r['strengt_bot']==0) {
       $output = preg_replace('/(#181#)[\S\s]+(#183#)/', '\1\2', $output);
     } else {
       if(trim($r['strengt_bot_col']))
         $output = str_replace("<<strengt_col>>", $r['strengt_bot_col'], $output);
       else
         $output = preg_replace('/(#182#)[\S\s]+(#183#)/', '\1\2', $output);
     }
     if($r['strengt_side']==0)
         $output = preg_replace('/(#183#)[\S\s]+(#20#)/', '\1\2', $output);

     if(trim($r['strengt_oth_tx'])) {
        $output = str_replace("<<strengt_oth>>", $r['strengt_oth_tx'], $output);
     } else {
        $output = preg_replace('/(#20#)[\S\s]+(#21#)/', '\1\2', $output);
     }
   }


/*
   if($r['strengt_tp'] == 0) {
      $output = preg_replace('/#18#([\S\s]+)#20#/', '', $output);
      $output = str_replace("<<strengt_oth>>", $r['strengt_oth'], $output);
   } elseif($r['strengt_tp'] == 2) {
      $output = preg_replace('/#18#([\S\s]+)#19#/', '', $output);
      $output = preg_replace('/#20#([\S\s]+)#21#/', '', $output);
      $output = str_replace("<<strengt_oth>>", $r['strengt_oth'], $output);
   } else {
      $output = preg_replace('/#19#([\S\s]+)#21#/', '', $output);
      $output = str_replace("<<strengt_oth>>", (($r['strengt_tp'] == 1)?'Без':'Бок'), $output);
   }
*/
   // Упаковка
   if($r['packing_korob']==1) {   // не важно
     $output = preg_replace('/#23#([\S\s]+)#25#/', '', $output);
   } else {
     $output = preg_replace('/(#22#)[\S\s]+(#23#)/', '\1\2', $output);
     if($r['packing_sel']==1) {
        $output = preg_replace('/(#231#)[\S\s]+(#25#)/', '\1\2', $output);
        $output = str_replace("<<pack_kor_nm>>", $r['packing_other'], $output);
     } elseif($r['packing_sel']==2) {
        $output = preg_replace('/(#23#)[\S\s]+(#231#)/', '\1\2', $output);
        $output = preg_replace('/(#24#)[\S\s]+(#25#)/', '\1\2', $output);
        $output = str_replace("<<pack_pl_nm>>", $r['packing_other'], $output);
     } else {
        $output = preg_replace('/(#23#)[\S\s]+(#24#)/', '\1\2', $output);
        $output = str_replace("<<packing_other>>", $r['packing_other'], $output);
     }
   }

   if($r['mark_of_company_tp']==0) {
     $output = preg_replace('/#26#([\S\s]+)#27#/', '', $output);
     $output = str_replace("<<mark_of_company>>", $r['mark_of_company'], $output);
   } else {
     $output = preg_replace('/#27#([\S\s]+)#28#/', '', $output);
     $mark_of_company = '';

     if($r['mark_of_company_tp']==1)
       $mark_of_company = 'Принтфолио';
     if($r['mark_of_company_tp']==2)
       $mark_of_company = 'Пакетофф';
     if($r['mark_of_company_tp']==3)
       $mark_of_company = 'Без';

     $output = str_replace("<<mark_of_company>>", $mark_of_company, $output);
   }

   // Сборка раз решается только
   if( ($r['assperm_1']==0) && ($r['assperm_2']==0) && ($r['assperm_3']==0) && ($r['assperm_4']==0)) {
     $output = preg_replace('/#29#([\S\s]+)#34#/', '', $output);
   } else {
      if($r['assperm_1']==0)
        $output = preg_replace('/(#30#)[\S\s]+(#31#)/', '\1\2', $output);
      if($r['assperm_2']==0)
        $output = preg_replace('/(#31#)[\S\s]+(#32#)/', '\1\2', $output);
      if($r['assperm_3']==0)
        $output = preg_replace('/(#32#)[\S\s]+(#33#)/', '\1\2', $output);
      if($r['assperm_4']==0)
        $output = preg_replace('/(#33#)[\S\s]+(#34#)/', '\1\2', $output);
   }

   if(trim($r['delivery_address'])) {
     $output = str_replace("<<delivery_address>>", $r['delivery_address'], $output);
   } else {
     $output = preg_replace('/(#35#)[\S\s]+(#36#)/', '\1\2', $output);
   }

   if(trim($r['contact_man'])) {
     $output = str_replace("<<contact_man>>", $r['contact_man'], $output);
   } else {
     $output = preg_replace('/(#36#)[\S\s]+(#37#)/', '\1\2', $output);
   }

   // Доставка
   if($r['delivery_tp']==0)
     $output = str_replace("<<delivery_tp>>", 'Наш транспорт', $output);
   elseif($r['delivery_tp']==1)
     $output = str_replace("<<delivery_tp>>", 'Наемный транспорт', $output);
   elseif($r['delivery_tp']==2)
     $output = str_replace("<<delivery_tp>>", 'Самовывоз заказчика', $output);
   elseif($r['delivery_tp']==3)
     $output = str_replace("<<delivery_tp>>", 'На склад', $output);
     
   // Особые требования
   if(trim($r['special_requir'])) {
     $output = str_replace("<<special_requir>>", $r['special_requir'], $output);
   } else {
     $output = preg_replace('/(#38#)[\S\s]+(#39#)/', '\1\2', $output);
   }

   // Особые требования
   if(trim($r['rate'])) {
     $output = str_replace("<<rate>>", $r['rate'], $output);
   } else {
     $output = preg_replace('/(#40#)[\S\s]+(#41#)/', '\1\2', $output);
   }

     // Артикул
   if(trim($r['art_num'])) {
     $output = str_replace("<<art_num>>", $r['art_num'], $output);
   } else {
     $output = preg_replace('/(#40#)[\S\s]+(#41#)/', '\1\2', $output);
   }

   $query = "SELECT * FROM applications_shipping_list WHERE apl_id=".$_GET['id']." ORDER BY num";
   $res = mysql_query($query);
   $nm = 1;
   while($r = mysql_fetch_array($res)) {
      $output = str_replace("<<shipp_nm_".$nm.">>", $r['val_nums'], $output);
      $output = str_replace("<<shipp_tx_".$nm.">>", $r['val_tx'], $output);
      $nm++;
   }
   if($nm == 1)
     $output = preg_replace('/(#42#)[\S\s]+(#46#)/', '\1\2', $output);
   elseif($nm < 4)
     $output = preg_replace('/(#'.(42+$nm).'#)[\S\s]+(#46#)/', '\1\2', $output);


   $output = preg_replace('/#\d{1,3}#/', '', $output);
   $output = preg_replace('/<<[\S\s]+>>/U', '', $output);


		//generate the headers to help a browser choose the correct application
    //header( "Content-type: application/rtf" );
	header("Content-Type: text/html; charset=utf-8");
    header( 'Content-Disposition: inline; filename="Заявка_'.$num_ord.'.html"');

    // send the generated document to the browser
    echo $output;

?>         