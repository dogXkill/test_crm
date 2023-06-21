<?
// Сохранение всего запроса на счет в базе
require_once($_SERVER['DOCUMENT_ROOT']."/acc/includes/lib/JsHttpRequest/JsHttpRequest.php");
include_once($_SERVER['DOCUMENT_ROOT'].'/acc/includes/db.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/acc/includes/auth.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/acc/includes/lib.php');


// если 1 -админ или бухгалтер, иначе 0 - менеджер
$tpacc = (($user_type == 'sup') || ($user_type == 'acc') || ($user_type == 'meg')) ? 1 : 0;


function pr($t) {
  echo '<pre>';
  print_r($t);
  echo '</pre>';
}


// Класс для работы с форматами дат
class dat_fn
{
	var $basedat 		= '';				// формат базы DATE
	var $basetm 		= '';				// формат базы DATETIME
	var $tmstamp 		= 0;				// tamestamp
	var $stringdat	= '';				// формат 12.05.2009
	var $stringtm		= '';				// формат 12.05.2009 12:49


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


// Блок динамической передачи данных в java script
//----------------------------------------------------------------------
$JsHttpRequest = new JsHttpRequest("windows-1251");


$op     = $_REQUEST['op'];    // текущая операция
$arr    = $_REQUEST['arr'];

$dat_ord = new dat_fn($arr['data']['apl_dat'], 2);
$dat_ord = $dat_ord -> basetm;

$paper_dat_deliv = new dat_fn($arr['data']['pap_przv_dat'], 3);
$paper_dat_deliv = $paper_dat_deliv -> basedat;

// Сохранить поля объекта
if($op == 'apl_save_all') {

if ($arr['data']['type'] == "1"){$type = $arr['data']['ClientName'];}
if ($arr['data']['type'] == "2"){$type = "серийник";}

$get_types = mysql_query("SELECT * FROM types");
while($gg =  mysql_fetch_array($get_types)){
$gg[]
}

if ($arr['data']['izd_type'] == "4"){$izd_type = "пакет";}
if ($arr['data']['izd_type'] == "5"){$izd_type = "коробка";}
if ($arr['data']['izd_type'] == "16"){$izd_type = "конверты";}
if ($arr['data']['izd_type'] == "18"){$izd_type = "наклейки-замочки";}
if ($arr['data']['izd_type'] == "10"){$izd_type = "прочее";}

if ($arr['data']['lami_sel'] == "0"){$izd_lami = "без ламинации";}
if ($arr['data']['lami_sel'] == "1"){$izd_lami = "матовая";}
if ($arr['data']['lami_sel'] == "2"){$izd_lami = "глянцевая";}
if($arr['data']['paper_side']>0){$x = "x";}else{$x="";}
$title = $type." ".$izd_type." ".$arr['data']['paper_wd']."x".$arr['data']['paper_hg'].$x.$arr['data']['paper_side'].", ".$arr['data']['paper_col_ext']." ".$arr['data']['paper_name']." лам.".$izd_lami;
//echo $title;
  if($arr['id'] == 0) {
    $dat_ord = new dat_fn(date("d.m.Y H:i"),2);
    $dat_ord = $dat_ord -> basetm;

    // номер заказа максимальный из тех что есть
    $res = mysql_query("SELECT MAX(num_ord) FROM applications WHERE 1=1");
    if($r = mysql_fetch_array($res)) {
      $nm_ord = $r['MAX(num_ord)']+1;
    } else
      $nm_ord = 1;

    $query = sprintf(
      "INSERT INTO applications(
	    art_num,
		art_uid,
        title,
        user_id,
        num_ord,
        tiraz,
        dat_ord,
        limit_per_sign,
        limit_per,
        paper_width,
        paper_height,
        paper_side,
        paper_color_ext,
        paper_color_inn,
        paper_density,
        paper_name,
        paper_dat_deliv,
        paper_press,
        paper_suppl,
        paper_num_list,
        paper_list_typ,
        lamination_tp,
        lamination_inn,
        lamination_ext,
        stamp,
        stamp_width,
        stamp_height,
        stamp_color,
        stamp_typ,
        stamp_foil_name,
        stamp_indent_bott,
        stamp_indent_right,
        hand_mater_tp,
        hand_mater_txt,
        hand_mount_tp,
        hand_mount_color,
        hand_mount_txt,
        hand_thick,
        hand_color,
        hand_length,
        hand_mater_scotch,
        hand_mater_scotch_tx,
        hand_mater_glue,
        hand_mater_glue_tx,
        pikalo_on,
        pikalo_diam_hol,
        pikalo_color,
        strengt_bot,
        strengt_bot_col,
        strengt_side,
        strengt_oth_tx,
        packing_korob,
        packing_sel,
        packing_other,
        mark_of_company_tp,
        mark_of_company,
        assperm_1,
        assperm_2,
        assperm_3,
        assperm_4,
        delivery_tp,
        delivery_address,
        contact_man,
        special_requir,
		yellow_tape,
        rate,
		rate_lamin,
rate_tigel_pril,
rate_tigel_udar,
rate_tisn_pril,
rate_tisn_udar,
rate_vstavka_dna_bok,
rate_podgotovka_truby,
rate_line_truba_pril,
rate_line_truba_prokat,
rate_line_dno_pril,
rate_line_dno_prokat,
rate_upak,
rate_drugoe,
		type,
		izd_type,
		ClientName,
		zakaz_id,
 		exec_on
      ) VALUES (
	     %d,
		 %d,
        '%s',
        %d,
        %d,
        %d,
        '%s',
        %d,
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        %d,
        '%s',
        %d,
        %d,
        %d,
        %d,
        '%s',
        '%s',
        '%s',
        %d,
        '%s',
        '%s',
        '%s',
        %d,
        '%s',
        %d,
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        %d,
        '%s',
        %d,
        '%s',
        %d,
        '%s',
        '%s',
        %d,
        '%s',
        %d,
        '%s',
        %d,
        '%d',
        '%s',
        %d,
        '%s',
        %d,
        %d,
        %d,
        %d,
        %d,
        '%s',
        '%s',
        '%s',
		'%d',
        '%s',
		'%s',
		'%s',
		'%s',
		'%s',
		'%s',
		'%s',
		'%s',
		'%s',
		'%s',
		'%s',
		'%s',
		'%s',
		'%s',
		'%d',
		'%d',
		'%s',
		'%d',
        0
      )",
	    $arr['data']['art_num'], //артикул на сайте
        $arr['data']['art_uid'], //номер id на сайте
        $title,
        $arr['data']['manager'],        // ид менеджера
        $nm_ord,
        $arr['data']['apl_tiraz'],
        $dat_ord,                       // дата заявки
        $arr['data']['limit_per_sign'],
        $arr['data']['apl_limit_per'],
        $arr['data']['paper_wd'],
        $arr['data']['paper_hg'],
        $arr['data']['paper_side'],
        $arr['data']['paper_col_ext'],
        $arr['data']['paper_col_inn'],
        $arr['data']['paper_density'],
        $arr['data']['paper_name'],
        $paper_dat_deliv,               // Планируемая дата поставки листов на производство
        $arr['data']['paper_press'],
        $arr['data']['paper_suppl'],
        $arr['data']['paper_num_list'],
        (($arr['data']['rad_paper_list_typ']==0)?'':$arr['data']['paper_list_typ_tx']),
        $arr['data']['lami_sel'],
        $arr['data']['ch_lami_tp2'],
        $arr['data']['ch_lami_tp1'],
        $arr['data']['tisn_sel1'],
        $arr['data']['stamp_width'],
        $arr['data']['stamp_height'],
        $arr['data']['stamp_color'],
        $arr['data']['ch_tisn_tp'],
        $arr['data']['stamp_foil_name'],
        $arr['data']['stamp_indent_bott'],
        $arr['data']['stamp_indent_right'],
        $arr['data']['hand_sel1'],
        $arr['data']['hand_mat_oth'],
        $arr['data']['hand_mount_sel'],
        $arr['data']['hand_mount_color'],
        $arr['data']['hand_mount_oth'],
        $arr['data']['hand_thick'],
        $arr['data']['hand_color'],
        $arr['data']['hand_length'],
        $arr['data']['hand_mater_sk'],
        $arr['data']['hand_mater_sk_tx'],
        $arr['data']['hand_mater_kl'],
        $arr['data']['hand_mater_kl_tx'],
        $arr['data']['ch_pikalo'],
        (($arr['data']['ch_pikalo']==0)?$arr['data']['pikalo_no_diam']:$arr['data']['pikalo_on_diam']),
        $arr['data']['pikalo_on_color'],
        $arr['data']['strengt_bot'],
        $arr['data']['strengt_bot_col'],
        $arr['data']['strengt_side'],
        $arr['data']['strengt_oth_tx'],
        $arr['data']['ch_packing'],
        (($arr['data']['ch_packing']==1)?0:$arr['data']['packing_sel']),
        (($arr['data']['ch_packing']==1)?'':$arr['data']['packing_oth']),
        $arr['data']['packing_nameof_sel'],
        $arr['data']['packing_nameof_oth'],
        $arr['data']['ch_assperm1'],
        $arr['data']['ch_assperm2'],
        $arr['data']['ch_assperm3'],
        $arr['data']['ch_assperm4'],
        $arr['data']['delev_typ_sel'],
        $arr['data']['deliv_addr'],
        $arr['data']['contact_man'],
        $arr['data']['spec_req'],
		$arr['data']['yellow_tape'],
		$arr['data']['rate_in'],
		$arr['data']['rate_lamin'],
		$arr['data']['rate_tigel_pril'],
		$arr['data']['rate_tigel_udar'],
		$arr['data']['rate_tisn_pril'],
		$arr['data']['rate_tisn_udar'],
		$arr['data']['rate_vstavka_dna_bok'],
		$arr['data']['rate_podgotovka_truby'],
		$arr['data']['rate_line_truba_pril'],
		$arr['data']['rate_line_truba_prokat'],
		$arr['data']['rate_line_dno_pril'],
		$arr['data']['rate_line_dno_prokat'],
		$arr['data']['rate_upak'],
		$arr['data']['rate_drugoe'],
		$arr['data']['type'],
		$arr['data']['izd_type'],
		$arr['data']['ClientName'],
		$arr['data']['zakaz_id']
      );

  } else {
     $query = sprintf(
       "UPDATE applications SET
	      art_num           = '%d',
	      art_uid           = '%d',
          title             = '%s',
          user_id           = %d,
          num_ord           = %d,
          tiraz             = %d,
          dat_ord           ='%s',
          limit_per_sign    = %d,
          limit_per         ='%s',
          paper_width       ='%s',
          paper_height      ='%s',
          paper_side        ='%s',
          paper_color_ext   ='%s',
          paper_color_inn   ='%s',
          paper_density     ='%s',
          paper_name        ='%s',
          paper_dat_deliv   ='%s',
          paper_press       ='%s',
          paper_suppl       ='%s',
          paper_num_list    = %d,
          paper_list_typ    ='%s',
          lamination_tp     =%d,
          lamination_inn    =%d,
          lamination_ext    =%d,
          stamp             =%d,
          stamp_width       ='%s',
          stamp_height      ='%s',
          stamp_color       ='%s',
          stamp_typ         =%d,
          stamp_foil_name   ='%s',
          stamp_indent_bott ='%s',
          stamp_indent_right  ='%s',
          hand_mater_tp     =%d,
          hand_mater_txt    ='%s',
          hand_mount_tp     =%d,
          hand_mount_color  ='%s',
          hand_mount_txt    ='%s',
          hand_thick        ='%s',
          hand_color        ='%s',
          hand_length       ='%s',
          hand_mater_scotch =%d,
          hand_mater_scotch_tx='%s',
          hand_mater_glue   =%d,
          hand_mater_glue_tx='%s',
          pikalo_on         =%d,
          pikalo_diam_hol   ='%s',
          pikalo_color      ='%s',
          strengt_bot       =%d,
          strengt_bot_col   ='%s',
          strengt_side      =%d,
          strengt_oth_tx    ='%s',
          packing_korob     =%d,
          packing_sel       ='%d',
          packing_other     ='%s',
          mark_of_company_tp=%d,
          mark_of_company   ='%s',
          assperm_1         =%d,
          assperm_2         =%d,
          assperm_3         =%d,
          assperm_4         =%d,
          delivery_tp       =%d,
          delivery_address  ='%s',
          contact_man       ='%s',
          special_requir    ='%s',
		  yellow_tape       ='%d',
          rate              ='%s',
          rate_lamin              ='%s',
		  rate_tigel_pril              ='%s',
		  rate_tigel_udar              ='%s',
		  rate_tisn_pril              ='%s',
		  rate_tisn_udar              ='%s',
		  rate_vstavka_dna_bok              ='%s',
		  rate_podgotovka_truby				='%s',
		  rate_line_truba_pril              ='%s',
		  rate_line_truba_prokat            ='%s',
		  rate_line_dno_pril                ='%s',
		  rate_line_dno_prokat              ='%s',
		  rate_upak              ='%s',
		  rate_drugoe              ='%s',
		  type              ='%d',
		  izd_type			='%d',
		  ClientName        ='%s',
		  zakaz_id          ='%d'
        WHERE uid=%d",
		  $arr['data']['art_num'],
		  $arr['data']['art_uid'],
          $title,
          $arr['data']['manager'],        // ид менеджера
          $arr['data']['apl_num_ord'],
          $arr['data']['apl_tiraz'],
          $dat_ord,                       // дата заявки
          $arr['data']['limit_per_sign'],
          $arr['data']['apl_limit_per'],
          $arr['data']['paper_wd'],
          $arr['data']['paper_hg'],
          $arr['data']['paper_side'],
          $arr['data']['paper_col_ext'],
          $arr['data']['paper_col_inn'],
          $arr['data']['paper_density'],
          $arr['data']['paper_name'],
          $paper_dat_deliv,               // Планируемая дата поставки листов на производство
          $arr['data']['paper_press'],
          $arr['data']['paper_suppl'],
          $arr['data']['paper_num_list'],
          (($arr['data']['rad_paper_list_typ']==0)?'':$arr['data']['paper_list_typ_tx']),
          $arr['data']['lami_sel'],
          $arr['data']['ch_lami_tp2'],
          $arr['data']['ch_lami_tp1'],
          $arr['data']['tisn_sel1'],
          $arr['data']['stamp_width'],
          $arr['data']['stamp_height'],
          $arr['data']['stamp_color'],
          $arr['data']['ch_tisn_tp'],
          $arr['data']['stamp_foil_name'],
          $arr['data']['stamp_indent_bott'],
          $arr['data']['stamp_indent_right'],
          $arr['data']['hand_sel1'],
          $arr['data']['hand_mat_oth'],
          $arr['data']['hand_mount_sel'],
          $arr['data']['hand_mount_color'],
          $arr['data']['hand_mount_oth'],
          $arr['data']['hand_thick'],
          $arr['data']['hand_color'],
          $arr['data']['hand_length'],
          $arr['data']['hand_mater_sk'],
          $arr['data']['hand_mater_sk_tx'],
          $arr['data']['hand_mater_kl'],
          $arr['data']['hand_mater_kl_tx'],
          $arr['data']['ch_pikalo'],
          (($arr['data']['ch_pikalo']==0)?$arr['data']['pikalo_no_diam']:$arr['data']['pikalo_on_diam']),
          $arr['data']['pikalo_on_color'],
          $arr['data']['strengt_bot'],
          $arr['data']['strengt_bot_col'],
          $arr['data']['strengt_side'],
          $arr['data']['strengt_oth_tx'],
          $arr['data']['ch_packing'],
          (($arr['data']['ch_packing']==1)?0:$arr['data']['packing_sel']),
          (($arr['data']['ch_packing']==1)?0:$arr['data']['packing_oth']),
          $arr['data']['packing_nameof_sel'],
          $arr['data']['packing_nameof_oth'],
          $arr['data']['ch_assperm1'],
          $arr['data']['ch_assperm2'],
          $arr['data']['ch_assperm3'],
          $arr['data']['ch_assperm4'],
          $arr['data']['delev_typ_sel'],
          $arr['data']['deliv_addr'],
          $arr['data']['contact_man'],
          $arr['data']['spec_req'],
		  $arr['data']['yellow_tape'],
          $arr['data']['rate_in'],
		  $arr['data']['rate_lamin'],
		  $arr['data']['rate_tigel_pril'],
		  $arr['data']['rate_tigel_udar'],
		  $arr['data']['rate_tisn_pril'],
		  $arr['data']['rate_tisn_udar'],
		  $arr['data']['rate_vstavka_dna_bok'],
		  $arr['data']['rate_podgotovka_truby'],
		  $arr['data']['rate_line_truba_pril'],
		  $arr['data']['rate_line_truba_prokat'],
		  $arr['data']['rate_line_dno_pril'],
		  $arr['data']['rate_line_dno_prokat'],
		  $arr['data']['rate_upak'],
		  $arr['data']['rate_drugoe'],
		  $arr['data']['type'],
		  $arr['data']['izd_type'],
		  $arr['data']['ClientName'],
		  $arr['data']['zakaz_id'],
		  $arr['id']

     );


  }
  //echo $query;
mysql_query($query);
print(mysql_error());

//внести номер заказа

