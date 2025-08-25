<?php

// Set headers to allow cross-origin requests (CORS) from any domain.
// This is important if your frontend is hosted on a different domain than your PHP backend.
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the raw POST data, which should be a JSON string from the frontend.
    $data = file_get_contents("php://input");

    // Decode the JSON data into a PHP associative array.
    $request_data = json_decode($data, true);

    // Validate if the required fields 'base' and 'height' exist and are numeric.
    if (isset($request_data['base']) && is_numeric($request_data['base']) &&
        isset($request_data['height']) && is_numeric($request_data['height'])) {
        
        $base = $request_data['base'];
        $height = $request_data['height'];

        // Calculate the area of the triangle.
        $area = ($base * $height) / 2;

        // Prepare a successful JSON response.
        http_response_code(200); // OK
        echo json_encode(array(
            "success" => true,
            "message" => "Area calculated successfully.",
            "area" => $area
        ));
    } else {
        // If data is missing or invalid, prepare an error response.
        http_response_code(400); // Bad Request
        echo json_encode(array(
            "success" => false,
            "message" => "Invalid input. Please provide numeric 'base' and 'height'."
        ));
    }
} else {
    // If the request method is not POST, prepare an error response.
    http_response_code(405); // Method Not Allowed
    echo json_encode(array(
        "success" => false,
        "message" => "Method not allowed. Only POST requests are accepted."
    ));
}

