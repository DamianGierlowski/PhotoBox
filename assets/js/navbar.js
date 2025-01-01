$(document).ready(function() {
    const $navbar = $('#navbar');
    const $navbarToggle = $('#navbarToggle');
    const $body = $('body');

    $navbarToggle.on('click', function() {
        $navbar.toggleClass('-translate-x-full');
        $body.toggleClass('overflow-hidden');
    });

    $(window).on('resize', function() {
        if ($(window).width() >= 1024) {
            $navbar.removeClass('-translate-x-full');
            $body.removeClass('overflow-hidden');
        } else {
            $navbar.addClass('-translate-x-full');
        }
    });
});

