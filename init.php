<?php
require 'config/DataBase.php';

spl_autoload_register(function($class) {
    $path = "classes/$class.php";
    if (file_exists($path)) {
        require_once $path;
    }
});

$db = new DataBase();
$auth = new AuthManager($db->getPdo());
$reg = new RegManager($db->getPdo());

?>