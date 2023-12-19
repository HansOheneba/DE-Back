<?php
class Sessions
{
    public function __construct()
    {
        $session_status = session_status();
        if ($session_status === PHP_SESSION_ACTIVE || session_id()) {
            session_regenerate_id();
        } else {
            session_start();
        }
    }

    public static function start()
    {
        return new Sessions();
    }

    public static function add($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function set($key, $value)
    {
        self::change_session($_SESSION[$key], $value);
    }

    private static function change_session(&$session, $value)
    {
        $session = $value;
    }

    public static function remove($key)
    {
        if (self::exists($key)) {
            $_SESSION[$key] = NULL;
            unset($_SESSION[$key]);
        }
    }


    public static function all()
    {
        return $_SESSION;
    }

    public static function get($key)
    {
        return $_SESSION[$key];
    }

    public static function exists($key): bool
    {
        $flag = false;
        try {
            if (isset($_SESSION[$key]))
                $flag = true;
        } catch (\Error $err) {
            $flag = false;
        }
        return $flag;
    }

    public static function removeAll()
    {
        session_destroy();
    }
}


$Session = Sessions::start();


// Create a new session
$session = Sessions::start();
$session::set('token', 'login token');


// Access a session

$authorizatonToken = 'token from the front end';

$authSession = Sessions::start();

if($authSession::exists('token') && $authSession::get('token') === $authSession) {

    // validate authorization token with jwt

    // get the username from the jwt library

    // check if the username of the jwt token exists in the database.

    // if their record the token is valid
    // the token is invalid

} else {

    // the token is invalid
}