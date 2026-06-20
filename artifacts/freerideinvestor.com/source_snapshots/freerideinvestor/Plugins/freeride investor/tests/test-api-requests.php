<?php

class Test_API_Requests extends WP_UnitTestCase {

    public function test_make_request() {
        $url = 'https://jsonplaceholder.typicode.com/posts/1';

        $response = FRI_API_Requests::make_request($url);

        $this->assertIsArray($response, 'API response should be an array.');
        $this->assertArrayHasKey('id', $response, 'Response should have an "id" key.');
    }
}
