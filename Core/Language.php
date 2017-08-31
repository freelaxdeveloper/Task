<?php
namespace Core;

use \Core\DB;

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
        if (isset($this->words[$string])) {
            return $this->words[$string];
        }
        $this->addWord($string);
        return $string;
    }
    private function addWord(string $string)
    {
        $this->words[$string] = $string;
    }
    private function getBaseWords()
    {
        static $words;
        if (!$words && file_exists($this->getFileLocalize())) {
            $words = parse_ini_file($this->getFileLocalize());
        }
        return $words;
    }
    private function getFileLocalize()
    {
        return H . '/Static/languages/' . $this->lang . '.ini';
    }
    private function update()
    {
        if (!file_exists($this->getFileLocalize())) {
            touch($this->getFileLocalize());
            chmod($this->getFileLocalize(), 0777);
        }
        $result = [];
        foreach ($this->words AS $key => $value) {
            $result[] = $key . ' = "' . $value . '";';
        }
        file_put_contents($this->getFileLocalize(), implode("\r\n", $result));
    }
    public function __destruct()
    {
        $this->update();
    }
}
