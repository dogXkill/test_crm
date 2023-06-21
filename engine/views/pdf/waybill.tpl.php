<style>
  .tac {
    text-align: center;
  }
  .tar {
    text-align: right;
  }
  .text-bottom-border {
    border-bottom: 1px solid #000;
  }
  .text-link {
    width: 16px;
    text-align: center;
  }
  .indent {
    width: 5px;
  }
  .right-notice {
    text-align: right;
    font-size: 7px;
    line-height: 120%;
    letter-spacing: -0.4px;
  }
  /* первая колонка таблицы */
  .first-column {
    width: 75px;
    border-right: 2px solid #000;
  }
  /* вторая колонка таблицы */
  .second-column {
    width: 470px;
  }
  .second-column td {
    line-height: 145%;
  }
  .second-column .title {
    width: 155px;
  }
  .second-column .border-field {
    width: 290px;
    border-bottom: 1px solid #000;
  }
  /* третья колонка таблицы */
  .third-column {
    width: 450px;
  }
  .third-column td {
    line-height: 145%;
  }
  .third-column .title {
    width: 125px;
  }
  .third-column .border-field {
    width: 270px;
    border-bottom: 1px solid #000;
  }
  /* главная таблица */
  .main-table-cell {
    border: 1px solid #000;
  }
  .main-table-line {
    line-height: 130%;
  }
  .main-table-cell-padding {
    height: 20px;
    line-height: 20px;
  }
  .main-table-cell-padding3 {
    height: 3px;
    line-height: 3px;
  }
  .main-table-cell-padding5 {
    height: 5px;
    line-height: 5px;
  }
  .main-table-cell-padding10 {
    height: 10px;
    line-height: 10px;
  }
