<?php
namespace TradingRobotPlug;

class Subscription_Manager {
    public function get_plans() {
        return [
            [
                'id' => 'free',
                'name' => 'Free Tier',
                'price' => 0,
                'features' => ['1 Demo Robot', 'Paper Trading Only', 'Daily Metrics', '7-Day History']
            ],
            [
                'id' => 'low',
                'name' => 'Low Commitment',
                'price' => 9.99,
                'features' => ['3 Active Robots', 'Live Trading Enabled', 'Real-time Metrics', '30-Day History']
            ],
            [
                'id' => 'mid',
                'name' => 'Mid-Tier',
                'price' => 29.99,
                'features' => ['Unlimited Robots', 'Priority Support', 'Advanced Analytics', 'Unlimited History', 'Most Popular']
            ],
            [
                'id' => 'premium',
                'name' => 'Premium',
                'price' => 99.99,
                'features' => ['Custom Strategy Dev', 'White Glove Setup', 'API Access', 'Enterprise Support']
            ]
        ];
    }

    public function get_user_subscription($user_id) {
        // Mock
        return [
            'plan_id' => 'free',
            'status' => 'active',
            'expires_at' => null
        ];
    }
}
