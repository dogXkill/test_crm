<?php
//для тестирования класса
require_once __DIR__.'/amocrm.php';
//данные с формы
/*формат на roistat

$roistatData = array(
            'name'    => $fio,
            'phone'   => $phone,
            'email'   => $email,
            'comment' => $roistatComm,
            'price'   => $goodPrice,
            'fields'  => array(
              'tags' => 'магазин_корзина',
              'status_id' => '142',
              'pipeline_id' => '2160859',  
            )
        );
		
		*/
		//массив клиента
$roistatComm='';
$goodPrice=123;
$data=array(
  'name'=>'test инт. NEW',
  'phone'=>'88005553535',
  'email'=>'test1@mail.ru',
  'comment' => $roistatComm,
  'price'   => $goodPrice,
            'fields'  => array(
              'tags' => 'магазин_корзина',
              'status_id' => '30619345',
              'pipeline_id' => '2160859',  
            )
);
//
$amoCrm=new AmoCrm();
//79267268330
$amoCrm->login_amo();
$amoCrm->account_check();
echo "</br>";
echo "test:".$amoCrm->load_sdelki_amo('74353453454');
//провереряем клиента по телефону,если нету - создаём и возвращаем id ,иначе просто вернём id тек.клиента
//$zn_contant=$amoCrm->check_client_amo('88005553535');

//$amoCrm->check_client_amo('79267268330');
//если новый контакт - создание

$custom_fields=array('EMAIL'=>'EMAIL','PHONE'=>'PHONE','FORMNAME'=>'FORMNAME');
/*test contact 1*/
$contacts['request']['contacts']['add']=array(
      'name'=>$data['name'],
      'custom_fields'=>array(
        array(
          'id'=>$custom_fields['EMAIL'],
          'values'=>array(
            array(
              'value'=>$data['email'],
              'enum'=>'WORK'
            )
          )
        ),
		array(
          'id'=>$custom_fields['PHONE'],
          'values'=>array(
            array(
              'value'=>$data['phone'],
              'enum'=>'WORK'
            )
          )
        ),
		array(
          'id'=>$custom_fields['FORMNAME'],
          'values'=>array(
            array(
              'value'=>'Корзина',
            )
          )
        )
      )
    );
/*--2--*/
$contacts1['add']=array(
array(
'name'=>$data['name'],
      'custom_fields'=>array(
        array(
          'id'=>284889,
          'values'=>array(
            array(
              'value'=>$data['email'],
              'enum'=>'WORK'
            )
          )
        ),
		array(
          'id'=>284887,
          'values'=>array(
            array(
              'value'=>$data['phone'],
              'enum'=>'WORK'
            )
          )
        )
      )
	 )
);
	echo "zn_con:".$zn_contant;
	if ($zn_contant==false){
	//создаем контакт
		//$zn_contant=$amoCrm->add_client_amo($contacts);
		//$zn_contant=$amoCrm->add_client_amo1($contacts1);//возвращает id контакта для создания сделки
		if ($zn_contant!=0){
			
		}else{echo "ошибка добавления контакта";}
		echo $zn_contant;
	}
//создание сделки
$mas_sdelki=array();
$mas_polei=array("ISTOCHIK"=>431721);
$datetime = new DateTime();

$leads['add']=array(
  array(
	"name" =>"Новая заявка с формы: Корзина",
	"created_at" =>$datetime->getTimestamp(),
	"status_id" =>"30619345",
	"sale" =>$goodPrice,
	"tags" =>"магазин_корзина",
	'pipeline_id' => '30619345',
	'contacts_id'=>$zn_contant
  )
);
//$amoCrm->add_sdelki_amo($leads);//добавить /обновить сделку

		
/*
{
   add: [
      {
         name: "Покупка карандашей",
         created_at: "1508101200",
         updated_at: "1508274000",
         status_id: "13670637",
         responsible_user_id: "957083",
         sale: "5000",
         tags: "pencil, buy",
         contacts_id: [
            "1099149"
            ],
            company_id: "1099148",
            custom_fields: [
               {
                  id: "4399649",
                  values: [
                     "3691615",
                     "3691616",
                     "3691617"
                  ]
               },
               {
                  id: "4399656",
                  values: [
                     {
                        value: "2017-10-26"
                     }
                  ]
               },
               {
                  id: "4399655",
                  values: [
                     {
                        value: "ул. Охотный ряд, 1",
                        subtype: "address_line_1"
                     },
                     {
                        value: "Москва",
                        subtype: "city"
                     },
                     {
                        value: "101010",
                        subtype: "zip"
                     },
                     {
                        value: "RU",
                        subtype: "country"
                     }
                  ]
               }
            ]
      }
   ]
}
*/
$data_note = array(
    'add' => array(
        0 => array(
            'element_id' => '25689535',
            'element_type' => '2',
            'text' => 'Примечание',
            'note_type' => '4',
            'created_at' => $datetime->getTimestamp(),
        ),
    ),
);
//$amoCrm->add_note($data_note);
echo $amoCrm->load_sdelki_amo('+79032479926'); 
?>              
