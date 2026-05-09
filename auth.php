<?php
session_start();

function cekLogin() {

    // MODE DEV (sementara bypass login)
    $DEV_MODE = true;

    if ($DEV_MODE) {
        // Simulasi user login
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
            $_SESSION['nama'] = "Elliot";
        }
        return;
    }

    // MODE REAL (nanti dipakai)
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../../public/login.php");
        exit;
    }
}