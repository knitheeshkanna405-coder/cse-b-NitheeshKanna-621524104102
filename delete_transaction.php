<?php
session_start();
require_once "config/db.php";
if(!isset($_SESSION["user_id"])){
 header("Location: login.php");
 exit;
}
$id=(int)($_GET["id"] ?? 0);
$userId=$_SESSION["user_id"];
$stmt=$conn->prepare(
 "DELETE FROM transactions
 WHERE id=? AND user_id=?"
);
$stmt->bind_param("ii",$id,$userId);
$stmt->execute();
header("Location: dashboard.php");
exit;
?>