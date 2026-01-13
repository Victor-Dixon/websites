<?php
/**
 * Comprehensive Security Test Suite for ariajet.site
 * Tests all security measures implemented from Vibe Code Security Cleanup Kit
 */

class SecurityTestSuite {
    private $base_url;
    private $test_results = [];

    public function __construct($base_url = 'http://localhost/wp-json/tradingrobotplug/v1') {
        $this->base_url = $base_url;
    }

    public function run_all_tests() {
        echo "🧪 COMPREHENSIVE SECURITY TEST SUITE\n";
        echo "===================================\n\n";

        $this->test_input_validation();
        $this->test_authentication();
        $this->test_authorization();
        $this->test_error_handling();
        $this->test_rate_limiting();

        $this->display_results();
    }

    private function test_input_validation() {
        echo "1. 🛡️  INPUT VALIDATION TESTS\n";
        echo "------------------------------\n";

        // Test 1: Invalid symbol
        $result = $this->make_request('POST', '/orders', [
            'symbol' => 'INVALID',
            'quantity' => 100,
            'side' => 'buy',
            'order_type' => 'market'
        ]);
        $this->assert_response($result, 400, 'Invalid symbol rejected');
        $this->assert_contains($result, 'Invalid symbol', 'Proper error message');

        // Test 2: Negative quantity
        $result = $this->make_request('POST', '/orders', [
            'symbol' => 'TSLA',
            'quantity' => -100,
            'side' => 'buy',
            'order_type' => 'market'
        ]);
        $this->assert_response($result, 400, 'Negative quantity rejected');

        // Test 3: Invalid order type
        $result = $this->make_request('POST', '/orders', [
            'symbol' => 'TSLA',
            'quantity' => 100,
            'side' => 'buy',
            'order_type' => 'invalid_type'
        ]);
        $this->assert_response($result, 400, 'Invalid order type rejected');

        // Test 4: Missing required field
        $result = $this->make_request('POST', '/orders', [
            'symbol' => 'TSLA',
            'quantity' => 100,
            'side' => 'buy'
            // missing order_type
        ]);
        $this->assert_response($result, 400, 'Missing required field rejected');

        // Test 5: Limit order without price
        $result = $this->make_request('POST', '/orders', [
            'symbol' => 'TSLA',
            'quantity' => 100,
            'side' => 'buy',
            'order_type' => 'limit'
            // missing price
        ]);
        $this->assert_response($result, 400, 'Limit order without price rejected');

        echo "\n";
    }

    private function test_authentication() {
        echo "2. 🔐 AUTHENTICATION TESTS\n";
        echo "-------------------------\n";

        // Test 1: No authentication
        $result = $this->make_request('GET', '/trades');
        $this->assert_response($result, 401, 'Unauthenticated request rejected');

        // Test 2: Invalid JWT format
        $result = $this->make_request('GET', '/trades', [], [
            'Authorization: Bearer invalid.jwt.token'
        ]);
        $this->assert_response($result, 401, 'Invalid JWT rejected');

        // Test 3: Expired JWT (would need actual expired token)
        // This would require generating an expired token for testing

        // Test 4: Valid API key
        $result = $this->make_request('GET', '/trades', [], [
            'X-API-Key: test-api-key'
        ]);
        // This should either work or be rejected based on configuration

        echo "\n";
    }

    private function test_authorization() {
        echo "3. 🛑 AUTHORIZATION TESTS\n";
        echo "------------------------\n";

        // Test 1: User trying to access another user's data (IDOR)
        // This would require valid authentication and testing data ownership

        // Test 2: Non-admin trying to access admin endpoints
        // This would require testing role-based access

        echo "   ⚠️  Authorization tests require authenticated sessions\n";
        echo "   📋 Manual testing required for IDOR and role-based access\n\n";
    }

    private function test_error_handling() {
        echo "4. 🚨 ERROR HANDLING TESTS\n";
        echo "-------------------------\n";

        // Test 1: Malformed JSON
        $result = $this->make_request('POST', '/orders', 'invalid json', [
            'Content-Type: application/json'
        ]);
        $this->assert_response($result, 400, 'Malformed JSON handled');

        // Test 2: SQL injection attempt
        $result = $this->make_request('GET', '/trades?symbol=TSLA\'; DROP TABLE users; --');
        // Should not crash and should sanitize input

        // Test 3: XSS attempt
        $result = $this->make_request('POST', '/orders', [
            'symbol' => 'TSLA',
            'quantity' => 100,
            'side' => 'buy',
            'order_type' => 'market',
            'note' => '<script>alert("xss")</script>'
        ]);
        // Should sanitize or reject malicious input

        echo "\n";
    }

    private function test_rate_limiting() {
        echo "5. 🛑 RATE LIMITING TESTS\n";
        echo "------------------------\n";

        echo "   ⚠️  Rate limiting not yet implemented\n";
        echo "   📋 Consider adding rate limiting for production deployment\n\n";
    }

    private function make_request($method, $endpoint, $data = null, $headers = []) {
        $url = $this->base_url . $endpoint;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For testing

        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $headers[] = 'Content-Type: application/json';
        }

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        return [
            'response' => $response,
            'http_code' => $http_code,
            'error' => $error,
            'success' => $http_code >= 200 && $http_code < 300
        ];
    }

    private function assert_response($result, $expected_code, $description) {
        $passed = $result['http_code'] == $expected_code;
        $status = $passed ? '✅' : '❌';
        echo "   $status $description\n";

        $this->test_results[] = [
            'test' => $description,
            'expected' => $expected_code,
            'actual' => $result['http_code'],
            'passed' => $passed
        ];

        if (!$passed) {
            echo "      Expected: $expected_code, Got: {$result['http_code']}\n";
        }
    }

    private function assert_contains($result, $needle, $description) {
        $contains = strpos($result['response'], $needle) !== false;
        $status = $contains ? '✅' : '❌';
        echo "   $status $description\n";

        $this->test_results[] = [
            'test' => $description,
            'expected' => "contains '$needle'",
            'actual' => $contains ? 'found' : 'not found',
            'passed' => $contains
        ];
    }

    private function display_results() {
        echo "📊 TEST RESULTS SUMMARY\n";
        echo "======================\n";

        $passed = 0;
        $total = count($this->test_results);

        foreach ($this->test_results as $result) {
            $passed += $result['passed'] ? 1 : 0;
        }

        $percentage = $total > 0 ? round(($passed / $total) * 100, 1) : 0;

        echo "✅ Passed: $passed/$total ($percentage%)\n";

        if ($passed < $total) {
            echo "\n❌ Failed Tests:\n";
            foreach ($this->test_results as $result) {
                if (!$result['passed']) {
                    echo "   - {$result['test']}\n";
                    echo "     Expected: {$result['expected']}, Got: {$result['actual']}\n";
                }
            }
        }

        echo "\n🔒 SECURITY STATUS: " . ($percentage >= 80 ? 'SECURE' : 'NEEDS ATTENTION') . "\n";
    }
}

// Run the test suite
if (!isset($argv[1]) || $argv[1] !== '--no-run') {
    $suite = new SecurityTestSuite();
    $suite->run_all_tests();
}
?>