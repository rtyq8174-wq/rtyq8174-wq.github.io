<?php
/**
 * JSON-каталог журналов для автообновления list.php без перезагрузки страницы.
 */

declare(strict_types=1);

require_once __DIR__ . "/script.php";

header("Content-Type: application/json; charset=utf-8");

if (($_SERVER["REQUEST_METHOD"] ?? "GET") !== "GET") {
    http_response_code(405);
    echo json_encode(["ok" => false, "message" => "Метод не разрешён.", "items" => []], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $items = getMagazines();
    echo json_encode(["ok" => true, "items" => $items], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(
        [
            "ok" => false,
            "message" => "Не удалось загрузить каталог.",
            "items" => [],
        ],
        JSON_UNESCAPED_UNICODE
    );
}
