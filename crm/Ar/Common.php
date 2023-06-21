<?php

abstract class Common
{
    public static $tableName;
    public static $idField;
    protected $data;
    /**
     * @var \mysqli
     */
    private $db;

    public function __construct($data)
    {
        $this->data = $data;
        $this->db = new \mysqli(_HostName, _User, _Passwd, _DB);
    }

    public function update()
    {
        $item = $this->data['item'];
		$tip=$this->data['pers'];

        $table = static::$tableName;

        $uidValue = $item[static::$idField];

        unset($item[static::$idField]);

        $set = [];

        foreach ($item as $field => $value) {
			
			//if ($tip=='pers' ){
			$value=iconv('utf-8//IGNORE', 'windows-1251//IGNORE', $value);
			
			//}
			if ($field!='tip'){
            $set[] = "$field = '$value'";
			}
        }

        $set = implode(',', $set);

        $where = static::$idField.' = '.$uidValue;

        $uSQL = <<<SQL
            UPDATE $table SET $set WHERE $where
SQL;
        $this->db->query($uSQL);

        $sSQL = <<<SQL
            SELECT * FROM $table WHERE $where
SQL;
        $res = $this->db->query($sSQL);
		//echo  $uSQL;
        return $res->fetch_assoc();

    }
	



}
