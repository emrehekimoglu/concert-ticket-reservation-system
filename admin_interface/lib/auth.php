<?php
session_start();

function checkLogin()
{
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: login.php");
        exit;
    }
}

function login($username, $password, $config)
{
    if ($username === $config['admin_user'] && $password === $config['admin_pass']) {
        $_SESSION['logged_in'] = true;
        return true;
    }
    return false;
}

function logout()
{
    session_destroy();
    header("Location: login.php");
    exit;
}
