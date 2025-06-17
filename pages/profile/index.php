<?php
require_once '../../config/database.php';
require_once '../../includes/functions.php';
if (!is_logged_in()) redirect('/donasiapp/pages/auth/login.php');

// Ambil data user terbaru dari session
$user = $_SESSION['user'];
?>
<?php include '../../includes/header.php'; ?>
<div class="container">
    <h2 class="fw-bold mb-4">Profil Saya</h2>
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <p><strong>Nama Lengkap:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Password:</strong> <?php echo htmlspecialchars($user['password']); ?></p>
            <p><strong>Tipe Akun:</strong> <span class="badge bg-info text-dark"><?php echo ucfirst($user['user_type']); ?></span></p>
            <hr>
            <a href="edit.php" class="btn btn-primary"><i class="fas fa-edit me-2"></i>Edit Profil & Password</a>
            <a href="../dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
