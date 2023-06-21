<?php
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");
$stamps = array();

//$sort_order_like=0;
function check_prefix($mas,$zn){
	foreach ($mas as $key =>$value){
		//print_r($value);
		if ($value['tid']==$zn){return $value['prefix'];}
	}
	return false;
}
function check_prefix_zn($mas,$zn){
	foreach ($mas as $key =>$value){
		//print_r($value);
		if ($value['prefix']==$zn){return $value['tid'];}
	}
	return false;
}
 $types = array();
$q = "SELECT * FROM `types` WHERE vis_stamps = 1 ORDER BY seq DESC";
$r = mysql_query($q);
while ($row = mysql_fetch_row($r))
{
    $type = array();
    $type['tid'] = $row[0];
    $type['type'] = $row[1];
	$type['prefix']=$row[5];

    array_push($types, $type);
    unset($type);
}
include("filters.php");
 //types

$q = "SELECT * FROM stamps" . $sort_where . $sort_order;
//echo $q;
//echo $sort_order_like;
$r = mysql_query($q);
if ((mysql_num_rows($r)==0) && ($sort_order_like!='')) { 
$q = "SELECT * FROM stamps" . $sort_where . $sort_order_like;
//echo $q;
$r = mysql_query($q);
}
//echo $q."|".mysql_num_rows($r);

while ($row = mysql_fetch_row($r) )
{
  $stamp = array();

  $stamp['ID'] = $row[0];
  $stamp['NUMBER'] = $row[1];
  $stamp['NAME'] = $row[2];
  $stamp['SHIR'] = $row[3];
  $stamp['VIS'] = $row[4];
  $stamp['BOK'] = $row[5];
  $stamp['SIZE_X'] = $row[6];
  $stamp['SIZE_Y'] = $row[7];
  $stamp['ANOTHER_STAMP'] = $row[8];
  $stamp['TYPE'] = $row[9];
  $izd_q = "SELECT * FROM types WHERE vis_stamps = 1 AND tid = $row[9]";
  $izd_r = mysql_query($izd_q);
  $izd_arr = mysql_fetch_assoc($izd_r);

  $stamp['IZD_TYPE'] = $izd_arr['type'];
  
  $stamp['KARKAS'] = $row[11];
  $stamp['PHOTO']=$row[10];
  $stamp['CANVAS'] =$row[11];
  if ($row[12] == 1) {
    $stamp['SKLEIKA'] = '¬нутренн¤¤';
  } elseif ($row[12] == 2) {
    $stamp['SKLEIKA'] = '¬нешн¤¤';
  } else {
    $stamp['SKLEIKA'] = '';
  }

  $stamp['TEXT'] = $row[13];
  $stamp['CREATED_AT'] = empty($row[14]) ? '' : date('d.m.Y H:i:s', strtotime($row[14]));
 $stamp['CREATE_USER'] = $row[15];

$prefix="";
$types_id=$stamp['TYPE'];
if ($types_id!=4){
$prefix=check_prefix($types,$types_id);
}else{$prefix="";}
$stamp['PREFIX']=$prefix;
echo $prefix;
  $photo_path = __DIR__ . '/photo-stamps/' . $prefix.''.$stamp['NUMBER'];
  

  if (!empty($stamp['PHOTO'])) {
      //$stamp['PHOTO_ICON'] = "/acc/sprav/stamps/photo-stamps/" .$prefix.'/'. $stamp['NUMBER'] . "/" . $stamp['PHOTO'];
	  $stamp['PHOTO_ICON']="/acc/sprav". $stamp['PHOTO'];
  } else {
      $stamp['PHOTO_ICON'] = '/acc/i/who.gif';
  }
  
//canvas

if (!empty($stamp['CANVAS'])) {
      //$stamp['PHOTO_ICON'] = "/acc/sprav/stamps/photo-stamps/" .$prefix.'/'. $stamp['NUMBER'] . "/" . $stamp['PHOTO'];
	  $stamp['CANVAS']="/acc/sprav". $stamp['CANVAS'];
	  
  } else {
      $stamp['CANVAS'] =null;
	  
  }
   $extension = explode('.', $stamp['CANVAS']);
      $extension = $extension[1];
      switch ($extension) {
          case 'cdr':
              $stamp['EXTENSION_ICON'] = '/acc/i/file_icons/cdr_icon.png';
              break;
          case 'ai':
              $stamp['EXTENSION_ICON'] = '/acc/i/file_icons/ai_icon.png';
              break;
          case 'eps':
              $stamp['EXTENSION_ICON'] = '/acc/i/file_icons/eps_icon.png';
              break;
          case 'pdf':
              $stamp['EXTENSION_ICON'] = '/acc/i/file_icons/pdf_icon.png';
              break;
      }
	 if (empty($stamp['EXTENSION_ICON']) ) {
		$stamp['EXTENSION_ICON'] = '/acc/i/who.gif';
	 }
array_push($stamps, $stamp);
unset($stamp);

}

if (isset($_GET['with_photo']) ) {
  foreach ($stamps as $key => $value) {
    /*if (empty($value['PHOTO'])) {
      unset($stamps[$key]);
    }*/
	if (!empty($value['PHOTO']) && ($value['PHOTO']!= '/acc/i/who.gif')) {
      unset($stamps[$key]);
    }
  }
}

