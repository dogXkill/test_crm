<? $mbox = imap_open("{imap.yandex.ru:993/imap/ssl}", "test@upak.me", "222565306")or die("can't connect: " . imap_last_error());

   $mails = imap_fetch_overview($mbox, "1:10", FT_UID);

   foreach ($mails as $mail) {
        //номер сообщения в текущей выборке, не соответствует UID
        $msgno = $mail->msgno;
        //UID сообщения непосредственно на сервере
        //$uid = imap_uid($mbox, $msgno);

    $header = imap_headerinfo($mbox, $msgno);
    $email = $header->from[0]->mailbox ."@".$header->from[0]->host;
    $receipent = $header->to[0]->mailbox ."@".$header->to[0]->host;
    $date_email = date("Y-m-d H:i:s", strtotime($header->date));

    $subject = iconv_mime_decode($header->subject,0, "windows-1251");


    echo "$email $receipent $date_email $subject<br>";


}

/*
  foreach($mails as $mail){
     //$email = $header->from[0]->mailbox ."@".$header->from[0]->host;

     //$email = $mail[0]->mailbox ."@".$mail[0]->host;
     //echo $email."<br>";
     echo "<pre>";
     var_dump($mail);
     echo "</pre>";
    // echo "#$num_mail $email<br>";
  }   */


 /* $size=sizeof($mails);
for($i=$size-1;$i>=0;$i--){

print_r($val);
/*$val=$mails[$i];
$msg=$val->msgno;
$from=iconv_mime_decode($val->from,0, "windows-1251");
$date=$val->date;
$subj=$val->subject;
$subj = iconv_mime_decode($subj,0, "windows-1251");
  */
/*$header = imap_headerinfo ($mbox, $msg);
    echo $header->from[0]->mailbox ."@". $header->from[0]->host. '</br>';
    echo $header->to[0]->mailbox ."@". $header->to[0]->host. '</br>';*/

//echo "#$msg: From:'$from' Date:'$date' Subject:'$subj'<BR>";}


     /*foreach($mails as $email_number) {

     $header = imap_headerinfo ( $mails, $email_number);
   // echo '<p>Name  / Email Address: ' . $header->from[0]->personal ." ".
    $email = $header->from[0]->mailbox ."@". $header->from[0]->host. '<p></br>';
      echo $email."<br>";
      }   */      ?>