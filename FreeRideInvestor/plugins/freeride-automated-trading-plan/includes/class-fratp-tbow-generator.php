<?php
/**
 * TBOW Tactic Generator Class
 * Generates TBOW-formatted tactics using automated trading plan strategy
 */

if (!defined('ABSPATH')) {
    exit;
}

class FRATP_TBOW_Generator {
    
    /**
     * Generate TBOW tactic HTML content from trading plan
     * 
     * @param array $plan Trading plan data
     * @return string TBOW HTML content
     */
    public function generate_tbow_html($plan) {
        $stock_ticker = $plan['symbol'];
        $title = 'Automated Strategy Plan - ' . date('Y-m-d', strtotime($plan['date']));
        
        // Build context from plan data
        $context = $this->build_context($plan);
        $objective = $this->build_objective($plan);
        
        // Build levels
        $resistance_levels = $this->build_resistance_levels($plan);
        $support_levels = $this->build_support_levels($plan);
        
        // Build entry criteria
        $short_entry = $this->build_short_entry($plan);
        $short_targets = $this->build_short_targets($plan);
        $short_stoploss = $this->build_short_stoploss($plan);
        
        $long_entry = $this->build_long_entry($plan);
        $long_targets = $this->build_long_targets($plan);
        $long_stoploss = $this->build_long_stoploss($plan);
        
        // Options strategies
        $bearish_options = $this->build_bearish_options($plan);
        $bullish_options = $this->build_bullish_options($plan);
        
        // Monitoring
        $volume_check = $this->build_volume_check($plan);
        $options_flow = $this->build_options_flow($plan);
        $market_indicators = $this->build_market_indicators($plan);
        
        // Risk management
        $risk_reward = $this->build_risk_reward($plan);
        $invalidation = $this->build_invalidation($plan);
        
        // Execution checklist
        $execution_checklist = $plan['action_items'] ?? array();
        
        // Generate HTML using TBOW template
        return $this->generate_tbow_html_content(
            $stock_ticker,
            $title,
            $context,
            $objective,
            $resistance_levels,
            $support_levels,
            $short_entry,
            $short_targets,
            $short_stoploss,
            $long_entry,
            $long_targets,
            $long_stoploss,
            $bearish_options,
            $bullish_options,
            $volume_check,
            $options_flow,
            $market_indicators,
            $risk_reward,
            $invalidation,
            $execution_checklist
        );
    }
    
    /**
     * Build context section
     */
    private function build_context($plan) {
        $context = sprintf(
            __('Analyzing %s with current price of $%s. Technical indicators show MA50 at $%s and MA200 at $%s. RSI is currently at %s.', 'freeride-automated-trading-plan'),
            $plan['symbol'],
            number_format($plan['current_price'], 2),
            number_format($plan['ma_short'], 2),
            number_format($plan['ma_long'], 2),
            number_format($plan['rsi'], 2)
        );
        
        if ($plan['signal'] === 'long') {
            $context .= ' ' . __('Bullish signal detected: Price is above both moving averages with RSI not overbought.', 'freeride-automated-trading-plan');
        } elseif ($plan['signal'] === 'short') {
            $context .= ' ' . __('Bearish signal detected: Price is below both moving averages with RSI not oversold.', 'freeride-automated-trading-plan');
        } else {
            $context .= ' ' . __('No clear signal at this time. Waiting for better entry conditions.', 'freeride-automated-trading-plan');
        }
        
        return $context;
    }
    
    /**
     * Build objective section
     */
    private function build_objective($plan) {
        if ($plan['signal'] === 'long') {
            return __('Execute a long position with defined risk management, targeting profit while protecting capital with stop-loss orders.', 'freeride-automated-trading-plan');
        } elseif ($plan['signal'] === 'short') {
            return __('Execute a short position with defined risk management, targeting profit while protecting capital with stop-loss orders.', 'freeride-automated-trading-plan');
        } else {
            return __('Monitor price action and wait for clear entry signals based on moving average crossovers and RSI conditions.', 'freeride-automated-trading-plan');
        }
    }
    
    /**
     * Build resistance levels
     */
    private function build_resistance_levels($plan) {
        $levels = array();
        
        if (isset($plan['trade']['profit_target']) && $plan['signal'] === 'short') {
            $levels[] = '$' . number_format($plan['trade']['profit_target'], 2) . ' - Primary Target';
        }
        
        // Use MA200 as resistance if price is below it
        if ($plan['current_price'] < $plan['ma_long']) {
            $levels[] = '$' . number_format($plan['ma_long'], 2) . ' - MA200 Resistance';
        }
        
        // Use MA50 as resistance if price is below it
        if ($plan['current_price'] < $plan['ma_short']) {
            $levels[] = '$' . number_format($plan['ma_short'], 2) . ' - MA50 Resistance';
        }
        
        // Add current high as resistance
        $levels[] = '$' . number_format($plan['current_price'] * 1.02, 2) . ' - Near-term Resistance';
        
        return $levels;
    }
    
