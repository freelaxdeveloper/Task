<?php
namespace Core;

use \Core\{DB,App};

/*
    Некоторые ураинские и русские слова пишутся одинаково, поэтому в таком случае
    переводимый текст будет сохранен как три звездочки (***), что бы не злоупотреблять сервисом
    по переводу текста
*/
class Language{
    public $lang;
    private $words;
    private $is_update = false;
    private $config;

    public function __construct(string $lang)
    {
        $this->lang = $lang;
        $this->config = $this->getConfig();
        $this->words = $this->getBaseWords();
    }
    private function getConfig(): array
    {
        static $config;
        if (!$config) {
            $config = App::config('languages', true);
        }
        return $config;
    }
    # возвращает ключи языков (@return [ru,en,uk...])
    public function getKeys(): array
    {
        return array_keys($this->config['list']);
    }
    # проверяем существующий ли передан язык
    private function is_lang(): bool
    {
        return array_key_exists($this->lang, $this->config['list']) ? true : false;
    }
    # переводим строку
    public function translate(string $string): string
    {
        # если язык русский то переводить не нужно
        if ($this->lang == 'ru' || !$this->is_lang()) {
            return $string;
        }
        if (isset($this->words[$string])) {
            # если перевод совпадает с исходным текстом, пробуем его перевести
            if ($this->words[$string] == $string) {
                $this->is_update = true;
                $this->words[$string] = $this->autoTranslate($string);
            }
            # если переведено как ***, возвращаем исходный текст
            if ($this->words[$string] == '***') {
                return $string;
            }
            return $this->words[$string];
        }
        $this->is_update = true; // говорим что нужно обновить словарь
        $this->addWord($string); // добавли новое слово в словарь
        return $string;
    }
    public function name(): string
    {
        $name = $this->config['list'][$this->lang] ?? 'Неизвестно';
        return __($name);
    }
    # автоперевод с попмощю API сервиса multillect.com
    private function autoTranslate(string $string): string
    {
        $data = file_get_contents('https://api.multillect.com/translate/json/1.0/' . $this->config['set']['id'] . '?method=translate/api/translate&from=ru&to=' . $this->lang . '&text=' . $string . '&sig=' . $this->config['set']['sig']);
        $data = json_decode($data);
        if (!$translated = $data->result->translated) {
            return $string;
        }
        $translated = trim($translated);
        if ($translated == $string) {
            # если перевод совпадает с исходным текстом, тогда перевод это звездочки =)
            $translated = '***';
        }
        return $translated;
    }
    # добавление нового слова в словарь
    private function addWord(string $string)
    {
        $this->words[$string] = $string;
        return;
    }
    # получение списка слов
    private function getBaseWords()
    {
        static $words;
        if (!$words && file_exists($this->getFileLocalize())) {
            $words = parse_ini_file($this->getFileLocalize());
        }
        return $words;
    }
    # файл локализации
    private function getFileLocalize()
    {
        return H . '/Resources/lang/' . $this->lang . '.ini';
    }
    # обновляем список локализации
    private function update()
    {
        if ($this->lang == 'ru' || !$this->is_update || !$this->is_lang()) {
            return;
        }
        if (!file_exists($this->getFileLocalize())) {
            touch($this->getFileLocalize());
            chmod($this->getFileLocalize(), 0777);
        }
        $result = [];
        foreach ($this->words AS $key => $value) {
            $result[] = $key . ' = "' . $value . '"';
        }
        file_put_contents($this->getFileLocalize(), implode("\r\n", $result));
        $this->is_update = true;
        return;
    }
    public function __destruct()
    {
        $this->update();
    }
}
