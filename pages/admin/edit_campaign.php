<?php
require_once '../../config/database.php';
require_once '../../includes/functions.php';
if (!is_logged_in() || $_SESSION['user']['user_type'] !== 'admin') redirect('/donasiapp/');
$campaign_id = (int)$_GET['id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $image_url = $_POST['existing_image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../../assets/uploads/";
        $image_name = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) { $image_url = 'assets/uploads/' . $image_name; }
    }
    $stmt = $conn->prepare("CALL SP_EditCampaign(?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdiss", $campaign_id, $_POST['title'], $_POST['description'], $_POST['target_amount'], $_POST['category_id'], $_POST['end_date'], $image_url);
    if ($stmt->execute()) { $success = "Kampanye berhasil diperbarui!"; } else { $error = "Gagal: " . $stmt->error; }
}
$campaign = $conn->query("SELECT * FROM campaigns WHERE campaign_id = $campaign_id")->fetch_assoc();
$categories = $conn->query("SELECT * FROM donation_categories");
include '../../includes/header.php';
?>
<h2>Edit Kampanye</h2>
<?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
<?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="existing_image" value="<?php echo $campaign['image_url']; ?>">
    <div class="mb-3"><label>Judul</label><input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($campaign['title']); ?>" required></div>
    <div class="mb-3"><label>Deskripsi</label><textarea name="description" class="form-control" rows="5" required><?php echo htmlspecialchars($campaign['description']); ?></textarea></div>
    <div class="mb-3"><label>Ganti Gambar</label><input type="file" name="image" class="form-control"></div>
    <div class="mb-3"><label>Target (Rp)</label><input type="number" name="target_amount" class="form-control" step="1" value="<?php echo $campaign['target_amount']; ?>" required></div>
    <div class="mb-3"><label>Kategori</label><select name="category_id" class="form-select" required><?php while($cat = $categories->fetch_assoc()): ?><option value="<?php echo $cat['category_id']; ?>" <?php if($cat['category_id'] == $campaign['category_id']) echo 'selected'; ?>><?php echo $cat['category_name']; ?></option><?php endwhile; ?></select></div>
    <div class="mb-3"><label>Tanggal Berakhir</label><input type="date" name="end_date" class="form-control" value="<?php echo $campaign['end_date']; ?>" required></div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="manage_campaigns.php" class="btn btn-secondary">Kembali</a>
</form>

<?php include '../../includes/footer.php'; ?>