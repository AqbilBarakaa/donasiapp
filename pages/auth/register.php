<?php
require_once '../../config/database.php';
require_once '../../classes/User.php';
require_once '../../includes/functions.php';
if (is_logged_in()) redirect('/donasiapp/');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = new User($conn);
    if ($user->register($_POST['username'], $_POST['email'], $_POST['password'], $_POST['full_name'])) {
        redirect('/donasiapp/pages/auth/login.php');
    } else { $error = "Registrasi gagal, username atau email mungkin sudah ada."; }
}
include '../../includes/header.php';
?>
<h2>Daftar Akun</h2>
<?php if (isset($error)): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
<form method="POST">
    <div class="mb-3"><label>Nama Lengkap</label><input type="text" name="full_name" class="form-control" required></div>
    <div class="mb-3"><label>Username</label><input type="text" name="username" class="form-control" required></div>
    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
    <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
    <button type="submit" class="btn btn-info">Daftar</button>
</form>
<?php include '../../includes/footer.php'; ?>