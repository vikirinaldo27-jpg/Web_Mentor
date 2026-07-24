-- =============================================================
-- MIGRATE DATA FROM mentorconnect TO web_mentor
-- =============================================================

-- 1. Migrasi users
INSERT INTO web_mentor.users (id, nama, email, email_verified_at, kata_sandi, peran, remember_token, created_at, updated_at)
SELECT id, nama, email, email_terverifikasi_pada, password, peran, token_ingat_saya, dibuat_pada, diperbarui_pada
FROM mentorconnect.pengguna
WHERE id > 0
ON DUPLICATE KEY UPDATE
    nama = VALUES(nama),
    email = VALUES(email),
    email_verified_at = VALUES(email_verified_at),
    kata_sandi = VALUES(kata_sandi),
    peran = VALUES(peran),
    remember_token = VALUES(remember_token),
    updated_at = VALUES(updated_at);

-- 2. Migrasi profil_mahasiswa
INSERT INTO web_mentor.profil_mahasiswa (user_id, universitas, jurusan, created_at, updated_at)
SELECT m.pengguna_id, '', COALESCE(NULLIF(NULLIF(m.jurusan, '-'), ''), 'Tidak diisi'), m.dibuat_pada, m.diperbarui_pada
FROM mentorconnect.mahasiswa m
WHERE m.pengguna_id IN (SELECT id FROM web_mentor.users);

-- 3. Buat kategori_keahlian dari bidang mentor yang unik
INSERT INTO web_mentor.kategori_keahlian (nama, slug, deskripsi, created_at, updated_at)
SELECT DISTINCT
    mt.bidang,
    LOWER(REPLACE(mt.bidang, ' ', '-')),
    CONCAT('Kategori ', mt.bidang),
    NOW(),
    NOW()
FROM mentorconnect.mentor mt
WHERE mt.bidang IS NOT NULL AND mt.bidang != '';

-- 4. Migrasi profil_mentor
INSERT INTO web_mentor.profil_mentor (id, user_id, gelar, universitas, pengalaman, bio, no_hp, foto, created_at, updated_at)
SELECT
    mt.id,
    mt.pengguna_id,
    NULL,
    COALESCE(NULLIF(mt.alumni, ''), 'Tidak diisi'),
    CASE
        WHEN mt.pengalaman IS NOT NULL AND mt.pengalaman != '[]' AND mt.pengalaman != 'NULL'
        THEN mt.pengalaman
        ELSE NULL
    END,
    COALESCE(NULLIF(mt.bio, ''), NULL),
    COALESCE(NULLIF(mt.nomor_whatsapp, ''), NULL),
    NULL,
    mt.dibuat_pada,
    mt.diperbarui_pada
FROM mentorconnect.mentor mt
WHERE mt.pengguna_id IN (SELECT id FROM web_mentor.users);

ALTER TABLE web_mentor.profil_mentor AUTO_INCREMENT = 5;

-- 5. Migrasi keahlian (link mentor to kategori)
INSERT INTO web_mentor.keahlian (mentor_id, kategori_id, created_at, updated_at)
SELECT
    mt.id,
    kk.id,
    NOW(),
    NOW()
FROM mentorconnect.mentor mt
JOIN web_mentor.kategori_keahlian kk ON kk.nama = mt.bidang
WHERE mt.pengguna_id IN (SELECT id FROM web_mentor.users);

-- 6. Migrasi pengajuan_mentoring (from pemesanan)
INSERT INTO web_mentor.pengajuan_mentoring (id, mahasiswa_id, mentor_id, kategori_id, judul, deskripsi, tanggal, jam, status, catatan_mentor, created_at, updated_at)
SELECT
    p.id,
    p.mahasiswa_id,
    pm.id,
    NULL,
    COALESCE(p.topik, 'Pengajuan Mentoring'),
    COALESCE(p.deskripsi, 'Tidak ada deskripsi'),
    NULL,
    NULL,
    CASE p.status
        WHEN 'pending' THEN 'pending'
        WHEN 'accepted' THEN 'disetujui'
        WHEN 'rejected' THEN 'ditolak'
        WHEN 'completed' THEN 'selesai'
        WHEN 'cancelled' THEN 'dibatalkan'
        WHEN 'expired' THEN 'dibatalkan'
        ELSE 'pending'
    END,
    NULL,
    p.dibuat_pada,
    p.diperbarui_pada
FROM mentorconnect.pemesanan p
JOIN web_mentor.profil_mentor pm ON pm.id = p.mentor_id;

ALTER TABLE web_mentor.pengajuan_mentoring AUTO_INCREMENT = 14;

-- 7. Migrasi jadwal (from jadwal_mentor)
INSERT INTO web_mentor.jadwal (mentor_id, tanggal, jam_mulai, jam_selesai, tersedia, created_at, updated_at)
SELECT
    jm.mentor_id,
    jm.tanggal,
    jm.jam_mulai,
    jm.jam_selesai,
    CASE WHEN jm.status = 'tersedia' THEN 1 ELSE 0 END,
    jm.dibuat_pada,
    jm.diperbarui_pada
FROM mentorconnect.jadwal_mentor jm;

ALTER TABLE web_mentor.jadwal AUTO_INCREMENT = 112;

-- 8. Migrasi ulasan (from penilaian)
-- Find matching pengajuan_mentoring by mahasiswa_id and mentor_id
INSERT INTO web_mentor.ulasan (pengajuan_id, mahasiswa_id, mentor_id, rating, komentar, created_at, updated_at)
SELECT
    COALESCE(
        (SELECT id FROM web_mentor.pengajuan_mentoring pm2
         WHERE pm2.mahasiswa_id = pl.mahasiswa_id
           AND pm2.mentor_id = pm3.id
         ORDER BY pm2.created_at DESC LIMIT 1),
        0
    ),
    pl.mahasiswa_id,
    pm3.id,
    pl.rating,
    pl.ulasan,
    pl.dibuat_pada,
    pl.diperbarui_pada
FROM mentorconnect.penilaian pl
JOIN web_mentor.profil_mentor pm3 ON pm3.id = pl.mentor_id;

-- 10. Reset sequences
ALTER TABLE web_mentor.ulasan AUTO_INCREMENT = 4;
