<?php
// projet-backend/test-auth.php

$token = "W10.eyJpZCI6MSwiZW1haWwiOiJqZWFuLmR1cG9udEBjbHViLmZyIiwibm9tIjoiRHVwb250IiwicHJlbm9tIjoiSmVhbiIsInJvbGUiOiJlbnRyYWluZXVyIiwiZXhwIjoxNzc0NzQzNDQ1fQ.pecjT6SoD1le7PqKt2kRkH2Tk7nMHzVw5R4cztZQ1-o";

$ch = curl_init("http://localhost/projet_r401_toc/gestion_equipe_R401/projet-apiAuthentification/auth.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n";
if ($curlError) {
    echo "Curl Error: " . $curlError . "\n";
}