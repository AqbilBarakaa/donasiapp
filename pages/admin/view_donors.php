<?php
require_once '../../config/database.php';
require_once '../../includes/functions.php';
if (!is_logged_in() || $_SESSION['user']['user_type'] !== 'admin') redirect('/donasiapp/');
$campaign_id = (int)$_GET['id'];
$campaign_title = $conn->query("SELECT title FROM campaigns WHERE campaign_id = $campaign_id")->fetch_assoc()['title'];
$donors = $conn->query("SELECT * FROM V_DonationHistory WHERE campaign_id = $campaign_id AND payment_status = 'completed' ORDER BY donation_date DESC");
include '../../includes/header.php';
?>
<h2>Donatur untuk "<?php echo htmlspecialchars($campaign_title); ?>"</h2>
<table class="table table-striped table-hover">
    <thead><tr><th>Nama</th><th>Jumlah</th><th>Pesan</th><th>Tanggal</th></tr></thead>
    <tbody>
        <?php if ($donors->num_rows > 0): ?>
            <?php while($d = $donors->fetch_assoc()): ?>
            <tr>
                <td><?php echo $d['is_anonymous'] ? '<i>Anonim</i>' : htmlspecialchars($d['donor_name']); ?></td>
                <td><?php echo format_rupiah($d['amount']); ?></td>
                <td><?php echo htmlspecialchars($d['message']); ?></td>
                <td><?php echo date('d M Y H:i', strtotime($d['donation_date'])); ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4" class="text-center">Saat ini belum ada data donasi yang masuk.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<a href="manage_campaigns.php" class="btn btn-secondary">Kembali</a>
<?php include '../../includes/footer.php'; ?>
