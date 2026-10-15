<?php
session_start();
require_once "config/db.php";
/** @var mysqli $conn */

// Redirect to login if user session is not found
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$error = "";
$success = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and assign form input values
    $type = strtolower(trim($_POST["type"] ?? "expense"));
    $category = trim($_POST["category"] ?? "");
    $amount = (float)($_POST["amount"] ?? 0);
    $description = trim($_POST["description"] ?? "");
    
    // Check both field names (date or transaction_date) to prevent null values
    $transaction_date = !empty($_POST["date"]) ? $_POST["date"] : (!empty($_POST["transaction_date"]) ? $_POST["transaction_date"] : date("Y-m-d"));

    // Validation check for empty category or invalid amount
    if (empty($category) || $amount <= 0) {
        $error = "Please enter valid transaction details.";
    } else {
        // Prepare SQL insert statement
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, category, amount, description, date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdss", $_SESSION["user_id"], $type, $category, $amount, $description, $transaction_date);

        // Execute query and redirect to dashboard
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Error adding transaction: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Transaction</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<main class="form-container">
<div class="panel">
<h1>Add Transaction</h1>
<?php if($error): ?>
<div class="alert error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<form method="post">
<label>Type</label>
<select name="type" required>
<option value="income">Income</option>
<option value="expense">Expense</option>
</select>
<label>Category</label>
<select name="category" required>
  <option value="College Bus" selected>College Bus</option>
  <option value="Food">Food</option>
  <option value="Books">Books</option>
  <option value="Fees">Fees</option>
  <option value="Stationery">Stationery</option>
  <option value="Others">Others</option>
</select>
<label>Amount</label>
<input type="number" name="amount"
step="0.01" min="0.01" required>
<label>Description</label>
<input type="text" name="description"
maxlength="255" placeholder="Example: College bus">
<label>Date</label>
<input type="date" name="transaction_date"
value="<?= date("Y-m-d") ?>" required>
<button type="submit">Save Transaction</button>
<a class="button light" href="dashboard.php">Cancel</a>
</form>
</div>
</main>
</body>
</html>