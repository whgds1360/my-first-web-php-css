<?php

class AuthManager{

    private PDO $pdo;
    private string $status = '';

    private string $us_login;
    private string $us_password;

    private string $error = '';
    private array $userData = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function getData(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $this->us_login = trim($_POST['us_login'] ?? '');
            $this->us_password = trim($_POST['us_password'] ?? '');
        }
    }

    private function getUserFromDb() : bool{
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE us_login = :us_login");
        $stmt->execute([':us_login' => $this->us_login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($this->us_password, $user['us_password'])){
            $this->userData = $user;
            return true;
        }
        
        return false;
    }

    private function intoSession() : bool{
        if (isset($_SESSION)){
            $_SESSION['us_login'] = $this->us_login;
            $_SESSION['user_id'] = $this->userData['id'] ?? null;
            return true;
        }
        else{
            $this->error = 'Ошибка записи в сессию';
            return false;
        }
    }

    public function login() : bool{
        $this->getData();
        
        if (empty($this->us_login) || empty($this->us_password)){
            $this->status = 'Заполните все поля';
            return false;
        }
        
        if (!$this->getUserFromDb()){
            $this->status = 'Неверный логин или пароль';
            return false;
        }
        
        if ($this->intoSession()){
            $this->status = 'Вы успешно авторизовались!';
            return true;
        }
        
        $this->status = 'Ошибка авторизации';
        return false;
    }

    public function logout() : bool{
        if (isset($_SESSION)){
            session_unset();
            session_destroy();
            $this->status = 'Вы вышли из системы';
            return true;
        }
        return false;
    }

    public function isLoggedIn() : bool{
        return isset($_SESSION['us_login']) && !empty($_SESSION['us_login']);
    }

    public function getStatus() : string{
        return $this->status;
    }

    public function getError() : string{
        return $this->error;
    }

    public function getCurrentUser() : ?array{
        return $this->userData;
    }

    public function clearStatus(){
        $this->status = '';
    }
}