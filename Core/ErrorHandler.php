<?php
namespace Core;

class ErrorHandler
{
    public $errors = [];
    protected $message = 'Произошла ошибка на сайте, сообщите о ней администрации!';

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
        echo __($message);
    }
    # сохраням ошибки в лог
    private function log()
    {
        $log_path = H . '/System/error_log.log';
        $errors = [];
        for ($i = 0; $i < count($this->errors); $i++) {
            $errors[] = 'Error: №' . $this->errors[$i]['errno'] . " (" . date('Y-m-d H:i:s') . ")\nDescription: " . $this->errors[$i]['errorstr'] . "\nFile: " . $this->errors[$i]['file'] . "\nLine: " . $this->errors[$i]['line'] . "\n---------\n";
        }
        error_log(implode("\n", $errors), 3, $log_path);
        chmod($log_path, 0777);
    }
    public function __destruct()
    {
        $this->log();
    }
}
