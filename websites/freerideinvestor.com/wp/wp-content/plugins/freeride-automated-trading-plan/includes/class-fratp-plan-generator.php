<?php
/**
 * Plan Generator Class
 * Generates daily trading plans based on strategy analysis
 */

if (!defined('ABSPATH')) {
    exit;
}

class FRATP_Plan_Generator {
    
    /**
     * Generate a trading plan for a symbol
     * 
     * @param string $symbol Stock symbol
     * @param string $date Date (Y-m-d format) or 'today'
     * @return array|WP_Error Plan data or error
     */
    public function generate_plan($symbol, $date = 'today') {
        if ($date === 'today') {
            $date = current_time('Y-m-d');
        }
        
        // Check if plan already exists for this date
        $existing = $this->get_plan($symbol, $date);
        if ($existing && $existing['date'] === $date) {
            return $existing; // Return existing plan
        }
        
        // Get strategy analysis
        $calculator = new FRATP_Strategy_Calculator();
        $analysis = $calculator->analyze_strategy($symbol);
        
        if (is_wp_error($analysis)) {
            return $analysis;
        }
        
        // Get current quote
        $market_data = new FRATP_Market_Data();
        $quote = $market_data->get_quote($symbol);
        
        if (is_wp_error($quote)) {
            return $quote;
        }
        
        // Build plan
        $plan = array(
            'symbol' => $symbol,
            'date' => $date,
            'generated_at' => current_time('mysql'),
            'current_price' => $quote['price'],
            'signal' => $analysis['signal'],
            'ma_short' => $analysis['ma_short'],
            'ma_long' => $analysis['ma_long'],
            'rsi' => $analysis['rsi'],
            'recommendation' => $this->get_recommendation($analysis),
            'action_items' => $this->get_action_items($analysis),
            'risk_metrics' => $this->get_risk_metrics($analysis),
        );
        
        // Add trade details if signal exists
        if ($analysis['signal'] !== 'none') {
            $plan['trade'] = array(
                'direction' => $analysis['direction'],
                'entry_price' => $analysis['entry_price'],
                'position_size' => $analysis['position_size'],
                'stop_loss' => $analysis['stop_loss'],
                'profit_target' => $analysis['profit_target'],
                'risk_amount' => $analysis['risk_amount'],
                'risk_reward_ratio' => $this->calculate_risk_reward($analysis),
            );
            
            if (isset($analysis['trailing_stop'])) {
                $plan['trade']['trailing_stop'] = $analysis['trailing_stop'];
            }
        }
        
        // Save to database
        $db = new FRATP_Database();
        $db->save_plan($plan);
        
        return $plan;
    }
    
    /**
     * Get plan for a specific date
     * 
     * @param string $symbol Stock symbol
     * @param string $date Date (Y-m-d format) or 'today'
     * @return array|false Plan data or false if not found
     */
    public function get_plan($symbol, $date = 'today') {
        if ($date === 'today') {
            $date = current_time('Y-m-d');
        }
        
        $db = new FRATP_Database();
        return $db->get_plan($symbol, $date);
    }
    
    /**
     * Get latest plan for a symbol
     * 
     * @param string $symbol Stock symbol
     * @return array|false Latest plan or false if not found
     */
    public function get_latest_plan($symbol) {
        $db = new FRATP_Database();
        return $db->get_latest_plan($symbol);
    }
    
    /**
     * Get recommendation text
     * 
     * @param array $analysis Strategy analysis
     * @return string Recommendation
     */
    private function get_recommendation($analysis) {
        if ($analysis['signal'] === 'long') {
            return sprintf(
                __('BUY signal detected. Price is above both moving averages (MA50: $%s, MA200: $%s) and RSI (%s) is not overbought. Consider entering a long position.', 'freeride-automated-trading-plan'),
                number_format($analysis['ma_short'], 2),
                number_format($analysis['ma_long'], 2),
                number_format($analysis['rsi'], 2)
            );
        } elseif ($analysis['signal'] === 'short') {
            return sprintf(
                __('SELL signal detected. Price is below both moving averages (MA50: $%s, MA200: $%s) and RSI (%s) is not oversold. Consider entering a short position.', 'freeride-automated-trading-plan'),
                number_format($analysis['ma_short'], 2),
                number_format($analysis['ma_long'], 2),
                number_format($analysis['rsi'], 2)
            );
        } else {
            return sprintf(
                __('NO SIGNAL. Price is between moving averages (MA50: $%s, MA200: $%s) or RSI (%s) is in neutral zone. Wait for clearer signal.', 'freeride-automated-trading-plan'),
                number_format($analysis['ma_short'], 2),
                number_format($analysis['ma_long'], 2),
                number_format($analysis['rsi'], 2)
            );
        }
    }
    
    /**
     * Get action items
     * 
     * @param array $analysis Strategy analysis
     * @return array Action items
     */
    private function get_action_items($analysis) {
        $items = array();
        
        if ($analysis['signal'] === 'none') {
            $items[] = __('Monitor price action and wait for signal', 'freeride-automated-trading-plan');
            $items[] = __('Check RSI levels for potential entry', 'freeride-automated-trading-plan');
            $items[] = __('Review market conditions and news', 'freeride-automated-trading-plan');
        } else {
            $direction = strtoupper($analysis['direction']);
            $items[] = sprintf(__('Enter %s position at $%s', 'freeride-automated-trading-plan'), $direction, number_format($analysis['entry_price'], 2));
            $items[] = sprintf(__('Set stop loss at $%s', 'freeride-automated-trading-plan'), number_format($analysis['stop_loss'], 2));
            $items[] = sprintf(__('Set profit target at $%s', 'freeride-automated-trading-plan'), number_format($analysis['profit_target'], 2));
            $items[] = sprintf(__('Position size: %s shares', 'freeride-automated-trading-plan'), number_format($analysis['position_size']));
            
            if (isset($analysis['trailing_stop'])) {
                $items[] = __('Enable trailing stop after trigger', 'freeride-automated-trading-plan');
            }
            
            $items[] = __('Monitor position and adjust stops as needed', 'freeride-automated-trading-plan');
        }
        
        return $items;
    }
    
    /**
     * Get risk metrics
     * 
     * @param array $analysis Strategy analysis
     * @return array Risk metrics
     */
    private function get_risk_metrics($analysis) {
        $equity = get_option('fratp_initial_capital', 1000000);
        $risk_pct = get_option('fratp_risk_pct_equity', 0.5);
        
        $metrics = array(
            'equity' => $equity,
            'risk_per_trade' => $equity * ($risk_pct / 100.0),
            'risk_percentage' => $risk_pct,
        );
        
        if ($analysis['signal'] !== 'none' && isset($analysis['position_size'])) {
            $metrics['position_size'] = $analysis['position_size'];
            $metrics['position_value'] = $analysis['entry_price'] * $analysis['position_size'];
            $metrics['max_loss'] = $analysis['risk_amount'];
        }
        
        return $metrics;
    }
    
    /**
     * Calculate risk-reward ratio
     * 
     * @param array $analysis Strategy analysis
     * @return float Risk-reward ratio
     */
    private function calculate_risk_reward($analysis) {
        if (!isset($analysis['entry_price']) || !isset($analysis['stop_loss']) || !isset($analysis['profit_target'])) {
            return 0;
        }
        
        $risk = abs($analysis['entry_price'] - $analysis['stop_loss']);
        $reward = abs($analysis['profit_target'] - $analysis['entry_price']);
        
        if ($risk == 0) {
            return 0;
        }
        
        return round($reward / $risk, 2);
    }
}

