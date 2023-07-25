<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Log; // Import the Log facade

class JwtAuthService
{
    private $apiKey;
    private $secretKey;

    public function __construct()
    {
        $this->apiKey = env('OMS_API_KEY') ;
        $this->secretKey = env('OMS_SECRET_KEY');
    }

    public function generateJwtToken()
    {
        $currentTime = time();
        $expirationTime = $currentTime + 3600; // Token expiration time (1 hour from now)

        $payload = array(
            "iss" => "your_issuer",     // Replace with your issuer name
            "aud" => "your_audience",   // Replace with your audience
            "iat" => $currentTime,
            "exp" => $expirationTime,
            "sub" => "your_subject",    // Replace with your subject
            "apiKey" => $this->apiKey,
            
        );

                // Generate the JWT token
                $token = JWT::encode($payload, $this->secretKey, 'HS256');

                // Log the generated JWT token for debugging purposes
                Log::info('Generated JWT Token: ' . $token);
        
                return $token;
    }
}
