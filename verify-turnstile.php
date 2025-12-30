
<?php
// CRITICAL: Replace this with your actual Secret Key from the Cloudflare dashboard
$secret_key = '0x4AAAAAACJw0F0s3SJuQLYoXCLk6ZUcyZQ';

// Cloudflare sends the token in a POST parameter named 'response'
$token = $_POST['response'] ?? '';

if (empty($token)) {
    echo json_encode(['success' => false, 'error-codes' => ['missing-input-response']]);
    exit();
}

// Prepare the data to send to the Cloudflare Siteverify API
$data = [
    'secret' => $secret_key,
    'response' => $token,
    // Optional: Include the visitor's IP address for enhanced security (recommended)
    'remoteip' => $_SERVER['REMOTE_ADDR'] ?? null,
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ],
];

$context  = stream_context_create($options);
// Call the mandatory Siteverify API endpoint
$response = file_get_contents('challenges.cloudflare.com', false, $context);

if ($response === FALSE) {
    // Handle API call failure
    echo json_encode(['success' => false, 'error-codes' => ['api-call-failed']]);
    exit();
}

$responseData = json_decode($response, true);

// Return the success status back to the JavaScript frontend
echo json_encode(['success' => $responseData['success']]);

// The frontend JavaScript will handle the final redirect once it receives this JSON response.
?>
