CREATE DATABASE IF NOT EXISTS db_ecuti;
USE db_ecuti;

-- Tabel Users / Pegawai
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nik VARCHAR(50) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    jabatan VARCHAR(100) NOT NULL,
    divisi VARCHAR(100) NOT NULL,
    role ENUM('Admin', 'HR', 'Atasan', 'Pegawai') NOT NULL DEFAULT 'Pegawai',
    sisa_cuti INT NOT NULL DEFAULT 12,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Jenis Cuti
CREATE TABLE IF NOT EXISTS jenis_cuti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_jenis VARCHAR(100) NOT NULL,
    durasi_default INT NOT NULL DEFAULT 1,
    keterangan TEXT
);

-- Tabel Pengajuan Cuti
CREATE TABLE IF NOT EXISTS pengajuan_cuti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    jenis_cuti_id INT NOT NULL,
    tgl_pengajuan DATE NOT NULL,
    tgl_mulai DATE NOT NULL,
    tgl_selesai DATE NOT NULL,
    durasi INT NOT NULL,
    alasan TEXT NOT NULL,
    bukti_pendukung VARCHAR(255) DEFAULT NULL,
    status ENUM('Pending', 'Disetujui', 'Ditolak') DEFAULT 'Pending',
    catatan TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (jenis_cuti_id) REFERENCES jenis_cuti(id) ON DELETE CASCADE
);

-- Data Default Pegawai Formal
INSERT INTO users (nik, username, password, nama_lengkap, jabatan, divisi, role, sisa_cuti) VALUES
('A001', 'admin', '$2y$10$eImiTXuWVxfM37uY4JANjO5E.5R2e2z5M9G5C3H1L9Z9Z9Z9Z9Z9Z', 'Ahmad Pratama, S.Kom.', 'IT System Administrator', 'Teknologi Informasi', 'Admin', 12),
('H001', 'rina', '$2y$10$eImiTXuWVxfM37uY4JANjO5E.5R2e2z5M9G5C3H1L9Z9Z9Z9Z9Z9Z', 'Rina Sulistiyowati, S.Psi.', 'Human Resources Manager', 'Sumber Daya Manusia', 'HR', 12),
('M001', 'manager', '$2y$10$eImiTXuWVxfM37uY4JANjO5E.5R2e2z5M9G5C3H1L9Z9Z9Z9Z9Z9Z', 'Bambang Wijaya, M.M.', 'General Manager', 'Manajemen Eksekutif', 'Atasan', 12),
('P001', 'budi', '$2y$10$eImiTXuWVxfM37uY4JANjO5E.5R2e2z5M9G5C3H1L9Z9Z9Z9Z9Z9Z', 'Budi Santoso, A.Md.', 'Software Engineer', 'Teknologi Informasi', 'Pegawai', 12),
('P002', 'supri', '$2y$10$eImiTXuWVxfM37uY4JANjO5E.5R2e2z5M9G5C3H1L9Z9Z9Z9Z9Z9Z', 'Supriyadi, S.E.', 'HR Specialist', 'Sumber Daya Manusia', 'Pegawai', 12);

-- Data Default Jenis Cuti
INSERT INTO jenis_cuti (nama_jenis, durasi_default, keterangan) VALUES
('Cuti Tahunan', 1, 'Cuti tahunan reguler pegawai'),
('Cuti Sakit', 1, 'Cuti dengan dilampirkan surat keterangan dokter'),
('Cuti Melahirkan', 90, 'Cuti khusus persalinan pegawai wanita'),
('Cuti Khusus', 1, 'Cuti karena keperluan mendesak/keluarga');

-- Data Default Pengajuan Cuti (Riwayat)
INSERT INTO pengajuan_cuti (user_id, jenis_cuti_id, tgl_pengajuan, tgl_mulai, tgl_selesai, durasi, alasan, status, catatan) VALUES
(1, 2, '2026-01-21', '2026-01-21', '2026-01-24', 4, 'Istirahat karena pemulihan sakit', 'Pending', '-'),
(1, 2, '2026-01-21', '2026-01-21', '2026-01-24', 4, 'Pemeriksaan kesehatan rutin', 'Pending', '-');