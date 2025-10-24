<?php
session_start();
$conn = new mysqli("localhost","root","", "gym_db");

$error = "";

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $user = $result->fetch_assoc();

        // Plain password check
        if($password === $user['password']){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // Redirect based on role
            if($user['role'] === "admin"){
                header("Location: admin_dashboard.php");  // Admin dashboard
                exit();
            } else {
                header("Location: member_dashboard.php");  // Member dashboard
                exit();
            }
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "Email not found!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Gym Login</title>
<style>
body { font-family: Arial; background:url('GYM.jpg'); background-size:cover; display:flex; justify-content:center; align-items:center; height:100vh; margin:0;}
.login-box { background:white; padding:30px; border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.2); text-align:center; width:320px;}
.login-box h2{margin-bottom:20px;color:#1e3c72;}
.login-box input{width:90%;padding:10px;margin:8px 0;border:1px solid #ccc;border-radius:8px;font-size:14px;}
.login-box button{width:95%;padding:10px;background:#1e3c72;color:white;border:none;border-radius:8px;font-size:16px;cursor:pointer;transition:0.3s;}
.login-box button:hover{background:#16335b;}
.error{color:red;margin-bottom:15px;font-size:14px;}
</style>
</head>
<body>
<div class="login-box">
<h2>Gym Login</h2>
<?php if($error) echo "<p class='error'>$error</p>"; ?>
<form method="POST">
<input type="email" name="email" placeholder="Email" required><br>
<input type="password" name="password" placeholder="Password" required><br>
<button type="submit" name="login">Login</button>
</form>
<p>New user? <a href="register.php">Register here</a></p>
</div>
</body>
</html>
