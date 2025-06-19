<?php

require_once 'config.php';

// URL вашего webhook (замените на ваш домен)
$webhook_url = WEBHOOK_URL;

// Устанавливаем webhook
$url = TELEGRAM_API . '/setWebhook';
$data = array(
    'url' => $webhook_url,
    'allowed_updates' => json_encode(['message', 'callback_query'])
);

$options = array(
    'http' => array(
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($data)
    )
);

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);
$response = json_decode($result, true);

if ($response['ok']) {
    echo "✅ Webhook успешно установлен!\n";
    echo "URL: " . $webhook_url . "\n";
} else {
    echo "❌ Ошибка установки webhook:\n";
    echo $result . "\n";
}

?> 