<?php

class DataBase{
    
    private PDO $pdo;
    private bool $isconnected = false;
    private string $error = '';

    public function __construct(){
        $this->connectDb();
    }

    private function connectDb(){
        $configPath = __DIR__ . '/../config.ini';
        
        if (!file_exists($configPath)) {
            $this->error = "config.ini не найден, используются настройки по умолчанию";
            $this->connectDefault();
            return;
        }
        
        $config = parse_ini_file($configPath, true);
        
        if ($config === false || !isset($config['database'])) {
            $this->error = "Ошибка чтения config.ini, используются настройки по умолчанию";
            $this->connectDefault();
            return;
        }
        
        $host = $config['database']['host'] ?? 'localhost';
        $db = $config['database']['db'] ?? 'test';
        $charset = $config['database']['charset'] ?? 'utf8';
        $user = $config['database']['user'] ?? 'root';
        $pass = $config['database']['pass'] ?? '';
        
        $dns = "mysql:host=$host;dbname=$db;charset=$charset";
        
        try {
            $this->pdo = new PDO($dns, $user, $pass);
            $this->isconnected = true;
        }
        catch (PDOException $ex) {
            $this->error = "Ошибка подключения: " . $ex->getMessage();
            $this->isconnected = false;
            $this->connectDefault();
        }
    }

    private function connectDefault(){
        try {
            $this->pdo = new PDO(
                'mysql:host=localhost;dbname=test;charset=utf8',
                'root',
                '',
            );
            $this->isconnected = true;
        }
        catch (PDOException $e) {
            $this->error = "Ошибка подключения по умолчанию: " . $e->getMessage();
            $this->isconnected = false;
        }
    }

    public function getPdo() : PDO{
        return $this->pdo;
    }

    public function isConnected() : bool{
        return $this->isconnected;
    }

    public function getError() : string{
        return $this->error;
    }
}