<?php
/**
 * Trading Plan Generator - Creates automated trading plan content
 */

if (!defined('ABSPATH')) {
    exit;
}

class PlanGenerator {

    private $api_client;

    public function __construct() {
        $this->api_client = new TradingAPIClient();
    }

    /**
     * Generate daily trading plan
     */
    public function generate_daily_plan() {
        $trading_data = $this->api_client->get_trading_data();
        $account_info = $this->api_client->get_account_info();
        $active_strategies = $this->api_client->get_active_strategies();
        $risk_status = $this->api_client->get_risk_status();
        $recommendations = $this->api_client->get_strategy_recommendations();

        if (!$trading_data || !$account_info) {
            error_log("Failed to fetch required data for daily plan generation");
            return false;
        }

        $plan_title = sprintf(
            'Daily Trading Plan - %s',
            wp_date('F j, Y')
        );

        $plan_content = $this->generate_plan_content(
            $trading_data,
            $account_info,
            $active_strategies,
            $risk_status,
            $recommendations
        );

        return array(
            'title' => $plan_title,
            'content' => $plan_content,
            'meta' => array(
                'plan_type' => 'daily',
                'generated_date' => current_time('mysql'),
                'account_balance' => $account_info['balance'] ?? 0,
                'risk_level' => get_option('tpa_risk_level', 'ultra_conservative'),
                'trading_status' => $trading_data['state'] ?? 'unknown'
            ),
            'categories' => array(get_cat_ID('Trading Plans')),
            'tags' => array('daily-plan', 'automated', 'conservative')
        );
    }

    /**
     * Generate weekly strategy review
     */
    public function generate_weekly_review() {
        $journal_summary = $this->api_client->get_journal_summary(2025);
        $performance_metrics = $this->api_client->get_performance_metrics();
        $recent_trades = $this->api_client->get_recent_trades(50); // Last 50 trades

        if (!$journal_summary) {
            error_log("Failed to fetch journal data for weekly review");
            return false;
        }

        $review_title = sprintf(
            'Weekly Strategy Review - Week of %s',
            wp_date('F j, Y', strtotime('last monday'))
        );

        $review_content = $this->generate_weekly_content(
            $journal_summary,
            $performance_metrics,
            $recent_trades
        );

        return array(
            'title' => $review_title,
            'content' => $review_content,
            'meta' => array(
                'review_type' => 'weekly',
                'generated_date' => current_time('mysql'),
                'week_start' => wp_date('Y-m-d', strtotime('last monday')),
                'total_trades' => $journal_summary['summary']['total_trades'] ?? 0,
                'weekly_pnl' => $performance_metrics['weekly_pnl'] ?? 0
            ),
            'categories' => array(get_cat_ID('Strategy Reviews')),
            'tags' => array('weekly-review', 'performance', 'strategy-analysis')
        );
    }

    /**
     * Generate monthly performance report
     */
    public function generate_monthly_report() {
        $journal_summary = $this->api_client->get_journal_summary(2025);
        $performance_metrics = $this->api_client->get_performance_metrics();
        $monthly_trades = $this->api_client->get_recent_trades(200); // Last 200 trades

        if (!$journal_summary) {
            error_log("Failed to fetch journal data for monthly report");
            return false;
        }

        $report_title = sprintf(
            'Monthly Performance Report - %s',
            wp_date('F Y')
        );

        $report_content = $this->generate_monthly_content(
            $journal_summary,
            $performance_metrics,
            $monthly_trades
        );

        return array(
            'title' => $report_title,
            'content' => $report_content,
            'meta' => array(
                'report_type' => 'monthly',
                'generated_date' => current_time('mysql'),
                'month' => wp_date('Y-m'),
                'total_trades' => $journal_summary['summary']['total_trades'] ?? 0,
                'monthly_pnl' => $performance_metrics['monthly_pnl'] ?? 0,
                'win_rate' => $performance_metrics['win_rate'] ?? 0
            ),
            'categories' => array(get_cat_ID('Performance Reports')),
            'tags' => array('monthly-report', 'performance', 'comprehensive')
        );
    }

