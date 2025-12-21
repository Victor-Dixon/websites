<?php
/**
 * Strategy Calculator Class
 * Implements the TradingView strategy logic: MA, RSI, risk management
 */

if (!defined('ABSPATH')) {
    exit;
}

class FRATP_Strategy_Calculator {
    
    /**
     * Calculate Simple Moving Average
     * 
     * @param array $prices Array of closing prices
     * @param int $length Period length
     * @return float|false MA value or false on error
     */
    public function calculate_sma($prices, $length) {
        if (count($prices) < $length) {
            return false;
        }
        
        $slice = array_slice($prices, -$length);
        return array_sum($slice) / $length;
    }
    
    /**
     * Calculate RSI (Relative Strength Index)
     * 
     * @param array $prices Array of closing prices
     * @param int $length Period length (default 14)
     * @return float|false RSI value or false on error
     */
    public function calculate_rsi($prices, $length = 14) {
        if (count($prices) < $length + 1) {
            return false;
        }
        
        $gains = array();
        $losses = array();
        
        // Calculate price changes
        for ($i = 1; $i < count($prices); $i++) {
            $change = $prices[$i] - $prices[$i - 1];
            if ($change > 0) {
                $gains[] = $change;
                $losses[] = 0;
            } else {
                $gains[] = 0;
                $losses[] = abs($change);
            }
        }
        
        if (count($gains) < $length) {
            return false;
        }
        
        // Calculate average gain and loss
        $avg_gain = array_sum(array_slice($gains, -$length)) / $length;
        $avg_loss = array_sum(array_slice($losses, -$length)) / $length;
        
        if ($avg_loss == 0) {
            return 100; // All gains, no losses
        }
        
        $rs = $avg_gain / $avg_loss;
        $rsi = 100 - (100 / (1 + $rs));
        
        return round($rsi, 2);
    }
    
    /**
     * Check long entry condition
     * Long when above both MAs and RSI not overheated
     * 
     * @param float $price Current price
     * @param float $ma_short Short MA value
     * @param float $ma_long Long MA value
     * @param float $rsi RSI value
     * @param int $rsi_overbought RSI overbought threshold
     * @return bool True if long condition met
     */
    public function check_long_condition($price, $ma_short, $ma_long, $rsi, $rsi_overbought = 60) {
        if ($ma_short === false || $ma_long === false || $rsi === false) {
            return false;
        }
        
        return ($price > $ma_short && $price > $ma_long && $rsi < $rsi_overbought);
    }
    
    /**
     * Check short entry condition
     * Short when below both MAs and RSI not too washed
     * 
     * @param float $price Current price
     * @param float $ma_short Short MA value
     * @param float $ma_long Long MA value
     * @param float $rsi RSI value
     * @param int $rsi_oversold RSI oversold threshold
     * @return bool True if short condition met
     */
    public function check_short_condition($price, $ma_short, $ma_long, $rsi, $rsi_oversold = 40) {
        if ($ma_short === false || $ma_long === false || $rsi === false) {
            return false;
        }
        
        return ($price < $ma_short && $price < $ma_long && $rsi > $rsi_oversold);
    }
    
    /**
     * Calculate position size based on risk
     * 
     * @param float $equity Current equity
     * @param float $risk_pct Risk percentage of equity
     * @param float $price Current price
     * @param float $stop_pct Stop loss percentage
     * @return int Number of shares
     */
    public function calculate_position_size($equity, $risk_pct, $price, $stop_pct) {
        $risk_amount = $equity * ($risk_pct / 100.0);
        $stop_dist = $price * ($stop_pct / 100.0);
        
        // Avoid divide-by-zero
        if ($stop_dist <= 0) {
            return 0;
        }
        
        $raw_qty = $risk_amount / $stop_dist;
        $qty = floor($raw_qty);
        
        return max(0, $qty);
    }
    
    /**
     * Calculate stop loss price
     * 
     * @param float $price Entry price
     * @param float $stop_pct Stop loss percentage
     * @param string $direction 'long' or 'short'
     * @return float Stop loss price
     */
    public function calculate_stop_loss($price, $stop_pct, $direction = 'long') {
        if ($direction === 'long') {
            return $price * (1 - $stop_pct / 100.0);
        } else {
            return $price * (1 + $stop_pct / 100.0);
        }
    }
    
    /**
     * Calculate profit target price
     * 
     * @param float $price Entry price
     * @param float $target_pct Profit target percentage
     * @param string $direction 'long' or 'short'
     * @return float Target price
     */
    public function calculate_profit_target($price, $target_pct, $direction = 'long') {
        if ($direction === 'long') {
            return $price * (1 + $target_pct / 100.0);
        } else {
            return $price * (1 - $target_pct / 100.0);
        }
    }
    
