(function($){
    $(".user-account-nav-item").click(function() {
        var li = $(this).parent(),
            ul = li.find("ul"),
            height = ul.height();
            if(ul.is(':visible')){
                ul.stop(true).slideUp();
            }else{
                ul.slideDown('slow');
            }
    }).trigger('click');
})(jQuery);