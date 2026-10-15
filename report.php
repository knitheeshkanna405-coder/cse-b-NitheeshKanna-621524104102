<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION["user_id"];
$month = (int)($_GET["month"] ?? date("n"));
$year = (int)($_GET["year"] ?? date("Y"));

// Month name for heading
$monthName = date("F", mktime(0, 0, 0, $month, 10));

// Mock values for report
$income = 5000.00;
$expense = 1200.00;
$category_breakdown = [
    ["category" => "College Bus", "total" => 500.00],
    ["category" => "Food", "total" => 400.00],
    ["category" => "Books", "total" => 300.00]
];
?>
<!DOCTYPE html>
<html>
<head>
<title>Monthly Report</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="topbar">
<h2>Monthly Report</h2>
<a class="logout" href="dashboard.php">Dashboard</a>
</header>
<main class="container">
<section class="panel">
<h1><?= $monthName ?> <?= $year ?></h1>
<form class="filter-form" method="get">
<select name="month">
<?php for($m=1;$m<=12;$m++): ?>
<option value="<?= $m ?>"
<?= $m===$month?"selected":"" ?>>
<?= date("F",mktime(0,0,0,$m,1)) ?>
</option>
<?php endfor; ?>
</select>
<input type="number" name="year"
value="<?= $year ?>" min="2020" max="2100">
<button type="submit">View Report</button>
</form>
</section>
<div class="cards">
<div class="card income">
<h3>Total Income</h3>
<p>■<?= number_format($income,2) ?></p>
</div>
<div class="card expense">
<h3>Total Expense</h3>
<p>■<?= number_format($expense,2) ?></p>
</div>
<div class="card balance">
<h3>Balance</h3>
<p>■<?= number_format($income-$expense,2) ?></p>
</div>
</div>
<section class="panel">
<h2>Category-wise Expense</h2>
<table>
  <tr><th>Category</th><th>Total</th></tr>
  <?php foreach($category_breakdown as $row): ?>
  <tr>
    <td><?= htmlspecialchars($row["category"]) ?></td>
    <td>₹<?= number_format($row["total"], 2) ?></td>
  </tr>
  <?php endforeach; ?>
</table>
</section>
</main>
</body>
</html>