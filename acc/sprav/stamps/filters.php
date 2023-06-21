<?
// Filters
//print_r($_GET);
$sort_order = '';
if (isset($_GET['izd_type'])) {
  $cur_type = $_GET['izd_type'];
  if ($cur_type !== '0') {
    if (empty($sort_order)) {
      $sort_order = 'izd_type = ' . $_GET['izd_type'] ;
	  $sort_order_like='izd_type = ' . $_GET['izd_type'];
    } else {
      $sort_order = $sort_order . ' AND izd_type = ' . $_GET['izd_type'] ;
	  $sort_order_like=$sort_order_like . ' AND izd_type = ' . $_GET['izd_type'] ;
    }
  }
}
if (isset($_GET['sklei'])) {
	$sklei = $_GET['sklei'];
	if ($sklei !== '0') {
	  if (empty($sort_order)) {
		$sort_order = 'skleika = ' . $_GET['sklei'] ;
		$sort_order_like='skleika = ' . $_GET['sklei'];
	  } else {
		$sort_order = $sort_order . ' AND skleika = ' . $_GET['sklei'] ;
		$sort_order_like=$sort_order_like . ' AND skleika = ' . $_GET['sklei'] ;
	  }
	}
  }
//echo $sort_order;
if (isset($_GET['shir']) ) {
  if (empty($sort_order)) {
    $sort_order = 'shir = ' . $_GET['shir'] ;
	$sort_order_like='shir LIKE "%'.$_GET['shir'].'%"';
  } else {
    $sort_order = $sort_order . ' AND shir = ' . $_GET['shir'];
	if ((empty($sort_order_like))){
		$sort_order_like=$sort_order_like.'shir LIKE "%'.$_GET['shir'].'%"';
	}else{
		$sort_order_like=$sort_order_like.'AND shir LIKE "%'.$_GET['shir'].'%"';
	}
  }
}

if (isset($_GET['vis'])) {
  if (empty($sort_order)) {
    $sort_order = 'vis = ' . $_GET['vis'] ;
	$sort_order_like='vis LIKE "%'.$_GET['vis'].'%"';
  } else {
    $sort_order = $sort_order . ' AND vis = ' . $_GET['vis'] ;
	if ((empty($sort_order_like))){
		$sort_order_like=$sort_order_like.'vis LIKE "%'.$_GET['vis'].'%"';
	}else{
		$sort_order_like=$sort_order_like.'AND vis LIKE "%'.$_GET['vis'].'%"';
	}
  }
}

if (isset($_GET['bok']) ) {
  if (empty($sort_order)) {
    $sort_order = 'bok = ' . $_GET['bok'] ;
	$sort_order_like='bok LIKE "%'.$_GET['bok'].'%"';
  } else {
    $sort_order = $sort_order . ' AND bok = ' . $_GET['bok'] ;
	if ((empty($sort_order_like))){
		$sort_order_like=$sort_order_like.'bok LIKE "%'.$_GET['bok'].'%"';
	}else{
		$sort_order_like=$sort_order_like.'AND bok LIKE "%'.$_GET['bok'].'%"';
	}
  }
}
if (isset($_GET['deleted']) ) {
  if (empty($sort_order)) {
    $sort_order = 'deleted = ' . $_GET['deleted'] ;
	$sort_order_like='deleted LIKE "%'.$_GET['deleted'].'%"';
  } else {
    $sort_order = $sort_order . ' AND deleted = ' . $_GET['deleted'] ;
	if ((empty($sort_order_like))){
		$sort_order_like=$sort_order_like.'deleted LIKE "%'.$_GET['deleted'].'%"';
	}else{
		$sort_order_like=$sort_order_like.'AND deleted LIKE "%'.$_GET['deleted'].'%"';
	}
  }
}

if (isset($_GET['number'])) {
    $number = $_GET['number'];
	$type_search=null;
	if (is_numeric($number)){
		//число
	}else{
		//с префиксом
		//отсекаем буквы и ищем префикс в таблице типов
		//types
		$z = preg_replace ("/[^a-zа-я\s]/ui","",$number);//префикс 
		$type_search=check_prefix_zn($types,$z);
		//echo "t_s:".$type_search;
	}
	if ($type_search!=null){
		$number= preg_replace('/[^0-9]/', '', $number);
		if (empty($sort_order)) {
		  $sort_order = 'number = "' . $number .'" AND izd_type='.$type_search;
		  $sort_order_like='number = "' . $number.'" AND izd_type='.$type_search;
		} else {
		  $sort_order = $sort_order . ' AND number = "' . $number.'" AND izd_type='.$type_search ;
		  $sort_order_like=$sort_order_like . ' AND number = "' . $number.'" AND izd_type='.$type_search ;
		}
	}else{
  //$sort_order = "number = '$number'";
   if (empty($sort_order)) {
      $sort_order = 'number = "' . $number.'"' ;
	  $sort_order_like='number = "' . $number.'"';
    } else {
      $sort_order = $sort_order . ' AND number = "' . $number.'"' ;
	  $sort_order_like=$sort_order_like . ' AND number = "' . $number.'"' ;
    }
	}
  
}
if (isset($_GET['deleted'])) {
	//не пустой = 1
    $deleted = $_GET['deleted'];
  //$sort_order = "number = '$number'";
  
   if (empty($sort_order)) {
      $sort_order = 'deleted = ' . $deleted ;
	  $sort_order_like='deleted = ' . $deleted;
    } else {
      $sort_order = $sort_order . ' AND deleted = ' . $deleted ;
	  $sort_order_like=$sort_order_like . ' AND deleted = ' . $deleted ;
    }
  
}else{
	//пустой
	$deleted=0;
	 if (empty($sort_order)) {
      $sort_order = 'deleted = ' . $deleted ;
	  $sort_order_like='deleted = ' . $deleted;
    } else {
      $sort_order = $sort_order . ' AND deleted = ' . $deleted ;
	  $sort_order_like=$sort_order_like . ' AND deleted = ' . $deleted ;
    }
}
//
if (isset($_GET['vk_r'])) {
	//не пустой = 1
    $vk_r = $_GET['vk_r'];
  //$sort_order = "number = '$number'";
  
   if (empty($sort_order)) {
      $sort_order = 'status_vk_r = ' . $vk_r ;
	  $sort_order_like='status_vk_r = ' . $vk_r;
    } else {
      $sort_order = $sort_order . ' AND status_vk_r = ' . $vk_r ;
	  $sort_order_like=$sort_order_like . ' AND status_vk_r = ' . $vk_r ;
    }
  
}else{
	//пустой
	$vk_r=0;
	 if (empty($sort_order)) {
      $sort_order = 'status_vk_r = ' . $vk_r ;
	  $sort_order_like='status_vk_r = ' . $vk_r;
    } else {
      $sort_order = $sort_order . ' AND status_vk_r = ' . $vk_r ;
	  $sort_order_like=$sort_order_like . ' AND status_vk_r = ' . $vk_r ;
    }
}
//
if (!empty($sort_order)) {
  $sort_where = ' WHERE ';
} else {
  $sort_where = '';
}


?>
