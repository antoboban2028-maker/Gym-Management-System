<?php
session_start();
$conn = new mysqli("localhost", "root", "", "gym_db");

// Only logged in users can access
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Handle delete request (by ID)
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM members WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Reorder IDs after delete
    $conn->query("SET @count = 0");
    $conn->query("UPDATE members SET id = (@count := @count + 1)");
    $conn->query("ALTER TABLE members AUTO_INCREMENT = 1");

    header("Location: members.php");
    exit();
}

// Handle search and filter
$search = "";
$today = date('Y-m-d');

if (isset($_GET['filter']) && $_GET['filter'] === "inactive" && isset($_GET['search']) && $_GET['search'] !== "") {
    // Search + Inactive together
    $search = trim($_GET['search']);
    $like = "%$search%";
    $stmt = $conn->prepare("SELECT * FROM members WHERE end_date < ? AND name LIKE ?");
    $stmt->bind_param("ss", $today, $like);
    $stmt->execute();
    $result = $stmt->get_result();

} elseif (isset($_GET['filter']) && $_GET['filter'] === "inactive") {
    // Show only inactive members
    $stmt = $conn->prepare("SELECT * FROM members WHERE end_date < ?");
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();

} elseif (isset($_GET['search']) && $_GET['search'] !== "") {
    // Normal name search
    $search = trim($_GET['search']);
    $like = "%$search%";
    $stmt = $conn->prepare("SELECT * FROM members WHERE name LIKE ?");
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();

} else {
    // Default: show all members
    $result = $conn->query("SELECT * FROM members");
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Gym Management - Members List</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin:0;
      padding:0;
      background-size:cover;
      background-image:url('GYM.jpg');
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .members-box {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0px 8px 20px rgba(0,0,0,0.2);
      width: 750px;
      text-align: center;
    }
    .members-box h2 {
      margin-bottom: 20px;
      color: #1e3c72;
    }
    form {
      margin-bottom: 0;
    }
    input[type=text] {
      padding: 5px;
      width: 200px;
    }
    button {
      padding: 6px 12px;
      background: #1e3c72;
      color: white;
      border: none;
      cursor: pointer;
      border-radius: 5px;
    }
    button:hover {
      background: #16315d;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
      margin-bottom: 15px;
    }
    table th, table td {
      border: 1px solid #ccc;
      padding: 10px;
      font-size: 14px;
      text-align: center;
    }
    table th {
      background: #1e3c72;
      color: white;
    }
    table tr:nth-child(even) {
      background: #f2f2f2;
    }
    .delete-btn {
      padding: 5px 10px;
      background: red;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      text-decoration: none;
    }
    .delete-btn:hover {
      background: darkred;
    }
    .status-active {
      color: green;
      font-weight: bold;
    }
    .status-inactive {
      color: red;
      font-weight: bold;
    }
    .nav-links {
      margin-top: 10px;
    }
    .nav-links a {
      text-decoration: none;
      color: #1e3c72;
      font-size: 14px;
      margin: 0 10px;
    }
    .nav-links a:hover {
      text-decoration: underline;
    }
    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <div class="members-box">
    <h2>Registered Members</h2>

    <!-- Search + Inactive button container -->
    <div class="top-bar">
      <!-- Search form -->
      <form method="GET" action="">
        <input type="text" name="search" placeholder="Search by name" value="<?= htmlspecialchars($search) ?>">
        <?php if (isset($_GET['filter']) && $_GET['filter'] === "inactive") { ?>
            <input type="hidden" name="filter" value="inactive">
        <?php } ?>
        <button type="submit">Search</button>
      </form>

      <!-- Toggle inactive/all -->
      <?php if (isset($_GET['filter']) && $_GET['filter'] === "inactive") { ?>
        <form method="GET" action="">
          <button type="submit" style="background:green;">Show All Members</button>
        </form>
      <?php } else { ?>
        <form method="GET" action="">
          <input type="hidden" name="filter" value="inactive">
          <button type="submit" style="background:red;">Show Inactive Members</button>
        </form>
      <?php } ?>
    </div>

    <table>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Gender</th>
        <th>Age</th>
        <th>Plan</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
      <?php while($row = $result->fetch_assoc()) { 
          $status = ($today <= $row['end_date']) 
                    ? "<span class='status-active'>Active</span>" 
                    : "<span class='status-inactive'>Inactive</span>";
      ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['gender']) ?></td>
          <td><?= htmlspecialchars($row['age']) ?></td>
          <td><?= htmlspecialchars($row['plan']) ?></td>
          <td><?= $status ?></td>
          <td>
            <a class="delete-btn"
               href="members.php?delete_id=<?= $row['id'] ?>"
               onclick="return confirm('Are you sure you want to delete <?= htmlspecialchars($row['name']) ?>?');">
               Delete
            </a>
          </td>
        </tr>
      <?php } ?>
    </table>

    <div class="nav-links">
      <a href="register.php">Add Member</a> |
      <a href="logout.php">Logout</a>
    </div>
  </div>
</body>
</html>
