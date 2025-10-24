<?php
session_start();
$conn = new mysqli("localhost","root","","gym_db");

// Only admins can access
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != "admin"){
    header("Location: login.php");
    exit();
}

$today = date('Y-m-d');

// Handle delete request
// Handle delete request
if(isset($_GET['delete_id'])){
    $id = intval($_GET['delete_id']);
    
    // Delete member
    $stmt = $conn->prepare("DELETE FROM members WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();

    // Reorder IDs consecutively
    $conn->query("SET @count=0");
    $conn->query("UPDATE members SET id = (@count:=@count+1) ORDER BY id ASC");
    $conn->query("ALTER TABLE members AUTO_INCREMENT = 1");

    header("Location: data.php");
    exit();
}


// Handle search/filter
$search = "";
$filter_inactive = false;
if(isset($_GET['search']) && $_GET['search'] != ""){
    $search = $conn->real_escape_string($_GET['search']);
}
if(isset($_GET['filter']) && $_GET['filter'] === "inactive"){
    $filter_inactive = true;
}

// Build query
if($search && $filter_inactive){
    $stmt = $conn->prepare("SELECT * FROM members WHERE end_date < ? AND name LIKE ? ORDER BY id ASC");
    $like = "%$search%";
    $stmt->bind_param("ss",$today,$like);
} elseif($search){
    $stmt = $conn->prepare("SELECT * FROM members WHERE name LIKE ? ORDER BY id ASC");
    $like = "%$search%";
    $stmt->bind_param("s",$like);
} elseif($filter_inactive){
    $stmt = $conn->prepare("SELECT * FROM members WHERE end_date < ? ORDER BY id ASC");
    $stmt->bind_param("s",$today);
} else {
    $stmt = $conn->prepare("SELECT * FROM members ORDER BY id ASC");
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<title>Members Data</title>
<style>
body { font-family: Arial; display:flex; justify-content:center; align-items:flex-start; min-height:100vh; margin:0; background:url('GYM.jpg'); background-size:cover; padding-top:20px; }
.box { background:white; padding:30px; border-radius:12px; width:900px; text-align:center; box-shadow:0 8px 20px rgba(0,0,0,0.2);}
input[type=text] { padding:5px; width:200px;}
button { padding:6px 12px; background:#2c5364; color:white; border:none; border-radius:5px; cursor:pointer;}
button:hover { background:#1e3c72; }
table { width:100%; border-collapse:collapse; margin-top:15px; font-size:14px;}
th, td { border:1px solid #ccc; padding:8px; text-align:center;}
th { background:#2c5364; color:white;}
.status-active { color:green; font-weight:bold; }
.status-inactive { color:red; font-weight:bold; }
.delete-btn { padding:5px 10px; background:red; color:white; border:none; border-radius:5px; text-decoration:none; }
.delete-btn:hover { background:darkred; }
.top-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; }
.top-bar form { margin:0; display:flex; gap:5px; }
.link { margin-top:10px; display:inline-block; }
</style>
</head>
<body>
<div class="box">
<h2>All Registered Members</h2>

<!-- Search + Filter -->
<div class="top-bar">
<form method="GET">
<input type="text" name="search" placeholder="Search by name" value="<?= htmlspecialchars($search) ?>">
<?php if($filter_inactive){ ?><input type="hidden" name="filter" value="inactive"><?php } ?>
<button type="submit">Search</button>
</form>

<form method="GET">
<?php if($filter_inactive){ ?>
<button type="submit" style="background:green;">Show All Members</button>
<?php } else { ?>
<input type="hidden" name="filter" value="inactive">
<button type="submit" style="background:red;">Show Inactive Members</button>
<?php } ?>
</form>
</div>

<table>
<tr><th>ID</th><th>Name</th><th>Gender</th><th>Age</th><th>Plan</th><th>Status</th><th>Action</th></tr>
<?php while($row=$result->fetch_assoc()):
$status = ($today <= $row['end_date']) ? "<span class='status-active'>Active</span>" : "<span class='status-inactive'>Inactive</span>";
?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= htmlspecialchars($row['gender']) ?></td>
<td><?= htmlspecialchars($row['age']) ?></td>
<td><?= htmlspecialchars($row['plan']) ?></td>
<td><?= $status ?></td>
<td>
<a class="delete-btn" href="data.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Delete <?= htmlspecialchars($row['name']) ?>?')">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</table>

<a class="link" href="admin_dashboard.php">Register New Member</a>
<a class="link" href="logout.php">Logout</a>
</div>
</body>
</html>
