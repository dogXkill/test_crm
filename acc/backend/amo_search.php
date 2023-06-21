<?php
require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
include_once '../../amo/amocrm.php';//подключаем амо
//поиск и создание сделки
//tip=1 - поиск 
//tip=2 - создание 
$amoCrm=new AmoCrm();
$amoCrm->login_amo();
if ($_GET['tip']==1){
$result=$amoCrm->load_sdelki_amo_id($_GET['id_amo']);
//print_r($result);
$query_id=$_GET['query_id'];
	$id_amo=$_GET['id_amo'];
if (count($result)==0){
	//сделок не найдено
	$mas['result']=0;
}else if (count($result)==1){
	$mas['result']=1;
	//нашли такую сделку
	//меняем в бд ей id_amo
	
	$sql="UPDATE `queries` SET `amo_crm_id` = '{$id_amo}' WHERE `uid` = '{$query_id}';";
	$result=mysql_query($sql);
	
}else if (count($result)>1){
	
	$dat_max=0;//максимальная дата 
	$temp_id=0;
	foreach ($result as $value){
		if ($value['created_at']>$dat_max){$dat_max=$value['created_at'];$temp_id=$value['id'];}
	}
	if ($temp_id!=0 && $temp_id!=null){
		$sql="UPDATE `queries` SET `amo_crm_id` = '{$id_amo}' WHERE `uid` = '{$query_id}';";
		$result=mysql_query($sql);
		$mas['result']=1;
	}else{
		$mas['result']=0;
	}
	//$mas['text']
	//несколько результатов
}else{
	//иная причина
	$mas['result']=0;
}
}
else if ($_GET['tip']==2){
	//создаем сделку 
	$name_cont=$_GET['name_cont'];
	$phone=$_GET['phone'];
	$phone2=$_GET['phone2'];
	$email=$_GET['email'];
	$query_id=$_GET['query_id'];
	$phone=$amoCrm->valid($phone,$email);
	$phone2=$amoCrm->valid($phone2);
	$zn_contant=$amoCrm->check_client_amo($phone['phone']);
	if ($zn_contant==false){
	//создаем контакт
	$contacts1['add']=array(
	array(
	'name'=>$name_cont,
		  'custom_fields'=>array(
			array(
			  'id'=>284889,
			  'values'=>array(
				array(
				  'value'=>$phone['email'],
				  'enum'=>'WORK'
				)
			  )
			),
			array(
			  'id'=>284887,
			  'values'=>array(
				array(
				  'value'=>$phone['phone'],
				  'enum'=>'WORK'
				),
				array(
				  'value'=>$phone2['phone'],
				  'enum'=>'WORK'
				)
			  )
			)
		  )
		 )
	);
		$zn_contant=$amoCrm->add_client_amo1($contacts1);//возвращает id контакта для создания сделки
		if ($zn_contant!=0){
			
		}else{$zn_contant=1;}
		
	}
	$zn_contant=preg_replace('~\D+~','', $zn_contant);
	$mas['zn_contant']=$zn_contant;
	
	$goodPrice=(double) $_GET['summ_itog'];
	$query1 = sprintf("SELECT * FROM obj_accounts WHERE query_id=%d", $query_id);
    $res_qr1 = mysql_query($query1);
    $arr_prdm_list = array();
	$roistatComm =array();
	$roistatComm1='';
    while ($r_prdm1 = mysql_fetch_array($res_qr1)){
        //$arr_prdm_list[] = $r_prdm;
		if(empty($r_prdm1['delivery'])) {
			$name_t=$r_prdm1['name'];
			$name_t1=$r_prdm1['name'];
			$name_t=mb_convert_encoding($name_t,"utf-8", "windows-1251");
			$quy_t=$r_prdm1['num'];
			//mb_convert_encoding($text, 'windows-1251', 'utf-8');
			$price_t=$r_prdm1['price'];
			//$temp_str="$name_t, кол-во: $quy_t шт., цена: $price_t руб.\r\n";
			$temp_str="$name_t, кол-во: $quy_t шт., цена: $price_t руб.\r\n";
            $roistatComm[]= $temp_str;
			$roistatComm1.="{$name_t}, кол-во: {$quy_t} шт., цена: {$price_t} руб.\r\n";//{$name_t1}  
			//echo $temp_str."</br>";
        }
	}
	//$roistatComm[]= "\r\nКомментарий к заказу\r\n";
	$roistatComm1.="\r\nКомментарий к заказу\r\n";
	//echo $roistatComm;
	//смотрим id в users
	//если amo не заполнен - >$user_amo=3939454
	$sql = sprintf("SELECT * FROM queries WHERE uid=%d", $query_id);
	//$sql="SELECT * FROM queries WHERE uid = '{$query_id}'";
	//echo $sql;
	$res_qr = mysql_query($sql);
	$r_prdm = mysql_fetch_array($res_qr);
	$user_id=$r_prdm['user_id'];
	$sql = sprintf("SELECT * FROM users WHERE uid=%d", $user_id);
	//$sql="SELECT * FROM users WHERE `users`.`uid` = {$user_id};";
	$res_qr = mysql_query($sql);
	$r_prdm = mysql_fetch_array($res_qr);
	$users_amo_id=$r_prdm['amo_id'];
	if ($users_amo_id==null || $users_amo_id==""){
		$users_amo_id=3939454;
	}
	//echo $users_amo_id ;
	$datetime = new DateTime();
	$leads['add']=array(
			  array(
				"name" =>$name_cont,
				"created_at" =>$datetime->getTimestamp(),
				"status_id" =>intval(30754849),
				"sale" =>$goodPrice,
				"tags" =>"автоматически_из_црм",
				'pipeline_id' => '30619345',
				'contacts_id'=>$zn_contant,
			  )
			);
			$id_sdelki=$amoCrm->add_sdelki_amo($leads);
			//меняем ответ.
			if ($user_amo_id!=3939454){
				$update['update']=array(
					array(
					'id'=>$id_sdelki,
					'responsible_user_id'=>intval($users_amo_id),
					 'updated_at'=>$datetime->getTimestamp(),
						 )
				);
					$res_update=$amoCrm->add_sdelki_amo($update);
			}
			//'comment' => $roistatComm,
			//echo $roistatComm;
			//$roistatComm
			//создаем примечание
			$cr_at=$datetime->getTimestamp();
			/*0 => array(
						'element_id' => $id_sdelki,
						'element_type' => '2',
						'text' => $roistatComm,
						'note_type' => '4',
						'created_at' => $datetime->getTimestamp(),
					),
					*/
					/*
			foreach ($roistatComm as $comments){
				$com=array();
				$com[]=array('element_id' => $id_sdelki,'element_type' => '2','text' => $comments,'note_type' => '4','created_at' =>$cr_at);
				$data_note = array(
				'add' => array(
					$com
				),
			);
				$data_note1 = array(
				'add' => array(
					0 => array(
						'element_id' => $id_sdelki,
						'element_type' => '2',
						'text' => $comments,
						'note_type' => '4',
						'created_at' => $datetime->getTimestamp(),
					),
				),
			);
			$amoCrm->add_note($data_note1);
			}
			
			$data_note = array(
				'add' => array(
					$com
				),
			);*/
			$data_note = array(
				'add' => array(
					0 => array(
						'element_id' => $id_sdelki,
						'element_type' => '2',
						'text' => $roistatComm1,
						'note_type' => '4',
						'created_at' => $datetime->getTimestamp(),
					),
				),
			);
			//print_r($data_note);
			$amoCrm->add_note($data_note);
			$sql="UPDATE `queries` SET `amo_crm_id` = '{$id_sdelki}' WHERE `uid` = '{$query_id}';";
			$result=mysql_query($sql);
			$mas['result']=1;
			$mas['id_amo']=$id_sdelki;
			//$mas['roistatComm']=$roistatComm;
}
echo json_encode($mas);
//}
?>