<?php

echo "🔍 Проверка системы Telegram Weather Bot\n\n";

// Проверка версии PHP
echo "📋 Проверка версии PHP: ";
$phpVersion = phpversion();
echo $phpVersion;

if (version_compare($phpVersion, '5.6.0', '>=')) {
    echo " ✅\n";
} else {
    echo " ❌ (требуется PHP 5.6 или выше)\n";
}

// Проверка расширений PHP
echo "\n📦 Проверка расширений PHP:\n";

$extensions = array('json', 'curl', 'openssl');
foreach ($extensions as $ext) {
    echo "  - " . $ext . ": ";
    if (extension_loaded($ext)) {
        echo "✅\n";
    } else {
        echo "❌ (не установлено)\n";
    }
}

// Проверка файлов
echo "\n📁 Проверка файлов проекта:\n";

$files = array(
    __DIR__ . '/../config.php' => 'Конфигурация',
    __DIR__ . '/../classes/TelegramBot.php' => 'Telegram API класс',
    __DIR__ . '/../classes/WeatherService.php' => 'Weather API класс',
    __DIR__ . '/../bot.php' => 'Основной файл бота',
    __DIR__ . '/../set_webhook.php' => 'Установка webhook'
);

foreach ($files as $file => $description) {
    echo "  - " . $file . " (" . $description . "): ";
    if (file_exists($file)) {
        echo "✅\n";
    } else {
        echo "❌ (отсутствует)\n";
    }
}

// Проверка конфигурации
echo "\n⚙️ Проверка конфигурации:\n";

require_once __DIR__ . '/../config.php';

echo "  - BOT_TOKEN: ";
if (defined('BOT_TOKEN') && BOT_TOKEN !== 'YOUR_BOT_TOKEN_HERE') {
    echo "✅ (настроен)\n";
} else {
    echo "❌ (не настроен - замените YOUR_BOT_TOKEN_HERE в config.php)\n";
}

echo "  - Количество городов: ";
if (isset($CITIES) && count($CITIES) == 10) {
    echo "✅ (" . count($CITIES) . ")\n";
} else {
    echo "❌ (ожидается 10 городов)\n";
}

// Тест API погоды
echo "\n🌤️ Проверка API погоды:\n";

require_once __DIR__ . '/../classes/WeatherService.php';

$weatherService = new WeatherService();
$testCity = 'Москва';
$cityData = $CITIES[$testCity];

echo "  - Подключение к Open Meteo API: ";
$weather = $weatherService->getWeather(
    $cityData['lat'], 
    $cityData['lon'], 
    $cityData['timezone']
);

if ($weather) {
    echo "✅ (данные получены)\n";
} else {
    echo "❌ (ошибка подключения)\n";
}

// Проверка прав доступа
echo "\n🔒 Проверка прав доступа:\n";

echo "  - Запись в текущую директорию: ";
$testFile = 'test_write.tmp';
if (file_put_contents($testFile, 'test') !== false) {
    echo "✅\n";
    unlink($testFile); // Удаляем тестовый файл
} else {
    echo "❌ (нет прав на запись)\n";
}

echo "\n📋 Резюме:\n";
echo "Если все проверки прошли успешно (✅), то бот готов к работе!\n";
echo "Не забудьте:\n";
echo "1. Настроить BOT_TOKEN в config.php\n";
echo "2. Загрузить файлы на сервер с HTTPS\n";
echo "3. Выполнить set_webhook.php для установки webhook\n";

?> 