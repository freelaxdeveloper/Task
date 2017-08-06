$(function(){
    /* ловим наведение мыши на меню */
    $('nav ul span.menu_action, .listing-menu').mouseover(function(){
        // узнали ID какого элемента будем показывать
        var action_id = $('#' + this.id).attr('data-action');
        // скрыли все элементы которые сейчас показываются
        $('.action_load').removeClass('action_display').addClass('display_none');
        // показываем выбранный элемент
        $('#' + action_id).removeClass('display_none').addClass('action_display');
    });
    /* если теряем фокус с действиий, скрываем его */
    $('.action_load').mouseleave(function(){
        $('.action_load').removeClass('action_display').addClass('display_none');
    });
});
