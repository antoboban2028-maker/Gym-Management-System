<?php
session_start();
$conn = new mysqli("localhost", "root", "", "gym_db");

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username='$username' AND password='$password'");
    if ($result->num_rows > 0) {
        $_SESSION['user'] = $username;
        header("Location: register.php");
    } else {
        $error = "Invalid Login! Try again.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Gym Management - Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #1e3c72, #2a5298);
      background-image:url('GYM.jpg');
      margin:0;
      padding:0;
      background-size:cover;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .login-box {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0px 8px 20px rgba(0,0,0,0.2);
      text-align: center;
      width: 320px;
    }
    .login-box h2 {
      margin-bottom: 20px;
      color: #1e3c72;
    }
    .login-box input {
      width: 90%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
    }
    .login-box button {
      width: 95%;
      padding: 10px;
      background: #1e3c72;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
    }
    .login-box button:hover {
      background: #16335b;
    }
    .error {
      color: red;
      margin-bottom: 15px;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Gym Login</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required><br>
      <input type="password" name="password" placeholder="Password" required><br>
      <button type="submit" name="login">Login