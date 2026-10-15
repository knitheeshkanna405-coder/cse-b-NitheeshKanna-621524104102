<?php
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING & ~E_NOTICE);
session_start( );
require_once "config/db.php";
if(!isset($_SESSION["user_id"])){
 header("Location: login.php");
 exit;
}
$id=(int)($_GET["id"] ?? 0);
$userId=$_SESSION["user_id"];
$categories=[
 "Food","Travel","Books","Hostel",
 "Entertainment","Education","Other"
];
$stmt= $conn->prepare(
 "SELECT * FROM transactions WHERE id=? AND user_id=?"
);
$stmt->bind_param("ii",$id,$userId);
$stmt->execute();
$transaction=$stmt->get_result()->fetch_assoc();
if(!$transaction){
 die("Transaction not found.");
}
$error="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
 $type=$_POST["type"] ?? "";
 $category=$_POST["category"] ?? "";
 $amount=(float)($_POST["amount"] ?? 0);
 $description=trim($_POST["description"] ?? "");
 $date=$_POST["transaction_date"] ?? date("Y-m-d");
 if(!in_array($type,["income","expense"],true) ||
 !in_array($category,$categories,true) ||
 $amount<=0){
 $error="Please enter valid details.";
 }else{
 $update = $conn->prepare("UPDATE transactions SET type=?, category=?, amount=?, description=?, date=? WHERE id=? AND user_id=?");
        $update->bind_param(
            "ssdssii", $type, $category, $amount,
            $description, $date, $id, $userId
        );
 $update->execute();
 header("Location: dashboard.php");
 exit;
 }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Transaction</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<main class="form-container">
<div class="panel">
<h1>Edit Transaction</h1>
<?php if($error): ?>
<div class="alert error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<form method="post">
<label>Type</label>
<select name="type" required>
<option value="income"
<?= $transaction["type"]==="income"?"selected":"" ?>>
Income</option>
<option value="expense"
<?= $transaction["type"]==="expense"?"selected":"" ?>>
Expense</option>
</select>
<label>Category</label>
<select name="category" required>
<?php foreach($categories as $cat): ?>
<option <?= $transaction["category"]===$cat?"selected":"" ?>>
<?= htmlspecialchars($cat) ?>
</option>
<?php endforeach; ?>
</select>
<label>Amount</label>
<input type="number" name="amount"
step="0.01" min="0.01"
value="<?= htmlspecialchars($transaction["amount"]) ?>"
required>
<label>Description</label>
<input type="text" name="description" value="<?= htmlspecialchars($transaction['description'] ?? '') ?>" required>
<label>Date</label>
<input type="date" name="date" value="<?= htmlspecialchars($transaction['date'] ?? '') ?>" required>
<button type="submit">Update Transaction</button>
<a class="button light" href="dashboard.php">Cancel</a>
</form>
</div>
</main>
</body>
</html>