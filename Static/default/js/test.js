$(function(){
    function classAction(action_id){
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
        }
        /* скрываем действия */
        this.actionHide = function()
        {
            $('.action_display').attr('class', 'action_none');
        }
        /* проверяем показываются выбранные действия или нет */
        this.checkDisplay = function()
        {
            if (this.action.css('opacity') == 0) {
                return false;
            } else {
                return true;
            }
        }
        /* пишем логи в консоль */
        this.log = function(message)
        {
            console.log(message + ' (' + this.id + ')');
        }
    }
    /* ловим клик по действиям */
    $('nav ul span').click(function(){
        var action_id = $('#' + this.id).attr('data-action');
        var action = new classAction(action_id);
        action.display();
    });
    /*-------------------------------------------------*/
    function classProject(){
        this.hideLinkAddProject = function()
        {
            $('#add_project').fadeOut('slowe');
            $('#formProject').fadeIn('slowe');
        }
        this.showLinkAddProject = function()
        {
            $('#add_project').fadeIn('slowe');
            $('#formProject').fadeOut('slowe');
        }
    }
    /* ловим клик по добавлению проекта */
    $('#add_project').click(function(){
        var project = new classProject();
        project.hideLinkAddProject();

        $('#cancelProject').click(function(){
            project.showLinkAddProject();
        });
        $('#typeProject').click(function(){
            var colors = ['green', 'red', 'blue', 'yellow'];
            // var key = Math.round(Math.random()*(colors.length-1));
            key = (key+1) % colors.length;
            $('.typeProject').css('background-color', colors[key]);
            console.log(key);
        });

    });


});
