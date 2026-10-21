<?php
class DashboardController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function index() {
        $user_id = $_SESSION['user_id'];

        $stmtPending = $this->pdo->prepare("SELECT COUNT(*) FROM pengajuan_cuti WHERE user_id = ? AND status = 'Pending'");
        $stmtPending->execute([$user_id]);
        $menunggu = $stmtPending->fetchColumn();

        $stmtApproved = $this->pdo->prepare("SELECT COUNT(*) FROM pengajuan_cuti WHERE user_id = ? AND status = 'Disetujui'");
        $stmtApproved->execute([$user_id]);
        $disetujui = $stmtApproved->fetchColumn();

        $stmtRejected = $this->pdo->prepare("SELECT COUNT(*) FROM pengajuan_cuti WHERE user_id = ? AND status = 'Ditolak'");
        $stmtRejected->execute([$user_id]);
        $ditolak = $stmtRejected->fetchColumn();

        $stmtUser = $this->pdo->prepare("SELECT sisa_cuti, nama_lengkap, role FROM users WHERE id = ?");
        $stmtUser->execute([$user_id]);
        $user = $stmtUser->fetch();

        $title = "Dashboard";
        include 'views/header.php';
        ?>
        <h2>Dashboard</h2>
        <div class="cards-grid">
            <div class="card card-purple">
                <p>Sisa Cuti</p>
                <h1><?= $user['sisa_cuti']; ?></h1>
                <span>Hari tersedia</span>
            </div>
            <div class="card card-orange">
                <p>Menunggu</p>
                <h1><?= $menunggu; ?></h1>
                <span>Pengajuan diproses</span>
            </div>
            <div class="card card-cyan">
                <p>Disetujui</p>
                <h1><?= $disetujui; ?></h1>
                <span>Riwayat disetujui</span>
            </div>
            <div class="card card-pink">
                <p>Ditolak</p>
                <h1><?= $ditolak; ?></h1>
                <span>Riwayat ditolak</span>
            </div>
        </div>

        <div class="dashboard-body">
            <div class="chart-container">
                <h3 style="font-size: 15px; color: #2d3748;">Statistik Pengajuan</h3>
                <div style="width: 210px; margin: 20px auto;">
                    <canvas id="cutiChart"></canvas>
                </div>
            </div>
            <div class="profile-card">
                <div class="avatar"><?= strtoupper(substr($user['nama_lengkap'], 0, 1)); ?></div>
                <h3 style="font-size: 16px; margin-top:5px;"><?= htmlspecialchars($user['nama_lengkap']); ?></h3>
                <p style="color: #888; font-size: 12px; margin-bottom: 20px;"><?= htmlspecialchars($user['role']); ?></p>
                <a href="index.php?controller=cuti&action=create" class="btn-primary" style="display:block; text-align:center; text-decoration:none; margin-bottom: 10px;">+ Ajukan Cuti Baru</a>
                <a href="index.php?controller=cuti&action=history" class="btn-secondary" style="display:block; text-align:center;">Lihat Riwayat</a>
            </div>
        </div>

        <script>
        const ctx = document.getElementById('cutiChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Disetujui', 'Menunggu', 'Ditolak'],
                datasets: [{
                    data: [<?= $disetujui; ?>, <?= $menunggu; ?>, <?= $ditolak; ?>],
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '75%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
                }
            }
        });
        </script>
        <?php
        include 'views/footer.php';
    }
}
?>