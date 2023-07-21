<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class HttpService
{
    public function post(string $url, $payload = null, array $headers = [])
    {
        return Http::withHeaders($headers)->post($url, $payload);
    }

}