<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WeatherService;

class WeatherController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function show(Request $request)
    {
        $city = $request->query('city', 'Cairo');
        $weatherData = $this->weatherService->getWeather($city);

        return response()->json([
            'city' => $weatherData['name'] . ', ' . $weatherData['sys']['country'], 
            'date' => now()->format('l, d F'),
            'temperature' => $weatherData['main']['temp'],
            'weather' => $weatherData['weather'][0]['description'], 
            'details' => [
                'sunrise' => date('g:i a', $weatherData['sys']['sunrise']), 
                'sunset' => date('g:i a', $weatherData['sys']['sunset']), 
                'wind_speed' => $weatherData['wind']['speed'] . ' km/h', 
                'humidity' => $weatherData['main']['humidity'] . ' %',
                'uv_index' => 'N/A', 
            ],
            'hourly_forecast' => [
                ['time' => 'Now', 'temperature' => $weatherData['main']['temp'], 'weather' => $weatherData['weather'][0]['main']],
                ['time' => '9 am', 'temperature' => $weatherData['main']['temp'] + 4, 'weather' => 'Cloudy'], // مثال
                ['time' => '12 pm', 'temperature' => $weatherData['main']['temp'] + 10, 'weather' => 'Sunny'], // مثال
            ],
        ]);
    }


}