<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
$result = $conn->query("SELECT * FROM V_CampaignSummary WHERE status = 'active' ORDER BY created_at DESC LIMIT 3");
include 'includes/header.php';
?>
<div class="p-5 mb-4 bg-white rounded-3 shadow-sm text-center">
    <h1 class="display-5 fw-bold">Satu Kebaikan, Sejuta Harapan</h1>
    <p class="fs-4">Platform terpercaya untuk menyalurkan donasi Anda.</p>
    <a href="/donasiapp/pages/campaign/index.php" class="btn btn-primary btn-lg">Lihat Semua Kampanye</a>
</div>
<h2 class="text-center mb-4">Bantu Mereka Sekarang</h2>
<div class="row">
    <?php while ($row = $result->fetch_assoc()): ?>
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm">
            <img src="/donasiapp/<?php echo htmlspecialchars($row['image_url']); ?>" class="card-img-top">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                <p class="card-text text-muted small">Terkumpul <strong><?php echo format_rupiah($row['collected_amount']); ?></strong></p>
                <a href="/donasiapp/pages/campaign/detail.php?id=<?php echo $row['campaign_id']; ?>" class="btn btn-outline-primary mt-auto">Donasi</a>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>
<?php include 'includes/footer.php'; ?>