<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Stock Management System</title>

    <style>
        /* Login Page Styles */
        body {
          font-family: Arial, sans-serif;
          background: lightgray;
          margin: 0;
        }

        .login-container {
          background: white;
          padding: 30px 40px;
          border-radius: 8px;
          box-shadow: 0 4px 10px rgba(0,0,0,0.1);
          width: 300px;
          text-align: center;
          margin: 80px auto; /* Move login box down */
        }

        .login-container h1 {
          margin-bottom: 10px;
          color: black;
          font-size: 22px;
        }

        .login-container h2 {
          margin-bottom: 20px;
          color: black;
          font-size: 18px;
        }

        .login-container input[type="email"],
        .login-container input[type="password"] {
          width: 100%;
          padding: 10px;
          margin: 8px 0;
          border-radius: 4px;
          border: 1px solid #ccc;
          box-sizing: border-box;
        }

        .login-container button {
          width: 100%;
          padding: 10px;
          background: gray;
          color: white;
          border: none;
          border-radius: 4px;
          font-size: 16px;
          cursor: pointer;
          margin-top: 10px;
        }

        .login-container button:hover {
          background: black;
        }

        .login-container .error {
          color: red;
          margin-bottom: 10px;
          font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1><b>Stock Management System📦</b></h1>
        <h2>Login</h2>

        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="Enter Email" required><br>
            <input type="password" name="password" placeholder="Enter Password" required><br>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>
