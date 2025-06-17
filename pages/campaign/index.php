<?php
require_once '../../config/database.php';
require_once '../../includes/functions.php';
$is_admin = (is_logged_in() && $_SESSION['user']['user_type'] === 'admin');
$result = $conn->query("SELECT * FROM V_CampaignSummary ORDER BY status ASC, created_at DESC");
include '../../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold">Semua Kampanye</h1>
    <?php if ($is_admin): ?>
        <a href="/donasiapp/pages/admin/manage_campaigns.php" class="btn btn-success"><i class="fas fa-cogs me-2"></i>Kelola Kampanye</a>
    <?php endif; ?>
</div>
<div class="row g-4">
    <?php while ($campaign = $result->fetch_assoc()): ?>
    <div class="col-md-4">
        <div class="campaign-card">
            <img src="/donasiapp/<?php echo htmlspecialchars($campaign['image_url']); ?>" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($campaign['title']); ?></h5>
                <div class="progress my-3"><div class="progress-bar" style="width: <?php echo $campaign['completion_percentage']; ?>%;"></div></div>
                <div class="d-flex justify-content-between align-items-center">
                    <div><small class="text-muted">Terkumpul</small><p class="fw-bold mb-0"><?php echo format_rupiah($campaign['collected_amount']); ?></p></div>
                    <a href="detail.php?id=<?php echo $campaign['campaign_id']; ?>" class="btn btn-outline-primary">Donasi</a>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>
<?php include '../../includes/footer.php'; ?>