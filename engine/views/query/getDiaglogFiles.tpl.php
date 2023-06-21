<?php
//print_r($params);
$uid=$params["queryId"];
$tip=$params['tip'];
$name_client=$params['name_client'];
$pdf_icon='<i class="fa-duotone fa-file-pdf fa-lg" style="--fa-primary-color: #fafafa; --fa-primary-opacity: 1; --fa-secondary-color: #de1212; --fa-secondary-opacity: 0.8;"></i>';
$word_icon='<i class="fa-duotone fa-file-word fa-lg" style="--fa-primary-color: #fafafa; --fa-secondary-color: #005eff; --fa-secondary-opacity: 1;"></i>';
switch($tip){
	case "pdf":
		$text="счет в PDF";
		$invoice = '<a href=/acc/backend/invoice_pdf.php?qid=' . $uid . ' title="Скачать счет в PDF">'.$pdf_icon.'</a>' . PHP_EOL;
		
	break;
	case "pdf1":
		$text="накладную в PDF";
		$invoice = '<a href=/acc/backend/waybill_pdf.php?qid=' . $uid . ' title="Скачать накладную в PDF">'.$pdf_icon.'</a>' . PHP_EOL;
	break;
	case "word":
		$text="договор в Word";
		$invoice = '<a href=/acc/files/load_word.php?qid=' . $uid . ' title="Скачать договор в Word">'.$word_icon.'</a>' . PHP_EOL;
	break;
}
echo "<h3>Отправить клиенту {$text}</h3>";
?>
<div id='uid_<?php echo $uid;?>'>
<input type='hidden' value="<?php echo $urls;?>" id='path_popup'>
<input type='hidden' value="<?php echo $uid;?>" id='uid_popup'>
<input type='hidden' value="<?php echo $tip;?>" id='tip_popup'>
<input type='hidden' value="<?php echo $name_client;?>" id='name_client'>
<p>Email клиента:&nbsp;<input type="text" value="<?php echo $params['email'];?>" id="to_email">
<span>&nbsp;<button class="otp_email_client">Отправить</button></span>
</p>
<div id='result_docs'>

</div>
<!--<p><button class='otp_email_client'>Отправить</button></p>-->
<hr>
<?php
echo "Скачать $text  $invoice";
?>
</div>
