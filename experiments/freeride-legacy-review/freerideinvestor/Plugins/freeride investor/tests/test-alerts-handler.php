<?php

class Test_Alerts_Handler extends WP_UnitTestCase {

    public function test_add_alert() {
        $email = 'test@example.com';
        $symbol = 'TSLA';
        $type = 'price_above';
        $value = 150.00;

        $result = FRI_Alerts_Handler::add_alert($email, $symbol, $type, $value);

        $this->assertNotFalse($result, 'Alert should be added successfully.');
    }

    public function test_get_active_alerts() {
        $alerts = FRI_Alerts_Handler::get_active_alerts();

        $this->assertIsArray($alerts, 'Active alerts should return as an array.');
    }
}
