<?php
    session_start();

    $token = $_GET["token"];
    if (!isset($token))
        die("No token specified");

    $email = $_GET["email"];
    if (!isset($email))
        die("No email specified");

    $_SESSION["sm_auth_token"] = $token;
    $_SESSION["sm_auth_email"] = $email;

    header("Location: ../");
    exit;
?>