if (isset($_GET['with_canvas']) ) {
  foreach ($stamps as $key => $value) {
    if (!empty($value['CANVAS']) && ($value['CANVAS']!= '/acc/i/who.gif')) {
      unset($stamps[$key]);
    }
  }
}
//users 
$q="SELECT * FROM `users` ORDER BY `uid` ASC";
$r = mysql_query($q);
$mas_name = array();
while ($row = mysql_fetch_row($r))
{
  $mas_name[$row[0]]['name'] = $row[6]." ".$row[5];
}

if (count($stamps)>=1){
foreach ($stamps as $key => $value) {
		$name_stamp=$value['prefix']
        ?>
        <tr id="stamp_cont_<?=$value['ID']?>">
          <td class="tab_td_marg" align="center" data-sort="<?=$value['NUMBER']?>">
		  <input type='checkbox' class='ch_print' data-id-print="<?=$value['ID']?>">
		 <?php if (($user_access['proizv_access']==1) && ($user_access['proizv_access_type']==2) && ($user_access['proizv_access_edit']==2)){?>
			  <span style='color:gray;font-size:13px;'><?=$value['PREFIX']?></span><a href="/acc/sprav/stamps?edit=<?=$value['ID']?>" style="font-weight:bold;"><?=$value['NUMBER']?></a>
			  <?php
		  }else{
			  ?>
			  <span style="font-weight:bold;"><span style='color:gray;font-size:13px;'><?=$value['PREFIX']?></span><?=$value['NUMBER']?></span>
			  <?php
		  }?>
		  </td>
          <td class="tab_td_norm" align="center"><?=$value['SHIR']?></td>
          <td class="tab_td_norm" align="center"><?=$value['VIS']?></td>
          <td class="tab_td_norm" align="center"><?=$value['BOK']?></td>
          <td class="tab_td_norm" align="center"><?=$value['SIZE_X']?></td>
          <td class="tab_td_norm" align="center"><?=$value['SIZE_Y']?></td>
          <td class="tab_td_norm" align="center"><a href="/acc/sprav/stamps?edit=<?=$value['ANOTHER_STAMP']?>"><?=$value['ANOTHER_STAMP']?></a></td>
          <td class="tab_td_norm" align="center"><?=$value['IZD_TYPE']?></td>
          <td class="tab_td_norm" align="center">
            <?php if (!empty($value['PHOTO'])) {?>
                <a href="/acc/sprav<?=$value['PHOTO']?>" target="blank">
                <img style="width: 30px;" src="<?=$value['PHOTO_ICON']?>">
            </a>
            <?php } else { ?>
               <!-- <img style="width: 30px;" src="/acc/i/who.gif">-->
				<i class="fa-sharp fa-regular fa-image-slash" style="color: #19b834;font-size:21px;"></i>
            <?php } ?>
          </td>
          <td class="tab_td_norm" align="center">
               <?php if (!empty($value['CANVAS'])) { ?>
              <a href="/acc/sprav<?=$value['CANVAS']?>" target="blank">
                <img style="width: 30px;" src="<?=$value['EXTENSION_ICON']?>">
              </a>
              <?php } else { ?>
                  <!--<img style="width: 30px;" src="/acc/i/who.gif">-->
				  <i class="fa-sharp fa-regular fa-image-slash" style="color: #19b834;font-size:21px;"></i>
              <?php } ?>
          </td>
          <td class="tab_td_norm" align="center"><?=$value['SKLEIKA']?></td>
          <td class="tab_td_norm" align="center"><?=$value['TEXT']?></td>
		   <?php $name_create=$mas_name[$value['CREATE_USER']]['name'];?>
		   <td class="tab_td_norm" align="center"><?=$name_create?></td>
		  <?php $mydate = strtotime($value['CREATED_AT']); ?>
          <td class="tab_td_norm" align="center" data-sort="<?=$mydate?>"><?=$value['CREATED_AT']?></td>
          <td class="tab_td_norm" align="center">
		  <a href="#" class='app_link' id="show_app_<?=$value['NUMBER']?>_<?=$value['TYPE']?>" data-id="<?=$value['NUMBER']?>_<?=$value['TYPE']?>"><i class="fa-light fa-shutters" style="color: #000000;font-size:21px;vertical-align:text-bottom;"></i></a>
				
		  <?php if (($user_access['proizv_access']==1) && ($user_access['proizv_access_type']==2) && ($user_access['proizv_access_edit']==2)){?>
		  <img class="stamp_delete" id="stamp_delete_<?=$value['ID']?>" data-number="<?=$value['ID']?>" src="/acc/i/del.gif" onclick="deletestamp(this.id);">
			  <?php
			  if (isset($_GET['deleted'])){
				  ?>
				  <i class='fa fa-undo' style='    font-size: 17px;
    background: white;
    padding: 5px;
    vertical-align: super;cursor:pointer;' id="stamp_no_archive_<?=$value['ID']?>" data-number="<?=$value['ID']?>" onclick="no_archivestamp(this.id);"></i>
				  <?php
			  }
			  ?>
		  <?php } ?>
		  </td>
          <?

        ?>
        </tr>
        <?
      }
}else{echo "<td colspan='14' style='text-align:center;'><h3>по вашему запросу ничего не найдено</h3></td>";}
//
?>
<