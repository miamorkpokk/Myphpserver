<?php
// A list of all allowed origins for production
$allowed_origins = array(
    'http://localhost:8080',
    'https://my-server-calculator.onrender.com', // Your frontend URL
    'https://my-production-site.com' // Keep other domains you might need
);

// Get the origin of the current request
$request_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

// Check if the request origin is in our list of allowed origins
if (in_array($request_origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $request_origin");
} else {
    // For unknown origins, you can either block or allow with restrictions
    // This allows requests from any origin (useful during development)
    // header("Access-Control-Allow-Origin: *");
    
    // For production, it's better to be restrictive
    http_response_code(403);
    echo json_encode(array(
        "success" => false, 
        "message" => "Origin not allowed. Please check your CORS configuration."
    ));
    exit();
}

// All other CORS headers
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

// Handle the preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = file_get_contents("php://input");
    $request_data = json_decode($data, true);

    // Input validation
    if (!$request_data || !is_array($request_data)) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "Invalid JSON data received."
        ));
        exit();
    }
    
    if (!isset($request_data['base']) || !isset($request_data['height'])) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "Missing parameters. Please provide both 'base' and 'height'."
        ));
        exit();
    }
    
    if (!is_numeric($request_data['base']) || !is_numeric($request_data['height'])) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "Invalid input. Please provide numeric values for 'base' and 'height'."
        ));
        exit();
    }
    
    $base = floatval($request_data['base']);
    $height = floatval($request_data['height']);
    
    // Validate that values are positive
    if ($base <= 0 || $height <= 0) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "Base and height must be positive numbers."
        ));
        exit();
    }
    
    // Calculate area
    $area = ($base * $height) / 2;

    // Return successful response
    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "message" => "Area calculated successfully.",
        "area" => $area,
        "base" => $base,
        "height" => $height
    ));
    
} else {
    http_response_code(405);
    echo json_encode(array(
        "success" => false,
        "message" => "Method not allowed. Only POST and OPTIONS requests are accepted."
    ));
}
?>
