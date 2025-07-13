<?php
function sendPushNotification(array $playerIds, string $title, string $message): string {
    $fields = [
        'app_id' => '9ac74085-50d8-4e85-987b-53ca46429f8e', // ✅ Your OneSignal App ID
        'include_player_ids' => $playerIds,
        'headings' => ['en' => $title],
        'contents' => ['en' => $message],
    ];

    $headers = [
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic os_v2_app_tldubbkq3bhilgd3kpfemqu7rzpj4bavhtfezo43g2fbzw5gncgd3fzw3l772odwqaqxulozfszvqwvz5moltjogqjnh6krckhgkjua', // ✅ Your REST API Key
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    curl_setopt($ch, CURLOPT_POST, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // Optional: log response to file
    file_put_contents(__DIR__ . '/push_log.txt', "[" . date('Y-m-d H:i:s') . "] " . $response . PHP_EOL, FILE_APPEND);

    // Return response with error info if failed
    if ($httpCode !== 200) {
        return json_encode([
            'status' => 'error',
            'http_code' => $httpCode,
            'curl_error' => $curlError,
            'response' => $response
        ]);
    }

    return $response;
}
