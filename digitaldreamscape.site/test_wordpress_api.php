<?php
echo "Testing basic WordPress API connectivity...\n";

// Test basic WordPress REST API
$tests = [
    'https://digitaldreamscape.site/wp-json/wp/v2/posts?per_page=1' => 'WordPress Posts API',
    'https://digitaldreamscape.site/wp-json/' => 'WordPress API Root',
    'https://digitaldreamscape.site/' => 'Site Homepage'
];

foreach ($tests as $url => $description) {
    echo "\nTesting: {$description}\n";
    echo "URL: {$url}\n";

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 10,
            'ignore_errors' => true,
            'user_agent' => 'DigitalDreamscape-Test/1.0'
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ]);

    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        echo "❌ No response\n";
    } else {
        $http_code = 'unknown';
        if (isset($http_response_header[0])) {
            if (preg_match('/HTTP\/\d+\.\d+\s+(\d+)/', $http_response_header[0], $matches)) {
                $http_code = $matches[1];
            }
        }

        echo "✅ HTTP {$http_code}\n";

        if ($http_code == '200') {
            $content_length = strlen($response);
            echo "Content length: {$content_length} bytes\n";

            // Check if it's HTML
            if (strpos($response, '<!DOCTYPE') !== false || strpos($response, '<html') !== false) {
                echo "Content type: HTML\n";
                // Extract title if possible
                if (preg_match('/<title[^>]*>(.*?)<\/title>/i', $response, $matches)) {
                    echo "Page title: " . trim($matches[1]) . "\n";
                }
            }
            // Check if it's JSON
            elseif (preg_match('/^\s*[\[\{]/', $response)) {
                echo "Content type: JSON\n";
                $data = json_decode($response, true);
                if ($data !== null) {
                    echo "Valid JSON with " . count($data) . " items\n";
                }
            }
        } else {
            echo "Response preview: " . substr($response, 0, 100) . "...\n";
        }
    }
}

echo "\nTest complete.\n";
?>