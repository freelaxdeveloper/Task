<?php
namespace Models;

class Form{
    public $action;
    public $method = 'POST';
    public $form;
    public $input;
    public $submit;
    public $captcha = false;
    public $class;
    public $id;

    public function __construct(string $action)
    {
        $this->action = $action;
    }
    public function input(array $params)
    {
        extract($params);
        $title = $title ?? '';
        $holder = $holder ?? '';
        $value = $value ?? '';
        $type = $type ?? 'text';
        $br = $br ?? true;
        $this->input[] = ($title ? $title . ':<br />' : '') . '<input type="' . $type . '" name="' . $name . '" value="' . $value . '" placeholder="' . $holder . '">' . ($br ? '<br />' : '');
    }
    public function html(string $html)
    {
        $this->input[] = $html;
    }
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
    public function submit(array $params)
    {
        extract($params);
        $value = $value ?? 'Отправить';
        $name = $name ?? 'send';
        $class = $class ?? '';
        $br = $br ?? true;
        $this->submit[] = '<input class="' . $class . '" type="submit" name="' . $name . '" value="' . $value . '">' . ($br ? '<br />' : '');
    }
    public function display(): string
    {
        $id = $this->id ? 'id="' . $this->id . '" ' : '';
        $this->form = '<form ' . $id . 'class="form ' . $this->class . '" action="' . $this->action . '" method="' . $this->method . '">';
        for ($i = 0; $i < count($this->input); $i++) {
            $this->form .= $this->input[$i];
        }
        if ($this->captcha) {
            $this->form .= '<div class="captcha-form">';
                $this->form .= '<div class="captcha-input"><input type="text" name="captcha" placeholder="Введите капчу" size="11"></div>';
                $this->form .= '<div class="captcha-img"><img src="/captcha.jpg" alt="captcha" id="captcha" title="Нажмите для обновления капчи" /></div>';
            $this->form .= '</div>';
        }
        for ($i = 0; $i < count($this->submit); $i++) {
            $this->form .= $this->submit[$i];
        }
        $this->form .= '</form>';
        return $this->form;
    }
}
