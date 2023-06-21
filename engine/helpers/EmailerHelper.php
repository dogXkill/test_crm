<?php

class EmailerHelper
{
    private $to;
    private $subject;
    private $body;
    private $headerFrom = 'CRM.Upak.me <crm@upak.me>';
    private $contentType = 'text/html';
    private $charset = 'utf-8';

    /**
     * @param $to string|array Адрес письма
     * @param $subject string Тема письма
     * @param $body string Тело письма
     */
    public function __construct($to, $subject, $body)
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;
    }

    /**
     * @param $header string Поменять поле "От кого" в письме
     * @return $this
     */
    public function setHeaderFrom($header)
    {
        $this->headerFrom = $header;

        return $this;
    }

    /**
     * @param $contentType string Тип письма
     * @return $this
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @param $charset string Кодировка письма
     * @return $this
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * @return string Заголовки письма
     */
    public function getHeader()
    {
        return "From: $this->headerFrom \r\nContent-type: $this->contentType; charset=\"$this->charset\"";
    }

    /**
     * @return void
     */
    public function send()
    {
        mail($this->to, $this->subject, $this->body, $this->getHeader());
    }

    /**
     * возвращает список email для разных писем (например, о поступлении оплаты по счету)
     * @return array
     */
    public static function getMail()
    {
        $mails = Database::getInstance()->getCol("SELECT DISTINCT email FROM mail");
        if ($mails === false) {
            $mails = array();
        }

        return $mails;
    }
}