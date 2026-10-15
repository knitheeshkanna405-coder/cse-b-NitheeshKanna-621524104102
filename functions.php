<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', ''); //URL: http://localhost:8080''
}
@session_start();
function e($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
function redirect($u){ header("Location: $u"); exit; }
function require_login(){ if(!isset($_SESSION['uid'])){ redirect('login.php'); } }
function render_header($title=''){ 
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>'.$title.'</title><link rel="stylesheet" href="'.BASE_URL.'/css/style.css"></head><body>'; 
}
function render_footer(){ echo '<script src="'.BASE_URL.'/js/app.js"></script></body></html>'; }
?>