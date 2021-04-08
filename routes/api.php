<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Zttp\Zttp;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/weather', function () {
    $apiKey = config('services.weather.key');
    $location = request('location');

    $response = Zttp::get("https://api.weatherapi.com/v1/forecast.json?key=$apiKey&q=$location&days=10&aqi=no&alerts=no");
    return $response->json();
});