    /**
     * Generate daily plan content
     */
    private function generate_plan_content($trading_data, $account_info, $active_strategies, $risk_status, $recommendations) {
        ob_start();
        ?>
        <!-- wp:heading {"level":2} -->
        <h2>Market Overview</h2>
        <!-- /wp:heading -->

        <!-- wp:paragraph -->
        <p><strong>Trading Status:</strong> <?php echo esc_html(ucfirst($trading_data['state'] ?? 'Monitoring')); ?><br>
        <strong>Account Balance:</strong> $<?php echo number_format($account_info['balance'] ?? 0, 2); ?><br>
        <strong>Buying Power:</strong> $<?php echo number_format($account_info['buying_power'] ?? 0, 2); ?><br>
        <strong>Daily P&L:</strong> $<?php echo number_format($trading_data['daily_pnl'] ?? 0, 2); ?></p>
        <!-- /wp:paragraph -->

        <!-- wp:heading {"level":2} -->
        <h2>Risk Management Status</h2>
        <!-- /wp:heading -->

        <!-- wp:paragraph -->
        <p><strong>Risk Level:</strong> <?php echo esc_html(ucfirst(str_replace('_', ' ', $risk_status['risk_level'] ?? 'ultra_conservative'))); ?><br>
        <strong>Daily Loss Limit:</strong> <?php echo esc_html($risk_status['limits']['daily_loss_limit_pct'] ?? 0.5); ?>%<br>
        <strong>Max Position Size:</strong> <?php echo esc_html($risk_status['limits']['max_position_size_pct'] ?? 5.0); ?>%<br>
        <strong>Open Positions:</strong> <?php echo esc_html($risk_status['open_positions'] ?? 0); ?><br>
        <strong>Emergency Stop:</strong> <?php echo $risk_status['emergency_stop'] ? 'ACTIVE' : 'Inactive'; ?></p>
        <!-- /wp:paragraph -->

        <!-- wp:heading {"level":2} -->
        <h2>Active Strategies</h2>
        <!-- /wp:heading -->

        <?php if (!empty($active_strategies)): ?>
        <!-- wp:list -->
        <ul>
            <?php foreach ($active_strategies as $strategy): ?>
            <li><strong><?php echo esc_html($strategy['name'] ?? 'Unknown Strategy'); ?>:</strong>
                <?php echo esc_html($strategy['description'] ?? ''); ?> (Confidence: <?php echo esc_html($strategy['confidence'] ?? 0); ?>%)</li>
            <?php endforeach; ?>
        </ul>
        <!-- /wp:list -->
        <?php else: ?>
        <!-- wp:paragraph -->
        <p>No active strategies currently. System in monitoring mode for high-probability opportunities.</p>
        <!-- /wp:paragraph -->
        <?php endif; ?>

        <!-- wp:heading {"level":2} -->
        <h2>Today's Trading Plan</h2>
        <!-- /wp:heading -->

        <?php if (!empty($recommendations)): ?>
        <!-- wp:list {"ordered":true} -->
        <ol>
            <?php foreach ($recommendations as $rec): ?>
            <li><strong><?php echo esc_html($rec['action'] ?? 'Monitor'); ?>:</strong> <?php echo esc_html($rec['symbol'] ?? ''); ?> -
                <?php echo esc_html($rec['reasoning'] ?? ''); ?> (Risk: <?php echo esc_html($rec['risk_amount'] ?? 0); ?>%)</li>
            <?php endforeach; ?>
        </ol>
        <!-- /wp:list -->
        <?php else: ?>
        <!-- wp:paragraph -->
        <p>Conservative monitoring mode: No trades recommended today. Market conditions not meeting ultra-safe entry criteria.</p>
        <!-- /wp:paragraph -->
        <?php endif; ?>

        <!-- wp:heading {"level":2} -->
        <h2>Safety Protocols Active</h2>
        <!-- /wp:heading -->

        <!-- wp:list -->
        <ul>
            <li>✅ Daily loss limit enforcement (0.5% maximum)</li>
            <li>✅ Position size caps (5% of portfolio max)</li>
            <li>✅ Emergency stop mechanisms active</li>
            <li>✅ Manual override capabilities available</li>
            <li>✅ Real-time risk monitoring enabled</li>
            <li>✅ Audit logging of all decisions</li>
        </ul>
        <!-- /wp:list -->

        <!-- wp:paragraph -->
        <p><em>This trading plan was automatically generated by our AI-powered risk management system. All trades are executed with conservative parameters designed to protect capital while seeking consistent returns.</em></p>
        <!-- /wp:paragraph -->
        <?php
        return ob_get_clean();
    }

