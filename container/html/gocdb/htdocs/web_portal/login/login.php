<?php
    session_start();

    $username = $_POST["username"];
    $password = $_POST["password"];

    if (!isset($username) || $username == "")
        die("You shall not pass without a name!");

    if (!isset($password) || $password == "")
        die("You shall not pass without a password!");

    $_SESSION["auth_username"] = $username;
    $_SESSION["auth_password"] = $password;

    $host = $_SERVER["HTTP_HOST"];
    $uri = rtrim(dirname($_SERVER["PHP_SELF"]), "/\\");
    die($host$uri);
    header("Location: https://$host$uri/gocdb/");
    exit;
?>
