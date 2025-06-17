<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
if (!is_logged_in()) redirect('/donasiapp/pages/auth/login.php');
$stmt = $conn->prepare("SELECT * FROM V_DonationHistory WHERE donor_id = ? ORDER BY donation_date DESC");
$stmt->bind_param("i", $_SESSION['user']['user_id']);
$stmt->execute();
$donations = $stmt->get_result();
include '../includes/header.php';
?>
<h2>Riwayat Donasi</h2>
<div class="container">
    <table class="table table-striped table-hover">
        <thead><tr><th>Tanggal</th><th>Kampanye</th><th>Jumlah</th><th>Status</th></tr></thead>
        <tbody>
            <?php if ($donations->num_rows > 0): ?>
                <?php while ($row = $donations->fetch_assoc()): ?>
                <tr>
                    <td><?php echo date('d M Y, H:i', strtotime($row['donation_date'])); ?></td>
                    <td><a href="/donasiapp/pages/campaign/detail.php?id=<?php echo $row['campaign_id']; ?>"><?php echo htmlspecialchars($row['campaign_title']); ?></a></td>
                    <td><?php echo format_rupiah($row['amount']); ?></td>
                    <td><span class="badge bg-success"><?php echo ucfirst($row['payment_status']); ?></span></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" class="text-center">Anda belum pernah berdonasi.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-secondary mt-4"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
</div>
<?php include '../includes/footer.php'; ?>