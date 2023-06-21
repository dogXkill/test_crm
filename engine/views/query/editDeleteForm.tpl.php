
<div class="commentDeleteForm">
<span id='del_id_row' style='display:none;'></span>
    <div style="width:400px;height:70px;overflow:auto; padding: 3px; background-color: #F2F2F2; font-size:13px";><?echo $query['note']; ?></div>
	<div class="commentDeleteForm_input" >
      <!--<select id='select_delete'>-->
	  <?php
		$mas_tip_delete=array(
		0=>'слишком дорого (нашли дешевле)',
		1=>'не устроили условия (сроки, доставка, договор)',
		2=>'клиент разместил у нас другой заказ (это дубль)',
		3=>'мы не смогли выполнить/доставить заказ вовремя',
		4=>'у клиента пропала потребность',
		5=>'нужной продукции не было в наличии',
		6=>'технически невыполнимый заказ',
		7=>'до клиента невозможно дозвониться',
		8=>'иная причина'
		);
		//функция перемешивания массива
		function shuffle_assoc($array)
		{
				$shuffled_array = array();
				$shuffled_keys = array_keys($array);
				shuffle($shuffled_keys);
				foreach ( $shuffled_keys AS $shuffled_key ) {
					$shuffled_array[  $shuffled_key  ] = $array[  $shuffled_key  ];
				} 
				return $shuffled_array;
		}
		$mas_tip_delete=shuffle_assoc($mas_tip_delete);
		//foreach($mas_tip_delete as $k =>$value){
			//echo "<option value='{$k}'>{$value}</option>";
		//}
	  ?>
	  <!--</select>-->
	  <div class='list_tip_delete'>
		<div class='btn_tip_delete'>
			<span>Выберите причину</span>
			<i class="fa fa-arrow-down"></i>
		</div>
		<div class='body_tip_delete' id='select_delete'>
			<?php
				foreach($mas_tip_delete as $k =>$value){
			echo "<div value='{$k}'>{$value}</div>";
		}
			?>
		</div>
	  </div>
	  </br>
	  <textarea id='comment_delete'></textarea>
	</div>

	<div class="commentDeleteForm_result"></div>
	
	<div class="commentDeleteForm_actions">
		<span class="saveDeleteAction"><i class="fa fa-floppy-o"></i><span>Удалить</span></span>
	</div>
</div>