  if($arr['id'] == 0) {

    $new_id = mysql_insert_id();

  } else {  // если редактирование - удалить список отгрузок
    $new_id = $arr['id'];
    $query = "DELETE FROM applications_shipping_list WHERE apl_id=$new_id";
    mysql_query($query);
  }
  //echo $new_id.'<br />';

  // Порядок отгрузки заказа список значений
  $nm = 0;
  for($i=1; $i<4; $i++) {

    if(trim($arr['data']['chipping_nm'.$i]) || trim($arr['data']['chipping_tx'.$i])) {
      $query = sprintf("INSERT INTO applications_shipping_list(apl_id,num,val_nums,val_tx) VALUES(%d,%d,%d,'%s')",
        $new_id,$nm, $arr['data']['chipping_nm'.$i], $arr['data']['chipping_tx'.$i]);
      mysql_query($query);
      $nm++;
    }
  }
  //echo mysql_error().'<br />';

  $GLOBALS['_RESULT'] = array('res' => $new_id);
}


//ID заказа
$zakaz_id = $arr['data']['zakaz_id'];
//$id = $arr['id'];
$apl_num_ord = $arr['data']['apl_num_ord'];
 //$query = sprintf("UPDATE queries SET zayavka = '111' WHERE uid=$acc_no");
 $query = sprintf("UPDATE queries SET zayavka = $new_id, num_ord = $apl_num_ord WHERE uid=$zakaz_id");

 mysql_query($query);