<?php
namespace Models;

class Form{
    public $action;
    public $method;
    public $form;
    public $input;

    public function __construct(string $action, string $method = 'POST')
    {
        $this->action = $action;
        $this->method = $method;
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
    public function display(): string
    {
        $this->form = '<form class="form" action="' . $this->action . '" method="' . $this->method . '">';
        for ($i = 0; $i < count($this->input); $i++) {
            $this->form .= $this->input[$i];
        }
        $this->form .= '</form>';
        return $this->form;
    }
}
