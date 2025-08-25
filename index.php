<?php

// A list of all allowed origins.
// You can add as many domains as you need to this array.
$allowed_origins = array(
    'http://localhost:8080', // Your local development server
    'https://my-production-site.com', // Example of a production domain
    'https://another-trusted-site.net' // Another trusted site
);

// Get the origin of the current request.
$request_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

// Check if the request origin is in our list of allowed origins.
if (in_array($request_origin, $allowed_origins)) {
    // If it is, set the Access-Control-Allow-Origin header to the specific origin.
    header("Access-Control-Allow-Origin: $request_origin");
} else {
    // If the origin is not in the allowed list, you can optionally exit
    // or handle the error here. For this case, we'll let the script continue,
    // and the CORS header simply won't be set, which will block the request.
    // However, the best practice is to exit with a 403 Forbidden response.
    // For this example, we'll just continue and let the browser handle it.
}

// All other CORS headers are still important for preflight requests.
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Include OPTIONS for preflight
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle the preflight OPTIONS request first.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Respond with 200 OK
    exit(); // Stop script execution
}

// Check if the request method is POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = file_get_contents("php://input");
    $request_data = json_decode($data, true);

    if (isset($request_data['base']) && is_numeric($request_data['base']) &&
        isset($request_data['height']) && is_numeric($request_data['height'])) {
        
        $base = $request_data['base'];
        $height = $request_data['height'];
        $area = ($base * $height) / 2;

        http_response_code(200); // OK
        echo json_encode(array(
            "success" => true,
            "message" => "Area calculated successfully.",
            "area" => $area
        ));
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(array(
            "success" => false,
            "message" => "Invalid input. Please provide numeric 'base' and 'height'."
        ));
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(array(
        "success" => false,
        "message" => "Method not allowed. Only POST and OPTIONS requests are accepted."
    ));
}
?>
