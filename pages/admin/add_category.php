<?php
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Proteksi halaman: hanya admin yang dapat mengakses
if (!is_logged_in() || $_SESSION['user']['user_type'] !== 'admin') {
    redirect('/donasiapp/');
}

$success = '';
$error = '';

// Logika untuk menangani penambahan kategori
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = trim($_POST['category_name']);
    
    if (!empty($category_name)) {
        // Panggil Stored Procedure SP_AddCategory
        $stmt = $conn->prepare("CALL SP_AddCategory(?, @p_category_id)");
        $stmt->bind_param("s", $category_name);
        
        if ($stmt->execute()) {
            $success = "Kategori '{$category_name}' berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan kategori: " . $stmt->error;
        }
    } else {
        $error = "Nama kategori tidak boleh kosong.";
    }
}

// Ambil daftar kategori yang sudah ada untuk ditampilkan
$categories = $conn->query("SELECT * FROM donation_categories ORDER BY category_name ASC");

include '../../includes/header.php';
?>

<div class="container my-4">
    <div class="row">
        <div class="col-md-6">
            <h2 class="fw-bold">Tambah Kategori Baru</h2>
            <hr>
            <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>

            <form method="POST" class="mb-4">
                <div class="mb-3">
                    <label for="category_name" class="form-label">Nama Kategori</label>
                    <input type="text" class="form-control" id="category_name" name="category_name" placeholder="Contoh: Kemanusiaan" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Tambah Kategori
                </button>
            </form>
        </div>
        <div class="col-md-6">
            <h4 class="fw-bold">Daftar Kategori Saat Ini</h4>
            <ul class="list-group">
                <?php while($cat = $categories->fetch_assoc()): ?>
                    <li class="list-group-item"><?php echo htmlspecialchars($cat['category_name']); ?></li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>
     <a href="manage_campaigns.php" class="btn btn-secondary mt-4">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Kelola Kampanye
    </a>
</div>

<?php include '../../includes/footer.php'; ?>