    /**
     * Build support levels
     */
    private function build_support_levels($plan) {
        $levels = array();
        
        if (isset($plan['trade']['profit_target']) && $plan['signal'] === 'long') {
            $levels[] = '$' . number_format($plan['trade']['profit_target'], 2) . ' - Primary Target';
        }
        
        // Use MA200 as support if price is above it
        if ($plan['current_price'] > $plan['ma_long']) {
            $levels[] = '$' . number_format($plan['ma_long'], 2) . ' - MA200 Support';
        }
        
        // Use MA50 as support if price is above it
        if ($plan['current_price'] > $plan['ma_short']) {
            $levels[] = '$' . number_format($plan['ma_short'], 2) . ' - MA50 Support';
        }
        
        // Add stop loss as support
        if (isset($plan['trade']['stop_loss'])) {
            $levels[] = '$' . number_format($plan['trade']['stop_loss'], 2) . ' - Stop Loss Level';
        }
        
        return $levels;
    }
    
    /**
     * Build short entry criteria
     */
    private function build_short_entry($plan) {
        if ($plan['signal'] === 'short' && isset($plan['trade'])) {
            return sprintf(
                __('Short entry at $%s when price is below both MA50 ($%s) and MA200 ($%s), with RSI above %s. Position size: %s shares.', 'freeride-automated-trading-plan'),
                number_format($plan['trade']['entry_price'], 2),
                number_format($plan['ma_short'], 2),
                number_format($plan['ma_long'], 2),
                number_format(get_option('fratp_rsi_oversold', 40), 0),
                number_format($plan['trade']['position_size'])
            );
        } else {
            return __('Wait for price to break below both MA50 and MA200 with RSI above oversold threshold before considering short entry.', 'freeride-automated-trading-plan');
        }
    }
    
    /**
     * Build short targets
     */
    private function build_short_targets($plan) {
        $targets = array();
        
        if (isset($plan['trade']['profit_target'])) {
            $targets[] = '$' . number_format($plan['trade']['profit_target'], 2) . ' - Primary Target';
            $targets[] = '$' . number_format($plan['trade']['profit_target'] * 0.95, 2) . ' - Partial Profit Target (50%)';
        } else {
            $targets[] = __('Calculate based on risk/reward ratio when entry signal appears', 'freeride-automated-trading-plan');
        }
        
        return $targets;
    }
    
    /**
     * Build short stop loss
     */
    private function build_short_stoploss($plan) {
        if (isset($plan['trade']['stop_loss'])) {
            return sprintf(
                __('Stop-loss at $%s (%.1f%% above entry). Risk amount: $%s.', 'freeride-automated-trading-plan'),
                number_format($plan['trade']['stop_loss'], 2),
                (($plan['trade']['stop_loss'] - $plan['trade']['entry_price']) / $plan['trade']['entry_price']) * 100,
                number_format($plan['trade']['risk_amount'], 2)
            );
        } else {
            return __('Set stop-loss 1% above entry price when short signal is confirmed.', 'freeride-automated-trading-plan');
        }
    }
    
    /**
     * Build long entry criteria
     */
    private function build_long_entry($plan) {
        if ($plan['signal'] === 'long' && isset($plan['trade'])) {
            return sprintf(
                __('Long entry at $%s when price is above both MA50 ($%s) and MA200 ($%s), with RSI below %s. Position size: %s shares.', 'freeride-automated-trading-plan'),
                number_format($plan['trade']['entry_price'], 2),
                number_format($plan['ma_short'], 2),
                number_format($plan['ma_long'], 2),
                number_format(get_option('fratp_rsi_overbought', 60), 0),
                number_format($plan['trade']['position_size'])
            );
        } else {
            return __('Wait for price to break above both MA50 and MA200 with RSI below overbought threshold before considering long entry.', 'freeride-automated-trading-plan');
        }
    }
    
    /**
     * Build long targets
     */
    private function build_long_targets($plan) {
        $targets = array();
        
        if (isset($plan['trade']['profit_target'])) {
            $targets[] = '$' . number_format($plan['trade']['profit_target'], 2) . ' - Primary Target';
            $targets[] = '$' . number_format($plan['trade']['profit_target'] * 0.95, 2) . ' - Partial Profit Target (50%)';
        } else {
            $targets[] = __('Calculate based on risk/reward ratio when entry signal appears', 'freeride-automated-trading-plan');
        }
        
        return $targets;
    }
    
