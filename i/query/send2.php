<?
  if (mail("vlad@printfolio.ru", "my subject", "stroka1"))
		echo "�������� �������!";
	else
		echo '������!';

exit;	

	$recipient = "vlad@printfolio.ru";
	$subject = "test mail tom";
	$message ="�������� ������� ����� � ������� printfolio";
	
	$header="Content-type: text/html; charset=\"windows-1251\"";
	$header.="From: \"Evgen\" <evgen@mail.ru>";
	$header.="Subject: $subject";
	$header.="Content-type: text/html; charset=\"windows-1251\"";
	
	if (mail($recipient, $subject, $message, $header)) {
		echo "�������� �������!";
	}
	else
		echo '������!';
		
		
	exit;
	
	
  // �������� �� ������������ e-mail, - ��������� e-mail
        $recipient = "zakaz@printfolio.ru ; ".$_POST['manager'];

        $subject = $_POST['client']." - �����";
        $message .= "<br><br><strong>����� ��������� ����</strong><br><br><strong>�� ����:</strong> <a href=mailto:".$_POST['manager'].">".$_POST['manager']."</a>\n";
        $message .= "<br>1. <strong>������������ �������:</strong> ".$_POST['client']."\n";
        $message .= "<br>2. <strong>���������:</strong><br> ".$_POST['req']."\n";
        $message .= "<br>3. <strong>������� �����:</strong> ".$_POST['predmet']."\n";
        $message .= "<br>4. <strong>����� �����:</strong> ".$_POST['sum']."\n";
		$message .= "<br>5. <strong>������������ ����������� / ����� ����� ������ ��� ��������� / ����� ������:</strong><br> ".$_POST['supl']."\n";
		$message .= "<br>6. <strong>����� �������������:</strong> ".$_POST['cost']."\n";
		$message .= "<br>7. <strong>����������:</strong><br> ".$_POST['adition']."\n";
		
        $headers .= "X-Mailer: PHP\n"; // mailer
        $headers .= "X-Priority: 1\n"; // Urgent message!
    
		$headers .= 'Content-type: text/html; charset=windows-1251' . "\r\n";
        
      
        if (mail($recipient, $subject, $message, $headers)) {
header("Location: http://printfolio.ru/query/index.php?id=ok&idm=dfgkjshdgjkdshfjdncjkdnkcndsjcdjkr89u8ud89fu8ufusd8af8sdfu8sdf8ds");
        }else {
            print "<font face=tahoma size=4 color=#273456>��� ���� �� ������� ���� ��������� �� ������� ���������. ������ �� zakaz@printfolio.ru</font>";
        }
?>