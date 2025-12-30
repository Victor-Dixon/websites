(function() {
    'use strict';
    document.addEventListener('DOMContentLoaded', function() {
        var menuToggle = document.querySelector('.mobile-menu-toggle');
        var navigation = document.querySelector('.main-navigation');
        if (menuToggle && navigation) {
            menuToggle.addEventListener('click', function() {
                navigation.classList.toggle('active');
                menuToggle.setAttribute('aria-expanded', navigation.classList.contains('active'));
            });
        }
    });
})();
