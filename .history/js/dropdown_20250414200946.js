jQuery(document).ready(function($) {
    // For mobile devices, make dropdowns toggle on click
    $('.dropdown-toggle').on('click', function(e) {
        if ($(window).width() <= 768) {
            e.preventDefault();
            $(this).parent().find('.dropdown-content').toggle();
        }
    });
});