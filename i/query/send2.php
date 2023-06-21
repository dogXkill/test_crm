<?
  if (mail("vlad@printfolio.ru", "my subject", "stroka1"))
		echo "Отправка удалась!";
	else
		echo 'Ошибка!';

exit;	

	$recipient = "vlad@printfolio.ru";
	$subject = "test mail tom";
	$message ="Проверка отсылки почты с сервера printfolio";
	
	$header="Content-type: text/html; charset=\"windows-1251\"";
	$header.="From: \"Evgen\" <evgen@mail.ru>";
	$header.="Subject: $subject";
	$header.="Content-type: text/html; charset=\"windows-1251\"";
	
	if (mail($recipient, $subject, $message, $header)) {
		echo "Отправка удалась!";
	}
	else
		echo 'Ошибка!';
		
		
	exit;
	
	
  // Высылаем на определенный e-mail, - указанный e-mail
        $recipient = "zakaz@printfolio.ru ; ".$_POST['manager'];

        $subject = $_POST['client']." - заказ";
        $message .= "<br><br><strong>Прошу выставить счет</strong><br><br><strong>От кого:</strong> <a href=mailto:".$_POST['manager'].">".$_POST['manager']."</a>\n";
        $message .= "<br>1. <strong>Наименование клиента:</strong> ".$_POST['client']."\n";
        $message .= "<br>2. <strong>Реквизиты:</strong><br> ".$_POST['req']."\n";
        $message .= "<br>3. <strong>Предмет счета:</strong> ".$_POST['predmet']."\n";
        $message .= "<br>4. <strong>Сумма счета:</strong> ".$_POST['sum']."\n";
		$message .= "<br>5. <strong>Наименования подрядчиков / какую часть работы они исполняют / суммы счетов:</strong><br> ".$_POST['supl']."\n";
		$message .= "<br>6. <strong>Общая себестоимость:</strong> ".$_POST['cost']."\n";
		$message .= "<br>7. <strong>Примечание:</strong><br> ".$_POST['adition']."\n";
		
        $headers .= "X-Mailer: PHP\n"; // mailer
        $headers .= "X-Priority: 1\n"; // Urgent message!
    
		$headers .= 'Content-type: text/html; charset=windows-1251' . "\r\n";
        
      
        if (mail($recipient, $subject, $message, $headers)) {
header("Location: http://printfolio.ru/query/index.php?id=ok&idm=dfgkjshdgjkdshfjdncjkdnkcndsjcdjkr89u8ud89fu8ufusd8af8sdfu8sdf8ds");
        }else {
            print "<font face=tahoma size=4 color=#273456>Иза сбоя на сервере Ваше сообщение не удалось отправить. Пишите на zakaz@printfolio.ru</font>";
        }
?>