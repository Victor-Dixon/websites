// Beverage Menu Public JavaScript
jQuery(document).ready(function($) {
    console.log('Beverage Menu Public JS loaded');
    
    // Add any public-facing JavaScript here
    $('.beverage-item').hover(
        function() {
            $(this).addClass('beverage-item-hover');
        },
        function() {
            $(this).removeClass('beverage-item-hover');
        }
    );
});