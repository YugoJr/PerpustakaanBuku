<?php
// controller/AuthController.php

// Hapus session_start() dari sini, pindahkan ke index.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/AkunModel.php';

class AuthController {
    private $db;
    private $akunModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->akunModel = new AkunModel($this->db);
    }

    public function login() {
    if($_POST) {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->akunModel->login($email, $password);
        if($user) {
            $_SESSION['user_id'] = $user['id_akun'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            
            // Set pesan selamat datang berdasarkan role
            if ($user['role'] == 'admin') {
                $_SESSION['success_message'] = "Selamat datang, Admin " . $user['username'] . "!";
                header("Location: index.php?controller=dashboard&action=admin");
                exit;
            } else if ($user['role'] == 'user') {
                $_SESSION['success_message'] = "Selamat datang, " . $user['username'] . "!";
                header("Location: index.php?controller=dashboard&action=user");
                exit;
            } else {
                // Default untuk guest
                $_SESSION['success_message'] = "Selamat datang, " . $user['username'] . "!";
                header("Location: index.php?controller=dashboard&action=user");
                exit;
            }
        } else {
            $error = "Email atau password salah!";
            include __DIR__ . '/../view/auth/login.php';
        }
    } else {
        include __DIR__ . '/../view/auth/login.php';
    }
}

    public function register() {
        if($_POST) {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if($this->akunModel->register($username, $email, $password)) {
                header("Location: index.php?controller=auth&action=login");
                exit;
            } else {
                $error = "Registrasi gagal!";
                include __DIR__ . '/../view/auth/register.php';
            }
        } else {
            include __DIR__ . '/../view/auth/register.php';
        }
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?controller=landing&action=index");
        exit;
    }
}
?>