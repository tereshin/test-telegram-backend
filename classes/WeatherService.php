<?php

class WeatherService {
    
    private $apiUrl;
    
    public function __construct() {
        $this->apiUrl = OPEN_METEO_API;
    }
    
    /**
     * Получение погоды для города
     */
    public function getWeather($lat, $lon, $timezone) {
        $params = array(
            'latitude' => $lat,
            'longitude' => $lon,
            'current_weather' => 'true',
            'hourly' => 'temperature_2m,relative_humidity_2m,surface_pressure,precipitation,weather_code',
            'timezone' => $timezone,
            'forecast_days' => 1
        );
        
        $url = $this->apiUrl . '?' . http_build_query($params);
        
        $options = array(
            'http' => array(
                'method' => 'GET',
                'timeout' => 10
            )
        );
        
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        
        if ($response === FALSE) {
            return null;
        }
        
        $data = json_decode($response, true);
        
        if (!$data || !isset($data['current_weather'])) {
            return null;
        }
        
        return $this->formatWeatherData($data, $timezone);
    }
    
    /**
     * Форматирование данных о погоде
     */
    private function formatWeatherData($data, $timezone) {
        $current = $data['current_weather'];
        $hourly = $data['hourly'];
        
        // Получаем текущий час
        $currentTime = new DateTime('now', new DateTimeZone($timezone));
        $currentHour = (int)$currentTime->format('H');
        
        // Находим индекс для текущего часа
        $hourIndex = 0;
        for ($i = 0; $i < count($hourly['time']); $i++) {
            $hourTime = new DateTime($hourly['time'][$i]);
            if ($hourTime->format('H') == $currentHour) {
                $hourIndex = $i;
                break;
            }
        }
        
        $temperature = round($current['temperature']);
        $humidity = isset($hourly['relative_humidity_2m'][$hourIndex]) ? 
                   round($hourly['relative_humidity_2m'][$hourIndex]) : 'Н/Д';
        $pressure = isset($hourly['surface_pressure'][$hourIndex]) ? 
                   round($hourly['surface_pressure'][$hourIndex]) : 'Н/Д';
        $precipitation = isset($hourly['precipitation'][$hourIndex]) ? 
                        $hourly['precipitation'][$hourIndex] : 0;
        $weatherCode = $current['weathercode'];
        
        return array(
            'temperature' => $temperature,
            'humidity' => $humidity,
            'pressure' => $pressure,
            'precipitation' => $precipitation,
            'weather_description' => $this->getWeatherDescription($weatherCode),
            'wind_speed' => round($current['windspeed']),
            'current_time' => $currentTime->format('d.m.Y H:i')
        );
    }
    
    /**
     * Описание погоды по коду WMO
     */
    private function getWeatherDescription($code) {
        $descriptions = array(
            0 => '☀️ Ясно',
            1 => '🌤️ Преимущественно ясно',
            2 => '⛅ Переменная облачность',
            3 => '☁️ Пасмурно',
            45 => '🌫️ Туман',
            48 => '🌫️ Изморозь',
            51 => '🌦️ Слабая морось',
            53 => '🌦️ Умеренная морось',
            55 => '🌦️ Сильная морось',
            56 => '🌨️ Слабая ледяная морось',
            57 => '🌨️ Сильная ледяная морось',
            61 => '🌧️ Слабый дождь',
            63 => '🌧️ Умеренный дождь',
            65 => '🌧️ Сильный дождь',
            66 => '🌨️ Слабый ледяной дождь',
            67 => '🌨️ Сильный ледяной дождь',
            71 => '❄️ Слабый снег',
            73 => '❄️ Умеренный снег',
            75 => '❄️ Сильный снег',
            77 => '❄️ Снежные зерна',
            80 => '🌦️ Слабые ливни',
            81 => '🌦️ Умеренные ливни',
            82 => '🌦️ Сильные ливни',
            85 => '❄️ Слабые снежные ливни',
            86 => '❄️ Сильные снежные ливни',
            95 => '⛈️ Гроза',
            96 => '⛈️ Гроза с градом',
            99 => '⛈️ Сильная гроза с градом'
        );
        
        return isset($descriptions[$code]) ? $descriptions[$code] : '🌥️ Неизвестно';
    }
}

?> 