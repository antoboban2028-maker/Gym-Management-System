<?php
session_start();
$conn = new mysqli("localhost","root","","gym_db");

$msg = "";

if(isset($_POST['register'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password']; // store as plain password
    $role = "member";

    $sql = "INSERT INTO users (name,email,password,role) VALUES ('$name','$email','$password','$role')";
    if($conn->query($sql)){
        $msg = "✅ Registration Successful! You can now <a href='login.php'>login</a>.";
    } else {
        $msg = "❌ Error: ".$conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Gym Registration</title>
<style>
body { font-family: Arial; background:url('GYM.jpg'); background-size:cover; display:flex; justify-content:center; align-items:center; height:100vh; margin:0;}
.register-box { background:white; padding:30px; border-radius:12px; box-shadow:0px 8px 20px rgba(0,0,0,0.2); width:400px;text-align:center;}
.register-box h2{margin-bottom:20px;color:#2c5364;}
input{width:95%;padding:10px;margin:8px 0;border:1px solid #ccc;border-radius:5px;font-size:14px;}
button{width:95%;padding:10px;margin-top:10px;background:#2c5364;color:white;border:none;border-radius:5px;cursor:pointer;}
button:hover{background:#1e3c72;}
.msg{margin:10px 0;font-weight:bold;color:green;}
</style>
</head>
<body>
<div class="register-box">
<h2>Gym Registration</h2>
<?php if($msg) echo "<p class='msg'>$msg</p>"; ?>
<form method="POST">
<input type="text" name="name" placeholder="Full Name" required><br>
<input type="email" name="email" placeholder="Email" required><br>
<input type="password" name="password" placeholder="Password" required><br>
<button type="submit" name="register">Register</button>
</form>
<p>Already have an account? <a href="login.php">Login here</a></p>
</div>
</body>
</html>
