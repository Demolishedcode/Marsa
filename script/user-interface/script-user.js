// jQuery
$(document).ready(function(){
    var menuShow = false,
        screenWidth = $(window).width();

    $('.menu-trigger img').click(function(){
        if (menuShow) {
            if (screenWidth <= 700) {
                $('.nav-inner').css('margin-left','-200px');
            } else if (screenWidth > 700) {
                $('nav').css('width','50px');
            }

            $('.menu-trigger img').attr('src','css/afbeeldingen/bars.png');
            menuShow = false
        } else {
            $('nav').css('width','200px');
            $('.menu-trigger img').attr('src','css/afbeeldingen/close.png');
            menuShow = true;
        }
    });

    $('.responsive-menu img').click(function(){
        if (!menuShow) {
            $('.nav-inner').css('margin-left','0px');
            $('.menu-trigger img').attr('src','css/afbeeldingen/close.png');
            menuShow = true;
        }
    });


    $(window).resize(function(){
        // Get screen width up to date
        screenWidth = $(window).width();

        if (screenWidth < 700) {
            $('.nav-inner').css('margin-left','-200px');
            menuShow = false;
        } else {
            $('.nav-inner').css('margin-left','0px');
            $('.menu-trigger img').attr('src','css/afbeeldingen/bars.png');
            menuShow = false;
        }
    });
});
