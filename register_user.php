<?php
/**
 * Регистрация пользователя: запись в таблицу site_users (пароль — password_hash).
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
$emailRaw = trim((string) ($_POST["email"] ?? ""));
$email = function_exists("mb_strtolower") ? mb_strtolower($emailRaw, "UTF-8") : strtolower($emailRaw);
$password = (string) ($_POST["password"] ?? "");
$passwordConfirm = (string) ($_POST["password_confirm"] ?? "");
$role = trim((string) ($_POST["role"] ?? ""));

function register_email_valid(string $s): bool
{
    $at = strpos($s, "@");
    if ($at === false || $at <= 0 || $at === strlen($s) - 1) {
        return false;
    }
    $local = substr($s, 0, $at);
    $domain = substr($s, $at + 1);
    if ($local === "" || $domain === "") {
        return false;
    }
    if (!preg_match('/^[a-zA-Z0-9._+-]+$/', $local)) {
        return false;
    }
    $parts = explode(".", $domain);
    if (count($parts) < 2) {
        return false;
    }
    foreach ($parts as $part) {
        if ($part === "" || !preg_match('/^[a-zA-Z]+$/', $part)) {
            return false;
        }
    }
    return true;
}

function register_password_valid(string $pwd): bool
{
    if (strlen($pwd) < 6) {
        return false;
    }
    return (bool) preg_match('/[a-zA-Zа-яА-ЯёЁ]/u', $pwd) && (bool) preg_match('/\d/', $pwd);
}

if ($name === "") {
    echo json_encode(["ok" => false, "message" => "Укажите имя."], JSON_UNESCAPED_UNICODE);
    exit;
}
if ($emailRaw === "") {
    echo json_encode(["ok" => false, "message" => "Укажите email."], JSON_UNESCAPED_UNICODE);
    exit;
}
if (!register_email_valid($emailRaw)) {
    echo json_encode(
        [
            "ok" => false,
            "message" =>
                "Введите корректный email: после @ домен — только латинские буквы, части через точку (например, user@mail.ru).",
        ],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}
if ($password === "") {
    echo json_encode(["ok" => false, "message" => "Укажите пароль."], JSON_UNESCAPED_UNICODE);
    exit;
}
if (!register_password_valid($password)) {
    echo json_encode(
        [
            "ok" => false,
            "message" => "Пароль не короче 6 символов и содержит букву и цифру.",
        ],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}
if ($password !== $passwordConfirm) {
    echo json_encode(["ok" => false, "message" => "Пароли не совпадают."], JSON_UNESCAPED_UNICODE);
    exit;
}
if ($role === "") {
    echo json_encode(["ok" => false, "message" => "Выберите роль."], JSON_UNESCAPED_UNICODE);
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);
if ($hash === false) {
    echo json_encode(["ok" => false, "message" => "Ошибка хеширования пароля."], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $pdo = getDb();
    $stmt = $pdo->prepare(
        "INSERT INTO site_users (name, email, password_hash, role) VALUES (:name, :email, :hash, :role)"
    );
    $stmt->execute([
        ":name" => $name,
        ":email" => $email,
        ":hash" => $hash,
        ":role" => $role,
    ]);
} catch (PDOException $e) {
    $sqlState = (string) ($e->errorInfo[0] ?? "");
    if ($sqlState === "23000" || str_contains($e->getMessage(), "Duplicate")) {
        echo json_encode(["ok" => false, "message" => "Пользователь с таким email уже зарегистрирован."], JSON_UNESCAPED_UNICODE);
        exit;
    }
    http_response_code(500);
    echo json_encode(
        [
            "ok" => false,
            "message" => "Не удалось сохранить регистрацию. Проверьте БД и выполните sql/add_site_users_table.sql при необходимости.",
        ],
        JSON_UNESCAPED_UNICODE
    );
    exit;
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "message" => "Ошибка сервера при регистрации."], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(["ok" => true, "message" => "Регистрация успешна! Теперь вы можете войти."], JSON_UNESCAPED_UNICODE);
