<?php
/**
 * Лабораторная работа №4: подключение к БД и выборка данных для сайта.
 */

declare(strict_types=1);

/**
 * Подключение к MySQL (PDO). Повторные вызовы возвращают то же соединение.
 */
function getDb(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $configPath = __DIR__ . "/config.php";
    if (!is_readable($configPath)) {
        throw new RuntimeException(
            "Создайте файл config.php на основе config.example.php и задайте параметры MySQL."
        );
    }
    /** @var array<string, string> $c */
    $c = require $configPath;

    $dsn = sprintf(
        "mysql:host=%s;port=%s;dbname=%s;charset=%s",
        $c["db_host"],
        $c["db_port"],
        $c["db_name"],
        $c["db_charset"]
    );

    $pdo = new PDO($dsn, $c["db_user"], $c["db_pass"], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}

/**
 * Список журналов для страницы каталога (таблица magazines).
 *
 * @return list<array{id:int,title:string,href:string,cover_image:string,data_search:string,sort_order:int}>
 */
function getMagazines(): array
{
    $sql = "SELECT id, title, href, cover_image, data_search, sort_order
            FROM magazines
            ORDER BY sort_order ASC, id ASC";
    $stmt = getDb()->query($sql);
    return $stmt->fetchAll();
}
