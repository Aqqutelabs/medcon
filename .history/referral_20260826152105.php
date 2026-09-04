<?php
require_once __DIR__ . '/app/includes/db.php';
require_once __DIR__ . '/app/includes/functions.php';
$slug=strtolower(trim($_GET['name']??''));
$stmt=$pdo->prepare("SELECT id FROM agents WHERE referral_slug=? AND approval_status='approved' LIMIT 1"); $stmt->execute([$slug]);
if($stmt->fetchColumn()) $_SESSION['referral_code']=$slug;
header('Location: '.site_url('app/signup.php')); exit;
