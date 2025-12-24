<div class="trp-dashboard">
    <div class="trp-metrics-grid">
        <div class="trp-metric-card">
            <h4>Total P&L</h4>
            <div class="value <?php echo $metrics['total_pnl'] >= 0 ? 'positive' : 'negative'; ?>">
                $<?php echo number_format($metrics['total_pnl'], 2); ?>
            </div>
        </div>
        <div class="trp-metric-card">
            <h4>Win Rate</h4>
            <div class="value">
                <?php echo number_format($metrics['win_rate'], 1); ?>%
            </div>
        </div>
        <div class="trp-metric-card">
            <h4>Total Trades</h4>
            <div class="value">
                <?php echo intval($metrics['trades_count']); ?>
            </div>
        </div>
        <div class="trp-metric-card">
            <h4>Profit Factor</h4>
            <div class="value">
                <?php echo number_format($metrics['profit_factor'], 2); ?>
            </div>
        </div>
    </div>
    
    <div class="trp-chart-container">
        <h3>Performance History</h3>
        <!-- Placeholder for chart -->
        <div class="trp-chart-placeholder" style="background:#f0f0f0; height:300px; display:flex; align-items:center; justify-content:center;">
            Chart Visualization Loading...
        </div>
    </div>
</div>
