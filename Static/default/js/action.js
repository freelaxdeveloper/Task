$(function(){
    /* ловим наведение мыши на меню */
    $('nav ul span.menu_action, .listing-menu').mouseover(function(){
        // узнали ID какого элемента будем показывать
        var action_id = $('#' + this.id).attr('data-action');
        // скрыли все элементы которые сейчас показываются
        $('.action_load').attr('class', 'action_load display_none');
        // показываем выбранный элемент
        $('#' + action_id).attr('class', 'action_load action_display');
    });
    /* если теряем фокус с действиий, скрываем его */
    $('.action_load').mouseleave(function(){
        $('.action_load').attr('class', 'action_load display_none');
    });
});
