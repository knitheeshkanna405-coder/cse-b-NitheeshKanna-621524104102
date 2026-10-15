<?php
$host = "db01.dbhost.dev";
$user = "user_455ndreze";
$pass = "p455ndreze";
$dbname = "db_455ndreze";
$port = 5051;


try {
    $conn = new mysqli($host, $user, $pass, $dbname, $port);

    if ($conn->connect_error) {
        die("Database Connection Error: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?><?php
$host = "db01.dbhost.dev";
$user = "user_455ndreze";
$pass = "p455ndreze";
$dbname = "db_455ndreze";
$port = 5051;


try {
    $conn = new mysqli($host, $user, $pass, $dbname, $port);

    if ($conn->connect_error) {
        die("Database Connection Error: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>