</style>
<table cellpadding="1" cellspacing="0">
    <tbody>
    <tr>
        <td rowspan="2" class="first-column" style="line-height: 130%;"><div style="line-height: 120%;">Универсальный передаточный документ</div></td>
        <td colspan="7">
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <td style="width: 100px;"><div style="line-height: 100%;">Счет-фактура №</div></td>
                    <td style="width: 100px;"><div class="text-bottom-border tac" style="line-height: 100%;"><?= $orderId ?></div></td><!-- номер счет фактуры -->
                    <td style="width: 15px;"><div class="tac" style="line-height: 100%;">от</div></td>
                    <td style="width: 130px;"><div class="text-bottom-border tac" style="line-height: 100%;"><?= $dateRus ?> г.</div></td><!-- дата выставления накладной -->
                    <td class="indent"></td>
                    <td class="text-link"><div style="line-height: 100%;">(1)</div></td>
                </tr>
            </table>
        </td>
        <td colspan="8" rowspan="2" style="width: 485px;">
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <td style="width: 60px;"></td>
                    <td style="width: 400px;"><div class="right-notice">Приложение № 1 к постановлению Правительства Российской Федерации от 26 декабря 2011 г. № 1137<br/>(в редакции постановления Правительства Российской Федерации от 2 апреля 2021 г. № 534)</div></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="7">
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <td style="width: 100px;"><div style="line-height: 80%;">Исправление №</div></td>
                    <td style="width: 100px;"><div class="text-bottom-border tac" style="line-height: 80%;">--</div></td>
                    <td style="width: 15px;"><div class="tac" style="line-height: 80%;">от</div></td>
                    <td style="width: 130px;"><div class="text-bottom-border tac" style="line-height: 80%;">--</div></td>
                    <td class="indent"></td>
                    <td class="text-link"><div style="line-height: 80%;">(1а)</div></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td rowspan="3" class="first-column">
            <br/><br/><br/>
            <table cellpadding="2" class="first-column">
                <tr>
                    <td style="width: 35px;"><div>Статус:</div></td>
                    <td style="text-align: center; border: 2px solid #000; width: 15px;"><div>2</div></td>
                </tr>
            </table>
        </td>

        <td colspan="7" class="second-column">
            <table cellpadding="0" cellspacing="0" class="second-column">
                <tr>
                    <td class="title"><div style="font-weight: bold;">Продавец</div></td>
                    <td class="border-field"><div>ИП Москвин Павел Дмитриевич</div></td>
                    <td class="indent"></td>
                    <td class="text-link"><div>(2)</div></td>
                </tr>
            </table>
        </td>
        <td colspan="7" class="third-column">
            <table cellpadding="0" cellspacing="0" class="third-column">
                <tr>
                    <td class="title"><div style="font-weight: bold;">Покупатель</div></td>
                    <td class="border-field"><div><?= $clientName ?></div></td><!-- наименование покупателя -->
                    <td class="indent"></td>
                    <td class="text-link"><div>(6)</div></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="7" class="second-column">
            <table cellpadding="0" cellspacing="0" class="second-column">
                <tr>
                    <td class="title"><div>Адрес</div></td>
                    <td class="border-field"><div>143530, Московская обл, г Истра, г Дедовск, тер. СНТ Садовод-1, д. 70</div></td>
                    <td class="indent"></td>
                    <td class="text-link"><div>(2а)</div></td>
                </tr>
            </table>
        </td>
        <td colspan="7" class="third-column">
            <table cellpadding="0" cellspacing="0" class="third-column">
                <tr>
                    <td class="title"><div>Адрес:</div></td>
                    <td class="border-field"><div><?= $clientAddress ?></div></td><!-- адрес покупателя -->
                    <td class="indent"></td>
                    <td class="text-link"><div>(6а)</div></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="7" class="second-column">
            <table cellpadding="0" cellspacing="0" class="second-column">
                <tr>
                    <td class="title"><div>ИНН/КПП продавца:</div></td>
                    <td class="border-field"><div>501703416801</div></td>
                    <td class="indent"></td>
                    <td class="text-link"><div>(2б)</div></td>
                </tr>
            </table>
        </td>
        <td colspan="7" class="third-column">
            <table cellpadding="0" cellspacing="0" class="third-column">
                <tr>
                    <td class="title"><div>ИНН/КПП покупателя:</div></td>
                    <td class="border-field"><div><?= $waybillBankData ?></div></td><!-- ИНН/КПП покупателя -->
                    <td class="indent"></td>
                    <td class="text-link"><div>(6б)</div></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td rowspan="4" class="first-column" style="font-size: 7px; line-height: 120%; letter-spacing: -0.4px;">1 – счет-фактура и передаточный документ (акт)<br>2 – передаточный документ (акт)</td>

        <td colspan="7" class="second-column">
            <table cellpadding="0" cellspacing="0" class="second-column">
                <tr>
                    <td class="title"><div>Грузоотправитель и его адрес:</div></td>
                    <td class="border-field"><div>он же</div></td>
                    <td class="indent"></td>
                    <td class="text-link"><div>(3)</div></td>
                </tr>
            </table>
        </td>
        <td colspan="7" class="third-column">
            <table cellpadding="0" cellspacing="0" class="third-column">
                <tr>
                    <td class="title"><div>Валюта: наименование, код</div></td>
                    <td class="border-field"><div>Российский рубль, 643</div></td>
                    <td class="indent"></td>
                    <td class="text-link"><div>(7)</div></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="7" class="second-column">
            <table cellpadding="0" cellspacing="0" class="second-column">
                <tr>
                    <td class="title"><div>Грузополучатель и его адрес:</div></td>
                    <td class="border-field"><div><?= trim($clientAddress) ?: '' ?></div></td><!-- адрес покупателя -->
                    <td class="indent"></td>
                    <td class="text-link"><div>(4)</div></td>
                </tr>
            </table>
        </td>

        <td colspan="7" rowspan="2" class="third-column">
            <table cellpadding="0" cellspacing="0" class="third-column">
                <tr>
                    <td class="title" style="width: 205px;"><div>Идентификатор государственного контракта, договора (соглашения) (при наличии):</div></td>
                    <td class="border-field" style="width: 190px;"><div></div></td>
                    <td class="indent"></td>
                    <td class="text-link"><table cellpadding="0" cellspacing="0" class="text-link">
                            <tr>
                                <td style="height: 5px;"></td>
                            </tr>
                            <tr>
                                <td class="text-link"><div>(8)</div></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="7" class="second-column">
            <table cellpadding="0" cellspacing="0" class="second-column">
                <tr>
                    <td class="title"><div>К платежно-расчетному документу №</div></td>
                    <td class="border-field"><div>-- от --</div></td>
                    <td class="indent"></td>
                    <td class="text-link"><div>(5)</div></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="7" class="second-column">
            <table cellpadding="0" cellspacing="0" class="second-column">
                <tr>
                    <td class="title"><div>Документ об отгрузке</div></td>
                    <td class="border-field"><div>№ п/п 1-<?= $lines ?> № <?= $orderId ?> от <?= $dateFormatted ?> г.</div></td>
                    <td class="indent"></td>
                    <td class="text-link"><div>(5а)</div></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="first-column" style="line-height: 20%;"></td>
    </tr>
    <tr>
        <td rowspan="2" class="main-table-cell first-column" style="width: 75px;"><table cellpadding="0" cellspacing="0" style="width: 75px;">
                <tr>
                    <td class="main-table-cell-padding"></td>
                </tr>
                <tr>
                    <td><div class="main-table-line tac">Код товара/<br/>работ, услуг</div></td>
                </tr>
            </table>
        </td>
        <td rowspan="2" class="main-table-cell" style="width: 20px;"><table cellpadding="0" cellspacing="0" style="width: 20px;">
                <tr>
                    <td class="main-table-cell-padding"></td>
                </tr>
                <tr>
                    <td style="width: 20px;"><div class="main-table-line tac">№<br/>п/п</div></td>
                </tr>
            </table>
        </td>
        <td rowspan="2" class="main-table-cell" style="width: 210px;"><table cellpadding="0" cellspacing="0" style="width: 210px;">
                <tr>
                    <td class="main-table-cell-padding"></td>
                </tr>
                <tr>
                    <td style="width: 210px;"><div class="main-table-line tac">Наименование товара (описание выполненных работ, оказанных услуг), имущественного права</div></td>
                </tr>
            </table>
        </td>
        <td rowspan="2" class="main-table-cell" style="width: 29px;"><table cellpadding="0" cellspacing="0" style="width: 29px;">
                <tr>
                    <td class="main-table-cell-padding"></td>
                </tr>
                <tr>
                    <td style="width: 29px;"><div class="main-table-line tac">Код<br/>вида<br/>товара</div></td>
                </tr>
            </table>
        </td>
        <td colspan="2" class="main-table-cell" style="width: 65px;"><table cellpadding="0" cellspacing="0" style="width: 65px;">
                <tr>
                    <td class="main-table-cell-padding3"></td>
                </tr>
                <tr>
                    <td style="width: 65px;"><div class="main-table-line tac">Единица<br>измерения</div></td>
                </tr>
            </table>
        </td>
        <td rowspan="2" class="main-table-cell" style="width: 42px;"><table cellpadding="0" cellspacing="0" style="width: 42px;">
                <tr>
                    <td class="main-table-cell-padding"></td>
                </tr>
                <tr>
                    <td style="width: 42px;"><div class="main-table-line tac">Коли-<br/>чество<br>(объем)</div></td>
                </tr>
            </table>
        </td>
        <td rowspan="2" class="main-table-cell" style="width: 58px;"><table cellpadding="0" cellspacing="0" style="width: 58px;">
                <tr>
                    <td class="main-table-cell-padding"></td>
                </tr>
                <tr>
                    <td style="width: 58px;"><div class="main-table-line tac">Цена (тариф)<br/>за<br/>единицу<br/>измерения</div></td>
                </tr>
            </table>
        </td>
        <td rowspan="2" class="main-table-cell" style="width: 70px;"><table cellpadding="0" cellspacing="0" style="width: 70px;">
                <tr>
                    <td class="main-table-cell-padding"></td>
                </tr>
                <tr>
                    <td style="width: 70px;"><div class="main-table-line tac">Стоимость<br/>товаров<br/>(работ, услуг),<br/>имущественных<br/>прав без<br/>налога - всего</div></td>
                </tr>
            </table></td>
        <td rowspan="2" class="main-table-cell" style="width: 50px;"><table cellpadding="0" cellspacing="0" style="width: 50px;">
                <tr>
                    <td class="main-table-cell-padding"></td>
                </tr>
                <tr>
                    <td style="width: 50px;"><div class="main-table-line tac">В том<br/>числе<br/>сумма<br/>акциза</div></td>
                </tr>
            </table>
        </td>
        <td rowspan="2" class="main-table-cell" style="width: 50px;"><table cellpadding="0" cellspacing="0" style="width: 50px;">
                <tr>
                    <td class="main-table-cell-padding"></td>
                </tr>
                <tr>
                    <td style="width: 50px;"><div class="main-table-line tac">Налоговая<br/>ставка</div></td>
                </tr>
            </table>
        </td>
        <td rowspan="2" class="main-table-cell" style="width: 65px;"><table cellpadding="0" cellspacing="0" style="width: 65px;">
                <tr>
                    <td class="main-table-cell-padding"></td>
                </tr>
                <tr>
                    <td style="width: 65px;"><div class="main-table-line tac">Сумма налога,<br/>предъяв-<br/>ляемая<br/>покупателю</div></td>
                </tr>
            </table>
        </td>
        <td rowspan="2" class="main-table-cell" style="width: 70px;"><table cellpadding="0" cellspacing="0" style="width: 70px;">
                <tr>
                    <td class="main-table-cell-padding"></td>
                </tr>
                <tr>
                    <td style="width: 70px;"><div class="main-table-line tac">Стоимость<br/>товаров(работ,<br/>услуг),<br/>имущест-<br/>венных прав с<br/>налогом - всего</div></td>
                </tr>
            </table>
        </td>
        <td colspan="2" class="main-table-cell" style="width: 75px;"><table cellpadding="0" cellspacing="0" style="width: 75px;">
                <tr>
                    <td class="main-table-cell-padding3"></td>
                </tr>
                <tr>
                    <td style="width: 75px;"><div class="main-table-line tac">Страна<br/>происхождения<br/>товара</div></td>
                </tr>
            </table>
        </td>
        <td rowspan="2" class="main-table-cell" style="width: 85px;"><table cellpadding="0" cellspacing="0" style="width: 85px;">
                <tr>
                    <td class="main-table-cell-padding3"></td>
                </tr>
                <tr>
                    <td style="width: 85px;"><div class="main-table-line tac">Регистрационный<br/>номер декларации на<br/>товары или<br/>регистрационный<br/>номер партии товара,<br/>подлежащего<br/>прослеживаемости</div></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr class="main-table-line">
        <td class="main-table-cell" style="width: 25px;"><table cellpadding="0" cellspacing="0" style="width: 25px;">
                <tr>
                    <td class="main-table-cell-padding3"></td>
                </tr>
                <tr>
                    <td style="width: 25px;"><div class="main-table-line tac">код</div></td>
                </tr>
            </table>
        </td>
        <td class="main-table-cell" style="width: 40px;"><table cellpadding="0" cellspacing="0" style="width: 40px;">
                <tr>
                    <td class="main-table-cell-padding3"></td>
                </tr>
                <tr>
                    <td style="width: 40px;"><div class="main-table-line tac">условное<br/>обозна-<br/>чение<br/>(нацио-<br/>нальное)</div></td>
                </tr>
            </table>
        </td>
        <td class="main-table-cell" style="width: 25px;"><table cellpadding="0" cellspacing="0" style="width: 25px;">
                <tr>
                    <td class="main-table-cell-padding3"></td>
                </tr>
                <tr>
                    <td style="width: 25px;"><div class="main-table-line tac">циф-<br/>ро-<br/>вой<br/>код</div></td>
                </tr>
            </table>
        </td>
        <td class="main-table-cell" style="width: 50px;"><table cellpadding="0" cellspacing="0" style="width: 50px;">
                <tr>
                    <td class="main-table-cell-padding3"></td>
                </tr>
                <tr>
                    <td style="width: 50px;"><div class="main-table-line tac">краткое<br/>наименова-<br/>ние</div></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="first-column main-table-cell main-table-line tac"><div>А</div></td>
        <td class="main-table-cell main-table-line tac"><div>1</div></td>
        <td class="main-table-cell main-table-line tac"><div>1а</div></td>
        <td class="main-table-cell main-table-line tac"><div>1б</div></td>
        <td class="main-table-cell main-table-line tac"><div>2</div></td>
        <td class="main-table-cell main-table-line tac"><div>2а</div></td>
        <td class="main-table-cell main-table-line tac"><div>3</div></td>
        <td class="main-table-cell main-table-line tac"><div>4</div></td>
        <td class="main-table-cell main-table-line tac"><div>5</div></td>
        <td class="main-table-cell main-table-line tac"><div>6</div></td>
        <td class="main-table-cell main-table-line tac"><div>7</div></td>
        <td class="main-table-cell main-table-line tac"><div>8</div></td>
        <td class="main-table-cell main-table-line tac"><div>9</div></td>
        <td class="main-table-cell main-table-line tac"><div>10</div></td>
        <td class="main-table-cell main-table-line tac"><div>10а</div></td>
        <td class="main-table-cell main-table-line tac"><div>11</div></td>
    </tr>
    <!-- таблица с данными из БД -->
    <?php foreach ($order as $index => $item) { ?>
    <tr>
        <td class="main-table-cell first-column" style="width: 75px;"><table cellpadding="0" cellspacing="0" style="width: 75px;">
                <tr>
                    <td><div class="main-table-line"></div></td>
                </tr>
            </table>
        </td>
        <td class="main-table-cell" style="width: 20px;"><table cellpadding="0" cellspacing="0" style="width: 20px;">
                <tr>
                    <td style="width: 20px;"><div class="main-table-line tac"><?= $index + 1 ?></div></td>
                </tr>
            </table>
        </td>
        <td class="main-table-cell" style="width: 210px;"><table cellpadding="0" cellspacing="0" style="width: 210px;">
                <tr>
                    <td style="width: 210px;"><div class="main-table-line"><?= $item['title'] ?></div></td>
                </tr>
            </table>
        </td>
        <td class="main-table-cell" style="width: 29px;"><table cellpadding="0" cellspacing="0" style="width: 29px;">
                <tr>
                    <td style="width: 29px;"><div class="main-table-line tac">--</div></td>
                </tr>
            </table>
        </td>
        <td class="main-table-cell" style="width: 25px;"><table cellpadding="0" cellspacing="0" style="width: 25px;">
                <tr>
                    <td style="width: 25px;"><div class="main-table-line tac"><?= $item['unit'] === 'услуга' ? '--' : 796 ?></div></td>
                </tr>
            </table>
        </td>
        <td class="main-table-cell" style="width: 40px;"><table cellpadding="0" cellspacing="0" style="width: 40px;">
                <tr>
                    <td style="width: 40px;"><div class="main-table-line tac"><?= $item['unit'] ?></div></td>
                </tr>
            </table>
        </td>
        <td class="main-table-cell" style="width: 42px;"><table cellpadding="0" cellspacing="0" style="width: 40px;">
                <tr>
                    <td style="width: 40px;"><div class="main-table-line tar"><?= number_format($item['quantity'], 2, ',', ' ') ?></div></td>
                </tr>
            </table>
        </td>
        <td class="main-table-cell" style="width: 58px;"><table cellpadding="0" cellspacing="0" style="width: 56px;">
                <tr>
                    <td style="width: 56px;"><div class="main-table-line tar"><?= number_format($item['price'], 2, ',', ' ') ?></div></td>
                </tr>
            </table>
        </td>
        <td class="main-table-cell" style="width: 70px;"><table cellpadding="0" cellspacing="0" style="width: 68px;">
                <tr>
                    <td style="width: 67px;"><div class="main-table-line tar"><?= number_format($item['sum'], 2, ',', ' ') ?></div></td>
                </tr>
            </table>
        </td>
        <td class="main-table-cell" style="width: 50px;"><table cellpadding="0" cellspacing="0" style="width: 50px;">
                <tr>
                    <td style="width: 50px;"><div class="main-table-line tac">без акциза</div></td>
                </tr>
            </table>
        </td>
        <td class="main-table-cell" style="width: 50px;"><table cellpadding="0" cellspacing="0" style="width: 50px;">
                <tr>
                    <td style="width: 50px;"><div class="main-table-line tac">--</div></td>
                </tr>
            </table>
        </td>
        <td class="main-table-cell" style="width: 65px;"><table cellpadding="0" cellspacing="0" style="width: 65px;">
                <tr>
                    <td style="width: 65px;"><div class="main-table-line tac">--</div></td>
                </tr>
            </table>
        </td>
        <td class="main-table-cell" style="width: 70px;"><table cellpadding="0" cellspacing="0" style="width: 68px;">
                <tr>
                    <td style="width: 67px;"><div class="main-table-line tar"><?= number_format($item['sum'], 2, ',', ' ') ?></div></td>
                </tr>
            </table>
        </td>
        <td class="main-table-cell" style="width: 25px;"><table cellpadding="0" cellspacing="0" style="width: 25px;">
                <tr>
                    <td style="width: 25px;"><div class="main-table-line tac">--</div></td>
                </tr>
            </table>
        </td>
        <td class="main-table-cell" style="width: 50px;"><table cellpadding="0" cellspacing="0" style="width: 50px;">
                <tr>
                    <td style="width: 50px;"><div class="main-table-line tac">--</div></td>
                </tr>
            </table>
        </td>
        <td class="main-table-cell" style="width: 85px;"><table cellpadding="0" cellspacing="0" style="width: 85px;">
                <tr>
                    <td style="width: 85px;"><div class="main-table-line tac">--</div></td>
                </tr>
            </table>
        </td>
    </tr>
    <?php } ?>
    <!-- конец: таблица с данными из БД -->
    <tr>
        <td class="first-column main-table-cell main-table-line"></td>
        <td colspan="7" class="main-table-cell main-table-line" style="font-weight: bold; text-align: left;"> Всего к оплате (9)</td>
        <td class="main-table-cell main-table-line tar"><?= $sumFormatted ?></td>
        <td colspan="2" class="main-table-cell main-table-line tac" style="font-weight: bold;">Х</td>
        <td class="main-table-cell main-table-line tac">--</td>
        <td class="main-table-cell main-table-line tar"><?= $sumFormatted ?></td>
        <td colspan="3" class="main-table-cell main-table-line"></td>
    </tr>
    <tr>
        <td rowspan="3" class="first-column"><table cellpadding="0" cellspacing="0" style="width: 75px;">
                <tr>
                    <td class="main-table-cell-padding5"></td>
                </tr>
                <tr>
                    <td style="width: 75px;"><div style="line-height: 130%; font-size: 7px;">Документ<br/>составлен на {{:ptp:}}<br/>листе(ах)</div></td>
                </tr>
            </table>
        </td>
        <td colspan="2" style="width: 170px;"><table cellpadding="0" cellspacing="0" style="width: 170px;">
                <tr>
                    <td class="main-table-cell-padding5"></td>
                </tr>
                <tr>
                    <td style="width: 10px;"></td>
                    <td style="width: 160px;"><div style="line-height: 130%; font-size: 8px;">Руководитель организации или иное уполномоченное лицо</div></td>
                </tr>
            </table>
        </td>
        <td colspan="2" style="width: 95px;"><table cellpadding="0" cellspacing="0">
                <tr>
                    <td class="main-table-cell-padding10"></td>
                </tr>
                <tr>
                    <td style="width: 95px;"><div class="text-bottom-border" style="line-height: 130%; font-size: 8px;"></div></td>
                </tr>
            </table>
        </td>
        <td style="width: 10px;"></td>
        <td colspan="2" style="width: 165px;"><table cellpadding="0" cellspacing="0" style="width: 165px;">
                <tr>
                    <td class="main-table-cell-padding10"></td>
                </tr>
                <tr>
                    <td style="width: 155px;"><div class="text-bottom-border" style="line-height: 130%; font-size: 8px;"></div></td>
                    <td style="width: 15px;"></td>
                </tr>
            </table>
        </td>

        <td colspan="3" style="width: 145px;"><table cellpadding="0" cellspacing="0" style="width: 145px;">
                <tr>
                    <td class="main-table-cell-padding5"></td>
                </tr>
                <tr>
                    <td style="width: 145px;"><div style="line-height: 130%; font-size: 8px;">Главный бухгалтер<br/>или иное уполномоченное лицо</div></td>
                </tr>
            </table>
        </td>
        <td style="width: 95px;"><table cellpadding="0" cellspacing="0">
                <tr>
                    <td class="main-table-cell-padding10"></td>
                </tr>
                <tr>
                    <td style="width: 95px;"><div class="text-bottom-border" style="line-height: 130%; font-size: 8px;"></div></td>
                </tr>
            </table>
        </td>
        <td style="width: 10px;"></td>
        <td colspan="3" style="width: 200px;"><table cellpadding="0" cellspacing="0" style="width: 200px;">
                <tr>
                    <td class="main-table-cell-padding10"></td>
                </tr>
                <tr>
                    <td style="width: 200px;"><div class="text-bottom-border" style="line-height: 130%; font-size: 8px;"></div></td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td colspan="2"></td>
        <td colspan="3" style="line-height: -120%;"><div style="text-align: center;">(подпись)</div></td>
        <td colspan="2" style="line-height: -120%;"><div style="text-align: center;">(ф.и.о.)</div></td>
        <td colspan="3"></td>
        <td colspan="2" style="line-height: -120%;"><div style="text-align: center;">(подпись)</div></td>
        <td colspan="3" style="line-height: -120%;"><div style="text-align: center;">(ф.и.о.)</div></td>
    </tr>

    <tr>
        <td colspan="2" style="width: 170px;"><table cellpadding="0" cellspacing="0" style="width: 170px;">
                <tr>
                    <td style="width: 10px;"></td>
                    <td style="width: 160px;"><div style="line-height: 130%; font-size: 8px;">Индивидуальный предприниматель или иное уполномоченное лицо</div></td>
                </tr>
            </table>
        </td>
        <td colspan="2" style="width: 95px;"><table cellpadding="0" cellspacing="0">
                <tr>
                    <td style="width: 95px;"><div class="text-bottom-border tac" style="line-height: -15%;"><img src="/i/pdf/signt.png" width="100" height="10" alt=""/></div></td>
                </tr>
            </table>
        </td>
        <td style="width: 10px;"></td>
        <td colspan="2" style="width: 165px;"><table cellpadding="0" cellspacing="0" style="width: 165px;">
                <tr>
                    <td style="width: 155px;"><div class="text-bottom-border" style="line-height: 130%; font-size: 8px;">Москвин П. Д.</div></td>
                    <td style="width: 15px;"></td>
                </tr>
            </table>
        </td>
        <td style="width: 10px;"></td>
        <td colspan="6" style="width: 440px;"><table cellpadding="0" cellspacing="0" style="width: 440px;">
                <tr>
                    <td style="width: 440px;"><div class="text-bottom-border tac" style="line-height: 130%; font-size: 8px;">свидетельство 50 № 013006060 от 18.12.2012</div></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="first-column"></td>
        <td colspan="2" style="line-height: -120%; border-bottom: 2px solid #000;"><div style="text-align: center;"></div></td>
        <td colspan="2" style="line-height: -120%; border-bottom: 2px solid #000;"><div style="text-align: center;">(подпись)</div></td>
        <td style="border-bottom: 2px solid #000;"></td>
        <td colspan="2" style="line-height: -120%; border-bottom: 2px solid #000;"><div style="text-align: center;">(ф.и.о.)</div></td>
        <td colspan="7" style="line-height: -120%; border-bottom: 2px solid #000;"><div style="text-align: center;">(реквизиты свидетельства о государственной регистрации индивидуального предпринимателя)</div></td>
    </tr>
    <tr>
        <td colspan="3" style="width: 235px;"><table cellpadding="0" cellspacing="0" style="width: 235px;">
                <tr>
                    <td class="main-table-cell-padding10"></td>
                </tr>
                <tr>
                    <td style="width: 235px;"><div style="font-size: 8px; line-height: 130%;">Основание передачи (сдачи) / получения (приемки)</div></td>
                </tr>
            </table>
        </td>
        <td colspan="10" style="width: 720px;"><table cellpadding="0" cellspacing="0" style="width: 720px;">
                <tr>
                    <td class="main-table-cell-padding10"></td>
                </tr>
                <tr>
                    <td style="width: 715px;"><div class="text-bottom-border" style="line-height: 130%;">Счет <?= $orderId ?> от <?= date('d.m.Y', $createdAt) ?></div></td>
                </tr>
            </table>
        </td>
        <td><table cellpadding="0" cellspacing="0" style="width: 18px;">
                <tr>
                    <td class="main-table-cell-padding10"></td>
                </tr>
                <tr>
                    <td style="width: 18px;"><div>[8]</div></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3"></td>
        <td colspan="10"><div style="text-align: center; line-height: -120%; font-size: 6px;">(договор; доверенность и др.)</div></td>
        <td></td>
    </tr>
    <tr>
        <td colspan="3" style="width: 180px;"><table cellpadding="0" cellspacing="0" style="width: 180px;">
                <tr>
                    <td style="width: 180px;"><div style="font-size: 8px; line-height: 40%;">Данные о транспортировке и грузе</div></td>
                </tr>
            </table>
        </td>
        <td colspan="10" style="width: 775px;"><table cellpadding="0" cellspacing="0" style="width: 775px;">
                <tr>
                    <td style="width: 770px;"><div class="text-bottom-border" style="line-height: 40%;"></div></td>
                </tr>
            </table>
        </td>
        <td><table cellpadding="0" cellspacing="0" style="width: 18px;">
                <tr>
                    <td style="width: 18px;"><div>[9]</div></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3"></td>
        <td colspan="10"><div style="text-align: center; line-height: -30%; font-size: 6px; ">(транспортная накладная, поручение экспедитору, экспедиторская / складская расписка и др. / масса нетто/ брутто груза, если не приведены ссылки на транспортные документы, содержащие эти сведения)</div></td>
        <td></td>
    </tr>
    <tr>
        <td colspan="15" style="line-height: -10%;"></td>
    </tr>
    <tr>
        <td colspan="15"><table cellpadding="0" cellspacing="0" style="width: 800px;">
                <tr>
                    <td style="width: 500px;"><table cellpadding="0" cellspacing="0" style="width: 500px;">
                            <tr>
                                <td class="main-table-cell-padding3"></td>
                            </tr>
                            <tr>
                                <td style="width: 500px;"><div style="font-size: 8px;">Товар (груз) передал / услуги, результаты работ, права сдал</div></td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 2px; border-right: 2px solid #000;"></td>
                    <td style="width: 5px;"></td>
                    <td style="width: 450px;"><table cellpadding="0" cellspacing="0" style="width: 450px;">
                            <tr>
                                <td class="main-table-cell-padding3"></td>
                            </tr>
                            <tr>
                                <td style="width: 450px;"><div style="font-size: 8px;">Товар (груз) получил / услуги, результаты работ, права принял</div></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="15"><table cellpadding="0" cellspacing="0" style="width: 975px;">
                <tr>
                    <td class="main-table-cell-padding10" style="width: 500px;"></td>
                    <td style="width: 2px; border-right: 2px solid #000;"></td>
                    <td class="main-table-cell-padding10" style="width: 463px;"></td>
                </tr>
                <tr>
                    <td style="width: 137px;"><div class="text-bottom-border"></div></td>
                    <td style="width: 15px;"></td>
                    <td style="width: 140px;"><div class="text-bottom-border"></div></td>
                    <td style="width: 15px;"></td>
                    <td style="width: 158px;"><div class="text-bottom-border"></div></td>
                    <td style="width: 15px;"></td>
                    <td style="width: 20px;"><div>[10]</div></td>
                    <td style="width: 2px; border-right: 2px solid #000;"></td>
                    <td style="width: 5px;"></td>
                    <td style="width: 140px;"><div class="text-bottom-border"></div></td>
                    <td style="width: 15px;"></td>
                    <td style="width: 140px;"><div class="text-bottom-border"></div></td>
                    <td style="width: 15px;"></td>
                    <td style="width: 133px;"><div class="text-bottom-border"></div></td>
                    <td style="width: 5px;"></td>
                    <td style="width: 20px;"><div>[15]</div></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="15"><table cellpadding="0" cellspacing="0" style="width: 975px;">
                <tr>
                    <td class="main-table-cell-padding3" style="width: 500px;"></td>
                    <td style="width: 2px; border-right: 2px solid #000;"></td>
                    <td class="main-table-cell-padding3" style="width: 463px;"></td>
                </tr>
                <tr>
                    <td style="width: 137px;"><div style="line-height: -140%; text-align: center;">(должность)</div></td>
                    <td style="width: 15px;"></td>
                    <td style="width: 140px;"><div style="line-height: -140%; text-align: center;">(подпись)</div></td>
                    <td style="width: 15px;"></td>
                    <td style="width: 158px;"><div style="line-height: -140%; text-align: center;">(ф.и.о.)</div></td>
                    <td style="width: 15px;"></td>
                    <td style="width: 20px;"></td>
                    <td style="width: 2px; border-right: 2px solid #000;"></td>
                    <td style="width: 5px;"></td>
                    <td style="width: 140px;"><div style="line-height: -140%; text-align: center;">(должность)</div></td>
                    <td style="width: 2px;"></td>
                    <td style="width: 175px;"><div style="line-height: -140%; text-align: center;">(подпись)</div></td>
                    <td style="width: 2px;"></td>
                    <td style="width: 120px;"><div style="line-height: -140%; text-align: center;">(ф.и.о.)</div></td>
                    <td style="width: 2px;"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="15"><table cellpadding="0" cellspacing="0" style="width: 975px;">
                <tr>
                    <td style="width: 200px;"><table cellpadding="0" cellspacing="0" style="width: 200px;">
                            <tr>
                                <td style="width: 200px;"><div style="font-size: 8px;">Дата отгрузки, передачи (сдачи)</div></td>
                                <td style="width: 265px;"><div class="text-bottom-border"><?= $shippingDate ?> года</div></td>
                                <td style="width: 15px;"></td>
                                <td style="width: 20px;"><div>[11]</div></td>
                                <td style="width: 2px; border-right: 2px solid #000;"></td>
                                <td style="width: 5px;"></td>
                                <td style="width: 190px;"><div style="font-size: 8px;">Дата получения (приемки)</div></td>
                                <td style="width: 252px;"><div class="text-bottom-border">«&nbsp;&nbsp;&nbsp;&nbsp;»&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;20&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;года</div></td>
                                <td style="width: 5px;"></td>
                                <td style="width: 20px;"><div>[16]</div></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="15"><table cellpadding="0" cellspacing="0" style="width: 975px;">
                <tr>
                    <td style="width: 500px;"><table cellpadding="0" cellspacing="0" style="width: 500px;">
                            <tr>
                                <td class="main-table-cell-padding3"></td>
                            </tr>
                            <tr>
                                <td style="width: 500px;"><div style="font-size: 8px;">Иные сведения об отгрузке, передаче</div></td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 2px; border-right: 2px solid #000;"></td>
                    <td style="width: 5px;"></td>
                    <td style="width: 468px;"><table cellpadding="0" cellspacing="0" style="width: 468px;">
                            <tr>
                                <td class="main-table-cell-padding3"></td>
                            </tr>
                            <tr>
                                <td style="width: 468px;"><div style="font-size: 8px;">Иные сведения о получении, приемке</div></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="15"><table cellpadding="0" cellspacing="0" style="width: 975px;">
                <tr>
                    <td class="main-table-cell-padding10" style="width: 500px;"></td>
                    <td style="width: 2px; border-right: 2px solid #000;"></td>
                </tr>
                <tr>
                    <td style="width: 465px;"><div class="text-bottom-border" style="line-height: 20%;"></div></td>
                    <td style="width: 15px;"></td>
                    <td style="width: 20px;"><div>[12]</div></td>
                    <td style="width: 2px; border-right: 2px solid #000;"></td>
                    <td style="width: 5px;"></td>
                    <td style="width: 443px;"><div class="text-bottom-border" style="line-height: 20%;"></div></td>
                    <td style="width: 5px;"></td>
                    <td style="width: 20px;"><div>[17]</div></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="15"><table cellpadding="0" cellspacing="0" style="width: 975px;">
                <tr>
                    <td style="width: 465px;"><div style="line-height: -100%; text-align: center; font-size: 6px;">(ссылки на неотъемлемые приложения, сопутствующие документы, иные документы и т.п.)</div></td>
                    <td style="width: 15px;"></td>
                    <td style="width: 20px;"></td>
                    <td style="width: 2px; border-right: 2px solid #000;"></td>
                    <td style="width: 5px;"></td>
                    <td style="width: 465px;"><div style="line-height: -100%; text-align: center; font-size: 6px;">(информация о наличии/отсутствии претензии; ссылки на неотъемлемые приложения, и другие документы и т.п.)</div></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="15"><table cellpadding="0" cellspacing="0" style="width: 975px;">
                <tr>
                    <td class="main-table-cell-padding3" style="width: 500px;"></td>
                    <td style="width: 2px; border-right: 2px solid #000;"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="15"><table cellpadding="0" cellspacing="0" style="width: 975px;">
                <tr>
                    <td style="width: 500px;"><table cellpadding="0" cellspacing="0" style="width: 500px;">
                            <tr>
                                <td class="main-table-cell-padding3"></td>
                            </tr>
                            <tr>
                                <td style="width: 500px;"><div style="font-size: 8px; line-height: -200%;">Ответственный за правильность оформления факта хозяйственной жизни</div></td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 2px; border-right: 2px solid #000;"></td>
                    <td style="width: 5px;"></td>
                    <td style="width: 468px;"><table cellpadding="0" cellspacing="0" style="width: 468px;">
                            <tr>
                                <td class="main-table-cell-padding3"></td>
                            </tr>
                            <tr>
                                <td style="width: 468px;"><div style="font-size: 8px; line-height: -200%;">Ответственный за правильность оформления факта хозяйственной жизни</div></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="15"><table cellpadding="0" cellspacing="0" style="width: 975px; line-height: 10px;">
                <tr>
                    <td style="width: 147px;"><div class="text-bottom-border">Индивидуальный предприниматель</div></td>
                    <td style="width: 15px;"></td>
                    <td style="width: 140px;"><div class="text-bottom-border tac" style="line-height: -5%;"><img src="/i/pdf/signt.png" width="100" height="10" alt=""/></div></td>
                    <td style="width: 15px;"></td>
                    <td style="width: 148px;"><div class="text-bottom-border">Москвин П. Д.</div></td>
                    <td style="width: 15px;"></td>
                    <td style="width: 20px;"><div>[13]</div></td>
                    <td style="width: 2px; border-right: 2px solid #000;"></td>
                    <td style="width: 5px;"></td>
                    <td style="width: 147px;"><div class="text-bottom-border"></div></td>
                    <td style="width: 15px;"></td>
                    <td style="width: 140px;"><div class="text-bottom-border"></div></td>
                    <td style="width: 15px;"></td>
                    <td style="width: 125px;"><div class="text-bottom-border"></div></td>
                    <td style="width: 5px;"></td>
                    <td style="width: 20px;"><div>[18]</div></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="15"><table cellpadding="0" cellspacing="0" style="width: 975px;">
                <tr>
                    <td style="width: 145px;"><div style="line-height: -140%; text-align: center;">(должность)</div></td>
                    <td style="width: 15px;"></td>
                    <td style="width: 145px;"><div style="line-height: -140%; text-align: center;">(подпись)</div></td>
                    <td style="width: 15px;"></td>
                    <td style="width: 145px;"><div style="line-height: -140%; text-align: center;">(ф.и.о.)</div></td>
                    <td style="width: 15px;"></td>
                    <td style="width: 20px;"></td>
                    <td style="width: 2px; border-right: 2px solid #000;"></td>
                    <td style="width: 5px;"></td>
                    <td style="width: 160px;"><div style="line-height: -140%; text-align: center;">(должность)</div></td>
                    <td style="width: 2px;"></td>
                    <td style="width: 145px;"><div style="line-height: -140%; text-align: center;">(подпись)</div></td>
                    <td style="width: 2px;"></td>
                    <td style="width: 150px;"><div style="line-height: -140%; text-align: center;">(ф.и.о.)</div></td>
                    <td style="width: 2px;"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="15"><table cellpadding="0" cellspacing="0" style="width: 975px;">
                <tr>
                    <td style="width: 500px;"><table cellpadding="0" cellspacing="0" style="width: 500px;">
                            <tr>
                                <td class="main-table-cell-padding3"></td>
                            </tr>
                            <tr>
                                <td style="width: 500px;"><div style="font-size: 8px; line-height: -120%;">Наименование экономического субъекта – составителя документа (в т.ч. комиссионера / агента)</div></td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 2px; border-right: 2px solid #000;"></td>
                    <td style="width: 5px;"></td>
                    <td style="width: 468px;"><table cellpadding="0" cellspacing="0" style="width: 468px;">
                            <tr>
                                <td class="main-table-cell-padding3"></td>
                            </tr>
                            <tr>
                                <td style="width: 468px;"><div style="font-size: 8px; line-height: -120%;">Наименование экономического субъекта – составителя документа</div></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="15"><table cellpadding="0" cellspacing="0" style="width: 975px;">
                <tr>
                    <td class="main-table-cell-padding3" style="width: 500px;"></td>
                    <td style="width: 2px; border-right: 2px solid #000;"></td>
                </tr>
                <tr>
                    <td style="width: 465px;"><div class="text-bottom-border" style="font-size: 8px; line-height: -80%;">ИП Москвин Павел Дмитриевич, ИНН 501703416801</div></td>
                    <td style="width: 15px;"></td>
                    <td style="width: 20px;"><div>[14]</div></td>
                    <td style="width: 2px; border-right: 2px solid #000;"></td>
                    <td style="width: 5px;"></td>
                    <td style="width: 442px;"><div class="text-bottom-border" style="font-size: 8px; line-height: -80%;"><?= $clientName ?>, ИНН/КПП <?= $waybillBankData ?></div></td>
                    <td style="width: 5px;"></td>
                    <td style="width: 20px;"><div>[19]</div></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="15"><table cellpadding="0" cellspacing="0" style="width: 997px;">
                <tr>
                    <td class="main-table-cell-padding3" style="width: 500px;"></td>
                    <td style="width: 2px; border-right: 2px solid #000;"></td>
                </tr>
                <tr>
                    <td style="width: 465px;"><div style="line-height: -140%; text-align: center; font-size: 7px;">(может не заполняться при проставлении печати в М.П., может быть указан ИНН / КПП)</div></td>
                    <td style="width: 15px;"></td>
                    <td style="width: 20px;"></td>
                    <td style="width: 2px; border-right: 2px solid #000;"></td>
                    <td style="width: 5px;"></td>
                    <td style="width: 465px;"><div style="line-height: -140%; text-align: center; font-size: 7px;">(может не заполняться при проставлении печати в М.П., может быть указан ИНН / КПП)</div></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="15"><table cellpadding="0" cellspacing="0" style="width: 997px;">
                <tr>
                    <td class="main-table-cell-padding3" style="width: 500px;"></td>
                    <td style="width: 2px; border-right: 2px solid #000;"></td>
                </tr>
                <tr>
                    <td style="width: 50px;"></td>
                    <td style="width: 450px;"><div style="line-height: -140%;">М.П.</div></td>
                    <td style="width: 2px; border-right: 2px solid #000;"></td>
                    <td style="width: 5px;"></td>
                    <td style="width: 45px;"></td>
                    <td style="width: 440px;"><div style="line-height: -140%;">М.П.</div></td>
                </tr>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<!--<div style="line-height: -250px;"><img src="/i/pdf/printip.png" width="100" alt=""/></div>-->