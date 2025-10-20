<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'ElderCare'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/head.css" />
    <link rel="stylesheet" href="css/foot.css" />
    <?php if (isset($extraCss) && is_array($extraCss)): foreach ($extraCss as $cssHref): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($cssHref) ?>" />
    <?php endforeach; endif; ?>
</head>
<body>
<header class="site-header">
    <nav class="nav-inner">
        <div class="brand">ElderCare</div>
        <div class="nav-spacer"></div>
        <ul class="nav-links">
            <?php if(isset($_SESSION['role'])): ?>
                <?php if($_SESSION['role']==='doctor'): ?><li><a href="dashboard_doctor.php">Doctor</a></li><?php endif; ?>
                <?php if($_SESSION['role']==='elderly'): ?><li><a href="dashboard_elderly.php">My Records</a></li><?php endif; ?>
                <li><a href="logout.php" class="danger">Logout</a></li>
            <?php else: ?>
                <li><a href="index.php">Home</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<main class="main">
