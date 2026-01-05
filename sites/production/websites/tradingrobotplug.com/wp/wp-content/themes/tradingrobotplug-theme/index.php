<?php
/*
C:\TheTradingRobotPlugWeb\my-custom-theme\index.php
Description: The main index template for The Trading Robot Plug theme, displaying the main content loop and handling post navigation.
Version: 1.1.0
Author: Victor Dixon
*/
?>

<?php get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <?php
        if (have_posts()) :
            while (have_posts()) : the_post();
                get_template_part('template-parts/content', get_post_format());
            endwhile;

            // Previous/next page navigation
            the_posts_navigation();
        else :
            get_template_part('template-parts/content', 'none');
        endif;
        ?>
    </main><!-- .site-main -->
</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>

<!-- Chart for Polygon Data -->
<div class="chart-container">
    <canvas id="polygonChart"></canvas>
</div>

<!-- Button to Fetch Real-Time Data -->
<button id="fetch-real-time-data">Fetch Real-Time Data</button>

<!-- Chart for Real-Time Data -->
<div class="chart-container">
    <canvas id="realTimeChart"></canvas>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fetch Polygon Data and Generate Chart
    fetch('<?php echo get_rest_url(null, 'tradingrobotplug/v1/fetchpolygondata'); ?>')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('polygonChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Polygon Data',
                        data: data.values,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        fill: false
                    }]
                }
            });
        });

    // Fetch Real-Time Data on Button Click and Generate Chart
    document.getElementById('fetch-real-time-data').addEventListener('click', function() {
        fetch('<?php echo get_rest_url(null, 'tradingrobotplug/v1/fetchrealtime'); ?>')
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('realTimeChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Real-Time Data',
                            data: data.values,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 2,
                            fill: false
                        }]
                    }
                });
            });
    });
});
</script>
