$('nav ul span').click(function(){
    var action_id = $('#' + this.id).attr('data-action');
    var action = $('#' + action_id);

    /* если действие не активировано, актвируем его */
    if (action.css('opacity') == '0') {
        /* любые ранее активированные действия делаем не активными */
        $('.action_display').attr('class', 'action_none');
        /* активируем действие по которому кликнули */
        action.attr('class', 'action_display');
    } else { /* если уже было активировано то скрываем */
        $('.action_display').attr('class', 'action_none');
    }
});
