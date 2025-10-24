<?php
session_start();
$conn = new mysqli("localhost","root","","gym_db");

// Only admins can access
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != "admin"){
    header("Location: login.php");
    exit();
}

$msg = "";

// Handle new member registration
if(isset($_POST['register'])){
    $name = $conn->real_escape_string($_POST['name']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $age = intval($_POST['age']);
    $plan = $conn->real_escape_string($_POST['plan']);
    $reg_date = $_POST['reg_date'];

    if($plan=="Monthly") $end_date = date('Y-m-d', strtotime($reg_date.' +1 month'));
    elseif($plan=="Quarterly") $end_date = date('Y-m-d', strtotime($reg_date.' +3 months'));
    elseif($plan=="Yearly") $end_date = date('Y-m-d', strtotime($reg_date.' +1 year'));
    else $end_date = $reg_date;

    $sql = "INSERT INTO members (name, gender, age, plan, reg_date, end_date, created_by) 
            VALUES ('$name','$gender',$age,'$plan','$reg_date','$end_date','{$_SESSION['user_name']}')";
    if($conn->query($sql)){
        $msg = "✅ Member Registered Successfully!";
    } else {
        $msg = "❌ Error: ".$conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<style>
body { font-family: Arial; display:flex; justify-content:center; align-items:flex-start; min-height:100vh; margin:0; background:url('GYM.jpg'); background-size:cover; padding-top:20px; }
.box { background:white; padding:30px; border-radius:12px; width:450px; text-align:center; box-shadow:0 8px 20px rgba(0,0,0,0.2);}
input, select { width:95%; padding:10px; margin:8px 0; border:1px solid #ccc; border-radius:5px; font-size:14px; box-sizing:border-box;}
button { width:95%; padding:10px; margin-top:5px; background:#2c5364; color:white; border:none; border-radius:5px; cursor:pointer;}
button:hover { background:#1e3c72; }
.msg { margin:10px 0; font-weight:bold; color:green;}
.link { margin-top:15px; display:block; }
</style>
</head>
<body>
<div class="box">
<h2>Admin Dashboard</h2>
<h3>Register New Member</h3>
<?php if($msg) echo "<p class='msg'>$msg</p>"; ?>
<form method="POST">
<input type="text" name="name" placeholder="Member Name" required><br>
<select name="gender" required>
<option value="">-- Select Gender --</option>
<option value="Male">Male</option>
<option value="Female">Female</option>
<option value="Other">Other</option>
</select><br>
<input type="number" name="age" placeholder="Age" required><br>
<select name="plan" required>
<option value="">-- Select Plan --</option>
<option value="Monthly">Monthly</option>
<option value="Quarterly">Quarterly</option>
<option value="Yearly">Yearly</option>
</select><br>
<input type="date" name="reg_date" required><br>
<button type="submit" name="register">Register Member</button>
</form>

<a class="link" href="data.php">View All Members</a>
<a class="link" href="logout.php">Logout</a>
</div>
</body>
</html>
