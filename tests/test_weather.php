<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../classes/WeatherService.php';

echo "🧪 Тестирование работы с Open Meteo API\n\n";

$weatherService = new WeatherService();

// Тестируем на примере Москвы
$testCity = 'Москва';
$cityData = $CITIES[$testCity];

echo "Получаем погоду для города: " . $testCity . "\n";
echo "Координаты: " . $cityData['lat'] . ", " . $cityData['lon'] . "\n";
echo "Часовой пояс: " . $cityData['timezone'] . "\n\n";

$weather = $weatherService->getWeather(
    $cityData['lat'], 
    $cityData['lon'], 
    $cityData['timezone']
);

if ($weather) {
    echo "✅ Успешно получены данные о погоде:\n\n";
    echo "📅 Время: " . $weather['current_time'] . "\n";
    echo "🌡️ Температура: " . $weather['temperature'] . "°C\n";
    echo "💧 Влажность: " . $weather['humidity'] . "%\n";
    echo "🏔️ Давление: " . $weather['pressure'] . " гПа\n";
    echo "💨 Скорость ветра: " . $weather['wind_speed'] . " км/ч\n";
    echo "🌧️ Осадки: " . $weather['precipitation'] . " мм\n";
    echo "🌤️ Состояние: " . $weather['weather_description'] . "\n";
} else {
    echo "❌ Ошибка получения данных о погоде\n";
}

?> 