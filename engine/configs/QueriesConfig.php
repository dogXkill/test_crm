<?php

abstract class QueriesConfig {
	
	public static $defaultPerPage = 80;
	public static $perPage = array(20, 60, 80, 100, 200, 300, 2000, 5000);
	
	public static $filter_orderType = array(
		1 => 'Заказ',
		2 => 'Магазин',
		3 => 'Готовые с лого',
		0 => 'Остальные'
	);
	
	public static $filter_deliveryType = array(
		1  => 'Самовывоз',
		12 => 'Из шоурума',
		2  => 'По Мск',
		8  => 'До ТК',
		5  => 'СДЭК',
		15 => 'Дел. линии',
		3  => 'Срочная',
		0  => 'Остальные'
	);
	
	public static $filter_paymentType = array(
		1 => 'Наличные',
		2 => 'Безнал по счету',
		3 => 'По квитанции',
		4 => 'Карта',
		0 => 'Прочее'
	);
	
	public static $sortValues = array(
		'date-desc' => array(
			'field' => 'date',
			'order' => 'desc'
		),
		'date-asc' => array(
			'field' => 'date',
			'order' => 'asc'
		),
		'summ-desc' => array(
			'field' => 'summ',
			'order' => 'desc'
		),
		'summ-asc' => array(
			'field' => 'summ',
			'order' => 'asc'
		),
		'debt-desc' => array(
			'field' => 'debt',
			'order' => 'desc'
		),
		'debt-asc' => array(
			'field' => 'debt',
			'order' => 'asc'
		),
	);
	
	public static $filter_deleted = array(
		1 => 'Удаленные',
		2 => 'На удаление'
	);
	public static $filter_tip_sdelki=array(
		1=>'Наше производство',
		2=>'Перезаказ',
		3=>'Доставка'
	);
}
