<?php
class Connection {
    private  static $connection = null;

    private static $showColumnsSQL = 'SHOW COLUMNS FROM pokemon';

    private static $selectSQL = 'SELECT * FROM pokemon ORDER BY id, name';

    private function __construct()
    {
        
    }    
    
    public static function GetInstance() {
        if (self::$connection === null) {
            try {
                self::$connection = new PDO(
                    'mysql:host=localhost;dbname=pokemondatabase',
                    'pokemonuser',
                    'pokemonpassword',
                    array(
                        PDO::ATTR_PERSISTENT => true,
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8'
                    )
                );
            } catch (PDOException $e) {
                header('Location:..');
                exit;
            }
        }

        return self::$connection;
    }

    public static function ExecuteSentence($sql) {
        try {
            $sentence = self::$connection->prepare($sql);
            $sentence->execute();
            return $sentence;
        } catch(PDOException $e) {
            header('Location:..');
            exit;
        }
    }

    public static function PrepareStatements($sql) {
        try {
            $sentence = self::$connection->prepare($sql);
            return $sentence;
        } catch(PDOException $e) {
            header('Location:..');
            exit;
        }
    }

    public static function ClearConnection() {
        self::$connection = null;
    }

}