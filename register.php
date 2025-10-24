<?php
session_start();
$conn = new mysqli("localhost", "root", "", "gym_db");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$msg = "";
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $plan = $_POST['plan'];
    $reg_date = $_POST['reg_date'];

    // Calculate end_date based on plan
    if ($plan == "Monthly") {
        $end_date = date('Y-m-d', strtotime($reg_date . ' +1 month'));
    } elseif ($plan == "Quarterly") {
        $end_date = date('Y-m-d', strtotime($reg_date . ' +3 months'));
    } elseif ($plan == "Yearly") {
        $end_date = date('Y-m-d', strtotime($reg_date . ' +1 year'));
    } else {
        $end_date = $reg_date;
    }

    $sql = "INSERT INTO members (name, gender, age, plan, reg_date, end_date) 
            VALUES ('$name', '$gender', '$age', '$plan', '$reg_date', '$end_date')";
    if ($conn->query($sql)) {
        $msg = "✅ Member Registered Successfully!";
    } else {
        $msg = "❌ Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Register Member</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #2c5364, #0f2027);
      margin:0;
      padding:0;
      background-size:cover;
      background-image:url('GYM.jpg');
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .register-box {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0px 8px 20px rgba(0,0,0,0.2);
      width: 400px;
      text-align: center;
    }
    .register-box h2 {
      margin-bottom: 20px;
      color: #2c5364;
    }
    input, select {
      width: 95%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size:14px;
      box-sizing:border-box;
    }
    button {
      width: 95%;
      padding: 10px;
      margin-top: 10px;
      background: #2c5364;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    button:hover {
      background: #1e3c72;
    }
    .msg {
      margin: 10px 0;
      font-weight: bold;
      color: green;
    }
    .nav-links {
      margin-top: 15px;
    }
    .nav-links a {
      text-decoration: none;
      color: #2c5364;
      font-size: 14px;
      margin: 0 10px;
    }
    .nav-links a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="register-box">
    <h2>Register Member</h2>
    <?php if ($msg) echo "<p class='msg'>$msg</p>"; ?>
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
      
      <!-- Registration Date -->
      <input type="date" name="reg_date" required><br>

      <button type="submit" name="register">Register</button>
    </form>

    <div class="nav-links">
      <a href="members.php">View Members</a> | 
      <a href="logout.php">Logout</a>
    </div>
  </div>
</body>
</html>
