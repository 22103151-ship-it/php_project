<?php
session_start();
include 'config.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password']; // plain text

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $db_password, $role);
        $stmt->fetch();

        // ❌ Change here: use plain password check (not password_verify)
        if ($password === $db_password) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_role'] = $role;

            if ($role == 'admin') {
                header("Location: admin/dashboard.php");
            } elseif ($role == 'staff') {
                header("Location: staff/dashboard.php");
            } elseif ($role == 'supplier') {
                header("Location: supplier/dashboard.php");
            }
            exit;
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "No user found with that email!";
    }
}
?>
