<?php
require_once '../../config/database.php';
require_once '../../includes/functions.php';
if (!is_logged_in()) redirect('/donasiapp/pages/auth/login.php');

$user_id = $_SESSION['user']['user_id'];
$message = ''; $message_type = '';

// Logika untuk menangani pembaruan profil atau password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Jika tombol 'update_profile' ditekan
    if (isset($_POST['update_profile'])) {
        $stmt = $conn->prepare("CALL SP_UpdateProfile(?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $_POST['full_name'], $_POST['username'], $_POST['email']);
        if ($stmt->execute()) {
            $_SESSION['user']['full_name'] = $_POST['full_name'];
            $_SESSION['user']['username'] = $_POST['username'];
            $_SESSION['user']['email'] = $_POST['email'];
            $message = 'Profil berhasil diperbarui.'; $message_type = 'success';
        } else { $message = 'Gagal, username/email mungkin sudah digunakan.'; $message_type = 'danger'; }
    }

    // Jika tombol 'update_password' ditekan
    if (isset($_POST['update_password'])) {
        $current_pass = $_POST['current_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        if ($current_pass !== $_SESSION['user']['password']) {
            $message = 'Password saat ini salah.'; $message_type = 'danger';
        } elseif ($new_pass !== $confirm_pass) {
            $message = 'Konfirmasi password baru tidak cocok.'; $message_type = 'danger';
        } elseif (empty($new_pass)) {
            $message = 'Password baru tidak boleh kosong.'; $message_type = 'danger';
        } else {
            $stmt = $conn->prepare("CALL SP_UpdatePassword(?, ?)");
            $stmt->bind_param("is", $user_id, $new_pass);
            if ($stmt->execute()) {
                $_SESSION['user']['password'] = $new_pass;
                $message = 'Password berhasil diubah.'; $message_type = 'success';
            } else { $message = 'Gagal mengubah password.'; $message_type = 'danger'; }
        }
    }
}

$user = $_SESSION['user']; // Ambil data terbaru
?>
<?php include '../../includes/header.php'; ?>
<div class="container">
    <?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><strong>Edit Detail Profil</strong></div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3"><label>Nama Lengkap</label><input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required></div>
                        <div class="mb-3"><label>Username</label><input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required></div>
                        <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required></div>
                        <button type="submit" name="update_profile" class="btn btn-primary">Simpan Profil</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><strong>Ubah Password</strong></div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3"><label>Password Saat Ini</label><input type="password" name="current_password" class="form-control" required></div>
                        <div class="mb-3"><label>Password Baru</label><input type="password" name="new_password" class="form-control" required></div>
                        <div class="mb-3"><label>Konfirmasi Password Baru</label><input type="password" name="confirm_password" class="form-control" required></div>
                        <button type="submit" name="update_password" class="btn btn-warning">Ubah Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <a href="index.php" class="btn btn-secondary mt-4"><i class="fas fa-arrow-left me-2"></i>Kembali ke Profil</a>
</div>
<?php include '../../includes/footer.php'; ?>
