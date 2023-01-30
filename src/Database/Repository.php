<?php

namespace PageAnalyzer\Database;

use PageAnalyzer\Database\Connection;
use Carbon\Carbon;

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
        $created_at = Carbon::now();
        $sql = 'INSERT INTO urls (name, created_at) VALUES (:name, :created_at)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':created_at', $created_at);

        $stmt->execute();

        // возврат полученного значения id
        return $this->db->lastInsertId();
    }

    public function all()
    {
        $sql = 'SELECT * FROM urls';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll($this->db::FETCH_ASSOC);
        return $result;

    }

    public function findUrl($id)
    {
        $sql ='SELECT id, name, created_at FROM urls WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch($this->db::FETCH_ASSOC);
        return $result;
    }
}