    /**
     * Build long stop loss
     */
    private function build_long_stoploss($plan) {
        if (isset($plan['trade']['stop_loss'])) {
            return sprintf(
                __('Stop-loss at $%s (%.1f%% below entry). Risk amount: $%s.', 'freeride-automated-trading-plan'),
                number_format($plan['trade']['stop_loss'], 2),
                (($plan['trade']['entry_price'] - $plan['trade']['stop_loss']) / $plan['trade']['entry_price']) * 100,
                number_format($plan['trade']['risk_amount'], 2)
            );
        } else {
            return __('Set stop-loss 1% below entry price when long signal is confirmed.', 'freeride-automated-trading-plan');
        }
    }
    
    /**
     * Build bearish options strategy
     */
    private function build_bearish_options($plan) {
        if ($plan['signal'] === 'short' && isset($plan['trade']['entry_price'])) {
            return sprintf(
                __('Use Bear Put Spread: Buy put options with strike prices near $%s, sell puts at lower strikes. Consider expiration 30-45 days out.', 'freeride-automated-trading-plan'),
                number_format($plan['trade']['entry_price'], 2)
            );
        } else {
            return __('Consider bearish options strategies when short signal is confirmed. Use put spreads or bear call spreads based on volatility.', 'freeride-automated-trading-plan');
        }
    }
    
    /**
     * Build bullish options strategy
     */
    private function build_bullish_options($plan) {
        if ($plan['signal'] === 'long' && isset($plan['trade']['entry_price'])) {
            return sprintf(
                __('Use Bull Call Spread: Buy call options with strike prices near $%s, sell calls at higher strikes. Consider expiration 30-45 days out.', 'freeride-automated-trading-plan'),
                number_format($plan['trade']['entry_price'], 2)
            );
        } else {
            return __('Consider bullish options strategies when long signal is confirmed. Use call spreads or bull put spreads based on volatility.', 'freeride-automated-trading-plan');
        }
    }
    
    /**
     * Build volume check
     */
    private function build_volume_check($plan) {
        return __('Monitor volume for confirmation. Look for volume spikes above 20-day average when entry signals appear. High volume confirms trend strength.', 'freeride-automated-trading-plan');
    }
    
    /**
     * Build options flow
     */
    private function build_options_flow($plan) {
        return __('Monitor unusual options activity using existing tools. Look for large block trades and unusual put/call ratios that may indicate institutional interest.', 'freeride-automated-trading-plan');
    }
    
    /**
     * Build market indicators
     */
    private function build_market_indicators($plan) {
        return sprintf(
            __('Price: $%s | MA50: $%s | MA200: $%s | RSI: %s | Signal: %s', 'freeride-automated-trading-plan'),
            number_format($plan['current_price'], 2),
            number_format($plan['ma_short'], 2),
            number_format($plan['ma_long'], 2),
            number_format($plan['rsi'], 2),
            strtoupper($plan['signal'])
        );
    }
    
    /**
     * Build risk reward
     */
    private function build_risk_reward($plan) {
        if (isset($plan['trade']['risk_reward_ratio'])) {
            return sprintf(
                __('%s:1 (Risk: $%s, Reward: $%s)', 'freeride-automated-trading-plan'),
                number_format($plan['trade']['risk_reward_ratio'], 2),
                number_format($plan['trade']['risk_amount'], 2),
                number_format(abs($plan['trade']['profit_target'] - $plan['trade']['entry_price']) * $plan['trade']['position_size'], 2)
            );
        } else {
            return __('Calculate based on entry, stop-loss, and target when signal appears. Target minimum 2:1 risk/reward ratio.', 'freeride-automated-trading-plan');
        }
    }
    
    /**
     * Build invalidation scenarios
     */
    private function build_invalidation($plan) {
        $scenarios = array();
        
        if ($plan['signal'] === 'long') {
            $scenarios[] = sprintf(
                __('Break below stop-loss at $%s invalidates long position.', 'freeride-automated-trading-plan'),
                isset($plan['trade']['stop_loss']) ? number_format($plan['trade']['stop_loss'], 2) : 'calculated level'
            );
            $scenarios[] = __('RSI moves above overbought threshold (60) may indicate reversal.', 'freeride-automated-trading-plan');
        } elseif ($plan['signal'] === 'short') {
            $scenarios[] = sprintf(
                __('Break above stop-loss at $%s invalidates short position.', 'freeride-automated-trading-plan'),
                isset($plan['trade']['stop_loss']) ? number_format($plan['trade']['stop_loss'], 2) : 'calculated level'
            );
            $scenarios[] = __('RSI moves below oversold threshold (40) may indicate reversal.', 'freeride-automated-trading-plan');
        } else {
            $scenarios[] = __('Monitor for clear signal before entering position.', 'freeride-automated-trading-plan');
        }
        
        $scenarios[] = __('Price breaks above MA200 for shorts or below MA200 for longs invalidates the setup.', 'freeride-automated-trading-plan');
        
        return $scenarios;
    }
    
