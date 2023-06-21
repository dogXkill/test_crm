<?php
require_once("../engine/helpers/QueryHelper.php");
require_once("../engine/helpers/EmailerHelper.php");

$request = file_get_contents('php://input');
$request = json_decode($request);

if ($request instanceof stdClass) {
    // для защиты обмена данных приходит спец код, который отправляется из интернет магазина
    if (!property_exists($request, 'request_code') || $request->request_code !== 'c7a3f330f3197d68fef01b21eba1a2a1512080fc') {
        die('403');
    }

    if (property_exists($request, 'order_id')) {
        // id заказа
        $orderId = $request->order_id;
        // банковский id платежа с эквайринга
        $paymentId = (int) $request->payment_id;
        // сумма платежа с эквайринга в копейках
        $paymentAmount = (float) $request->payment_amount / 100;
        // для заказов с интернет магазина к id заказа добавляется префикс "p"
        $shopOrder = strpos($orderId, 'p') !== false;

        // в номер заказа могут дописать через дефис разные символы для доплаты по заказу
        $pos = strpos($orderId, '-');
        if ($pos !== false) {
            $temp = explode('-', $orderId);
            $orderId = (int) $temp[0];
        }

        // оплата заказа с интранет
        if (!$shopOrder) {
            $order = QueryHelper::getQueryInfoById($orderId);
        } else {
            $orderId = (int) substr($orderId, 1);
            // заказ с интернет магазина
            $order = QueryHelper::getQueryInfoByShopNum($orderId);
        }

        if (!$order) {
            exit;
        }

        // Добавляем в базу новую платежку
        // проверка на дубль (по каким-то причинам банк делает несколько обращений к странице нотификаций)
        $check = Database::getInstance()->getRow("SELECT * FROM payment_predm WHERE acquiring_id={$paymentId}");
        if ($check === false) {
            $dbQuery = sprintf("INSERT INTO payment_predm (query_id, sum_accounts, date_ready, number_pp, acquiring_id) VALUES (%d, '%s', '%s', '%s', %d)", $order['order_id'], $paymentAmount, date('Y-m-d'), 'экв', $paymentId);
            Database::getInstance()->query($dbQuery);

            // ID созданного платежа
            $insertedId = Database::getInstance()->insertedId();

            if (!$shopOrder) {
                $order = QueryHelper::getQueryInfoById($orderId);
            } else {
                $order = QueryHelper::getQueryInfoByShopNum($orderId);
            }

            // Вычисляем новые данные запроса и обновляем запрос
            $oplaceno = floatval($order['sum_accounts']);
            $dolg = floatval($order['prdm_sum_acc']) - $oplaceno;

            $dbQuery = sprintf("UPDATE queries SET prdm_opl = '%s', prdm_dolg = '%s' WHERE uid = %d", $oplaceno, $dolg, $order['order_id']);
            Database::getInstance()->query($dbQuery);

            // Подготовка данных для отправки письма
            $mailsToSend = EmailerHelper::getMail();
            $mailsToSend[] = $order['email'];
            $mailsToSend = array_unique($mailsToSend);
            $mailsToSend = implode(', ', $mailsToSend);

            // Отправляем оповещение
            $params = array(
                'queryId' => $order['order_id'],
                'paymentId' => $insertedId,
                'summ' => $paymentAmount,
                'date' => date('d.m.Y'),
                'number' => $orderId,
                'client' => $order['short'],
                'dolg' => $dolg,
                'name' => $order['name'],
                'surname' => $order['surname'],
                'crm_url' => SITE_URL
            );

            $subject = Str::get('query/addPaymentMailSubject', array('{client}' => $params['client'], '{summ}' => $params['summ']), false);
            $body = Template::getInstance()->render('mails/actionPaymentAdd', $params, true);
            $emailer = new EmailerHelper($mailsToSend, $subject, $body);
            $emailer->send();
        }
    }
}