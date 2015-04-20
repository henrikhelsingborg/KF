/* HBG dev JS - To merged into app.js, minified with grunt */
jQuery(document).foundation({

});


jQuery(document).ready(function(){
    setTimeout(function () {
        $(window).trigger('resize');
    }, 1000);

    $('.support-nav').prepend('<a href="#" class="show-support-nav"><span class="arrow-icon"></span>Toppmeny</a>');

    $('.show-support-nav:not(.show-support-nav-mobile)').bind('click', function(){
        $('.support-nav-list').toggle();
        $(this).toggleClass('active');
    });

    $('.show-support-nav-mobile').bind('click', function () {
        $('#support-nav-mobile').toggle();
        $(this).toggleClass('active');
    })

    $('.show-mobile-nav').bind('click', function(){
        $(this).toggleClass('active');
    });

    $(window).on('resize', function () {
        var $offcanvas = $('.off-canvas-wrap');
        var $mobilemenubutton = $('.show-mobile-nav');
        if ($(window).width() > 640 && $offcanvas.hasClass('move-right')) {
            $offcanvas.removeClass('move-right');
            $mobilemenubutton.removeClass('active');
        }

        if ($(window).width() <= 640 && !$('.support-nav-mobile #google-translate-element').length) {
            $('#google-translate-element').detach().appendTo('.support-nav-mobile');
        } else if ($(window).width() > 640 && $('.support-nav-mobile #google-translate-element').length) {
            $('#google-translate-element').detach().insertAfter('.google-translate-toggle');
        }
    });

    $('.exit-off-canvas').bind('click', function(){
        if($('.show-mobile-nav').hasClass('active')) {
            $('.show-mobile-nav').removeClass('active');
        }
    });

    $('.show-mobile-search').bind('click', function(e){
        $('.mobile-search').toggle();
        e.preventDefault();
        $(this).toggleClass('active');
    });

    if($('.table-list').length > 0) {
        $('.table-list').delegate('tbody tr.table-item','click', function(){
            if(!$(this).is('.active')) {
                $('.table-item').removeClass('active');
                $('.table-content').removeClass('open');
                $(this).addClass('active');
                $(this).next('.table-content').addClass('open');
            } else if($(this).hasClass('active')) {
                $(this).toggleClass('active');
                $(this).next('.table-content').removeClass('open');
            }
        });
    }

    // Facebook share popup
    $('.socialmedia-list a').on('click', function (e) {
        e.preventDefault();

        // Width and height of the popup
        var width = 626;
        var height = 305;

        // Gets the href from the button/link
        var url = $(this).attr('href');

        // Calculate popup position
        var leftPosition = (window.screen.width / 2) - ((width / 2) + 10);
        var topPosition = (window.screen.height / 2) - ((height / 2) + 50);

        // Popup window features
        var windowFeatures = "status=no,height=" + height + ",width=" + width + ",resizable=no,left=" + leftPosition + ",top=" + topPosition + ",screenX=" + leftPosition + ",screenY=" + topPosition + ",toolbar=no,menubar=no,scrollbars=no,location=no,directories=no";

        // Open popup
        window.open(url, 'Share', windowFeatures);
        return false;
    });

    // Toggle the Google Translate "box"
    $('.google-translate-toggle').on('click', function (e) {
        e.preventDefault();
        $('#google-translate-element').fadeToggle(200);
    });

    // Hides the Google Translate "box" on "click outside"
    $(document).on('click', function (e) {
        var translate_container = $('#google-translate-element');
        var translate_toggle = $('.google-translate-toggle');
        if (!translate_container.is(e.target) && translate_container.has(e.target).length === 0 && !translate_toggle.is(e.target) && translate_toggle.has(e.target).length === 0) {
            translate_container.fadeOut(200);
        }
    });

    // Search button loading indicator
    $('#searchsubmit').on('click', function (e) {
        $(this).html('<div class="loader"></div>');
        return true;
    });
});
