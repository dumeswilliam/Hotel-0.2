<?php

class Db {
    
    private static $instance;
    
    private $connection;
    private $connected = false;

    private function __construct() {
        $this->connect();
    }

    public function __destruct() {
        $this->close();
    }
    
    public static function getInstance() {
        if (!isset(self::$instance)) self::$instance = new self;
        return self::$instance;
    }
    
    public function connect() {
        global $_CONFIG; 
        $toconnect = '';
        foreach ($_CONFIG['db'] as $key => $value) 
            $toconnect .= " $key=$value ";
        $this->connection = pg_connect($toconnect);
        if ($this->connection) $this->connected = true;
    }

    public function close() {
        if ($this->connected) pg_close($this->connection);
    }

    public function execute($sql, $all = true) {
        if ($this->connected && ($result = pg_query($this->connection, $sql)))
            return $all ? pg_fetch_all($result) : pg_fetch_array($result, null, PGSQL_ASSOC);
        return false;
    }

    public static function init() {
        self::getInstance()->execute("
            CREATE SCHEMA IF NOT EXISTS hotel;

            CREATE TABLE IF NOT EXISTS hotel.daily (
                id SERIAL PRIMARY KEY,
                day VARCHAR(3) NOT NULL,
                check_out TIME NOT NULL,
                value FLOAT NOT NULL,
                garage FLOAT
            );
            
            CREATE TABLE IF NOT EXISTS hotel.person (
                document VARCHAR(11) PRIMARY KEY,
                name TEXT NOT NULL,
                phone TEXT
            );
            
            CREATE TABLE IF NOT EXISTS hotel.check (
                id SERIAL PRIMARY KEY, 
                document VARCHAR(11) NOT NULL,
                check_in TIMESTAMP NOT NULL,
                check_out TIMESTAMP,
                garage SMALLINT DEFAULT 0,
                paid FLOAT,
                FOREIGN KEY (document) REFERENCES hotel.person (document) 
            )
        ");
        
        $result = self::getInstance()->execute('SELECT * FROM hotel.daily');
        if (!$result) {
            self::getInstance()->execute("
                INSERT INTO hotel.daily (day, check_out, value, garage) VALUES 
                ('dom', '16:30:59', 150, 20),
                ('seg', '16:30:59', 120, 15),
                ('ter', '16:30:59', 120, 15),
                ('qua', '16:30:59', 120, 15),
                ('qui', '16:30:59', 120, 15),
                ('sex', '16:30:59', 120, 15),
                ('sab', '16:30:59', 150, 20);
            ");

        }

    }

}
