<?php
    session_start();

    $token = $_GET["token"];

    if (!isset($token))
        die("No token specified");

    $_SESSION["auth_token"] = $token;

    header("Location: ../");
    exit;
?>
