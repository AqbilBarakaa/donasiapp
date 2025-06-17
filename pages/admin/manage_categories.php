<?php
require_once '../../config/database.php';
require_once '../../includes/functions.php';
if (!is_logged_in() || $_SESSION['user']['user_type'] !== 'admin') redirect('/donasiapp/');
$message = ''; $message_type = '';
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $stmt = $conn->prepare("CALL SP_DeleteCategory(?, @p_message)");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $res = $conn->query("SELECT @p_message AS msg")->fetch_assoc();
    $message_type = (strpos($res['msg'], 'SUCCESS') !== false) ? 'success' : 'danger';
    $message = str_replace(['SUCCESS: ', 'ERROR: '], '', $res['msg']);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $stmt = $conn->prepare("CALL SP_AddCategory(?)");
    $stmt->bind_param("s", $_POST['category_name']);
    if ($stmt->execute()) { $message = 'Kategori baru berhasil ditambahkan.'; $message_type = 'success';
    } else { $message = 'Gagal menambahkan kategori.'; $message_type = 'danger'; }
}
$categories = $conn->query("SELECT * FROM donation_categories ORDER BY category_name ASC");
include '../../includes/header.php';
?>
<h2 class="fw-bold">Kelola Kategori Donasi</h2><hr>
<?php if ($message): ?><div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div><?php endif; ?>
<div class="row"><div class="col-md-5">
    <div class="card"><div class="card-header"><strong>Tambah Kategori</strong></div><div class="card-body">
    <form method="POST">
        <div class="mb-3"><label class="form-label">Nama Kategori</label><input type="text" name="category_name" class="form-control" required></div>
        <button type="submit" name="add_category" class="btn btn-primary">Tambah</button>
    </form>
    </div></div>
</div><div class="col-md-7">
    <table class="table table-bordered table-hover">
        <thead class="table-light"><tr><th>Nama Kategori</th><th class="text-center">Aksi</th></tr></thead>
        <tbody>
            <?php while($cat = $categories->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($cat['category_name']); ?></td>
                <td class="text-center">
                    <a href="edit_category.php?id=<?php echo $cat['category_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="manage_categories.php?action=delete&id=<?php echo $cat['category_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin?');">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div></div>
<a href="manage_campaigns.php" class="btn btn-secondary mt-4">Kembali</a>
<?php include '../../includes/footer.php'; ?>