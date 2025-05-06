<?php
session_start();
include 'db.php'; // Your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role = $_POST["role"]; // 'donor' or 'organization'

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $_SESSION['error'] = "Email already registered.";
        header("Location: register.php");
        exit;
    }
    $check->close();

    // Insert into users table
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;

        // If role is organization, also insert into organizations table
        if ($role === 'organization') {
            $org_name = $name;
            $description = "N/A"; // You can make this dynamic later
            $status = "pending";  // Default status

            $org_stmt = $conn->prepare("INSERT INTO organizations (user_id, org_name, description, status, created_at) VALUES (?, ?, ?, ?, NOW())");
            $org_stmt->bind_param("isss", $user_id, $org_name, $description, $status);
            $org_stmt->execute();
            $org_stmt->close();
        }

        $_SESSION['success'] = "Registration successful. Please log in.";
        header("Location: login.php");
    } else {
        $_SESSION['error'] = "Registration failed. Try again.";
        header("Location: register.php");
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: register.php");
    exit;
}
