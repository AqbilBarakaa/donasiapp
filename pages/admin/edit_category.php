<?php
require_once '../../config/database.php';
require_once '../../includes/functions.php';
if (!is_logged_in() || $_SESSION['user']['user_type'] !== 'admin') redirect('/donasiapp/');
if (!isset($_GET['id'])) redirect('manage_categories.php');
$category_id = (int)$_GET['id'];
$message = ''; $message_type = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $conn->prepare("CALL SP_EditCategory(?, ?)");
    $stmt->bind_param("is", $category_id, $_POST['category_name']);
    if ($stmt->execute()) {
        $message = 'Kategori berhasil diperbarui.'; $message_type = 'success';
    } else { $message = 'Gagal memperbarui kategori.'; $message_type = 'danger'; }
}
$category = $conn->query("SELECT * FROM donation_categories WHERE category_id = $category_id")->fetch_assoc();
if (!$category) redirect('manage_categories.php');
include '../../includes/header.php';
?>
<h2 class="fw-bold">Edit Kategori</h2><hr>
<?php if ($message): ?><div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div><?php endif; ?>
<form method="POST">
    <div class="mb-3">
        <label class="form-label">Nama Kategori</label>
        <input type="text" name="category_name" class="form-control" value="<?php echo htmlspecialchars($category['category_name']); ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Update Kategori</button>
    <a href="manage_categories.php" class="btn btn-secondary">Batal</a>
</form>
<?php include '../../includes/footer.php'; ?>