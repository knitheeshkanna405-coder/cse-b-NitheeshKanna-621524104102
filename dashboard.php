<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION["user_id"];
$username = $_SESSION["username"] ?? "student";

$categoryList = ["College Bus", "Food", "Books", "Fees", "Allowance", "Others"];
$search = trim($_GET["search"] ?? "");
$category = trim($_GET["category"] ?? "");

require_once"config/db.php";

$userId = $_SESSION["user_id"];
$username = $_SESSION["username"] ?? "student";

$search = trim($_GET["search"] ?? "");
$category = trim($_GET["category"] ?? "");

$sql = "SELECT * FROM transactions WHERE user_id = ?";
$params = [$userId];
$types = "i";

if (!empty($search)) {
    $sql .= " AND (description LIKE ? OR category LIKE ?)";
    $searchWildcard = "%" . $search . "%";
    $params[] = $searchWildcard;
    $params[] = $searchWildcard;
    $types .= "ss";
}

if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

$sql .= " ORDER BY date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$transactions = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$income = 0.00;
$expense = 0.00;

foreach ($transactions as $t) {
    if ($t["type"] === "income") {
        $income += (float)$t["amount"];
    } else {
        $expense += (float)$t["amount"];
    }
}

$balance = $income - $expense;
$category_summary = [];
$catStmt = $conn->prepare("SELECT category, SUM(amount) as total FROM transactions WHERE user_id = ? AND type = 'expense' GROUP BY category");
$catStmt->bind_param("i", $userId);
$catStmt->execute();
$catResult = $catStmt->get_result();
while ($cRow = $catResult->fetch_assoc()) {
    $category_summary[] = $cRow;
}
$catStmt->close();




?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/style.css">
<title>Dashboard</title>
<link rel="stylesheet" href="css/style.css">
<script src="js/dashboard.js" defer></script>
</head>
<body>
<header class="topbar">
<div>
<h2>Student Expense Tracker</h2>
<span>Welcome, <?= htmlspecialchars($username) ?></span>
</div>
<a class="logout" href="login.php?logout=1">Logout</a>
</header>
<main class="container">
<div class="cards">
<div class="card income">
<h3>Income</h3>
<p>■<?= number_format($income,2) ?></p>
</div>
<div class="card expense">
<h3>Expenses</h3>
<p>■<?= number_format($expense,2) ?></p>
</div>
<div class="card balance">
<h3>Balance</h3>
<p>■<?= number_format($balance,2) ?></p>
</div>
</div>
<div class="dashboard-actions">
            <a href="add_transaction.php" class="btn-add">+ Add Transaction</a>
            <a href="report.php" class="btn-report">Monthly Report</a>
        </div>

<section class="panel">
<h2>Search & Filter</h2>
<form class="filter-form" method="get">
<input type="text" name="search"
 placeholder="Search..."
 value="<?= htmlspecialchars($search) ?>">
<select name="category">
<option value="">All Categories</option>
<?php foreach($categoryList as $cat): ?>
<option value="<?= htmlspecialchars($cat) ?>"
<?= $category===$cat ? "selected" : "" ?>>
<?= htmlspecialchars($cat) ?>
</option>
<?php endforeach; ?>
</select>
<button type="submit">Search</button>
<a class="button light" href="dashboard.php">Clear</a>
</form>
</section>
<section class="panel">
<h2>Category-wise Expenses</h2>
<div class="category-grid">
<?php foreach($category_summary as $row): ?>
<div class="category-box">
<strong><?= htmlspecialchars($row["category"]) ?></strong>
<span>₹<?= number_format($row["total"], 2) ?></span>
</div>
<?php endforeach; ?>
</div>
</section>
<section class="panel">
<h2>Transactions</h2>
<div class="table-wrap">
<table>
<tr>
<th>Date</th><th>Type</th><th>Category</th>
<th>Amount</th><th>Description</th><th>Action</th>
</tr>
<?php if(empty($transactions)): ?>
<tr><td colspan="6" class="center">No transactions found.</td></tr>
<?php endif; ?>
<?php foreach($transactions as $row): ?>
<tr>
<td><?= htmlspecialchars($row["date"]) ?></td>
<td><span class="tag <?= $row["type"] ?>">
<?= ucfirst($row["type"]) ?></span></td>
<td><?= htmlspecialchars($row["category"]) ?></td>
<td>■<?= number_format($row["amount"],2) ?></td>
<td><?= htmlspecialchars($row["description"]) ?></td>
<td>
<a href="edit_transaction.php?id=<?= $row["id"] ?>">Edit</a> |
<a class="danger-text"
href="delete_transaction.php?id=<?= $row["id"] ?>"
onclick="return confirmDelete()">Delete</a>
</td>
</tr>
<?php endforeach; ?>
</table>
</div>
</section>
</main>
</body>
</html>    