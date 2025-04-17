<?php
    namespace App\DB;
    use PDO;
    use Exception;

    // database class
    class DB{
        private static $instance = null;
        private $connection;
        
        private static $HOST = HOST;
        private static $PORT = PORT;
        private static $USER = USER;
        private static $PASSWORD = PASSWORD;
        private static $DATABASE = DATABASE;
        
        private function __construct(){
            try{
                $this->connection =  new PDO("mysql:host=".self::$HOST.";dbname=".self::$DATABASE, self::$USER, self::$PASSWORD);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            }catch(Exception $Ex){
                throw new Exception("Error: ".$Ex->getMessage());
            }
        }

        public static function getConnection(){
            if(self::$instance === null){
                self::$instance = new DB();
            }
            return self::$instance->connection;
        }
    }   
?>