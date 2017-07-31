<?php
namespace Core;

abstract class App{
    /*
     * в любой не понятной ситуации ошибка 404
     */
    static function access_denied($message = '')
    {
        if (true) {
            die($message);
        }
        header("HTTP/1.1 404 Not Found");
        exit;
    }
    /*
    * принимаем полный путь к директории и пытаемся создать всё их древо
    */
    static function mkdir(string $path)
    {
        $dir_path = '';
        # если директории нету начинаем создавать
        if (!is_dir($path)) {
            # разбиваем путь на подкатегории
            $dirs = explode('/', $path);
            # проверяем все категории, при необходимости создаем
            for ($i = 0; $i < count($dirs); $i++) {
                # если пусто, пропускаем
                if (!$dirs[$i]){
                    continue;
                }
                # за каждым разом добавляем по категории к нашему пути
                $dir_path .= '/' . $dirs[$i];
                # если уже есть категория, тоже пропускаем
                if (is_dir($dir_path)) {
                    continue;
                }
                # создаем новую категорию с правами для чтения и записи
                mkdir($dir_path);
                chmod($dir_path, 0777);
            }
        }
    }
}
