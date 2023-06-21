<?php
$id=$_GET['qid'];
$name_file="contract-".$id.".docx";


$file_name="dogovor.docx";
//
//   header("Content-Length: ".filesize($file_name));
//
//   header("Content-Disposition: attachment; filename=".$file_name);
//
header("Content-Type: application/x-force-download; name=\"".$file_name."\"");
header("Content-type: application/vnd.ms-word");
header("Content-disposition: attachment; filename=contract-$id.docx");



//queries
include '../includes/db.inc.php';
$query = "SELECT
                queries.uid,
                queries.typ_ord typ_ord,
                clients.name client_name,
                clients.cont_pers name_use,
                clients.inn client_inn,
                clients.kpp client_kpp,
                clients.firm_tel client_tel,
                clients.bik client_bik,
                clients.bank client_bank,
                clients.email client_email,
                clients.rs_acc client_rc_acc,
                IFNULL(legal_address, postal_address) client_address,
                obj_accounts.name product_title,
                obj_accounts.num product_quantity,
                obj_accounts.art_num artikul,
                obj_accounts.price,
                queries.booking_till,
                queries.date_query created_at
            FROM queries
                JOIN clients ON clients.uid = queries.client_id
                JOIN obj_accounts ON queries.uid = obj_accounts.query_id
            WHERE queries.uid = $id";
            // echo $query;
$resource = mysql_query($query);
while ($row = mysql_fetch_array($resource, MYSQL_ASSOC)) {
    $result[] = $row;
}
// print_r($result);
$client_name=iconv('windows-1251', 'UTF-8', $result[0]['client_name']);
$name_use=iconv('windows-1251', 'UTF-8', $result[0]['name_use']);
$adress_dost=iconv('windows-1251','UTF-8',$result[0]['client_address']);
//реквизиты
$client_inn=iconv('windows-1251','UTF-8',$result[0]['client_inn']);
$client_kpp=iconv('windows-1251','UTF-8',$result[0]['client_kpp']);
$client_tel=iconv('windows-1251','UTF-8',$result[0]['client_tel']);
$client_bik=iconv('windows-1251','UTF-8',$result[0]['client_bik']);
$client_bank=iconv('windows-1251','UTF-8',$result[0]['client_bank']);
$client_email=iconv('windows-1251','UTF-8',$result[0]['client_email']);
$client_rc_acc=iconv('windows-1251','UTF-8',$result[0]['client_rc_acc']);

$typ_ord=$result[0]['typ_ord'];
$sum=0;
foreach ($result as $lines){
  $sum+=$lines['price']*$lines['product_quantity'];
}
//
/**
 * Возвращает сумму прописью *
**/
function num2str($num) {
	$nul='ноль';
	$ten=array(
		array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
		array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
	);
	$a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
	$tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
	$hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
	$unit=array( // Units
		array('копейка' ,'копейки' ,'копеек',	 1),
		array('рубль'   ,'рубля'   ,'рублей'    ,0),
		array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
		array('миллион' ,'миллиона','миллионов' ,0),
		array('миллиард','милиарда','миллиардов',0),
	);
	//
	list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
	$out = array();
	if (intval($rub)>0) {
		foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
			if (!intval($v)) continue;
			$uk = sizeof($unit)-$uk-1; // unit key
			$gender = $unit[$uk][3];
			list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
			// mega-logic
			$out[] = $hundred[$i1]; # 1xx-9xx
			if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
			else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
			// units without rub & kop
			if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
		} //foreach
	}
	else $out[] = $nul;
	$out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
	$out[] = $kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
	return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
}

/**
 * Склоняем словоформу *
**/
function morph($n, $f1, $f2, $f5) {
	$n = abs(intval($n)) % 100;
	if ($n>10 && $n<20) return $f5;
	$n = $n % 10;
	if ($n>1 && $n<5) return $f2;
	if ($n==1) return $f1;
	return $f5;
}
//


require $_SERVER["DOCUMENT_ROOT"].'/acc/includes/lib/phpword/autoload.php';

