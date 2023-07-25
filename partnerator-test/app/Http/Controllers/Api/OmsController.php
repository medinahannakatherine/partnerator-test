<?php 

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\HttpService;
use App\Services\JWTAuthService; // Add the JwtAuthService namespace
use Illuminate\Support\Facades\Log; // Import the Log facade

class OmsController extends Controller
{
    protected $httpService;
    protected $jwtAuthService; // Add the JwtAuthService property

    public function __construct(HttpService $httpService, JwtAuthService $jwtAuthService)
    {
        $this->httpService = $httpService;
        $this->jwtAuthService = $jwtAuthService;
    }

    public function getRates(Request $request)
    {
        $token = $this->jwtAuthService->generateJwtToken();

        // Set the headers for the POST request
        $headers = [
            'Authorization' => 'Bearer ' . $token,
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
        // Log the response for debugging purposes
        Log::info('OMS API Response:', ['response' => $response->json()]);
        
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

        public function getDates(Request $request)
    {
        $token = $this->jwtAuthService->generateJwtToken();

        // Set the headers for the POST request
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ];

        // Prepare the payload
        $data = [
            "data" => [
                "attributes" => $request->all()
            ]
        ];

        // Make a POST request with the authentication header and payload using the HttpService instance
        $response = $this->httpService->post('https://api.staging.quadx.xyz/v2/orders/estimates/dates', $data, $headers);

        // Check if the request was successful
        if ($response->successful()) {
            // Get the JSON response
            $responseData = $response->json();

            // Extract and return the shipping fee from the JSON response
            // Assuming the pickup date and estimated delivery dates are available in the 'data' key
            $pickupDate = $responseData['data']['attributes']['pickup_date'];
            $estimatedDeliveryDate = $responseData['data']['attributes']['estimated_delivery_date'];

            return response()->json([
                'pickupDate' => $pickupDate,
                'estimatedDeliveryDate' => $estimatedDeliveryDate,
            ]);

        } else {
            // Handle the case where the request was not successful
            return response()->json(['error' => 'Failed to retrieve estimated pick up and delivery dates'], 500);
        }
    }


    public function getRatesDates(Request $request) {
        $estimateDatesResponse = $this->getDates($request);
        $estimateRatesResponse = $this->getRates($request);

        // Check if both responses are successful
        if ($estimateDatesResponse->getStatusCode() == 200 && $estimateRatesResponse->getStatusCode() == 200) {
            // Get the JSON data from each response as objects
            $estimateDatesData = $estimateDatesResponse->getData();
            $estimateRatesData = $estimateRatesResponse->getData();

            // Access object properties using the correct syntax
            $pickupDate = $estimateDatesData->pickupDate;
            $estimatedDeliveryDate = $estimateDatesData->estimatedDeliveryDate;
            $shippingFee = $estimateRatesData->shipping_fee;

            // Merge the data into a single object
            $mergedData = (object) [
                'pickupDate' => $pickupDate,
                'estimatedDeliveryDate' => $estimatedDeliveryDate,
                'shippingFee' => $shippingFee,
            ];

            return response()->json($mergedData);
        } else {
            // Handle the case where one or both of the requests were not successful
            return response()->json(['error' => 'Failed to retrieve estimates'], 500);
        }
    }

}
