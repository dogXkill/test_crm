<html>
<head>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8" />
    <style type="text/css">
      <!--  
      body,table,td {font-family: Arial, Helvetica, sans-serif;font-size: 11px;color:#000000;}  
      body {padding:0;padding-left:10px;margin:5px;background-color:#FFFFFF;}
      -->
    </style>
</head>
<body>
<strong>Уважаемый(ая) <?= ($name . ' ' . $surname); ?>!</strong><br><br>
по заказу <?= $client; ?> за номером: <a href="<?= $crm_url ?>/acc/query/query_send.php?show=<?= $queryId; ?>"><strong><?= $queryId; ?></strong></a>
произошло следующее действие<br><br>
удален ранее внесенный платеж на сумму <strong><?= $summ; ?></strong> по платежке номер <strong><?= ($number ? $number : 'НЕ УКАЗАН'); ?></strong> от <strong><?= $date; ?></strong> зарегистрированный ранее за номером <?= $paymentId; ?>
<br><br>
Задолженность клиента по данному проекту составляет <?= $dolg; ?> руб.
</body>
</html>