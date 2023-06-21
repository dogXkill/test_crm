

<html>

<head>
  <title>Untitled</title>
</head>

<body style="margin:0px;padding:0px;">
<?
$adress = $_GET["adress"];
$adress = iconv("CP1251", "UTF-8",$adress);
$adress=str_replace ('   ','+',$adress);
$adress=str_replace ('  ','+',$adress);
$adress=str_replace (' ','+',$adress);
 ?>
<iframe width="792" style="overflow:hidden;border:0; margin-top: -120px;" height="784" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.ru/maps?f=q&source=s_q&hl=ru&geocode=&q=<?=$adress;?>&output=embed&ll=55.735616,37.602081&z=9&spn=0.42525,1.028595"></iframe>
<br>
<small><a style="color:#0000FF;text-align:left" href="http://local.google.com/?ie=UTF8&t=h&vpsrc=6&ll=55.735616,37.602081&spn=0.42525,1.028595&z=10&source=embed&q=<?=$adress;?>" target="_blank">перейти на гугл</a></small>
 <?echo iconv("CP1251", "UTF-8",$_GET["adress"]);?>
</body>

</html>