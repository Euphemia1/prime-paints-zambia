<?php
/**
 * Prime Paints Zambia - Payment Processing Bridge
 * Handles MoneyUnify (Sparco) API requests
 */

header('Content-Type: application/json');

// Get POST data
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

// Config - Replace with actual credentials
// IMPORTANT: User should update this with their real MoneyUnify Auth ID
$auth_id = "YOUR_MONEYUNIFY_AUTH_ID_HERE"; 
$api_url = "https://api.moneyunify.one/payments/request";

// Prepare parameters for x-www-form-urlencoded
$postFields = [
    'auth_id' => $auth_id,
    'first_name' => $data['firstName'] ?? 'Customer',
    'last_name' => $data['lastName'] ?? '',
    'email' => $data['email'] ?? '',
    'phone' => $data['phone'] ?? '',
    'amount' => $data['amount'] ?? '1280.00',
    'detail' => $data['detail'] ?? 'Prime Paints Purchase',
    'reference' => 'PP-' . time() // Generate unique reference
];

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    echo json_encode([
        'success' => false, 
        'message' => 'Payment initiation failed: ' . $err
    ]);
} else {
    // Return MoneyUnify response directly or parse it
    // Expecting response like {"status":"success", "message":"Payment Request Initiated", "reference":"..."}
    echo $response;
}
?>
