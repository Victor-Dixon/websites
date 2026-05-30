(function($) {
    wp.customize('link_color', function(value) {
        value.bind(function(newval) {
            $('a').css('color', newval);
        });
    });
})(jQuery);
