$(function(){
    var key = 0;
    // текущее значение цвета
    var current_color = $('input[name=color_edit]').attr('value');
    // устанавливаем текущее значение цвета в блок цвета формы
    $('#ProjectEdit').css('background-color', current_color);

    $('#ProjectEdit').click(function(){
        var colors = ['red', 'green', 'blue', 'yellow','black'];
        // ключ цвета проекта в массиве всех цветов
        var current_key = colors.indexOf(current_color);
        // удаляем цвет проекта из массива
        colors.splice(current_key, 1);

        key = (key + 1) % colors.length;
        $('#ProjectEdit').css('background-color', colors[key]);
        $('input[name=color_edit]').attr('value', colors[key]);
    });
});
