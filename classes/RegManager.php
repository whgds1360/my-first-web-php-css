<?php

class RegManager{
    private PDO $pdo;
    private string $status = '';

    private string $us_login;
    private string $us_password;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    private function getData(){
        $this->us_login = trim($_POST['us_login'] ?? '');
        $this->us_password = trim($_POST['us_password'] ?? '');
    }

    private function checkMatchPass() : bool{
        if (empty($_POST['auth_password'])) return false;
        return $this->us_password === $_POST['auth_password'];
    }

    private function checkLoginDuplicate() : bool{
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE us_login = :us_login");
        $stmt->execute([':us_login' => $this->us_login]);
        return $stmt->fetchColumn() == 0;
    }

    private function intoDb() : bool{
        $stmt = $this->pdo->prepare("INSERT INTO users (us_login, us_password) VALUES (:us_login, :us_password)");
        return $stmt->execute([
            ':us_login' => $this->us_login,
            ':us_password' => password_hash($this->us_password, PASSWORD_DEFAULT)
        ]);
    }

    public function createUser() : bool{
        $this->getData();
        
        if (empty($this->us_login) || empty($this->us_password) || empty($_POST['auth_password'])){
            $this->status = 'Заполните все поля';
            return false;
        }
        
        if (!$this->checkMatchPass()){
            $this->status = 'Пароли не совпадают';
            return false;
        }
        
        if (!$this->checkLoginDuplicate()){
            $this->status = 'Логин уже занят';
            return false;
        }
        
        if ($this->intoDb()){
            $_SESSION['us_login'] = $this->us_login;
            $this->status = 'Вы успешно зарегистрировались!';
            return true;
        }
        
        $this->status = 'Ошибка регистрации';
        return false;
    }

    public function getStatus() : string{
        return $this->status;
    }

    public function clearStatus(){
        $this->status = '';
    }
}