$phpWord = new  \PhpOffice\PhpWord\PhpWord();

use PhpOffice\PhpWord\Element\Table;

if ($typ_ord==1 || $typ_ord==3){
$_doc = new \PhpOffice\PhpWord\TemplateProcessor('chablon1_word1.docx');
}else{
$_doc = new \PhpOffice\PhpWord\TemplateProcessor('chablon1_word1_1.docx');}

$_doc->setValue('per1', $client_name);
$_doc->setValue('per2', $name_use);
$_doc->setValue('per3', $sum);
$_doc->setValue('per4',num2str($sum));
$_doc->setValue('per8',$adress_dost);
if ($typ_ord==1 || $typ_ord==3){
  //Срок исполнения
  $_doc->setValue('per5','-');
  $_doc->setValue('per6','-');
}else{
  //дата день и месяц
  $arr = [
  'январь',
  'февраль',
  'март',
  'апрель',
  'май',
  'июнь',
  'июль',
  'август',
  'сентябрь',
  'октябрь',
  'ноябрь',
  'декабрь'
];
  $month = date('n')-1;
  $_doc->setValue('per5',date('d'));
  $_doc->setValue('per6',$arr[$month]);
  $_doc->setValue('per-year',date('Y'));
}
if ($client_inn==null){
  $dan=$client_kpp;
}else{$dan=$client_inn;}
$_doc->setValue('block-yr',$client_name);
$_doc->setValue('block-yr1',$client_address);
$_doc->setValue('block-yr2',$dan);
$_doc->setValue('block-yr3',$client_rc_acc);
$_doc->setValue('block-yr4',$client_bank);
$_doc->setValue('block-yr5',$client_bik);
$_doc->setValue('block-yr6',$client_tel);
$_doc->setValue('block-yr7',$client_email);
$_doc->setValue('block-yr8',$client_name);
/*---*/
$styleTable = array('borderSize' => 6, 'borderColor' => '#000000','cellMargin'=>0);
$style_text=array('color'=>'#000000', 'size'=>8, 'bold'=>true,'name'=>'Times New Roman','align'=>'center');
$style_text1=array('color'=>'#000000', 'size'=>8, 'bold'=>false,'name'=>'Times New Roman','align'=>'left');
$section = $phpWord->addSection($styleTable);

$table = $section->addTable();

// for ($r = 1; $r <= 5; $r++) {
//     $table->addRow();
//     for ($c = 1; $c <= 8; $c++) {
//         $table->addCell(102,8031496063)->addText("Row {$r}, Cell {$c}");
//     }
// }
//  $sum+=$lines['price']*$lines['product_quantity'];
if ($typ_ord==1 || $typ_ord==3){
$chablon_table=array('Наименование и размер изделия (шир х выс х бок)','Материал и граммаж','Печать','Ламинация и другая постпечатная обработка','Ручки и люверсы','Цена','Количество','Стоимость');
}else{
  $chablon_table=array('Артикул','Краткое описание','Цена','Количество','Стоимость');
}
  $table->addRow();
foreach ($chablon_table as $calls){
  // echo $calls."</br>";
  $table->addCell(1750,$styleTable)->addText($calls,$style_text);
}

