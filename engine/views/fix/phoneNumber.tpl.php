<div id="fixContainer">
	<input id="currentUid" /><button id="getNextClient">Следующий клиент с непонятным номером</button> (количество левых <span id="count"><?php echo $count; ?></span>)
	<div id="resultContainer">
		<div class="row clear">
			<div class="row_label">Идентификатор</div>
			<div id="client_uid" class="row_content"></div>
		</div>
		<div class="row clear">
			<div class="row_label">Короткое название</div>
			<div id="client_short" class="row_content"></div>
		</div>
		<div class="row clear">
			<div class="row_label">Полное название</div>
			<div id="client_name" class="row_content"></div>
		</div>
		<div class="row clear">
			<div class="row_label">Юридический адрес</div>
			<div id="client_legal_address" class="row_content"></div>
		</div>
		<div class="row clear">
			<div class="row_label">Фактический/почтовый адрес:</div>
			<div id="client_postal_address" class="row_content"></div>
		</div>
		<div class="row clear">
			<div class="row_label">Контактный телефон:</div>
			<div id="client_cont_tel" class="row_content"></div>
		</div>
		<div class="row clear">
			<div class="row_label">Телефон:</div>
			<div id="client_firm_tel" class="row_content"></div>
		</div>
		
		<div id="workingArea">
			<div class="workingArea_row">
				<button class="put_cont_tel">-> 1</button>
				<button class="put_firm_tel">-> 2</button>
				<input type="text" class="new_phone" value="" />
				<button class="clearq" data-what="space">space</button>
				<button class="clearq" data-what="defis">-</button>
				<button class="clearq" data-what="dot">. ,</button>
				<button class="clearq" data-what="bracket">( ) [ ]</button>
				<button class="clearq" data-what="char">все буквы</button>
				<button class="clearq" data-what="all">все кроме цифр</button>
				<button class="clearq" data-what="reset">X</button>
			</div>
			<div class="workingArea_row">
				<button class="put_cont_tel">-> 1</button>
				<button class="put_firm_tel">-> 2</button>
				<input type="text" class="new_phone" value="" />
				<button class="clearq" data-what="space">space</button>
				<button class="clearq" data-what="defis">-</button>
				<button class="clearq" data-what="dot">. ,</button>
				<button class="clearq" data-what="bracket">( ) [ ]</button>
				<button class="clearq" data-what="char">все буквы</button>
				<button class="clearq" data-what="all">все кроме цифр</button>
				<button class="clearq" data-what="reset">X</button>
			</div>
			<div class="workingArea_row">
				<button class="put_cont_tel">-> 1</button>
				<button class="put_firm_tel">-> 2</button>
				<input type="text" class="new_phone" value="" />
				<button class="clearq" data-what="space">space</button>
				<button class="clearq" data-what="defis">-</button>
				<button class="clearq" data-what="dot">. ,</button>
				<button class="clearq" data-what="bracket">( ) [ ]</button>
				<button class="clearq" data-what="char">все буквы</button>
				<button class="clearq" data-what="all">все кроме цифр</button>
				<button class="clearq" data-what="reset">X</button>
			</div>
			
			<button id="savePhones">Сохранить все телефоны</button>
		</div>
	</div>
</div>