    /**
     * Calculate trailing stop values
     * 
     * @param float $price Current price
     * @param float $trail_offset_pct Trail offset percentage
     * @param float $trail_trigger_pct Trail trigger percentage
     * @return array Array with trail_points and trail_offset
     */
    public function calculate_trailing_stop($price, $trail_offset_pct, $trail_trigger_pct) {
        return array(
            'trail_points' => $price * ($trail_trigger_pct / 100.0),
            'trail_offset' => $price * ($trail_offset_pct / 100.0),
        );
    }
    
    /**
     * Get current strategy status for a symbol
     * 
     * @param string $symbol Stock symbol
     * @return array|WP_Error Strategy status or error
     */
    public function get_current_status($symbol) {
        $market_data = new FRATP_Market_Data();
        $historical_data = $market_data->get_historical_data($symbol, 250); // Get enough data for 200 MA
        
        if (is_wp_error($historical_data)) {
            return $historical_data;
        }
        
        if (empty($historical_data) || count($historical_data) < 200) {
            return new WP_Error('insufficient_data', __('Insufficient historical data.', 'freeride-automated-trading-plan'));
        }
        
        // Extract closing prices
        $closes = array_column($historical_data, 'close');
        $current_price = end($closes);
        
        // Get settings
        $ma_short_length = get_option('fratp_ma_short_length', 50);
        $ma_long_length = get_option('fratp_ma_long_length', 200);
        $rsi_length = get_option('fratp_rsi_length', 14);
        $rsi_overbought = get_option('fratp_rsi_overbought', 60);
        $rsi_oversold = get_option('fratp_rsi_oversold', 40);
        
        // Calculate indicators
        $ma_short = $this->calculate_sma($closes, $ma_short_length);
        $ma_long = $this->calculate_sma($closes, $ma_long_length);
        $rsi = $this->calculate_rsi($closes, $rsi_length);
        
        // Check conditions
        $long_condition = $this->check_long_condition($current_price, $ma_short, $ma_long, $rsi, $rsi_overbought);
        $short_condition = $this->check_short_condition($current_price, $ma_short, $ma_long, $rsi, $rsi_oversold);
        
        // Determine signal
        $signal = 'none';
        if ($long_condition) {
            $signal = 'long';
        } elseif ($short_condition) {
            $signal = 'short';
        }
        
        return array(
            'symbol' => $symbol,
            'price' => $current_price,
            'ma_short' => $ma_short,
            'ma_long' => $ma_long,
            'rsi' => $rsi,
            'signal' => $signal,
            'long_condition' => $long_condition,
            'short_condition' => $short_condition,
            'timestamp' => current_time('mysql'),
        );
    }
    
    /**
     * Analyze strategy for plan generation
     * 
     * @param string $symbol Stock symbol
     * @return array|WP_Error Analysis results or error
     */
    public function analyze_strategy($symbol) {
        $status = $this->get_current_status($symbol);
        
        if (is_wp_error($status)) {
            return $status;
        }
        
        // Get risk settings
        $equity = get_option('fratp_initial_capital', 1000000);
        $risk_pct = get_option('fratp_risk_pct_equity', 0.5);
        $stop_pct = get_option('fratp_stop_pct_price', 1.0);
        $target_pct = get_option('fratp_target_pct_price', 15.0);
        $use_trailing = get_option('fratp_use_trailing_stop', true);
        $trail_offset_pct = get_option('fratp_trail_offset_pct', 0.5);
        $trail_trigger_pct = get_option('fratp_trail_trigger_pct', 5.0);
        
        $analysis = $status;
        
        // Calculate position sizing if signal exists
        if ($status['signal'] !== 'none') {
            $direction = $status['signal'];
            $position_size = $this->calculate_position_size(
                $equity,
                $risk_pct,
                $status['price'],
                $stop_pct
            );
            
            $stop_loss = $this->calculate_stop_loss($status['price'], $stop_pct, $direction);
            $profit_target = $this->calculate_profit_target($status['price'], $target_pct, $direction);
            
            $analysis['position_size'] = $position_size;
            $analysis['entry_price'] = $status['price'];
            $analysis['stop_loss'] = $stop_loss;
            $analysis['profit_target'] = $profit_target;
            $analysis['risk_amount'] = $equity * ($risk_pct / 100.0);
            $analysis['direction'] = $direction;
            
            if ($use_trailing) {
                $trailing = $this->calculate_trailing_stop($status['price'], $trail_offset_pct, $trail_trigger_pct);
                $analysis['trailing_stop'] = $trailing;
            }
        }
        
        return $analysis;
    }
}



