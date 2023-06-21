<?php
define("_DB", "intranet");
define("_User", "root");
define("_Passwd", "");
define("_HostName", "localhost");



if (!mysql_connect(_HostName, _User, _Passwd)){
	error(__FILE__, __LINE__, mysql_error());
	exit;
}


mysql_query ("set character_set_client='cp1251'");
mysql_query ("set character_set_results='cp1251'");
mysql_query ("set collation_connection='cp1251_general_ci'");

mysql_select_db(_DB);

//$r = mysql_query("SELECT * FROM applications WHERE 0");
//if (!$r) {
  $query = "DROP TABLE IF EXISTS `applications`;";
  mysql_query($query);

  $query = "
    CREATE TABLE `applications` (
      `uid` int(10) unsigned NOT NULL auto_increment,
      `title` tinytext COMMENT 'название заказа',
      `user_id` int(10) unsigned default '0' COMMENT 'менеджер',
      `num_ord` int(10) unsigned default '0' COMMENT 'номер заказа',
      `tiraz` int(10) unsigned default '0' COMMENT 'Общий тираж',
      `dat_ord` datetime default '0000-00-00 00:00:00' COMMENT 'дата заказа',
      `limit_per_sign` tinyint(1) unsigned default '0' COMMENT 'знак +(умл) или -',
      `limit_per` tinytext COMMENT 'Пределы перекат/недокат',
      `paper_width` tinytext COMMENT 'бумага ширина',
      `paper_height` tinytext COMMENT 'бумага высота',
      `paper_side` tinytext COMMENT 'бумага бок',
      `paper_color_ext` tinytext COMMENT 'бумага цвет снаружи',
      `paper_color_inn` tinytext COMMENT 'бумага цвет внутри',
      `paper_density` tinytext COMMENT 'бумага плотность',
      `paper_name` tinytext COMMENT 'бумага название',
      `paper_dat_deliv` date default '0000-00-00' COMMENT 'планируемая дата поставки листов на производство',
      `paper_press` tinytext COMMENT 'Бумага типография',
      `paper_suppl` tinytext COMMENT 'поставщик бумаги',
      `paper_num_list` tinyint(2) unsigned default '1' COMMENT 'Из скольких листов собирается',
      `paper_list_typ` text COMMENT 'бумага листы на пакете, одинаковые, разные',
      `lamination_tp` tinyint(10) unsigned NOT NULL default '0' COMMENT 'ламинация тип',
      `lamination_inn` tinyint(1) unsigned default '0' COMMENT 'ламинация внутри галочка',
      `lamination_ext` tinyint(1) unsigned default '0' COMMENT 'ламинация снаружи галочка',
      `stamp` tinyint(1) unsigned default '0' COMMENT 'тиснение тип список видов',
      `stamp_width` tinytext COMMENT 'тиснение ширина',
      `stamp_height` tinytext COMMENT 'тиснение высота',
      `stamp_color` tinytext COMMENT 'тиснение цвет',
      `stamp_typ` tinyint(4) default '0' COMMENT 'тиснение тип (одинаковое, разное)',
      `stamp_foil_name` tinytext COMMENT 'название фольги',
      `stamp_indent_bott` tinytext COMMENT 'отступ от дна',
      `stamp_indent_right` tinytext COMMENT 'отступ справа',
      `hand_mater_tp` tinyint(2) unsigned default '0' COMMENT 'Материал ручек селект',
      `hand_mater_txt` tinytext COMMENT 'Материал ручек другой',
      `hand_mount_tp` tinyint(2) unsigned default '0' COMMENT 'крепление ручек селект',
      `hand_mount_color` tinytext COMMENT 'крепление цвет',
      `hand_mount_txt` tinytext COMMENT 'крепление ручек другое',
      `hand_thick` tinytext COMMENT 'толщина ручек',
      `hand_color` tinytext COMMENT 'цвет ручек',
      `hand_length` tinytext COMMENT 'Видимая длина ручек (без учета узелков)',
      `hand_mater_scotch` tinyint(1) unsigned default '0' COMMENT 'Материал для скрепления пакета галочка',
      `hand_mater_scotch_tx` tinytext COMMENT 'Материал для скрепления пакета скотч значение',
      `hand_mater_glue` tinyint(1) unsigned default '0' COMMENT 'Материал для скрепления пакета клей горячий галочка',
      `hand_mater_glue_tx` tinytext COMMENT 'Материал для скрепления пакета клей горячий значение',
      `pikalo_on` tinyint(1) unsigned default '0' COMMENT 'пикало (0 - нет, 1- есть)',
      `pikalo_diam_hol` tinytext COMMENT 'диаметр отверстий (с пикало и без)',
      `pikalo_color` tinytext COMMENT 'пикало цвет',
      `strengt_bot` tinyint(1) unsigned default '1' COMMENT 'укрепление дно галка',
      `strengt_bot_col` tinytext COMMENT 'укрепление дно цвет',
      `strengt_side` tinyint(1) unsigned default '1' COMMENT 'укрепление бок галка',
      `strengt_oth_tx` tinytext COMMENT 'укрепление пакета другое текст',
      `packing_korob` tinyint(1) unsigned default '0' COMMENT 'Упаковка коробки галка',
      `packing_sel` tinyint(3) unsigned default '0' COMMENT 'селект упаковки, коробки(1), пленка(2), другая(3)',
      `packing_other` tinytext COMMENT 'упаковка другое или колич шт',
      `mark_of_company_tp` tinyint(2) unsigned default '0' COMMENT 'Маркировка и накладные от имени селект',
      `mark_of_company` tinytext COMMENT 'Маркировка и накладные от имени текст',
      `assperm_1` tinyint(1) unsigned default '0' COMMENT 'Сборка раз решается только - Цех дневная смена (галка)',
      `assperm_2` tinyint(1) unsigned default '0' COMMENT 'Сборка раз решается только - Цех вечерняя смена (галка)',
      `assperm_3` tinyint(1) unsigned default '0' COMMENT 'Сборка раз решается только - Надомники надежные (галка)',
      `assperm_4` tinyint(1) unsigned default '0' COMMENT 'Сборка раз решается только - Надомники все (галка)',
      `delivery_tp` tinyint(2) unsigned default '0' COMMENT 'доставка селект',
      `delivery_address` text COMMENT 'Доставка адрес',
      `contact_man` tinytext COMMENT 'Контактное лицо',
      `special_requir` text COMMENT 'Особые требования',
      `rate` tinytext COMMENT 'тариф',
      `exec_on` tinyint(1) unsigned default '0' COMMENT 'выполнен',
      `exec_dat` datetime default '0000-00-00 00:00:00' COMMENT 'даты выполнения',
      PRIMARY KEY  (`uid`)
    ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=cp1251 COMMENT='Заявки';
  ";
  mysql_query($query);
  //echo $query;
//}
//$r = mysql_query("SELECT * FROM applications_shipping_list WHERE 0");
//if (!$r) {

  $query = "DROP TABLE IF EXISTS `applications_shipping_list`;";
  mysql_query($query);

  $query = "
    CREATE TABLE `applications_shipping_list` (
      `uid` int(10) unsigned NOT NULL auto_increment,
      `apl_id` int(10) unsigned default '0' COMMENT 'ид заявки',
      `num` int(10) unsigned default '1' COMMENT 'номер п.п.',
      `val_nums` int(10) unsigned default '0' COMMENT 'количество',
      `val_tx` tinytext COMMENT 'к...',
      PRIMARY KEY  (`uid`)
    ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=cp1251 COMMENT='Порядок отгрузки заказа список значений';";
  mysql_query($query);
  echo mysql_error();
//}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Hello!</title>
</head>

<body>

  <table width="100%" height="100" cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td align="center" valign="middle">
        База обновлена<br /><br />
        <a href="/">вернуться на главную</a>
      </td>
    </tr>
  </table>

</body>
</html>