// foreach ($result as $key => $value)
// {
//   foreach ($value as $key1=> $lines1){
//     // echo "[$key1]".iconv("windows-1251", "UTF-8",$lines1)."</br>";
//   }
//   // echo iconv("UTF-8", "CP1251",$value);
// }
if ($typ_ord==1 || $typ_ord==3){
foreach ($result as $lines){
      $table->addRow();
      //Наименование и размер изделия (шир х выс х бок)
      $col1=iconv('windows-1251', 'UTF-8', $lines['product_title']);
      $table->addCell(1750,$styleTable)->addText($col1,$style_text1);
      //Материал и граммаж
      $col2=iconv('windows-1251', 'UTF-8', '-');
      $table->addCell(1750,$styleTable)->addText($col2,$style_text1);
      //Печать
      $col3=iconv('windows-1251', 'UTF-8', '-');
      $table->addCell(1750,$styleTable)->addText($col3,$style_text1);
      //Ламинация и другая постпечатная обработка
      $col4=iconv('windows-1251', 'UTF-8', '-');
      $table->addCell(1750,$styleTable)->addText($col4,$style_text1);
      //Ручки и люверсы
      $col5=iconv('windows-1251', 'UTF-8', '-');
      $table->addCell(1750,$styleTable)->addText($col5,$style_text1);
      //Цена
      $col6=iconv('windows-1251', 'UTF-8', $lines['price']);
      $table->addCell(1750,$styleTable)->addText($col6,$style_text1);
      //Количество
      $col7=iconv('windows-1251', 'UTF-8', $lines['product_quantity']);
      $table->addCell(1750,$styleTable)->addText($col7,$style_text1);
      //Стоимость
      $stoim=$col6*$col7;
      $col8=iconv('windows-1251', 'UTF-8', $stoim);
      $table->addCell(1750,$styleTable)->addText($col8,$style_text1);
}
}else{
  foreach ($result as $lines){
    $table->addRow();
    //Артикул
    $col1=iconv('windows-1251', 'UTF-8', $lines['artikul']);
    $table->addCell(1750,$styleTable)->addText($col1,$style_text1);
    //Краткое описание
    $col2=iconv('windows-1251', 'UTF-8', $lines['product_title']);
    // echo $col2;
    $table->addCell(1750,$styleTable)->addText($col2,$style_text1);
    //Цена
    $col3=iconv('windows-1251', 'UTF-8', $lines['price']);
    $table->addCell(1750,$styleTable)->addText($col3,$style_text1);
    //Количество
    $col4=iconv('windows-1251', 'UTF-8', $lines['product_quantity']);
    $table->addCell(1750,$styleTable)->addText($col4,$style_text1);
    //Стоимость
      $stoim=$col3*$col4;
    $table->addCell(1750,$styleTable)->addText($stoim,$style_text1);
  }

}

// Create writer to convert document to xml
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

// Get all document xml code
$fullxml = $objWriter->getWriterPart('Document')->write();

// Get only table xml code
$tablexml = preg_replace('/^[\s\S]*(<w:tbl\b.*<\/w:tbl>).*/', '$1', $fullxml);

$_doc->setValue('per7', $tablexml);
/*---*/

$img_Dir_Str = "acc/files/";
//$_doc->setValue('summa_str', num2str($summa));
$img_Dir = $_SERVER['DOCUMENT_ROOT']."/". $img_Dir_Str;
//echo $img_Dir;
@mkdir($img_Dir, 0777);
$file = str_replace("/","-", "dogovor.docx");
$_doc->saveAs($img_Dir.$file);
readfile($img_Dir.$file);


// header('Content-Type: application/vnd.openxmlformats');
// header('Content-Disposition: attachment; filename="dogovor.docx"');
// readfile($img_Dir.$file);


// $curl = curl_init();
// $options = [
//                 CURLOPT_URL =>  "http://".$_SERVER[SERVER_NAME]."/acc/files/index_word.php?qid=".$number,
//                 CURLOPT_RETURNTRANSFER => true,
//                 CURLOPT_ENCODING => "",
//                 CURLOPT_MAXREDIRS => 10,
//                 CURLOPT_TIMEOUT => 30,
//                 CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//                 CURLOPT_CUSTOMREQUEST => "GET"
// ];
// // echo "http://".$_SERVER[SERVER_NAME]."/acc/files/index_word.php?qid=".$number;
// curl_setopt_array($curl, $options);
// $responce = curl_exec($curl);
// $errors = curl_error($curl);
// curl_close($curl);
// header("Content-disposition: attachment; filename=contract-$number.docx");
// header("Content-type: application/octet-stream");
// header("Content-Description: File Transfer");
// $img_Dir_Str = "/acc/files/";
// $img_Dir = $_SERVER['DOCUMENT_ROOT']."/". $img_Dir_Str;
// $file = "dogovor.docx";
// readfile($img_Dir.$file);
// Имя скачиваемого файла

?>
