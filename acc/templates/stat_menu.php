<? $type = $_GET["type"];  ?>
<table width=1000 align=center><tr>
  <td>

  	<?if($tpacc) {?>

  <a href="stat_shop.php?type=popular_art_cost" class="task_link<?if($type == "popular_art_cost"){echo "_bold";}?>">продажи</a> &nbsp;&nbsp; |  &nbsp;&nbsp;
  <?}?>
  <a href="stat_shop.php?type=shop_history" class="task_link<?if($type == "shop_history"){echo "_bold";}?>">история продаж</a>  &nbsp;&nbsp; |  &nbsp;&nbsp;

  </td>
</tr></table>
