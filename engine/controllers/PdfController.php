<?php

// необходимые либы для заполнения счета и генерации pdf
require_once("../../engine/helpers/MoneyHelper.php");
require_once("../../engine/helpers/StringHelper.php");
require_once("../../assets/libs/tcpdf/tcpdf.php");

class PdfController extends AbstractController
{
    /**
     * @param $orderId
     * @return array|void
     */
    private function getOrderData($orderId)
    {
        $months = array(
            1 => 'января',
            2 => 'февраля',
            3 => 'марта',
            4 => 'апреля',
            5 => 'мая',
            6 => 'июня',
            7 => 'июля',
            8 => 'августа',
            9 => 'сентября',
            10 => 'октября',
            11 => 'ноября',
            12 => 'декабря'
        );

        // Наименование заказчика (клиента)
        $clientName = '';
        // Адрес заказчика (клиента)
        $clientAddress = '';
        // ИНН заказчика (клиента)
        $clientINN = 0;
        // КПП заказчика (клиента)
        $clientKPP = 0;
        // Список услуг(товаров) заказа
        $order = [];
        // Итого
        $sum = 0;
        // Количество наименований
        $lines = 0;
        // Оплатить до
        $booking_till = '';
        // Дата создания заказа
        $createdAt = '';

        $rows = Database::getInstance()->getRows("
            SELECT 
                queries.uid, 
                clients.name client_name, 
                clients.inn client_inn, 
                clients.kpp client_kpp,
                IFNULL(legal_address, postal_address) client_address,
                obj_accounts.name product_title,
                obj_accounts.num product_quantity,
                obj_accounts.art_num artikul,
                obj_accounts.price,
                queries.booking_till,
                queries.date_query created_at
            FROM queries 
                JOIN clients ON clients.uid = queries.client_id
                JOIN obj_accounts ON queries.uid = obj_accounts.query_id
            WHERE queries.uid = $orderId
        ");

        if (!$rows) {
            // mysql error
            echo 'Mysql error!';
            die();
        }

        foreach ($rows as $row) {
            if (!$clientName) {
                $clientName = trim($row['client_name']);
            }
            if (!$clientAddress) {
                $clientAddress = trim($row['client_address']);
            }
            if (!$booking_till) {
                $booking_till = date('d.m.Y', strtotime($row['booking_till']));
            }
            if (!$clientINN) {
                $clientINN = trim($row['client_inn']);
            }
            if (!$clientKPP) {
                $clientKPP = trim($row['client_kpp']);
            }
            if (!$createdAt) {
                $createdAt = trim($row['created_at']);
                $createdAt = strtotime($createdAt);
            }

            $row['artikul'] = $row['artikul'] ? trim($row['artikul']) : '';
            if ($row['artikul']) {
                $unit = mb_strtolower($row['artikul']) === 'd' ? 'услуга' : 'шт';
            } else {
                $unit = 'шт';
            }

            $order[] = [
                'title' => $row['product_title'],
                'quantity' => $row['product_quantity'],
                'artikul' => $row['artikul'],
                'price' => $row['price'],
                'sum' => $row['price'] * $row['product_quantity'],
                'unit' => $unit,
            ];

            $sum += $row['price'] * $row['product_quantity'];
            $lines++;
        }

        // Дата формирования счета
        $day = date('d');
        $monthRus = $months[intval(date('n'))];
        $month = date('m');
        $year = date('Y');
        $dateRus = "$day $monthRus $year";
        $dateFormatted = "$day.$month.$year";
        $shippingDate = "«{$day}» {$monthRus} {$year}";

        // отформатированная сумма
        $sumFormatted = number_format($sum, 2, ',', ' ');

        // ИНН и КПП для накладной
        $waybillBankData = '';
        if ($clientINN && $clientKPP) {
            $waybillBankData = "{$clientINN}/{$clientKPP}";
        }

        return compact(
            'clientName',
            'clientAddress',
            'clientINN',
            'clientKPP',
            'orderId',
            'order',
            'dateRus',
            'dateFormatted',
            'shippingDate',
            'sum',
            'sumFormatted',
            'booking_till',
            'lines',
            'createdAt',
            'waybillBankData'
        );
    }


    protected function invoiceAction()
    {
        $orderId = intval($this->request->get['qid']);

        if (!$orderId) {
            echo 'некорректный id счета';
            die();
        }

        $params = $this->getOrderData($orderId);

        // Сумма заказа прописью
        $stringSum = MoneyHelper::num2str($params['sum']);
        $stringSum = StringHelper::mbUcFirst($stringSum);
        $stringSum = iconv('cp1251', 'utf-8', $stringSum);
        $params['stringSum'] = $stringSum;

        $invoice = $this->template->render('pdf/invoice', $params, true);

        // ----------------Работа с PDF----------------------------------------
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'utf-8', false);

        // set document information
        $pdf->setCreator(PDF_CREATOR);
        $pdf->setTitle("Счет на оплату $orderId");

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // set default monospaced font
        $pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->setMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT);

        // set auto page breaks
        $pdf->setAutoPageBreak(TRUE, 0);

        // cell line height
        $pdf->setCellHeightRatio(1.1);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set font
        $pdf->setFont('dejavusans', '', 10);

        // add a page
        $pdf->AddPage();

        // print a block of text using Write()
        $pdf->WriteHTML($invoice);

        // Close and output PDF document
        $pdf->Output("Invoice_{$orderId}.pdf", 'D');
        // ---------------------------------------------------------
    }

    protected function waybillAction()
    {
        $orderId = intval($this->request->get['qid']);

        if (!$orderId) {
            echo 'некорректный id счета';
            die();
        }

        $params = $this->getOrderData($orderId);
        $waybill = $this->template->render('pdf/waybill', $params, true);

        // ----------------Работа с PDF----------------------------------------
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'utf-8', false);

        // set document information
        $pdf->setCreator(PDF_CREATOR);
        $pdf->setTitle("Товарная накладная $orderId");

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // set default monospaced font
        $pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->setMargins(12, 8, 12);

        // set auto page breaks
        $pdf->setAutoPageBreak(TRUE, 0);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set font
        $pdf->setFont('dejavusans', '', 6);

        // cell line height
        $pdf->setCellHeightRatio(0.75);

        // add a page
        $pdf->AddPage('L');

        // print a block of text using Write()
        $pdf->WriteHTML($waybill);

        // количество листов
        $pagesCount = $pdf->getNumPages();

        // печать
        if ($pagesCount > 1) {
            $pdf->setPage(1);
            $pdf->Image(dirname(dirname(__DIR__)) . '/i/pdf/printip.png', 55, 175, 35, 35);
        } else {
            $pdf->Image(dirname(dirname(__DIR__)) . '/i/pdf/printip.png', 55, 145, 35, 35);
        }

        // Close and output PDF document
        $pdf->Output("Waybill_{$orderId}.pdf", 'D');
        // ---------------------------------------------------------
    }

}
