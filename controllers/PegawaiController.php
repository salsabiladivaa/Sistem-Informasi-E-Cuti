<?php
class PegawaiController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function index() {
        // 1. Proses Tambah Pegawai Baru
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_pegawai'])) {
            $nik = trim($_POST['nik']);
            $username = trim($_POST['username']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $nama = trim($_POST['nama_lengkap']);
            $jabatan = trim($_POST['jabatan']);
            $divisi = trim($_POST['divisi']);
            $role = $_POST['role'];
            $sisa_cuti = (int)$_POST['sisa_cuti'];

            $stmt = $this->pdo->prepare("INSERT INTO users (nik, username, password, nama_lengkap, jabatan, divisi, role, sisa_cuti) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nik, $username, $password, $nama, $jabatan, $divisi, $role, $sisa_cuti]);

            header("Location: index.php?controller=pegawai&action=index");
            exit();
        }

        // 2. Proses Hapus Data Pegawai
        if (isset($_GET['delete'])) {
            $id = (int)$_GET['delete'];
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            header("Location: index.php?controller=pegawai&action=index");
            exit();
        }

        // 3. Proses Edit Data Pegawai
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_pegawai'])) {
            $id = (int)$_POST['id'];
            $nama = $_POST['nama_lengkap'];
            $role = $_POST['role'];
            $sisa_cuti = (int)$_POST['sisa_cuti'];

            $stmt = $this->pdo->prepare("UPDATE users SET nama_lengkap = ?, role = ?, sisa_cuti = ? WHERE id = ?");
            $stmt->execute([$nama, $role, $sisa_cuti, $id]);
            header("Location: index.php?controller=pegawai&action=index");
            exit();
        }

        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY id ASC");
        $pegawai = $stmt->fetchAll();

        $title = "Manajemen Pegawai";
        include 'views/header.php';
        ?>
        <div class="header-action">
            <h2>Manajemen Pegawai</h2>
            <button class="btn-primary" onclick="openModalTambah()">+ Tambah Pegawai</button>
        </div>
        <div class="table-card" style="padding: 0;">
            <div class="table-header-title">Daftar Pegawai</div>
            <table>
                <thead>
                    <tr>
                        <th>NIK</th>
                        <th>Nama Lengkap</th>
                        <th>Jabatan & Divisi</th>
                        <th>Role</th>
                        <th>Sisa Cuti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pegawai as $row): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['nik']); ?></strong></td>
                        <td>
                            <strong><?= htmlspecialchars($row['nama_lengkap']); ?></strong><br>
                            <small style="color: #a0aec0;">User ID: <?= $row['id']; ?></small>
                        </td>
                        <td>
                            <?= htmlspecialchars($row['jabatan']); ?><br>
                            <small style="color: #a0aec0;"><?= htmlspecialchars($row['divisi']); ?></small>
                        </td>
                        <td>
                            <span class="badge-role-<?= strtolower($row['role']); ?>">
                                <?= htmlspecialchars($row['role']); ?>
                            </span>
                        </td>
                        <td><span class="badge-pill"><?= $row['sisa_cuti']; ?> Hari</span></td>
                        <td>
                            <button class="btn-icon edit" title="Edit" onclick="editPegawai(<?= $row['id']; ?>, '<?= htmlspecialchars($row['nama_lengkap']); ?>', '<?= $row['role']; ?>', <?= $row['sisa_cuti']; ?>)">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                            </button>
                            <a href="index.php?controller=pegawai&action=index&delete=<?= $row['id']; ?>" class="btn-icon delete" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data pegawai ini?')">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- MODAL POPUP TAMBAH PEGAWAI -->
        <div id="modalTambah" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; justify-content:center; align-items:center;">
            <div style="background:#fff; padding:25px; border-radius:12px; width:450px; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
                <h3 style="margin-bottom:15px; font-size:16px;">Tambah Pegawai Baru</h3>
                <form method="POST">
                    <input type="hidden" name="tambah_pegawai" value="1">
                    <div style="display:flex; gap:10px;">
                        <div class="form-group" style="flex:1;">
                            <label>NIK</label>
                            <input type="text" name="nik" placeholder="P003" required>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label>Username</label>
                            <input type="text" name="username" placeholder="userbaru" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" placeholder="Nama Beserta Gelar" required>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <div class="form-group" style="flex:1;">
                            <label>Jabatan</label>
                            <input type="text" name="jabatan" required>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label>Divisi</label>
                            <input type="text" name="divisi" required>
                        </div>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <div class="form-group" style="flex:1;">
                            <label>Role</label>
                            <select name="role" required>
                                <option value="Pegawai">Pegawai</option>
                                <option value="Atasan">Atasan</option>
                                <option value="HR">HR</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label>Sisa Cuti</label>
                            <input type="number" name="sisa_cuti" value="12" required>
                        </div>
                    </div>
                    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:15px;">
                        <button type="button" onclick="closeModalTambah()" style="padding:8px 15px; border:1px solid #ccc; background:#fff; border-radius:6px; cursor:pointer;">Batal</button>
                        <button type="submit" class="btn-primary" style="width:auto; padding:8px 15px;">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- FORM EDIT HIDDEN -->
        <form id="formEditPegawai" method="POST" style="display:none;">
            <input type="hidden" name="update_pegawai" value="1">
            <input type="hidden" name="id" id="edit_id">
            <input type="hidden" name="nama_lengkap" id="edit_nama">
            <input type="hidden" name="role" id="edit_role">
            <input type="hidden" name="sisa_cuti" id="edit_sisa_cuti">
        </form>

        <script>
        function openModalTambah() {
            document.getElementById('modalTambah').style.display = 'flex';
        }
        function closeModalTambah() {
            document.getElementById('modalTambah').style.display = 'none';
        }
        function editPegawai(id, nama, role, sisaCuti) {
            let newSisa = prompt("Ubah Sisa Cuti untuk " + nama + ":", sisaCuti);
            if (newSisa !== null) {
                document.getElementById('edit_id').value = id;
                document.getElementById('edit_nama').value = nama;
                document.getElementById('edit_role').value = role;
                document.getElementById('edit_sisa_cuti').value = newSisa;
                document.getElementById('formEditPegawai').submit();
            }
        }
        </script>
        <?php
        include 'views/footer.php';
    }
}
?>