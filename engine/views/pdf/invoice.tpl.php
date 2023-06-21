<style>
  table {
    width: 100%;
  }
  table, tr, th, td {
    border: 2px solid #000;
    border-collapse: collapse;
  }
  table.no-border, table.no-border tr, table.no-border td {
    border: none;
  }
</style>
<table style="width: 100%;" cellpadding="2">
    <tbody>
    <tr>
        <td colspan="2" rowspan="2" style="width: 400px;">АО "ТИНЬКОФФ БАНК" г. Москва<br>Банк получателя</td>
        <td style="width: 50px">БИК</td>
        <td style="width: 190px">044525974</td>
    </tr>
    <tr>
        <td style="width: 50px">Сч. №</td>
        <td>30101810145250000974</td>
    </tr>
    <tr>
        <td>ИНН 501703416801</td>
        <td>КПП</td>
        <td rowspan="2" style="width: 50px">Сч. №</td>
        <td rowspan="2">40802810900000051534</td>
    </tr>
    <tr>
        <td colspan="2">ИП Москвин Павел Дмитриевич<br>Получатель</td>
    </tr>
    </tbody>
</table>
<br><br>
<div style="font-size: 16px; font-weight: bold; border-bottom: 2px solid #000;">Счет на оплату № <?= $orderId ?> от <?= $dateRus ?> г.</div>
<table cellspacing="0" cellpadding="0" class="no-border">
    <tr>
        <td style="height: 5px;"></td>
    </tr>
    <tr>
        <td style="width: 180px;">Поставщик (Исполнитель):</td>
        <td style="width: 458px; font-weight: bold;">ИП Москвин Павел Дмитриевич, ИНН 501703416801, 143530, Московская обл, г Истра, г Дедовск, тер. СНТ Садовод-1, д. 70, тел.: +7 (495) 150-78-12</td>
    </tr>
</table>
<table cellspacing="0" cellpadding="0" class="no-border">
    <tr>
        <td style="height: 10px;"></td>
    </tr>
    <tr>
        <td style="width: 180px;">Покупатель(Заказчик):</td>
        <td style="width: 458px; font-weight: bold;"><?= $clientName ?></td>
    </tr>
</table>
<table cellspacing="0" cellpadding="0" class="no-border">
    <tr>
        <td style="height: 10px;"></td>
    </tr>
    <tr>
        <td style="width: 180px;">Основание:</td>
        <td style="width: 458px; font-weight: bold;">Счет <?= $orderId ?> от <?= $dateFormatted ?></td>
    </tr>
    <tr>
        <td style="height: 10px;"></td>
    </tr>
</table>

<table width="100%" cellpadding="6">
    <tr>
        <th style="width: 30px"><div>№</div></th>
        <th style="width: 270px"><div>Товары (работы, услуги)</div></th>
        <th style="width: 60px"><div>Кол-во</div></th>
        <th style="width: 70px"><div>Ед.</div></th>
        <th><div>Цена</div></th>
        <th><div>Сумма</div></th>
    </tr>
    <?php foreach ($order as $index => $item) { ?>
    <tr>
        <td><?= $index + 1 ?></td>
        <td><?= $item['title'] ?></td>
        <td><?= $item['quantity'] ?></td>
        <td><?= $item['unit'] ?></td>
        <td><?= number_format($item['price'], 2, ',', ' ') ?></td>
        <td><?= number_format($item['sum'], 2, ',', ' ') ?></td>
    </tr>
    <?php } ?>
</table>
<table class="no-border" style="font-weight: bold;">
    <tr>
        <td colspan="2" style="height: 3px; line-height: 10px;"></td>
    </tr>
    <tr>
        <td align="right" style="width: 536px; line-height: 120%;">Итого:</td>
        <td style="width: 106px;"><?= $sumFormatted ?></td>
    </tr>
    <tr>
        <td align="right" style="width: 536px; line-height: 120%;">Без налога (НДС)</td>
        <td style="width: 106px;">-</td>
    </tr>
    <tr>
        <td align="right" style="width: 536px; line-height: 120%;">Всего к оплате:</td>
        <td style="width: 106px;"><?= $sumFormatted ?></td>
    </tr>
</table>

<div>Всего наименований <?= $lines ?>, на сумму <?= $sumFormatted ?> руб.</div>
<div style="font-weight: bold; line-height: 50%;"><?= $stringSum ?></div>
<div>Оплатить не позднее <?= $booking_till ?></div>
<div>Оплата данного счета означает согласие с условиями поставки товара.<br>Уведомление об оплате обязательно, в противном случае не гарантируется наличие товара на складе.<br>Товар отпускается по факту прихода денег на р/с Поставщика, самовывозом, при наличии доверенности и паспорта.</div>
<div style="border-top: 2px solid #000;"></div>
<table class="no-border">
    <tr>
        <td style="width: 150px"><div style="line-height: 140%;">Предприниматель</div></td>
        <td width="385px" style="border-bottom: 2px solid #000; text-align: center;">
            <img src="/i/pdf/signt.png" width="100" height="30" alt=""/>
        </td>
        <td align="right" style="width: 105px; border-bottom: 2px solid #000;">Москвин П. Д.</td>
    </tr>
</table>
<table class="no-border" style="width: 180px;">
    <tr>
        <td style="width: 30px;"></td>
        <td style="width: 150px;"><img src="/i/pdf/printip.png" width="150" alt=""/></td>
    </tr>
</table>