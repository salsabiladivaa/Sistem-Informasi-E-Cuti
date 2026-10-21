<?php
class CutiController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function jenis() {
        if (isset($_GET['delete_jenis'])) {
            $id = (int)$_GET['delete_jenis'];
            $stmt = $this->pdo->prepare("DELETE FROM jenis_cuti WHERE id = ?");
            $stmt->execute([$id]);
            header("Location: index.php?controller=cuti&action=jenis");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_jenis'])) {
            $id = (int)$_POST['id'];
            $durasi = (int)$_POST['durasi_default'];

            $stmt = $this->pdo->prepare("UPDATE jenis_cuti SET durasi_default = ? WHERE id = ?");
            $stmt->execute([$durasi, $id]);
            header("Location: index.php?controller=cuti&action=jenis");
            exit();
        }

        $stmt = $this->pdo->query("SELECT * FROM jenis_cuti");
        $jenis = $stmt->fetchAll();

        $title = "Manajemen Jenis Cuti";
        include 'views/header.php';
        ?>
        <div class="header-action">
            <h2>Manajemen Jenis Cuti</h2>
        </div>
        <div class="table-card" style="padding: 0;">
            <div class="table-header-title">Daftar Jenis Cuti</div>
            <table>
                <thead>
                    <tr>
                        <th>Nama Jenis Cuti</th>
                        <th>Durasi Default</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jenis as $row): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['nama_jenis']); ?></strong></td>
                        <td><span class="badge-pill"><?= $row['durasi_default']; ?> Hari</span></td>
                        <td><?= htmlspecialchars($row['keterangan']); ?></td>
                        <td>
                            <button class="btn-icon edit" title="Edit" onclick="editJenis(<?= $row['id']; ?>, '<?= htmlspecialchars($row['nama_jenis']); ?>', <?= $row['durasi_default']; ?>)">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                            </button>
                            <a href="index.php?controller=cuti&action=jenis&delete_jenis=<?= $row['id']; ?>" class="btn-icon delete" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus jenis cuti ini?')">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <form id="formEditJenis" method="POST" style="display:none;">
            <input type="hidden" name="update_jenis" value="1">
            <input type="hidden" name="id" id="edit_jenis_id">
            <input type="hidden" name="durasi_default" id="edit_durasi">
        </form>

        <script>
        function editJenis(id, nama, durasi) {
            let newDurasi = prompt("Ubah Durasi Default untuk " + nama + " (Hari):", durasi);
            if (newDurasi !== null) {
                document.getElementById('edit_jenis_id').value = id;
                document.getElementById('edit_durasi').value = newDurasi;
                document.getElementById('formEditJenis').submit();
            }
        }
        </script>
        <?php
        include 'views/footer.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $jenis_cuti_id = $_POST['jenis_cuti_id'];
            $alasan = $_POST['alasan'];
            $tgl_mulai = $_POST['tgl_mulai'];
            $tgl_selesai = $_POST['tgl_selesai'];
            $user_id = $_SESSION['user_id'];
            
            $d1 = new DateTime($tgl_mulai);
            $d2 = new DateTime($tgl_selesai);
            $durasi = $d2->diff($d1)->days + 1;

            // Validasi sisa cuti sebelum ajukan
            $stmtUser = $this->pdo->prepare("SELECT sisa_cuti FROM users WHERE id = ?");
            $stmtUser->execute([$user_id]);
            $sisa_cuti = $stmtUser->fetchColumn();

            if ($durasi > $sisa_cuti) {
                echo "<script>alert('Pengajuan gagal! Sisa cuti Anda tidak mencukupi (Sisa: {$sisa_cuti} hari, Pengajuan: {$durasi} hari).'); window.history.back();</script>";
                exit();
            }

            $bukti = NULL;
            if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0) {
                $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
                $bukti = time() . '_' . $user_id . '.' . $ext;
                move_uploaded_file($_FILES['bukti']['tmp_name'], 'uploads/' . $bukti);
            }

            $stmt = $this->pdo->prepare("INSERT INTO pengajuan_cuti (user_id, jenis_cuti_id, tgl_pengajuan, tgl_mulai, tgl_selesai, durasi, alasan, bukti_pendukung) VALUES (?, ?, CURDATE(), ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $jenis_cuti_id, $tgl_mulai, $tgl_selesai, $durasi, $alasan, $bukti]);

            header("Location: index.php?controller=cuti&action=history");
            exit();
        }

        $jenis_list = $this->pdo->query("SELECT * FROM jenis_cuti")->fetchAll();
        $title = "Ajukan Cuti";
        include 'views/header.php';
        ?>
        <h2>Ajukan Cuti</h2>
        <div class="form-card">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Jenis Cuti</label>
                    <select name="jenis_cuti_id" required>
                        <?php foreach ($jenis_list as $j): ?>
                            <option value="<?= $j['id']; ?>"><?= htmlspecialchars($j['nama_jenis']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="display:flex; gap:15px;">
                    <div class="form-group" style="flex:1;">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="tgl_mulai" required>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="tgl_selesai" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Alasan Cuti</label>
                    <textarea name="alasan" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label>Bukti Pendukung (Opsional - PDF/JPG)</label>
                    <input type="file" name="bukti" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <button type="submit" class="btn-primary" style="width: auto; padding: 10px 24px;">Kirim Pengajuan</button>
            </form>
        </div>
        <?php
        include 'views/footer.php';
    }

    public function history() {
        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['role'] ?? 'Pegawai';

        // Fitur Aksi Approval (Setuju / Tolak) + Menangkap Catatan
        if (isset($_GET['approve']) || isset($_GET['reject'])) {
            $status = isset($_GET['approve']) ? 'Disetujui' : 'Ditolak';
            $cuti_id = (int)($_GET['approve'] ?? $_GET['reject']);
            $catatan = isset($_GET['catatan']) && !empty(trim($_GET['catatan'])) ? trim($_GET['catatan']) : '-';

            $stmtCuti = $this->pdo->prepare("SELECT user_id, durasi, status FROM pengajuan_cuti WHERE id = ?");
            $stmtCuti->execute([$cuti_id]);
            $cutiData = $stmtCuti->fetch();

            if ($cutiData && $cutiData['status'] === 'Pending') {
                if ($status === 'Disetujui') {
                    // Validasi sisa cuti sebelum menyetujui
                    $stmtUser = $this->pdo->prepare("SELECT sisa_cuti FROM users WHERE id = ?");
                    $stmtUser->execute([$cutiData['user_id']]);
                    $sisa_cuti = $stmtUser->fetchColumn();

                    if ($cutiData['durasi'] > $sisa_cuti) {
                        echo "<script>alert('Gagal menyetujui! Sisa cuti pegawai tidak mencukupi.'); window.location.href='index.php?controller=cuti&action=history';</script>";
                        exit();
                    }

                    // Kurangi sisa cuti jika mencukupi
                    $stmtUser = $this->pdo->prepare("UPDATE users SET sisa_cuti = sisa_cuti - ? WHERE id = ?");
                    $stmtUser->execute([$cutiData['durasi'], $cutiData['user_id']]);
                }

                $stmtUpdate = $this->pdo->prepare("UPDATE pengajuan_cuti SET status = ?, catatan = ? WHERE id = ?");
                $stmtUpdate->execute([$status, $catatan, $cuti_id]);
            }

            header("Location: index.php?controller=cuti&action=history");
            exit();
        }

        if (in_array($user_role, ['Admin', 'HR', 'Atasan'])) {
            $stmt = $this->pdo->prepare("SELECT p.*, j.nama_jenis, u.nama_lengkap FROM pengajuan_cuti p JOIN jenis_cuti j ON p.jenis_cuti_id = j.id JOIN users u ON p.user_id = u.id ORDER BY p.id DESC");
            $stmt->execute();
        } else {
            $stmt = $this->pdo->prepare("SELECT p.*, j.nama_jenis, u.nama_lengkap FROM pengajuan_cuti p JOIN jenis_cuti j ON p.jenis_cuti_id = j.id JOIN users u ON p.user_id = u.id WHERE p.user_id = ? ORDER BY p.id DESC");
            $stmt->execute([$user_id]);
        }
        
        $history = $stmt->fetchAll();
        $title = "Riwayat Pengajuan Cuti";
        include 'views/header.php';
        ?>
        <h2>Riwayat Pengajuan Cuti</h2>
        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>Tgl Pengajuan</th>
                        <th>Pegawai</th>
                        <th>Jenis Cuti</th>
                        <th>Tanggal</th>
                        <th>Durasi</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <?php if (in_array($user_role, ['Admin', 'HR', 'Atasan'])): ?>
                            <th>Aksi Approval</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $row): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($row['tgl_pengajuan'])); ?></td>
                        <td><strong><?= htmlspecialchars($row['nama_lengkap']); ?></strong></td>
                        <td><?= htmlspecialchars($row['nama_jenis']); ?></td>
                        <td><?= date('d/m/Y', strtotime($row['tgl_mulai'])); ?> - <?= date('d/m/Y', strtotime($row['tgl_selesai'])); ?></td>
                        <td><?= $row['durasi']; ?> Hari</td>
                        <td><?= htmlspecialchars($row['alasan']); ?></td>
                        <td>
                            <?php if ($row['status'] === 'Disetujui'): ?>
                                <span class="badge-role-atasan">Disetujui</span>
                            <?php elseif ($row['status'] === 'Ditolak'): ?>
                                <span class="badge-role-admin">Ditolak</span>
                            <?php else: ?>
                                <span class="badge-status-pending">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $row['catatan'] ? htmlspecialchars($row['catatan']) : '-'; ?></td>
                        <?php if (in_array($user_role, ['Admin', 'HR', 'Atasan'])): ?>
                            <td>
                                <?php if ($row['status'] === 'Pending'): ?>
                                    <a href="#" class="btn-icon edit" title="Setujui" style="background:#10b981;" onclick="
                                        let cat = prompt('Tambahkan catatan persetujuan (opsional):', 'Disetujui');
                                        if (cat !== null) { window.location.href = 'index.php?controller=cuti&action=history&approve=<?= $row['id']; ?>&catatan=' + encodeURIComponent(cat); }
                                        return false;
                                    ">✓</a>
                                    <a href="#" class="btn-icon delete" title="Tolak" onclick="
                                        let cat = prompt('Masukkan alasan penolakan:', 'Sisa cuti tidak mencukupi');
                                        if (cat !== null) { window.location.href = 'index.php?controller=cuti&action=history&reject=<?= $row['id']; ?>&catatan=' + encodeURIComponent(cat); }
                                        return false;
                                    ">✕</a>
                                <?php else: ?>
                                    <small style="color:#aaa;">Selesai</small>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
        include 'views/footer.php';
    }
}
?>