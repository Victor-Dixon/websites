<?php
echo "Testing API connectivity...\n";

$url = 'https://digitaldreamscape.site/wp-json/digitaldreamscape/v1/questlines';
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 10,
        'ignore_errors' => true
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false
    ]
]);

$response = file_get_contents($url, false, $context);

if ($response === false) {
    echo "❌ API not accessible\n";
    echo "Possible issues:\n";
    echo "- Site firewall blocking requests\n";
    echo "- API endpoint not configured\n";
    echo "- SSL certificate issues\n";
    echo "- Site is down\n";
} else {
    $http_code = 'unknown';
    if (isset($http_response_header[0])) {
        if (preg_match('/HTTP\/\d+\.\d+\s+(\d+)/', $http_response_header[0], $matches)) {
            $http_code = $matches[1];
        }
    }

    echo "✅ API responded with HTTP {$http_code}\n";

    if ($http_code == '200') {
        $data = json_decode($response, true);
        if ($data !== null) {
            echo "✅ Valid JSON response\n";
            echo "Questlines available: " . count($data) . "\n";
            if (!empty($data)) {
                echo "Sample questline: " . key($data) . "\n";
            }
        } else {
            echo "❌ Invalid JSON response\n";
        }
    } else {
        echo "❌ HTTP error: {$http_code}\n";
        echo "Response: " . substr($response, 0, 200) . "...\n";
    }
}

echo "\nTest complete.\n";
?>