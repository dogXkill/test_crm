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
      `title` tinytext COMMENT '�������� ������',
      `user_id` int(10) unsigned default '0' COMMENT '��������',
      `num_ord` int(10) unsigned default '0' COMMENT '����� ������',
      `tiraz` int(10) unsigned default '0' COMMENT '����� �����',
      `dat_ord` datetime default '0000-00-00 00:00:00' COMMENT '���� ������',
      `limit_per_sign` tinyint(1) unsigned default '0' COMMENT '���� +(���) ��� -',
      `limit_per` tinytext COMMENT '������� �������/�������',
      `paper_width` tinytext COMMENT '������ ������',
      `paper_height` tinytext COMMENT '������ ������',
      `paper_side` tinytext COMMENT '������ ���',
      `paper_color_ext` tinytext COMMENT '������ ���� �������',
      `paper_color_inn` tinytext COMMENT '������ ���� ������',
      `paper_density` tinytext COMMENT '������ ���������',
      `paper_name` tinytext COMMENT '������ ��������',
      `paper_dat_deliv` date default '0000-00-00' COMMENT '����������� ���� �������� ������ �� ������������',
      `paper_press` tinytext COMMENT '������ ����������',
      `paper_suppl` tinytext COMMENT '��������� ������',
      `paper_num_list` tinyint(2) unsigned default '1' COMMENT '�� �������� ������ ����������',
      `paper_list_typ` text COMMENT '������ ����� �� ������, ����������, ������',
      `lamination_tp` tinyint(10) unsigned NOT NULL default '0' COMMENT '��������� ���',
      `lamination_inn` tinyint(1) unsigned default '0' COMMENT '��������� ������ �������',
      `lamination_ext` tinyint(1) unsigned default '0' COMMENT '��������� ������� �������',
      `stamp` tinyint(1) unsigned default '0' COMMENT '�������� ��� ������ �����',
      `stamp_width` tinytext COMMENT '�������� ������',
      `stamp_height` tinytext COMMENT '�������� ������',
      `stamp_color` tinytext COMMENT '�������� ����',
      `stamp_typ` tinyint(4) default '0' COMMENT '�������� ��� (����������, ������)',
      `stamp_foil_name` tinytext COMMENT '�������� ������',
      `stamp_indent_bott` tinytext COMMENT '������ �� ���',
      `stamp_indent_right` tinytext COMMENT '������ ������',
      `hand_mater_tp` tinyint(2) unsigned default '0' COMMENT '�������� ����� ������',
      `hand_mater_txt` tinytext COMMENT '�������� ����� ������',
      `hand_mount_tp` tinyint(2) unsigned default '0' COMMENT '��������� ����� ������',
      `hand_mount_color` tinytext COMMENT '��������� ����',
      `hand_mount_txt` tinytext COMMENT '��������� ����� ������',
      `hand_thick` tinytext COMMENT '������� �����',
      `hand_color` tinytext COMMENT '���� �����',
      `hand_length` tinytext COMMENT '������� ����� ����� (��� ����� �������)',
      `hand_mater_scotch` tinyint(1) unsigned default '0' COMMENT '�������� ��� ���������� ������ �������',
      `hand_mater_scotch_tx` tinytext COMMENT '�������� ��� ���������� ������ ����� ��������',
      `hand_mater_glue` tinyint(1) unsigned default '0' COMMENT '�������� ��� ���������� ������ ���� ������� �������',
      `hand_mater_glue_tx` tinytext COMMENT '�������� ��� ���������� ������ ���� ������� ��������',
      `pikalo_on` tinyint(1) unsigned default '0' COMMENT '������ (0 - ���, 1- ����)',
      `pikalo_diam_hol` tinytext COMMENT '������� ��������� (� ������ � ���)',
      `pikalo_color` tinytext COMMENT '������ ����',
      `strengt_bot` tinyint(1) unsigned default '1' COMMENT '���������� ��� �����',
      `strengt_bot_col` tinytext COMMENT '���������� ��� ����',
      `strengt_side` tinyint(1) unsigned default '1' COMMENT '���������� ��� �����',
      `strengt_oth_tx` tinytext COMMENT '���������� ������ ������ �����',
      `packing_korob` tinyint(1) unsigned default '0' COMMENT '�������� ������� �����',
      `packing_sel` tinyint(3) unsigned default '0' COMMENT '������ ��������, �������(1), ������(2), ������(3)',
      `packing_other` tinytext COMMENT '�������� ������ ��� ����� ��',
      `mark_of_company_tp` tinyint(2) unsigned default '0' COMMENT '���������� � ��������� �� ����� ������',
      `mark_of_company` tinytext COMMENT '���������� � ��������� �� ����� �����',
      `assperm_1` tinyint(1) unsigned default '0' COMMENT '������ ��� �������� ������ - ��� ������� ����� (�����)',
      `assperm_2` tinyint(1) unsigned default '0' COMMENT '������ ��� �������� ������ - ��� �������� ����� (�����)',
      `assperm_3` tinyint(1) unsigned default '0' COMMENT '������ ��� �������� ������ - ��������� �������� (�����)',
      `assperm_4` tinyint(1) unsigned default '0' COMMENT '������ ��� �������� ������ - ��������� ��� (�����)',
      `delivery_tp` tinyint(2) unsigned default '0' COMMENT '�������� ������',
      `delivery_address` text COMMENT '�������� �����',
      `contact_man` tinytext COMMENT '���������� ����',
      `special_requir` text COMMENT '������ ����������',
      `rate` tinytext COMMENT '�����',
      `exec_on` tinyint(1) unsigned default '0' COMMENT '��������',
      `exec_dat` datetime default '0000-00-00 00:00:00' COMMENT '���� ����������',
      PRIMARY KEY  (`uid`)
    ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=cp1251 COMMENT='������';
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
      `apl_id` int(10) unsigned default '0' COMMENT '�� ������',
      `num` int(10) unsigned default '1' COMMENT '����� �.�.',
      `val_nums` int(10) unsigned default '0' COMMENT '����������',
      `val_tx` tinytext COMMENT '�...',
      PRIMARY KEY  (`uid`)
    ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=cp1251 COMMENT='������� �������� ������ ������ ��������';";
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
        ���� ���������<br /><br />
        <a href="/">��������� �� �������</a>
      </td>
    </tr>
  </table>

</body>
</html>
