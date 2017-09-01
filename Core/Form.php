<?php
namespace Core;

use \Core\App;

class Form{
    public $action; // экшен (string)
    public $method = 'POST'; // метод передачи данных (string)
    public $html_form; // итоговый HTML код формы (string)
    public $input; // содержимое формы (array)
    public $submit; // кнопки формы (array)
    public $captcha = false; // отображение капчи в форме (bool)
    public $class; // стили формы  (string)
    public $id; // ID формы  (string)

    public function __construct(string $action)
    {
        $this->action = $action;
    }
    # формируем поля типа text, password, hidden...
    public function input(array $params)
    {
        extract($params);
        $title = $title ?? '';
        $holder = $holder ?? '';
        $value = $value ?? '';
        $type = $type ?? 'text';
        $class = $class ?? '';
        $br = $br ?? true;
        $this->input[] = ($title ? $title . ':<br />' : '') . '<input' . ($class ? ' class="' . $class . '"' : '') . ' type="' . $type . '" name="' . $name . '" value="' . $value . '" placeholder="' . $holder . '">' . ($br ? '<br />' : '');
    }
    # формируем любую строку для отображения в форме
    public function html(string $html, bool $br = true)
    {
        $this->input[] = $html . ($br ? '<br />' : '');
    }
    # формируем выпадающий список типа select
    public function select(array $params)
    {
        extract($params);
        $title = $title ?? '';
        $br = $br ?? true;
        $select = ($title ? $title . ':' : '') . '<select name="' . $name . '">';
        for ($i = 0; $i < count($options); $i++) {
            $select .= '<option value="' . $options[$i]['value'] . '" ' . $options[$i]['selected'] . '>' . $options[$i]['title'] . '</option>';
        }
        $select .= '</select>' . ($br ? '<br />' : '');
        $this->input[] = $select;
    }
    # формируем кнопки типа submit
    public function submit(array $params)
    {
        extract($params);
        $value = $value ?? __('Отправить');
        $name = $name ?? 'send';
        $class = $class ?? '';
        $br = $br ?? true;
        $this->submit[] = '<input class="' . $class . '" type="submit" name="' . $name . '" value="' . $value . '">' . ($br ? '<br />' : '');
    }
    # возвращаем итоговый html код
    public function display(): string
    {
        $id = $this->id ? 'id="' . $this->id . '" ' : '';
        $this->html_form = '<form ' . $id . 'class="form ' . $this->class . '" action="' . App::url($this->action) . '" method="' . $this->method . '">';
        for ($i = 0; $i < count($this->input); $i++) {
            $this->html_form .= $this->input[$i];
        }
        if ($this->captcha) {
            $this->html_form .= $this->captcha();
        }
        for ($i = 0; $i < count($this->submit); $i++) {
            $this->html_form .= $this->submit[$i];
        }
        $this->html_form .= '</form>';
        return $this->html_form;
    }
    private function captcha(): string
    {
        $captcha = '<div class="captcha-form">';
        $captcha .= '<div class="captcha-input"><input type="text" name="captcha" placeholder="' . __('Введите капчу') . '" size="11"></div>';
        $captcha .= '<div class="captcha-img"><img src="/captcha.jpg" alt="captcha" id="captcha" title="' . __('Нажмите для обновления капчи') . '" /></div>';
        $captcha .= '</div>';
        return $captcha;
    }
}
