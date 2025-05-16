<?php
define('BOT_TOKEN', '7606552281:AAH9PmdYp34jFf9zqqZbY-8rxR4XZy-hBM0'); // Замените на токен вашего бота

function validateTelegramInitData($initData) {
    $checkHash = '';
    $data = [];
    foreach (explode('&', $initData) as $item) {
        if (strpos($item, 'hash=') === 0) {
            $checkHash = substr($item, 5);
            continue;
        }
        $data[] = $item;
    }

    sort($data);
    $dataCheckString = implode("\n", $data);
    $secretKey = hash_hmac('sha256', BOT_TOKEN, 'WebAppData', true);
    $hash = hash_hmac('sha256', $dataCheckString, $secretKey);

    return hash_equals($hash, $checkHash);
}

// Получение POST-данных
$input = json_decode(file_get_contents('php://input'), true);
$initData = $input['initData'] ?? '';
$taps = $input['taps'] ?? 0;

if (!validateTelegramInitData($initData)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid signature']);
    exit;
}

// TODO: здесь вы можете сохранять $taps и данные пользователя в базу
file_put_contents('data.json', json_encode([
  'timestamp' => time(),
  'taps' => $taps,
  'initData' => $initData
], JSON_PRETTY_PRINT));

echo json_encode(['status' => 'ok']);
