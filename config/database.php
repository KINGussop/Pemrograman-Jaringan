<?php
class Database {
    private static $connection = null;

    public static function connection(): PDO {
        if (self::$connection === null) {
            $dsn = 'mysql:host=localhost;dbname=jaringan;charset=utf8mb4';
            self::$connection = new PDO($dsn, 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }

        return self::$connection;
    }
}
?>