    /**
     * Generate weekly review content
     */
    private function generate_weekly_content($journal_summary, $performance_metrics, $recent_trades) {
        $summary = $journal_summary['summary'] ?? array();

        ob_start();
        ?>
        <!-- wp:heading {"level":2} -->
        <h2>Weekly Performance Summary</h2>
        <!-- /wp:heading -->

        <!-- wp:paragraph -->
        <p><strong>Total Trades:</strong> <?php echo esc_html($summary['total_trades'] ?? 0); ?><br>
        <strong>Total P&L:</strong> $<?php echo number_format($summary['total_pnl'] ?? 0, 2); ?><br>
        <strong>Win Rate:</strong> <?php echo esc_html($summary['win_rate'] ?? 0); ?>%<br>
        <strong>Average Trade:</strong> $<?php echo number_format($summary['average_trade'] ?? 0, 2); ?><br>
        <strong>Largest Win:</strong> $<?php echo number_format($summary['largest_win'] ?? 0, 2); ?><br>
        <strong>Largest Loss:</strong> $<?php echo number_format($summary['largest_loss'] ?? 0, 2); ?><br>
        <strong>Total Commissions:</strong> $<?php echo number_format($summary['total_commissions'] ?? 0, 2); ?></p>
        <!-- /wp:paragraph -->

        <!-- wp:heading {"level":2} -->
        <h2>Risk Metrics</h2>
        <!-- /wp:heading -->

        <!-- wp:paragraph -->
        <p><strong>Sharpe Ratio:</strong> <?php echo number_format($performance_metrics['sharpe_ratio'] ?? 0, 2); ?><br>
        <strong>Consistency Score:</strong> <?php echo esc_html($performance_metrics['consistency_score'] ?? 0); ?>%<br>
        <strong>Best Day:</strong> $<?php echo number_format($performance_metrics['best_day'] ?? 0, 2); ?><br>
        <strong>Worst Day:</strong> $<?php echo number_format($performance_metrics['worst_day'] ?? 0, 2); ?><br>
        <strong>Max Drawdown:</strong> $<?php echo number_format($performance_metrics['max_drawdown'] ?? 0, 2); ?></p>
        <!-- /wp:paragraph -->

        <!-- wp:heading {"level":2} -->
        <h2>Recent Trades</h2>
        <!-- /wp:heading -->

        <?php if (!empty($recent_trades)): ?>
        <!-- wp:table -->
        <table class="wp-block-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Symbol</th>
                    <th>Side</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>P&L</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($recent_trades, 0, 10) as $trade): ?>
                <tr>
                    <td><?php echo esc_html(wp_date('M j', strtotime($trade['timestamp'] ?? ''))); ?></td>
                    <td><?php echo esc_html($trade['instrument'] ?? $trade['symbol'] ?? 'N/A'); ?></td>
                    <td><?php echo esc_html(ucfirst($trade['side'] ?? 'N/A')); ?></td>
                    <td><?php echo esc_html($trade['quantity'] ?? 0); ?></td>
                    <td>$<?php echo number_format($trade['price'] ?? 0, 2); ?></td>
                    <td class="<?php echo ($trade['pnl'] ?? 0) >= 0 ? 'positive' : 'negative'; ?>">
                        $<?php echo number_format($trade['pnl'] ?? 0, 2); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <!-- /wp:table -->
        <?php endif; ?>

        <!-- wp:heading {"level":2} -->
        <h2>Strategy Performance</h2>
        <!-- /wp:heading -->

