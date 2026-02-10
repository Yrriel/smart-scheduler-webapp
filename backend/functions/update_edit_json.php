<?php
$editFile = __DIR__ . '/edit-output.json';

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['index'])) {
    http_response_code(400);
    exit;
}

$json = json_decode(file_get_contents($editFile), true);
$idx = (int)$data['index'];

if (!isset($json['schedules'][$idx])) {
    http_response_code(404);
    exit;
}

$json['schedules'][$idx]['room']       = $data['room'];
$json['schedules'][$idx]['day']        = $data['day'];
$json['schedules'][$idx]['time_start'] = $data['time_start'];
$json['schedules'][$idx]['time_end']   = $data['time_end'];

file_put_contents(
    $editFile,
    json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
    LOCK_EX
);

echo json_encode(['status' => 'ok']);