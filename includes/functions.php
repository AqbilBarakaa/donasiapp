<?php
if (session_status() === PHP_SESSION_NONE) session_start();
function redirect($url) { header("Location: " . $url); exit(); }
function is_logged_in() { return isset($_SESSION['user']); }
function format_rupiah($number) { return 'Rp ' . number_format($number, 0, ',', '.'); }
?>