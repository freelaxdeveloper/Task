<?php
namespace Core;

class ErrorHandler
{
    public $errors = [];
    protected $message = 'Произошла ошибка, сообщите об этом администратору!';

    public function register()
    {
        # обработчик для warning ошибок (предупреждения)
        set_error_handler([$this, 'warningErrorHandler']);
        # обработчик для необработанных исключений
        set_exception_handler([$this, 'exceptionErrorHandler']);
    }
    public function warningErrorHandler($errno, $errorstr, $file, $line)
    {
        # записываем в лог
        $this->setErrors($errno, $errorstr, $file, $line);
        # выводим сообщение на экран
        $this->showMessage($this->message);
    }
    public function exceptionErrorHandler($e)
    {
        # записываем в лог
        $this->setErrors(get_class($e), $e->getMessage(), $e->getFile(), $e->getLine());
        # выводим сообщение на экран
        $this->showMessage($this->message);
    }
    protected function setErrors($errno, $errorstr, $file, $line)
    {
        $this->errors[] = [
            'errno' => $errno,
            'errorstr' => $errorstr,
            'file' => $file,
            'line' => $line
        ];
    }
    # вывод сообщения на экран
    protected function showMessage(string $message)
    {
        echo $message;
    }
    # сохраням ошибки в лог
    private function log()
    {
        $log_path = H . '/System/errorLog.txt';
        $f = fopen($log_path, 'a+');
        for ($i = 0; $i < count($this->errors); $i++) {
            $error = 'Ошибка: №' . $this->errors[$i]['errno'] . " (" . date('Y-m-d H:i:s') . ")\nОписание: " . $this->errors[$i]['errorstr'] . "\nФайл: " . $this->errors[$i]['file'] . "\nСтрока: " . $this->errors[$i]['line'] . "\n---------\n";
            fwrite($f, $error);
        }
        fclose($f);
        chmod($log_path, 0777);
    }
    public function __destruct()
    {
        $this->log();
    }
}
