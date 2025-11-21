<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Handle Registration
    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password'])) {
        
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $passwordPlain = $_POST['password'];
        $username = trim($_POST['username']);
        $gender = $_POST['gender'];
        $role = $_POST['role'];

        // Password strength enforcement
        if (strlen($passwordPlain) < 6 
            || !preg_match('/[A-Z]/', $passwordPlain) 
            || !preg_match('/\d/', $passwordPlain) 
            || !preg_match('/[^A-Za-z0-9]/', $passwordPlain)) 
        {
            $_SESSION['register_error'] = "Weak password. Must include uppercase letter, number & special character.";
            header("Location: signup.php");
            exit();
        }

        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM datingAppUsers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['register_error'] = "This email is already registered.";
            header("Location: login.php");
            exit();
        }

        // Hash password and insert user
        $passwordHash = password_hash($passwordPlain, PASSWORD_DEFAULT);
        
        $insert = $conn->prepare("INSERT INTO datingAppUsers (name, email, password, username, gender, role) VALUES (?, ?, ?, ?, ?, ?)");
        $insert->bind_param("ssssss", $name, $email, $passwordHash, $username, $gender, $role);
        
        if ($insert->execute()) {
            $_SESSION['register_success'] = "Account created successfully! Please login.";
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['register_error'] = "Registration failed. Please try again.";
            header("Location: signup.php");
            exit();
        }
    }
    
    // Handle Login
    if (isset($_POST['email']) && isset($_POST['password']) && !isset($_POST['name'])) {
        
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM datingAppUsers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            header("Location: home.php");
            exit();
        } else {
            $_SESSION['login_error'] = "Invalid email or password.";
            header("Location: login.php");
            exit();
        }
    }
}

header("Location: login.php");
exit();
?>