<?php
/**
 * Сохранение данных формы обратной связи в таблицу feedback_messages.
 * Ожидает POST; при AJAX возвращает JSON, иначе редирект на главную.
 */

declare(strict_types=1);

require_once __DIR__ . "/script.php";

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["ok" => false, "message" => "Метод не разрешён."], JSON_UNESCAPED_UNICODE);
    exit;
}

$name = trim((string) ($_POST["name"] ?? ""));
$email = trim((string) ($_POST["email"] ?? ""));
$phone = trim((string) ($_POST["phone"] ?? ""));
$message = trim((string) ($_POST["message"] ?? ""));

if ($name === "" || $email === "" || $phone === "" || $message === "") {
    echo json_encode(["ok" => false, "message" => "Заполните все обязательные поля формы."], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email)) {
    echo json_encode(["ok" => false, "message" => "Укажите корректный адрес электронной почты."], JSON_UNESCAPED_UNICODE);
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
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(
        [
            "ok" => false,
            "message" =>
                "Не удалось сохранить сообщение. Проверьте config.php, запуск через MAMP и импорт sql/schema.sql.",
        ],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

echo json_encode(
    [
        "ok" => true,
        "message" => "Спасибо! Ваше сообщение сохранено и будет рассмотрено редакцией.",
    ],
    JSON_UNESCAPED_UNICODE
);
