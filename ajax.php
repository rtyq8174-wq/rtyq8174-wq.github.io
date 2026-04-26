<?php
/**
 * Лабораторная работа №5: Ajax — GET (полный список сообщений), POST (сохранение формы + полный список).
 */

declare(strict_types=1);

require_once __DIR__ . "/script.php";

header("Content-Type: application/json; charset=utf-8");

/**
 * @return list<array{id:int|string,name:string,email:string,phone:string,message:string,created_at:string}>
 */
function ajax_fetch_all_feedback(PDO $pdo): array
{
    $sql = "SELECT id, name, email, phone, message,
                   DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS created_at
            FROM feedback_messages
            ORDER BY id DESC";
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows === false ? [] : $rows;
}

$method = $_SERVER["REQUEST_METHOD"] ?? "GET";

if ($method === "GET") {
    try {
        $pdo = getDb();
        $items = ajax_fetch_all_feedback($pdo);
        echo json_encode(["ok" => true, "items" => $items], JSON_UNESCAPED_UNICODE);
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(
            [
                "ok" => false,
                "message" => "Не удалось загрузить сообщения. Проверьте config.php и таблицу feedback_messages.",
                "items" => [],
            ],
            JSON_UNESCAPED_UNICODE
        );
    }
    exit;
}

if ($method !== "POST") {
    http_response_code(405);
    echo json_encode(["ok" => false, "message" => "Метод не разрешён.", "items" => []], JSON_UNESCAPED_UNICODE);
    exit;
}

$name = trim((string) ($_POST["name"] ?? ""));
$email = trim((string) ($_POST["email"] ?? ""));
$phone = trim((string) ($_POST["phone"] ?? ""));
$message = trim((string) ($_POST["message"] ?? ""));

if ($name === "" || $email === "" || $phone === "" || $message === "") {
    echo json_encode(["ok" => false, "message" => "Заполните все обязательные поля формы.", "items" => []], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email)) {
    echo json_encode(["ok" => false, "message" => "Укажите корректный адрес электронной почты.", "items" => []], JSON_UNESCAPED_UNICODE);
    exit;
}

$digits = preg_replace("/\D/", "", $phone);
$phoneOk = false;
if (strlen($digits) === 11 && ($digits[0] === "7" || $digits[0] === "8")) {
    $phoneOk = ($digits[1] ?? "") === "9";
} elseif (strlen($digits) === 10 && ($digits[0] ?? "") === "9") {
    $phoneOk = true;
}
if (!$phoneOk) {
    echo json_encode(
        [
            "ok" => false,
            "message" => "Укажите корректный номер мобильного телефона (например, +79991234567).",
            "items" => [],
        ],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

try {
    $pdo = getDb();
    $stmt = $pdo->prepare(
        "INSERT INTO feedback_messages (name, email, phone, message) VALUES (:name, :email, :phone, :message)"
    );
    $stmt->execute([
        ":name" => $name,
        ":email" => $email,
        ":phone" => $phone,
        ":message" => $message,
    ]);
    $items = ajax_fetch_all_feedback($pdo);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(
        [
            "ok" => false,
            "message" =>
                "Не удалось сохранить сообщение. Проверьте config.php, запуск через MAMP и импорт sql/schema.sql.",
            "items" => [],
        ],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

echo json_encode(
    [
        "ok" => true,
        "message" => "Спасибо! Ваше сообщение сохранено и будет рассмотрено редакцией.",
        "items" => $items,
    ],
    JSON_UNESCAPED_UNICODE
);
