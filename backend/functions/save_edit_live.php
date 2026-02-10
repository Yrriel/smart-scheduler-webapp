<?php
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['schedules'])) {
    http_response_code(400);
    exit('Invalid payload');
}

file_put_contents(
    __DIR__ . '/edit-output.json',
    json_encode($data, JSON_PRETTY_PRINT)
);

echo json_encode(['ok'=>true]);
