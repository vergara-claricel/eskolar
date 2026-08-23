<?php
require_once __DIR__ . "/supabase.php";
$config = require __DIR__ . "esko/../api/supabase.php";

$api = new Supabase($config);

class User
{
    private $api;

    function __construct($api)
    {
        $this->api = $api;
    }

     public function login($username, $password)
    {
        $username = trim($username);
        $password = trim($password);
        $query = "?username=eq." . urlencode($username)
               . "&select=id,role,password";
        $users = $this->api->get("users", $query);
        $user = $users[0] ?? null;

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

}
