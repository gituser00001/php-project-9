<?php

namespace PageAnalyzer\Database;

use PageAnalyzer\Database\Connection;

class Repository
{
    private $db;

    public function __construct()
    {
        $this->db = Connection::get()->connect();
    }

    public function insertUrl($name)
    {
        // подготовка запроса для добавления данных
        $sql = 'INSERT INTO urls (name) VALUES (:name)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $name);

        $stmt->execute();

        // возврат полученного значения id
        return $this->db->lastInsertId();
    }
}
