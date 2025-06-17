<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$base_url = "/donasiapp";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DonasiApp | Platform Kebaikan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css">
</head>
</head>
<body class="d-flex flex-column min-vh-100">
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?php echo $base_url; ?>/index.php">DonasiApp</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="main-nav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>/index.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>/pages/campaign/index.php">Kampanye</a></li>
                <?php if (isset($_SESSION['user'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <?php echo htmlspecialchars($_SESSION['user']['full_name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/pages/dashboard.php">Dashboard</a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/pages/donation_history.php">Riwayat Donasi</a></li>
                            <?php if ($_SESSION['user']['user_type'] === 'admin'): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo $base_url; ?>/pages/admin/add_campaign.php">Tambah Kampanye</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/pages/auth/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>/pages/auth/login.php">Login</a></li>
                    <li class="nav-item"><a class="btn btn-primary ms-2" href="<?php echo $base_url; ?>/pages/auth/register.php">Daftar</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="container my-4 flex-grow-1">
