<?php
require_once __DIR__.'/../Ar/Customer.php';

class Api
{
    private static $classes = [
        'customer' => Customer::class,
    ];
    protected $data;
    protected $module;
    protected $action;
    protected $class;

    /**
     * @throws \Exception
     */
    public function __construct($request)
    {
        $request = json_decode($request, true);

        $this->action = isset($request['action']) ? $request['action'] : null;
        $this->module = isset($request['module']) ? $request['module'] : null;

        if (!$this->action || !$this->module) {
            throw new \Exception('Не найден модуль или действие');
        }

        if (!array_key_exists($this->module, self::$classes)) {
            throw new \Exception('Не найден класс модуля');
        }

        $this->data = $request['data'];

        $this->class = new self::$classes[$this->module]($this->data);

        $this->class->{$this->action}();
    }

    public function exec()
    {
        return ['success' => true, 'data' => $this->data];
    }
}
