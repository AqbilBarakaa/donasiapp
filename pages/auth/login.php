<?php
require_once '../../config/database.php';
require_once '../../classes/User.php';
require_once '../../includes/functions.php';
if (is_logged_in()) redirect('/donasiapp/');
$user = new User($conn);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_user = $user->login($_POST['email'], $_POST['password']);
    if ($login_user) {
        $_SESSION['user'] = $login_user;
        redirect('/donasiapp/pages/dashboard.php');
    } else { $error = "Email atau password salah."; }
}
include '../../includes/header.php';
?>
<h2>Login</h2>
<?php if (isset($error)): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
<form method="POST">
    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
    <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
    <button type="submit" class="btn btn-success">Login</button>
</form>
<?php include '../../includes/footer.php'; ?>