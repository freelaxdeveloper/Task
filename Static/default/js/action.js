$(function(){
    /* ловим наведение мыши на меню */
    $('nav ul span.menu_action, .listing-menu').click(function(){
        // узнали ID какого элемента будем показывать
        var action_id = $('#' + this.id).attr('data-action');

        $('#' + action_id).slideToggle();
    });
    /* если теряем фокус с действиий, скрываем его */
    $('.action_load').mouseleave(function(){
        $('.action_load').hide();
    });


    $('.current_lang').click(function(){
        $('.localize').fadeIn();
        $('.current_lang').fadeOut();
    });

    $('#captcha').click(function(){
        console.log('Клик по капче');
        document.getElementById('captcha').src='/captcha.jpg';
    });


});