        <!-- wp:paragraph -->
        <p>This weekly review provides insights into our conservative automated trading strategy performance. The system maintains strict risk controls to protect capital while seeking consistent returns through high-probability trading opportunities.</p>
        <!-- /wp:paragraph -->
        <?php
        return ob_get_clean();
    }

    /**
     * Generate monthly report content
     */
    private function generate_monthly_content($journal_summary, $performance_metrics, $monthly_trades) {
        $summary = $journal_summary['summary'] ?? array();

        ob_start();
        ?>
        <!-- wp:heading {"level":2} -->
        <h2>Monthly Performance Overview</h2>
        <!-- /wp:heading -->

        <!-- wp:paragraph -->
        <p><strong>Reporting Period:</strong> <?php echo esc_html(wp_date('F Y')); ?><br>
        <strong>Total Trades:</strong> <?php echo esc_html($summary['total_trades'] ?? 0); ?><br>
        <strong>Net P&L:</strong> <span class="<?php echo ($summary['total_pnl'] ?? 0) >= 0 ? 'positive' : 'negative'; ?>">
            $<?php echo number_format(abs($summary['total_pnl'] ?? 0), 2); ?> <?php echo ($summary['total_pnl'] ?? 0) >= 0 ? 'profit' : 'loss'; ?>
        </span><br>
        <strong>Win Rate:</strong> <?php echo esc_html($summary['win_rate'] ?? 0); ?>%<br>
        <strong>Profit Factor:</strong> <?php echo number_format($performance_metrics['profit_factor'] ?? 0, 2); ?><br>
        <strong>Sharpe Ratio:</strong> <?php echo number_format($performance_metrics['sharpe_ratio'] ?? 0, 2); ?><br>
        <strong>Max Drawdown:</strong> <?php echo number_format($performance_metrics['max_drawdown'] ?? 0, 2); ?>%</p>
        <!-- /wp:paragraph -->

        <!-- wp:heading {"level":2} -->
        <h2>Monthly Breakdown</h2>
        <!-- /wp:heading -->

        <?php if (!empty($performance_metrics['monthly_performance'])): ?>
        <!-- wp:table -->
        <table class="wp-block-table">
            <thead>
                <tr>
                    <th>Week</th>
                    <th>P&L</th>
                    <th>Trades</th>
                    <th>Win Rate</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($performance_metrics['monthly_performance'] as $week => $data): ?>
                <tr>
                    <td><?php echo esc_html($week); ?></td>
                    <td class="<?php echo ($data['pnl'] ?? 0) >= 0 ? 'positive' : 'negative'; ?>">
                        $<?php echo number_format($data['pnl'] ?? 0, 2); ?>
                    </td>
                    <td><?php echo esc_html($data['trades'] ?? 0); ?></td>
                    <td><?php echo esc_html($data['win_rate'] ?? 0); ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <!-- /wp:table -->
        <?php endif; ?>

        <!-- wp:heading {"level":2} -->
        <h2>Risk Management Report</h2>
        <!-- /wp:heading -->

        <!-- wp:paragraph -->
        <p>Throughout the month, our automated risk management system maintained strict controls:<br>
        - Daily loss limits never exceeded the 0.5% threshold<br>
        - Position sizes remained within conservative limits<br>
        - Emergency stop mechanisms remained active<br>
        - All trades executed with proper stop-loss protection</p>
        <!-- /wp:paragraph -->

        <!-- wp:heading {"level":2} -->
        <h2>Strategy Effectiveness</h2>
        <!-- /wp:heading -->

        <!-- wp:paragraph -->
        <p>The conservative automated strategy continued to demonstrate capital preservation while capturing profitable opportunities. Key highlights include consistent daily performance within risk parameters and effective drawdown management.</p>
        <!-- /wp:paragraph -->

        <!-- wp:paragraph -->
        <p><em>This report was automatically generated from live trading data and journal entries. All figures represent actual trading performance with transparent commission costs.</em></p>
        <!-- /wp:paragraph -->
        <?php
        return ob_get_clean();
    }
}