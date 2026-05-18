<?php
/**
 * Вход пользователя: проверка email/пароля по таблице site_users.
 * В ответе только имя, email, роль (без пароля и хеша).
 */

declare(strict_types=1);

require_once __DIR__ . "/script.php";

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["ok" => false, "message" => "Метод не разрешён."], JSON_UNESCAPED_UNICODE);
    exit;
}

$emailRaw = trim((string) ($_POST["email"] ?? ""));
$email = function_exists("mb_strtolower") ? mb_strtolower($emailRaw, "UTF-8") : strtolower($emailRaw);
$password = (string) ($_POST["password"] ?? "");

if ($email === "" || $password === "") {
    echo json_encode(["ok" => false, "message" => "Введите email и пароль."], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $pdo = getDb();
    $stmt = $pdo->prepare("SELECT id, name, email, password_hash, role FROM site_users WHERE email = :email LIMIT 1");
    $stmt->execute([":email" => $email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(
        [
            "ok" => false,
            "message" => "Ошибка подключения к базе. Проверьте config.php и таблицу site_users.",
        ],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

if ($row === false || !password_verify($password, (string) $row["password_hash"])) {
    echo json_encode(["ok" => false, "message" => "Неверный email или пароль."], JSON_UNESCAPED_UNICODE);
    exit;
}

$user = [
    "name" => $row["name"],
    "email" => $row["email"],
    "role" => $row["role"],
];

echo json_encode(
    [
        "ok" => true,
        "message" => "Добро пожаловать, " . $row["name"] . "!",
        "user" => $user,
    ],
    JSON_UNESCAPED_UNICODE
);
