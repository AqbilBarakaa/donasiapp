<?php
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$campaign_id = (int)$_GET['id'];
$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && is_logged_in()) {
    $amount = filter_var($_POST['amount'], FILTER_VALIDATE_INT);
    if (!$amount || $amount <= 0) {
        $error = 'Jumlah donasi tidak valid.';
    } else {
        $message = $_POST['message'] ?? '';
        $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
        
        $stmt = $conn->prepare("CALL SP_ProcessDonation(?, ?, ?, ?, ?, @donation_id)");
        $stmt->bind_param("iidsi", $campaign_id, $_SESSION['user']['user_id'], $amount, $message, $is_anonymous);
        if ($stmt->execute()) {
            $success = "Terima kasih! Donasi Anda telah berhasil kami terima.";
        } else {
            $error = "Gagal memproses donasi.";
        }
    }
}

$stmt = $conn->prepare("SELECT * FROM V_CampaignSummary WHERE campaign_id = ?");
$stmt->bind_param("i", $campaign_id);
$stmt->execute();
$campaign = $stmt->get_result()->fetch_assoc();
if (!$campaign) redirect('/donasiapp/pages/campaign/index.php');

$donors_result = $conn->query("SELECT * FROM V_DonationHistory WHERE campaign_id = $campaign_id AND payment_status = 'completed' ORDER BY donation_date DESC LIMIT 5");

include '../../includes/header.php';
?>

<div class="container">
    <div class="row g-5">
        <div class="col-lg-8">
            <article>
                <header class="mb-4">
                    <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($campaign['category_name']); ?></span>
                    <h1 class="fw-bold mb-2"><?php echo htmlspecialchars($campaign['title']); ?></h1>
                </header>
                <img src="/donasiapp/<?php echo htmlspecialchars($campaign['image_url']); ?>" class="detail-image shadow-sm mb-4" alt="Gambar Kampanye">
                <section>
                    <h4 class="fw-bold border-bottom pb-2 mb-3">Cerita Kampanye</h4>
                    <div class="fs-5 lh-lg"><?php echo nl2br(htmlspecialchars($campaign['description'])); ?></div>
                </section>
                <section class="mt-5">
                    <h4 class="fw-bold border-bottom pb-2 mb-3">Donatur Terbaru</h4>
                    <ul class="list-group list-group-flush">
                        <?php if ($donors_result && $donors_result->num_rows > 0): ?>
                            <?php while ($donor = $donors_result->fetch_assoc()): ?>
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong class="d-block"><?php echo $donor['is_anonymous'] ? 'Hamba Allah' : htmlspecialchars($donor['donor_name']); ?></strong>
                                        
                                        <!-- [BARU] Menampilkan pesan donatur jika ada -->
                                        <?php if (!empty($donor['message'])): ?>
                                            <em class="d-block text-muted fst-italic">"<?php echo htmlspecialchars($donor['message']); ?>"</em>
                                        <?php endif; ?>

                                        <small class="text-muted"><?php echo date('d M Y, H:i', strtotime($donor['donation_date'])); ?></small>
                                    </div>
                                    <span class="badge bg-success rounded-pill fs-6"><?php echo format_rupiah($donor['amount']); ?></span>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li class="list-group-item px-0">Belum ada donasi untuk kampanye ini. Jadilah yang pertama membawa harapan!</li>
                        <?php endif; ?>
                    </ul>
                </section>
            </article>
        </div>
        <div class="col-lg-4">
            <div class="donation-box">
                <div class="mb-3">
                    <span class="fs-4 fw-bold text-primary"><?php echo format_rupiah($campaign['collected_amount']); ?></span>
                    <p class="text-muted mb-1">Terkumpul dari target <?php echo format_rupiah($campaign['target_amount']); ?></p>
                </div>
                <div class="progress mb-3" style="height: 10px;">
                    <div class="progress-bar" style="width: <?php echo $campaign['completion_percentage']; ?>%;"></div>
                </div>
                <div class="d-flex justify-content-between small mb-4">
                    <span><strong><?php echo number_format($campaign['completion_percentage'], 2); ?>%</strong> tercapai</span>
                    <span><strong><?php echo max(0, $campaign['days_remaining']); ?></strong> hari lagi</span>
                </div>
                <hr>
                <h5 class="card-title mb-3 text-center">Beri Donasi</h5>
                <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>
                <?php if (is_logged_in()): ?>
                    <form method="POST">
                        <div class="mb-3"><label class="form-label">Jumlah (Rp)</label><input type="number" name="amount" class="form-control" step="1" required></div>
                        <div class="mb-3"><label class="form-label">Pesan (Opsional)</label><textarea name="message" class="form-control" rows="2"></textarea></div>
                        <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="is_anonymous" value="1"><label class="form-check-label">Donasi sebagai anonim</label></div>
                        <div class="d-grid"><button type="submit" class="btn btn-primary btn-lg">Donasi Sekarang</button></div>
                    </form>
                <?php else: ?>
                    <div class="text-center"><p>Anda harus login untuk berdonasi.</p><a href="/donasiapp/pages/auth/login.php" class="btn btn-primary w-100">Login</a></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <a href="index.php" class="btn btn-secondary mt-4"><i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Kampanye</a>
</div>

<?php include '../../includes/footer.php'; ?>
