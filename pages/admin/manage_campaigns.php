<?php
require_once '../../config/database.php';
require_once '../../includes/functions.php';
if (!is_logged_in() || $_SESSION['user']['user_type'] !== 'admin') redirect('/donasiapp/');
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $stmt = $conn->prepare("CALL SP_DeleteCampaign(?)");
    $stmt->bind_param("i", $_GET['id']);
    if ($stmt->execute()) redirect('manage_campaigns.php?status=deleted'); else echo "Error menghapus.";
}
$campaigns = $conn->query("SELECT campaign_id, title, status, end_date FROM campaigns ORDER BY created_at DESC");
include '../../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold">Kelola Kampanye</h2>
    <div>
        <a href="manage_categories.php" class="btn btn-info"><i class="fas fa-tags me-1"></i> Kelola Kategori</a>
        <a href="add_campaign.php" class="btn btn-success"><i class="fas fa-plus me-1"></i> Tambah Kampanye</a>
    </div>
</div>
<?php if (isset($_GET['status']) && $_GET['status'] == 'deleted') echo "<div class='alert alert-success'>Kampanye berhasil dihapus.</div>"; ?>
<div class="card shadow-sm"><div class="card-body">
<div class="table-responsive">
<table class="table table-bordered table-hover">
    <thead class="table-dark"><tr><th>Judul</th><th>Status</th><th>Berakhir</th><th class="text-center">Aksi</th></tr></thead>
    <tbody>
        <?php while($c = $campaigns->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($c['title']); ?></td>
            <td><span class="badge bg-primary"><?php echo ucfirst($c['status']); ?></span></td>
            <td><?php echo date('d F Y', strtotime($c['end_date'])); ?></td>
            <td class="text-center">
                <a href="edit_campaign.php?id=<?php echo $c['campaign_id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                <a href="view_donors.php?id=<?php echo $c['campaign_id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-users"></i> Donatur</a>
                <a href="manage_campaigns.php?action=delete&id=<?php echo $c['campaign_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin?');"><i class="fas fa-trash"></i> Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</div></div></div>
<?php include '../../includes/footer.php'; ?>