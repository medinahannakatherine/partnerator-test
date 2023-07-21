<?php 

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\HttpService;

class OmsController extends Controller
{
    protected $httpService;

    public function __construct(HttpService $httpService)
    {
        $this->httpService = $httpService;
    }

    public function getRates(Request $request)
    {
        // Set the headers for the POST request
        $headers = [
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMDgwMTVjZGI4OWMxNjFiYzIiLCJpYXQiOjE2ODg2MTM3NTEsImp0aSI6MTY4ODYxMzc1MX0.GLtXpd03Gdw2GXHqYOvNGfYBNAVWcrXiJzALKyE0NMA',
            'Accept' => 'application/json',
        ];

        // Prepare the payload
        $data = [
            "data" => [
                "attributes" => $request->all()
            ]
        ];

        // Make a POST request with the authentication header and payload using the HttpService instance
        $response = $this->httpService->post('https://api.staging.quadx.xyz/v2/orders/estimates/rates', $data, $headers);

        // Check if the request was successful
        if ($response->successful()) {
            // Get the JSON response
            $responseData = $response->json();

            // Extract and return the shipping fee from the JSON response
            // Assuming the shipping fee is available in the 'data' key
            $shippingFee = $responseData['data']['attributes']['shipping_fee'];

            return response()->json(['shipping_fee' => $shippingFee]);
        } else {
            // Handle the case where the request was not successful
            return response()->json(['error' => 'Failed to retrieve shipping fee'], 500);
        }
    }
}
