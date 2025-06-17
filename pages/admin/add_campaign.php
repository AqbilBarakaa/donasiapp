<?php
require_once '../../config/database.php';
require_once '../../includes/functions.php';
if (!is_logged_in() || $_SESSION['user']['user_type'] !== 'admin') redirect('/donasiapp/');
$categories = $conn->query("SELECT * FROM donation_categories");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $image_url = 'assets/img/placeholder.png';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../../assets/uploads/"; if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
        $image_name = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) { $image_url = 'assets/uploads/' . $image_name; } else { $error = "Gagal upload."; }
    }
    if (empty($error)) {
        $stmt = $conn->prepare("CALL SP_AddCampaign(?, ?, ?, ?, ?, ?, ?, @p_campaign_id)");
        $stmt->bind_param("ssdiiss", $_POST['title'], $_POST['description'], $_POST['target_amount'], $_SESSION['user']['user_id'], $_POST['category_id'], $_POST['end_date'], $image_url);
        if ($stmt->execute()) { $success = "Kampanye berhasil ditambahkan!"; } else { $error = "Gagal: " . $stmt->error; }
    }
}
include '../../includes/header.php';
?>
<h2>Tambah Kampanye</h2>
<?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
<?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
<form method="POST" enctype="multipart/form-data">
    <div class="mb-3"><label>Judul</label><input type="text" name="title" class="form-control" required></div>
    <div class="mb-3"><label>Deskripsi</label><textarea name="description" class="form-control" rows="5" required></textarea></div>
    <div class="mb-3"><label>Gambar</label><input type="file" name="image" class="form-control"></div>
    <div class="mb-3"><label>Target (Rp)</label><input type="number" name="target_amount" class="form-control" step="1" required></div>
    <div class="mb-3"><label>Kategori</label><select name="category_id" class="form-select" required><?php while($cat = $categories->fetch_assoc()): ?><option value="<?php echo $cat['category_id']; ?>"><?php echo $cat['category_name']; ?></option><?php endwhile; ?></select></div>
    <div class="mb-3"><label>Tanggal Berakhir</label><input type="date" name="end_date" class="form-control" required></div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="manage_campaigns.php" class="btn btn-secondary">Kembali</a>
</form>
<?php include '../../includes/footer.php'; ?>