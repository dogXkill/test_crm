<div id="customerCallsBox">
	<div class="customerInfo">
		<div class="customerInfo_item clear">
			<div class="customerInfo_label">Контактное лицо</div>
			<div class="customerInfo_value"><?=$customer['cont_pers'];?></div>
		</div>
		<div class="customerInfo_item clear">
			<div class="customerInfo_label">Контактный телефон</div>
			<div class="customerInfo_value"><?=$customer['cont_tel'];?></div>
		</div>
		<div class="customerInfo_item clear">
			<div class="customerInfo_label">Телефоны</div>
			<div class="customerInfo_value"><?=$customer['firm_tel'];?></div>
		</div>
		<?php if (($decoded = json_decode($customer['temp_phone'], true))) { ?>
		<div class="customerInfo_item clear">
			<div class="customerInfo_label">"Чистые телефоны"</div>
			<div class="customerInfo_value"><?=implode(', ', $decoded);?></div>
		</div>
		<?php } ?>
		<div class="customerInfo_item clear">
			<div class="customerInfo_label">Email</div>
			<div class="customerInfo_value"><?=$customer['email'];?></div>
		</div>
	</div>
	
	<div class="addCallBox">
		<div class="addCallBoxInfo">Добавить звонок <span class="setDate" data-days="0">сегодня</span> <span class="setDate" data-days="1">вчера</span> <span class="setDate" data-days="2">п/вчера</span></div>
		<div class="addCallForm_errors"></div>
		<div class="addCallForm clear">
			<div class="addCallForm_field" data-field-id="date" id="addCallForm_field__date">
				<div class="addCallForm_label">Дата</div>
				<div class="addCallForm_input"><input type="text" class="inputDate" name="date" value="" readonly></div>
			</div>
			<div class="addCallForm_field" data-field-id="result" id="addCallForm_field__result">
				<div class="addCallForm_label">Результат звонка</div>
				<div class="addCallForm_input"><?=CustomersCallsHelper::printCallResultSelect();?></div>
			</div>
			<div class="addCallForm_field" data-field-id="customResult" id="addCallForm_field__customResult">
				<div class="addCallForm_label">Другой результат</div>
				<div class="addCallForm_input"><input type="text" /></div>
			</div>
			<div class="addCallForm_field" data-field-id="comment" id="addCallForm_field__comment">
				<div class="addCallForm_label">Комментарий к звонку</div>
				<div class="addCallForm_input"><textarea></textarea></div>
			</div>
		</div>
		<div class="addCallForm_buttons"><span id="addCallButton"><i class="fa fa-plus"></i><span>Добавить</span></span></div>
	</div>
	

	<?=$callsList;?>
</div>