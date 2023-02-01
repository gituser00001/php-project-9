<?php

namespace PageAnalyzer\Database;

use PageAnalyzer\Database\Connection;
use PDO;
use Carbon\Carbon;

class Repository
{
    private object $db;

    public function __construct()
    {
        $this->db = Connection::get()->connect();
    }

    public function insertUrl(string $name): int
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

    public function all(): mixed
    {
        $sql = 'SELECT * FROM urls';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll($this->db::FETCH_ASSOC);
        return $result;
    }

    public function findUrl(int $id): mixed
    {
        $sql = 'SELECT id, name, created_at FROM urls WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch($this->db::FETCH_ASSOC);
        return $result;
    }

    public function findId(string $name): mixed
    {
        $sql = 'SELECT id FROM urls WHERE name = :name';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->execute();
        $result = $stmt->fetch($this->db::FETCH_ASSOC);
        return $result;
    }

    public function addCheck(
        mixed $id,
        mixed $statusCode,
        mixed $title = '',
        mixed $h1 = '',
        mixed $description = ''
    ): mixed {
        // подготовка запроса для добавления данных
        $created_at = Carbon::now();
        $sql = 'INSERT INTO url_checks (url_id, status_code, h1, title, description, created_at)
        VALUES (:url_id, :status_code, :h1, :title, :description, :created_at)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':url_id', $id);
        $stmt->bindValue(':created_at', $created_at);
        $stmt->bindValue(':status_code', $statusCode);
        $stmt->bindValue(':h1', $h1);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':description', $description);

        $stmt->execute();

        // возврат полученного значения id
        return $this->db->lastInsertId();
    }

    public function findCheckUrl(int $id): mixed
    {
        $sql = 'SELECT * FROM url_checks WHERE url_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $result = $stmt->fetchAll($this->db::FETCH_ASSOC);
        return $result;
    }

    public function findLastCheck(int $id): mixed
    {
        $sql = 'SELECT created_at, status_code FROM url_checks WHERE url_id = :id ORDER BY id DESC LIMIT 1;';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch($this->db::FETCH_ASSOC);
        return $result;
    }
}
