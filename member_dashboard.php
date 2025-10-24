<?php
session_start();
$conn = new mysqli("localhost","root","","gym_db");

// protect: only member role allowed
if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'member'){
    header("Location: login.php");
    exit();
}

$msg = "";
if(isset($_POST['register'])){
    $name = $conn->real_escape_string($_POST['name']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $age = intval($_POST['age']);
    $plan = $conn->real_escape_string($_POST['plan']);
    $reg_date = $conn->real_escape_string($_POST['reg_date']);

    if($plan === "Monthly") $end_date = date('Y-m-d', strtotime($reg_date.' +1 month'));
    elseif($plan === "Quarterly") $end_date = date('Y-m-d', strtotime($reg_date.' +3 months'));
    elseif($plan === "Yearly") $end_date = date('Y-m-d', strtotime($reg_date.' +1 year'));
    else $end_date = $reg_date;

    $created_by = $conn->real_escape_string($_SESSION['user_name']);
    $sql = "INSERT INTO members (name, gender, age, plan, reg_date, end_date, created_by)
            VALUES ('$name','$gender',$age,'$plan','$reg_date','$end_date','$created_by')";
    if($conn->query($sql)){
        $msg = "✅ Member Registered Successfully!";
    } else {
        $msg = "❌ Error: ".$conn->error;
    }
}

// fetch members added by this user
$user_name_esc = $conn->real_escape_string($_SESSION['user_name']);
$members = $conn->query("SELECT * FROM members WHERE created_by='$user_name_esc' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html><head><title>Member Dashboard</title>
<style>
/* (same CSS as before) */
body{font-family:Arial;background:url('GYM.jpg');background-size:cover;display:flex;justify-content:center;align-items:flex-start;min-height:100vh;margin:0;padding-top:30px}
.dashboard-box{background:white;padding:30px;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,0.2);width:450px;text-align:center}
h2{color:#2c5364;margin-bottom:15px}
input, select {
  width: 90%;
  padding: 10px;
  margin: 8px 0;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
  box-sizing: border-box;
}
button {
  width: 50%;       /* smaller than inputs */
  padding: 10px;
  margin-top: 10px;
  background: #2c5364; /* keep original color */
  color: white;
  border: none;
  border-radius: 6px;
  font-weight: bold;
  font-size: 15px;
  cursor: pointer;
}
button:hover {
  background: #1e3c72; /* original hover color */
}

.msg{margin:10px 0;font-weight:bold;color:green}
table{width:100%;border-collapse:collapse;margin-top:20px;font-size:14px}
th,td{border:1px solid #ddd;padding:8px}
th{background:#2c5364;color:white}
.logout{display:inline-block;margin-top:10px;color:#2c5364;text-decoration:none;font-weight:bold}
</style>
</head>
<body>
<div class="dashboard-box">
<h2>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?> 👋</h2>
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

<h3 style="margin-top:20px">Your Registered Members</h3>
<table>
<tr><th>Name</th><th>Plan</th><th>Reg Date</th><th>End Date</th></tr>
<?php
if($members && $members->num_rows>0){
    while($r = $members->fetch_assoc()){
        echo "<tr><td>".htmlspecialchars($r['name'])."</td>
              <td>".htmlspecialchars($r['plan'])."</td>
              <td>".htmlspecialchars($r['reg_date'])."</td>
              <td>".htmlspecialchars($r['end_date'])."</td></tr>";
    }
} else {
    echo "<tr><td colspan='4'>No members registered yet.</td></tr>";
}
?>
</table>

<a href="logout.php" class="logout">Logout</a>
</div>
</body></html>