    /**
     * Generate TBOW HTML content (same structure as original TBOW template)
     */
    private function generate_tbow_html_content(
        $stock_ticker,
        $title,
        $context,
        $objective,
        $resistance_levels,
        $support_levels,
        $short_entry,
        $short_targets,
        $short_stoploss,
        $long_entry,
        $long_targets,
        $long_stoploss,
        $bearish_options,
        $bullish_options,
        $volume_check,
        $options_flow,
        $market_indicators,
        $risk_reward,
        $invalidation,
        $execution_checklist
    ) {
        // Build HTML lists for various sections.
        $build_list = function ( $items ) {
            $html = '';
            foreach ( $items as $item ) {
                $html .= "<li>" . esc_html( trim( $item ) ) . "</li>";
            }
            return $html;
        };

        $html = "
        <!DOCTYPE html>
        <html lang=\"en\">
        <head>
            <meta charset=\"UTF-8\">
            <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
            <meta name=\"description\" content=\"" . esc_attr( $stock_ticker ) . " TBoW Trading Strategy for Actionable Insights.\">
            <meta name=\"keywords\" content=\"" . esc_attr( $stock_ticker ) . ", Trading, TBoW, Stock Market, Strategy\">
            <meta name=\"author\" content=\"FreeRideInvestor\">
            <title>" . esc_html( $stock_ticker ) . " TBoW Tactic: " . esc_html( $title ) . "</title>
        </head>
        <body>
            <header>
                <h1>" . esc_html( $stock_ticker ) . " TBoW Tactic: " . esc_html( $title ) . "</h1>
            </header>

            <section>
                <h2>1. Contextual Insight</h2>
                <p>" . esc_html( $context ) . "</p>
            </section>

            <section>
                <h2>2. Tactic Objective</h2>
                <p>" . esc_html( $objective ) . "</p>
            </section>

            <section>
                <h2>3. Key Levels to Watch</h2>
                <ul>
                    <li><strong>Resistance (For Short Entries):</strong>
                        <ul>" . $build_list( $resistance_levels ) . "</ul>
                    </li>
                    <li><strong>Support (For Targets or Pullback Longs):</strong>
                        <ul>" . $build_list( $support_levels ) . "</ul>
                    </li>
                </ul>
            </section>

            <section>
                <h2>4. Actionable Steps</h2>
                <h3>A. Short Setup</h3>
                <ul>
                    <li><strong>Entry Criteria:</strong> " . esc_html( $short_entry ) . "</li>
                    <li><strong>Targets:</strong>
                        <ul>" . $build_list( $short_targets ) . "</ul>
                    </li>
                    <li><strong>Stop-Loss:</strong> " . esc_html( $short_stoploss ) . "</li>
                </ul>

                <h3>B. Long Setup</h3>
                <ul>
                    <li><strong>Entry Criteria:</strong> " . esc_html( $long_entry ) . "</li>
                    <li><strong>Targets:</strong>
                        <ul>" . $build_list( $long_targets ) . "</ul>
                    </li>
                    <li><strong>Stop-Loss:</strong> " . esc_html( $long_stoploss ) . "</li>
                </ul>

                <h3>C. Options Strategy</h3>
                <ul>
                    <li><strong>Bearish Options Strategy:</strong> " . esc_html( $bearish_options ) . "</li>
                    <li><strong>Bullish Options Strategy:</strong> " . esc_html( $bullish_options ) . "</li>
                </ul>
            </section>

            <section>
                <h2>5. Real-Time Monitoring</h2>
                <ul>
                    <li><strong>Volume:</strong> " . esc_html( $volume_check ) . "</li>
                    <li><strong>Options Flow:</strong> " . esc_html( $options_flow ) . "</li>
                    <li><strong>Broader Market Indicators:</strong> " . esc_html( $market_indicators ) . "</li>
                </ul>
            </section>

            <section>
                <h2>6. Risk Management and Adaptability</h2>
                <ul>
                    <li><strong>Risk/Reward Ratio:</strong> " . esc_html( $risk_reward ) . "</li>
                    <li><strong>Scenario Planning:</strong>
                        <ul>" . $build_list( $invalidation ) . "</ul>
                    </li>
                </ul>
            </section>

            <section>
                <h2>7. Execution Checklist</h2>
                <ul>" . $build_list( $execution_checklist ) . "</ul>
            </section>

            <footer>
                <p>
                    This tactic is designed to align with current macro and technical signals, maximizing the chance of a profitable trade. Remember, flexibility and real-time analysis are key to successful execution.
                </p>
            </footer>
        </body>
        </html>
        ";

        return $html;
    }
}



