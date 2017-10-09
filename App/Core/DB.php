<?php
/*
  Класс для подключения к БД
  Можно использовать в любом месте движка
  $db = DB::me();
 */
namespace App\Core;

use \App\Core\App;

class DB
{
    private static $host;
    private static $user;
    private static $password;
    private static $db_name;
    private static $_instance;

    private $tableName;


    protected function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }
    protected function __clone()
    {
    }
    /*
     * Получаем данные от БД
     */
    private static function getConfig()
    {
        return App::config('db');
    }

    /**
     * @return PDO
     * @throws ExceptionPdoNotExists
     * @throws Exception
     */
    public static function me()
    {
        $args = self::getConfig();
        self::$host = $args['host'];
        self::$db_name = $args['db_name'];
        self::$user = $args['user'];
        self::$password = $args['password'];

        if (!self::$_instance) {
            if (!self::$db_name || !self::$user || !self::$host) {
                die(__('Введите параметры для подключения к базе данных'));
            }
            $dsn = 'mysql:host=' . self::$host . ';dbname=' . self::$db_name . ';charset=utf8';
            $opt = [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
              self::$_instance = new \PDO($dsn, self::$user, self::$password, $opt);
            } catch (Exception $e) {

            }
        }
        return self::$_instance;
    }
    // создаем объект для работы с указанной таблицей
    public static function table(string $tableName)
    {
        return new self($tableName);
    }
    // очищаем таблицу
    public function clear()
    {
        self::me()->query('TRUNCATE ' . $this->tableName);
    }
    // удаляем таблицу
    public function delete()
    {
        self::me()->query('DROP TABLE ' . $this->tableName);
    }
}
