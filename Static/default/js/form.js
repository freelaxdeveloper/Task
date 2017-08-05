$(function(){
    function classForm(id)
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
            var form = new classForm(id);
            form.hideLinkAddProject();
            $('.cancel').click(function(){
                form.showLinkAddProject();
                return false;
            });
            var key = 0;
            $('#type' + id).click(function(){
                var colors = form.getColors();
                key = (key+1) % colors.length;
                $('#type' + id).css('background-color', colors[key]);
                $('input[name=color]').attr('value', colors[key]);
            });
            return false;
        });


    });
});
