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
    /*-------------------------------------------------*/
    function classProject(id)
    {
        this.id = id;
        /* скрываем ссылку и показываем форму */
        this.hideLinkAddProject = function()
        {
            $('#' + this.id).fadeOut('slowe'); // скрыли ссылку
            $('#form' + this.id).fadeIn('slowe'); // скрыли форму
        }
        /* показываем ссылку и скрываем форму */
        this.showLinkAddProject = function()
        {
            $('#' + this.id).fadeIn('slowe'); // показали ссылку
            $('#form' + this.id).fadeOut('slowe'); // скрыли форму
        }
        this.getColors = function()
        {
            if ('Task' == this.id) {
                return ['red', 'green', 'yellow'];
            }
            return ['red', 'green', 'blue', 'yellow','black'];
        }
    }
    $('#Task, #Project').each(function(i, el) {
        $(el).click(function(){
            var id = $(this).attr('id');
            var form = new classProject(id);
            form.hideLinkAddProject();
            $('.cancel').click(function(){
                form.showLinkAddProject();
            });
            var key = 0;
            $('#type' + id).click(function(){
                var colors = form.getColors();
                key = (key+1) % colors.length;
                $('#type' + id).css('background-color', colors[key]);
                $('input[name=color]').attr('value', colors[key]);
            });
        });


    });
});
