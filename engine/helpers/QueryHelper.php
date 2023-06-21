<?php
// инициализируем движок
require_once(dirname(__DIR__) . "/init.php");

class QueryHelper
{
    /**
     * Возвращает SQL запрос который в дальнейшем дополняется условиями
     * @return string
     */
    private static function getSqlQuery() {
        $dbQuery = 'SELECT q.uid as order_id, q.prdm_sum_acc, u.surname, u.name, u.email, c.short, (SELECT SUM(sum_accounts) FROM payment_predm pp WHERE pp.query_id = q.uid) as sum_accounts
			FROM queries q
			LEFT JOIN clients c ON q.client_id = c.uid
			LEFT JOIN users u ON q.user_id = u.uid';

        return $dbQuery;
    }

    /**
     * Возвращает информацию о заказе по ID заказа
     * @param $queryId
     * @return mixed
     */
    public static function getQueryInfoById($queryId) {
        $query = self::getSqlQuery();
        $query .= ' WHERE q.uid = '. intval($queryId) .' LIMIT 1';

        return Database::getInstance()->getRow($query);
    }

    /**
     * Возвращает информацию о заказе по номеру заказа в магазине paketoff
     * @param $shopNum
     * @return mixed
     */
    public static function getQueryInfoByShopNum($shopNum) {
        $query = self::getSqlQuery();
        $query .= ' WHERE q.corsina_order_num = '. intval($shopNum) .' LIMIT 1';

        return Database::getInstance()->getRow($query);
    }
}
