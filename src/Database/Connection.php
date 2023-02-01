<?php

namespace PageAnalyzer\Database;

use PDO;

final class Connection
{
    /**
     * Connection
     * тип @var
     */
    private static $conn;

    /**
     * Подключение к базе данных и возврат экземпляра объекта \PDO
     * @return \PDO
     * @throws \Exception
     */
    public function connect()
    {
        // Чтение параметров
        $params = parse_url($_ENV['DATABASE_URL']);
        //$params = 'postgresql://sal:vjkjnjd@localhost:5432/hexlet33';
        //$params = parse_url($params);
        if ($params == 'false') {
            throw new \Exception('Ошибка чтения файла database.ini');
        }
        // Подключение к БД
        $conStr = sprintf(
            "pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
            $params['host'],
            $params['port'],
            ltrim($params['path'], '/'),
            $params['user'],
            $params['pass']
        );

        $pdo = new \PDO($conStr);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    public static function get(): mixed
    {
        if (null === static::$conn) {
            static::$conn = new static();
        }

        return static::$conn;
    }

    protected function __construct()
    {
    }
}
