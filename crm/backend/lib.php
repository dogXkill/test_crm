<?


function check_is_email($email){

if (strpos($email, "@") == true and strpos($email, ".") == true){return "1";}

}




function clear_some_parts($var_to_clear){

$var_to_clear = str_replace("'", "", $var_to_clear);
$var_to_clear = str_replace("\"", "", $var_to_clear);
$var_to_clear = str_replace("%%%", "", $var_to_clear);

return $var_to_clear;
}



  //очищаем заголовок письма от лишнего
  function clear_subject($subject,$crm_outbox_check){

  $subject = iconv_mime_decode($subject,0, "windows-1251");
  if($crm_outbox_check !== "1"){
  $subject = str_replace('RE: ', ' ', $subject);
  $subject = str_replace('Re: ', ' ', $subject);
  $subject = str_replace('Fwd ', ' ', $subject);
  $subject = str_replace('FW: ', ' ', $subject);
  }
  $subject = str_replace('[Blacklisted] ', ' ', $subject);
  $subject = str_replace('[Spam] ', ' ', $subject);
  $subject = preg_replace('%[ ](?=[ ])|[^-_,A-Za-zА-Яа-я0-9 ]+%', '', $subject);


  return $subject;
  }



  //функция проставляет id всем обращениям поступившим в последние Х дней, если найдена уже существующая открытая сделка со стадией > 0
function update_appeals_set_sdelka_id($existing_deal_id,$date_added,$email){
    /*
    $start_date = date($date_added,strtotime("-5 days"));

    $set_sdelka_id_q = "UPDATE crm_appeals SET deal_id = '$existing_deal_id' AND email='$email' WHERE date_added BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW() AND deleted <> 1";
    mysql_query($set_sdelka_id_q);

    $set_sdelka_id_q2 = "UPDATE crm_outcoming_appeals SET deal_id = '$existing_deal_id' AND recepient='$email' WHERE date_added BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW() AND deleted <> 1";
    mysql_query($set_sdelka_id_q2);

    echo "<br><i>Проставлена id существующей сделки ($existing_deal_id) к входящим и исходящим письмам по емейлу $email<</i>br>";

     */
    //echo mysql_error().$set_sdelka_id_q;
}

 ?>