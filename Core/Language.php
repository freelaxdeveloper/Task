<?php
namespace Core;

use \Core\DB;

/*
    Некоторые ураинские и русские слова пишутся одинаково, поэтому в таком случае
    переводимый текст будет сохранен как три звездочки (***), что бы не злоупотреблять сервисом
    по переводу текста
*/
class Language{
    public $lang;
    private $words;

    public function __construct(string $lang)
    {
        $this->lang = $lang;
        $this->words = $this->getBaseWords();
    }
    public function translate(string $string): string
    {
        if ($this->lang == 'ru') {
            return $string;
        }
        if (isset($this->words[$string])) {
            # если перевод совпадает с исходным текстом, пробуем его переводить
            if ($this->words[$string] == $string) {
                $this->words[$string] = $this->autoTranslate($string);
            }
            # если переведено как ***, возвращаем исходный текст
            if ($this->words[$string] == '***') {
                return $string;
            }
            return $this->words[$string];
        }
        # добавли новое слово в словарь
        $this->addWord($string);
        return $string;
    }
    public function name(): string
    {
        switch ($this->lang) {
            case 'ru' : return __('Русский');
            case 'en' : return __('Английский');
            case 'uk' : return __('Украинский');
        }
        return 'Неизвестно';
    }
    # автоперевод с попмощю API сервиса multillect.com
    private function autoTranslate(string $string): string
    {
        $data = file_get_contents('https://api.multillect.com/translate/json/1.0/561?method=translate/api/translate&from=ru&to=' . $this->lang . '&text=' . $string . '&sig=1b779fccacb995e5971cbb7e1adc371f');
        $data = json_decode($data);
        $translated = trim($data->result->translated);
        if ($translated == $string) {
            $translated = '***';
        }
        return $translated ?? $string;
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
        return H . '/Static/languages/' . $this->lang . '.ini';
    }
    # обновляем список локализации
    private function update()
    {
        if ($this->lang == 'ru') {
            return;
        }
        if (!file_exists($this->getFileLocalize())) {
            touch($this->getFileLocalize());
            chmod($this->getFileLocalize(), 0777);
        }
        $result = [];
        foreach ($this->words AS $key => $value) {
            $result[] = $key . ' = "' . $value . '";';
        }
        file_put_contents($this->getFileLocalize(), implode("\r\n", $result));
        return;
    }
    public function __destruct()
    {
        $this->update();
    }
}
