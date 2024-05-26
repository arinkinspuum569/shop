<?php

namespace common;

use mysqli_sql_exception;

enum tables: string{
    case MENU = 'menu';
    case USER = 'users';
}
class db_helper
{
    private \mysqli $mysql;
    private static ?db_helper $db = null;
    private function __construct()
    {
        $this->mysql = new \mysqli("localhost", "root", "", "2k2024", 3306);
    }

    public static function getInstance(): db_helper{
        if (self::$db == null){
            self::$db = new db_helper();
        }
        return self::$db;
    }

    public function get_all_data(tables $table, array $conditioins = []): array
    {
        try {
            $this->mysql->begin_transaction(name: "get_all");
            // Имя таблицы не поддерживается в качестве параметра запроса.
            // Поэтому при создании запросов строковое значение
            // было изменено на перечисление
            $c = sizeof($conditioins) > 0 ? " WHERE " : "";
            $amp = "";
            foreach ($conditioins as $k => $v){
                $c .= "$amp$k = ?";
                $amp = " and ";
            }

            $stmt = $this->mysql->prepare("SELECT * FROM $table->value $c");
            foreach ($conditioins as $v) {
                if (!$stmt->bind_param('s', $v))
                    throw new mysqli_sql_exception("Ошибка привязки параметра");
            }

            if (!$stmt->execute())
                throw new mysqli_sql_exception("Ошибка выполнения запроса");
            if (!$res = $stmt->get_result())
                throw new mysqli_sql_exception("Ошибка получения результатов запроса");
            $arr = $res->fetch_all(MYSQLI_ASSOC);
            $this->mysql->commit(name: "get_all");
            return $arr;
        } catch (mysqli_sql_exception $e) {
            print($e->getMessage());
            $this->mysql->rollback(name: "get_all");
            return array();
        }
    }

    public function reg_user($username, $password): bool{
        try{
            $this->mysql->begin_transaction(name: "user");
            $t = tables::USER->value;
            $stmt = $this->mysql->prepare("INSERT INTO $t (login, password) VALUES (?, ?)");
            if (!$stmt->bind_param("ss", $username, $password))
                throw new mysqli_sql_exception("Не удалось привязать значения параметров");
            if (!$stmt->execute())
                throw new mysqli_sql_exception("Ошибка выполнения запроса");
            $this->mysql->commit(name: "user");
            return true;
        } catch (mysqli_sql_exception $e) {
            print ($e->getMessage());
            $this->mysql->rollback(name: "user");
            return false;
        }
    }
}
