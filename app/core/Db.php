<?php

class Db
{
    static $link = null;
    static $host = 'localhost';
    static $user = 'root';
    static $pass = '';
    static $db = 'test40';
    function __construct()
    {
        if ( $this::$link == null )
        {
            try
            {
                $this::$link = $this->db_connect($this::$host, $this::$user, $this::$pass, $this::$db);
            }
            catch (Exception $e)
            {
                throw $e;
            }
        }
    }

    private function db_connect($host, $user, $password, $database)
    {
        $link = mysqli_connect($host, $user, $password, $database);
        if (!$link) {
            $message = "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL;
            $message .= " Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL;
            $message .= "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL;
            throw new ErrorException($message);
        }
        mysqli_set_charset($link, 'utf-8');
        return $link;
    }

    public function get_query($sql)
    {
        if ($this::$link === null) {
            return;
        }
        $link = $this::$link;

        $response = mysqli_query($link, $sql);
        return mysqli_fetch_all($response, 1);
    }

    // извлекает таблицу из бд;
    function get_data($table, $predicate = null)
    {
        if ($this::$link === null) {
            return;
        }
        $link = $this::$link;
        $query = "SELECT * FROM `$table`";

        if ( !is_null($predicate) ) {
            $query .= " WHERE " . $predicate;
        }

        $response = mysqli_query($link, $query);
        return mysqli_fetch_all($response, 1);
    }

    // создаёт запись, принимает массив вида ['Название поля'=>'Значение'];
    function insert_to_db($data, $table)
    {
        if ($this::$link === null) {
            return;
        }
        $link = $this::$link;

        $query = "INSERT INTO `$table`(";
        $values = "(";
        foreach ($data as $field => $value) {
            $query .= "`$field`,";
            $values .= "'$value',";
        }
        $query = rtrim($query, ",");
        $values = rtrim($values, ",");
        $query .= ") VALUES " . $values . ")";
        return (mysqli_query($link, $query));
    }

    // аналогично предыдущему, только обновляет запись, удовлетворяющую условию;
    function update_db($table, $values, $condition)
    {
        if ($this::$link === null) {
            return;
        }
        $link = $this::$link;

        $query = "UPDATE `$table` SET ";
        foreach ($values as $col => $val) {
            $query .= "`$col`='$val',";
        }
        $query = rtrim($query, ",");
        $query .= " WHERE " . $condition;
        return mysqli_query($link, $query);
    }

    function rm_note($table, $condition)
    {
        if ($this::$link === null) {
            return;
        }
        $link = $this::$link;

        $query = "DELETE FROM `$table`";
        $query .= " WHERE " . $condition;
        return mysqli_query($link, $query);
    }
}

?>