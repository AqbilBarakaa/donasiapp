<?php
require_once '../includes/functions.php';
if (!is_logged_in()) redirect('/donasiapp/pages/auth/login.php');
$is_admin = (strtolower($_SESSION['user']['user_type']) === 'admin');
include '../includes/header.php';
?>
<div class="container mt-4">
    <h2 class="mb-3">Dashboard</h2>
    <?php if ($is_admin) : ?>
        <div class="alert alert-info">Anda login sebagai <strong>Admin</strong>.</div>
    <?php else: ?>
        <p class="lead">Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['user']['full_name']); ?></strong>!</p>
    <?php endif; ?>
    <div class="list-group">
      <a href="/donasiapp/pages/profile/index.php" class="list-group-item list-group-item-action">Profil Saya</a>
      <a href="/donasiapp/pages/donation_history.php" class="list-group-item list-group-item-action">Riwayat Donasi Saya</a>
      <?php if ($is_admin): ?>
      <a href="/donasiapp/pages/admin/manage_campaigns.php" class="list-group-item list-group-item-action list-group-item-primary"><strong>Kelola Kampanye</strong></a>
      <?php endif; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>