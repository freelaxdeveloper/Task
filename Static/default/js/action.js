$(function(){
    function classAction(action_id)
    {
        this.id = action_id; // ID списка дейтсвий
        this.action = $('#' + this.id); // объект действий

        /* выводим/скрываем действия */
        this.display = function()
        {
            // если действия скрыты значит будем показывать
            if (!this.checkDisplay()) {
                this.actionShow();
                this.log('Показываем');
            } else { // иначе скрываем
                this.actionHide();
                this.log('Скрываем');
            }
        }
        /* показываем действия */
        this.actionShow = function()
        {
            // прежде чем показать скрываем всё что уже активно
            this.actionHide();
            // показываем действия выбранного проекта
            this.action.attr('class', 'action_display');
            //this.action.css('opacity', '1');
        }
        /* скрываем действия */
        this.actionHide = function()
        {
            $('.action_display').attr('class', 'display_none');
            //this.action.css('opacity', '0');

        }
        /* проверяем показываются выбранные действия или нет */
        this.checkDisplay = function()
        {
            if (this.action.css('display') == 'none') {
                return false;
            } else {
                return true;
            }
        }
        /* пишем логи в консоль */
        this.log = function(message)
        {
            //console.log(message + ' (' + this.id + ')');
        }
    }
    /* ловим клик по действиям */
    $('nav ul span, .listing-menu').click(function(){
        var action_id = $('#' + this.id).attr('data-action');
        var action = new classAction(action_id);
        action.display();
    });
});
